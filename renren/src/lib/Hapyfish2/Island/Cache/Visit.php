<?php

class Hapyfish2_Island_Cache_Visit
{
    public static function dailyVisit($uid, $fid)
    {
    	$nowTime = time();
    	
    	//1315929600->2011-09-14 00:00:00
    	if ( $nowTime < 1315929600 ) {
			$today = date('Ymd');
			
			$key = 'i:u:dlyvisit:' . $uid . ':' . $fid . ':' . $today;
			
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        if ($cache->add($key, 1, 86400)) {
	        	Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_6', 1);
	        	Hapyfish2_Island_HFC_AchievementDaily::updateUserAchievementDailyByField($uid, 'num_6', 1);
	        
		        try {
					//task id 3027,task type 6
					Hapyfish2_Island_Bll_Task::checkTask($uid, 3027);
		        } catch (Exception $e) {
		        }
	        }
        }
        else {
	        $today = date('Ymd');
	        
	        $key = 'i:u:dlyvisit:' . $uid . ':' . $fid;
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        $data = $cache->get($key);
	        
	        if ( $data == $today ) {
	        	return;
	        }
	        
	        Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_6', 1);
	        Hapyfish2_Island_HFC_AchievementDaily::updateUserAchievementDailyByField($uid, 'num_6', 1);
	        
	        $cache->set($key, $today);
	        
	        try {
	            //task id 3027,task type 6
	            Hapyfish2_Island_Bll_Task::checkTask($uid, 3027);
	        } catch (Exception $e) {
	        }
        }
    }
    
}