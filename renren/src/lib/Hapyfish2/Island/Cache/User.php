<?php

class Hapyfish2_Island_Cache_User
{
	public static function isAppUser($uid)
    {
        $key = 'i:u:isapp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);

        if ($data === false) {
			if ($cache->isNotFound()) {
				$levelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
				if (!$levelInfo) {
					return false;
				} else {
					$data = 'Y';
					$cache->set($key, $data);
					return true;
				}
			} else {
				return false;
			}
        }
        
        if ($data == 'Y') {
        	return true;
        } else {
        	return false;
        }
    }
    
    public static function setAppUser($uid)
    {
        $key = 'i:u:isapp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $cache->set($key, 'Y');
    }
    
    public static function canEZineShow($uid, $todayTime, $version)
    {
        $key = 'i:u:ezinecount:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $show = true;
        if ($data === false) {
        	$data = array($version, $todayTime, 0);
        	$cache->add($key, $data, 864000);
        } else {
        	if ($data[0] < $version) {
        		$data = array($version, $todayTime, 0);
        		$cache->set($key, $data, 864000);
        	} else if ($data[1] < $todayTime) {
				if ($data[2] >= 7) {
        			$show = false;
        		} else {
	        		$data[1] = $todayTime;
	        		$data[2] += 1;
	        		$cache->set($key, $data, 864000);
        		}
        	} else {
        		$show = false;
        	}
        }
        
        return $show;
    }
    
    public static function showEZine($uid, $todayTime)
    {
    	$EZineStatus = Hapyfish2_Island_Cache_BasicInfo::getEZineStatus();
    	if ($EZineStatus['show']) {
    		return self::canEZineShow($uid, $todayTime, $EZineStatus['ver']);
    	} else {
    		return false;
    	}
    }
    
    public static function addUserIndulge($uid)
    {
    	$time = time();
    	$key = 'i:u:indulge'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->set($key, $uid, 5*60*60);
    }
    
    public static function getUserIndulge($uid)
    {
    	$key = 'i:u:indulge'.$uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if($data === false){
        	return null;
        }
        return $data;
    }
    
    public static function indulgeTime($uid)
    {
    	$key = 'i:u:indulge:time'.$uid;
    	$date = date('Ymd');
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	if($data === false){
    		$data['date'] = $date;
    		$data['time'] = 0;
    	}
    	if($data){
    		if($data['date'] != $date){
    			$data['date'] = $date;
    			$data['time'] = 0;
    		}
    	}
    	return $data;
    }
    
    public static function setUserindulgeTime($uid)
    {
    	$indulgeTime = self::indulgeTime($uid);
    	if($indulgeTime['time'] >= 18){
    		$indulgeTime['time'] = 1;
    	}else{
    		$indulgeTime['time'] += 1;
    	}
    	$key = 'i:u:indulge:time'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->set($key, $indulgeTime);
    	return $indulgeTime['time'];
    }
    
}