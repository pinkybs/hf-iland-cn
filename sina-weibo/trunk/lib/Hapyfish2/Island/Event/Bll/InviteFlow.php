<?php


class Hapyfish2_Island_Event_Bll_InviteFlow
{
    public static function getState($uid)
    {
    	$result = array('result' => array('status' => 1));
    	$step = self::getInviteStep($uid);
		$result['step'] = $step + 1;
		$result['friendsList'] = self::getInviteFriendList($uid, $result['step']);

		return $result;
    }

    public static function getInviteStep($uid)
    {
    	
		$key = 'i:u:e:invf_gold2:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);

        $data = $cache->get($key);
 
		if ($data === false) {
			try {
    			$dalInviteFlow = Hapyfish2_Island_Event_Dal_InviteFlow::getDefaultInstance();
    			$data = $dalInviteFlow->getStep($uid);
    			$cache->set($key, $data);
				return $data;
			} catch (Exception $e) {
				return -1;
			}
		} else {
			return $data;
		}
    }

    public static function getInviteFriendList($uid, $step)
    {
    	$friendList = array();
    	if ($step < 0 || $step > 4) {
    		return $friendList;
    	}

    	$inviteList = Hapyfish2_Island_Bll_InviteLog::getAllOfFlow($uid);
    	if (!$inviteList) {
    		return $friendList;
    	}

    	$count = count($inviteList);
    	if ($step == 1) {
    		$start = 0;
    		$end = 4;
    		if ($count < $end) {
    			$end = $count;
    		}
    	} else if ($step == 2) {
    		$start = 4;
    		$end = 7;
    		if ($count < $end) {
    			$end = $count;
    		}
    	} else if ($step == 3) {
    	    $start = 7;
    		$end = 9;
    		if ($count < $end) {
    			$end = $count;
    		}
    	} else if ($step == 4) {
			$start = 9;
    		$end = 10;
    		if ($count < $end) {
    			$end = $count;
    		}
    	}

		for($i = $start; $i < $end; $i++) {
    		$fid = $inviteList[$i]['fid'];
    		$info = Hapyfish2_Platform_Bll_User::getUser($fid);
    		$friendList[] = array(
    			'name' => $info['name'],
    			'face' => $info['figureurl']
    		);
		}

		return $friendList;
    }

    public static function isGaind($uid, $step)
    {
    	$nowStep = self::getInviteStep($uid);
    	if ($step < $nowStep) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public static function gain($uid, $step, $time = null)
    {
    	$result = array('result' => array('status' => '-1', 'content' => 'serverWord_110'));

    	if ($step < 0 || $step > 4) {
    		return  $result;
    	}

    	$compensation = new Hapyfish2_Island_Bll_Compensation();

    	if ($step == 1) {
			//邀请4名新玩家，奖励糖果秋千53421
			//$compensation->setGold(53421, 1);
			//2011-03-09 改成送宝石 
			$goldnum = 50;
			$compensation->setGold($goldnum, 0);
			$title = '恭喜你获得邀请好礼第一重礼包！';
    	} else if ($step == 2) {
			//邀请3名新玩家，奖励兔爷54321
			//$compensation->setItem(54321, 1);
			//2011-03-09 改成送宝石 
			$goldnum = 20;
			$compensation->setGold($goldnum, 0);
			$title = '恭喜你获得邀请好礼第二重礼包！';
    	} else if ($step == 3) {
    		//邀请2名新玩家，奖励丘比特天空23312
    		//$compensation->setItem(23312, 1);
    		//2011-03-09 改成送宝石 
    		$goldnum = 20;
			$compensation->setGold($goldnum, 0);
    		$title = '恭喜你获得邀请好礼第三重礼包！';
    	} else if ($step == 4) {
    		//邀请1名新玩家，奖励人气王门牌75421
    		//$compensation->setItem(75421, 1);
    		//2011-03-09 改成送宝石 
			$goldnum = 10;
    		$compensation->setGold($goldnum, 0);
    		$title = '恭喜你获得邀请好礼第四重礼包！';
    	}

		$compensation->setFeedTitle($title);
		$ok = $compensation->sendOne($uid, '');

		if ($ok) {
			if (!$time) {
				$time = time();
			}
			try {
				$key = 'i:u:e:invf_gold2:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        		$cache->set($key, $step);

				$dal = Hapyfish2_Island_Event_Dal_InviteFlow::getDefaultInstance();
				$info = array('uid' => $uid, 'step' => $step, 'create_time' => $time);
				$dal->insert($uid, $info);
			} catch (Exception $e) {
				info_log($uid, 'Event_InviteFlow');
			}

			$result = array('result' => array('status' => 1,'goldChange' => $goldnum));
			
			//update by hudanfeng add send gold log start
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('801', array($uid, $goldnum, 9));
			//end				
		}

		return $result;
    }
}