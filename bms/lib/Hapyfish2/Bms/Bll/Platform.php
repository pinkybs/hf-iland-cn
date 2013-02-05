<?php

class Hapyfish2_Bms_Bll_Platform
{
	public static function getList()
	{
		$list = null;
		try {
			$dalPlatform = Hapyfish2_Bms_Dal_Platform::getDefaultInstance();
			$list = $dalPlatform->getList();
		} catch (Exception $e) {
			
		}
		
		return $list;
	}
	
	public static function getInfoByName($name)
	{
		$info = null;
		try {
			$dalPlatform = Hapyfish2_Bms_Dal_Platform::getDefaultInstance();
			$info = $dalPlatform->getInfoByName($name);
		} catch (Exception $e) {
			
		}
		
		return $info;
	}
	
	public static function getInfoById($id)
	{
		$info = null;
		try {
			$dalPlatform = Hapyfish2_Bms_Dal_Platform::getDefaultInstance();
			$info = $dalPlatform->getInfoById($id);
		} catch (Exception $e) {
			
		}
		
		return $info;
	}
	
	public static function updatePlatform($pid, $info)
	{
		try {
			$dalPlatform = Hapyfish2_Bms_Dal_Platform::getDefaultInstance();
			$list = $dalPlatform->update($pid, $info);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function newPlatform($info)
	{
		try {
			$dalPlatform = Hapyfish2_Bms_Dal_Platform::getDefaultInstance();
			$list = $dalPlatform->insert($info);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
}