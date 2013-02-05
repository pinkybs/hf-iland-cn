<?php

class Hapyfish2_Island_Bll_MainMonth
{
	public static function addMainMonth($platform, $info)
	{
		var_dump($info);
		$time = $info['log_time'];
		$startTime = $time.'00';
		$endTime = $time.'32';
		
        $dal = Hapyfish2_Island_Dal_MainMonth::getDefaultInstance();
        $dal->setDbPrefix($platform);
        
        $monthInfo = $dal->getMonthInfo($startTime, $endTime);
        
        $monthAllTotalCount = $dal->getMonthTotalUser($startTime, $endTime);
        
		$newData = array('log_time' => $info['log_time'],
		                 'total_user' => $monthAllTotalCount,
		                 'add_user' => $monthInfo['all_add_user'],
		                 'active_user' => $info['active_user'],
		                 'pay_amount' => $monthInfo['all_pay']);
		self::add($platform, $newData);
	}
	
	public static function getMonth($platform, $month)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_MainMonth::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getMonth($info); 
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getRange($platform, $begin, $end, $sort = 'DESC')
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_MainMonth::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getRange($begin, $end, $sort);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function add($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data', 'Hapyfish2_Island_Bll_MainMonth.add');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_MainMonth::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insert($info);
		} catch (Exception $e) {
		}
	}
	
	public static function updateInfo($platform, $month, $info)
	{
		try {
			$dal = Hapyfish2_Island_Dal_MainMonth::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->update($month, $info);
		} catch (Exception $e) {
		}
	}

}