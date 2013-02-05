<?php

class Hapyfish2_Island_Stat_Bll_Day
{
	public static function getMain($day)
	{
		$data = null;
		try {
			$dalMain = Hapyfish2_Island_Stat_Dal_Main::getDefaultInstance();
			$data = $dalMain->getDay($day);
		} catch (Exception $e) {
			
		}
		//$activeTwoDay = self::getActiveTwoDay($day);
		//$data['active_twoday'] = $activeTwoDay;
		
		return $data;
	}
	
	public static function getRetention($day)
	{
		$data = null;
		try {
			$dalRetention = Hapyfish2_Island_Stat_Dal_Retention::getDefaultInstance();
			$data = $dalRetention->getRetention($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getActiveUserLevel($day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Stat_Dal_ActiveUserLevel::getDefaultInstance();
			$data = $dalLevel->getDay($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getPayment($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Stat_Dal_Payment::getDefaultInstance();
			$data = $dal->getPayment($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}

    //统计每天活跃用户（连续登录2天的玩家，计算为活跃）
    public static function getActiveTwoDay($day)
    {
        $begin = strtotime($day);
        $end = $begin + 86400;
        $allActive = 0;
        
        try {
            $dal = Hapyfish2_Island_Stat_Dal_Main::getDefaultInstance();
            for ($i = 0; $i < DATABASE_NODE_NUM; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    //活跃用户数
                    $data = $dal->getActiveTwoDay($i, $j, $begin, $end);
                    if ( $data > 0 ) {
                        $allActive += $data;
                    }
                }
            }
        } catch (Exception $e) {

        }
        
        return $allActive;
    }
}