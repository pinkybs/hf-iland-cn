<?php

class Hapyfish2_Platform_Bll_UserMore
{
    public static function getInfo($uid)
    {
        $hc = Hapyfish2_Cache_HighCache::getInstance();
        $key = 'p:u:m:' . $uid;
        $data = $hc->get($key);

        if (!$data) {
        	$data = Hapyfish2_Platform_Cache_UserMore::getUser($uid);
        	if ($data) {
        		$hc->set($key, $data);
        	}
        }

        return $data;
    }

    public static function updateInfo($uid, $user, $savedb = false)
    {
        //get
    	$old = Hapyfish2_Platform_Cache_UserMore::getUser($uid);
    	if ($old && !empty($old['info'])) {
    		if (json_encode($old['info']) == json_encode($user['info'])) {
    			return false;
    		}

    		//if can not get new info , remain keep the old info
    		$oldInfo = $old['info'];
    		foreach ($user['info'] as $field=>$val) {
    		    if (array_key_exists($field, $oldInfo)) {
    		        if (!$val) {
    		            $user['info'][$field] = $oldInfo[$field];
    		        }
    		    }
    		}
    	} else {
    		$data = self::addUser($user);
    		if ($data) {
    			return true;
    		} else {
    			return false;
    		}
    	}
    	$res = Hapyfish2_Platform_Cache_UserMore::updateUser($uid, $user, $savedb);
        if ($res) {
        	$hc = Hapyfish2_Cache_HighCache::getInstance();
        	$key = 'p:u:m:' . $uid;
        	$data = array(
	        	'uid' => $uid,
        		'info' => $user['info']
        	);
        	$hc->set($key, $data);
        }

        return $res;
    }

    public static function addUser($user)
    {
        $res = Hapyfish2_Platform_Cache_UserMore::addUser($user);
        if ($res) {
        	$hc = Hapyfish2_Cache_HighCache::getInstance();
        	$uid = $user['uid'];
        	$key = 'p:u:m:' . $uid;
        	$data = array(
	        	'uid' => $uid,
        		'info' => $user['info']
        	);
        	$hc->set($key, $data);

        	return $data;
        }

        return null;
    }

    public static function updateUserSessionKey($uid, $sessionKey, $savedb = false)
    {
        //get
    	$old = Hapyfish2_Platform_Cache_UserMore::getUserSessionKey($uid);
    	if ($old && $old['session_key'] && !$savedb) {
    	    $savedb = false;
    	}
    	else {
    	    $savedb = true;
    	}
        $res = Hapyfish2_Platform_Cache_UserMore::updateUserSessionKey($uid, $sessionKey, $savedb);
        if ($res) {
        	$hc = Hapyfish2_Cache_HighCache::getInstance();
        	$key = 'p:u:m:s' . $uid;
        	$hc->set($key, $sessionKey);
        	return $sessionKey;
        }

        return null;
    }
}