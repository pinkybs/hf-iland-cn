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
		} else if (!empty($_SERVER['HTTP_QVIA'])) {
			$strData = substr($_SERVER['HTTP_QVIA'], 0, 8);
			$data = array(hexdec(substr($strData, 0, 2)),
			              hexdec(substr($strData, 2, 2)),
			              hexdec(substr($strData, 4, 2)),
			              hexdec(substr($strData, 6, 2)));
			$ip = implode('.', $data);
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }

    public function indexAction()
    {
    	$openid = $this->_request->getParam('openid');
    	$openkey = $this->_request->getParam('openkey');

    	if (empty($openid) || empty($openkey)) {
    		echo '出错啦';
    		exit;
    	}

    	if (APP_STATUS == 0) {
    		$ip = $this->getClientIP();
    		if ($ip != '27.115.48.202' && $ip != '116.247.76.102' && $ip != '122.147.63.223') {
    			header('Location: ' . STATIC_HOST . '/maintance/index.html?v=2011022401');
    			exit;
    		}
    	}

    	try {
    		$application = Hapyfish2_Application_Qzone::newInstance($this);
        	$application->run();
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    		//echo '加载数据出错，请重新进入。';
    		echo '<div style="text-align:center;margin-top:30px;"><img src="' . STATIC_HOST . '/maintance/images/problem1.gif" alt="加载数据出错，请重新进入" /></div>';
    		exit;
    	}

        $uid = $application->getUserId();
        $isnew = $application->isNewUser();

        if ($isnew) {
        	$ok = Hapyfish2_Island_Bll_User::joinUser($uid);
			Hapyfish2_Island_Event_Bll_Timegift::setup($uid);
        	if (!$ok) {
    			echo '创建初始化数据出错，请重新进入。';
    			exit;
        	}

            //invite
	        if ($application->invite) {
	        	$iopenid = $application->params['iopenid'];
	        	if ($iopenid) {
	        		$inviterInfo = Hapyfish2_Platform_Cache_UidMap::getUser($iopenid);
	        		if ($inviterInfo) {
	        			$iuid = $inviterInfo['uid'];
	        			$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($iuid);
	        			if ($isAppUser) {
	        				Hapyfish2_Island_Bll_Invite::add($iuid, $uid);
	        			}
	        		}
	        	}
	        }
        } else {
        	$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
        	if (!$isAppUser) {
        		$ok = Hapyfish2_Island_Bll_User::joinUser($uid);
        	    if (!$ok) {
    				echo '创建初始化数据出错，请重新进入。';
    				exit;
        		}
        	} else {
        		$status = Hapyfish2_Platform_Bll_Factory::getStatus($uid);
        		if ($status > 0) {
        			if ($status == 1) {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因使用外挂或违规已被封禁，有问题请联系管理员QQ:1349148526';
        			} else if ($status == 2) {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因数据出现异常被暂停使用，有问题请联系管理员QQ:1349148526';
        			} else if ($status == 3)  {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因利用bug被暂停使用[待处理后恢复]，有问题请联系管理员QQ:1349148526';
        			} else {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')暂时不能访问，有问题请联系管理员QQ:1349148526';
        			}

        			echo $msg;
        			exit;
        		}
        	}
        }

        //update friend count achievement
		$count = Hapyfish2_Platform_Bll_Factory::getFriendCount($uid);
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

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }
		$this->view->uid = $uid;
        $this->view->showpay = true;
        $this->view->openid = $openid;
        $this->view->openkey = $openkey;
        $this->view->newuser = $isnew ? 1 : 0;
        $this->render();
    }

 }

