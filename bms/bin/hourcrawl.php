<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	Hapyfish2_Island_Bll_Bot::crawlHour();
}
catch (Exception $e) {
	err_log($e->getMessage());
}
