<?php

class Hapyfish2_Platform_Cache_UserMore
{
    public static function getUser($uid)
    {
        $key = 'p:u:m:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$result = $cache->get($key);
        if ($result === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_UserMore::getDefaultInstance();
		            $result = $dalUser->getUser($uid);
		            if ($result) {
		            	$cache->add($key, $result);
		            } else {
				        return array(
				        	'uid' => $uid,
				        	'info' => array()
				        );
		            }
        		}
	            catch (Exception $e) {
	            	return null;
	            }
        	} else {
        		return null;
        	}
        }

        return array(
        	'uid' => $result['uid'],
        	'info' => $result['info']? json_decode($result['info'], true) : array()
        );
    }

    public static function updateUser($uid, $user, $savedb = false)
    {
        $key = 'p:u:m:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $moreInfo = json_encode($user['info']);
        $data = array('uid' => $user['uid'], 'info' => $moreInfo);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 3600);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
	        	try {
	        		$info = array(
	        			'info' => $moreInfo
	        		);
	        		$dalUser = Hapyfish2_Platform_Dal_UserMore::getDefaultInstance();
	        		$r = $dalUser->update($uid, $info);
	        	} catch (Exception $e) {
                    error_log('Platform_Cache_UserMore:updateUser:'.$e->getMessage());
	        	}
        	}
        }
        else {
    		$ok = $cache->update($key, $data);
    	}

        return $ok;
    }

    public static function addUser($user)
    {
        $uid = $user['uid'];
        $moreInfo = json_encode($user['info']);

    	$key = 'p:u:m:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        $data = array('uid' => $user['uid'], 'info' => $moreInfo);
        $ok = $cache->save($key, $data);
        if ($ok) {
        	try {
        		$dalUser = Hapyfish2_Platform_Dal_UserMore::getDefaultInstance();
        		$dalUser->add($data);
        	}catch (Exception $e) {
                error_log('Platform_Cache_UserMore:addUser:'.$e->getMessage());
        	}
        }

        return $ok;
    }

    public static function getUserSessionKey($uid)
    {
        $key = 'p:u:m:s' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$result = $cache->get($key);
        if ($result === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_UserMore::getDefaultInstance();
		            $result = $dalUser->getUser($uid);
		            if ($result) {
		            	$cache->add($key, $result);
		            } else {
				        return array(
				        	'uid' => $uid,
				        	'session_key' => ''
				        );
		            }
        		}
	            catch (Exception $e) {
	            	return null;
	            }
        	} else {
        		return null;
        	}
        }

        return array(
        	'uid' => $result['uid'],
        	'session_key' => $result['session_key']
        );
    }

    public static function updateUserSessionKey($uid, $sessionKey, $savedb = false)
    {
        $key = 'p:u:m:s' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 86400);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $sessionKey);
        	if ($ok) {
	        	try {
	        		$info = array(
	        			'session_key' => $sessionKey
	        		);
	        		$dalUser = Hapyfish2_Platform_Dal_UserMore::getDefaultInstance();
	        		$r = $dalUser->update($uid, $info);
	        	} catch (Exception $e) {
                    error_log('Platform_Cache_UserMore:updateUserSessionKey:'.$e->getMessage());
	        	}
        	}
        }
        else {
    		$ok = $cache->update($key, $sessionKey);
    	}

        return $ok;
    }

}