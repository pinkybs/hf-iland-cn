<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	//Hapyfish2_Island_Bll_Test::copy('fb_thailand', 'facebook_thai', '2011-01-01', '2011-04-28');
	//Hapyfish2_Island_Bll_Test::copy2('fb_thailand', 'facebook_thai', '2011-01-01', '2011-04-28');
	
	$day = date('Y-m-d', strtotime("-1 day"));
	
	Hapyfish2_Island_Bll_Test::copyOneDay('renren', 'renren', $day);
	Hapyfish2_Island_Bll_Test::copyOneDay('taobao', 'taobao', $day);
	Hapyfish2_Island_Bll_Test::copyOneDay('fb_taiwan', 'facebook', $day);
	Hapyfish2_Island_Bll_Test::copyOneDay('fb_thailand', 'facebook_thai', $day);
	
	Hapyfish2_Island_Bll_Test::copy2OneDay('renren', 'renren', $day);
	Hapyfish2_Island_Bll_Test::copy2OneDay('taobao', 'taobao', $day);
	Hapyfish2_Island_Bll_Test::copy2OneDay('fb_taiwan', 'facebook', $day);
	Hapyfish2_Island_Bll_Test::copy2OneDay('fb_thailand', 'facebook_thai', $day);
	
	Hapyfish2_Island_Bll_Test::copyhourOneDay('renren', 'renren', $day);
	Hapyfish2_Island_Bll_Test::copyhourOneDay('taobao', 'taobao', $day);
	Hapyfish2_Island_Bll_Test::copyhourOneDay('fb_taiwan', 'facebook', $day);
	Hapyfish2_Island_Bll_Test::copyhourOneDay('fb_thailand', 'facebook_thai', $day);
}
catch (Exception $e) {
	err_log($e->getMessage());
}
