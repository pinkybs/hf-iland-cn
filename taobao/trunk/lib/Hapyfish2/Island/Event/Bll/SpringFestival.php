<?php

/**
 * @Event SpringFestival
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/09    zhangli
*/
class Hapyfish2_Island_Event_Bll_SpringFestival
{
	const TXT001 = '购买数量错误';
	const TXT002 = '春节购买水晶';
	const TXT003 = '水晶不足，不能升级';
	const TXT004 = '春节购买福袋';
	const TXT005 = '不能重复领取';
	const TXT006 = '碎片不足，不能领取';
	const TXT007 = '恭喜你获得：';
	const TXT008 = '春节购买碎片';
	const TXT009 = '福袋不足，不能开启';
	const TXT010 = '踢出饺子';
	const TXT011 = '饺子还没煮好哦，不要着急';
	const TXT012 = '只剩下一个饺子啦,不能再踢了';
	const TXT013 = '建筑已升到最高级别';
	
	/**
	 * @获取拼图页面数据
	 * @param int $uid
	 * @return Array
	 */
	public static function getSpringFestivalData($uid)
	{
		$result = array('status' => -1);

		//静态数据
		$buildingClass = Hapyfish2_Island_Event_Cache_SpringFestival::getBasicData();
			
		//拼图的价格
		$pingtuPrice = 4;
		
		//饺子上限数量
		$jiaoMaxNum = 20;
		
		//踢出饺子的价格
		$kickJiaoziPrice = 2;
		
		//每个水晶的价格
		$crystalPrice = 8;
		
		//吃饺子间隔时间
		$jiaoziDelayTime = 3600;
		
		//福袋的价格
		$fudaiPrice = array(array(1, 2), array(10, 15), array(20, 20));
		
		//升级建筑的价格
		$buildingUpgradePrices = array(5, 10);

		//获取福袋数量
		$luckyBagNum = Hapyfish2_Island_Event_Cache_SpringFestival::getLuckyBagNum($uid);
		
		//水晶数量
		$curCrystalNum = Hapyfish2_Island_Event_Cache_SpringFestival::getCurCrystalNum($uid);
		
		//当前可以吃的饺子数量
		$curJiaoNum = Hapyfish2_Island_Event_Cache_SpringFestival::getDumplingNum($uid);
		
		//最多累积20盘饺子
		if ($curJiaoNum['num'] >= 20) {
			$nextJiaoziDelay = -1;
		} else {
			//下一盘饺子倒计时
			$nextJiaoziDelay = $curJiaoNum['time'];
		}
		
		//饺子列表
		$jiaozis = Hapyfish2_Island_Event_Cache_SpringFestival::getDumpling($uid); 
		
		foreach ($jiaozis as $key => $list) {
			unset($jiaozis[$key]['id']);
			unset($jiaozis[$key]['odds']);
			unset($jiaozis[$key]['item_name']);
		}
		
		//建筑拼图列表
		$buildings = Hapyfish2_Island_Event_Cache_SpringFestival::getFragmentData($uid);
		
		$result['status'] = 1;
		$resultVo = array('result' => $result,
						'curFudaiNum' => (int)$luckyBagNum,
						'curCrystalNum' => (int)$curCrystalNum,
						'pingtuPrice' => $pingtuPrice,
						'fudaiPrice' => $fudaiPrice,
						'crystalPrice' => $crystalPrice,
						'kickJiaoziPrice' => $kickJiaoziPrice,
						'buildingUpgradePrices' => $buildingUpgradePrices,
						'jiaoziDelayTime' => $jiaoziDelayTime,
						'jiaoMaxNum' => $jiaoMaxNum,
						'curJiaoNum' => (int)$curJiaoNum['num'],
						'nextJiaoziDelay' => $nextJiaoziDelay,
						'jiaozis' => $jiaozis,
						'buildingClass' => $buildingClass,
						'buildings' => $buildings);
		
		return $resultVo;
	}
	
