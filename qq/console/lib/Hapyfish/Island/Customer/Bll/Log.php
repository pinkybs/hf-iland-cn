<?php

class Hapyfish_Island_Customer_Bll_Log
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
		$info = '[' . $uid . '] - ' . $ip;
		$filename = 'login_' . date('Ymd');
		info_log($info, $filename);
	}
	
	public static function loginerror($name, $pwd)
	{
		$ip = self::getIP();
		$info = '[' . $name . '][' . $pwd . '] - ' . $ip;
		$filename = 'loginerror_' . date('Ymd');
		info_log($info, $filename);
	}
	
}