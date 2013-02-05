<?php

class Hapyfish2_Island_Cache_Vip
{
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function getBasic()
	{
		$key = 'i:fish:vip';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadvip();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		return $tpl;
	}
	
	public static function loadvip()
	{
		$db = Hapyfish2_Island_Dal_Vip::getDefaultInstance();
		$data = $db->getVipInfo();
		if ($data) {
			$key = 'i:fish:vip';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}

	public static function getGem($uid)
	{
		$key = 'i:u:f:m:v:g:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_Vip::getDefaultInstance();
				$data = $db->getUserGem($uid);
	    	} catch (Exception $e) {
	    		return 0;
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return 0;
	    	}
		}
		return $data;
	}
	
	public static function getVipInfo($vip)
	{
		$basic = self::getBasic();
		if(isset($basic[$vip])){
			return $basic[$vip];
		}
		return null;
	}
	
	public static function getskipLimit($uid)
	{
		$key ='i:u:f:m:vip:s:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			$data['limit'] = 0;
			$data['date'] = $date;
		}
		return $data;
	}
	
	public static function updateskipLimit($uid, $userLimit)
	{
		$key ='i:u:f:m:vip:s:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userLimit);
	}
	
	
	public static function updateGem($uid, $usergem)
	{
		$key = 'i:u:f:m:v:g:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $usergem);
		try{
			$db = Hapyfish2_Island_Dal_Vip::getDefaultInstance();
			$data = $db->updateUserGem($uid, $usergem);
    	} catch (Exception $e) {
    	}
	}
	
	public static function insertVipMessage($uid, $vip)
	{
		$key = 'i:u:f:m:v:m'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $vip);
	}
	
	public static function deleteMessage($uid)
	{
		$key = 'i:u:f:m:v:m'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
	}
	
	public static function getVipMessage($uid)
	{
		$key = 'i:u:f:m:v:m'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$data = 0;
		}
		return $data;
	}
	
	public static function getEventAward($uid)
	{
		$key = 'i:u:f:m:v:m:e:a'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			$data['one'] = -1;
			$data['two'] = -1;
			$data['date'] = $date;
		}
		return $data;
	}
	
	public static function updateEventAward($uid, $Award)
	{
		$key = 'i:u:f:m:v:m:e:a'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->set($key, $Award);
	}
}