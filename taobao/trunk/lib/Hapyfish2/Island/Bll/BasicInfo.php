<?php

class Hapyfish2_Island_Bll_BasicInfo
{
	public static function getInitVoData($v = '1.0', $compress = false)
	{
		if (!$compress) {
			return self::restore($v);
		} else {
			return self::restoreCompress($v);
		}
	}
	public static function removeDumpFile($v = '1.0', $compress = false)
	{
	    $file = TEMP_DIR . '/initvo.' . $v . '.cache';
	    if ($compress) {
	        $file .= '.zip';
	    }
	    if (is_file($file)) {
            $rst = @unlink($file);
	    }
	    return $rst;
	}

	public static function dump($v = '1.0', $compress = false)
	{
		$resultInitVo = self::getInitVo();
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		$data = json_encode($resultInitVo);
		if ($compress) {
			$data = gzcompress($data, 9);
			$file .= '.zip';
		}

		file_put_contents($file, $data);
		return $data;
	}

	public static function restore($v = '1.0')
	{
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($v);
		}
	}

	public static function restoreCompress($v = '1.0')
	{
		$file = TEMP_DIR . '/initvo.' . $v . '.cache.zip';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($v, true);
		}
	}

	public static function getInitVo()
	{
        $resultInitVo = array();

        $backgroundList = self::getBackgroundList();
        $buildingList = self::getBuildingList();
        $plantList = self::getPlantList();
        $cardList = self::getCardList();
        $levelList = self::getLevelList();
        $levelBigGiftList = self::getLevelBigGiftList();
        $islandUpgradeList = self::getIslandUpgradeList();
        $compoundList = self::getCompoundList();

        //get task list
        $dailyTask = self::getDailyTaskList();
        $buildTask = self::getBuildTaskList();
        $achievementTask = self::getAchievementTaskList();
        $taskList = array_merge($dailyTask, $buildTask, $achievementTask);
        $titleList = self::getTitleList();
		$resolve = Hapyfish2_Island_Bll_Compound::initResolve();
        $resultInitVo['itemClass'] = array_merge($cardList, $backgroundList, $buildingList, $plantList, $compoundList);
        $resultInitVo['boatClass'] = self::getBoatClass();
        $resultInitVo['levelClass'] = $levelList;
        $resultInitVo['taskClass'] = $taskList;
        $resultInitVo['titleClass'] = $titleList;
        $resultInitVo['helpExpList'] = array(50, 100, 200, 300, 400, 500, 600);
        $resultInitVo['fourIslandStaticInfo'] = self::getNewIslandVo();
        $resultInitVo['islandUpgradeInfo'] = $islandUpgradeList;
        $resultInitVo['levelBigGiftList'] = $levelBigGiftList;
		$resultInitVo['demandList'] = array(261,5,361,5,861,1);
		$resultInitVo['notBuyList'] = array(1161);
		$resultInitVo['resolveMap'] = $resolve;
        return $resultInitVo;
	}

	public static function getNewIslandVo()
	{
		$newIslandVo = array('IslandName1' => '快乐岛',
							 'IslandName2' => '童话王国',
							 'IslandName3' => '失落世界',
							 'IslandName4' => '游乐场',
							 'openIsland2Coin' => 2000000,
							 'openIsland2Gem' => 200,
							 'openIsland3Gem' => 500,
							 'openIsland4Gem' => 800,
							 'openIsland2HZGem' => 200,
							 'openIsland3HZGem' => 500,
							 'openIsland4HZGem' => 800,
							 'openIsland2Level' => 15,
							 'openIsland3Level' => 25,
							 'openIsland4Level' => 40);
		return $newIslandVo;
	}

	public static function getBackgroundList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['bgid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'price' => $item['price'],
				'priceType' => $item['price_type'],
				'salePrice' => $item['sale_price'],
				'type' => $item['item_type'],
				'needLevel' => $item['need_level'],
				'addPraise' => $item['add_praise'],
				'isNew' => $item['new'],
				'cheapPrice' => $item['cheap_price'],
				'cheapStartTime' => $item['cheap_start_time'],
				'cheapEndTime' => $item['cheap_end_time']
			);
		}

		return $info;
	}

	public static function getBuildingList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'price' => $item['price'],
				'priceType' => $item['price_type'],
				'salePrice' => $item['sale_price'],
				'nodes' => $item['nodes'],
				'type' => $item['item_type'],
				'needLevel' => $item['need_level'],
				'addPraise' => $item['add_praise'],
				'isNew' => $item['new'],
				'cheapPrice' => $item['cheap_price'],
				'cheapStartTime' => $item['cheap_start_time'],
				'cheapEndTime' => $item['cheap_end_time']
			);
		}

		return $info;
	}

	public static function getPlantList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		$cdata = Hapyfish2_Island_Cache_Compound::getUpdateConfig();
		foreach ($data as $item) {
			$list = array(
				'cid' => $item['cid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'price' => $item['price'],
				'priceType' => $item['price_type'],
				'salePrice' => $item['sale_price'],
				'nodes' => $item['nodes'],
				'type' => $item['item_type'],
				'needLevel' => $item['need_level'],
				'addPraise' => $item['add_praise'],
				'isNew' => $item['new'],
				'level' => $item['level'],
				'ticket' => $item['ticket'],
				'payTime' => $item['pay_time'],
				'safeTime' => $item['safe_time'],
				'safeCoinNum' => $item['safe_coin_num'],
				'needPraise' => $item['need_praise'],
				'nextCid' => $item['next_level_cid'],
				'cheapPrice' => $item['cheap_price'],
				'cheapStartTime' => $item['cheap_start_time'],
				'cheapEndTime' => $item['cheap_end_time'],
				'actName' => $item['act_name']
			);
			if($item['act_name']){
               $str = explode('_', $item['act_name']);
               if($str[0] == 'miracle'){
                   $mList = array();
               	   $numList = array();
               	   $paperCidList = array();
               	   $paperNumList = array();
                   $needList = json_decode($cdata[$item['cid']]['consume']);
                   foreach($needList as $m){
	                   $mList[] = $m[0];
	                   $numList[] = $m[1];
                   }
                   $list['dendureCidList'] = $mList;
                   $list['dendureNumList'] = $numList;
                   $list['content'] = $item['content'];
                   $list['endureTime'] = 3600*12;
                   if($item['level'] < 5){
                   	   $mcList = json_decode($cdata[$item['next_level_cid']]['material']);
	                   $cpriceList = json_decode($cdata[$item['next_level_cid']]['price']);
	                   $syntheticPrices[0] = $cpriceList->coin;
					   $syntheticPrices[1] = $cpriceList->gold;
	                   foreach($mcList as $m){
						   $paperCidList[] = $m[0];
						   $paperNumList[] = $m[1];
					   }
					   $list['syntheticPrices'] = $syntheticPrices;
					   $list['paperCidList'] = $paperCidList;
					   $list['paperNumList'] = $paperNumList;
					   $list['beginRatio'] = $cdata[$item['next_level_cid']]['init_rate'];
					   $list['maxRatio'] = $cdata[$item['next_level_cid']]['max_rate'];
					   $list['gemPerRatio'] = 1;
                   }
                   
                 //update by hdf add airfield Start  
                 if($str[1] == 'airfield') { 
                 	//飞行时间 
                 	$list['coolingTime'] = 1;
                 	
                 	//载客人数	
                 	$allPeopleNumArr = array(1=>20, 2=>30, 3=>40, 4=>50, 5=>70);
                 	$list['allPeopleNum'] = $allPeopleNumArr[$item['level']];
                 }
                 //End
				   
               }
			}
			$info[] = $list;
		}

		return $info;
	}

	public static function getCardList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getCardList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'content' => $item['introduce'],
				'price' => $item['price'],
				'priceType' => $item['price_type'],
				'salePrice' => $item['sale_price'],
				'type' => $item['item_type'],
				'needLevel' => $item['need_level'],
				'isNew' => $item['new'],
				'cheapPrice' => $item['cheap_price'],
				'cheapStartTime' => $item['cheap_start_time'],
				'cheapEndTime' => $item['cheap_end_time']
			);
		}

		return $info;
	}

	public static function getLevelList()
	{
		$info = array();
		$userLevelList = Hapyfish2_Island_Cache_BasicInfo::getUserLevelList();
		$islandLevelList = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelList();
		$giftLevelList = Hapyfish2_Island_Cache_BasicInfo::getGiftLevelList();

		$tmp = array();
		foreach ($islandLevelList as $item) {
			$tmp[$item['need_user_level']] = array('island_size' => $item['island_size'], 'max_visitor' => $item['max_visitor']);
		}
		$lastCount = 0;
		foreach ($userLevelList as $level => $exp) {
			$itemIdList = isset($giftLevelList[$level]['cid']) ? $giftLevelList[$level]['cid'] : '';
			$itemNumList = isset($giftLevelList[$level]['cid']) ? '1' : '';
			if ( $giftLevelList[$level]['item_id'] > 0 ) {
				$itemIdList = $itemIdList . ',' . $giftLevelList[$level]['item_id'];
				$itemNumList = '1,1';
			}
			
			$v = array(
				'level' => $level,
				'addGem' => isset($giftLevelList[$level]) ? $giftLevelList[$level]['gold'] : 0,
				'exp' => $exp,
				'itemIdList' => $itemIdList,
				'itemNumList' => $itemNumList,
				'island' => isset($tmp[$level]) ? $tmp[$level]['island_size'] : null
			);
			$addVisitor = 0;
			if (isset($tmp[$level])) {
				if ($lastCount > 0) {
					$addVisitor = $tmp[$level]['max_visitor'] - $lastCount;
				}
				$lastCount = $tmp[$level]['max_visitor'];
			}
			$v['addVisitor'] = $addVisitor;

			$info[] = $v;
		}

		return $info;
	}

	public static function getLevelBigGiftList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getStepGiftLevelList();
		foreach ($data as $item) {
			$itemIdList = explode(',', $item['item_id']);
			$itemNumList = explode(',', $item['item_num']);
			
			if ( $item['gold'] > 0 ) {
				$info[] = array(
					'level' => $item['level'],
					'coin' => $item['coin'],
					'itemIdList' => $itemIdList,
					'itemNumList' => $itemNumList,
					'gem' => $item['gold'],
					'starfish' => $item['star']
				);
			}
			else {
				$info[] = array(
					'level' => $item['level'],
					'coin' => $item['coin'],
					'itemIdList' => $itemIdList,
					'itemNumList' => $itemNumList,
					'starfish' => $item['star']
				);
			}
		}

		return $info;
	}
	
	public static function getIslandUpgradeList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'mustLevel' => $item['need_user_level'],
				'mustLevel2' => $item['need_user_level_2'],
				'mustLevel3' => $item['need_user_level_3'],
				'mustLevel4' => $item['need_user_level_4'],
				'coin' => $item['coin'],
				'fastGem' => $item['gold'],
				'size' => $item['island_size']
			);
		}
		
		return $info;	
	}
	
	public static function getDailyTaskList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getDailyTaskList();
		foreach ($data as $item) {
			$info[] = array(
				'taskClassId' => $item['id'],
				'type' => 1,
				'name' => $item['name'],
				'content' => $item['content'],
				'needType' => $item['need_field'],
				'needCid' => null,
				'needNum' => $item['need_num'],
				'level' => $item['level'],
				'unLockLevel' => $item['need_level'],
				'addCoin' => $item['coin'],
				'addExp' => $item['exp'],
				'addItemCid' => $item['cid'],
				'addItemNum' => 1,
				'addTitle' => $item['title'],
				'description' => $item['description']
			);
		}

		return $info;
	}

	public static function getBuildTaskList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getBuildTaskList();
		foreach ($data as $item) {
			$info[] = array(
				'taskClassId' => $item['id'],
				'type' => 2,
				'name' => $item['name'],
				'content' => $item['content'],
				'needType' => $item['need_field'],
				'needCid' => $item['need_cid'],
				'needNum' => $item['need_num'],
				'level' => $item['level'],
				'unLockLevel' => $item['need_level'],
				'addCoin' => $item['coin'],
				'addExp' => $item['exp'],
				'addItemCid' => $item['cid'],
				'addItemNum' => 1,
				'addTitle' => $item['title'],
				'description' => $item['description']
			);
		}

		return $info;
	}

	public static function getAchievementTaskList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getAchievementTaskList();
		$titleList = Hapyfish2_Island_Cache_BasicInfo::getTitleList();
		foreach ($data as $item) {
			if(in_array($item['id'], array(3068, 3069, 3070, 3083, 3084, 3085))) {
				continue;
			}

			$info[] = array(
				'taskClassId' => $item['id'],
				'type' => 3,
				'name' => $item['name'],
				'description' => $item['content'],
				'needType' => $item['need_field'],
				'needCid' => null,
				'needNum' => $item['need_num'],
				'level' => $item['level'],
				'unLockLevel' => $item['need_level'],
				'addCoin' => $item['coin'],
				'addExp' => $item['exp'],
				'addItemCid' => $item['cid'],
				'addItemNum' => 1,
				'addTitle' => $titleList[$item['title']],
				'nextTaskId' => $item['next_task'],
				'nextTwoTaskId' => $item['next_two_task'],
				'titleId' => $item['title'],
				'honorNum' => $item['honor'],
			);
		}

		return $info;
	}

	public static function getTitleList()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getTitleList();
		$achievementTaskList = Hapyfish2_Island_Cache_BasicInfo::getAchievementTaskList();
		$tmp = array();
		foreach ($achievementTaskList as $item) {
			$tmp[$item['title']] = array('coin' => $item['coin'], 'exp' => $item['exp'], 'gold' => $item['gold'], 'cid' => $item['cid']);
		}
		foreach ($data as $id => $name) {
			$info[] = array(
				'id' => $id,
				'name' => $name,
				'coin' => $tmp[$id]['coin'],
				'exp' => $tmp[$id]['exp'],
				'gemNum' => $tmp[$id]['gold'],
				'cardId' => $tmp[$id]['cid'],
				'cardNum' => 1
			);
		}

		return $info;
	}

	public static function getBoatClass()
	{
		$info = array();
		$data = Hapyfish2_Island_Cache_BasicInfo::getShipList();
		$shipPraiseList = Hapyfish2_Island_Cache_BasicInfo::getShipPraiseList();

		foreach ($data as $id => $item) {
			$t = array(
				'boatId' => $item['sid'],
				'level' => $item['sid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'startVisitorNum' => $item['start_visitor_num'],
				'safeVisitorNum' => $item['safe_visitor_num'],
				'waitTime' => $item['wait_time'],
				'safeTime1' => $item['safe_time_1'],
				'safeTime2' => $item['safe_time_2'],
				'coin' => $item['coin'],
				'gem' => $item['gem'],
				'needLevel' => $item['level'],
			);

			$shipPraise = $shipPraiseList[$id];
			$addVisitors = array();
			foreach ($shipPraise as $v) {
				$addVisitors[] = $v[0] . ',' . $v[1];
			}
			$t['addVisitors'] = $addVisitors;

			$info[] = $t;
		}

		return $info;
	}
	
	public static function getCompoundList()
	{
		$info = array();
		//图纸和材料
		$bdata = Hapyfish2_Island_Cache_Compound::getBasicInfo();
		//奇迹建筑需求
		$cdata = Hapyfish2_Island_Cache_Compound::getUpdateConfig();
		foreach($bdata['51'] as $k => $v){
			$syntheticPrices = array();
			$paperCidList = array();
			$paperNumList= array();
			if( isset($cdata[$v['to_cid']])){
				$mlist = json_decode($cdata[$v['to_cid']]['material']);
				$cpriceList = json_decode($cdata[$v['to_cid']]['price']);
				$syntheticPrices[0] = $cpriceList->coin;
				$syntheticPrices[1] = $cpriceList->gold;
				foreach($mlist as $m){
					$paperCidList[] = $m[0];
					$paperNumList[] = $m[1];
				}
			}
			$b = array(
				'cid' => $v['cid'],
				'name' => $v['name'],
				'className' => $v['class_name'],
				'type' 		=> $v['type'],
				'price'		=> $v['price'],
				'salePrice' => 1,
				'priceType' => $v['price_type'],
				'nextCid'    => $v['to_cid'],
				'syntheticPrices' => $syntheticPrices,
				'paperCidList' => $paperCidList,
				'paperNumList' => $paperNumList,
				'beginRatio' => $cdata[$v['to_cid']]['init_rate'],
				'maxRatio' => $cdata[$v['to_cid']]['max_rate'],
				'gemPerRatio' => 1,
				'content' =>$v['content'],
			);
			$info[] = $b;
		}
		
		foreach($bdata['61'] as $k1 => $v1){
			$m = array(
				'cid' => $v1['cid'],
				'name' => $v1['name'],
				'className' => $v1['class_name'],
				'type' 		=> $v1['type'],
				'price'		=> $v1['price'],
				'salePrice' => 1,
				'priceType' => $v1['price_type'],
				'content' =>$v1['content'],
				'getBy' => $v1['getBy']
			);
			$info[] = $m;
		}
		return $info;
	}

    /**
     * get super visitor visitor list
     * 
     * @return array
     */
    public static function getSVisitorList()
    {
        $info = array();
        $data = Hapyfish2_Island_Cache_BasicInfo::getSVisitorList();
        foreach ($data as $item) {
            $info[] = array(
                'cid' => $item['cid'],
                'name' => $item['name'],
                'bodyClass' => $item['body_class'],
                'faceClass' => $item['face_class']
            );
        }
        return $info;
    }

    /**
     * get super visitor demand list
     * 
     * @return array
     */
    public static function getSVDemandList()
    {
        $info = array();
        $data = Hapyfish2_Island_Cache_BasicInfo::getSVDemandList();
        foreach ($data as $item) {
            $info[] = array(
                'id' => $item['id'],
                'name' => $item['name'],
                'content' => $item['content'],
                'needs' => $item['needs'],
                'awards' => $item['awards']
            );
        }
        return $info;
    }
    
    /**
     * get collection list
     * 
     * @return array
     */
    public static function getCollectionList()
    {
        $info = array();
        $data = Hapyfish2_Island_Cache_BasicInfo::getCollectionList();
        foreach ($data as $item) {
            $info[] = array(
                'cid' => $item['cid'],
                'name' => $item['name'],
                'className' => $item['class_name'],
                //'groupId' => $item['group_id']
            );
        }
        return $info;
    }

    /**
     * get collection groups
     * 
     * @return array
     */
    public static function getCollectionGroups()
    {
        $info = array();
        $data = Hapyfish2_Island_Cache_BasicInfo::getCollectionGroups();
        foreach ($data as $item) {
            $info[] = array(
                'groupId' => $item['gid'],
                'needCollections' => $item['needs'],
                'awards' => $item['awards']
            );
        }
        return $info;
    }
    
    
}