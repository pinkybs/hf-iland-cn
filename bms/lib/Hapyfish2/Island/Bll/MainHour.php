<?php

class Hapyfish2_Island_Bll_MainHour
{
	public static function add($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data', 'Hapyfish2_Island_Bll_MainHour.add');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_MainHour::getDefaultInstance();
			$dal->setDbPrefix($platform);
			foreach ($info as $row) {
				$dal->insert($row);
			}
		} catch (Exception $e) {
		}
	}
	
	public static function getDay($platform, $day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_MainHour::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getDay($day);
		} catch (Exception $e) {
		}
		
		return $data;
	}

}