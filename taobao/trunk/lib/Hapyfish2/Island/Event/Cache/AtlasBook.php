<?php

/**
 * Event AtlasBook
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2012/01/03    zhangli
*/
class Hapyfish2_Island_Event_Cache_AtlasBook
{
	/**
	 * @获取已经开放的图鉴
	 * @return Array
	 */
	public static function getData()
	{
		$key = 'ev:atlasbook';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');	
		$data = $cache->get($key);
		
		if ($data === false) {
			$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
			$dataStr = $db->getData();
			
			if ($dataStr) {
				foreach ($dataStr as $dsk => $dsv) {
					$data[$dsk]['id'] = $dsv['id'];
					$data[$dsk]['name'] = $dsv['name'];
					$data[$dsk]['items'] = json_decode($dsv['items']);
				}
			}
			
			$cache->set($key, $data);
		}
		
		return $data;
	}
	
	/**
	 * @获取用户拥有的图鉴
	 * @param int $uid
	 * @param int $data
	 * @return Array
	 */
	public static function getUserData($uid, $data)
	{
		$key = 'i:u:atlasbook:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$dataCo = $cache->get($key);

		$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
		if ($dataCo === false) {
			try {
				$listStr = $db->getUserData($uid);
			} catch (Exception $e) {}

			if ($listStr) {
				$dataCo = json_decode($listStr);
				$cache->set($key, $dataCo);
			}
		}

		//判断用户拥有的图鉴中是否包含所有的图鉴
		if ((count($data) > count($dataCo)) || !$dataCo || (count($dataCo) == 0)) {	
			if (!$dataCo || (count($dataCo) == 0)) {			
				foreach ($data as $dk => $dv) {
					$dataCo[] = array($dk + 1, 0);
				}
				
				$dataStr = json_encode($dataCo);
				try {
					$db->incUserData($uid, $dataStr);
				} catch (Exception $e) {}
			} else {
				foreach ($data as $dk => $dv) {
					if (!$dataCo[$dk] && !isset($dataCo[$dk])) {
						$dataCo[$dk] = array($dk + 1, 0);
					}
				}
				
				$dataStr = json_encode($dataCo);
				try {
					$db->renewUserData($uid, $dataStr);
				} catch (Exception $e) {}
			}
			
			$cache->set($key, $dataCo);
		}
		
		return $dataCo;
	}
	
	/**
	 * @更新用户信息
	 * @param int $uid
	 * @param Array $userData
	 */
	public static function renewUserData($uid, $userData)
	{
		try {
			$dataStr = json_encode($userData);
			
			$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
			$db->renewUserData($uid, $dataStr);
		} catch (Exception $e) {}
		
		$key = 'i:u:atlasbook:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userData);
	}
	
	/**
	 * @计算勋章的信息
	 * @param int $uid
	 * @param Array $data
	 * @param Array $userData
	 * @param Array $plantList
	 * @return Array
	 */
	public static function getMedalList($uid, $dataVo, $userData)
	{
		//基础建筑列表
		$plants = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		$cids = self::getBasicInfo($uid);
		foreach ($dataVo as $keys => $data) {
			foreach ($data['items'] as $itemKey => $item) {
				$medalList[$keys]['cid'] = $data['id'];
				$medalList[$keys]['name'] = $data['name'];
		
				foreach ($userData as $udk => $udv) {
					if ($udv[0] == $data['id']) {
						$medalList[$keys]['level'] = $udv[1];	
					}
				}
	
				$medalList[$keys]['totalStar'] = count($data['items']) * 5;
				$medalList[$keys]['currentStar'] = 0;
				$medalList[$keys]['collectedCount'] = self::getNum($data['id']);
				
				if ($medalList[$keys]['level'] == 3) {
					$medalList[$keys]['nextLevelStar'] = 0;
					$medalList[$keys]['buildingList'][$itemKey]['cid'] = self::getCid($uid, $item[0], $plants, array());
					$medalList[$keys]['buildingList'][$itemKey]['star'] = 5;
				} else {
					$medalList[$keys]['nextLevelStar'] = $medalList[$keys]['totalStar'] - $medalList[$keys]['currentStar'];
					$medalList[$keys]['buildingList'][$itemKey]['cid'] = self::getCid($uid, $item[0], $plants, $cids);
					$medalList[$keys]['buildingList'][$itemKey]['star'] = self::getItemLevel($uid, $item[0], $cids);
				}

				$medalList[$keys]['buildingList'][$itemKey]['name'] = self::getName($item[0], $plants);
			}
		}
		
		foreach ($medalList as $key => $list) {
			foreach ($list['buildingList'] as $plant) {
				$medalList[$key]['currentStar'] += $plant['star'];
			}
		}
		return $medalList;
	}
	
