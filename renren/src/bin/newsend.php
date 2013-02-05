<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	
	$uid = 332843;
	
	$oks = Hapyfish2_Island_Event_Bll_UpgradeGift::getData($uid);

	echo $oks . ' |' . 'OK';
}
catch (Exception $e) {
	info_log($e->getMessage(), 'tmpErr');
	//err_log($e->getMessage());
}