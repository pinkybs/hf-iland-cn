<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$counter = 8000;
	for($i = 0; $i < $counter; $i++) {
		try {
			Hapyfish2_Island_Bll_Vote::doview();
		} catch (Exception $e) {
			
		}
	}
}
catch (Exception $e) {
	err_log($e->getMessage());
}
