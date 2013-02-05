<?php

class Hapyfish2_Island_Event_Cache_FishAward
{
	public static function getUserFishAward($uid, $id)
	{
		$key = 'i:e:u:fh:ex'.$uid.':lid:'.$id;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false) {
			return 0;
		}else {
			return 1;
		}
	}
	
	public static function setUserFishAward($uid, $id)
	{
		$key = 'i:e:u:fh:ex'.$uid.':lid:'.$id;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, 1);
	}	
}