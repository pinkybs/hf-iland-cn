<?php

class Hapyfish2_Island_Bll_Compound
{
	const TXT001 = '对不起，您已经有该奇迹建筑了，同类奇迹建筑每人只能有一个。';
	const TXT002 = '您没有该图纸不能合成';
	const TXT003 = '材料不足 不能合成';
	const TXT004 = '恭喜你合成：';
	const TXT005 = '合成失败';
	const TXT006 = '宝石不足';
	const TXT007 = '金币不足';
	const TXT008 = '合成奇迹建筑';
	const TXT009 = '今日分解建筑数已达上限制';
	const TXT010 = '一级回收站只能回收1级建筑';
	const TXT011 = '二级回收站只能回收1-2级建筑';
	const TXT012 = '三级回收站只能回收1-3级建筑';
	const TXT013 = '四级回收站只能回收1-4级建筑';
	const TXT014 = '只能回收建筑或装饰';
	const TXT015 = '请确认分解祭坛放在岛上或修理后在使用';
	const TXT016 = '该建筑未损坏不需修理';
	const TXT017 = '刷新黑市';
	const TXT018 = '我急需';
	const TXT019 = '帮忙送给我一个吧';
	const TXT020 = '被拆解的建筑物等级不能高于分解祭坛的等级';
	const TXT021 = '商店已刷新请关闭面板后重新打开';
	const TXT022 = '在藏宝商店购买：';
	const TXT023 = '装饰度不够不能升级';
	const TXT024 = '相同奇迹建筑（图纸）只能拥有一个。';
	const TXT025 = '每次刷新只能购买一样物品。';
	const TXT026 = '等级不足不能购买。';
	public static function initResolve()
	{
		$configData = Hapyfish2_Island_Cache_Compound::getResolveConfig();
		$newList = array();
		foreach($configData as $k => $v){
			$newK = explode("_", $k);
			$mlist = json_decode($v['material']);
			foreach($mlist as $k2 => $v2){
				if(count($newK) == 3){
					$newList['jianzhu'][$newK[0]][$newK[1]-1][] = $v2[0];
					$newList['jianzhu'][$newK[0]][$newK[1]-1][] = $v2[1];
				}else{
					$newList['zhuangshi'][$newK[0]][] = $v2[0];
					$newList['zhuangshi'][$newK[0]][] = $v2[1];
				}
				
			}
		}
		return $newList;
	}
	
	public static function supermarket($uid, $isGem)
	{
		if($isGem == 1){
			$result = self::refreshSupermarket($uid);
		}else{
			$result = self::initSupermarket($uid);
		}
		return $result;
	}
	//黑店初始化
	public static function initSupermarket($uid)
	{
		$result = array('status' => -1);
		$key = 'i:b:c:bm:m'.$uid;	
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$mdata = Hapyfish2_Island_Cache_Compound::getMarket();
		$data = $cache->get($key);
		$time = time();
		$check = self::checkStatue($uid, 'blackMarket');
		if($check['status'] == false){
			$result['content'] = self::TXT015;
			return array('result'=>$result);
		}
		if($data === false){
			$endTime = self::getEndTime($check['level'], $time);
			$data['time'] = $endTime;
			$list = array_rand($mdata, $check['level']+1);
			$data['cid'] = $list;
			$data['buy'] = false;
			$cache->set($key, $data);
		}else{
			if($time < $data['time']){
				$list = $data['cid'];
			}else{
				$endTime = self::getEndTime($check['level'], $time);
				$list = array_rand($mdata, $check['level']+1);
				$data['time'] = $endTime;
				$data['cid'] = $list;
				$data['buy'] = false;
				$cache->set($key, $data);
			}
		}
		$listTime = $data['time'] - $time;
		$nList = array();
		if($list){
			foreach($list as $v){
				$nList['cid'] = $mdata[$v]['cid'];
				$nList['priceType'] = $mdata[$v]['price_type'];
				$nList['price'] = $mdata[$v]['price'];
				$nList['state'] = 0;
				if($data['buy']){
					if($data['buy'] == $mdata[$v]['cid']){
						$nList['state'] = 1;
					}
				}
			   $newLsit[] = $nList;
			}
		}
		$result['status'] = 1;
		return array('result'=> $result, 'list' => $newLsit, 'refreshGem' => 2, 'time' => $listTime);
	}
	
	//刷新黑店
	public static function refreshSupermarket($uid)
	{
		$result = array('status' => -1);
		$key = 'i:b:c:bm:m'.$uid;	
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$mdata = Hapyfish2_Island_Cache_Compound::getMarket();
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
		$data = $cache->get($key);
		if($data === false){
			return array('result' => $result);
		}
		$time = time();
		$balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
        	if (!$balanceInfo) {
	        	$result['content'] = 'serverWord_1002';
	        	return array('result' => $result);
	        }
        $gold = $balanceInfo['balance'];
        if($gold < 2){
        	$result['content'] = self::TXT006;
        }
        $check = self::checkStatue($uid, 'blackMarket');
        if($check['status'] == false){
        	$result['content'] = self::TXT015;
        	return array('result'=> $result);
        }
		$list = array_rand($mdata, $check['level']+1);
		$endTime = self::getEndTime($check['level'], $time);
		$data['time'] = $endTime;
		$data['cid'] = $list;
		$data['buy'] = false;
		$cache->set($key, $data);
		$listTime = $data['time'] - $time;
		$nList = array();
		if($list){
			foreach($list as $v){
				$nList['cid'] = $mdata[$v]['cid'];
				$nList['priceType'] = $mdata[$v]['price_type'];
				$nList['price'] = $mdata[$v]['price'];
				$nList['state'] = 0;
			   $newLsit[] = $nList;
			}
		}
        $goldInfo = array(
					'uid' => $uid,
					'cost' => 2,
					//'summary' => '升级' . $plantInfo['name'] . '到' . $userPlant['level'] . '星',
					'summary' => self::TXT017,
					'user_level' => $userVO['level'],
					'cid' => 61,
					'num' => 1
				);
		Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		$result['status'] = 1;
		$result['goldChange'] = -2;
		return array('result'=> $result, 'list' => $newLsit, 'refreshGem' => 2, 'time' => $listTime);
	}
	public static function compound($uid, $cid, $priceType = 1, $addGe = 0)
	{
		$type = substr($cid, -2, 2);
		if($type == 51){
			$result = self::compoundbyTuzhi($uid, $cid, $priceType);
		}else{
			$result = self::compoundbyPlant($uid, $cid, $priceType, $addGe);
		}
		return $result;
	}
	
