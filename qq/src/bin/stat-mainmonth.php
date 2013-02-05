<?php

define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');

try {
	    $v = $_SERVER["argv"][1];
	    $month = $v;
	    if ( !$month ) {
	        $month = '201106';
	    }
        $tempdir = '/usr/local/services/tools/stat-data/';
        info_log('/****mainmonth - '.$month.' - start*****/', 'mainmonth');
        Hapyfish2_Island_Stat_Log_Mainmonth::handle($month, $tempdir);
        info_log('/****mainmonth - end*******/', 'mainmonth');
        echo 'ok';
        exit;
}
catch (Exception $e) {
        err_log($e->getMessage());
}