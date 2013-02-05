<?php

class GiftController extends Zend_Controller_Action
{
    protected $uid;

    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
            echo '<html><body>出错了，请刷新重新进入应用。</body></html>';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'openid' => $info['openid'], 'openkey' => $info['openkey']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);
        $this->view->openid = $info['openid'];
        $this->view->openkey = $info['openkey'];

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

    protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
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
	        $vsig = md5($uid . $openid . $openkey . $t . APP_KEY);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $openid . $openkey . $t . $rnd . APP_KEY);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'openid' => $openid, 'openkey' => $openkey,  't' => $t, 'rnd' => $rnd);
    }

    public function topAction()
    {
    	$cid = $this->_request->getParam("tid", 1);

        $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($this->uid);
        $giftList = Hapyfish2_Island_Cache_BasicInfo::getGiftList();
        $giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($this->uid);

        $balanceInfo = Hapyfish2_Island_Bll_Gold::get($this->uid, true);
    	if (!$balanceInfo) {
			$userGold = 0;
        } else {
        	$userGold = $balanceInfo['balance'];
        }

        $giftFree = array ( );
		$giftCharge = array ( );
		foreach ( $giftList as $gift ) {
			switch ( $gift['is_free'] ) {
				case 0 :
					$giftFree[] = $gift;
				break;
				case 1 :
					$giftCharge[] = $gift;
				break;
			}
		}

		$gift_free = array_chunk( $giftFree, 8 );
		$gift_charge = array_chunk( $giftCharge, 8 );
		$this->view->giftFrees = $gift_free;
		$this->view->freeCount = count( $gift_free );
		$giftCharges = array_chunk( $giftCharge, 8 );
		$this->view->giftCharges = $giftCharges;
		$this->view->chargeCount = count ( $gift_charge );
		$this->view->userGold = $userGold;
		$this->view->cid = $cid;
        $this->view->userLevel = $userLevelInfo['level'];
        $this->view->count = $giftSendCountInfo['count'];

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }

        $this->render();
    }

    public function friendsAction()
    {
		$gid = $this->_request->getPost('gid');
		$tid = $this->_request->getPost('tid');
		$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();

        //get gold
        $balanceInfo = Hapyfish2_Island_Bll_Gold::get($this->uid, true);
        if (!$balanceInfo) {
	        $userGold = 0;
	    } else {
			$userGold = $balanceInfo['balance'];
	    }

		if (empty($gid)) {
			echo '-100';
			exit;
		}

		$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getGiftInfo($gid);
		if (!$giftInfo) {
			echo '-100';
			exit;
		}

		$uid = $this->uid;

		$pageSize = 15;
		$fids = Hapyfish2_Platform_Bll_Factory::getFriendIds($uid);
		if ($fids) {
			$friendList = Hapyfish2_Platform_Bll_Factory::getMultiUser($fids);
			$friendNum = count($friendList);
		} else {
			$friendList = '[]';
			$friendNum = 0;
		}

		$giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($uid);
		$goldSendCount = floor($userGold / 5);
    	if($tid == 1) {
			$this->view->giftSendNum = $giftSendCountInfo['count'];
		} else {
			$this->view->giftSendNum = $goldSendCount;
		}
		$this->view->gift = $giftInfo;
		$this->view->friendList = json_encode($friendList);
		$this->view->friendNum = $friendNum;
		$this->view->pageSize = $pageSize;
		$this->view->pageNum = ceil($friendNum/$pageSize);
		$this->view->tid = $tid;

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }

    	$this->render();
    }

    public function sendAction()
    {
        $gid = $this->_request->getPost('gid');
        $fids = $this->_request->getPost('fids');
        $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($this->uid);

        $result = array();
    	if (empty($gid)) {
    		$result['errno'] = 1001;
			echo json_encode($result);
			exit;
		}
    	if (empty($fids)) {
    		$result['errno'] = 1001;
			echo json_encode($result);
			exit;
		}
    	$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getGiftInfo($gid);
		if (!$giftInfo) {
    		$result['errno'] = 1001;
			echo json_encode($result);
			exit;
		}
		$fids = split(',', $fids);
        if (empty($fids)) {
    		$result['errno'] = 1001;
			echo json_encode($result);
			exit;
		}

		$uid = $this->uid;
		$gift_need_money = 0;

    	$friendIds = Hapyfish2_Platform_Bll_Factory::getFriendIds($uid);
        $tmp = array_flip($friendIds);

        foreach ($fids as $fid) {
        	if (!isset($tmp[$fid])) {
    			$result['errno'] = 1003;
				echo json_encode($result);
				exit;
        	}
        }

        $ok2 = 0;
        $giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($uid);
    	if($giftInfo['is_free'] == 0) {
			$count = count($fids);

			if ($giftSendCountInfo['count'] <= 0 || $count > $giftSendCountInfo['count']) {
	    		$result['errno'] = 1002;
				echo json_encode($result);
				exit;
			}
			$ok2 = Hapyfish2_Island_Bll_GiftPackage::sendGift($gid, $uid, $fids, $giftSendCountInfo, 1);

			Hapyfish2_Island_Bll_GiftPackage::insertGiftLog($uid, $gid, $fids, 1);
        }
        else {
        	$balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
	    	if (!$balanceInfo) {
	        	$userGold = 0;
	        } else {
        		$userGold = $balanceInfo['balance'];
	        }

        	$count = count($fids);
			$gift_need_money = $giftInfo['price'];
			$all_need_money = (int)$count * (int)$gift_need_money;

        	//如果钱不够，就跳转到充值页面
			if($userGold < $all_need_money){
				$this->_redirect($this->view->baseUrl . '/gift/err/t/2');
				exit();
			}

        	$price = $all_need_money;
	        $payInfo = array(
	        	'amount' => $price,
	        	'is_vip' => 0,
	        	'item_id' => $gid,
	        	'item_num' => $count,
	        	'uid' => $uid,
	        	'user_level' => $userLevelInfo['level']
	        );

			$ok = Hapyfish2_Island_Bll_Gold::consume($uid, $payInfo);
			if ($ok) {
				$ok2 = Hapyfish2_Island_Bll_GiftPackage::sendGift($gid, $uid, $fids, $giftSendCountInfo, 2);
				if ($ok2) {
		        	$goldInfo = array(
		        		'uid' => $payInfo['uid'],
		        		'cost' => $payInfo['amount'],
		        		'summary' => '赠送收费礼物' . $giftInfo['name'],
		        		'is_vip' => $payInfo['is_vip'],
		        		'user_level' => $userLevelInfo['level'],
		        		'cid' => $payInfo['item_id'],
		        		'num' => $ok2
		        	);
		        	Hapyfish2_Island_Bll_Gold::consumeComfirm($uid, $payInfo, $goldInfo);
					$result['gold'] += $price;
					$result['count']++;
				} else {
					//cancel consume
					Hapyfish2_Island_Bll_Gold::consumeCancel($uid, $payInfo);
				}
			} else {
				info_log(json_encode($payInfo), 'payorder_failure');
			}

			Hapyfish2_Island_Bll_GiftPackage::insertGiftLog($uid, $gid, $fids, 2);
        }

        $result = array('errno' => 0, 'count' => $count, 'num' => $ok2);
        echo json_encode($result);
    	exit;
    }

	function getAction()
	{
		$uid = $this->uid;
		$pageIndex = $this->_request->getParam('page', 1);
        $pageSize = 10;

		$my_gift_list = Hapyfish2_Island_Bll_GiftPackage::getGiftLog($uid);

		$num = count($my_gift_list);
        $pages = ceil($num/$pageSize);
        $new_list = array_chunk($my_gift_list, $pageSize);
        $pageIndexNum = $pageIndex - 1;

        $this->view->gift_list = $new_list[$pageIndexNum];
		$this->view->pages = $pages;
    }

	function postAction()
	{
		$uid = $this->uid;
		$pageIndex = $this->_request->getParam('page', 1);
		$pageSize = 10;

		$my_gift_list = Hapyfish2_Island_Bll_GiftPackage::postGiftLog($uid);

		$num = count($my_gift_list);
		$pages = ceil($num/$pageSize);
		$new_list = array_chunk($my_gift_list, $pageSize);
		$pageIndexNum = $pageIndex - 1;

		$this->view->gift_list = $new_list[$pageIndexNum];
		$this->view->pages = $pages;
    }

	function errAction()
	{
		$uid = $this->uid;
		$t = $this->_request->getParam("t");

		$balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
		if (!$balanceInfo) {
			$result['content'] = 'serverWord_1002';
			return $result;
		}
		$userGold = $balanceInfo['balance'];


		switch ($t){
			case 1:
			    $msg = "<p>免费礼物一天只能发送三次<br/></p><p>建议你发送<a href='/gift/top/t/2'><b>收费礼物</b></a>给你的好友</p>";
			break;
			case 2:
				$msg = '<p>你的宝石不够哦，建议您<a href="http://pay.qq.com/app_pay/pay.shtml?appid=610" target="_blank"><b>马上充值</b></a></p>';
			break;
		}
		$this->view->msg = $msg;
	}

 }