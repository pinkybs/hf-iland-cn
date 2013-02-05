<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	
        $v = $_SERVER["argv"][1];
        $dbId = $v;
        if ( !$dbId ) {
            $dbId = 0;
        }
        
	Hapyfish2_Island_Tool_Clearcache::clearAllUserCacheByDB($dbId);
	echo "OK ";
}
catch (Exception $e) {
    err_log($e->getMessage());
}
