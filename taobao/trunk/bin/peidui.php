<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	//Hapyfish2_Island_Event_Bll_Peidui::Peidui(113932,1315908000,114632);
//	$cids = array(119832, 119932, 120032);
//	foreach ($cids as $cid) {
//		$ok = Hapyfish2_Island_Event_Bll_Peidui::getPlantNum($cid);
//		echo $cid . ' : ' . $ok . '  ';
//	}
//	echo "OK ";
	$data = Hapyfish2_Island_Event_Bll_Peidui::Peidui(1, 2, 3);
	info_log(json_encode($data), 'peidduistar');
}
catch (Exception $e) {
	err_log($e->getMessage());
}