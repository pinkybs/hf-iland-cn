<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	//修复奇迹飞机场没有气球
	$tot = Hapyfish2_Island_Event_Bll_Peidui::repaireAirRemain();
	
	echo "OK : " . $tot;
}
catch (Exception $e) {
	err_log($e->getMessage());
}