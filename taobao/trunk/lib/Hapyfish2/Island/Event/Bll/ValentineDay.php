<?php
require_once(CONFIG_DIR . '/language.php');
/**
 * @Event ValentineDay
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/02/06    zhangli
*/
class Hapyfish2_Island_Event_Bll_ValentineDay
{
	const TXT001 = '红玫瑰';
	const TXT002 = 'ValentinehongRose1';
	const TXT003 = '白玫瑰';
	const TXT004 = 'ValentinebaiRose1';
	const TXT005 = '蓝玫瑰';
	const TXT006 = 'ValentinelanRose1';
	const TXT007 = '红粉佳人';
	const TXT008 = 'ValentinehongRose2';
	const TXT009 = '白雪公主';
	const TXT010 = 'ValentinebaiRose2';
	const TXT011 = '蓝色妖姬';
	const TXT012 = 'ValentinelanRose2';
	const TXT013 = '恭喜你获得:';
	const TXT014 = '玫瑰产量已经达到上限,不能再使用肥料';
	const TXT015 = '使用肥料';
	const TXT016 = '每人只能用于5座玫瑰花园';
	const TXT017 = '恭喜你获得:';
	const TXT018 = '你今天已经帮过忙了,还是明天再来吧~';
	const TXT019 = '帮助好友种植玫瑰';
	const TXT020 = '高级肥料不足';
	const TXT021 = '当前肥料只能增加玫瑰花的产量至';
	const TXT022 = '请选择其他肥料';
	const TXT023 = '当前玫瑰的产量已经达到100,不需要使用肥料';
	const TXT024 = '情人节加速收获玫瑰';
	
