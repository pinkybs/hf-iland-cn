<?php

class Hapyfish2_Island_Stat_Bll_NewData
{
	//获取总数
    public static function getNewData()
    {
    	$allActive = 0;
        $begin = strtotime('2010-05-01 00:00:00');
        $end = strtotime('2010-09-01 00:00:00');
        
        try {
            $dal = Hapyfish2_Platform_Dal_User::getDefaultInstance();
            for ($i = 0; $i < DATABASE_NODE_NUM; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    $data = $dal->getNewUid($i, $j, $begin, $end);
                    if ($data > 0) {
                        $allActive += $data;
                    }
                }
            }
        } catch (Exception $e) {
        	info_log($e, 'getNewData');
        }
        
        return $allActive;
    }
    
    public static function getNewDataDay()
    {
		$allActive = array();
        $begin = strtotime('2010-05-01 00:00:00');
        $end = strtotime('2010-09-01 00:00:00');
        
        for ($k = $begin; $k <= $end; $k += 3600 * 24) {
        	$dayArr[] = $k;
        }
        
        try {
            $dal = Hapyfish2_Platform_Dal_User::getDefaultInstance();
            foreach ($dayArr as $key => $day) {
            	$dayOne = date('Y-m-d', $day);
            	$next = $day + 3600 * 24;
          	
	            for ($i = 0; $i < DATABASE_NODE_NUM; $i++) {
	                for ($j = 0; $j < 10; $j++) {
						$dataCo = $dal->getNewUid($i, $j, $day, $next);
						
						if ( isset($allActive[$dayOne]) ) {
							$allActive[$dayOne] += $dataCo;
						}
						else {
							$allActive[$dayOne] = $dataCo;
						}
	                }
	            }
            }
        } catch (Exception $e) {
        	info_log($e, 'getDayArrErrBll');
        }
        
        return $allActive;
    }
}