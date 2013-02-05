<?php

/**
 * Event ReceivePlant
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/11    zhangli
*/
class Hapyfish2_Island_Event_Cache_ReceivePlant
{
	/**
	 * @获取用户建筑领取状态
	 * @param int $uid
	 * @return Array
	 */
	public static function getExchangeAble($uid)
	{
		$key = 'ev:exchange:able:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$list = $cache->get($key);
		
		if ($list == false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_ReceivePlant::getDefaultInstance();
				$listStr = $db->getExchangeAble($uid);
			} catch (Exception $e) {}
			
			if ($listStr) {
				$list = json_decode($listStr);
				$cache->set($key, $list);
			} else {
				$list = array(0, 0, 0);
			}
		}
		
		return $list;
	}
	
	/**
	 * @更新领取状态
	 * @param int $uid
	 * @param Array $list
	 */
	public static function renewExchangeAble($uid, $listNew)
	{
		$db = Hapyfish2_Island_Event_Dal_ReceivePlant::getDefaultInstance();
		
		$key = 'ev:exchange:able:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$list = $cache->get($key);
		
		if ($list == false) {
 			try {
				$listStr = $db->getExchangeAble($uid);
			} catch (Exception $e) {}
			
			if ($listStr) {
				$list = json_decode($listStr);
				$cache->set($key, $list);
			}
		}
		
		if ($list === false) {
			try {
				$listStrNew = json_encode($listNew);
				$listState = $db->incExchangeAble($uid, $listStrNew);
			} catch (Exception $e) {}

		} else {
			try {
				$listStrNew = json_encode($listNew);
				$listStr = $db->renewExchangeAble($uid, $listStrNew);
			} catch (Exception $e) {}
		}
		
		$cache->set($key, $listNew);
	}
}