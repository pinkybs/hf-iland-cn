<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');
                $dir = '/home/admin/data/stat-data/';
                $prefix1 = '609';
                $prefix2 = '610';
                $prefix3 = '608';
                $prefix4 = '607';
                $prefix5 = '606';
                $prefix6 = '611';
                $prefix7 = '612';
                $prefix8 = '613';
                $dtYesterday = strtotime("-1 day");
                $dt = date('Ymd',$dtYesterday);
                $file1 = $dir.$prefix1.'/'.$dt.'/all-'.$prefix1.'-'.$dt.'.log';
                $file2 = $dir.$prefix2.'/'.$dt.'/all-'.$prefix2.'-'.$dt.'.log';
                $file3 = $dir.$prefix3.'/'.$dt.'/all-'.$prefix3.'-'.$dt.'.log';
                $file4 = $dir.$prefix4.'/'.$dt.'/all-'.$prefix4.'-'.$dt.'.log';
                $file5 = $dir.$prefix5.'/'.$dt.'/all-'.$prefix5.'-'.$dt.'.log';
                $file6 = $dir.$prefix6.'/'.$dt.'/all-'.$prefix6.'-'.$dt.'.log';
                $file7 = $dir.$prefix7.'/'.$dt.'/all-'.$prefix7.'-'.$dt.'.log';
                $file8 = $dir.$prefix8.'/'.$dt.'/all-'.$prefix8.'-'.$dt.'.log';
try {
        Hapyfish2_Island_Stat_Log_Catchfish::handle($dt, $file1);
        Hapyfish2_Island_Stat_Log_Catchfish::handleCoin($dt, $file2);
        Hapyfish2_Island_Stat_Log_Catchfish::handleIsland($dt, $file3);
        Hapyfish2_Island_Stat_Log_Catchfish::handleCannon($dt, $file4);
        Hapyfish2_Island_Stat_Log_Catchfish::handleCard($dt, $file5);
		Hapyfish2_Island_Stat_Log_Catchfish::brushCard($dt, $file6);
		Hapyfish2_Island_Stat_Log_Catchfish::handleBrush($dt, $file7);
		Hapyfish2_Island_Stat_Log_Catchfish::handleBrushIsland($dt, $file8);
    
    
        echo "OK ";
}
catch (Exception $e) {
        err_log($e->getMessage());
        info_log($e, 'BUYU-Err');
}