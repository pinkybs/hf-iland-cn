<?php

/**
 * Event AtlasBook
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2012/01/03    zhangli
*/
class Hapyfish2_Island_Event_Bll_AtlasBook
{
	const TXT001 = '当前勋章未开放';
	const TXT002 = '当前勋章已经是最高级';
	const TXT003 = '所需建筑等级不足，不能领取';
	const TXT004 = '所需建筑不足，不能领取';
	
	/**
	 * @获取用户图鉴数据
	 * @param int $uid
	 * @return Array
	 */
	public static function atlasBookInit($uid)
	{
		$result = array('status' => -1);
		
		//获取已经开放的图鉴 
		$data = Hapyfish2_Island_Event_Cache_AtlasBook::getData();
		//获取用户拥有的图鉴
		$userData = Hapyfish2_Island_Event_Cache_AtlasBook::getUserData($uid, $data);

		//获取用户的建筑
		$plantList = self::getUserPlantList($uid);
		
		//计算勋章的数据
		$medalList = Hapyfish2_Island_Event_Cache_AtlasBook::getMedalList($uid, $data, $userData);
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'medalList' => $medalList);
		return $resultVo;
	}

	/**
	 * @图鉴升级
	 * @param int $uid
	 * @param int $cid
	 * return Array
	 */
	public static function atlasBookLevelUp($uid, $cid)
	{
		$result = array('status' => -1);
		
		//获取已经开放的图鉴 
		$dataVo = Hapyfish2_Island_Event_Cache_AtlasBook::getData();
		$ids = array();
		foreach ($dataVo as $data) {
			$ids[] = $data['id'];
		}
		
		//要领取或者升级的图鉴没有开放
		if (!in_array($cid, $ids)) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}

		$newData = array();
		foreach ($dataVo as $data) {
			if ($data['id'] == $cid) {
				$newData = $data;
				break;
			}
		}
		
		if (!$newData) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}

		//获取用户拥有的图鉴
		$userData = Hapyfish2_Island_Event_Cache_AtlasBook::getUserData($uid, $dataVo);

		//判断勋章是否已经是最高级
		foreach ($userData as $user) {
			$userIds[] = $user[0];
			
			if ($user[0] == $cid) {
				if ($user[1] >= 3) {
					$result['content'] = self::TXT002;
					$resultVo = array('result' => $result);
					return $resultVo;
				}
			}
		}
		
		//计算勋章的数据
		$medalList = Hapyfish2_Island_Event_Cache_AtlasBook::getMedalList($uid, $dataVo, $userData);

		foreach ($medalList as $list) {
			if ($list['cid'] == $cid) {
				$medalData = $list;
				break;
			}
		}
	
		//缺少任意一个建筑都不能领取
		foreach ($medalData['buildingList'] as $value) {
			if ($value['star'] == 0) {
				$result['content'] = self::TXT004;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}

		$level = 0;
		$nextLevelStar = 0;
		
		//领取铜勋章
		if (!in_array($cid, $userIds)) {
			if ($medalData['currentStar'] >= ($medalData['totalStar'] / 5 * 3)) {
				array_push($userData, array($cid, 1));
				$level = 1;
				$nextLevelStar = $medalData['totalStar'] / 5 * 4;
			} else {
				$result['content'] = self::TXT003;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		} else {
			//升级或者领取勋章
			foreach ($userData as $user) {
				if ($user[0] == $cid) {
					$needData = $user;
					break;
				}
			}
			
			if ($needData[1] == 0) {
				//铜勋章
				if ($medalData['currentStar'] >= ($medalData['totalStar'] / 5 * 3)) {
					foreach ($userData as $uk => $user) {
						if ($user[0] == $cid) {
							$userData[$uk][1] = 1;
							$level = 1;
							$nextLevelStar = $medalData['totalStar'] / 5 * 4;
							break;
						}
					}
				} else {
					$result['content'] = self::TXT003;
					$resultVo = array('result' => $result);
					return $resultVo;
				}			
			} else if ($needData[1] == 1) {
				//银勋章
				if ($medalData['currentStar'] >= ($medalData['totalStar'] / 5 * 4)) {
					foreach ($userData as $uk => $user) {
						if ($user[0] == $cid) {
							$userData[$uk][1] = 2;
							$level = 2;
							$nextLevelStar = $medalData['totalStar'];
							break;
						}
					}
				} else {
					$result['content'] = self::TXT003;
					$resultVo = array('result' => $result);
					return $resultVo;
				}
			} else if ($needData[1] == 2) {
				//金勋章
				if ($medalData['currentStar'] >= $medalData['totalStar']) {
					foreach ($userData as $uk => $user) {
						if ($user[0] == $cid) {
							$userData[$uk][1] = 3;
							$level = 3;
							$nextLevelStar = 0;
							break;
						}
					}
				} else {
					$result['content'] = self::TXT003;
					$resultVo = array('result' => $result);
					return $resultVo;
				}
			} else {
				$result['content'] = self::TXT002;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//更新
		Hapyfish2_Island_Event_Cache_AtlasBook::renewUserData($uid, $userData);

		//更新完成数量
		Hapyfish2_Island_Event_Cache_AtlasBook::renewNum($cid, $level);

		
		$result['status'] = 1;
		$result['cid'] = $cid;
		$result['level'] = $level;
		$result['nextLevelStar'] = $nextLevelStar;
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @获取用户建筑列表
	 * @param int $uid
	 * @return Array
	 */
	public static function getUserPlantList($uid)
	{
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);

		$islandIds = explode(',', $userVO['unlockIsland']);

		foreach ($islandIds as $islandId) {
			$plantVo[] = Hapyfish2_Island_Bll_Plant::getAllOnIsland($uid, $uid, $islandId);
		}

		$lstArr = array();
		if (count($plantVo) > 0) {
			foreach ($plantVo as $plants) {
				foreach ($plants['plants'] as $plant) {
					foreach ($plant as $lsp) {
						$lstArr[] = $lsp['id'];
					}
				}
			}
		}
		
		$lstPlants = Hapyfish2_Island_HFC_Plant::getInWareHouse($uid);
		if (count($lstPlants) > 0) {
			foreach ($lstPlants as $lstPlant) {
				$lstPlantArr[] = $lstPlant['cid'];
			}
		}

		if (count($lstPlantArr) > 0) { 
			foreach ($lstPlantArr as $lskey => $lsvalue) {
				array_push($lstArr, $lsvalue);
			}
		}
		
		return $lstArr;
	}
}