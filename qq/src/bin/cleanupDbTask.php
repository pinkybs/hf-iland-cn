<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {

    //sh /home/admin/website/magic/bin/cleanupDbTask.cron /home/admin/website/magic/bin /usr/local/php-cgi/bin/php 1>/dev/null 2>&1 &
    //sh :execfilepath :execpath :phppath :streamout
	$tmLine = strtotime("-3 day");
    info_log('ExpireDate:'.date('Ymd H:i:s', $tmLine), Hapyfish2_Island_Tool_CleanupDb::$logFile);
    echo Hapyfish2_Island_Tool_CleanupDb::cleanQpointBuy($tmLine);

}
catch (Exception $e) {
	err_log($e->getMessage());
}