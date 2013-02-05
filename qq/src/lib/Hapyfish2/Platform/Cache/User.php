<?php

class Hapyfish2_Platform_Cache_User
{
    public static function getUser($uid)
    {
        $key = 'p:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$result = $cache->get($key);
        if ($result === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
		            $result = $dalUser->getInfo($uid);
		            if ($result) {
		            	$cache->add($key, $result);
		            } else {
				        return array(
				        	'uid' => $uid,
				        	'openid' => '',
				        	'nickname' => '未知',
				        	'figureurl' => 'http://xy.store.qq.com/01ca4cb4615550879958494fcd2726b3f82eab143c9897eb0',
				        	'gender' => -1,
				        	'is_vip' => 0,
				        	'is_year_vip' => 0,
				        	'vip_level' => 0,
				        	'create_time' => 0
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

		if (!isset($result[8]) || !$result[8]) {
            try {
	            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
	            $result = $dalUser->getInfo($uid);
	            if ($result) {
	            	$cache->update($key, $result);
	            }
        	} catch (Exception $e) {
				return null;
			}
        }

        return array(
        	'uid' => $result[0],
        	'openid' => $result[1],
        	'nickname' => $result[2],
        	'figureurl' => $result[3],
        	'gender' => $result[4],
        	'is_vip' => $result[5],
        	'is_year_vip' => $result[6],
        	'vip_level' => $result[7],
        	'create_time' => $result[8]
        );
    }

    public static function updateUser($uid, $user, $savedb = false)
    {
        $key = 'p:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array($uid, $user['openid'], $user['nickname'], $user['figureurl'], $user['gender'], $user['is_vip'], $user['is_year_vip'], $user['vip_level'], $user['create_time']);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 3600);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
	        	try {
	        		$info = array(
	        			'nickname' => $user['nickname'],
	        			'figureurl' => $user['figureurl'],
	        			'gender' => $user['gender'],
	        			'is_vip' => $user['is_vip'],
	        			'is_year_vip' => $user['is_year_vip'],
	        			'vip_level' => $user['vip_level']
	        		);
	        		$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
	        		$r = $dalUser->update($uid, $info);
	        		if ($r == 0) {
	        			$info['uid'] = $uid;
	        			$info['openid'] = $user['openid'];
	        			$dalUser->add($info);
	        		}
	        	} catch (Exception $e) {
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
		if (!isset($user['create_time'])) {
        	$user['create_time'] = time();
        }
    	$key = 'p:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array($uid, $user['openid'], $user['nickname'], $user['figureurl'], $user['gender'], $user['is_vip'], $user['is_year_vip'], $user['vip_level'], $user['create_time']);

        $ok = $cache->save($key, $data);
        if ($ok) {
        	try {
        		$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
        		$dalUser->add($user);
        	}catch (Exception $e) {

        	}
        }

        return $ok;
    }

    public static function getStatus($uid)
    {
        /*
    	$key = 'p:u:s:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $status = $cache->get($key);
        if ($status === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
		            $status = $dalUser->getStatus($uid);
		            $cache->add($key, $status);
		            return $status;
        		} catch (Exception $e) {
        			return -1;
        		}
        	} else {
        		return -1;
        	}
        }*/
    	$statusInfo = self::getStatus2($uid);

        return $statusInfo['status'];
    }

    public static function getStatus2($uid)
    {
        $key = 'p:u:s2:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
		            $data = $dalUser->getStatus2($uid);
		            if ($data) {
		            	$cache->add($key, $data);
		            } else {
		            	$data = array(-1, 0);
		            }
        		} catch (Exception $e) {
        			$data = array(-1, 0);
        		}
        	} else {
				$data = array(-1, 0);
        	}
        }

        return array('status' => $data[0], 'status_update_time' => $data[1]);
    }

    public static function updateStatus($uid, $status, $savedb = true)
    {
        $key = 'p:u:s2:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $t = time();
        $data = array($status, $t);
        $result = $cache->set($key, $data);

        if ($savedb && $result) {
			try {
        		$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
        		$dalUser->updateStatus($uid, $status, $t);
        	} catch (Exception $e) {
        	}
        }

        return $result;
    }
}