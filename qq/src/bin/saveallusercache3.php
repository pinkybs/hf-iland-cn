<?php

define('ROOT_DIR', realpath('../'));
include_once ROOT_DIR . '/bin/config.php';

try {
	$dbId = 12;
	info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
	info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
	
    $dbId = 13;
    info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
    info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
    
    $dbId = 14;
    info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
    info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
    
    $dbId = 15;
    info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
    info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
	exit;
}
catch (Exception $e) {
	err_log($e->getMessage());
}