<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$dtYesterday = strtotime("-1 day");
	$logDate = date('Ymd', $dtYesterday);
    //$logDate = '20110716';
    $dir = '/home/admin/data/stat-data/cLoadTm/';///home/admin/logs/weibo/stat-data/cLoadTm/

	$rst = Hapyfish2_Island_Stat_Bll_DaycLoadTm::calcDayData($logDate, $dir);
	echo $logDate."-cLoadTm-" . ($rst ? 'OK' : 'NG');
	/*
	 *     $start = 1310659200;
    $end = 1312387200;
    while ($start <= $end) {
        $logDate = date('Ymd', $start);
        Hapyfish2_Island_Stat_Bll_DaycLoadTm::calcDayData($logDate, $dir);
        $start = $start + 60*60*24;
    }*/
}
catch (Exception $e) {
	err_log($e->getMessage());
}