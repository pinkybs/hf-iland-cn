<?php

class Hapyfish2_Island_Bll_AppInfo
{	
	
	/**
	 * 检查应用状态
	 *
	 * @param int $uid
	 * @param bool $redirect
	 * @param bool $force    强制,包括uid=0的时候
	 * @param Array $info
	 */
	public static function checkStatus($uid = 0, $redirect = false, $force = true, $info = null)
	{
		if ($info == null) {
			$info = self::getAdvanceInfo();
		}
		
		if ($info) {
			//IP黑名单无效
			if (!empty($info['black_ip_list'])) {
				$ip = self::getClientIP();
				if ($ip) {
					if (in_array($ip, $info['black_ip_list'])) {
						header('HTTP/1.1 403 Forbidden');
						exit;
					}
				}
			}
			
			$status = $info['app_status'];
			if ($status != 1) { //非全部开放状态
				//IP白名单有效
				if (!empty($info['white_ip_list'])) {
					$ip = self::getClientIP();
					if ($ip) {
						if (in_array($ip, $info['white_ip_list'])) {
							return $info;
						}
					}
				}
				
				if ($status == 0) { //停机维护
					if ($redirect) {
						self::redirect(HOST . '/index/maintance?ts=' . $info['update_time']);
					}
					exit;
				} else if ($status == 2) { //测试人员开放
					if ($uid > 0) {
						if (in_array($uid, $info['test_id_list'])) {
							return $info;
						}
					}
				} else if ($status == 3) { //开发人员开放
					if ($uid > 0) {
						if (in_array($uid, $info['dev_id_list'])) {
							return $info;
						}
					}
				}
				
				if ($uid == 0 && !$force) {
					return $info;
				}
				
				//缺省
				if ($redirect) {
    				self::redirect(HOST . '/index/maintance?ts=' . $info['update_time']);
				}
				exit;
			}
		}
		
		return $info;
	}
	
	public static function redirect($url, $top = false)
	{
		if ($top) {
			echo '<html><body><script type="text/javascript">window.top.location.href="' . $url . '";</script></body></html>';
		} else {
			header('Location: ' . $url);
		}
		exit;
	}
	
    public static function getClientIP()
    {
    	$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ('^(10|172\.16|192\.168)\.', $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }
	
	public static function getAppInfo()
	{
		return Hapyfish2_Island_Cache_AppInfo::getInfo();
	}
	
	public static function getAdvanceInfo()
	{
		$info = Hapyfish2_Island_Cache_AppInfo::getInfo();
		
		$white_ip_list = array();
		if (!empty($info['white_ip_list'])) {
			$white_ip_list = explode(',', $info['white_ip_list']);
		}
		$info['white_ip_list'] = $white_ip_list;
		
		$black_ip_list = array();
		if (!empty($info['black_ip_list'])) {
			$black_ip_list = explode(',', $info['black_ip_list']);
		}
		$info['black_ip_list'] = $black_ip_list;
		
		$dev_id_list = array();
		if (!empty($info['dev_id_list'])) {
			$dev_id_list = explode(',', $info['dev_id_list']);
		}
		$info['dev_id_list'] = $dev_id_list;
		
		$test_id_list = array();
		if (!empty($info['test_id_list'])) {
			$test_id_list = explode(',', $info['test_id_list']);
		}
		$info['test_id_list'] = $test_id_list;
		
		return $info;
	}
	
	public static function update($appId, $info)
	{
		try {
			$db = Hapyfish2_Island_Dal_AppInfo::getDefaultInstance();
			$db->update($appId, $info);
			Hapyfish2_Island_Cache_AppInfo::loadInfo();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function loadToMemcached()
	{
		return Hapyfish2_Island_Cache_AppInfo::loadInfo();
	}
	
	public static function loadToAPC()
	{
		$key = Hapyfish2_Island_Cache_AppInfo::getKey('Info');
		$cache = Hapyfish2_Island_Cache_AppInfo::getBasicMC();
		$info = $cache->get($key);
		
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $info);
		
		return $info;
	}
	
	public static function getFromMemcached()
	{
		$key = Hapyfish2_Island_Cache_AppInfo::getKey('Info');
		$cache = Hapyfish2_Island_Cache_AppInfo::getBasicMC();
		return $cache->get($key);
	}
	
	public static function getFromAPC()
	{
		$key = Hapyfish2_Island_Cache_AppInfo::getKey('Info');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		return $localcache->get($key);
	}

}