	//图纸合成奇迹建筑
	public static function compoundbyTuzhi($uid, $cid, $priceType = 1)
	{
		$result['status'] = -1; 
		$time = time();
		//用户信息
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
		//图纸和材料
		$bdata = Hapyfish2_Island_Cache_Compound::getBasicInfo();
		//奇迹建筑需求
		$cdata = Hapyfish2_Island_Cache_Compound::getUpdateConfig();
		//user 材料和图纸
		$userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
		$type = substr($cid, -2, 2);
		foreach($bdata[$type] as $k => $v){
			if($cid == $v['cid']){
				$nextCid = $v['to_cid'];
				break;
			}
		}
		//check 是否已有该建筑
		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($nextCid);
		$dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$id = $dalPlant->getOneIdByItemid($uid, $plantInfo['item_id']);
		if($id){
			$result['content'] = self::TXT001;
			return array('result' => $result);
		}
		$have = false;
		//check图纸
		if(isset($userMaB[$type])){
			foreach($userMaB[$type] as $k => $v){
				if($v['cid'] == $cid){
					$userMaB[$type][$k]['num'] = $v['num'] - 1;
					$have = true;
					break;
				}
			}
			if(!$have){
				$result['content'] = self::TXT002;
				return array('result' => $result);
			}
		} else {
			$result['content'] = self::TXT002;
			return array('result' => $result);
		}
		$gold = $userVO['gold'];
		$coin = $userVO['coin'];
		$price = json_decode($cdata[$nextCid]['price']);
		if($priceType == 1){
			if($coin < $price->coin || $price->coin == 0){
				$result['content'] = self::TXT007;
				return array('result' => $result);
			}
		}else{
			if($gold < $price->gold){
				$result['content'] = self::TXT006;
				return array('result' => $result);
			}
		}
		
		//check 材料
		if(isset($userMaB['61'])){
			$needMaterial = json_decode($cdata[$nextCid]['material']);
			$lastMaterial = self::checkMaterial($needMaterial, $userMaB['61']);
			if(!$lastMaterial){
				$result['content'] = self::TXT003;
				return array('result' => $result);
			}
		}else{
			$result['content'] = self::TXT003;
			return array('result' => $result);
		}
		//合成建筑
		$com = new Hapyfish2_Island_Bll_Compensation();
		$com->setItem($nextCid, 1);
		$ok = $com->sendOne($uid, self::TXT004);
		//扣除材料和图纸
		if($ok){
			$id = $dalPlant->getOneId($uid, $nextCid);
			$key = 'i:u:plt:' . $uid . ':' . $id;
	    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    	$cPlantInfo = $cache->get($key);
	    	$cPlantInfo[] = $plantInfo['act_name'];
	    	$cache->save($key, $cPlantInfo);
			if($priceType == 1){
				if($price->coin == 0 ){
					return array('result' => $result);
				}
				$ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $price->coin);
				$result['coinChange'] = -$price->coin;
				if($ok2){
					$summary = '合成奇迹建筑';
					Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $price->coin, $summary, $time);
				}
			}else{
				$goldInfo = array(
					'uid' => $uid,
					'cost' => $price->gold,
					'summary' => '合成奇迹建筑',
					'user_level' => $userVO['level'],
					'cid' => $nextCid,
					'num' => 1
				);
		        Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		        $result['goldChange'] = -$price->gold;
			}
			
			self::updateUserMA($uid, $userMaB, $lastMaterial);
		}else{
			$result['content'] = self::TXT005;
			return array('result' => $result);
		}
		if(isset($plantInfo['act_name'])){
			$str = explode('_', $plantInfo['act_name']);
	        if($str[1] == 'resolve'){
				$key = 'i:b:c:bm:r'.$uid;
		    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
		    	$date = date('Ymd');
		    	$userR['date'] = $date;
		    	$userR['num'] = 0;
		    	$userR['max'] = 2;
		    	$cache->set($key, $userR);
		    	try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_57', 1);
					//task id 3012,task type 14
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3102);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
				} catch (Exception $e) {
				}
	        }
	        if($str[1] == 'blackMarket'){
	        	try {
	        		Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_59', 1);
					//task id 3012,task type 14
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3104);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
				} catch (Exception $e) {
				}
	        }
	        
			//update by hdf add airfield Start
			if($str[1] == 'airfield'){
				
				$data = array();
				$data['receive_time'] = time();
				$data['remain_visitor_num'] = 20;
	        	Hapyfish2_Island_Cache_Compound::updateAirRemainVisitor($uid, $nextCid, $data);
				try {
	        		Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_61', 1);
					//task id 3106,task type 
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3106);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
				} catch (Exception $e) {
				}	        	
	        }
	        //End
	        	        
		}
	    $result['status'] = 1;
	    return array('result' => $result, 'buildingVo' =>'');    
	}
