<?php

/**
 * Event LanternFestival
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/29    zhangli
*/
class Hapyfish2_Island_Event_Cache_LanternFestival
{
	const LIFE_TIME_ONE_MONTH = 2592000;
	
	/**
	 * @判断是否是第一次请求
	 * @param int $uid
	 */
	public static function checkFirst($uid)
	{
		$key = 'lantern:lf:user:first:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		if ($data === false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_LanternFestival::getDefaultInstance();
				$dataStr = $db->getUserData($uid);
			} catch (Exception $e) {
				info_log($e, 'LF-Err-29');
			}
			
			try {
				if (!$dataStr) {
					$db->incUserData($uid);
				}
			} catch (Exception $e) {
				info_log($e, 'LF-Err-37');
			}
			
			$cache->set($key, 1, self::LIFE_TIME_ONE_MONTH);
		}
	}
	
	/**
	 * @用户数据
	 * @param int $uid
	 * @return array
	 */
	public static function getUserData($uid)
	{
		//判断是否是第一次请求
		self::checkFirst($uid);
		
		$key = 'lantern:lf:userdata:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		$ok = false;
		if ($data === false) {
 			try {
 				$db = Hapyfish2_Island_Event_Dal_LanternFestival::getDefaultInstance();
				$dataStr = $db->getUserData($uid);
			} catch (Exception $e) {
				info_log($e, 'LF-Err-64');
			}
			
			if (!$dataStr) {
	 			try {
					$db->incUserData($uid);
				} catch (Exception $e) {
					info_log($e, 'LF-Err-71');
				}
			}
			
			$dataArr = json_decode($dataStr);
			
			foreach ($dataArr as $dakey => $davalues) {
				$data[$dakey]['item_id'] = $davalues[0];
				$data[$dakey]['food'] = $davalues[1];
				$data[$dakey]['praise'] = $davalues[2];
				$data[$dakey]['needMedalStar'] = $davalues[3];
				$data[$dakey]['getted'] = $davalues[4];
			}
			
			$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);
		}
		
		foreach ($data as $usekey => $useValues) {
			if (!isset($useValues['item_id'])) {
				$data[$usekey]['item_id'] = $useValues[0];
				unset($data[$usekey][0]);
			}
			
			if (!isset($useValues['food'])) {
				$data[$usekey]['food'] = $useValues[1];
				unset($data[$usekey][1]);
			}
			
			if (!isset($useValues['praise'])) {
				$data[$usekey]['praise'] = $useValues[2];
				unset($data[$usekey][2]);
			}
			
			if (!isset($useValues['needMedalStar'])) {
				$data[$usekey]['needMedalStar'] = $useValues[3];
				unset($data[$usekey][3]);
			}
			
			if (!isset($useValues['getted'])) {
				$data[$usekey]['getted'] = $useValues[4];
				unset($data[$usekey][4]);
			}
		}
	
		foreach ($data as $upKey => $upData) {	
			if ($upData['getted'] == 1) {
				if ($upData['food'] < 5) {
					 //获取开始时间点
					 $startTime = self::getStartTime($uid, $upData['item_id']);

					 $nowTime = time();
			 
					 $time = $nowTime - $startTime;

					 //数量
					 $foodNum = floor($time / 3600);
	 
					 if ($foodNum >= 1) {
					 	$allNum = $upData['food'] + $foodNum;
					 	
					 	if ($allNum > 5) {
					 		$allNum = 5;
					 	}
					 	
					 	$data[$upKey]['food'] = $allNum;
					 	$ok = true;
					 	
	 					//更新开始时间
 						$newStartTime = $startTime + $foodNum * 3600;
						self::renewStartTime($uid, $newStartTime, $upData['item_id']);
					 }
				}
			}
		}
			
		if ($ok == true) {
			foreach ($data as $dataKey => $dataVlues) {
				$dataNewArr[$dataKey][] = $dataVlues['item_id'];
				$dataNewArr[$dataKey][] = $dataVlues['food'];
				$dataNewArr[$dataKey][] = $dataVlues['praise'];
				$dataNewArr[$dataKey][] = $dataVlues['needMedalStar'];
				$dataNewArr[$dataKey][] = $dataVlues['getted'];
			}
			
 			try {
 				$dataStrNew = json_encode($dataNewArr);
 				
 				$db = Hapyfish2_Island_Event_Dal_LanternFestival::getDefaultInstance();
				$db->renewUserData($uid, $dataStrNew);
			} catch (Exception $e) {
				info_log($e, 'LF-Err-167');
			}
			
			$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);
		}
		
		return $data;
	}
	
	/**
	 * @获取倒计时的开始时间
	 * @param int $uid
	 * @return time
	 */
	public static function getStartTime($uid, $itemId)
	{
		 $key = 'lantern:lf:food:' . $itemId . 'time:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid); 
		 $time = $cache->get($key);
		 
		 if ($time === false) {
		 	$time = time();
		 	$cache->set($key, $time, self::LIFE_TIME_ONE_MONTH);
		 }
		 
		 return $time;
	}
	
	/**
	 * @更新开始时间
	 * @param int $uid
	 * @param int $newStartTime
	 */
	public static function renewStartTime($uid, $newStartTime, $itemId)
	{
		 $key = 'lantern:lf:food:' . $itemId . 'time:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);
		 $cache->set($key, $newStartTime, self::LIFE_TIME_ONE_MONTH);
	}
	
	/**
	 * @更新数据
	 * @param int $uid
	 * @param array $data
	 */
	public static function renewUserData($uid, $dataVo)
	{		
		foreach ($dataVo as $dataKey => $dataValues) {
			$dataVo[$dataKey][] = $dataValues['item_id'];
			$dataVo[$dataKey][] = $dataValues['food'];
			$dataVo[$dataKey][] = $dataValues['praise'];
			$dataVo[$dataKey][] = $dataValues['needMedalStar'];
			$dataVo[$dataKey][] = $dataValues['getted'];
			
			unset($dataVo[$dataKey]['item_id']);
			unset($dataVo[$dataKey]['food']);
			unset($dataVo[$dataKey]['praise']);
			unset($dataVo[$dataKey]['needMedalStar']);
			unset($dataVo[$dataKey]['getted']);
			
			if (isset($dataValues['cid'])) {
				unset($dataVo[$dataKey]['cid']);
			}
			
			if (isset($dataValues['star'])) {
				unset($dataVo[$dataKey]['star']);
			}
		}
				
		$dataStr = json_encode($dataVo);
		
		try {
			$db = Hapyfish2_Island_Event_Dal_LanternFestival::getDefaultInstance();
			$db->renewUserData($uid, $dataStr);
		} catch (Exception $e) {
			info_log($e, 'LF-Err-242');
		}
		
		$key = 'lantern:lf:userdata:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $dataVo, self::LIFE_TIME_ONE_MONTH);
		
		//self::getUserData($uid);
	}
	
	/**
	 * @可以烹饪次数
	 * @param int $uid
	 * @return int
	 */
	public static function getCookTimes($uid)
	{
		//判断是否是第一次请求
		self::checkFirst($uid);
		
		$key = 'lantern:lf:cooktimes:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$num = $cache->get($key);
		
		if ($data == false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_LanternFestival::getDefaultInstance();
				$num = $db->getCookTimes($uid);
			} catch (Exception $e) {
				info_log($e, 'LF-Err-271');
			}
			
			if ($num) {
				$cache->set($key, $num, self::LIFE_TIME_ONE_MONTH);
			}
		}
		
		return $num;
	}
	
	/**
	 * @更新烹饪次数
	 * @param int $uid
	 * @param int $num
	 */
	public static function renewCookTimes($uid, $num)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_LanternFestival::getDefaultInstance();
			$db->renewCookTimes($uid, $num);
		} catch (Exception $e) {
			info_log($e, 'LF-Err-293');
		}
		
		$key = 'lantern:lf:cooktimes:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $num, self::LIFE_TIME_ONE_MONTH);
	}
	
}