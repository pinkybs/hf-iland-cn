<?php

define('ROOT_DIR', realpath('../'));
include_once ROOT_DIR . '/bin/config.php';

try {
	$dbId = 16;
	info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
	info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
	
    $dbId = 17;
    info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
    info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
    
    $dbId = 18;
    info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
    info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
    
    $dbId = 19;
    info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);
    Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser($dbId);
    info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
	exit;
}
catch (Exception $e) {
	err_log($e->getMessage());
}