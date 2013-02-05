<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {

        $v = $_SERVER["argv"][1];
        $platform = $v;
        if ( !$platform ) {
            $platform = 'nk_poland';
        }
        
	Hapyfish2_Island_Bll_Bot::crawlByPlatform($platform);
}
catch (Exception $e) {
	err_log($e->getMessage());
}
