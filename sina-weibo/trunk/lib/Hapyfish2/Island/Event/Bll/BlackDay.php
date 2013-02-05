<?php

/**
 * Event BlackDay
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/04    zhangli
*/
class Hapyfish2_Island_Event_Bll_BlackDay
{
	const TXT001 = '对不起，升级该建筑所需人数不足，不能升级！';
	const TXT002 = '赠送婚纱店';
	const TXT003 = '对不起，您没有选择好友！';
	const TXT004 = '赠送成功！';
	
	/**
	 * @获取购买人数
	 * @param int $uid
	 * @retur Array
	 */
	public static function getBuyNum()
	{
		$key = 'ev:BlackDay:buyNum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$count = $cache->get($key);

		if ($count === false) {
			$falseTime = strtotime('2011-11-17 23:59:59');
			$count = 0;
			$cache->set($key, 0, $falseTime);
		}
		
		$upNeeds = array(200, 500);
		
		$result = array('status' => 1);
		$resultVo = array('result' => $result, 'currentCount' => $count, 'upNeeds' => $upNeeds);
		
		return $resultVo;
	}
	
	/**
	 * @升级婚纱店
	 * @param int $uid
	 * @param int $itemId
	 * @return Array
	 */
	public static function gradeUpBridal($uid, $itemId)
    {
        $result = array('status' => -1);

        $itemType = substr($itemId, -2, 1);
        $itemId = substr($itemId, 0, -2);
        if ($itemType != 3) {
            $result['content'] = 'serverWord_115';
            $result = array('result' => $result);
            
            return $result;
        }
        
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $ownerCurrentIsland = $userVO['current_island'];
        $userPlant = Hapyfish2_Island_HFC_Plant::getOne($uid, $itemId, 1, $ownerCurrentIsland);
        if (!$userPlant) {
            $result['content'] = 'serverWord_115';
            $result = array('result' => $result);
            return $result;
        }

        //如果不是婚纱店不能用此接口升级
        if (!in_array($userPlant['cid'], array(121032, 121132)))  {
			$result['content'] = 'serverWord_101';
            $result = array('result' => $result);
            return $result;
        }
        
        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($userPlant['cid']);
        if (!$plantInfo || !$plantInfo['next_level_cid']) {
        	return array('result' => $result);
        }
        
        $nextLevelPlantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($plantInfo['next_level_cid']);
        if (!$nextLevelPlantInfo) {
        	return array('result' => $result);
        }

        $praiseChange = $nextLevelPlantInfo['add_praise'] - $plantInfo['add_praise'];
        
        $userInfo = Hapyfish2_Island_HFC_User::getUser($uid, array('coin' => 1, 'level' => 1));
        if ($userInfo === null) {
        	return array('result' => $result);
        }
        
        //获得当前购买人数和升级需要数据
        $upgrdeData = self::getBuyNum();
        
        //判断升级人数是否足够
        if ($userPlant['cid'] == 121032) {
        	//3星升级到4星
        	if ($upgrdeData['currentCount'] < $upgrdeData['upNeeds'][0]) {
        		$result['content'] = self::TXT001;
	            $result = array('result' => $result);
	            return $result;
        	}
        } else {
        	//4星升级到5星
        	if ($upgrdeData['currentCount'] < $upgrdeData['upNeeds'][1]) {
        		$result['content'] = self::TXT001;
	            $result = array('result' => $result);
	            return $result;
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
        
        $result = array('result' => $result, 'buildingVo' => $buildingVo);

        return $result;
    }
    
    /**
     * @获取好友列表
     * @param int $uid
     * @return Array
     */
    public static function getFriendListBridal($uid)
    {
    	$result = array('status' => -1);
    	
		$list = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
		
		$max = 3;
		$fids = array();
		foreach ($list as $fid) {
			$key = 'ev:BlackDay:to:' . $fid . $uid;
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$count = $cache->get($key);
		
			if (($count < 3) || ($count === false)) {
				$fids[] = $fid;
			}
		}
		
		return $fids;
    }
    
    /**
     * @赠送朋友建筑
     * @param int $uid
     * @param array $friends
     * @return Array
     */
    public static function toSendBridal($uid, $friendListStr)
    {
    	$result = array('status' => -1);

    	//没有朋友列表
    	if ($friendListStr == false) {
    		$result['status'] = self::TXT003;
    		$resultVo = array('result' => $result);
    		
    		return $resultVo;
    	}
    	
    	$friends = json_decode($friendListStr);
    	
        $cid = 121032;
    	$needGold = 30;
    	$nowTime = time();
    	$falseTime = strtotime('2011-11-17 23:59:59');
    	
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
    	
		$allGold = $needGold * count($friends);
		
		//宝石不足
		if ($allGold > $userGold) {
    		$result['status'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		
    		return $resultVo;
		}
		
        //get plant by cid
        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
		
		//好友列表
		$fids = self::getFriendListBridal($uid);
		
		//送婚纱店
		foreach ($friends as $fid) {
			if (!in_array($fid, $fids)) {
	    		$result['status'] = 'serverWord_101';
	    		$resultVo = array('result' => $result);
	    		
	    		return $resultVo;
			} else {
				$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($fid, $cid, 1);
				
				if ($ok) {
					//记录赠送好友的ID
					$key = 'ev:BlackDay:to:' . $fid . $uid;
					$cache = Hapyfish2_Cache_Factory::getMC($uid);
					$count = $cache->get($key);
						
					if ($count === false) {			
						$cache->set($key, 1, $falseTime);
					} else {
						$count++;
						$cache->set($key, $count, $falseTime);
					}
					
					//记录购买婚纱店人数
					$keyBuyNum = 'ev:BlackDay:buyNum';
					$cacheMC = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
					$buyNum = $cacheMC->get($keyBuyNum);
					
					if ($buyNum === false) {
						$buyNum = 1;
						$cacheMC->set($keyBuyNum, $buyNum, $falseTime);
					} else {
						$buyNum++;
						$cacheMC->set($keyBuyNum, $buyNum, $falseTime);
					}
					
					//获取用户等级
					$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
					$userLevel = $userLevelInfo['level'];
					
					//扣除宝石
					$goldInfo = array('uid' => $uid,
									'cost' => $needGold,
									'summary' => self::TXT002,
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
					
					$user = Hapyfish2_Platform_Bll_User::getUser($uid);
					
					$title = '你的好友<font color="#379636">' . $user['name'] . '</font>赠送给你一个<font color="#FF0000">' . $plantInfo['name'] . '</font>';
					
					//发feed
					$feed = array('uid' => $fid,
								'template_id' => 0,
								'actor' => $uid,
								'target' => $fid,
								'type' => 3,
								'title' => array('title' => $title),
								'create_time' => $nowTime);
				
					Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
					
					info_log($uid . '->' . $fid, 'BlackDay');
				} else {
					info_log($uid . '->' . $fid, 'BlackDayErr');
					
		    		$result['status'] = 'serverWord_101';
		    		$resultVo = array('result' => $result);
		    		
		    		return $resultVo;
				}
			}
		}
		
		$result['status'] = 1;
		$result['content'] = self::TXT004;
		$result['goldChange'] = -$allGold;
    	$resultVo = array('result' => $result);
	    		
    	return $resultVo;
    }
}