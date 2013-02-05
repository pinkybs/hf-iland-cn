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
//info_log(json_encode($_REQUEST), 'fromsina');
        $wyx_user_id = $this->_request->getParam('wyx_user_id');
    	$wyx_session_key = $this->_request->getParam('wyx_session_key');
        $wyx_expire = $this->_request->getParam('wyx_expire');
    	if ( empty($wyx_user_id) || empty($wyx_session_key) ) {
    		echo '出错啦';
    		exit;
    	}
    	//wyx session time out
    	if ( empty($wyx_expire) || ($wyx_expire+3600) < time() ) {
            echo '出错啦1';
            exit;
    	}

        /*
		{"wyx_user_id":"2099009544","wyx_session_key":"55779f71b0b267280602c10ef623009401414cfa_1304105918_2099009544","wyx_create":"1304069918","wyx_expire":"1304105918","wyx_signature":"ffe517a8169d5ee0508b8a4eb01b0b965d96d777"}
         * */

        if (APP_STATUS == 0) {
    		$stop = true;
    		if (APP_STATUS_DEV == 1) {
    			$ip = $this->getClientIP();
	    		if ($ip == '116.247.76.102') {
	    			$stop = false;
	    		}
    		}
    		if ($stop) {
    			header('Location: ' . STATIC_HOST . '/maintance/index.html?v=' . date('YmdHi'));
    			exit;
    		}
    	}

    	$isAppLoadErr = false;
    	try {
    		$application = Hapyfish2_Application_SinaWeibo::newInstance($this);
        	$application->run();
    	} catch (Exception $e) {
    	    //api called error or memcached can not reached or server sth err
    	    $isAppLoadErr = true;
    		$log = Hapyfish2_Util_Log::getInstance();
            $log->report('appLoadErr', array($wyx_user_id, $e->getMessage()));
            err_log($e->getMessage());
            $errMsg = $e->getMessage();
    	}

    	//check if can login game and play temperately
    	if ($isAppLoadErr) {
    	    $allowTemp = $application->getRest()->isCurlBegun();
    	    $ptUser = Hapyfish2_Platform_Bll_UidMap::getUser($wyx_user_id);
    	    if (!$allowTemp || empty($ptUser)) {
    	        //echo '加载数据出错，请重新进入。';
    	        $log = Hapyfish2_Util_Log::getInstance();
                $log->report('appFailLogin', array($wyx_user_id, $errMsg));
        		echo '<div style="text-align:center;margin-top:30px;"><img src="' . STATIC_HOST . '/maintance/images/problem1.gif" alt="加载数据出错，请重新进入" /></div>';
        		exit;
    	    }

            $uid = $ptUser['uid'];
            $isnew = false;
            $platformUid = $ptUser['puid'];
            $sessionKey = $wyx_session_key;

            header('P3P: CP=CAO PSA OUR');
            $t = time();
            $rnd = mt_rand(1, ECODE_NUM);
            $sig = md5($uid . $platformUid . $sessionKey . $t . $rnd . APP_SECRET);
            $skey = $uid . '.' . $platformUid . '.' . base64_encode($sessionKey) . '.' . $t . '.' . $rnd . '.' . $sig;
            setcookie('hf_skey', $skey , 0, '/', str_replace('http://', '.', HOST));
    	}
    	//normally in game
    	else {
    	    $uid = $application->getUserId();
            $isnew = $application->isNewUser();
            $platformUid = $application->getPlatformUid();
            $sessionKey = $application->getSessionKey();
    	}

        $data = array('uid' => $uid, 'puid' => $platformUid, 'session_key' => $sessionKey);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

        if ($isnew) {
        	$ok = Hapyfish2_Island_Bll_User::joinUser($uid);
        	Hapyfish2_Island_Event_Bll_Timegift::setup($uid);
        	if (!$ok) {
    			echo '创建初始化数据出错，请重新进入。';
    			exit;
        	}

            //是否受邀请加入
        	if (isset($_REQUEST['inviter_id'])) {
        	    $inviterId = $_REQUEST['inviter_id'];
        	    $rowInviter = Hapyfish2_Platform_Bll_UidMap::getUser($inviterId);
        		Hapyfish2_Island_Bll_Invite::add($rowInviter['uid'], $uid);
        		$application->getRest()->setUser($sessionKey);
                $application->getRest()->ignoreAllInvite();
                info_log($rowInviter['uid'].' -> '.$uid, 'invitedone');
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
        		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
        		if ($status > 0) {
        			if ($status == 1) {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因使用外挂或违规已被封禁，有问题请联系管理员-http://weibo.com/2155826421';
        			} else if ($status == 2) {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因数据出现异常被暂停使用，有问题请联系管理员-http://weibo.com/2155826421';
        			} else if ($status == 3)  {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')因利用bug被暂停使用[待处理后恢复]，有问题请联系管理员-http://weibo.com/2155826421';
        			} else {
        				$msg = '该帐号(小岛门牌号:' . $uid . ')暂时不能访问，有问题请联系管理员-http://weibo.com/2155826421';
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

    	//sina ranking -- WB_RANK_FRIEND
		if ($application->_cntFidBefore != $count) {
            Hapyfish2_Platform_Bll_WeiboRank::setRank($uid, WB_RANK_FRIEND, $count);
		}

        /*
		//sina ranking -- WB_RANK_EXP
		$nowExp = Hapyfish2_Island_HFC_User::getUserExp($uid);
		if ($nowExp) {
		    Hapyfish2_Platform_Bll_WeiboRank::setRank($uid, WB_RANK_EXP, $nowExp);
		}

		//sina ranking -- WB_RANK_COST
		$rowCost = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
		$nowCost = $rowCost['num_19'];
		if ($nowCost) {
		    Hapyfish2_Platform_Bll_WeiboRank::setRank($uid, WB_RANK_COST, $nowCost);
		}
		*/

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }

    	// 宝箱朋友帮助判断
        $friendhelpName = '';
        $friendhelpCoin = '0';
        
        if (isset($_GET['guoqinghelp'])) {
        	$helpuid = base64_decode($_GET['guoqinghelp']);
        	$helpuid = Hapyfish2_Util_Authcode::authcode($helpuid, 'DECODE');
        	Hapyfish2_Island_Event_Bll_Midautumn::addPassbyHelp($uid, $helpuid);
        }
        
        if (isset($_GET['friendhelp'])) {

        	$friendhelpUid = base64_decode($_GET['friendhelp']);
        	$friendhelpUid = Hapyfish2_Util_Authcode::authcode($friendhelpUid, 'DECODE');

        	$list = Hapyfish2_Island_Cache_BottleFriendHelp::getByUid($friendhelpUid);
        	$tf = true;
        	$fids = $list['fid'] || $list['fid']=='0' ? explode(',', $list['fid']) : array();
        	$goldtfs = $list['goldTF'] || $list['goldTF']=='0' ? explode(',', $list['goldTF']) : array();

        	// 人数已满
        	if (count($fids) >=5 ) {
        		$tf = false;
        	}
        	// 此朋友已存在
        	if (in_array($uid, $fids)) {
        		$tf = false;
        	}
        	// 时间未到
        	if ($list['lasttime'] && ($list['lasttime']+3600*8) > time() ) {
        		$tf = false;
        	}
        	// 不可以帮助自己
        	if ($friendhelpUid == $uid) {
        		$tf = false;
        	}
        	// 我只可以在8小时以内帮助一个人
        	if (Hapyfish2_Island_Cache_Counter::getBottleFriendHelpTF($uid)) {
        		$tf = false;
        	} else {
        		Hapyfish2_Island_Cache_Counter::updateBottleFriendHelpTF($uid);
        	}

        	if ($tf) {
        		// 获得帮助对象名字
        		$friendhelpName = Hapyfish2_Island_Bll_User::getUserInit($friendhelpUid);
        		$friendhelpName = $friendhelpName['name'];
        		$friendhelpCoin = 5000;
        		// 更新好友帮助信息
        		$fids[] = $uid;
        		$goldtfs[] = '0';
        		if ($list) {
        			$info = array('fid'=>join(',',$fids), 'goldTF'=>join(',', $goldtfs));
        			Hapyfish2_Island_Cache_BottleFriendHelp::update($friendhelpUid, $info);
        		} else {
        			$info = array( 'uid'=>$friendhelpUid, 'fid'=>join(',',$fids), 'goldTF'=>join(',', $goldtfs));
        			Hapyfish2_Island_Cache_BottleFriendHelp::insert($friendhelpUid, $info);
        		}

        		// 发送金币
        		$com = new Hapyfish2_Island_Bll_Compensation();
        		$com->setCoin($friendhelpCoin);
        		$com->sendOne($uid,$friendhelpName . ' 谢谢你给予的帮助!');
        	}

        }

        // 宝箱朋友帮助
//        $friendhelpName = 'lei.wu';
//        $friendhelpCoin = '2000';
		$this->view->friendhelpName = $friendhelpName;
		$this->view->friendhelpCoin = $friendhelpCoin;
		$this->view->authcodeuid = base64_encode(Hapyfish2_Util_Authcode::authcode($uid, 'ENCODE'));

		//is from campaign
		$fromCamp = $this->_request->getParam('hf_fromcamp');
		if ($isnew && ($fromCamp || isset($_COOKIE['hf_fromcamp']))) {
		    $fromCamp = empty($fromCamp) ? $_COOKIE['hf_fromcamp'] : $fromCamp;
		    Hapyfish2_Island_Stat_Bll_Campaign::fromCampaign($fromCamp, $uid);
		}

        $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
        $tmp = str_replace("'", "\'", $rowUser['name']);
        $tmp = str_replace('"', '\"', $tmp);
        $this->view->uname = str_replace("'", "\'", $tmp);
        $this->view->uid = $uid;
        $this->view->platformUid = $platformUid;
        $this->view->showpay = true;
        $this->view->newuser = $isnew ? 1 : 0;
        $this->render();
    }

    protected function _vailid()
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

}