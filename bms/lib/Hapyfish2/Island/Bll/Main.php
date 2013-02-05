<?php

class Hapyfish2_Island_Bll_Main
{
	public static function add($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data', 'Hapyfish2_Island_Bll_Main.add');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_Main::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insert($info); 
		} catch (Exception $e) {
		}
	}
	
	public static function updateMemo($platform, $day, $memo)
	{
		try {
			$dal = Hapyfish2_Island_Dal_Main::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->update($day, array('memo' => $memo));
		} catch (Exception $e) {
		}
	}
	
	public static function updateInfo($platform, $day, $info)
	{
		if (empty($platform) || empty($info)) {
			info_log('no data', 'Hapyfish2_Island_Bll_Main.updateInfo');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_Main::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->update($day, $info);
		} catch (Exception $e) {
		}
	}

}