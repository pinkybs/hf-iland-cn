<?php

/**
 * Event SpringFestival
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/09    zhangli
*/
class Hapyfish2_Island_Event_Cache_SpringFestival
{
	/**
	 * @获取拼图基础数据
	 * @return Array
	 */
	public static function getBasicData()
	{
		 $key = 'ev:newYears:data';
		 $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		 $data = $cache->get($key);
 
		 if ($data === false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
				$listVo = $db->getBasicData();
			} catch (Exception $e) {}

			if ($listVo) {
				foreach ($listVo as $listKey => $list) {
					$data[$listKey]['cid'] = (int)$list['cid'];
					$data[$listKey]['openTime'] = (int)$list['time'];
					$data[$listKey]['upgradeCids'] = json_decode($list['plant']);
					$data[$listKey]['giftCids'] = json_decode($list['to_send']);
					$data[$listKey]['awards'] = json_decode($list['list']);
				}
				
				$cache->set($key, $data);
			}
		 }

		foreach ($data as $dk => $dv) {
			foreach ($dv['awards'] as $akey => $avalue) {					
				$data[$dk]['awards'][$akey]['type'] = $avalue[0];
				$data[$dk]['awards'][$akey]['cid'] = $avalue[1];
				$data[$dk]['awards'][$akey]['num'] = $avalue[2];
				
				if (isset($avalue[3])) {
					$data[$dk]['awards'][$akey]['index'] = $avalue[3];
					unset($data[$dk]['awards'][$akey][3]);
				} else {
					$data[$dk]['awards'][$akey]['index'] = 0;
				}
				
				unset($data[$dk]['awards'][$akey][0]);
				unset($data[$dk]['awards'][$akey][1]);
				unset($data[$dk]['awards'][$akey][2]);	
			}
		}
		 
