<?php

class Hapyfish2_Island_Bll_Test
{
	public static function copy($platform1, $platform2, $startDay, $endDay)
	{
		$startTime = strtotime($startDay);
		$endTime = strtotime($endDay);
		$tips = 86400;
		
		$t = $startTime;
		$dal = Hapyfish2_Island_Dal_Test::getDefaultInstance();
		while($t<=$endTime) {
			try {
				$dal->copy($platform1, $platform2, $t);
			} catch (Exception $e) {
				info_log($e->getMessage(), 'test');
			}
			$t += $tips;
		}
	}
	
	public static function copyhour($platform1, $platform2, $startDay, $endDay)
	{
		$startTime = strtotime($startDay);
		$endTime = strtotime($endDay);
		$tips = 86400;
		
		$t = $startTime;
		$dal = Hapyfish2_Island_Dal_Test::getDefaultInstance();
		while($t<=$endTime) {
			try {
				$dal->copyhour($platform1, $platform2, $t);
			} catch (Exception $e) {
				info_log($e->getMessage(), 'test');
			}
			$t += $tips;
		}
	}
	
	public static function copyOneDay($platform1, $platform2, $day)
	{
		$t = strtotime($day);
		$dal = Hapyfish2_Island_Dal_Test::getDefaultInstance();
		try {
			$dal->copy($platform1, $platform2, $t);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'test');
		}
	}
	
	public static function copy2($platform1, $platform2, $startDay, $endDay)
	{
		$startTime = strtotime($startDay);
		$endTime = strtotime($endDay);
		$tips = 86400;
		
		$t = $startTime;
		$dal = Hapyfish2_Island_Dal_Test::getDefaultInstance();
		
		while($t<=$endTime) {
			try {
				$data = $dal->getoldactivelevel($platform2, $t);
				if ($data) {
					$info = array('log_time' => date('Ymd', $t));
					$level = array();
					foreach ($data as $row) {
						$level[$row['level']] = $row['count'];
					}
					$info['level'] = json_encode($level);
					$dal->insertnewactivelevel($platform1, $info);
				}
			} catch (Exception $e) {
				info_log($e->getMessage(), 'test');
			}
			$t += $tips;
		}
	}
	
	public static function copy2OneDay($platform1, $platform2, $day)
	{
		$t = strtotime($day);
		$dal = Hapyfish2_Island_Dal_Test::getDefaultInstance();
		try {
			$data = $dal->getoldactivelevel($platform2, $t);
			if ($data) {
				$info = array('log_time' => date('Ymd', $t));
				$level = array();
				foreach ($data as $row) {
					$level[$row['level']] = $row['count'];
				}
				$info['level'] = json_encode($level);
				$dal->insertnewactivelevel($platform1, $info);
			}
		} catch (Exception $e) {
			info_log($e->getMessage(), 'test');
		}
	}
	
	public static function copyhourOneDay($platform1, $platform2, $day)
	{
		$t = strtotime($day);
		$dal = Hapyfish2_Island_Dal_Test::getDefaultInstance();
		try {
			$dal->copyhour($platform1, $platform2, $t);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'test');
		}
	}
	
}