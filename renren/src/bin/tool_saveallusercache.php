<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	
        $dbId = $_SERVER["argv"][1];
        if ( !$dbId ) {
            $dbId = 0;
        }

        $tableId = $_SERVER["argv"][2];
        if ( !$tableId ) {
            $tableId = 0;
        }
        
	Hapyfish2_Island_Tool_Savecache::saveAllUserCacheByDB($dbId, $tableId);
	echo "OK ";
}
catch (Exception $e) {
    err_log($e->getMessage());
}
