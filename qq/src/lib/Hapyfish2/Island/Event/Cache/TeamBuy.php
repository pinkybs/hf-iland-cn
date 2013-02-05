<?php

/**
 * Event TeamBuy
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/12/13    zhangli
*/
class Hapyfish2_Island_Event_Cache_TeamBuy
{
    /**
     * @获取团购信息
     * @return Array
     */
	public static function getData()
	{
		$key = 'ev:teambuy:data';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$data = $cache->get($key);
		
		if ($data === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
				$data = $db->getData();
			} catch (Exception $e) {}
			
			if ($data) {
				$cache->set($key, $data);
			}
		}
		
		return $data;
	}
	
	/**
	 * @用户参加团购状态
	 * @param int $uid
	 * @return int
	 */
	public static function getStatus($uid)
	{
		$key = 'ev:teambuy:join:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		if ($data === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
				$data = $db->getStatus($uid);
			} catch (Exception $e) {}

			if ($data) {
				$cache->set($key, $data);
			}
		}
		
		return (int)$data;
	}
	
	/**
	 * @更新用户参加团购状态
	 * @param int $uid
	 * @return int
	 */
	public static function addStatus($uid)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
			$db->addStatus($uid);
		} catch (Exception $e) {}
	
		$key = 'ev:teambuy:join:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, -1);
	}
	
	public static function updateStatus($uid)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
			$db->updateStatus($uid);
		} catch (Exception $e) {}
	
		$key = 'ev:teambuy:join:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, 1);
	}
	
	/**
     * @更新团购信息
     * @param int $num
     */
	public static function renewData($num)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
			$db->renewData($num);
		} catch (Exception $e) {}
	
		$key = 'ev:teambuy:data';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
	}
	
    /**
     * @参加团购的人数
     * @return int
     */
	public static function getNum()
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
			$data = $db->getNum();
		} catch (Exception $e) {}
		
		return $data;
	}
	
    /**
     * @购买的人数
     * @return int
     */
	public static function getBuyNum()
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
			$data = $db->getBuyNum();
		} catch (Exception $e) {}
		
		return $data;
	}
	
}