<?php

class Hapyfish2_Bms_Bll_Access
{
	public static function getAccessList($uid)
	{
		$list = null;
		try {
			$dalAccess = Hapyfish2_Bms_Dal_Access::getDefaultInstance();
			$list = $dalAccess->getAccessList($uid);
		} catch (Exception $e) {
			
		}
		
		return $list;
	}
	
	public static function getAccess($uid, $pid)
	{
		$info = null;
		try {
			$dalAccess = Hapyfish2_Bms_Dal_Access::getDefaultInstance();
			$info = $dalAccess->getAccess($uid, $pid);
		} catch (Exception $e) {
			
		}
		
		return $info;
	}
	
	public static function allowed($uid, $pid, $type)
	{
		$list = self::getAccessList($uid);
		if ($list) {
			if (isset($list[$pid]) && $list[$pid]['m_' . $type]) {
				return true;
			}
		}
		
		return false;
	}

	public static function addAccess($info)
	{
		$info['create_time'] = time();
		try {
			$dalAccess = Hapyfish2_Bms_Dal_Access::getDefaultInstance();
			$dalAccess->insert($info);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function updateAccess($uid, $pid, $info)
	{
		try {
			$dalAccess = Hapyfish2_Bms_Dal_Access::getDefaultInstance();
			$dalAccess->update($uid, $pid, $info);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function deleteAccess($uid, $pid)
	{
		try {
			$dalAccess = Hapyfish2_Bms_Dal_Access::getDefaultInstance();
			$dalAccess->delete($uid, $pid);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}