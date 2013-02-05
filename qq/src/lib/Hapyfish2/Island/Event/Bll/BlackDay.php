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
		
		$upNeeds = array(100, 250);
		
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
    
        $userPlant = Hapyfish2_Island_HFC_Plant::getOne($uid, $itemId, 1);
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
    	
		$list = Hapyfish2_Platform_Bll_Factory::getFriendIds($uid);
		
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
    	$needGold = 20;
    	
		$allGold = $needGold * count($friends);
		
		$result =  Hapyfish2_Island_Bll_QpointBuy::getToken($uid, 1499, array('gold' => $allGold, 'cid' => $cid, 'fidsArr' => $friendListStr));
		
    	$resultVo = array('result' => $result);
	    		
    	return $resultVo;
    }
}