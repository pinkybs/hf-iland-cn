<?php
class Hapyfish2_Island_Event_Bll_UpgradeGift
{
	
	public static function getTF($uid)
	{
		$key = 'i:u:upgradegift:' . $uid;
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		return ($data ? true : false);
	}
	
	public static function setTF($uid)
	{
		$key = 'i:u:upgradegift:' . $uid;
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		return $cache->add($key, 1, Hapyfish2_Cache_Memcached::LIFE_TIME_ONE_MONTH);
	}
	
	public static function clearTF($uid) 
	{
		$key = 'i:u:upgradegift:' . $uid;
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		return $cache->delete($key);		
	}
	
	public static function gifttouser($uid)
	{
		if ($uid) {
			
			$gold = 10;
			$coin = 20000;
			$items[] = array('74841', 5);
			$items[] = array('67441', 5);
			$items[] = array('67541', 5);
			$items[] = array('26441', 10);
			$items[] = array('56641', 5);
			$items[] = array('56741', 2);
			$items[] = array('45231', 1);
			
			$com = new Hapyfish2_Island_Bll_Compensation();
			
			$com->setCoin($coin);
			$com->setGold($gold);
			foreach ($items as $key => $val) {
				$com->setItem($val[0], $val[1]);
			}
			
			return $com->sendOne($uid,'');
		} else {
			return false;
		}
	}
	
	public static function getData($uid)
	{
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$plants = $db->getAllCidRow($uid);
			
			foreach ($plants as $plant) {
				info_log($plant, 'moveDataCol');
			}
			
			$dal = Hapyfish2_Island_Dal_Building::getDefaultInstance();
			$buildings = $dal->getAllData($uid);
			
			foreach ($buildings as $building) {
				info_log($building, 'moveDataCol');
			}
		} catch (Exception $e) {
			info_log($e, 'moveDataErr');
		}
		
		return true;
	}
	
}