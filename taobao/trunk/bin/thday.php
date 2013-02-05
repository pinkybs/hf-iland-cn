<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$ok = Hapyfish2_Island_Event_Bll_ThanksDay::sendPlant();
	echo $ok;
}
catch (Exception $e) {
	info_log($e->getMessage(), 'thdayErr');
}