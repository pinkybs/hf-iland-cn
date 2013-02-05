<?php

class Hapyfish2_Island_Bll_Monitor
{
	public static function getServerList($platform)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Monitor::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getServerList(); 
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getServerById($platform, $sid)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Monitor::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getServerById($sid); 
		} catch (Exception $e) {
		}
		
		return $data;
	}

}