	/**
	 * @买水晶
	 * @param int $uid
	 * @param int $num
	 * @return Array
	 */
	public static function sfBuyRystal($uid, $num)
	{
		$result = array('status' => -1);
		
		//购买数量不能少于1
		if ($num <= 0) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//水晶价格
		$needGold = $num * 8;
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $needGold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//获得用户水晶数量
		$curCrystalNum = Hapyfish2_Island_Event_Cache_SpringFestival::getCurCrystalNum($uid);
		
		//更新用户水晶数量
		$curCrystalNum += $num;
		$ok = Hapyfish2_Island_Event_Cache_SpringFestival::renewCurCrystalNum($uid, $curCrystalNum);
		
		if ($ok) {
			//统计每日购买水晶数量
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('sfbuyrystalOK', array($uid, $num));
			
			$nowTime = time();
			
			//获取用户等级
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			
			//扣除宝石
			$goldInfo = array('uid' => $uid,
							'cost' => $needGold,
							'summary' => self::TXT002 . 'x' . $num,
							'user_level' => $userLevel,
							'create_time' => $nowTime,
							'cid' => '',
							'num' => $num);
	
	        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
	
			if ($ok2) {
				try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldInfo['cost']);
	
					//task id 3068,task type 19
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
		        } catch (Exception $e) {}
			}
		}
		
		$result['status'] = 1;
		$result['goldChange'] = -$needGold;
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @购买福袋
	 * @param int $uid
	 * @param int $index
	 * @return Array
	 */
	public static function sfBuyLuckyBag($uid, $index)
	{
		$result = array('status' => -1);
		
		switch ($index) {
			case 0:
				$num = 1;
				$gold = 2;
			break;
			case 1:
				$num = 10;
				$gold = 15;
			break;
			case 2:
				$num = 20;
				$gold = 20;
			break;
			default:
				$num = 0;
				$gold = 0;
		}
		
		//福袋只有3种
		if (($num == 0) || ($gold == 0)) {
			$result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $gold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//获取福袋数量
		$luckyBagNum = Hapyfish2_Island_Event_Cache_SpringFestival::getLuckyBagNum($uid);
		
		//更新福袋数量
		$luckyBagNum += $num;
		$ok = Hapyfish2_Island_Event_Cache_SpringFestival::renewLuckyBag($uid, $luckyBagNum);
		
		if ($ok) {
			//统计每日购买福袋
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('sfbuyluckybagOK', array($uid, $num, $gold));
			
			$nowTime = time();
			
			//获取用户等级
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			
			//扣除宝石
			$goldInfo = array('uid' => $uid,
							'cost' => $gold,
							'summary' => self::TXT004 . 'x' . $num,
							'user_level' => $userLevel,
							'create_time' => $nowTime,
							'cid' => '',
							'num' => $num);
	
	        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);

			if ($ok2) {
				try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldInfo['cost']);
	
					//task id 3068,task type 19
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
		        } catch (Exception $e) {}
			}
		}
		
		$result['status'] = 1;
		$result['goldChange'] = -$gold;
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @领取建筑
	 * @param int $uid
	 * @param int $cid
	 * @return Array
	 */
	public static function sfToReceivePlant($uid, $cid)
	{
		$result = array('status' => -1);
		
		//只有4个建筑可以领取
		if (!in_array($cid, array(136332, 137832, 137032, 135832))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		if ($cid == 136332) {
			$index = 0;
		} else if ($cid == 137832) {
			$index = 1;
		} else if ($cid == 137032) {
			$index = 2;
		} else if ($cid == 135832) {
			$index = 3;
		}
		
		//建筑拼图列表
		$buildings = Hapyfish2_Island_Event_Cache_SpringFestival::getFragmentData($uid);
		
		foreach ($buildings as $key => $building) {
			if ($key == $index) {
				$needData = $building;
				break;
			}
		}
	
		if (!$needData) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $result;
		}
		
		//不能重复领取
		if ($needData['hasGet'] == 1) {
            $result['content'] = self::TXT005;
            $resultVo = array('result' => $result);
            return $resultVo;
		}

		//碎片不足
		foreach ($needData['puzzles'] as $puzzles) {
			if ($puzzles <= 0) {
	            $result['content'] = self::TXT006;
	            $resultVo = array('result' => $result);
	            return $resultVo;
			}
		}
			
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();
	
		$compensation->setItem($cid, 1);
		$ok = $compensation->sendOne($uid, self::TXT007);
		
		if ($ok) {
			//统计领取建筑
			info_log($uid, 'sfToReceivePlantOK-' . $cid);
			
			//扣除碎片
			foreach ($buildings as $bkey => $bdata) {
				if ($bkey == $index) {
					//更改领取状态
					$buildings[$bkey]['hasGet'] = 1;
					foreach ($bdata['puzzles'] as $pkeys => $pvalues) {
						$buildings[$bkey]['puzzles'][$pkeys] = ($pvalues - 1);
					}
				}
			}
			
			//更新碎片和领取状态
			Hapyfish2_Island_Event_Cache_SpringFestival::renewFragmentData($uid, $buildings);
		} else {
			info_log($uid, 'sfToReceivePlantErr-' . $cid);
			
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		$result['status'] = 1;
		$result['itemBoxChange'] = true;
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @升级建筑
	 * @param int $uid
	 * @param int $cid
	 * @return Array
	 */
	public static function sfUpgradePlant($uid, $itemId)
	{
		$result = array('status' => -1);
		
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

        if (in_array($userPlant['cid'], array(136532, 138032, 137332, 136032)))  {
			$result['content'] = self::TXT013;
            $resultVo = array('result' => $result);
            return $resultVo;
        }
        
        //只有春节的4个建筑共12个cid可以用水晶升级
        if (!in_array($userPlant['cid'], array(136332, 136432, 137832, 137932, 137032, 137132, 135832, 135932)))  {
			$result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
        }

		switch ($userPlant['cid']) {
			case 136332:
				$nextCid = 136432;
			break;
			case 136432:
				$nextCid = 136532;
			break;
			case 137832:
				$nextCid = 137932;
			break;
			case 137932:
				$nextCid = 138032;
			break;
			case 137032:
				$nextCid = 137132;
			break;
			case 137132:
				$nextCid = 137232;
			break;
			case 135832:
				$nextCid = 135932;
			break;
			case 135932:
				$nextCid = 136032;
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
        
		//获得用户水晶数量
		$curCrystalNum = Hapyfish2_Island_Event_Cache_SpringFestival::getCurCrystalNum($uid);
        
        //判断升级的水晶是否足够
        $num = 0;
        if (in_array($userPlant['cid'], array(136332, 137832, 137032, 135832))) {
        	//3星升级到4星
        	$num = 5;
        	if ($curCrystalNum < $num) {
        		$result['content'] = self::TXT003;
	            $resultVo = array('result' => $result);
	            return $resultVo;
        	}
        } else {
        	//4星升级到5星
        	$num = 10;
			if ($curCrystalNum < 10) {
        		$result['content'] = self::TXT003;
	            $resultVo = array('result' => $result);
	            return $resultVo;
        	}
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

			//更新用户水晶数量
			$curCrystalNum -= $num;
			$ok = Hapyfish2_Island_Event_Cache_SpringFestival::renewCurCrystalNum($uid, $curCrystalNum);
			
			$result['status'] = 1;
			$result['expChange'] = $addExp;
			$result['praiseChange'] = $praiseChange;
			
			//升级成功,赠送建筑
			switch ($nextCid) {
				case 136432:
					$toSendCid = 135732;
				break;
				case 136532:
					$toSendCid = 136932;
				break;
				case 137932:
					$toSendCid = 136132;
				break;
				case 138032:
					$toSendCid = 136832;
				break;
				case 137132:
					$toSendCid = 136232;
				break;
				case 137232:
					$toSendCid = 138132;
				break;
				case 135932:
					$toSendCid = 136732;
				break;
				case 136032:
					$toSendCid = 136632;
				break;
			}
				
			//发放奖励
			$compensation = new Hapyfish2_Island_Bll_Compensation();
			$compensation->setItem($toSendCid, 1);
			
			$okToSend = $compensation->sendOne($uid, self::TXT007);
			
			if ($okToSend) {
				info_log($uid . ':' . $toSendCid, 'sf-toSendCid');
			}
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
        
        $resultVo = array('result' => $result, 'crystalNum' => -$num, 'buildingVo' => $buildingVo);

        return $resultVo;
	}
	
	/**
	 * @购买碎片
	 * @param int $uid
	 * @param int $cid
	 * @param int $index
	 * $return Array
	 */
	public static function sfBuyFragment($uid, $cid, $id, $num)
	{
		$result = array('status' => -1);
		
		//只能4个cid中的
		if (!in_array($cid, array(136332, 137832, 137032, 135832))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//碎片每个cid下只有9种
		if (!in_array($id, array(0, 1, 2, 3, 4, 5, 6, 7, 8))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		if ($num <= 0) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		if ($cid == 136332) {
			$index = 0;
		} else if ($cid == 137832) {
			$index = 1;
		} else if ($cid == 137032) {
			$index = 2;
		} else if ($cid == 135832) {
			$index = 3;
		}
		
		//碎片价格
		$gold = $num * 4;
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $gold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//建筑拼图列表
		$buildings = Hapyfish2_Island_Event_Cache_SpringFestival::getFragmentData($uid);
		
		//统计购买碎片
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('sfBuyFragment', array($uid, $cid, $id));
		
		//增加碎片
		foreach ($buildings as $bkey => $bdata) {
			if ($bkey == $index) {
				foreach ($bdata['puzzles'] as $pkeys => $pvalues) {
					if ($id == $pkeys) {
						$buildings[$bkey]['puzzles'][$pkeys] = ($pvalues + $num);
						break;
					}
				}
			}
		}
		
		//更新碎片和领取状态
		Hapyfish2_Island_Event_Cache_SpringFestival::renewFragmentData($uid, $buildings);
		
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
						'num' => $num);

        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);

		if ($ok2) {
			try {
				Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldInfo['cost']);

				//task id 3068,task type 19
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
				if ( $checkTask['status'] == 1 ) {
					$result['finishTaskId'] = $checkTask['finishTaskId'];
				}
	        } catch (Exception $e) {}
		}
		
		$result['status'] = 1;
		$result['goldChange'] = -$gold;
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @用碎片换礼包
	 * @param int $uid
	 * @param int $cid
	 * @param int $index
	 * @return Array
	 */
	public static function sfToReceiveBox($uid, $cid, $id)
	{
		$result = array('status' => -1);
		
		//只能4个cid中的
		if (!in_array($cid, array(136332, 137832, 137032, 135832))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}

		//每个建筑下对应礼包只有3种
		if (!in_array($id, array(0, 1, 2))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}

		//需要扣除的碎片索引
		if ($id == 0) {
			$decFragmentIndexs = array(0, 1, 2);
		} else if ($id == 1) {
			$decFragmentIndexs = array(3, 4, 5);
		} else {
			$decFragmentIndexs = array(6, 7, 8);
		}
		
		if ($cid == 136332) {
			$index = 0;
		} else if ($cid == 137832) {
			$index = 1;
		} else if ($cid == 137032) {
			$index = 2;
		} else if ($cid == 135832) {
			$index = 3;
		}
		
		//建筑拼图列表
		$buildings = Hapyfish2_Island_Event_Cache_SpringFestival::getFragmentData($uid);
		
		foreach ($buildings as $key => $building) {
			if ($key == $index) {
				$needData = $building;
				break;
			}
		}

		if (!$needData) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
	
		foreach ($needData['puzzles'] as $pkey => $puzzles) {
			foreach ($decFragmentIndexs as $desIndex) {
				if ($desIndex == $pkey) {
					//碎片不足
					if ($puzzles <= 0) {
			            $result['content'] = self::TXT006;
			            $resultVo = array('result' => $result);
			            return $resultVo;
					}
					break;
				}
			}
		}
	
		$nowTime = time();
		
		//静态数据
		$buildingClass = Hapyfish2_Island_Event_Cache_SpringFestival::getBasicData();
			
		//要兑换的列表
		foreach ($buildingClass as $listVo) {
			if ($listVo['cid'] == $cid) {
				foreach ($listVo['awards'] as $listkey => $list) {
					if ($listkey == $id) {
						$cidData = $list;
					}
				}
			}
		}
	
		if (!$cidData) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
	
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();
	
		$coin = 0;
		$gold = 0;
		$items = array();
		
		//金币
		if ($cidData['type'] == 1) {
			$compensation->setCoin($cidData['num']);
			$coin += $cidData['num'];
		}
		
		//宝石
		if ($cidData['type'] == 2) {
			$compensation->setGold($cidData['num']);
			$gold += $cidData['num'];
		}
		
		//道具
		if ($cidData['type'] == 3) {
			$compensation->setItem($cidData['cid'], $cidData['num']);
			$items[] = array('cid' => $cidData['cid'], 'num' => $cidData['num']);
		}
		
		//水晶
		if ($cidData['type'] == 4) {
			//获得用户水晶数量
			$curCrystalNum = Hapyfish2_Island_Event_Cache_SpringFestival::getCurCrystalNum($uid);
			
			$curCrystalNum += $cidData['num'];
			Hapyfish2_Island_Event_Cache_SpringFestival::renewCurCrystalNum($uid, $curCrystalNum);
			
			//发送Feed
        	$minifeed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => self::TXT007 . $cidData['num'] . '水晶'),
							'type' => 3,
							'create_time' => $nowTime);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			
			$ok = true;
		}
		
		//碎片
		if ($cidData['type'] == 5) {
			foreach ($buildings as $bkey => $bdata) {
				if ($bkey == $index) {
					foreach ($bdata['puzzles'] as $pkeys => $pvalues) {
						foreach ($decFragmentIndexs as $decFraIndex) {
							if ($decFraIndex == $pkeys) {
								$buildings[$bkey]['puzzles'][$pkeys] += $cidData['num'];
							}
						}
					}
				}
			}
			
			//更新碎片和领取状态
			Hapyfish2_Island_Event_Cache_SpringFestival::renewFragmentData($uid, $buildings);
			
			//发送Feed
        	$minifeed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => self::TXT007 . $cidData['num'] . '个碎片'),
							'type' => 3,
							'create_time' => $nowTime);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			
			$ok = true;
		}
		
		if (!$ok) {
			$ok = $compensation->sendOne($uid, self::TXT007);
		}
		
		if ($ok) {
			//统计每日兑换礼包
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('sfToReceiveBoxOK-' . $cid, array($uid, $id));
			
			//扣除碎片
			foreach ($buildings as $bkey => $bdata) {
				if ($bkey == $index) {
					foreach ($bdata['puzzles'] as $pkeys => $pvalues) {
						foreach ($decFragmentIndexs as $desIndex) {
							if ($desIndex == $pkeys) {
								$buildings[$bkey]['puzzles'][$pkeys] = ($pvalues - 1);
							}
						}
					}
				}
			}
			
			//更新碎片和领取状态
			Hapyfish2_Island_Event_Cache_SpringFestival::renewFragmentData($uid, $buildings);
		} else {
			info_log($uid, 'sfToReceiveBoxOErr-' . $cid . '-' . $id);
			
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		$result['status'] = 1;
		$result['coinChange'] = $coin;
		$result['goldChange'] = $gold;
		
		if (count($items) > 0) {
			$result['itemBoxChange'] = true;
			$resultVo = array('result' => $result, 'items' => $items);
		} else {
			$resultVo = array('result' => $result);
		}
		
		return $resultVo;
	}
	
	/**
	 * @开福袋
	 * @param int $uid
	 * @param int $cid
	 * @return Array
	 */
	public static function sfOpenLuckyBag($uid, $cid)
	{
		$result = array('status' => -1);
		
		//只能4个cid中的
		if (!in_array($cid, array(136332, 137832, 137032, 135832))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//获取福袋数量
		$luckyBagNum = Hapyfish2_Island_Event_Cache_SpringFestival::getLuckyBagNum($uid);
		
		//没有福袋
		if ($luckyBagNum <= 0) {
            $result['content'] = self::TXT009;
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//获取概率表
		$aryItem = Hapyfish2_Island_Event_Cache_SpringFestival::getLuckyBagList();
		if (!$aryItem) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}

		//get random item key
		$aryRandOdds = array();
		foreach ($aryItem as $item) {
			$key = $item['item_order'];
			$aryRandOdds[$key] = $item['item_odds'];
		}

		$gainKey = self::randomKeyForOdds($aryRandOdds);
        $gainItem = $aryItem[$gainKey - 1];
 
        $crystal = 0;
        $nowTime = time();
        $pingtu = array();
		
        //水晶
		if ($gainItem['item_type'] == 4) {
			$crystal = $gainItem['item_num'];
			
			//水晶数量
			$curCrystalNum = Hapyfish2_Island_Event_Cache_SpringFestival::getCurCrystalNum($uid);
			
			//更新水晶数量
			$curCrystalNum += $crystal;
			Hapyfish2_Island_Event_Cache_SpringFestival::renewCurCrystalNum($uid, $curCrystalNum);
			
			//发送Feed
        	$minifeed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => self::TXT007 . $crystal . $gainItem['item_name']),
							'type' => 3,
							'create_time' => $nowTime);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
		}
        		
		//碎片
		if ($gainItem['item_type'] == 5) {
			$id = $gainItem['item_id'];
			
			if ($cid == 136332) {
				$index = 0;
			} else if ($cid == 137832) {
				$index = 1;
			} else if ($cid == 137032) {
				$index = 2;
			} else {
				$index = 3;
			}
			
			//建筑拼图列表
			$buildings = Hapyfish2_Island_Event_Cache_SpringFestival::getFragmentData($uid);
			
			foreach ($buildings as $key => $building) {
				if ($key == $index) {
					$needData = $building;
					break;
				}
			}
 			
			if (!$needData) {
	            $result['content'] = 'serverWord_101';
	            $resultVo = array('result' => $result);
	            return $resultVo;
			}
			
			//增加碎片
			foreach ($buildings as $bkey => $bdata) {
				if ($bkey == $index) {
					foreach ($bdata['puzzles'] as $pkeys => $pvalues) {
						if ($id == $pkeys) {
							$buildings[$bkey]['puzzles'][$pkeys] += $gainItem['item_num'];
							break;
						}
					}
				}
			}
				
			//更新碎片和领取状态
			Hapyfish2_Island_Event_Cache_SpringFestival::renewFragmentData($uid, $buildings);
			
			//发送Feed
        	$minifeed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => self::TXT007 . $gainItem['item_name'] . 'x' . $gainItem['item_num']),
							'type' => 3,
							'create_time' => $nowTime);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			
			$pingtu['index'] = (int)$id;
			$pingtu['cid'] = (int)$cid;
			$pingtu['num'] = (int)$gainItem['item_num'];
		}
        
		//扣除福袋
		$luckyBagNum -= 1;
		Hapyfish2_Island_Event_Cache_SpringFestival::renewLuckyBag($uid, $luckyBagNum);
		
		//统计开福袋
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('sfOpenLuckyBag-' . $cid, array($uid, $gainItem['item_type'], $gainItem['item_num']));
		
        $result['status'] = 1;
        
        $PTOK = count($pingtu);
        
        if ($PTOK) {
        	$resultVo = array('result' => $result, 'pingtu' => $pingtu);
        } else {
        	$resultVo = array('result' => $result, 'crystalNum' => (int)$crystal);
        }
        
        return $resultVo;
	}
	
	/**
	 * @随机取ID
	 * @param Array $aryKeys
	 * @return int
	 */
	public static function randomKeyForOdds($aryKeys)
	{	
		$tot = 0;
		$aryTmp = array();
		foreach ($aryKeys as $aryKey => $odd) {
			$tot += $odd;
			$aryTmp[$aryKey] = $tot;
		}

		$rnd = mt_rand(1, $tot);

		foreach ($aryTmp as $key => $value) {
			if ($rnd <= $value) {
				return $key;
			}
		}
	}
	
	/**
	 * @踢出饺子
	 * @param int $uid
	 * @param int $index
	 * @return Array
	 */
	public static function sfDecDumpling($uid, $index)
	{
		$result = array('status' => -1);
		
		//只有6个饺子
		if (!in_array($index, array(0, 1, 2, 3, 4, 5))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//获取饺子列表
		$dumplingList = Hapyfish2_Island_Event_Cache_SpringFestival::getDumpling($uid);
	
		//最少还有2个饺子,否则不能踢饺子
		if (count($dumplingList) <= 1) {
            $result['content'] = self::TXT012;
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//踢出饺子的价格
		$gold = 2;
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $gold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//踢出饺子
		foreach ($dumplingList as $key => $value) {
			if ($key == $index) {
				unset($dumplingList[$key]);
				break;
			}
		}
		
		$newDumplingArr = array();
		foreach ($dumplingList as $dumpling) {
			$newDumplingArr[] = $dumpling;
		}
					
		//更新cache中的饺子列表
		Hapyfish2_Island_Event_Cache_SpringFestival::renewDumpling($uid, $newDumplingArr);
		
		//统计踢出饺子
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('sfDecDumpling', array($uid));
		
		$nowTime = time();
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $gold,
						'summary' => self::TXT010 . 'x1',
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 1);

        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);

		if ($ok2) {
			try {
				Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldInfo['cost']);

				//task id 3068,task type 19
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
				if ( $checkTask['status'] == 1 ) {
					$result['finishTaskId'] = $checkTask['finishTaskId'];
				}
	        } catch (Exception $e) {}
		}
		
		$result['status'] = 1;
		$result['goldChange'] = -$gold;
		$resultVo = array('result' => $result);
		return $resultVo;
	}

	/**
	 * @吃饺子
	 * @param int $uid
	 * @return Array
	 */
	public static function sfEatDumpling($uid, $cid)
	{
		$result = array('status' => -1);
		
		//只能4个cid中的
		if (!in_array($cid, array(136332, 137832, 137032, 135832))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            return $resultVo;
		}
		
		//获取用户可以吃饺子的数量
		$dumplingData = Hapyfish2_Island_Event_Cache_SpringFestival::getDumplingNum($uid);
		$dumplingNum = $dumplingData['num'];
		
		//没有次数,不能吃饺子
		if ($dumplingNum <= 0) {
			$result['content'] = self::TXT011;
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//获取饺子列表
		$aryItem = Hapyfish2_Island_Event_Cache_SpringFestival::getDumpling($uid);
	
		//get random item key
		$aryRandOdds = array();
		foreach ($aryItem as $itemkey => $item) {
			$aryRandOdds[$itemkey + 1] = $item['odds'];
		}
		
		$gainKeyOr = self::randomKeyForOdds($aryRandOdds);

		$gainKey = $gainKeyOr - 1;
        foreach ($aryItem as $key => $items) {
        	$keys[] = $key;
        }
   
        //产生的饺子index
        if (!in_array($gainKey, $keys)) {
			$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
        }
       
 		foreach ($aryItem as $aryKey => $aryData) {
 			if ($gainKey == $aryKey) {
 				$gainData = $aryData;
 				break;
 			}
 		}
      
 		$coin = 0;
 		$gold = 0;
 		$nowTime = time();
 		
 		//发放奖励
 		$compensation = new Hapyfish2_Island_Bll_Compensation();
 		
		//金币
		if ($aryData['type'] == 1) {
			$compensation->setCoin($aryData['num']);
			$coin += $aryData['num'];
		} else  if ($aryData['type'] == 2) {
			//宝石
			$compensation->setGold($aryData['num']);
			$gold += $aryData['num'];
		} else if ($aryData['type'] == 3) {
			//道具
			$compensation->setItem($aryData['cid'], $aryData['num']);
		} else if ($aryData['type'] == 4) {
			//获得用户水晶数量
			$curCrystalNum = Hapyfish2_Island_Event_Cache_SpringFestival::getCurCrystalNum($uid);
			
			$curCrystalNum += $aryData['num'];
			Hapyfish2_Island_Event_Cache_SpringFestival::renewCurCrystalNum($uid, $curCrystalNum);
			
			//发送Feed
        	$minifeed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => self::TXT007 . $aryData['num'] . $aryData['item_name']),
							'type' => 3,
							'create_time' => $nowTime);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
  			
			$ok = true;
		} else if ($aryData['type'] == 5) {
			//碎片
			if ($cid == 136332) {
				$index = 0;
			} else if ($cid == 137832) {
				$index = 1;
			} else if ($cid == 137032) {
				$index = 2;
			} else {
				$index = 3;
			}
			
			//建筑拼图列表
			$buildings = Hapyfish2_Island_Event_Cache_SpringFestival::getFragmentData($uid);
				
			//增加碎片
			foreach ($buildings as $bkey => $bdata) {
				if ($bkey == $index) {
					//更改领取状态
					foreach ($bdata['puzzles'] as $pkeys => $pvalues) {
						if ($aryData['id'] == $pkeys) {
							$buildings[$bkey]['puzzles'][$pkeys] = ($pvalues + $aryData['num']);
							break;
						}
					}
				}
			}
			
			//更新碎片和领取状态
			Hapyfish2_Island_Event_Cache_SpringFestival::renewFragmentData($uid, $buildings);
			
			//发送Feed
        	$minifeed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => self::TXT007 . $aryData['item_name'] . 'x' . $aryData['num']),
							'type' => 3,
							'create_time' => $nowTime);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			
			$ok = true;
		}
		
		if (!$ok) {
			$ok = $compensation->sendOne($uid, self::TXT007);
		}
        
 		//删除饺子缓存
 		Hapyfish2_Island_Event_Cache_SpringFestival::delDumplingList($uid);
 		
		//获取饺子列表
		$aryItem = Hapyfish2_Island_Event_Cache_SpringFestival::getDumpling($uid);
		foreach ($aryItem as $aryItemkey => $aryItemlist) {
			unset($aryItem[$aryItemkey]['id']);
			unset($aryItem[$aryItemkey]['odds']);
			unset($aryItem[$aryItemkey]['item_name']);
		}

		$dumplingNum -= 1;

		//计算可以吃饺子的数量
		Hapyfish2_Island_Event_Cache_SpringFestival::decDumplingNum($uid, $dumplingNum);
		
		$result['status'] = 1;
		
		if ($aryData['type'] == 1) {
			$result['coinChange'] = $coin;
		} else if ($aryData['type'] == 2) {
			$result['goldChange'] = $gold;
		} else if ($aryData['type'] == 3) {
			$result['itemBoxChange'] = true;
		}

		$resultVo = array('result' => $result, 'jiaoziIndex' => $gainKey, 'newJiaozis' => $aryItem);
		return $resultVo;   
	}
	
}