//升级奇迹建筑
	public static function compoundbyPlant($uid, $cid, $priceType = 1, $addGem)
	{
		$result['status'] = -1; 
		//用户信息
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
		//图纸和材料
		$bdata = Hapyfish2_Island_Cache_Compound::getBasicInfo();
		//奇迹建筑需求
		$cdata = Hapyfish2_Island_Cache_Compound::getUpdateConfig();
		//user 材料和图纸
		$userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
		$time = time();
		//check 是否已有该建筑
		$dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$id = $dalPlant->getOneId($uid, $cid);
		$ownerCurrentIsland = $userVO['current_island'];
		$itemType = substr($cid, -2, 2);
		
		$userPlant = Hapyfish2_Island_HFC_Plant::getOne($uid, $id, 1, $ownerCurrentIsland);
		if (!$userPlant) {
            $result['content'] = 'serverWord_115';
            $result = array('result' => $result);
            return $result;
        }
        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($userPlant['cid']);
        $nextCid = $plantInfo['next_level_cid'];
        if (!$plantInfo || !$plantInfo['next_level_cid']) {
        	return array('result' => $result);
        }
        $nextLevelPlantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($nextCid);
        if (!$nextLevelPlantInfo) {
        	return array('result' => $result);
        }

        $praiseChange = $nextLevelPlantInfo['add_praise'] - $plantInfo['add_praise'];
        $userIslandInfo = Hapyfish2_Island_HFC_User::getUserIsland($uid);
        switch ( $ownerCurrentIsland ) {
        	case 1 :
        		//$userIslandInfo['praise'] = $userIslandInfo['praise'];
        		$currentIslandPraise = $userIslandInfo['praise'];
        		break;
        	case 2 :
        		//$userIslandInfo['praise'] = $userIslandInfo['praise_2'];
        		$currentIslandPraise = $userIslandInfo['praise_2'];
        		break;
        	case 3 :
        		//$userIslandInfo['praise'] = $userIslandInfo['praise_3'];
        		$currentIslandPraise = $userIslandInfo['praise_3'];
        		break;
        	case 4 :
        		//$userIslandInfo['praise'] = $userIslandInfo['praise_4'];
        		$currentIslandPraise = $userIslandInfo['praise_4'];
        		break;
        }
		//check need praise
        if ($nextLevelPlantInfo['need_praise'] > $currentIslandPraise) {
			$result['content'] = self::TXT023;
        	$result = array('result' => $result);
            return $result;
        }
		$gold = $userVO['gold'];
		$coin = $userVO['coin'];
		$price = json_decode($cdata[$nextCid]['price']);
		if($priceType == 1){
			if($coin < $price->coin){
				$result['content'] = self::TXT007;
				return array('result' => $result);
			}
		}else{
			$price->gold += $addGem;
			if($gold < $price->gold){
				$result['content'] = self::TXT006;
				return array('result' => $result);
			}
		}
        $addExp = 5;
		//check 材料
		if(isset($userMaB['61'])){
			$needMaterial = json_decode($cdata[$nextCid]['material']);
			$lastMaterial = self::checkMaterial($needMaterial, $userMaB['61']);
			if(!$lastMaterial){
				$result['content'] = self::TXT003;
				return array('result' => $result);
			}
		}else{
			$result['content'] = self::TXT003;
			return array('result' => $result);
		}
		$initRate = $cdata[$nextCid]['init_rate'];
		$maxRate = $cdata[$nextCid]['max_rate'];
		$Rate = $initRate + $addGem;
		$Rate = $maxRate > $Rate ? $Rate : $maxRate;
		$rand = rand(1, 100);
		if($rand <= $Rate){
			$upOk = true;
		}else{
			$upOk = false;
		}
		if($upOk){
			$userPlant['level'] += 1;
			$userPlant['cid'] = $nextLevelPlantInfo['cid'];
			$userPlant['pay_time'] = $nextLevelPlantInfo['pay_time'];
			$userPlant['ticket'] = $nextLevelPlantInfo['ticket'];
			$res = Hapyfish2_Island_HFC_Plant::updateOne($uid, $id, $userPlant, true);
			if(isset($plantInfo['act_name'])){
			$str = explode('_', $plantInfo['act_name']);
		        if($str[1] == 'resolve'){
					$key = 'i:b:c:bm:r'.$uid;
			    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
			    	$date = date('Ymd');
			    	$userR['date'] = $date;
			    	$userR['num'] = 0;
			    	if($userPlant['level'] < 5){
			    		$userR['max'] = $userPlant['level']*2;
			    	}else{
			    		$userR['max'] = 14;
			    	}
			    	
			    	$cache->set($key, $userR);
			    	if($nextLevelPlantInfo['level'] == 5){
			    		Hapyfish2_Island_Cache_Compound::setNotice($uid, $nextCid, $time);
				    	try {
							Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_58', 1);
							//task id 3012,task type 14
							$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3103);
							if ( $checkTask['status'] == 1 ) {
								$result['finishTaskId'] = $checkTask['finishTaskId'];
							}
						} catch (Exception $e) {
						}
			    	}
		        }
		        if($str[1] == 'blackMarket'){
		        	
		        	$key = 'i:b:c:bm:m'.$uid;	
					$cache = Hapyfish2_Cache_Factory::getMC($uid);
					$mdata = Hapyfish2_Island_Cache_Compound::getMarket();
					$data = $cache->get($key);
					$endTime = self::getEndTime($nextLevelPlantInfo['level'], $time);
					$list = array_rand($mdata, $nextLevelPlantInfo['level']+1);
					$data['time'] = $endTime;
					$data['cid'] = $list;
					$data['buy'] = false;
					$cache->set($key, $data);
		        	if($nextLevelPlantInfo['level'] == 5){
		        		Hapyfish2_Island_Cache_Compound::setNotice($uid, $nextCid, $time);
				    	try {
							Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_60', 1);
							//task id 3012,task type 14
							$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3105);
							if ( $checkTask['status'] == 1 ) {
								$result['finishTaskId'] = $checkTask['finishTaskId'];
							}
						} catch (Exception $e) {
						}
			    	}
			    }
			   
			    if($str[1] == 'airfield'){
			    	
					//update by hdf add airfield Start
					$allPeopleNumArr = array(1=>20, 2=>30, 3=>40, 4=>50, 5=>70);
					$upgradeInfo = array(); 
					$upgradeInfo['receive_time'] = time();
					$upgradeInfo['remain_visitor_num'] = $allPeopleNumArr[$nextLevelPlantInfo['level']];
					Hapyfish2_Island_Cache_Compound::upgradeAirRemainVisitor($uid, $cid, $nextCid, $upgradeInfo);
					
					if($nextLevelPlantInfo['level'] == 5){
		        		Hapyfish2_Island_Cache_Compound::setNotice($uid, $nextCid, $time);
				    	try {
							Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_62', 1);
							//task id 3012,task type 14
							$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3107);
							if ( $checkTask['status'] == 1 ) {
								$result['finishTaskId'] = $checkTask['finishTaskId'];
							}
						} catch (Exception $e) {
						}
			    	}					
					
					//End
						
			    }
			    
			}
			if (!$res) {
				$result['content'] = self::TXT005;
				$result = array('result' => $result);
				return $result;
			}
		}
		self::updateUserMA($uid, $userMaB, $lastMaterial);
		if($priceType == 1){
			if($price->coin == 0){
				$result = array('result' => $result);
			}
			$ok = Hapyfish2_Island_HFC_User::decUserCoin($uid, $price->coin);
			if ($ok) {		
				$result['coinChange'] = -$price->coin;		//add log
				Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $price->coin, self::TXT008, $time);
				try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $price->coin);
					//task id 3012,task type 14
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3001);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
				} catch (Exception $e) {
				}
			} else {
				info_log(json_encode($userPlant), 'upgrade_coin_failure');
			}
		}else{
			$goldInfo = array(
					'uid' => $uid,
					'cost' => $price->gold,
					'summary' => self::TXT008,
					'user_level' => $userVO['level'],
					'cid' => $nextLevelPlantInfo['cid'],
					'num' => 1
				);
		   $ok =  Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		   if($ok){
		   	$result['goldChange'] = -$price->gold;
		  	 try {
				Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $price->gold);
				
				//task id 3068,task type 19
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
				if ( $checkTask['status'] == 1 ) {
					$result['finishTaskId'] = $checkTask['finishTaskId'];
				}
			} catch (Exception $e) {
			}
		   }
		}
		if($ok && $upOk){
			Hapyfish2_Island_Cache_Plant::reloadAllByItemKind($uid);
				//check user current island 
	        switch ( $userIslandInfo['current_island'] ) {
            	case 2 :
            		$userIslandInfo['praise_2'] += $praiseChange;
            		break;
            	case 3 :
            		$userIslandInfo['praise_3'] += $praiseChange;
            		break;
            	case 4 :
            		$userIslandInfo['praise_4'] += $praiseChange;
            		break;
            	default :
            		$userIslandInfo['praise'] += $praiseChange;
            		break;
            }
            
			Hapyfish2_Island_HFC_User::updateUserIsland($uid, $userIslandInfo);
				
				//check double exp
			$userCardStatus = Hapyfish2_Island_HFC_User::getCardStatus($uid);
			$doubleexpCardTime = $userCardStatus['doubleexp'];
			if ($doubleexpCardTime - $time > 0) {
				$addExp = $addExp*2;
				$result['expDouble'] = 2;
			}
			Hapyfish2_Island_HFC_User::incUserExp($uid, $addExp);
		}else{
			$result['status'] = -2;
			$result['content'] = self::TXT005;
			$result = array('result' => $result);
			return $result;
		}
		try {
			//check user level up
        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
            $result['levelUp'] = $levelUp['levelUp'];
            $result['islandLevelUp'] = $levelUp['islandLevelUp'];
			if ($levelUp['feed']) {
            	$result['feed'] = $levelUp['feed'];
            } else {
            	$result['feed'] = Hapyfish2_Island_Bll_Activity::send('BUILDING_LEVEL_UP', $uid);
            }
		} catch (Exception $e) {
		}
        $buildingVo = Hapyfish2_Island_Bll_Plant::handlerPlant($userPlant, $time);
	    $result['status'] = 1;
	    $result['praiseChange'] = $praiseChange;
	    
	    return array('result' => $result, 'buildingVo' => $buildingVo);    
	}
	//修理奇迹建筑
	public  static function manageplant($uid, $itemId) 
	{
		$result = array('status' => -1);
		$itemType = substr($itemId, -2, 1);
        $itemId = substr($itemId, 0, -2);
        $time = time();
	 	if ($itemType != 3) {
            $result['content'] = 'serverWord_115';
            $result = array('resultVo' => $result);
            return $result;
        }
        $userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $cdata = Hapyfish2_Island_Cache_Compound::getUpdateConfig();
        $userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
		$islandId = $userVO['current_island'];
        $key = 'i:u:plt:' . $uid . ':' . $itemId;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);
    	if(!$item){
        	return array('result' => $result);
    	}
    	if(isset($item[18])){
    		if($time <= $item[18]){
    			$result['content'] = self::TXT016; 
    			return array('result' => $result);
    		}
    	}else{
    		$result['content'] = self::TXT016; 
    		return array('result' => $result);
    	}
        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($item[1]);
        if (!$plantInfo) {
        	return array('result' => $result);
        }
        //check 材料
        if(isset($userMaB['61'])){
        	$needList = json_decode($cdata[$item[1]]['consume']);
        	$lastMaterial = self::checkMaterial($needList, $userMaB['61']);
        	if(!$lastMaterial){
				$result['content'] = self::TXT003;
				return array('result' => $result);
			}
        }else{
			$result['content'] = self::TXT003;
			return array('result' => $result);
		}
		self::updateUserMA($uid, $userMaB, $lastMaterial);
		$endTime = $time + 3600*12;
		$item[18] = $endTime;
		$cache->save($key, $item);
		$result = array('status' => 1);
		$endureRemain = 3600*12;
        return array('result' => $result, 'endureRemain' => $endureRemain);
	}
	//材料分解
	public static function decomposePlant($uid, $itemId)
	{
		$result = array('status' => -1);
		$itemType = substr($itemId, -2, 1);
		if($itemType != 3 && $itemType != 2){
			$result['content'] = self::TXT014;
		 	return array('result'=>$result);
		}
		$max = self::getMax($uid);
    	$id = substr($itemId, 0, -2);
		$check = self::checkStatue($uid, 'resolve');
        if($check['status'] == false){
        	$result['content'] = self::TXT015;
        	return array('result'=> $result);
        }
    	
    	$key = 'i:b:c:bm:r'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$userR = $cache->get($key);
    	$date = date('Ymd');
    	if($userR['date'] == $date){
    		if($userR['num'] >= $max){
    			$result['content'] = self::TXT009;
		 		return array('result'=>$result);
    		}
    	}
		$configData = Hapyfish2_Island_Cache_Compound::getResolveConfig();
        //get user current island id
        $userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $userCurrentIsland = $userVO['current_island'];
		if($itemType == 3){
			$plant = Hapyfish2_Island_HFC_Plant::getOne($uid, $id, 0, $userCurrentIsland);
			if (!$plant) {
				return $result;
			}
	        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($plant['cid']);
	        if (!$plantInfo) {
	        	return $result;
	        }
	        if($plantInfo['level'] > $check['level']){
	        	$result['content'] = self::TXT020;
		 		return array('result'=>$result);
	        }
	        $rId = $plantInfo['nodes'].'_'.$plantInfo['level'].'_'.$itemType;
		}else{
			$building = Hapyfish2_Island_HFC_Building::getOne($uid, $id, $userCurrentIsland);
			$buildingInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($building['cid']);
			if (!$buildingInfo) {
	        	return $result;
	        }
	        $rId = $buildingInfo['nodes'].'_'.$itemType;
		}
        
        $mList = array();
        $idList = array();
        if(isset($configData[$rId])){
        	$materialList = json_decode($configData[$rId]['material']);
        	foreach($materialList as $k => $v){
        		$addRate = 0;
        		if($itemType == 3 && $v[0] == 1161){
        			if($plantInfo['level'] >= 3){
        				$userRate = Hapyfish2_Island_Cache_Compound::getUserRate($uid);
        				if($userRate){
        					$addRate += $userRate;
        				}
        			}
        		}
        		$maxRate = $v[2];
        		$maxRate += $addRate;
        		if($v[0] == 1161){
        			$shuijingRate = $maxRate;
        		}
        		$rate = rand(1, 100);
        		if($rate <= $maxRate){
        			$mList[$v[0]] = $v[1];
        			$idList[] = $v[0];
        		}
        	}
        }else{
        	$result['content'] = 'serverWord_110';
        	return array('result' => $result);
        }
		$status = $plant['status'];
		//delete user Plant by id
		if($itemType == 3){
			$ok = Hapyfish2_Island_HFC_Plant::delOne($uid, $id, $status, $userCurrentIsland);
		}else{
			$ok = Hapyfish2_Island_HFC_Building::delOne($uid, $id, $status, $userCurrentIsland);
		}
		
		if ($ok) {
            $userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
            $uMlist = array();
			if(isset($userMaB[61])){
				foreach($userMaB[61] as $v1){
					$uMlist[$v1['cid']] = $v1['num']; 
				}
			}
			$data = array();
			foreach($mList as $k2 => $v2){
				if(isset($uMlist[$k2])){
					$data[$k2] = $v2 + $uMlist[$k2];
				}else{
					$data[$k2] = $v2;
				}
			}
			self::updateUserMA($uid, $userMaB, $data);
			if(!empty($idList)){
				if(in_array(1161, $idList)){
					Hapyfish2_Island_Cache_Compound::clearUserRate($uid);
				}
				if($itemType == 3){
					if(!in_array(1161, $idList) && $plantInfo['level'] >= 3){
						Hapyfish2_Island_Cache_Compound::UpdateUserRate($uid, 5);
						$shuijingRate +=5;
					}
				}
			}
			if($userR['date'] == $date){
	    		$userR['num'] += 1;
	    	}else{
	    		$userR['date'] = $date;
	    		$userR['num'] = 1;
	    	}
	    	$cache->set($key, $userR);	
			$result['status'] = 1;
			Hapyfish2_Island_Cache_Mooch::clearMoochPlant($uid, $id);
		} else {
			$result['status'] = -1;
			$result['content'] = 'serverWord_110';
		}

        return array('result'=>$result, 'list'=>$idList, 'rate' => $shuijingRate);
	} 
	
	//check图纸
	public static function checkMaterial($needMaterial, $userMaB)
	{
		$data = null;
		foreach($needMaterial as $v){
			$mList[$v[0]] = $v[1];
		}
		foreach($userMaB as $v1){
			$uMlist[$v1['cid']] = $v1['num']; 
		}
		foreach($mList as $k2 => $v2){
			if(!isset($uMlist[$k2])){
				return null;
			}else{
				$data[$k2] = $uMlist[$k2] - $v2;
				if($data[$k2] < 0){
					return null;
				}
			}
		}
		return $data;
	}
	//更新材料与图纸
	public static function updateUserMA($uid, $userMaB, $lastMaterial)
	{
		$key = 'i:u:c:bm:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
		if(!empty($lastMaterial)){
			foreach($lastMaterial as $k => $v){
				$type = substr($k, -2);
				if(isset($userMaB[$type][$k])){
					$userMaB[$type][$k]['num'] = $v;
				}else{
					$userMaB[$type][$k]['num'] = $v;
					$userMaB[$type][$k]['cid'] = $k;
					$userMaB[$type][$k]['type'] = $type;
				}
				
			}
		}
		$cache->set($key, $userMaB);
		foreach($userMaB as $v1){
			foreach($v1 as $v2)
			{
				$dal->updateUserMa($uid, $v2['cid'], $v2['num'], $v2['type']);
			}
		}
		return true;
	}
	
	public static function checkStatue($uid, $type)
	{
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
		$ownerCurrentIsland = $userVO['current_island'];
		$ids = Hapyfish2_Island_Cache_Plant::getOnIslandIds($uid, $ownerCurrentIsland);
		$time = time();
		$keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:plt:' . $uid . ':' . $id;
        }
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);
        $status = false;
        if($data){
        	foreach($data as $item){
        		if(count($item) == 19){
        			$str = explode('_', $item[17]);
        			if($str[1] == $type){
        				$status = true;
        				$level = $item[2];
        				if($time > $item[18] ){
        					$status = false;
        					return $status;
        				}
        			}
        		}
        	}
        }
        return array('status' => $status, 'level' => $level);
	}
	
	//像好友索要
	public static function begMaterial($uid, $cid, $fids)
	{
		$resultVo = array();
		if (empty($fids)) {
			$resultVo['status'] = -1;
        	$resultVo['content'] = 'serverWord_110';
        	return array('result' => $resultVo);
		}

		$aryFid = explode(',', $fids);
		if (empty($aryFid)) {
			$resultVo['status'] = -1;
        	$resultVo['content'] = 'serverWord_110';
        	return array('result' => $resultVo);
		}

		//is friend check
		foreach ($aryFid as $fid) {
			$isFriend = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
			if (!$isFriend) {
				$resultVo['status'] = -1;
	        	$resultVo['content'] = 'serverWord_173';
	        	return array('result' => $resultVo);
			}
		}
		$bdata = Hapyfish2_Island_Cache_Compound::getBasicInfo();
		$name = $bdata[61][$cid]['name'];
		//ask for
		$content = self::TXT018.$name.self::TXT019;
		foreach ($aryFid as $fid) {
			Hapyfish2_Island_Bll_Remind::addRemind($uid, $fid, $content, 0);
		}
		$resultVo['status'] = 1;
		return array('result' => $resultVo);
	}

	public static function buyItem($uid, $itemArray)
	{
		$result = array('coin' =>0, 'gold'=>0);
		$cost = $itemArray['num'] * $itemArray['price'];
		$userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid); 
		$last[$itemArray['cid']] = $itemArray['num'];
		if($userMaB){
			foreach($userMaB as $k => $v){
				if(isset($v[$itemArray['cid']])){
					$last[$itemArray['cid']] += $v[$itemArray['cid']]['num'];
				}
			}
		}else{
			$userMaB = array();
		}
		$ok = self::updateUserMA($uid, $userMaB, $last);
		if($itemArray['price_type'] == 1){
			Hapyfish2_Island_HFC_User::decUserCoin($uid, $cost);
			$summary = LANG_PLATFORM_BASE_TXT_13 . $itemArray['name'];
			Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $cost, $summary, $itemArray['buy_time']);
			$result['coin'] = $cost;
		}else{
			$goldInfo = array(
		        		'uid' => $uid,
		        		'cost' => $cost,
		        		//'summary' => '购买' . $background['name'],
		        		'summary' => LANG_PLATFORM_BASE_TXT_13 . $itemArray['name'],
		        		'user_level' => 1,
		        		'cid' => $itemArray['cid'],
		        		'num' => $itemArray['num']
		        	);
		     $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
			 $result['gold'] = $cost;
		}
		return $result;
	}
	
	public static function  addMaterial($uid, $cid, $num)
	{
		$userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid); 
		if($cid == 261 || $cid == 361){
			$last[$cid] = 5;
		}else{
			$last[$cid] = $num;
		}
		if($userMaB){
			foreach($userMaB as $k => $v){
				if(isset($v[$cid])){
					$last[$cid] += $v[$cid]['num'];
				}
			}
		}else{
			$userMaB = array();
		}
		
		$ok = self::updateUserMA($uid, $userMaB, $last);
		return $ok;
	}
	
    public static function  addMaterialByNum($uid, $cid, $num)
    {
        $userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
        $last[$cid] = $num;
        if($userMaB){
            foreach($userMaB as $k => $v){
                if(isset($v[$cid])){
                    $last[$cid] += $v[$cid]['num'];
                }
            }
        }else{
            $userMaB = array();
        }
        
        $ok = self::updateUserMA($uid, $userMaB, $last);
        return $ok;
    }	
    
	public static function getFuncBuilding($uid)
	{
		$ids = array();
		for($id=1;$id<=4;$id++)
		{
			$list = Hapyfish2_Island_Cache_Plant::getOnIslandIds($uid, $id);
			if($list){
				$ids = array_merge($ids, $list);
			}
		}
		$time = time();
		$keys = array();
		$list = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:plt:' . $uid . ':' . $id;
        }
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);
        $status = false;
        if($data){
        	foreach($data as $item){
        		if(isset($item[17])){
        			$str = explode('_', $item[17]);
	        		if($str[0] == 'miracle'){
	        			$list[$str[1]]['id'] = $item[0];
	        			$list[$str[1]]['cid'] = $item[1];
	        		}
        		}	
        	}
        }
        return $list;
	}
	
	
	public static function getUserNotice($uid, $time)
	{
		$list = array();
		$result = array('status'=>1);
		$notice = Hapyfish2_Island_Cache_Compound::getNoticeAll($time);
		$key = 'compound:notice'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if(!$notice){
			return array('result'=> $result, 'list'=>array());
		}
		$lastDay = date('Ymd', $time-3*86400);
		if($data){
			foreach($data as $date => $num){
				if($date <= $lastDay){
					unset($data[$date]);
				}
			}
		}
		foreach($notice as $k => $v){
			$num = count($v);
			$slist = array();
			if(isset($data[$k])){
				if($data[$k]< $num){
					$slist = array_splice($notice[$k], $data[$k]);
				}
			}else{
				$slist = $v;
			}
			$data[$k] = $num;
			$list = array_merge($list, $slist);
		}
		$newList = array();
		if(!empty($list)){
			foreach($list as $k1 => $v1){
				$userInfo = Hapyfish2_Platform_Bll_User::getUser($v1['uid']);
				$newList[$k1]['cid'] = $v1['cid'];
				$newList[$k1]['name'] = $userInfo['name'];
			}
		}
		$cache->set($key, $data);
		return array('result'=> $result, 'list'=>$newList);
	}
	
	public static function saleItem($uid, $cid, $num)
	{
		$userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
		$result = array('status' => -1);
		$type = substr($cid, -2);
		if(!isset($userMaB[$type][$cid])){
			return array('result' => $result);
		}else{
			$last[$cid] = $userMaB[$type][$cid]['num'] - $num;
			if($last[$cid] < 0){
				return array('result' => $result);
			}
		}
		self::updateUserMA($uid, $userMaB, $last);
		Hapyfish2_Island_HFC_User::incUserCoin($uid, $num);
		$result['coinChange'] = $num;
		$result['status'] = 1;
		return $result;
	}
	
	public static function buyfrommarket($uid, $itemBoxAry)
	{
		$result = array('status' => -1);
		$cid = $itemBoxAry[0]['cid'];
		$num = $itemBoxAry[0]['num'];
		$key = 'i:b:c:bm:m'.$uid;	
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data['buy']){
			$result['content'] = self::TXT025;	
			return $result;
		}
		if(!in_array($cid, $data['cid'])){
			$result['content'] = self::TXT021;	
			return $result;
		}
	 	$check = self::checkStatue($uid, 'blackMarket');
        if($check['status'] == false){
        	$result['content'] = self::TXT015;
        	return $result;
        }
        $mdata = Hapyfish2_Island_Cache_Compound::getMarket();
        $userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $detailInfo = Hapyfish2_Island_Cache_BasicInfo::getDetailInfo($cid);
        if(!isset($mdata[$cid])){ 
        	return $result;
        }
		 if(!$detailInfo){
        	return $result;
        }
        $info = $mdata[$cid];
        $coin = $userVO['coin'];
        $gold = $userVO['gold'];
        $time = time();
        if($info['price_type'] == 1){
        	if($coin < $info['price']*$num){
        		$result['content'] = self::TXT007;
        		return $result;
        	}
        }else{
        	if($gold < $info['price']*$num){
        		$result['content'] = self::TXT006;
        		return  $result;
        	}
        }
       $com = new Hapyfish2_Island_Bll_Compensation();
       $com->setItem($cid, $num);
	   $ok = $com->sendOne($uid, self::TXT022);
	   if($ok){
	   	 if($info['price_type'] == 1){
			Hapyfish2_Island_HFC_User::decUserCoin($uid, $info['price']*$num);
			$summary = LANG_PLATFORM_BASE_TXT_13 . $detailInfo['name'];
			Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $info['price']*$num, $summary, $time);
			$result['coinChange'] = -$info['price']*$num;
	   	 }else{
	   		$goldInfo = array(
		        		'uid' => $uid,
		        		'cost' => $info['price']*$num,
		        		//'summary' => '购买' . $background['name'],
		        		'summary' => LANG_PLATFORM_BASE_TXT_13 . $detailInfo['name'],
		        		'user_level' => 1,
		        		'cid' => $cid,
		        		'num' => $num
		        	);
		    Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		    $result['goldChange'] = -$info['price']*$num;
	   	}
	   	$data['buy'] = $cid;
	   	$cache->set($key, $data);
	   }else {
	         return $result;
	   }
	   $result['status'] = 1;
	   return $result;
	} 
	
	public static function getEndTime($level, $time)
	{
		if($level ==1){
			 $endTime = $time+12*3600;
		}else if($level ==2){
			$endTime = $time+10*3600;
		}else if($level ==3){
			$endTime = $time+8*3600;
		}else if($level ==4){
			$endTime = $time+7*3600;
		}else if($level ==5){
			$endTime = $time+6*3600;
		}
		return $endTime;
	}
	
	public static function checkCanBuy($uid, $cid)
	{
		$userVo = Hapyfish2_Island_HFC_User::getUserVO($uid);
		$bdata = Hapyfish2_Island_Cache_Compound::getBasicInfo();
		$to_cid = $bdata['51'][$cid]['to_cid'];
		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getDetailInfo($to_cid);
        if($userVo['level'] < $plantInfo['need_level']){
        	$content = self::TXT026;
            return $content;
        }
		
		$userMaB = Hapyfish2_Island_Cache_Compound::getUserbAm($uid); 
		if(isset($userMaB[51][$cid])){
			if($userMaB[51][$cid]['num'] > 0){
				$content = self::TXT024;
				return $content;
			}
		}
		$bdata = Hapyfish2_Island_Cache_Compound::getBasicInfo();
		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($bdata[51][$cid]['to_cid']);
		$dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$id = $dalPlant->getOneIdByItemid($uid, $plantInfo['item_id']);
		if($id){
			$content = self::TXT024;
			return $content;
		}
		return null;
	}
	
	public static function getShuiJingRate($uid, $cid)
	{
		$result = array('status' => -1);
		$itemType = substr($cid, -2, 1);
		if($itemType == 3){
			$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
	        if (!$plantInfo) {
	        	return  array('result'=> $result);
	        }
	        $rId = $plantInfo['nodes'].'_'.$plantInfo['level'].'_'.$itemType;
		}else{
			$buildingInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($cid);
			if (!$buildingInfo) {
	        	return  array('result'=> $result);
	        }
	        $rId = $buildingInfo['nodes'].'_'.$itemType;
		}
		$configData = Hapyfish2_Island_Cache_Compound::getResolveConfig();
	 	if(isset($configData[$rId])){
        	$materialList = json_decode($configData[$rId]['material']);
        	foreach($materialList as $k => $v){
        		$addRate = 0;
        		if($v[0] == 1161){
        			if($itemType == 3){
	        			if($plantInfo['level'] >= 3){
	        				$userRate = Hapyfish2_Island_Cache_Compound::getUserRate($uid);
	        				if($userRate){
	        					$addRate += $userRate;
	        				}
	        			}
        			}
        		$maxRate = $v[2];
        		$maxRate += $addRate;
        		break;
        		}
        	}
        	$result['status'] = 1;
        	return array('result' => $result, 'rate'=>$maxRate);
        }else{
        	$result['content'] = 'serverWord_110';
        	return array('result' => $result);
        }
		
	}
	
	/**
	 * 飞机场奇迹建筑接船
	 * @param $uid	用户UID
	 * @param $cid	建筑CID
	 */
	public static function receiveBoat($uid, $cid)
	{
		$now = time();
		$allPeopleNumArr = array(1=>20, 2=>30, 3=>40, 4=>50, 5=>70);
		$dataVisitors = array();
		$resultVo = array('status' => -1);
		
		$cid = (int)$cid;
		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
		if(!$plantInfo) {
			return array('result' => $resultVo);
		}
		
		$vInfo = Hapyfish2_Island_Cache_Compound::getAirRemainVisitor($uid, $cid);
		if(!$vInfo) {
			return array('result' => $resultVo);
		}
		if( ($vInfo['receive_time']+3600-$now) >0 ) {
			return array('result' => $resultVo);
		}
		if( $vInfo['remain_visitor_num'] <= 0 ) {
			return array('result' => $resultVo);
		}	

		
		$plantLevel = $plantInfo['level'];
		$dataVo = Hapyfish2_Island_Cache_Compound::getAirRemainVisitor($uid, $cid);

		
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid, array('level' => 1));
		if (!$userLevelInfo) {
			return array('result' => $resultVo);
		}
    		
		//get user vo,current island
		$userVo = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $userCurrentIsland = $userVo['current_island']; 
        switch ( $userCurrentIsland ) {
        	case 2 :
        		$userIslandLevel = $userLevelInfo['island_level_2'];
        		break;
        	case 3 :
        		$userIslandLevel = $userLevelInfo['island_level_3'];
        		break;
        	case 4 :
        		$userIslandLevel = $userLevelInfo['island_level_4'];
        		break;
        	default :
        		$userIslandLevel = $userLevelInfo['island_level'];
        		break;
        }
		$islandLevelInfo = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfo($userIslandLevel);
		
		if (!$islandLevelInfo) {
			return array('result' => $resultVo);
		}

		$visitorCount = $islandLevelInfo['max_visitor'];//岛上最大可接待游客数
		
		$plantVO = Hapyfish2_Island_Bll_Plant::getAllOnIslandNoMooch($uid, $userCurrentIsland, true);
		$currently_visitor = $plantVO['visitorNum'];	//岛上现有游客数

		if ($currently_visitor >= $visitorCount) {
			$resultVo['content'] = 'serverWord_133';
			return array('result' => $resultVo);
		}

        //receiveNum
        $receiveNum = $dataVo['remain_visitor_num'];
        if ($currently_visitor > 0) {
            $receiveNum = min($visitorCount - $currently_visitor, $receiveNum);
        }
        else if ($visitorCount < $receiveNum) {
            $receiveNum = $visitorCount;
        }

        $userIslandInfo = Hapyfish2_Island_HFC_User::getUserIsland($uid);
        $praise = $userIslandInfo['praise'];

	    //check this ship has super visitor
        $hasShipSuperVisitor = Hapyfish2_Island_Cache_CompoundSuperVisitor::hasShipSuperVisitor($uid);
        if ( $hasShipSuperVisitor ) {
            $superVisitorInfo = Hapyfish2_Island_Bll_CompoundSuperVisitor::updateSuperVisitor($uid, $cid);
        }        
        
        try{
			$positionAry = array();
			$visitorNum = 0;
			if ($receiveNum == $dataVo['remain_visitor_num']) {
				//接完
				$dataVisitors['receive_time'] = $now;
				$dataVisitors['remain_visitor_num'] = $allPeopleNumArr[$plantLevel];
				
				$downPeopleTime = 3600;
				$currentPeopleNum = 0;
				
             	//update this ship,super visitor,小人系统
            	Hapyfish2_Island_Cache_CompoundSuperVisitor::updateShipSuperVisitor($uid, 'Y');  
            	             				
			} else {
				//未接完
				$dataVisitors['remain_visitor_num'] = $vInfo['remain_visitor_num'] - $receiveNum;
				$downPeopleTime = 0;
				$currentPeopleNum = $dataVisitors['remain_visitor_num'];
				
				Hapyfish2_Island_Cache_CompoundSuperVisitor::updateShipSuperVisitor($uid, 'N');      				
			} 

            			
			Hapyfish2_Island_Cache_Compound::updateAirRemainVisitor($uid, $cid, $dataVisitors);
			
			
			$addExp = 3;
			//check double exp
			$userCardStatus = Hapyfish2_Island_HFC_User::getCardStatus($uid);
			$doubleexpCardTime = $userCardStatus['doubleexp'];
			if ($doubleexpCardTime - $now > 0) {
				$addExp = $addExp*2;
				$result['expDouble'] = 2;
			}
			Hapyfish2_Island_HFC_User::incUserExp($uid, $addExp);
            $visitorsAry = Hapyfish2_Island_Bll_Dock::visitorsInvite($uid, $receiveNum, $userCurrentIsland, true);
            
			//isitor arrive pay
			$resultVo['expChange'] = $addExp;
			$resultVo['islandChange'] = true;
			$resultVo['status'] = 1;

		} catch (Exception $e) {
			info_log('[receiveBoat]:'.$e->getMessage(), 'Hapyfish_Island_Bll_Compound');
			$resultVo['content'] = 'serverWord_110';
            return array('result' => $resultVo);
		}

		
    	try {
			//check user level up
        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
            $resultVo['levelUp'] = $levelUp['levelUp'];
            $resultVo['islandLevelUp'] = $levelUp['islandLevelUp'];
			if ($levelUp['feed']) {
            	$resultVo['feed'] = $levelUp['feed'];
            }
		} catch (Exception $e) {
		}

		if (empty($visitorsAry)) {
			$visitorsAry = array();
		}
		$result = array('visitors' => $visitorsAry, 'visitorNum' => $visitorNum);
		
    	//统计每日接待游客数
		$accVisitorNum = 0;
		foreach ($visitorsAry as $visitorsValue) {
			$accVisitorNum += $visitorsValue['num'];
		}

		if ($accVisitorNum > 0) {
			Hapyfish2_Island_Cache_Visit::addAccVisitorNum($uid, $accVisitorNum);
		}
		
		$result['result'] = $resultVo;
		$result['downPeopleTime'] = $downPeopleTime;
		$result['currentPeopleNum'] = $currentPeopleNum;
		
		//小人系统
        if ( $hasShipSuperVisitor ) {
            $result['spVisitors'] = $superVisitorInfo;
        }
        		
		return $result;		
	}
	
	public static function getMax($uid)
	{
		$ids = array();
		$level = 1;
		$max = 2;
		for($id=1;$id<=4;$id++)
		{
			$list = Hapyfish2_Island_Cache_Plant::getOnIslandIds($uid, $id);
			if($list){
				$ids = array_merge($ids, $list);
			}
		}
		$time = time();
		$keys = array();
		$list = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:plt:' . $uid . ':' . $id;
        }
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);
        $status = false;
        if($data){
        	foreach($data as $item){
        		if(isset($item[17])){
        			$str = explode('_', $item[17]);
	        		if($str[1] == 'resolve'){
	        			$level = $item[2];
	        			break;
	        		}
        		}	
        	}
        }
		if($level < 5){
	    	$max = $level*2;
	    }else{
	    	$max = 14;
	    }
	    return $max;
	}
}