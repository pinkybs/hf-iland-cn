<?php

class PaytestController extends Zend_Controller_Action
{
	protected function vailid()
    {
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
    	if (!$skey) {
    		return false;
    	}

    	$tmp = split('_', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $openid = $tmp[1];
        $openkey = $tmp[2];
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $openid . $openkey . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $openid . $openkey . $t . $rnd . APP_SECRET);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        $this->uid = $uid;
        return array('uid' => $uid, 'openid' => $openid, 'openkey' => $openkey,  't' => $t, 'rnd' => $rnd);
    }

	public function getbalanceAction()
	{
		$info = $this->vailid();
		$qzone = Qzone_Rest::getInstance();
        $openid = $info['openid'];
        $openkey = $info['openkey'];
        $qzone->setUser($openid, $openkey);
        $balance = $qzone->getPayBalance();
        echo '当前岛钻余额:' . $balance;
        exit;
	}

	public function buyAction()
	{
        $info = $this->vailid();
        $uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'openid' => $info['openid'], 'openkey' => $info['openkey']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

        $balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
        if (!$balanceInfo) {
        	echo 'error';
        	exit;
        }

        $balance = $balanceInfo['balance'];
        $isVip = $balanceInfo['is_vip'];

        //26341,船只加速卡II
        $itemId = 26341;
        $itemNum = 5;
        $amount = 5;

        $payInfo = array(
        	'amount' => $amount,
        	'is_vip' => $isVip,
        	'item_id' => $itemId,
        	'item_num' => $itemNum,
        	'uid' => $uid
        );

        $ok = Hapyfish2_Island_Bll_Gold::consume($uid, $payInfo);
        if (!$ok) {
        	echo 'failure';
        	exit;
        }

        $userCards = Hapyfish2_Island_HFC_Card::getUserCard($uid);
        if (isset($userCards[$itemId])) {
	        $userCards[$itemId]['count'] += $itemNum;
	        $userCards[$itemId]['update'] = 1;
        } else {
        	$userCards[$itemId] = array('count' => $itemNum, 'update' => 1);
        }

        $transmit = Hapyfish2_Island_HFC_Card::updateUserCard($uid, $userCards);
        if ($transmit) {
        	$goldInfo = array(
        		'uid' => $payInfo['uid'],
        		'cost' => $payInfo['amount'],
        		'summary' => $itemNum . '张船只加速卡II',
        		'billno' => $payInfo['bill_no'],
        		'is_vip' => $payInfo['is_vip'],
        		'user_level' => 1,
        		'cid' => $itemId,
        		'num' => $itemNum
        	);
        	Hapyfish2_Island_Bll_Gold::consumeComfirm($uid, $payInfo, $goldInfo);
        	echo 'comfirm';
        	exit;
        } else {
        	Hapyfish2_Island_Bll_Gold::consumeCancel($uid, $payInfo);
        	echo 'cacel';
        	exit;
        }
	}

	public function test2Action()
	{
		$uid = 100;
		$billnoPrefix = '-HLHD-20101228-';
		$i = 10000;
		$time = time();
		$payInfo = array(
			'time' => $time,
			'openid' => '000000000000000000000000051DFE15',
			'cmd' => 7,
        	'amount' => 5,
        	'is_vip' => 1,
        	'item_id' => '26341',
        	'item_num' => 5,
			'result' => 0,
			'platform' => 0
        );
        for($j = 0; $j < 5000; $j++) {
        	$payInfo['bill_no'] = $billnoPrefix . ($i+$j);
        	//$payInfo['bill_no'] = '';
        	$payInfo['uid'] = $uid + $j;
        	Hapyfish2_Island_Bll_Gold::addPayOrderFlow($payInfo['uid'], $payInfo);
        }

        echo 'ok';
        exit;
	}

}