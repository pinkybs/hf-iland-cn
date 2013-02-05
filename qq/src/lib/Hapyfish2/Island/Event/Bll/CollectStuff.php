<?php


class Hapyfish2_Island_Event_Bll_CollectStuff
{
    public static function getCollectStuff($uid){
        $result['result']['status'] = 1;
    	$result['haveGetTree'] = false;
    	$result['haveGetGirl'] = false;
    	$result['haveGetBoy'] = false;
    	$plants = Hapyfish2_Island_HFC_Plant::getAll($uid);
        if ($plants) {
    		foreach($plants as $item) {
	    		if ($item['cid'] == 76131) {
	    			$result['haveGetTree'] = true;
	    		}
    			if ($item['cid'] == 73131) {
	    		$result['haveGetGirl'] = true;
	    		}
    			if ($item['cid'] == 73231) {
	    			$result['haveGetBoy'] = true;
	    		}
	    	}
    	}
    	return $result;
    }
    
public static function goldSale($uid,$goods,$time)
	{
		$now = time();
		if ($now > $time['end'] || $now <$time['start']) {
			$resultVo['status'] = -1;
			$resultVo['content'] = 'serverWord_148';
			return array('result' => $resultVo);
		}
		if (empty($uid)) {
			$resultVo['status'] = -4;
			$resultVo['content'] = 'serverWord_148';
			return array('result' => $resultVo);
		}

		$balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
        if (!$balanceInfo) {
        	$resultVo['status'] = -1;
        	$resultVo['content'] = 'serverWord_1002';
        	return array('result' => $resultVo);
        }
        //is gold enough
		$userGold = $balanceInfo['balance'];
		if ($userGold < $goods['price']) {
			$resultVo['status'] = -3;
			$resultVo['content'] = 'serverWord_140';
			return array('result' => $resultVo);
		}

		$isVip = $balanceInfo['is_vip'];
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		$payInfo = array(
	        	'amount' => $goods['price'],
	        	'is_vip' => $isVip,
	        	'item_id' => $goods['id'],
	        	'item_num' => 1,
	        	'uid' => $uid,
	        	'user_level' => $userLevel
	    );
		$ok = Hapyfish2_Island_Bll_Gold::consume($uid, $payInfo);
		if ($ok) {
			$robot = new Hapyfish2_Island_Bll_Compensation();
			$robot->setItem($goods['id'], 1);
			$robot->setFeedTitle('成功购买：'.$goods['name']);
			$ok2 = $robot->sendOne($uid, '');
			if ($ok2) {
	        	$goldInfo = array(
	        		'uid' => $payInfo['uid'],
	        		'cost' => $payInfo['amount'],
	        		'summary' => '购买' . $goods['name'],
	        		'billno' => $payInfo['bill_no'],
	        		'is_vip' => $payInfo['is_vip'],
	        		'user_level' => $userLevel,
	        		'cid' => $payInfo['item_id'],
	        		'num' => $payInfo['item_num']
	        	);
	        	Hapyfish2_Island_Bll_Gold::consumeComfirm($uid, $payInfo, $goldInfo);
				$resultVo['status'] = 1;
				$resultVo['goldChange'] = -$goods['price'];
				return array('result' => $resultVo);
			} else {
				//cancel consume
				Hapyfish2_Island_Bll_Gold::consumeCancel($uid, $payInfo);
				$resultVo['status'] = -5;
				$resultVo['content'] = 'serverWord_148';
				return array('result' => $resultVo);
			}
		} else {
			info_log(Zend_Json::encode($payInfo), 'payorder_failure');
			$resultVo['status'] = -5;
			$resultVo['content'] = 'serverWord_148';
			return array('result' => $resultVo);
		}
	}
	public static function haveGetgift($uid){
	    $key = 'CollectStuff:'.$uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
		if ($data === false) {
			try {
    		    $dal = Hapyfish2_Island_Event_Dal_CollectStuff::getDefaultInstance();
    		    $data = $dal->haveGetgift($uid);
    			$cache->set($key, $data);
				return $data;
			} catch (Exception $e) {
				info_log($e->getMessage(), 'Event_CollectStuff');
			}
		} else {
			return $data;
		}
	}
    public static function Exchange($uid,$time){
        if (time()>$time['end'] || time()<$time['start']) {
			$resultVo['status'] = -1;
			$resultVo['content'] = 'serverWord_148';
			return array('result' => $resultVo);
		}
		if (empty($uid)) {
			$resultVo['status'] = -4;
			$resultVo['content'] = 'serverWord_148';
			return array('result' => $resultVo);
		}
		$can_exchang = self::getCollectStuff($uid);
		if($can_exchang['haveGetTree'] == true  && $can_exchang['haveGetGirl'] == true && $can_exchang['haveGetBoy'] == true){
			$compensation = new Hapyfish2_Island_Bll_Compensation();
    	    $compensation->setItem(75532, 1);
    	    $title = '恭喜你成功兑换双鱼座！';
		    $compensation->setFeedTitle($title);
		    $ok = $compensation->sendOne($uid, '');
		    if ($ok) {
				$times = time();
			    try {
				    $key = 'CollectStuff:'.$uid;
        		    $cache = Hapyfish2_Cache_Factory::getMC($uid);
				    $dal = Hapyfish2_Island_Event_Dal_CollectStuff::getDefaultInstance();
				    $info = array('uid' => $uid, 'step' => 1, 'create_time' => $times);
				    $dal->insert($uid, $info);
				    $cache->set($key, 1);
			    } catch (Exception $e) {
				    info_log($e->getMessage(), 'Event_CollectStuff');
			    }
			    $picUrl = STATIC_HOST.'/apps/island/images/event/Stuff.jpg';
			    $result = array('result' => array('status' => 1),'picUrl' => $picUrl);
		    }
		}else{
			$result = array('result' => array('status' => -1,'content' => '对不起您没有达到兑换条件'));
		}
		return $result;
    }
    public static function getCsvData($file)
    {
        $handle=fopen($file,"r");
        $row=1;
        while($data=fgetcsv($handle,1000,",")){
            $num=count($data);
            for($i=0;$i<$num;$i++){
                $uidlist[]=$data[0];
            }
        $row++;
        }
        return $uidlist;
    }
}