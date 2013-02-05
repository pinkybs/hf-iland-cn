<?php

class Hapyfish2_Island_Event_Bll_Active5Day
{
    public static function isGained($uid)
    {
		$key = 'i:u:e:atv5d:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        
        $data = $cache->get($key);
		if ($data === false) {
			try {
				$dal = Hapyfish2_Island_Event_Dal_Active5Day::getDefaultInstance();
				$data = $dal->get($uid);
				if ($data) {
					$cache->set($key, $data);
					return true;
				}
				return false;
			} catch (Exception $e) {
				return true;
			}
		} else {
			return true;
		}
    }
    
    public static function gain($uid, $time = null)
    {
    	$result = array('status' => '-1', 'content' => 'serverWord_110');
    	
    	$compensation = new Hapyfish2_Island_Bll_Compensation();

		//马戏团41831 1个
		$compensation->setItem(41831, 1);
		
		$compensation->setFeedTitle('连续登陆5天获得奖品：马戏团');
		$ok = $compensation->sendOne($uid, '');
		
		if ($ok) {
			if (!$time) {
				$time = time();
			}
			try {
				$key = 'i:u:e:atv5d:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        		$cache->set($key, $time);
        		
				$dal = Hapyfish2_Island_Event_Dal_Active5Day::getDefaultInstance();
				$info = array('uid' => $uid, 'create_time' => $time);
				$dal->insert($uid, $info);
			} catch (Exception $e) {
				info_log($uid, 'Event_Active5day');
			}
			
			$result = array('status' => 1);
		}

		return $result;
    }
}