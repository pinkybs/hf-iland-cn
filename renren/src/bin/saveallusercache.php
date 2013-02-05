<?php

define('ROOT_DIR', realpath('../'));
include_once ROOT_DIR . '/bin/config.php';

try {
	$dbId = 0;
	info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser');
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser();
	info_log('/****savedbAllUser - end*******/', 'savedbAllUser');
	exit;
}
catch (Exception $e) {
	err_log($e->getMessage());
}
