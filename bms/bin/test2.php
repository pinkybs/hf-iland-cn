<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	Hapyfish2_Island_Bll_Test::copyhourOneDay('fb_thailand', 'facebook_thai', '2011-04-28');
}
catch (Exception $e) {
	err_log($e->getMessage());
}
