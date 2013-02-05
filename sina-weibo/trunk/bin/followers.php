<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	echo Hapyfish2_Island_Tool_Fans::fetchOfficialFan();
}
catch (Exception $e) {
	err_log($e->getMessage());
}