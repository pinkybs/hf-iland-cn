<?php

class Hapyfish2_Bms_Bll_Auth
{
	public static function login($name, $pwd)
	{
		$user = null;
		try {
			$dalAuth = Hapyfish2_Bms_Dal_Auth::getDefaultInstance();
			$password = md5($pwd. '&' . SECRET);
			$user = $dalAuth->get($name, $password);
		} catch (Exception $e) {
			
		}
		
		if ($user) {
			Hapyfish2_Bms_Bll_Log::login($user['uid']);
		} else {
			Hapyfish2_Bms_Bll_Log::loginerror($name, $pwd);
		}
		
		return $user;
	}
	
	public static function isSuper($uid)
	{
		$super = false;
		try {
			$dalAuth = Hapyfish2_Bms_Dal_Auth::getDefaultInstance();
			$user = $dalAuth->getBuUid($uid);
			if ($user['super'] == 1 && $user['status'] == 0) {
				$super = true;
			}
		} catch (Exception $e) {
			
		}
		
		return $super;
	}
	
	public static function getAccountList()
	{
		$list = null;
		try {
			$dalAuth = Hapyfish2_Bms_Dal_Auth::getDefaultInstance();
			$list = $dalAuth->getList();
		} catch (Exception $e) {
		}
		
		return $list;
	}
	
	public static function getAccount($uid)
	{
		$account = null;
		try {
			$dalAuth = Hapyfish2_Bms_Dal_Auth::getDefaultInstance();
			$account = $dalAuth->getBuUid($uid);
		} catch (Exception $e) {
		}
		
		return $account;
	}
	
	public static function addAccount($info)
	{
		$t = time();
		$ret = null;
		$info['pwd'] = md5($info['pwd']. '&' . SECRET);
		$info['create_time'] = $t;
		try {
			$dalAuth = Hapyfish2_Bms_Dal_Auth::getDefaultInstance();
			$uid = $dalAuth->insert($info);
			if ($uid > 0) {
				$ret = array('uid' => $uid, 'name' => $info['name'], 'real_name' => $info['real_name'], 'status' => $info['status'], 'create_time' => $t);
			}
		} catch (Exception $e) {
		}
		
		return $ret;
	}
	
	public static function updateAccount($uid, $info)
	{
		if (isset($info['pwd']) && !empty($info['pwd'])) {
			$info['pwd'] = md5($info['pwd']. '&' . SECRET);
		}
		try {
			$dalAuth = Hapyfish2_Bms_Dal_Auth::getDefaultInstance();
			$dalAuth->update($uid, $info);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function vailid()
	{
    	$skey = $_COOKIE[SECURITY_KEY];
    	if (!$skey) {
    		return false;
    	}
    	
    	$tmp = split('_', $skey);
    	if (empty($tmp) || count($tmp) != 3) {
    		return false;
    	}
    	
        $uid = $tmp[0];
        $t = $tmp[1];
        $sig = $tmp[2];
        
        $vsig = md5($uid . $t . SECRET);
        if ($sig != $vsig) {
        	return false;
        }
        
        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }
        
        return array('uid' => $uid, 't' => $t);
	}
	
	public static function setVerified($uid)
	{
        $t = time();
        $sig = md5($uid . $t . SECRET);
        
        $skey = $uid .  '_' . $t . '_' . $sig;
        
        setcookie(SECURITY_KEY, $skey , 0, '/', COOKIE_DOMAIN);
	}
	
	public static function setUnverified()
	{
		setcookie(SECURITY_KEY, '' , 0, '/', COOKIE_DOMAIN);
	}

}