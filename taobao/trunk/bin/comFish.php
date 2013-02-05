<?php
define('ROOT_DIR', realpath('../'));
include_once ROOT_DIR . '/bin/config.php';


	//Hapyfish2_Island_Event_Bll_Peidui::deleteOnePlant();
try {
	$dtYesterday = strtotime("-1 day");
	$dt = date('Ymd', $dtYesterday);
	Hapyfish2_Island_Stat_Log_Catchfish::handleMatchFIsh();
	Hapyfish2_Island_Stat_Log_Catchfish::handlePve();
	Hapyfish2_Island_Stat_Log_Catchfish::handleSkill();
	Hapyfish2_Island_Stat_Log_Catchfish::handlePvp($dt);
	Hapyfish2_Island_Stat_Log_Catchfish::handleShengwang($dt);
	Hapyfish2_Island_Stat_Log_Catchfish::handleziZhi($dt);
}catch(Exception $e){
	err_log($e->getMessage());
}
