<?php

/**
 * Event OneGoldShop
 *
 * @package    Island/Event/Cache
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/10/18    zhangli
*/
class Hapyfish2_Island_Event_Cache_OneGoldShop
{
	const LIFE_TIME_ONE_WEEK = 604800;
	
	/**
	 * @获取所有信息
	 * @return Array
	 */
	public static function getAllData()
	{
		$key = 'i:e:onegold:all';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$data = $cache->get($key);
		
		if ($data === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
				$data = $db->getAllOneGoldGift();
			} catch (Exception $e) {}
			
			if ($data) {
				$cache->set($key, $data, self::LIFE_TIME_ONE_WEEK);
			}
		}
		
		return $data;
	}
	
	/**
	 * @获取当前出售物品
	 * @param time $nowTime
	 * @return Array
	 */
	public static function getSaleData()
	{
		$key = 'i:e:oneshop:gift';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$data = $cache->get($key);
		
		if ($data === false) {
			$allData = self::getAllData();
			
			if ($allData) {
				$nowTime = time();
				foreach ($allData as $dataGift) {
					if (($dataGift['start_time'] <= $nowTime) && $nowTime < $dataGift['end_time']) {
						$data = $dataGift;
						$cache->set($key, $data, $data['end_time']);
						break;
					}
				}
			} else {
				$data = array();
			}
		}
		
		return $data;
	}

	/**
	 * @获取本期物品过期时间
	 * @return time
	 */
	public static function getFalseTime()
	{
		$key = 'i:e:oneshop:falseTime';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$falseTime = $cache->get($key);
		
		if ($falseTime === false) {
			$data = self::getSaleData();
			$falseTime = $data['end_time'];
			
			$cache->set($key, $falseTime, $falseTime);
		}
		
		return $falseTime;
	}
	
	/**
	 * @获取当前物品剩余数量
	 *@return int
	 */
	public static function getHasNum()
	{
		$key = 'i:e:oneshop:gift:hasnum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$hasNum = $cache->get($key);
		
		if ($hasNum === false) {
			$data = self::getSaleData();
			
			$hasNum = $data['num'];
			
			$cache->set($key, $hasNum, $data['end_time']);
		}
		
		return $hasNum;
	}
	
	/**
	 * @更新物品剩余数量
	 * @param int $hasNum
	 */
	public static function decHasNum($hasNum)
	{
		$falseTime = self::getFalseTime();
		
		$key = 'i:e:oneshop:gift:hasnum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->set($key, $hasNum, $falseTime);
	}
	
	/**
	 * @获取用户领取状态
	 * @param int $uid
	 * @return int
	 */
	public static function getBuyStatus($uid)
	{
		$key = 'i:u:oneshop:gift:get_status:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		if ($data === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
				$data = $db->getBuyStatus($uid);
				//$data = $db->getOneGoldHasGet($uid);
			} catch (Exception $e) {}
			
			$falseTime = self::getFalseTime();
			
			$cache->set($key, $data, $falseTime);
		}
		
		return $data;
	}
	
	/**
	 * @设置用户领取状态
	 */
	public static function incBuyStatus($uid, $step, $leftTime = 0)
	{
		if ($leftTime == 0) {
			$leftTime = self::LIFE_TIME_ONE_WEEK;
		}
		
		try {
			$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
			$db->refurbishHasGet($uid);
		} catch (Exception $e) {}
		
		$key = 'i:u:oneshop:gift:get_status:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->set($key, $step, $leftTime);
	}
	
	/**
	 * @获取用户参加次数
	 * @param int $uid
	 * @return int
	 */
	public static function getBuyNum($uid)
	{
		$key = 'i:e:oneshop:buynum:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$buyNum = $cache->get($key);
		
		if ($buyNum === false) {
			$buyNum = 0;
			try {
				$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
				$buyNum = $db->getBuyNum($uid);
			} catch (Exception $e) {}
			
			$cache->set($key, $buyNum, self::LIFE_TIME_ONE_WEEK);
		}
		
		return $buyNum;
	}
	
	/**
	 * @增加用户参加活动次数
	 * @param int $uid
	 */
	public static function addBuyNum($uid)
	{
		$hasBuyNum = self::getBuyNum($uid);
		
		$hasBuyNum++;
		
		$key = 'i:e:oneshop:buynum:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $hasBuyNum, self::LIFE_TIME_ONE_WEEK);
	}
	
	/**
	 * @设置用户领取状态
	 * @param int $uid
	 * @return int
	 */
	public static function setBuyStatus($uid, $step)
	{
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		try {
			$act = $db->getAct($uid);
		} catch (Exception $e) {}		
		
		try {
			if ($act == false) {
				$db->incBuyStatus($uid);
			} else {
				$db->repBuyStatus($uid, $step);
			}
		} catch (Exception $e) {}
	}
	
	/**
	 * @查询用户领取到哪一期礼包了
	 * @param int $uid
	 * @reutn int
	 */
	public static function getBoxStep($uid)
	{
		$key = 'i:e:oneshop:box:qishu:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$step = $cache->get($key);
		
		if ($step === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
				$step = $db->hasCountBox($uid);
			} catch (Exception $e) {}
			
			$cache->set($key, $step, self::LIFE_TIME_ONE_WEEK);
		}
		
		return $step;
	}
	
	public static function getBoxHas($uid, $step)
	{
		$key = 'i:e:oneshop:box:has:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		if ($key === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
				$dataVo = $db->getOneGoldBox($uid);
			} catch (Exception $e) {}
			
			$boxData = self::getBoxData($uid, $step);
			
			$msy = array();
			foreach ($boxData as $keyData => $valData) {
				$msy[$keyData] = 0;
			}
			
			if ($dataVo) {
				$hg = explode(',', $dataVo);
				foreach ($hg as $stas) {
					$sky[] = explode('*', $stas);
				}

				foreach ($sky as $sky_value) {
					$vak = 0;
					$vav = 0;
					$vak = $sky_value[0];
					$vav = $sky_value[1];
					$msy[$vak] = $vav;
				}

				$cache->set($key, $msy);
			} else {
				$cache->set($key, $msy);
			}
		}
	}
	
	/**
	 * @获取用户的礼物盒子信息
	 * @param int $uid
	 * @param int $step
	 * @return Array
	 */
	public static  function getBoxData($uid, $step)
	{
		$key = 'i:e:oneshop:gift:bigbox:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$boxData = $cache->get($key);
		
		if ($boxData === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
				$dataVo = $db->getBoxInfo($step);
			} catch (Exception $e) {}
			
			if ($dataVo === false) {
				$dataVo = array();
			} else {
				$botDataNew = array();

				foreach ($dataVo as $data) {
					$boxId = $data['idx'];

					$boxData[$boxId]['gold'] = 0;
					if ($data['gold'] > 0) {
						$botDataNew[$boxId]['gold'] = (int)$data['gold'];
					}

					$boxData[$boxId]['coin'] = 0;
					if ($data['coin'] > 0) {
						$botDataNew[$boxId]['coin'] = (int)$data['coin'];
					}

					$boxData[$boxId]['starfish'] = 0;
					if ($data['starfish'] > 0) {
						$botDataNew[$boxId]['starfish'] = (int)$data['starfish'];
					}

					$boxData[$boxId]['cid'] = array();
					if ($data['data']) {
						$msgCid = explode(',', $data['data']);

						foreach ($msgCid as $vaCid) {
							$toCid = explode('*', $vaCid);

							$botDataNew[$boxId]['cid'][] = $toCid[0] . '*' . $toCid[1];
						}
					}
				}
				
				$boxData = $botDataNew;
				
				$cache->set($key, $boxData, self::LIFE_TIME_ONE_WEEK);
			}
		}
		
		return $boxData;
	}
	
}