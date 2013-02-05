<?php

class Hapyfish2_Island_Event_Bll_TestGift
{
    public static function isGained($uid)
    {
		$key = 'i:u:e:tstg:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        
        $data = $cache->get($key);
		if ($data === false) {
			try {
				$dal = Hapyfish2_Island_Event_Dal_TestGift::getDefaultInstance();
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

		//圣诞岛 1个
		$compensation->setItem(27411, 1);
		//小溪流水池 1个
		$compensation->setItem(34421, 1);
		//船只加速卡II 3张
		$compensation->setItem(26341, 3);
		//防御卡 5张
		$compensation->setItem(26841, 5);
		
		$compensation->setFeedTitle('获得测试大礼包一份。');
		$ok = $compensation->sendOne($uid, '');
		
		if ($ok) {
			if (!$time) {
				$time = time();
			}
			try {
				$key = 'i:u:e:tstg:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        		$cache->set($key, $time);
        		
				$dal = Hapyfish2_Island_Event_Dal_TestGift::getDefaultInstance();
				$info = array('uid' => $uid, 'create_time' => $time);
				$dal->insert($uid, $info);
			} catch (Exception $e) {
				info_log($uid, 'Event_TestGift');
			}
			
			$result = array('status' => 1);
		}

		return $result;
    }
}