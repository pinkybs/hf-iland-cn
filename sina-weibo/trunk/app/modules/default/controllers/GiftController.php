<?php

class GiftController extends Zend_Controller_Action
{
    protected $uid;

    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
            echo '<script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script>';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->uid = $info['uid'];
        $this->view->platformUid = $info['puid'];
    }

    protected function vailid()
    {
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
    	if (!$skey) {
    		return false;
    	}

    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $puid = $tmp[1];
        $session_key = base64_decode($tmp[2]);
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $puid . $session_key . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
    }

    public function topAction()
    {
    	$cid = $this->_request->getParam("tid", 1);

		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($this->uid);
		$giftList = Hapyfish2_Island_Cache_BasicInfo::getGiftList();
		$giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($this->uid);

        //get user gold
        $userGold = Hapyfish2_Island_HFC_User::getUserGold($this->uid);

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
		$this->view->userLevel = $userLevelInfo['level'];
		$this->view->userGold = $userGold;
		$this->view->cid = $cid;
		$this->view->count = $giftSendCountInfo['count'];

        $this->render();
    }

    public function friendsAction()
    {
    	$gid = $this->_request->getPost('gid');
    	$tid = $this->_request->getPost('tid');

        //get user gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($this->uid);

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
		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
		if ($fids) {
			$friendList = Hapyfish2_Platform_Bll_User::getMultiUser($fids);
			$friendNum = count($friendList);
		} else {
			$friendList = '[]';
			$friendNum = 0;
		}

		$giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($uid);
		$goldSendCount = floor($userGold/5);
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

    	$this->render();
    }

    public function sendAction()
    {
    	$gid = $this->_request->getPost('gid');
        $fids = $this->_request->getPost('fids');
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

        $friendIds = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
        $tmp = array_flip($friendIds);

        foreach ($fids as $fid) {
        	if (!isset($tmp[$fid])) {
    			$result['errno'] = 1003;
				echo json_encode($result);
				exit;
        	}
        }

       $giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($uid);

        if($giftInfo['is_free'] == 0) {

			$count = count($fids);

			if ($giftSendCountInfo['count'] <= 0 || $count > $giftSendCountInfo['count']) {
	    		$result['errno'] = 1002;
				echo json_encode($result);
				exit;
			}
			$num = Hapyfish2_Island_Bll_GiftPackage::sendGift($gid, $uid, $fids, $giftSendCountInfo, 1);

        	if($num) {
				//add gift log
				Hapyfish2_Island_Bll_GiftPackage::insertGiftLog($uid, $gid, $fids, 1);
			}
        }
        else {
        	$count = count($fids);
			$gift_need_money = $giftInfo['price'];
			$all_need_money = (int)$count * (int)$gift_need_money;

        	//get user gold
	        $userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);

        	//如果钱不够，就返回
			if($userGold < $all_need_money){
				$this->_redirect('<a href="javascript:void(0);"' . ' onclick="topGift();"');
				exit();
			}

			$num = Hapyfish2_Island_Bll_GiftPackage::sendGift($gid, $uid, $fids, $giftSendCountInfo, 2);

			//dec user gold
			$goldInfo = array(
				'uid' => $uid,
				'cost' => $all_need_money,
				'summary' => '赠送收费礼物' . $giftInfo['name'] . 'x' . $num,
				'cid' => $gid,
				'num' => 1
			);
	        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);

			Hapyfish2_Island_Bll_GiftPackage::insertGiftLog($uid, $gid, $fids, 2);
        }

        $result = array('errno' => 0, 'count' => $count, 'num' => $num);
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

        if(empty($new_list)) {
        	$this->view->gift_list = $new_list;
        } else {
        	$this->view->gift_list = $new_list[$pageIndexNum];
        }
		$this->view->pages = $pages;
    }

	function postAction()
	{
		$uid = $this->uid;
		$pageIndex = $this->_request->getParam('page', 1);
		$pageSize = 10;

		$my_gift_list = Hapyfish2_Island_Bll_GiftPackage::postGiftLog($uid);

		$num = count($my_gift_list);
		$pages = ceil($num / $pageSize);
		$new_list = array_chunk($my_gift_list, $pageSize);
		$pageIndexNum = $pageIndex - 1;

		if(empty($new_list)) {
			$this->view->gift_list = $new_list;
		} else {
			$this->view->gift_list = $new_list[$pageIndexNum];
		}
		$this->view->pages = $pages;
    }

	function errAction()
	{
		$uid = $this->uid;
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		$t = $this->_request->getParam("t");

		switch ($t){
			case 1:
			    $msg = "<p>免费礼物一天只能发送三次<br/></p><p>建议你发送" . '<a href="javascript:void(0);" onclick="topGift();"' . "><b>收费礼物</b></a>给你的好友</p>";
			break;
			case 2:
				$msg = "<p>你的宝石不够哦，建议您<a href='" . $this->view->hostUrl . "/pay/top'><b>马上充值</b></a></p>";
			break;
			case 3:
				$msg = "<p>你的金币不足哦，无法赠送</p>";
		}
		$this->view->msg = $msg;
	}

 }