	/**
	 * @更新勋章获得人数
	 * @param int $cid
	 */
	public static function renewNum($cid, $level)
	{
		$key = 'ev:atlasbook:num';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$dataCo = $cache->get($key);

		$data = self::getData();
		$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
		
		if (($dataCo === false) || (count($data) > count($dataCo))) {
			try {
				$dataCoStrs = $db->getNum();
			} catch (Exception $e) {}

			foreach ($dataCoStrs as $dak => $dataCoStr) {
				$dataCo[$dak]['id'] = $dataCoStr['id'];
				$dataCo[$dak]['num'] = json_decode($dataCoStr['num']);
			}
			
			$cache->set($key, $dataCo);
		}
		
		foreach ($dataCo as $keyCo => $valueCo) {
			if ($valueCo['id'] == $cid) {
				foreach ($valueCo['num'] as $nk => $nums) {
					if ($nums[0] == $level) {
						$dataCo[$keyCo]['num'][$nk][1] += 1;
					}
				}
			}
		}	
		
		$cache->set($key, $dataCo);

		foreach ($dataCo as $dk => $valueCo) {
			if ($valueCo['id'] == $cid) {
				$JSdataArr = $valueCo['num'];
				break;
			}
		}

		$dataCoJS = json_encode($JSdataArr);

		try {
			$dataCo = $db->renewNum($cid, $dataCoJS);
		} catch (Exception $e) {}
	}
	
	/**
	 * @当前勋章获得人数
	 * @param inr $cid
	 * @return Array
	 */
	public static function getNum($cid)
	{
		$key = 'ev:atlasbook:num';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$dataCo = $cache->get($key);
		
		$data = self::getData();
		
		if (($dataCo === false) || (count($data) > count($dataCo))) {
			try {
				$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
				$dataCoStrs = $db->getNum();
			} catch (Exception $e) {}

			foreach ($dataCoStrs as $dak => $dataCoStr) {
				$dataCo[$dak]['id'] = $dataCoStr['id'];
				$dataCo[$dak]['num'] = json_decode($dataCoStr['num']);
			}
			
			$cache->set($key, $dataCo);
		}
	
		foreach ($dataCo as $data) {
			if ($data['id'] == $cid) {
				foreach ($data['num'] as $num) {
					$nums[] = $num[1];
				}
			}
		}
		
		return $nums;
	}
	
	public static function getName($itemId, $plants)
	{
		foreach ($plants as $plant) {
			if ($plant['item_id'] == $itemId) {
				return $plant['name'];
			}
		}
	}
	
	public static function getCid($uid, $itemId, $plants, $cids)
	{
		$cid = 0;
		$level = 0;

		if (count($cids) > 0) {			
			foreach ($cids as $data) {
				$giftInfo[] = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($data);
			}

			$itemIds = array();
			foreach ($giftInfo as $gift) {
				$itemIds[] = $gift['item_id'];
			}
			
			if (in_array($itemId, $itemIds)) {
				foreach ($giftInfo as $info) {			
					if ($info['item_id'] == $itemId) {
						if ($info['level'] > $level) {
							$cid = $info['cid'];
							$level = $info['level'];			
						}
					}
				}
			}
		}

		if ($cid == 0) {		
			foreach ($plants as $plant) {
				if ($itemId == $plant['item_id']) {
					if ($plant['level'] == 5) {
						return $plant['cid'];			
					}
				}
			}
		}
		
		return $cid;
	}
	
	public static function getBasicInfo($uid)
	{
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);

		$islandIds = explode(',', $userVO['unlockIsland']);

		foreach ($islandIds as $islandId) {
			$plantVo[] = Hapyfish2_Island_Bll_Plant::getAllOnIsland($uid, $uid, $islandId);
		}
		
		$dataVo = array();
		if (count($plantVo) > 0) {
			foreach ($plantVo as $plants) {
				foreach ($plants['plants'] as $plant) {
					if (!in_array($plant['cid'], $dataVo)) {
						$dataVo[] = $plant['cid'];
					}
				}
			}
		}
		
		$lstPlants = Hapyfish2_Island_HFC_Plant::getInWareHouse($uid);
		if (count($lstPlants) > 0) {
			foreach ($lstPlants as $lstPlant) {
				if (!in_array($lstPlant['cid'], $dataVo)) {
					$dataVo[] = $lstPlant['cid'];
				}
			}
		}
		
		return $dataVo;
	}
	
	public static function getLevelBuyCid($cid, $plants)
	{
		foreach ($plants as $plant) {
			if ($plant['cid'] == $cid) {
				return $plant['level'];
			}
		}
	}
	
	public static function getItemLevel($uid, $itemId, $dataVo)
	{
		$level = 0;

		if (count($dataVo) > 0) {
			foreach ($dataVo as $cid) {
				$giftInfo[] = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
			}
			
			foreach ($giftInfo as $info) {
				if ($info['item_id'] == $itemId) {
					if ($info['level'] > $level) {
						$level = $info['level'];
					}
				}
			}
		}
		
		return $level;
	}
	
	public static function getItemID($cid)
	{
		//基础建筑列表
		$plants = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		
		foreach ($plants as $plant) {
			if ($plant['cid'] == $cid) {
				return $plant['item_id'];
			}
		}
	}
	
}