	/**
	 * @初始化
	 * @param int $uid
	 * @return array
	 */
	public static function valentineDayInit($uid)
	{
		$result = array('status' => -1);
		
		//高级肥料的数量
		$card = 141541;
		$fertilizerNum = 0;
		
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);

		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ($cid == $card) {
					$fertilizerNum = $item['count'];
					break;
				}
			}
		}
		
		//花园信息
		$gardenList = Hapyfish2_Island_Event_Cache_ValentineDay::getGardenList($uid);
		
		foreach ($gardenList as $key => $garden) {
			unset($gardenList[$key]['maxYield']);
			unset($gardenList[$key]['gainTime']);
		}
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'seniorFertilizerNum' => (int)$fertilizerNum, 'gardenList' => $gardenList);
		
		return $resultVo;
	}
	
	/**
	 * @玫瑰的数据
	 * @param int $uid
	 * @return array
	 */
	public static function valentineDayRoseInfo($uid)
	{
		$result = array('status' => -1);
		
		//玫瑰信息
		$roseList = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseList($uid);
		
		$roseListArr = array();
		foreach ($roseList as $key => $rose) {
			if ($key == 'rose_1') {
				$roseListArr[0]['id'] = 1;
				$roseListArr[0]['name'] = self::TXT001;
				$roseListArr[0]['className'] = self::TXT002;
				$roseListArr[0]['num'] = $rose;
			} else if ($key == 'rose_2') {
				$roseListArr[1]['id'] = 2;
				$roseListArr[1]['name'] = self::TXT003;
				$roseListArr[1]['className'] = self::TXT004;
				$roseListArr[1]['num'] = $rose;
			} else if ($key == 'rose_3') {
				$roseListArr[2]['id'] = 3;
				$roseListArr[2]['name'] = self::TXT005;
				$roseListArr[2]['className'] = self::TXT006;
				$roseListArr[2]['num'] = $rose;
			} else if ($key == 'rose_4') {
				$roseListArr[3]['id'] = 4;
				$roseListArr[3]['name'] = self::TXT007;
				$roseListArr[3]['className'] = self::TXT008;
				$roseListArr[3]['num'] = $rose;
			} else if ($key == 'rose_5') {
				$roseListArr[4]['id'] = 5;
				$roseListArr[4]['name'] = self::TXT009;
				$roseListArr[4]['className'] = self::TXT010;
				$roseListArr[4]['num'] = $rose;
			} else {
				$roseListArr[5]['id'] = 6;
				$roseListArr[5]['name'] = self::TXT011;
				$roseListArr[5]['className'] = self::TXT012;
				$roseListArr[5]['num'] = $rose;
			}
		}
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'roseList' => $roseListArr);
		
		return $resultVo;
	}
	
	/**
	 * @种玫瑰
	 * @param int $uid
	 * @param int $buildingId
	 * @param int $flowerId
	 * @return array
	 */
	public static function valentineDayPlant($uid, $buildingId, $flowerId)
	{
		$result = array('status' => -1);

		//玫瑰的ID只有三种
		if (!in_array($flowerId, array(1, 2, 3))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$itemVo = $db->getItemVo($uid, 1409);
		} catch (Exception $e) {}
	 	
		$itemIdArr = array();
		if ($itemVo) {
			foreach ($itemVo as $item) {
				$itemIdArr[] = $item['id'] . $item['item_type'];
				$cids[] = $item['cid'];
			}
		}

		//查询这个建筑是否存在
		if (!in_array($buildingId, $itemIdArr)) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
	
		//判断这个实例id是否是玫瑰花园建筑
		foreach ($cids as $cid) {
			if ($cid != 140932) {
				$result['content'] = 'serverWord_101';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//花园信息
		$gardenList = Hapyfish2_Island_Event_Cache_ValentineDay::getGardenList($uid);
		
		$nowGarden = array();
		
		//取得要种植玫瑰的花园信息
		foreach ($gardenList as $key => $garden) {
			if ($buildingId == $garden['buildingId']) {
				$nowGarden = $garden;
				break;
			}
		}
	
		//花园建筑不存在
		if (!$nowGarden) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
	
		//已经种植或者没有收获
		if ($nowGarden['yield'] > 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$nowTime = time();
		
		//种植玫瑰后的数据
		$nowGarden['completeTime'] = $nowTime + 60 * 60 * 3;
		$nowGarden['yield'] = 30;
		$nowGarden['flowerId'] = $flowerId;
		$nowGarden['maxYield'] = 30;
		$nowGarden['gainTime'] = $nowTime;
		
		foreach ($gardenList as $gardenKey => $gardenData) {
			if ($buildingId == $gardenData['buildingId']) {
				$gardenList[$gardenKey] = $nowGarden;
				break;
			}
		}
		
		//更新花园信息
		Hapyfish2_Island_Event_Cache_ValentineDay::renewGardenList($uid, $gardenList);
		
		$newsGarden = array('buildingId' => $nowGarden['buildingId'],
							'completeTime' => $nowGarden['completeTime'],
							'yield' => $nowGarden['yield'],
							'flowerId' => $nowGarden['flowerId']);
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'garden' => $newsGarden);
		
		return $resultVo;
	}
	
	/**
	 * 
	 * @使用肥料
	 * @param int $uid
	 * @param int $buildingId
	 * @param int $fertilizeType
	 * @return array
	 */
	public static function valentineDayFertilize($uid, $buildingId, $fertilizeType)
	{
		$result = array('status' => -1);
		
		//肥料的ID只有三种
		if (!in_array($fertilizeType, array(0, 1, 2))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//高级肥料的数量
		$card = 141541;
		$fertilizerNum = 0;
		
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);

		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ($cid == $card) {
					$fertilizerNum = $item['count'];
					break;
				}
			}
		}
		
		//使用高级肥料
		if ($fertilizeType == 2) {			
			//肥料不足
			if ($fertilizerNum <= 0) {
				$result['content'] = self::TXT020;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$itemVo = $db->getItemVo($uid, 1409);
		} catch (Exception $e) {}
	 	
		$itemIdArr = array();
		if ($itemVo) {
			foreach ($itemVo as $item) {
				$itemIdArr[] = $item['id'] . $item['item_type'];
				$cids[] = $item['cid'];
			}
		}
	
		//查询这个建筑是否存在
		if (!in_array($buildingId, $itemIdArr)) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
	
		//判断这个实例id是否是玫瑰花园建筑
		foreach ($cids as $cid) {
			if ($cid != 140932) {
				$result['content'] = 'serverWord_101';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//花园信息
		$gardenList = Hapyfish2_Island_Event_Cache_ValentineDay::getGardenList($uid);
		
		$nowGarden = array();
		
		//取得要种植玫瑰的花园信息
		foreach ($gardenList as $key => $garden) {
			if ($buildingId == $garden['buildingId']) {
				$nowGarden = $garden;
				break;
			}
		}
	
		//花园建筑不存在
		if (!$nowGarden) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
	
		//已经种植或者没有收获
		if (($nowGarden['completeTime'] + $nowGarden['yield']) <= 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$moneyType = 0;
		
		//产量和上限到了100,不能再使用肥料
		if (($nowGarden['maxYield'] >= 100) && ($nowGarden['yield'] >= 100)) {
			$result['content'] = self::TXT023;
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//随机取得增加的产量
		if ($fertilizeType == 0) {
			if (($nowGarden['maxYield'] >= 60) && ($nowGarden['yield'] >= 60)) {
				$result['content'] = self::TXT021 . '60,' . self::TXT022;
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
			
			$addYield = rand(-2, 3);
			$maxYidld = 60;
			$coin = 5000;
			$moneyType = 1;
		} else if ($fertilizeType == 1) {
			if (($nowGarden['maxYield'] >= 80) && ($nowGarden['yield'] >= 80)) {
				$result['content'] = self::TXT021 . '80,' . self::TXT022;
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
			
			$addYield = rand(1, 5);
			$maxYidld = 80;
			$gold = 1;
			$moneyType = 2;
		} else {			
			$addYield = 5;
			$maxYidld = 100;
		}
		
		//判断钱够不够
		if ($moneyType == 1) {
			//获得用户coin
			$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
			
			//金币不足
			if($userCoin < $coin) {
				$result['content'] = 'serverWord_137';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
		} else if ($moneyType == 2) {
	    	//获得用户gold
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			
			//宝石不足
			if ($userGold < $gold) {
	    		$result['content'] = 'serverWord_140';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
		}
		
		//玫瑰产量已经达到上限
		if ($nowGarden['maxYield'] < $maxYidld) {
			$nowGarden['maxYield'] = $maxYidld;
		}

		if ($fertilizeType == 0) {
			if ($nowGarden['yield'] >= 60) {
				$addYield = 0;
			}
		} else if ($fertilizeType == 1) {
			if ($nowGarden['yield'] >= 80) {
				$addYield = 0;
			}
		}
		
		//使用肥料后的数据
		$nowGarden['yield'] += $addYield;

		//第一种肥料下限为10
		if ($fertilizeType == 0) {
			if ($nowGarden['yield'] < 10) {
				$nowGarden['yield'] = 10;
			}
		}
		
		//产量不能超过上限
		if ($nowGarden['yield'] >= $nowGarden['maxYield']) {
			$nowGarden['yield'] = $nowGarden['maxYield'];
		}

		foreach ($gardenList as $gardenKey => $gardenData) {
			if ($buildingId == $gardenData['buildingId']) {
				$gardenList[$gardenKey] = $nowGarden;
				break;
			}
		}
	
		//更新花园信息
		Hapyfish2_Island_Event_Cache_ValentineDay::renewGardenList($uid, $gardenList);
		
		$nowTime = time();
		
		//扣钱
		if ($moneyType == 1) {
			$ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $coin);
	
			if ($ok2) {
				//add log
				$summary = self::TXT015 . 'x1';
				Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $coin, $summary, $nowTime);
		        try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $coin);

					//task id 3012,task type 14
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3001);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
		        } catch (Exception $e) {}
			}
		} else if ($moneyType == 2) {			
			//获取用户等级
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			
			//扣除宝石
			$goldInfo = array('uid' => $uid,
							'cost' => $gold,
							'summary' => self::TXT015 . 'x1',
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
		} else {
			//扣除高级肥料的数量
			$decCard = Hapyfish2_Island_HFC_Card::useUserCard($uid, $card, 1);
			
			if (!$decCard) {
				$result['content'] = 'serverWord_110';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
			
			$fertilizerNum -= 1;
		}
		
		$report = array($uid);
		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('valDay_useFer_' . $fertilizeType, $report);
		
		$newsGarden = array('buildingId' => $nowGarden['buildingId'],
							'completeTime' => $nowGarden['completeTime'],
							'yield' => $nowGarden['yield'],
							'flowerId' => $nowGarden['flowerId']);
		
		$result['status'] = 1;
		$result['itemBoxChange'] = true;
		
		if ($gold > 0) {
			$result['goldChange'] = -$gold;
		}
		
		if ($coin) {
			$result['coinChange'] = -$coin;
		}
		
		$resultVo = array('result' => $result, 'seniorFertilizerNum' => $fertilizerNum, 'garden' => $newsGarden);
		
		return $resultVo;
	}
	
	/**
	 * 
	 * @收获玫瑰
	 * @param int $uid
	 * @param int $buildingId
	 * @return array
	 */
	public static function valentineDayGain($uid, $buildingId)
	{
		$result = array('status' => -1);
		
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$itemVo = $db->getItemVo($uid, 1409);
		} catch (Exception $e) {}
	 	
		$itemIdArr = array();
		if ($itemVo) {
			foreach ($itemVo as $item) {
				$itemIdArr[] = $item['id'] . $item['item_type'];
				$cids[] = $item['cid'];
			}
		}
		
		//查询这个建筑是否存在
		if (!in_array($buildingId, $itemIdArr)) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//判断这个实例id是否是玫瑰花园建筑
		foreach ($cids as $cid) {
			if ($cid != 140932) {
				$result['content'] = 'serverWord_101';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//花园信息
		$gardenList = Hapyfish2_Island_Event_Cache_ValentineDay::getGardenList($uid);
		
		$nowGarden = array();
		
		//取得要种植玫瑰的花园信息
		foreach ($gardenList as $key => $garden) {
			if ($buildingId == $garden['buildingId']) {
				$nowGarden = $garden;
				break;
			}
		}
		
		//花园建筑不存在
		if (!$nowGarden) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//没有产量
		if ($nowGarden['yield'] <= 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$nowTime = time();
		
		//没到收货时间
		if (($nowGarden['completeTime'] - $nowTime) > 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//判断种植的时间点
		$gainHour = date('G', $nowGarden['gainTime']);
		
		//在20~22点之间种植可以加成20%产量
		if (in_array($gainHour, array(20, 21, 22, 23))) {
			$nowGarden['yield'] *= 1.2;
		}
		
		if ($nowGarden['flowerId'] == 1) {
			//产量多于80是高级玫瑰
			if ($nowGarden['yield'] >= 80) {
				$addFlowerId = 'rose_4';
				$roseId = 4;
				$roseName = self::TXT007;
				$className = self::TXT008;
			} else {
				$addFlowerId = 'rose_1';
				$roseId = 1;
				$roseName = self::TXT001;
				$className = self::TXT002;
			}
		} else if ($nowGarden['flowerId'] == 2) {
			if ($nowGarden['yield'] >= 80) {
				$addFlowerId = 'rose_5';
				$roseId = 5;
				$roseName = self::TXT009;
				$className = self::TXT010;
			} else {
				$addFlowerId = 'rose_2';
				$roseId = 2;
				$roseName = self::TXT003;
				$className = self::TXT004;
			}
		} else if ($nowGarden['flowerId'] == 3) {
			if ($nowGarden['yield'] >= 80) {
				$addFlowerId = 'rose_6';
				$roseId = 6;
				$roseName = self::TXT011;
				$className = self::TXT012;
			} else {
				$addFlowerId = 'rose_3';
				$roseId = 3;
				$roseName = self::TXT005;
				$className = self::TXT006;
			}
		}
		
		//增加的产量
		$addRoseNum = floor($nowGarden['yield'] / 10);
		
		//玫瑰信息
		$roseList = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseList($uid);
		
		$roseNum = 0;
		$roseListArr = array();
		foreach ($roseList as $key => $rose) {
			if ($key == $addFlowerId) {
				$roseNum = $roseList[$key] + $addRoseNum;
				$roseList[$key] += $addRoseNum;
				break;
			}
		}
		
		//更新玫瑰数量
		Hapyfish2_Island_Event_Cache_ValentineDay::renewRoseList($uid, $roseList);
		
		//返回数据
		$roseArr = array('id' => $roseId, 'name' => $roseName, 'className' => $className, 'num' => $roseNum);
		
		//收获玫瑰
		$nowGarden['completeTime'] = 0;
		$nowGarden['yield'] = 0;
		$nowGarden['maxYield'] = 0;
		$nowGarden['gainTime'] = 0;
		
		foreach ($gardenList as $gardenKey => $gardenData) {
			if ($buildingId == $gardenData['buildingId']) {
				$gardenList[$gardenKey] = $nowGarden;
				break;
			}
		}
		
		//更新花园信息
		Hapyfish2_Island_Event_Cache_ValentineDay::renewGardenList($uid, $gardenList);
		
		$gardenArr = array('buildingId' => $nowGarden['buildingId'],
						'completeTime' => $nowGarden['completeTime'],
						'yield'	=> $nowGarden['yield'],
						'flowerId' => 0);
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'garden' => $gardenArr, 'rose' => $roseArr);
		
		return $resultVo;
	}
	
	/**
	 * 
	 * @加速收获玫瑰
	 * @param int $uid
	 * @param int $buildingId
	 * @return array
	 */
	public static function valentineDayGainPass($uid, $buildingId)
	{
		$result = array('status' => -1);
		
		$gold = 5;
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($userGold < $gold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$itemVo = $db->getItemVo($uid, 1409);
		} catch (Exception $e) {}
	 	
		$itemIdArr = array();
		if ($itemVo) {
			foreach ($itemVo as $item) {
				$itemIdArr[] = $item['id'] . $item['item_type'];
				$cids[] = $item['cid'];
			}
		}
		
		//查询这个建筑是否存在
		if (!in_array($buildingId, $itemIdArr)) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//判断这个实例id是否是玫瑰花园建筑
		foreach ($cids as $cid) {
			if ($cid != 140932) {
				$result['content'] = 'serverWord_101';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//花园信息
		$gardenList = Hapyfish2_Island_Event_Cache_ValentineDay::getGardenList($uid);
		
		$nowGarden = array();
		
		//取得要种植玫瑰的花园信息
		foreach ($gardenList as $key => $garden) {
			if ($buildingId == $garden['buildingId']) {
				$nowGarden = $garden;
				break;
			}
		}
		
		//花园建筑不存在
		if (!$nowGarden) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//没有产量
		if ($nowGarden['yield'] <= 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$nowTime = time();
		
		//没到收货时间
		if ($nowGarden['completeTime'] <= 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//更改玫瑰成熟时间
		$nowGarden['completeTime'] = $nowTime - 5;
		
		foreach ($gardenList as $gardenKey => $gardenData) {
			if ($buildingId == $gardenData['buildingId']) {
				$gardenList[$gardenKey] = $nowGarden;
				break;
			}
		}
		
		//更新花园信息
		Hapyfish2_Island_Event_Cache_ValentineDay::renewGardenList($uid, $gardenList);
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $gold,
						'summary' => self::TXT024,
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => 0,
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
		
		$report = array($uid);
		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('valDay-GainPass-', $report);
		
		$gardenArr = array('buildingId' => $nowGarden['buildingId'],
						'completeTime' => $nowGarden['completeTime'],
						'yield'	=> $nowGarden['yield'],
						'flowerId' => $nowGarden['flowerId']);
		
		$result['status'] = 1;
		$result['goldChange'] = -$gold;
		$resultVo = array('result' => $result, 'garden' => $gardenArr);
		
		return $resultVo;
	}
	
	/**
	 * 
	 * @购买玫瑰花园
	 * @param int $uid
	 * @return array
	 */
	public static function valentineDayBuyGarden($uid)
	{
		$result = array('status' => -1);
		
		$cid = 140932;
		try {
			$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$num = $db->getOneNum($uid, $cid);
		} catch (Exception $e) {}
		
		//每人只能有5个花园
		if ($num >= 5) {
			$result['content'] = self::TXT016;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$coin = 0;
		$gold = 0;
		$moneyType = 2;
		
		switch ($num) {
			case 0:
				$moneyType = 1;
				$coin = 200000;	
			break;
			case 1:
				$gold = 10;	
			break;
			case 2:
				$gold = 20;	
			break;
			case 3:
				$gold = 30;	
			break;
			case 4:
				$gold = 40;	
			break;
		}
		
		//判断钱够不够
		if ($moneyType == 1) {
			//获得用户coin
			$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
			
			//金币不足
			if($userCoin < $coin) {
				$result['content'] = 'serverWord_137';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
		} else if ($moneyType == 2) {
	    	//获得用户gold
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			
			//宝石不足
			if ($userGold < $gold) {
	    		$result['content'] = 'serverWord_140';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
		}
		
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();
	
		$compensation->setItem($cid, 1);
		$ok = $compensation->sendOne($uid, self::TXT017);
		
		if ($ok) {
			$nowTime = time();
			$plant = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
			
			//扣钱
			if ($moneyType == 1) {
				$ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $coin);
				$price = $coin;
				
				if ($ok2) {
					//add log
					$summary = LANG_PLATFORM_BASE_TXT_13 . $plant['name'];
					Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $coin, $summary, $nowTime);
			        try {
						Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $coin);
	
						//task id 3012,task type 14
						$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3001);
						if ( $checkTask['status'] == 1 ) {
							$result['finishTaskId'] = $checkTask['finishTaskId'];
						}
			        } catch (Exception $e) {}
				}
			} else if ($moneyType == 2) {
				$price = $gold;			
				//获取用户等级
				$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
				$userLevel = $userLevelInfo['level'];
				
				//扣除宝石
				$goldInfo = array('uid' => $uid,
								'cost' => $gold,
								'summary' => LANG_PLATFORM_BASE_TXT_13 . $plant['name'],
								'user_level' => $userLevel,
								'create_time' => $nowTime,
								'cid' => $cid,
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
			}
			
			//report log
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('203', array($uid, 1, $cid, $moneyType, $price));
		}
		
		$result['status'] = 1;
		$result['itemBoxChange'] = true;
		
		if ($gold > 0) {
			$result['goldChange'] = -$gold;
		}
		
		if ($coin > 0) {
			$result['coinChange'] = -$coin;
		}
		
		$resultVo = array('result' => $result);
		
		return $resultVo;
	}
	
	/**
	 * 
	 * @兑换初始化
	 * @param int $uid
	 * @return array
	 */
	public static function valentineDayExchInit($uid)
	{
		$result = array('status' => -1);
		
		//花园的数量
		$buildingNum = 0;
		
		//花园信息
		$gardenList = Hapyfish2_Island_Event_Cache_ValentineDay::getGardenList($uid);
		
		foreach ($gardenList as $key => $garden) {
			if ($garden['buildingId'] > 0) {
				$buildingNum += 1;
			}
		}
		
		//玫瑰信息
		$roseList = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseList($uid);
		
		$roseListArr = array();
		foreach ($roseList as $key => $rose) {
			if ($key == 'rose_1') {
				$roseListArr[0]['id'] = 1;
				$roseListArr[0]['num'] = $rose;
			} else if ($key == 'rose_2') {
				$roseListArr[1]['id'] = 2;
				$roseListArr[1]['num'] = $rose;
			} else if ($key == 'rose_3') {
				$roseListArr[2]['id'] = 3;
				$roseListArr[2]['num'] = $rose;
			} else if ($key == 'rose_4') {
				$roseListArr[3]['id'] = 4;
				$roseListArr[3]['num'] = $rose;
			} else if ($key == 'rose_5') {
				$roseListArr[4]['id'] = 5;
				$roseListArr[4]['num'] = $rose;
			} else {
				$roseListArr[5]['id'] = 6;
				$roseListArr[5]['num'] = $rose;
			}
		}
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'buildingNum' => $buildingNum, 'valentineRoseInitVo' => $roseListArr);
		
		return $resultVo;
	}
	
	/**
	 * 兑换玫瑰
	 * 
	 * @param $uid
	 * @param $gid，兑换公式id
	 */
	public static function changeRose($uid, $gid)
	{
        $resultVo = array('resultVo' => array('status' => -1));
        
        //获取兑换公式信息
        $groupInfo = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseGroupById($gid);
        if ( !$groupInfo ) {
            $resultVo['resultVo']['content'] = 'serverWord_101';
            return $resultVo;
        }
        
        //get user rose info
        //$userCollection = Hapyfish2_Island_Cache_SuperVisitor::getUserCollection($uid);
        $userRose = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseList($uid);
        $userCollection = array('1' => $userRose['rose_1'],
                                '2' => $userRose['rose_2'],
                                '3' => $userRose['rose_3'],
                                '4' => $userRose['rose_4'],
                                '5' => $userRose['rose_5'],
                                '6' => $userRose['rose_6']);
        
        //收集物的cid和数量，对应收集物数据例: [[cid,num],[11,1],[12,1],[35,1],[13,10]]
        $needs = json_decode($groupInfo['needs']);
        $varItem = array();
        foreach ( $needs as $var ) {
            $cid = $var[0];
            $num = $var[1];
            if ( $userCollection[$cid] < $num ) {
                $resultVo['resultVo']['content'] = 'serverWord_204';
                return $resultVo;
            }
            $userCollection[$cid] -= $num;
            //$userCollection[$cid]['update'] = 1;
        }
        
        //get award info
        $awardArray = self::transformData($groupInfo['awards']);
        $awardCoin = $awardArray['coin'];
        $awardGold = $awardArray['gold'];
        $awardStar = $awardArray['star'];
        $awardItem = $awardArray['item'];
        $awardExp  = $awardArray['exp'];
        
        $nowTime = time();
        try {
            //发放奖励  
            $feedTitle = self::TXT013;     
            if ( $awardCoin > 0 ) {
                Hapyfish2_Island_HFC_User::incUserCoin($uid, $awardCoin);
                $feedTitle .= $awardCoin.LANG_PLATFORM_BASE_TXT_01.' ';
                $resultVo['resultVo']['coinChange'] = $awardCoin;
            }
            if ( $awardGold > 0 ) {
                //type = 7,玫瑰花兑换
                $goldInfo = array('gold' => $awardGold, 'type' => 7, 'time' => $nowTime);
                Hapyfish2_Island_Bll_Gold::add($uid, $goldInfo);
                $feedTitle .= $awardGold.LANG_PLATFORM_BASE_TXT_02.' ';
                $resultVo['resultVo']['goldChange'] = $awardGold;
            }
            if ( $awardExp > 0 ) {
                Hapyfish2_Island_HFC_User::incUserExp($uid, $awardExp);
                $feedTitle .= $awardExp.LANG_PLATFORM_BASE_TXT_04.' ';
                $resultVo['resultVo']['expChange'] = $awardExp;
                try {
                    //check level up
                    $levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
                    $resultVo['resultVo']['levelUp'] = $levelUp['levelUp'];
                } catch (Exception $e) {
                }
            }
            if ( $awardStar > 0 ) {
                Hapyfish2_Island_Bll_StarFish::add($uid, $awardStar, LANG_PLATFORM_BASE_TXT_17, $nowTime);
                $feedTitle .= $awardStar.LANG_PLATFORM_BASE_TXT_18.' ';
            }
            if ( !empty($awardItem) ) {
                $bllCompensation = new Hapyfish2_Island_Bll_Compensation();             
                //道具卡和建筑
                foreach ( $awardItem as $aItem ) {
                    $cid = $aItem['cid'];
                    $num = $aItem['num'];
                    $itemType = substr($cid, -2, 1);
                    //send
                    $bllCompensation->setItem($cid, $num);
                    
                    if ( $itemType == 4 ) {
                        $cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
                        $name = $cardInfo['name'];
                    }
                    else if ( $itemType == 3 ) {
                        $cardInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
                        $name = $cardInfo['name'];
                    }
                    else if ( $itemType == 2 ) {
                        $cardInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($cid);
                        $name = $cardInfo['name'];
                    }
                    else if ( $itemType == 1 ) {
                        $cardInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($cid);
                        $name = $cardInfo['name'];
                    }
                    $feedTitle .= $name.'*'.$num.' ';
                }
                
                $bllCompensation->sendOne($uid, '', false);
                $resultVo['resultVo']['itemBoxChange'] = true;
            }

            //扣除收集物品,玫瑰花
            $newUserRose = array('rose_1' => $userCollection['1'],
                                 'rose_2' => $userCollection['2'],
                                 'rose_3' => $userCollection['3'],
                                 'rose_4' => $userCollection['4'],
                                 'rose_5' => $userCollection['5'],
                                 'rose_6' => $userCollection['6']);
            Hapyfish2_Island_Event_Cache_ValentineDay::renewRoseList($uid, $newUserRose);
            
            $resultVo['resultVo']['status'] = 1;
        } catch (Exception $e) {
            $resultVo['resultVo']['content'] = 'serverWord_110';
            return $resultVo;
        }
        
        $feed = array(
                    'uid' => $uid,
                    'template_id' => 0,
                    'actor' => 134,
                    'target' => $uid,
                    'type' => 3,
                    'title' => array('title' => $feedTitle),
                    'create_time' => time()
                );
        Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
        
        //report log,兑换物品统计
        $logger = Hapyfish2_Util_Log::getInstance();
        //uid,兑换公式id
        $logger->report('1001', array($uid, $gid));
        
        return $resultVo;
    }
    
    /**
     * transform data,data: "[[awardCid,awardType,awardNum],[0,1,50]]"
     * TYPE->1：金币 2宝石 3卡片 4建筑   不是卡片或者建筑的时候 awardCid为0
     */
    public static function transformData($data) 
    {
        $array = json_decode($data);
        $varCoin = $varGold = $varStar = $varExp = 0;
        $varItem = array();
        foreach ( $array as $var ) {
        	//[[awardCid,awardType,awardNum]]
            $cid = $var[0];
            $type = $var[1];
            $num = $var[2];
            
            //TYPE->1：金币 2宝石 3卡片 4建筑   不是卡片或者建筑的时候 awardCid为0
            if ( $type == 1 ) {
                $varCoin += $num;
            }
            elseif ( $type == 2 ) {
                $varGold += $num;
            }
            elseif ( $type == 3 ) {
                $varItem[] = array('cid' => $cid, 'num' => $num);
            }
            elseif ( $type == 4 ) {
                $varItem[] = array('cid' => $cid, 'num' => $num);
            }
            /*elseif ( $type == 5 ) {
                $varExp += $num;
            }
            elseif ( $type == 6 ) {
                $varStar += $num;
            }*/
        }
        $info = array('coin' => $varCoin,
                      'gold' => $varGold,
                      'star' => $varStar,
                      'exp'  => $varExp,
                      //'collection'  => $varCollection,
                      'item' => $varItem);
        return $info;
    }
    
    /**
     * 初始化玫瑰兑换信息
     */
    public static function initRoseChange()
    {
        $roseList = self::getRoseStaticVo();
        $roseGroups = self::getRoseGroups();
        $roseBuildingList = array();
        $roseBuildingList[] = array('id' => 1, 'price' => 200000, 'type' => 1);
        $roseBuildingList[] = array('id' => 2, 'price' => 10, 'type' => 2);
        $roseBuildingList[] = array('id' => 3, 'price' => 20, 'type' => 2);
        $roseBuildingList[] = array('id' => 4, 'price' => 30, 'type' => 2);
        $roseBuildingList[] = array('id' => 5, 'price' => 40, 'type' => 2);
        
        return array('valentineRoseStaticVo' => $roseList, 
                     'valentineExchangeGiftFormulaStaticVo' => $roseGroups,
                     'valentineExchangePriceRuleVo' => $roseBuildingList);
    }
    
    /**
     * 玫瑰静态信息
     */
    public static function getRoseStaticVo()
    {
    	$roseListArr = array();
    	/*$roseListArr = array('1' => array('id' => 1,'name' => self::TXT001,'className' => self::TXT002),
    	                     '2' => array('id' => 2,'name' => self::TXT003,'className' => self::TXT004),
                             '3' => array('id' => 3,'name' => self::TXT005,'className' => self::TXT006),
                             '4' => array('id' => 4,'name' => self::TXT007,'className' => self::TXT008),
                             '5' => array('id' => 5,'name' => self::TXT009,'className' => self::TXT010),
                             '6' => array('id' => 6,'name' => self::TXT011,'className' => self::TXT012));*/
    	$roseListArr[] = array('id' => 1,'name' => self::TXT001,'className' => self::TXT002);
    	$roseListArr[] = array('id' => 2,'name' => self::TXT003,'className' => self::TXT004);
        $roseListArr[] = array('id' => 3,'name' => self::TXT005,'className' => self::TXT006);
        $roseListArr[] = array('id' => 4,'name' => self::TXT007,'className' => self::TXT008);
        $roseListArr[] = array('id' => 5,'name' => self::TXT009,'className' => self::TXT010);
        $roseListArr[] = array('id' => 6,'name' => self::TXT011,'className' => self::TXT012);
        
        return $roseListArr;
    }

    /**
     * get rose groups,兑换公式信息
     * 
     * @return array
     */
    public static function getRoseGroups()
    {
        $info = array();
        $data = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseGroups();
        foreach ($data as $item) {
            $info[] = array(
                'id' => $item['gid'],
                'awardArray' => $item['awards'],
                'formulaArray' => $item['needs']
            );
        }
        return $info;
    }
    
}