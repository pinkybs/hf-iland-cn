<?php

/**
 * @Event LanternFestival
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/29    zhangli
*/
class Hapyfish2_Island_Event_Bll_LanternFestival
{
	const TXT001 = '不能重复领取';
	const TXT002 = '恭喜你获得：';
	const TXT003 = '星级不够，不能领取';
	const TXT004 = '食材不足，不能烹饪';
	const TXT005 = '元宵节烹饪';
	const TXT006 = '烹饪次数不足，不能烹饪';
	const TXT007 = '购买烹饪次数';
	const TXT008 = '购买食材';
	const TXT009 = '牛肉';
	const TXT010 = '水果';
	const TXT011 = '芝士';
	const TXT012 = '鸡蛋';
	const TXT013 = '建筑已经达到最高级,不能再烹饪啦~';
	
	/**
	 * @元宵节初始化
	 * @param int $uid
	 * @return array
	 */
	public static function LanternFestivalInit($uid)
	{
		$result = array('status' => -1);
		
		//获取图鉴数据
		$atlasBookDataVo = Hapyfish2_Island_Event_Bll_AtlasBook::atlasBookInit($uid);
		
		//自己的星级
		$medalStar = 0;
		$atlasIndex = 6;

		foreach ($atlasBookDataVo['medalList'] as $atlasKey => $atlasBook) {
			if ($atlasKey == $atlasIndex) {
				$medalStar = $atlasBook['currentStar'];
				break;
			}
		}
		
		//获取用户数据
		$buildings = Hapyfish2_Island_Event_Cache_LanternFestival::getUserData($uid);
		
		//用户建筑列表
		$userPlants = self::getUserPlantList($uid);

		//基础建筑列表
		$plants = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		
		foreach ($buildings as $bikey => $bivalue) {			
			if ($bivalue['getted'] == 0) {
				$buildings[$bikey]['cid'] = self::getCidByItemId($uid, $bivalue['item_id'], $plants, array());
				$buildings[$bikey]['star'] = 3;
			} else {
				$buildings[$bikey]['cid'] = self::getCidByItemId($uid, $bivalue['item_id'], $plants, $userPlants);
				$buildings[$bikey]['star'] = self::getItemLevel($uid, $bivalue['item_id'], $userPlants);
			}
			
			unset($buildings[$bikey]['item_id']);
			
			if ($bikey == 0) {
				$buildings[$bikey]['foodName'] = self::TXT012;
			} else if ($bikey == 1) {
				$buildings[$bikey]['foodName'] = self::TXT009;
			} else if ($bikey == 2) {
				$buildings[$bikey]['foodName'] = self::TXT010;
			} else {
				$buildings[$bikey]['foodName'] = self::TXT011;
			}
		}
		
		//剩余烹饪次数
		$cookTimes = Hapyfish2_Island_Event_Cache_LanternFestival::getCookTimes($uid);
		
		$result['status'] = 1;
		$resultVo = array('result' => $result,
						'medalStar' => (int)$medalStar,
						'cookTimes' => (int)$cookTimes,
						'buildings' => $buildings);
		
		return $resultVo;
	}
	
	/**
	 * @领取建筑
	 * @param int $uid
	 * @param int $id
	 * @return array
	 */
	public static function getLFplant($uid, $index)
	{
		$result = array('status' => -1);
	
		//id错误
		if (!in_array($index, array(0, 1, 2, 3))) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}

		if ($index == 0) {
			$cid = 138432;
		} else if ($index == 1) {
			$cid = 138932;
		} else if ($index == 2) {
			$cid = 139632;
		} else {
			$cid = 139232;
		}
	
		//获取用户数据
		$buildings = Hapyfish2_Island_Event_Cache_LanternFestival::getUserData($uid);
		
		foreach ($buildings as $key => $plant) {
			if ($key == $index) {
				$needStar = $plant['needMedalStar'];
				
				//不能重复领取
				if ($plant['getted'] == 1) {
		    		$result['content'] = self::TXT001;
		    		$resultVo = array('result' => $result);
		    		return $resultVo;
				}
			}
		}

