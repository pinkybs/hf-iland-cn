<?php

class Hapyfish2_Island_Event_Bll_Active5DayVer2
{
	protected static $_cacheKey = 'i:u:e:atv5dver2:';

    public static function isGained($uid)
    {
		$key = self::$_cacheKey . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);

        $data = $cache->get($key);
		if ($data === false) {
			try {
				$dal = Hapyfish2_Island_Event_Dal_Active5DayVer2::getDefaultInstance();
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

		//水瓶座74732 1个
		$compensation->setItem(74732, 1);
		$ok = $compensation->sendOne($uid, '连续登陆5天获得奖品：');

		if ($ok) {
			if (!$time) {
				$time = time();
			}
			try {
				$key = self::$_cacheKey . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        		$cache->set($key, $time);

				$dal = Hapyfish2_Island_Event_Dal_Active5DayVer2::getDefaultInstance();
				$info = array('uid' => $uid, 'create_time' => $time);
				$dal->insert($uid, $info);
			} catch (Exception $e) {
				info_log('gain error:'.$uid, 'Event_Active5dayVer2_Err');
				info_log($e->getMessage(), 'Event_Active5dayVer2_Err');
			}

			$result = array('status' => 1);
		}

		return $result;
    }
}