		 return $data;
	}
	
	/**
	 * @获取碎片数
	 * @param int $uid
	 * @param int $eid
	 * @return Array
	 */
	public static function getFragmentData($uid)
	{
		$key = 'ev:newYears:fragmentnum:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$dataVo = $cache->get($key);
		
		if ($dataVo == false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
				$listVo = $db->getFragmentData($uid);
			} catch (Exception $e) {}
			
			$list['fragmentData'] = json_decode($listVo['data_str']);
			$list['state'] = json_decode($listVo['state']);
		
			foreach ($list['fragmentData'] as $fk => $fdata) {
				foreach ($list['state'] as $lsk => $lsdata) {
					if ($fk == $lsk) {
						$dataVo[$fk]['hasGet'] = $lsdata;
						$dataVo[$fk]['puzzles'] = $fdata;
					}
				}
			}
	
			$cache->set($key, $dataVo);
		}
		
		return $dataVo;
	}
	
	/**
	 * @更新碎片和建筑领取状态
	 * @param int $uid
	 * @param Array $buildings
	 */
	public static function renewFragmentData($uid, $buildings)
	{
		$key = 'ev:newYears:fragmentnum:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $buildings);
		
		foreach ($buildings as $bkey => $building) {
			$state[] = $building['hasGet'];
			$dataArr[] = $building['puzzles'];
		}
		
		$stateStr = json_encode($state);
		$dataStr = json_encode($dataArr);
		
		try {
			$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
			$db->renewFragmentData($uid, $stateStr, $dataStr);
		} catch (Exception $e) {}
	}
	
	/**
	 * @获取水晶数量
	 * @param int $uid
	 * @return Array
	 */
	public static function getCurCrystalNum($uid)
	{
		$key = 'ev:newYears:curcrystal:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$num = $cache->get($key);
		
		if ($num == false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
				$num = $db->getCurCrystalNum($uid);
			} catch (Exception $e) {}
			
			if ($num) {
				$cache->set($key, $num);
			}
		}
		
		return $num;
	}

	/**
	 * @更新用户水晶数量
	 * @param int $uid
	 * @param int $num
	 * @return boolean
	 */
	public static function renewCurCrystalNum($uid, $num)
	{
		$ok = false;
		
		try {
			$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
			$ok = $db->renewCurCrystalNum($uid, $num);
		} catch (Exception $e) {}
		
		if ($ok) {
			$key = 'ev:newYears:curcrystal:' . $uid;
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$cache->set($key, $num);
		}
		
		return true;
	}
	
	/**
	 * @获取用户福袋数量
	 * @param int $uid
	 * @return int
	 */
	public static function getLuckyBagNum($uid)
	{
		$key = 'ev:newYears:luckybag:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$num = $cache->get($key);
		
		if ($num == false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
				$num = $db->getLuckyBagNum($uid);
			} catch (Exception $e) {}
			
			if ($num) {
				$cache->set($key, $num);
			}
		}
		
		return $num;
	}
	
	/**
	 * @更新用户福袋数量
	 * @param int $uid
	 * @param int $num
	 * @return boolean
	 */
	public static function renewLuckyBag($uid, $num)
	{
		$ok = false;
		
		try {
			$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
			$ok = $db->renewLuckyBag($uid, $num);
		} catch (Exception $e) {}
		
		if ($ok) {
			$key = 'ev:newYears:luckybag:' . $uid;
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$cache->set($key, $num);
		}
		
		return true;
	}
	
	/**
	 * @获取开福袋的概率表
	 * @return Array
	 */
	public static function getLuckyBagList()
	{
		 $key = 'ev:newYears:luckybaglist';
		 $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		 $data = $cache->get($key);
 
		 if ($data === false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
				$data = $db->getLuckyBagList();
			} catch (Exception $e) {}

			if ($data) {
				$cache->set($key, $data);
			}
		 }
		 
		 return $data;
	}
	
	/**
	 * @获取用户饺子列表
	 * @param int $uid
	 * @return Array
	 */
	public static function getDumpling($uid)
	{
		$key = 'ev:newYears:dumpling:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$list = $cache->get($key);

		if ($list === false) {
			$aryItem = self::getDumplingBasic();
		
			//get random item key
			$aryRandOdds = array();
			foreach ($aryItem as $item) {
				$itemKey = $item['item_order'];
				$aryRandOdds[$itemKey] = $item['item_odds'];
			}

			for ($i = 1; $i <= 6; $i++) {
				$gainKey = Hapyfish2_Island_Event_Bll_SpringFestival::randomKeyForOdds($aryRandOdds);
				$gainItem = $aryItem[$gainKey - 1];
				
				$list[] = array('id' => $gainItem['item_id'],
								'odds' => $gainItem['item_odds'],
								'type' => (int)$gainItem['item_type'],
		 						'cid' => (int)$gainItem['item_id'],
		 						'num' => (int)$gainItem['item_num'],
								'index' => (int)$gainItem['item_id'],
								'item_name' => $gainItem['item_name']);
			}

	        $cache->set($key, $list);
		}

		return $list;
	}
	
	/**
	 * @更新用户饺子列表
	 * @param int $uid
	 * @param Array $dumplingList
	 */
	public static function renewDumpling($uid, $dumplingList)
	{
		$key = 'ev:newYears:dumpling:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $dumplingList);
	}
	
	/**
	 * @删除饺子列表
	 * @param int $uid
	 */
	public static function delDumplingList($uid)
	{
		$key = 'ev:newYears:dumpling:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
	}
	
	/**
	 * @获取饺子信息基础列表
	 * @return Array
	 */
	public static function getDumplingBasic()
	{
		 $key = 'ev:newYears:dumpling:list';
		 $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		 $data = $cache->get($key);
 
		 if ($data === false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
				$data = $db->getDumplingBasic();
			} catch (Exception $e) {}

			if ($data) {
				$cache->set($key, $data);
			}
		 }
		 
		 return $data;
	}
	
	public static function getFirst($uid)
	{
		 $key = 'ev:newYears:first:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);
		 $first = $cache->get($key);
		 if ($first === false) {
		 	$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
            $listVo = $db->getFragmentData($uid);
            if ( !$listVo ) {
                $db->incUserData($uid);
            }
		 	
		 	$first = 1;
		 	$cache->set($key, $first);
		 }
		 
		 return $first;
	}
	
	/**
	 * @获取用户的饺子数量
	 * @param int $uid
	 * @return Array
	 */
	public static function getDumplingNum($uid)
	{
		 self::getFirst($uid);
		
		 $key = 'ev:newYears:dumpling:num:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);
		 $num = $cache->get($key);
 
		 $db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
		 if ($num === false) {
 			try {
				$num = $db->getDumplingNum($uid);
			} catch (Exception $e) {}

			if ($num) {
				$cache->set($key, $num);
			}
		 }

		 if ($num >= 20) {
		 	$num = 20;
		 	$timeLong = -1;
		 } else {
			 //获取开始时间点
			 $startTime = self::getStartTime($uid);
			 
			 $nowTime = time();
			 
			 $time = $nowTime - $startTime;
			 
			 //饺子数量
			 $dumplingNum = floor($time / 3600);
			 
			 if ($dumplingNum >= 1) {
				$num += $dumplingNum;
				 
				//上限是20盘
				if ($num >= 20) {
				 	$num = 20;
				}
				 
				//更新饺子数量
				try {
					$db->renewDumplingNum($uid, $num);
				} catch (Exception $e) {}
				 
				$cache->set($key, $num);
				 
				//倒计时
				$newStartTime = $startTime + $dumplingNum * 3600;
				$timeLong = $newStartTime + 3600 - $nowTime;
				 
				//更新开始时间
				self::renewStartTime($uid, $newStartTime);
			} else {
				$timeLong = $startTime + 3600 - $nowTime;
			 }
		 }
		 
		 return array('num' => $num, 'time' => $timeLong);
	}
	
	/**
	 * @更新饺子数量
	 * @param int $uid
	 * @param int $dumplingNum
	 */
	public static function decDumplingNum($uid, $dumplingNum)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
			$db->renewDumplingNum($uid, $dumplingNum);
		} catch (Exception $e) {}
		
		$key = 'ev:newYears:dumpling:num:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $dumplingNum);
		 
		if ($dumplingNum == 19) {
			$nowTime = time();
		 
			//更新开始时间
			self::renewStartTime($uid, $nowTime);
		}
	}
	
	/**
	 * @获取饺子倒计时的开始时间
	 * @param int $uid
	 * @return time
	 */
	public static function getStartTime($uid)
	{
		 $key = 'ev:newYears:dumpling:time:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);
		 $time = $cache->get($key);
		 
		 if ($time === false) {
		 	$time = strtotime('2012-01-15 12:00:00');
		 	$cache->set($key, $time);
		 }
		 
		 return $time;
	}
	
	/**
	 * @更新饺子开始时间
	 * @param int $uid
	 * @param int $newStartTime
	 */
	public static function renewStartTime($uid, $newStartTime)
	{
		 $key = 'ev:newYears:dumpling:time:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);
		 $cache->set($key, $newStartTime);
	}
	
}