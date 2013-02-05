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

	public static function getUserNextBigGiftLevel($uid)
    {
        $key = 'i:u:ngiftl:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
        	$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
        	$userLevel = $userLevelInfo['level'];
        	$nextLevel = 5;
            $list = Hapyfish2_Island_Cache_BasicInfo::getStepGiftLevelList();
            foreach ( $list as $val ) {
            	if ( $val['level'] <= $userLevel ) {
            		$nextLevel = $val['level'] + 5;
            	}
            }
            
        	$data = $nextLevel;
        	$cache->add($key, $data);
        }
        return $data;
    }
    
	public static function updateUserNextBigGiftLevel($uid, $level)
    {
        $key = 'i:u:ngiftl:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $level);
    }
    
	public static function isAutoUserDock($uid, $pid)
    {
        $key = 'i:u:autodock:' . $uid . ':p:' . $pid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data == 1) {
        	return 1;
        } else {
        	return 0;
        }
    }

	public static function updateAutoUserDock($uid, $pid, $auto)
    {
        $key = 'i:u:autodock:' . $uid . ':p:' . $pid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $auto);
    }
        
}