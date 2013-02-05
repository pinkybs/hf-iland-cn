<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$counter = 4000;
	for($i = 0; $i < $counter; $i++) {
		try {
			Hapyfish2_Island_Bll_Vote::done();
		} catch (Exception $e) {
			
		}
		//sleep(3);
	}
}
catch (Exception $e) {
	err_log($e->getMessage());
}
