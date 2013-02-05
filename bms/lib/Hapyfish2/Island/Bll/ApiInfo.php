<?php

class Hapyfish2_Island_Bll_ApiInfo
{
	public static function getInfo($name)
	{
		$info = null;
		try {
			$dalApiInfo = Hapyfish2_Island_Dal_ApiInfo::getDefaultInstance();
			$info = $dalApiInfo->getInfo($name);
		} catch (Exception $e) {

		}
		
		return $info;
	}
	
	public static function getStatPlatform()
	{
		$list = array();
		try {
			$dalApiInfo = Hapyfish2_Island_Dal_ApiInfo::getDefaultInstance();
			$info = $dalApiInfo->getStatPlatform();
			if ($info) {
				foreach ($info as $p) {
					$list[$p['name']] = $p['stat'];
				}
			}
		} catch (Exception $e) {

		}
		
		return $list;
	}
}