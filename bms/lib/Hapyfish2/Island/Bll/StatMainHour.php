<?php

class Hapyfish2_Island_Bll_StatMainHour
{
	public static function add($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data', 'Hapyfish2_Island_Bll_StatMainHour.add');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_StatMainHour::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$dal->insert($info);
		} catch (Exception $e) {
		}
	}
	
	public static function getDay($platform, $day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_StatMainHour::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getDay($day);
		} catch (Exception $e) {
		}
		
		return $data;
	}

}