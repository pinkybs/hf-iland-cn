<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');
                $dir = '/data/stat/island/weibo/';
                $prefix1 = '601';
                $prefix2 = '602';
                $prefix3 = '603';
                $prefix4 = '604';
                $dtYesterday = strtotime("-1 day");
                $dt = date('Ymd',$dtYesterday);
                $file1 = $dir.$prefix1.'/'.$dt.'/all-'.$prefix1.'-'.$dt.'.log';
                $file2 = $dir.$prefix2.'/'.$dt.'/all-'.$prefix2.'-'.$dt.'.log';
                $file3 = $dir.$prefix3.'/'.$dt.'/all-'.$prefix3.'-'.$dt.'.log';
                $file4 = $dir.$prefix4.'/'.$dt.'/all-'.$prefix4.'-'.$dt.'.log';
try {
        Hapyfish2_Island_Stat_Bll_Catchfish::handle($dt, $file1);
        Hapyfish2_Island_Stat_Bll_Catchfish::handleProduct($dt, $file2);
        Hapyfish2_Island_Stat_Bll_Catchfish::handleUserNum($dt, $file3);
        Hapyfish2_Island_Stat_Bll_Catchfish::handleCoinAndCard($dt, $file4);
    
    
        echo "OK ";
}
catch (Exception $e) {
        err_log($e->getMessage());
}