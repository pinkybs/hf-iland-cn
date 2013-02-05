<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	//$ok = Hapyfish2_Island_Stat_Bll_NewData::getNewData();
	//echo $ok . '   ';
	$ok = Hapyfish2_Island_Stat_Bll_NewData::getNewDataDay();
	info_log(json_encode($ok), 'getDayUserArr');
} catch (Exception $e) {
	info_log($e, 'getDayArrErr');
}