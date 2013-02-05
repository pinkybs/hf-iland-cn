<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');
                $dir = '/data/stat/island/weibo/';
                $prefix1 = '801';
                $dtYesterday = strtotime("-1 day");
                $dt = date('Ymd',$dtYesterday);
                $file1 = $dir.$prefix1.'/'.$dt.'/all-'.$prefix1.'-'.$dt.'.log';
try {
        Hapyfish2_Island_Stat_Bll_Goldlog::handle($dt, $file1);  
    
        echo "OK ";
}
catch (Exception $e) {
        err_log($e->getMessage());
}