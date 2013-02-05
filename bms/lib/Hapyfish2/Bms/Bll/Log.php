<?php

class Hapyfish2_Bms_Bll_Log
{
	public static function getIP()
	{
		$ip = false;
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if (!$ip) {
			$ip = '';
		}
		
		return $ip;
	}
	
	public static function login($uid)
	{
		$ip = self::getIP();
		$info = array('login_time' => time(), 'uid' => $uid, 'ip' => $ip);
		try {
			$dalLog = Hapyfish2_Bms_Dal_LoginLog::getDefaultInstance();
			$dalLog->insert($info);
		} catch (Exception $e) {
			
		}
	}
	
	public static function loginerror($name, $pwd)
	{
		$ip = self::getIP();
		$info = '[' . $name . '][' . $pwd . '] - ' . $ip;
		$filename = 'loginerror_' . date('Ymd');
		info_log($info, $filename);
	}
	
	public static function getLogin($uid, $limit = 10)
	{
		$logs = null;
		try {
			$dalLog = Hapyfish2_Bms_Dal_LoginLog::getDefaultInstance();
			$logs = $dalLog->get($uid, $limit);
		} catch (Exception $e) {
			
		}
		
		return $logs;
	}
	
	public static function operation($uid, $platform, $content)
	{
		$info = array('do_time' => time(), 'uid' => $uid, 'platform' => $platform, 'content' => $content);
		try {
			$dalLog = Hapyfish2_Bms_Dal_OperationLog::getDefaultInstance();
			$dalLog->insert($info);
		} catch (Exception $e) {
			
		}
	}
	
	public static function getOperation($uid, $limit = 10)
	{
		$logs = null;
		try {
			$dalLog = Hapyfish2_Bms_Dal_OperationLog::getDefaultInstance();
			$logs = $dalLog->get($uid, $limit);
		} catch (Exception $e) {
			
		}
		
		return $logs;
	}
	
}