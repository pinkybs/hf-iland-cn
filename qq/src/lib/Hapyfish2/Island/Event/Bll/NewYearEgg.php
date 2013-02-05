<?php

class Hapyfish2_Island_Event_Bll_NewYearEgg
{
    public static function isGained($uid)
    {
		$key = 'i:u:e:nye:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        
        $data = $cache->get($key);
		if ($data === false) {
			try {
				$dal = Hapyfish2_Island_Event_Dal_NewYearEgg::getDefaultInstance();
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
		//金币3000
		$compensation->setCoin(3000);
		$rnd = rand(1,3);
		if ($rnd == 1) {
			$cid = 41521;
			$itemName = '进财小猫';
		} else if ($rnd == 2) {
			$cid = 41621;
			$itemName = '金币小猫';
		} else {
			$cid = 41721;
			$itemName = '元宝小猫';
		}
		$compensation->setItem($cid, 1);
		//元旦天空 1个
		$compensation->setItem(67712, 1);
		//船只加速卡II 5张
		$compensation->setItem(26341, 3);
		//道具防御卡 5张
		$compensation->setItem(26841, 5);
		$compensation->setFeedTitle('金币3000，' . $itemName . '，新年天空和卡片包。');
		$ok = $compensation->sendOne($uid, '获得砸金蛋礼包：');
		
		if ($ok) {
			if (!$time) {
				$time = time();
			}
			try {
				$key = 'i:u:e:nye:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        		$cache->set($key, $time);
        		
				$dal = Hapyfish2_Island_Event_Dal_NewYearEgg::getDefaultInstance();
				$info = array('uid' => $uid, 'create_time' => $time);
				$dal->insert($uid, $info);
			} catch (Exception $e) {
				info_log($uid, 'Event_NewYearEgg');
			}
			
			$result = array('status' => 1, 'coinChange' => 3000);
		}

		return $result;
    }
}