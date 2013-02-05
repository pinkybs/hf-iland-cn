<?php

class Hapyfish2_Island_Cache_AppInfo
{
	const NAMESPACE = 'island:appinfo:';
	
	public static function getKey($word)
	{
		return self::NAMESPACE . $word;
	}
	
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function getBasicDB()
	{
		return Hapyfish2_Island_Dal_AppInfo::getDefaultInstance();
	}
	
	public static function getInfo()
	{
		$key = self::getKey('Info');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$info = $localcache->get($key);
		if (!$info) {
			$cache = self::getBasicMC();
			$info = $cache->get($key);
			if (!$info) {
				$info = self::loadInfo();
			}
			if ($info) {
				$localcache->set($key, $info);
			}
		}
		
		return $info;
	}
	
	public static function loadInfo()
	{
		$db = self::getBasicDB();
		$info = $db->getInfo();
		if ($info) {
			$key = self::getKey('Info');
			$cache = self::getBasicMC();
			$cache->set($key, $info);
		}
		
		return $info;
	}
		
}