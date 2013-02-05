<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2010/10    lijun.hu
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
    }

    protected function getClientIP()
    {
    	$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }

    public function indexAction()
    {
        $campaignId = (int)$this->_request->getParam('hf_fromcamp');
        if (!empty($campaignId) && $campaignId<=500 && $campaignId>0) {
            Hapyfish2_Island_Stat_Bll_Campaign::fromCampaignPv($campaignId, $this->getClientIP());
        }

        $top_appkey = $this->_request->getParam('top_appkey');      
        if (empty($top_appkey)) {
            //echo '<html><body><a href="http://i.taobao.com/apps/show.htm?appkey=' . APP_KEY . '">Enter</a></body></html>';
			echo '<html><body><a href="http://yingyong.taobao.com/show.htm?app_id=' . APP_ID . '">Enter</a></body></html>';
            exit;
        }

    	if (APP_STATUS == 0) {
    		$stop = true;
    		if (APP_STATUS_DEV == 1) {
    			$ip = $this->getClientIP();
	    		if ($ip == '27.115.48.202' || $ip == '122.147.63.223' || $ip == '116.247.76.102') {
	    			$stop = false;
	    		}
    		}
    		if ($stop) {
    			header('Location: ' . STATIC_HOST . '/maintance/index.html?v=' . date('YmdHi') . '01');
    			exit;
    		}
    	}

    	try {
    		$application = Hapyfish2_Application_Taobao::newInstance($this);
        	$application->run();
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    		//echo '加载数据出错，请重新进入。';
    		echo '<div style="text-align:center;margin-top:30px;"><img src="' . STATIC_HOST . '/maintance/images/problem1.gif" alt="加载数据出错，请重新进入" /></div>';
    		exit;
    	}

        $uid = $application->getUserId();
        $isnew = $application->isNewUser();
        $platformUid = $application->getPlatformUid();
        
        if ($isnew) {
			$ok = Hapyfish2_Island_Bll_User::joinUser($uid);
        	Hapyfish2_Island_Event_Bll_Timegift::setup($uid);
        	if (!$ok) {
    			echo '创建初始化数据出错，请重新进入。';
    			exit;
        	}

//        	$inviteID = $this->_request->getParam('hf_inviter');
//        	$inviteIDNew = base64_decode($inviteID);
//        	if ($inviteID) {
//        		Hapyfish2_Island_Bll_Invite::add($inviteIDNew, $uid);
//        		info_log('inviter:' . $inviteIDNew . ',uid:' . $uid, 'invitejoin');
//        	}
        	
        	/**
	        $isInvite = $this->_request->getParam('hf_invite');

	        if ($isInvite) {
	            $inviteUid = 0;
	        	$isExists = 0;
	        	$ivniteType = 'INVITE';

    			$inviteUid = $this->_request->getParam('hf_inviter');
    			$sig = $this->_request->getParam('hf_sg');

	   			$dalInvite = Hapyfish2_Island_Dal_InviteLog::getDefaultInstance();
    			$isExists = $dalInvite->getInvite($sig);

    			if ($inviteUid && $isExists && $inviteUid == $isExists['actor']) {
	    			$puidMS = Hapyfish2_Platform_Bll_UidMap::getUser($inviteUid);
	    			Hapyfish2_Island_Bll_Invite::add($puidMS['uid'], $uid);

	    			if ($ivniteType == 'INVITE') {
	    				$dalInvite->deleteInvite($sig);
	 					info_log('inviter:'.$puidMS['uid'].',uid:'.$uid, 'invitejoin');
	    			}
	    		}
	        }*/
        } else {
        	$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
        	if (!$isAppUser) {
        		$ok = Hapyfish2_Island_Bll_User::joinUser($uid);
        	    if (!$ok) {
    				echo '创建初始化数据出错，请重新进入。';
    				exit;
        		}
        	} else {
        		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
        		if ($status > 0) {
        			if ($status == 1) {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因使用外挂或违规已被封禁，有问题请联系管理员QQ:1471558464';
        			} else if ($status == 2) {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因数据出现异常被暂停使用，有问题请联系管理员QQ:1471558464';
        			} else if ($status == 3)  {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因利用bug被暂停使用[待处理后恢复]，有问题请联系管理员QQ:1471558464';
        			} else {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')暂时不能访问，有问题请联系管理员QQ:1471558464';
        			}

        			echo $msg;
        			exit;
        		}

        	}
        }

        //update friend count achievement
		$count = Hapyfish2_Platform_Bll_Friend::getFriendCount($uid);
        if ($count > 0) {
        	$achievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
        	if ($achievement['num_16'] < $count) {
        		$achievement['num_16'] = $count;
				try {
        			Hapyfish2_Island_HFC_Achievement::saveUserAchievement($uid, $achievement);

					//task id 3018,task type 16
					Hapyfish2_Island_Bll_Task::checkTask($uid, 3018);
				} catch (Exception $e) {
				}
        	}
        }

        $next = $this->_request->getParam('hf_next');
		if ($next) {
		    $this->_redirect($next);
		}

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }

        $baseuid = base64_encode($uid);
        $inviteUrl = 'http://yingyong.taobao.com/show.htm?app_id=73015&hf_inviter=' . $baseuid;
        
        $this->view->uid = $uid;
        $this->view->platformUid = $platformUid;
        $this->view->showpay = true;
        $this->view->newuser = $isnew ? 1 : 0;
        $this->view->inviteUrl = $inviteUrl;
        $this->render();
    }
    
	public function maintanceAction()
	{
		$appInfo = Hapyfish2_Island_Bll_AppInfo::getAdvanceInfo();
		$this->view->notice = $appInfo['maintance_notice'];
		$this->render();
	}

    public function testAction()
    {
        echo 'hello taobaov2';
        exit;
    }
}