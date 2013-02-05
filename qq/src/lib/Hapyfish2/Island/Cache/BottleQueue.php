<?php
class Hapyfish2_Island_Cache_BottleQueue
{
	protected static $_key = 'bottle:queue';
	
	protected static $_count = 20;
	
	public static function unshift($val, $uid)
	{
		if ($uid) {
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$queue = $cache->get(self::$_key);
			$queue = array_slice($queue, 0, self::$_count-1);
			if ($queue) {
				array_unshift($queue, $val);
			} else {
				$queue = array($val);
			}
			return $cache->set(self::$_key, $queue, 0);
		} else {
			return false;
		}
		
	}
	
	public static function getall($uid)
	{
		if ($uid) {
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$val = $cache->get(self::$_key);
			return $val;
		} else {
			return array();
		}
	}
	
	public static function clear($uid)
	{
		try {
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$cache->delete(self::$_key);
		} catch (Exception $e) {
			return false;
		}
		
		return true;
	}
	
}