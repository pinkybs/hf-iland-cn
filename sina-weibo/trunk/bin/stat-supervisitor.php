<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config-stat.php');
$v = $_SERVER["argv"][1];
try {
    $dayAgo = $v;
    if ( !$dayAgo ) {
        $dayAgo = 1;
    }
    $time = strtotime("-$dayAgo day");
    $day = date("Ymd", $time);
    $day0 = date('Y-m-d', $time);
    $time0 = strtotime($day0) - 3600;
    $file1 = "/data/weibo/stat-data/503/$day/all-503-$day.log";
    $result1 = Hapyfish2_Island_Stat_Bll_SuperVisitor::saveSvInfoToDb($day, $time0, $file1);
    
    $file2 = "/data/weibo/stat-data/505/$day/all-505-$day.log";
    $result2 = Hapyfish2_Island_Stat_Bll_SuperVisitor::saveCollectionInfoToDb($day, $time0, $file2);
    
    $data = json_encode($result2);
    echo $data;
}
catch (Exception $e) {
    err_log($e->getMessage());
}
