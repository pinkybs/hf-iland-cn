<?php


class Hapyfish2_Island_Event_Bll_CollectKitten
{
    public static function getKittenStatus($uid)
    {
    	$result = array('41521' => 0, '41621' => 0, '41721' => 0);
    	$buildings = Hapyfish2_Island_HFC_Building::getAll($uid);
    	if (!$buildings) {
    		return $result;
    	}
    	
    	foreach($buildings as $item) {
    		if ($item['cid'] == 41521) {
    			$result['41521'] += 1;
    		} else if ($item['cid'] == 41621) {
    			$result['41621'] += 1;
    		} else if ($item['cid'] == 41721) {
    			$result['41721'] += 1;
    		}
    	}
    	
    	return $result;
    }
	
	public static function exchangeKitten($uid, $cid)
    {
    	$userStarFish = Hapyfish2_Island_HFC_User::getUserStarFish($uid);
    	if ($userStarFish < 3) {
    		return false;
    	}
    	
    	$buildingInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($cid);
    	if (!$buildingInfo) {
    		return false;
    	}
    	$now = time();
    	
		$newBuilding = array(
			'uid' => $uid,
			'cid' => $cid,
			'status' => 0,
			'item_type' => $buildingInfo['item_type'],
			'buy_time' => $now
		);
		
		$ok = Hapyfish2_Island_HFC_Building::addOne($uid, $newBuilding);
		if ($ok) {
			$summary = '兑换' . $buildingInfo['name'] . '消耗3个海星';
			$ok2 = Hapyfish2_Island_Bll_StarFish::consume($uid, 3, $summary, $now);
			if (!$ok2) {
				info_log($uid . ':' . $cid, 'exchangeKitten-error');
			}
			
			//
			$title = '成功兑换' . $buildingInfo['name'] . '，消耗3个海星';
			$feed = array(
				'uid' => $uid,
				'template_id' => 0,
				'actor' => $uid,
				'target' => $uid,
				'type' => 3,
				'title' => array('title' => $title),
				'create_time' => $now
			);
			Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
		}
		
		return $ok;
    }
	
	public static function isGained($uid)
    {
		$key = 'i:u:e:ckitten:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        
        $data = $cache->get($key);
		if ($data === false) {
			try {
				$dal = Hapyfish2_Island_Event_Dal_CollectKitten::getDefaultInstance();
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

		//摩羯座74632 1个
		$compensation->setItem(74632, 1);
		
		$compensation->setFeedTitle('收集小猫获得奖品：摩羯座');
		$ok = $compensation->sendOne($uid, '');
		
		if ($ok) {
			if (!$time) {
				$time = time();
			}
			try {
				$key = 'i:u:e:ckitten:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        		$cache->set($key, $time);
        		
				$dal = Hapyfish2_Island_Event_Dal_CollectKitten::getDefaultInstance();
				$info = array('uid' => $uid, 'create_time' => $time);
				$dal->insert($uid, $info);
			} catch (Exception $e) {
				info_log($uid, 'Event_CollectKitten');
			}
			
			$result = array('status' => 1);
		}

		return $result;
    }
    
}