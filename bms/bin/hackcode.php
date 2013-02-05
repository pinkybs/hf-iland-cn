<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$file = 'http://zt.subaonet.com/2011/pgy/imgchk/validatecode.asp';
	$code = new Hapyfish2_Bms_Bll_ValidateCode();
	$code->setImage($file);
	$code->getHec();
	$code->Draw();
	//$code->run();
}
catch (Exception $e) {
	err_log($e->getMessage());
}