		//领取第一个建筑不用判断星数
		if ($index > 0) {
			//获取图鉴数据
			$atlasBookDataVo = Hapyfish2_Island_Event_Bll_AtlasBook::atlasBookInit($uid);
			
			//自己的星级
			$userStar = 0;
			$atlasIndex = 6;

			foreach ($atlasBookDataVo['medalList'] as $atlasKey => $atlasBook) {
				if ($atlasKey == $atlasIndex) {
					$userStar = $atlasBook['currentStar'];
					break;
				}
			}
			
			//判断星数是否足够
			if ($userStar < $needStar) {
	    		$result['content'] = self::TXT003;
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
		}
		
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();

		$compensation->setItem($cid, 1);
		$ok = $compensation->sendOne($uid, self::TXT002);
		
		if ($ok) {
			info_log($uid . ':' . $cid, 'LF-getPlantOK');
			
			//标记已经领取
			foreach ($buildings as $keys => $building) {
				if ($keys == $index) {
					$buildings[$keys]['getted'] = 1;
					break;
				}
			}
			
			Hapyfish2_Island_Event_Cache_LanternFestival::renewUserData($uid, $buildings);
			
			$result['status'] = 1;
			$result['itemBoxChange'] = true;
			$resultVo = array('result' => $result, 'index' => $index);
		} else {
			info_log($uid . ':' . $cid, 'LF-getPlantErr');
			
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
		}
		
		return $resultVo;
	}
	
	/**
	 * @购买烹饪次数
	 * @param int $uid
	 * @param int $id
	 * @return array
	 */
	public static function buyLFcookTimes($uid, $id)
	{
		$result = array('status' => -1);
		
		//id错误
		if (!in_array($id, array(0, 1, 2))) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}

		if ($id == 0) {
			$gold = 2;
			$addCookTimes = 1;
		} else if ($id == 1) {
			$gold = 8;
			$addCookTimes = 5;
		} else {
			$gold = 12;
			$addCookTimes = 10;
		}
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $gold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//剩余烹饪次数
		$cookTimes = Hapyfish2_Island_Event_Cache_LanternFestival::getCookTimes($uid);
		
		$cookTimes += $addCookTimes;
		
		//更新烹饪次数
		Hapyfish2_Island_Event_Cache_LanternFestival::renewCookTimes($uid, $cookTimes);
		
		$nowTime = time();

		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $gold,
						'summary' => self::TXT007 . 'x' . $addCookTimes,
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 1);

        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		
        //统计
		$report = array($uid, $id, $cookTimes);
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('LF-buyCookTimes', $report);
        
		$result['status'] = 1;
		$result['goldChange'] = -$gold;
		$resultVo = array('result' => $result, 'cookTimes' => $cookTimes);
		
		return $resultVo;
	}
	
	/**
	 * @烹饪
	 * @param int $uid
	 * @param int $id
	 * @param int $eid
	 * @return array
	 */
	public static function toLFcook($uid, $index, $eid)
	{
		$result = array('status' => -1);
		
		//id错误
		if (!in_array($index, array(0, 1, 2, 3))) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//烹饪的方式只有2种
		if (!in_array($cid, array(0, 1))) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//获取用户数据
		$buildings = Hapyfish2_Island_Event_Cache_LanternFestival::getUserData($uid);

		foreach ($buildings as $key => $plant) {
			if ($key == $index) {
				$needData = $plant;
				break;
			}
		}

		if (!$needData) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}

		if ($needData['getted'] == 1) {
			//用户建筑列表
			$userPlants = self::getUserPlantList($uid);

			$nowLevel = self::getItemLevel($uid, $needData['item_id'], $userPlants);
			
			if ($nowLevel == 5) {
				$resultVo = array('result' => $result,
							'index' => (int)$index,
							'food' => (int)$needData['food'],
							'praise' => (int)$needData['praise'],
							'star' => (int)$levelDataVo['level']);
				
	    		$result['content'] = self::TXT013;
	    		$resultVo = array('result' => $result,
	    					'index' => (int)$index,
							'food' => (int)$needData['food'],
							'praise' => (int)$needData['praise'],
							'star' => (int)$nowLevel);

	    		return $resultVo;
			}
		}

		
		//食材不足
		if ($needData['food'] <= 0) {
    		$result['content'] = self::TXT004;
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//剩余烹饪次数
		$cookTimes = Hapyfish2_Island_Event_Cache_LanternFestival::getCookTimes($uid);
		
		//烹饪次数不足
		if ($cookTimes <= 0) {
    		$result['content'] = self::TXT006;
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		if ($eid == 1) {
			//精致烹饪消耗的宝石
			$gold = 5;
			$result['goldChange'] = -$gold;

	    	//获得用户gold
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			
			//宝石不足
			if ($userGold < $gold) {
	    		$result['content'] = 'serverWord_140';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
			
			//增加的好评
			$addPraise = 2;
		} else {
			//增加的好评
			$addPraise = 1;
		}
		
		//增加好评
		$needData['praise'] += $addPraise;
		
		//扣除一个食材
		$needData['food'] -= 1;

		foreach ($buildings as $newKey => $newData) {
			if ($index == $newKey) {
				$buildings[$newKey] = $needData;
				break;
			}
		}
		
		//烹饪次数减少
		$cookTimes -= 1;
		Hapyfish2_Island_Event_Cache_LanternFestival::renewCookTimes($uid, $cookTimes);
		
		if ($eid == 1) {
			$nowTime = time();

			//获取用户等级
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			
			//扣除宝石
			$goldInfo = array('uid' => $uid,
							'cost' => $gold,
							'summary' => self::TXT005,
							'user_level' => $userLevel,
							'create_time' => $nowTime,
							'cid' => '',
							'num' => 1);
	
	        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		}
		
		//判断好评数是否足够升级
		$levelDataVo = self::checkLevelUp($uid, $needData);
		
		$result['status'] = 1;

		if ($levelDataVo['buildingVo']) {
			foreach ($buildings as $decKey => $decData) {
				if ($index == $decKey) {
					if ($levelDataVo['level'] == 4) {
						$buildings[$decKey]['praise'] -= 5;
					} else {
						$buildings[$decKey]['praise'] -= 10;
					}
					
					$nowPraise = $buildings[$decKey]['praise'];
					break;
				}
			}
			
			$resultVo = array('result' => $result,
							'index' => (int)$index,
							'food' => (int)$needData['food'],
							'praise' => (int)$nowPraise,
							'star' => (int)$levelDataVo['level'],
							'buildingVo' => $levelDataVo['buildingVo']);
		} else {
			$resultVo = array('result' => $result,
							'index' => (int)$index,
							'food' => (int)$needData['food'],
							'praise' => (int)$needData['praise'],
							'star' => (int)$levelDataVo['level']);
		}
		
		//更新用户的数据
		Hapyfish2_Island_Event_Cache_LanternFestival::renewUserData($uid, $buildings);
		
		return $resultVo;
	}
	
	/**
	 * @判断好评数是否足够升级
	 * @param int $uid
	 * @param array $needData
	 * @return array
	 */
	public static function checkLevelUp($uid, $needData)
	{
		//用户建筑列表
		$userPlants = self::getUserPlantList($uid);

		//基础建筑列表
		$plants = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		
		$cid = self::getCidByItemId($uid, $needData['item_id'], $plants, $userPlants);
		$level = self::getItemLevel($uid, $needData['item_id'], $userPlants);

		$resultVo = array('result' => array('status' => -1));
		if ($level == 3) {
			if ($needData['praise'] >= 5) {
				//升级建筑
				$resultVo = self::lfUpgradePlant($uid, $cid);			
			}
		} else if ($level == 4) {
			if ($needData['praise'] >= 10) {
				//升级建筑
				$resultVo = self::lfUpgradePlant($uid, $cid);
			}
		}
		
		if ($resultVo['result']['status'] == 1) {
			$level += 1;
			$dataVo = array('level' => $level, 'buildingVo' => $resultVo['buildingVo']);
		} else {
			$dataVo = array('level' => $level, 'buildingVo' => array());
		}
		
		return $dataVo;
	}
	
	/**
	 * @购买食材
	 * @param int $uid
	 * @param int $index
	 * @param int $num
	 * @return array
	 */
	public static function buyLFcook($uid, $index, $num)
	{
		$result = array('status' => -1);
		
		if ($num <= 0) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//id错误
		if (!in_array($index, array(0, 1, 2, 3))) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		$gold = $num * 2;

    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $gold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//获取用户数据
		$buildings = Hapyfish2_Island_Event_Cache_LanternFestival::getUserData($uid);
		
		$nowNum = 0;
		foreach ($buildings as $key => $plant) {
			if ($key == $index) {
				$buildings[$key]['food'] += $num;
				$nowNum = $buildings[$key]['food'];
				break;
			}
		}
		
		//更新用户的数据
		Hapyfish2_Island_Event_Cache_LanternFestival::renewUserData($uid, $buildings);
		
		$nowTime = time();

		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $gold,
						'summary' => self::TXT008 . 'x' . $num,
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 1);

        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		
		$result['status'] = 1;
		$result['goldChange'] = -$gold;
		$resultVo = array('result' => $result, 'index' => (int)$index, 'num' => (int)$nowNum);
		
		return $resultVo;
	}
	
	/**
	 * @升级建筑
	 * @param int $uid
	 * @param int $cid
	 * @return Array
	 */
	public static function lfUpgradePlant($uid, $cid)
	{
		$result = array('status' => -1);
		
        //只有4个建筑共8个cid
        if (!in_array($cid, array(138432, 138532, 138932, 139032, 139632, 139732, 139232, 139332)))  {
			$result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
        }
		
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$items = $db->checkUseing($uid, $cid);
		} catch (Exception $e) {}

		$itemId = $items['id'] . $items['item_type'];

        $itemType = substr($itemId, -2, 1);
        $itemId = substr($itemId, 0, -2);
        if ($itemType != 3) {
            $result['content'] = 'serverWord_115';
            $resultVo = array('result' => $result);
            return $resultVo;
        }
        
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $ownerCurrentIsland = $userVO['current_island'];
        $userPlant = Hapyfish2_Island_HFC_Plant::getOne($uid, $itemId, 1, $ownerCurrentIsland);
        if (!$userPlant) {
            $result['content'] = 'serverWord_115';
            $result = array('result' => $result);
            return $result;
        }

		switch ($userPlant['cid']) {
			case 138432:
				$nextCid = 138532;
			break;
			case 138532:
				$nextCid = 138632;
			break;
			case 138932:
				$nextCid = 139032;
			break;
			case 139032:
				$nextCid = 139132;
			break;
			case 139632:
				$nextCid = 139732;
			break;
			case 139732:
				$nextCid = 139832;
			break;
			case 139232:
				$nextCid = 139332;
			break;
			case 139332:
				$nextCid = 139432;
			break;
		}
        
        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($userPlant['cid']);
        if (!$plantInfo || !$nextCid) {
        	$resultVo = array('result' => $result);
            return $resultVo;
        }
        
        $nextLevelPlantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($nextCid);
        if (!$nextLevelPlantInfo) {
			$resultVo = array('result' => $result);
            return $resultVo;
        }

        $praiseChange = $nextLevelPlantInfo['add_praise'] - $plantInfo['add_praise'];
        
        $userInfo = Hapyfish2_Island_HFC_User::getUser($uid, array('coin' => 1, 'level' => 1));
        if ($userInfo === null) {
        	$resultVo = array('result' => $result);
            return $resultVo;
        }
        
        $userIslandInfo = Hapyfish2_Island_HFC_User::getUserIsland($uid);
    
        $now = time();
        
        $addExp = 5;
       
		$userPlant['level'] += 1;
		$userPlant['cid'] = $nextLevelPlantInfo['cid'];
		$userPlant['pay_time'] = $nextLevelPlantInfo['pay_time'];
		$userPlant['ticket'] = $nextLevelPlantInfo['ticket'];
		
		$res = Hapyfish2_Island_HFC_Plant::updateOne($uid, $itemId, $userPlant, true);
		
		$aryParam = array();
		$aryParam['name'] = $plantInfo['name'];
		$aryParam['num'] = $userPlant['level'];
		
		//add log
		foreach ($aryParam as $k => $v) {
            $parakeys[] = '{*' . $k . '*}';
            $paravalues[] = $v;
        }
        
		if ($res) {
			Hapyfish2_Island_Cache_Plant::reloadAllByItemKind($uid);
			$userIslandInfo['praise'] += $praiseChange;
			Hapyfish2_Island_HFC_User::updateUserIsland($uid, $userIslandInfo);
			
			//check double exp
			$userCardStatus = Hapyfish2_Island_HFC_User::getCardStatus($uid);
			$doubleexpCardTime = $userCardStatus['doubleexp'];
			if ($doubleexpCardTime - $now > 0) {
				$addExp = $addExp * 2;
				$result['expDouble'] = 2;
			}
			Hapyfish2_Island_HFC_User::incUserExp($uid, $addExp);
			
			$result['status'] = 1;
			$result['expChange'] = $addExp;
			$result['praiseChange'] = $praiseChange;
		}

    	try {
			//check user level up
        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
            $result['levelUp'] = $levelUp['levelUp'];
            $result['islandLevelUp'] = $levelUp['islandLevelUp'];
			if ($levelUp['feed']) {
            	$result['feed'] = $levelUp['feed'];
            } 
		} catch (Exception $e) {
		}
        
        $buildingVo = Hapyfish2_Island_Bll_Plant::handlerPlant($userPlant, $now);
        
        $resultVo = array('result' => $result, 'buildingVo' => $buildingVo);

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
					if (!in_array($plant['cid'], $lstArr)) {
						$lstArr[] = $plant['cid'];
					}
				}
			}
		}
		
		$lstPlants = Hapyfish2_Island_HFC_Plant::getInWareHouse($uid);
		if (count($lstPlants) > 0) {
			foreach ($lstPlants as $lstPlant) {
				if (!in_array($lstPlant['cid'], $lstArr)) {
					$lstArr[] = $lstPlant['cid'];
				}
			}
		}
		
		return $lstArr;
	}
	
	/**
	 * 获取建筑cid
	 * @param int $uid
	 * @param int $itemId
	 * @param array $plants
	 * @param array $cids
	 */
	public static function getCidByItemId($uid, $itemId, $plants, $cids)
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
					if ($plant['level'] == 3) {
						return $plant['cid'];			
					}
				}
			}
		}
		
		return $cid;
	}
	
	/**
	 * @获取建筑等级
	 * @param int $uid
	 * @param int $itemId
	 * @param array $dataVo
	 * @return int
	 */
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
	
}