<?php

/**
 * Event ThanksGivingDay
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/14    zhangli
*/
class Hapyfish2_Island_Event_Bll_ThanksDay
{	
	const TXT001 = '感恩节雇佣机器人';
	const TXT002 = '感恩节解雇好友';
	const TXT003 = '对不起，您已经帮了朋友5次忙，还是等明天吧!';
	const TXT004 = '对不起，您的爱心值不足，不能兑换';
	const TXT005 = '不能入驻自己的工地';
	const TXT006 = '不能解雇机器人';
	const TXT007 = '您的雕像已经5级，不需要补齐！';
	const TXT008 = '感恩节购买爱心';
	const TXT009 = '对不起，您今天已经去过该好友家了，请明天再来吧！';
	
	/**
	 * @感恩节初始化
	 * @param int $uid
	 * @param int $fid  (是自己的时候不传fid)
	 * @return Array
	 */
	public static function thDayInit($uid, $fid)
	{
		$result = array('status' => -1);
		
		//是自己的时候
		if ($fid != 0) {
			$uid = $fid;
		}
		
		//获取建筑等级
		$plantLevl = Hapyfish2_Island_Event_Cache_ThanksDay::getPlantLevel($uid);
        
		//获取用户的爱心值
		$hasLove = Hapyfish2_Island_Event_Cache_ThanksDay::hasLove($uid);
		
		//获取用户用的最大爱心值
		$hasLoveMax = Hapyfish2_Island_Event_Cache_ThanksDay::hasLoveMax($uid);
		
		$feedTrue = 0;
		
		if ($plantLevl < 5) {
			//获取雕像基础信息
			$plantVo = Hapyfish2_Island_Event_Cache_ThanksDay::aryPlant();
			$level = 0;
			foreach ($plantVo as $plkey => $plant) {
				if ($hasLoveMax < $plant['needLove']) {
					$level = $plantVo[$plkey - 1]['level'];
					break;
				}
			}
			
			if ($level == false) {
				if ($hasLoveMax >= $plantVo[4]['needLove']) {
					$level = 5;
					
					$nowTime = time();
					
					$dateFormat = date('Y-m-d H:i:s', $nowTime);
					
					//记录拥有5级沙雕的人
					info_log($dateFormat . '  ' . $uid, 'sculpture');
				}
			}
			
			if ($level > $plantLevl) {
				//升级用户雕像
				Hapyfish2_Island_Event_Cache_ThanksDay::addPlantLevel($uid, $level);
				$feedTrue = 1;

				//统计雕像等级
				$db = Hapyfish2_Island_Event_Dal_ThanksDay::getDefaultInstance();
				$hasFlag = $db->getFlag($uid);
				if (!$hasFlag) {
					try {
						$db->incFlag($uid, $level);
					} catch (Exception $e) {}
				} else {
					try {
						$db->updateFlag($uid, $level);
					} catch (Exception $e) {}
				}
				
				$plantLevl = $level;
			}
			
			if ($plantLevl == 5) {
				//雕像升级到5级的第二天才可以获取爱心值奖励
				Hapyfish2_Island_Event_Cache_ThanksDay::addLoveFlag($uid);
			}
		}
		
		//5级雕像每天可以获得100爱心
		if ($plantLevl == 5) {
			$canAddLove = Hapyfish2_Island_Event_Cache_ThanksDay::canAddLove($uid);
			
			if ($canAddLove == false) {
				Hapyfish2_Island_Event_Cache_ThanksDay::renewHasLove($uid, 100);
				
				//每天只能获得一次
				Hapyfish2_Island_Event_Cache_ThanksDay::addLoveFlag($uid);
				
				$nowTime = time();
				
				//发feed
				$feed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'type' => 3,
							'title' => array('title' => '5级雕像奖励:<font color="#379636">100爱心</font>'),
							'create_time' => $nowTime);
			
				Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
			}
		}
	
		//用户工地列表
		$siteList = Hapyfish2_Island_Event_Cache_ThanksDay::siteList($uid);
		
		//获取用户的爱心值
		$hasLove = Hapyfish2_Island_Event_Cache_ThanksDay::hasLove($uid);
		
		//获取用户用的最大爱心值
		$hasLoveMax = Hapyfish2_Island_Event_Cache_ThanksDay::hasLoveMax($uid);
		
		if ($hasLove > $hasLoveMax) {
			$key = 'ev:thday:loveMax:' . $uid;
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$cache->set($key, $hasLove, 2592000);
	
			//记录用户爱心值
			$db = Hapyfish2_Island_Event_Dal_ThanksDay::getDefaultInstance();
			$maxLove = $db->getLoveMax($uid);
		
			if (!$maxLove) {
				try {
					$db->incLoveMax($uid, $hasLove);
				} catch (Exception $e) {}			
			} else {
				try {
					$db->renewLoveMax($uid, $hasLove);
				} catch (Exception $e) {}
			}
		}
		
		$result['status'] = 1;
		$resultVo['result'] = $result;
		$resultVo['buildingLevel'] = (int)$plantLevl;
		$resultVo['hasLove'] = $hasLove;
		$resultVo['maxLove'] = $hasLoveMax;
		$resultVo['feedTrue'] = $feedTrue;
		$resultVo['siteList'] = $siteList;
		
		return $resultVo;
	}
	
	/**
	 * @雇佣机器人
	 * @param int $uid
	 * @param int $id
	 * @param int $siteId
	 * @return Array
	 */
	public static function thDayRobot($uid, $id, $siteId)
	{
		$result = array('status' => -1);
		
		//雇佣的机器人ID和工地ID不能为空
		if (!$id || !$siteId) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		if (!in_array($siteId, array(1, 2, 3, 4))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		if (!in_array($id, array(1, 2, 3))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//获取机器人信息
		$robotData = Hapyfish2_Island_Event_Cache_ThanksDay::getRobotData($id);
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($robotData['needGold'] > $userGold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//判断当前工地是否可以雇佣机器人
		$siteRobot = Hapyfish2_Island_Event_Cache_ThanksDay::getSiteById($uid, $siteId);
		if ($siteRobot == false) {
    		$result['content'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		
    		return $resultVo;
		}
		
		//机器人入住工地
		Hapyfish2_Island_Event_Cache_ThanksDay::incSite($uid, $siteId, $id, $robotData);
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		$nowTime = time();
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $robotData['needGold'],
						'summary' => self::TXT001,
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 0);

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
	        
	        info_log($uid . ',' . $id, 'thDayBuyRobot');
		}
		
		$result['status'] = 1;
		$result['goldChange'] = -$goldInfo['cost'];
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	
	/**
	 * @解雇好友
	 * @param int $uid
	 * @param int $fid
	 * @param int $siteId
	 * @return Array
	 */
	public static function thDayDisMiss($uid, $fid, $siteId)
	{
		$result = array('status' => -1);
		
		//好友ID和工地ID不能为空
		if (!$fid || !$siteId) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		if (!in_array($siteId, array(1, 2, 3, 4))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//机器人不能解雇
		if (in_array($fid, array(1, 2, 3))) {
			$result['content'] = self::TXT006;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//解雇好友许花费1宝石
		$needGood = 1;
		
    	//获得用户宝石
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($needGood > $userGold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}
		
		//把好友从工地解雇
		Hapyfish2_Island_Event_Cache_ThanksDay::delSite($uid, $siteId);
		
		$nowTime = time();
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $needGood,
						'summary' => self::TXT002,
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 0);

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
	        
	        info_log($uid . ',' . $fid, 'thDayDisMiss');
		}
		
		$result['status'] = 1;
		$result['goldChange'] = -$goldInfo['cost'];
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	
	/**
	 * @入驻好友工地
	 * @param int $uid
	 * @param int $fid
	 * @param int $siteId (好友的工地ID)
	 * @return Array
	 */
	public static function thDayCheckIn($uid, $fid, $siteId)
	{
		$result = array('status' => -1);

		if ($uid == $fid) {
			$result['content'] = self::TXT005;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		if (!in_array($siteId, array(1, 2, 3, 4))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//好友ID和好友工地ID不能为空
		if (!$fid || !$siteId) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//每天每人最多可以入驻5次好友的工地
		$num = Hapyfish2_Island_Event_Cache_ThanksDay::getInSiteNum($uid);
		if ($num >= 5) {
			$result['content'] = self::TXT003;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//每人每天只能入驻同一个好友工地一次
//		$incSameFidSite = Hapyfish2_Island_Event_Cache_ThanksDay::getSameFidSite($uid, $fid);
//		if ($incSameFidSite == false) {
//			$result['content'] = self::TXT009;
//			$resultVo = array('result' => $result);
//			return $resultVo;
//		}
		
		//判断当前工地是否可以入驻
		$siteData = Hapyfish2_Island_Event_Cache_ThanksDay::getSiteById($fid, $siteId);
		if ($siteData == false) {
    		$result['status'] = 'serverWord_101';
    		$resultVo = array('result' => $result);
    		return $resultVo;
		}

		//入驻好友工地
		Hapyfish2_Island_Event_Cache_ThanksDay::incFidSite($fid, $uid, $siteId);

		//入驻好友工地统计
		try {
			$db = Hapyfish2_Island_Event_Dal_ThanksDay::getDefaultInstance();
			$db->incSite($uid, $fid, time());
		} catch (Exception $e) {}
		
		//记录入驻次数
		Hapyfish2_Island_Event_Cache_ThanksDay::addInSiteNum($uid);
		
		//入驻好友工地成功,自己立刻获得10爱心
		Hapyfish2_Island_Event_Cache_ThanksDay::renewHasLove($uid, 10);
		
		$result['status'] = 1;
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	
	/**
	 * @兑换礼包
	 * @param int $uid
	 * @param int $id
	 * @return Array
	 */
	public static function thDayExch($uid, $id)
	{
		$result = array('status' => -1);
		
		//礼包ID不能为空,切只能是三个礼包中的一个
		if (!in_array($id, array(1, 2, 3))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//获取用户的爱心值
		$hasLove = Hapyfish2_Island_Event_Cache_ThanksDay::hasLove($uid);
		
		//获取礼包信息
		$giftList = Hapyfish2_Island_Event_Cache_ThanksDay::getGiftList();
		
		//获取当前要兑换的物品
		foreach ($giftList as $key => $value) {
			if ($key == $id) {
				$data = $value;
				break;
			}
		}	

		//判断用户的爱心是否足够
		if ($hasLove < $data['needLove']) {
			$result['content'] = self::TXT004;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
	
		//发东西
		$compensation = new Hapyfish2_Island_Bll_Compensation();

		if ($data['cid'] && $data['num']) {
			foreach ($data['cid'] as $cidKey => $cid) {
				$compensation->setItem($cid, $data['num'][$cidKey]);
			}
		}
			
		$ok = $compensation->sendOne($uid, '恭喜你用' . $data['needLove'] . '爱心兑换了：');
		
		if ($ok) {
			//减少用户爱心值
			Hapyfish2_Island_Event_Cache_ThanksDay::renewHasLove($uid, -$data['needLove']);
			
			info_log($uid . ',' . $id . ',' . $data['needLove'], 'thDayExch');
		} else {
			info_log($uid . ',' . $id . ',' . $data['needLove'], 'thDayExchErr');
			
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$result['status'] = 1;
        $result['itemBoxChange'] = true;
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	
	/**
	 * @排行榜
	 * @return Array
	 */
	public static function thDayRank()
	{
		$list = Hapyfish2_Island_Event_Cache_ThanksDay::getRanList();
		
		$resultVo['list'] = $list;
		
		return $resultVo;
	}
	
	/**
	 * @购买爱心值
	 * @param int $uid
	 * @param int $love
	 * @return Array
	 */
	public static function thDayBuyLove($uid, $love)
	{
		$result = array('status' => -1);
		
		//购买的爱心值不能少0
		if ($love <= 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($love > $userGold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		
    		return $resultVo;
		}
		
		$nowTime = time();
		
		//增加爱心
		Hapyfish2_Island_Event_Cache_ThanksDay::renewHasLove($uid, $love);	
		//统计购买爱心值
		$db = Hapyfish2_Island_Event_Dal_ThanksDay::getDefaultInstance();
	
		$before = $db->getBuyLove($uid);
	
		if (!$before) {
			try {
				$db->incBuyLove($uid, $love);
			} catch (Exception $e) {}			
		} else {
			try {
				$db->addBuyLove($uid, $love);
			} catch (Exception $e) {}
		}
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $love,
						'summary' => self::TXT008 . $love,
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 0);

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
		$result['goldChange'] = -$love;
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	
	/**
	 * @宝石补齐雕像
	 * @param int $uid
	 */
	/**
	public static function thDayComplete($uid)
	{
		$result = array('status' => -1);
		
		//获取建筑等级
		$plantLevl = Hapyfish2_Island_Event_Cache_ThanksDay::getPlantLevel($uid);
		if ($plantLevl >= 5) {
			$result['content'] = self::TXT007;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
        
		//获取雕像基础信息
		$plantVo = Hapyfish2_Island_Event_Cache_ThanksDay::aryPlant();
		
		//获取用户用的最大爱心值
		$hasLoveMax = Hapyfish2_Island_Event_Cache_ThanksDay::hasLoveMax($uid);
		
		//补齐需要爱心值
		$needGold = $plantVo[4]['needLove'] - $hasLoveMax;

    	//获得用户gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		//宝石不足
		if ($needGold > $userGold) {
    		$result['content'] = 'serverWord_140';
    		$resultVo = array('result' => $result);
    		
    		return $resultVo;
		}
		
		//升级用户雕像为5级
		Hapyfish2_Island_Event_Cache_ThanksDay::addPlantLevel($uid, 5);
		
		//补齐雕像花费的宝石等于获得的爱心值数
		Hapyfish2_Island_Event_Cache_ThanksDay::renewHasLove($uid, $needGold);
		
		$nowTime = time();
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $needGold,
						'summary' => '补齐感恩节雕像',
						'user_level' => $userLevel,
						'create_time' => $nowTime,
						'cid' => '',
						'num' => 0);

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
		
		//记录拥有5级沙雕的人
		info_log($uid . ',' . 'com', 'sculpture-5');
		
		$result['status'] = 1;
		$result['goldChange'] = -$needGold;
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	*/
	
	/**
	 * @感恩节发雕像
	 */
	public static function sendPlant()
	{
		//5星沙雕建筑
		$db = Hapyfish2_Island_Event_Dal_ThanksDay::getDefaultInstance();
		$uids = $db->getAllUser();
		
		$cid = 125332;
		$com = new Hapyfish2_Island_Bll_Compensation();
		
		foreach ($uids as $uid) {					
			$com->setItem($cid, 1);
			$ok = $com->sendOne($uid, '感恩节建筑：');
			if($ok){
				info_log($uid, 'thday-' . $cid);
			}
		}
				
		return true;
	}
	
	/**
	 * @排行榜奖励
	 */
	public static function sendRankPlant()
	{
		$send = new Hapyfish2_Island_Bll_Compensation();
		
		$nowTime = time();
		
		$list = Hapyfish2_Island_Event_Cache_ThanksDay::getRanList();
		foreach ($list as $listArr) {	
			$ok2 = Hapyfish2_Island_Bll_GiftPackage::addGift($listArr['uid'], 26441, 10);
			$ok2 = Hapyfish2_Island_Bll_GiftPackage::addGift($listArr['uid'], 67441, 10);
			$ok2 = Hapyfish2_Island_Bll_GiftPackage::addGift($listArr['uid'], 74841, 10);
			$ok2 = Hapyfish2_Island_Bll_GiftPackage::addGift($listArr['uid'], 124832, 1);
			
			$title = '感恩节排行榜奖励：船只加速卡IIIx10,一键收取卡x10,双倍经验卡x10,礼物蛋糕店x1';
			
			//发feed
			$feed = array('uid' => $listArr['uid'],
						'template_id' => 0,
						'actor' => $listArr['uid'],
						'target' => $listArr['uid'],
						'type' => 3,
						'title' => array('title' => $title),
						'create_time' => $nowTime);
		
			Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
			
			if($ok2){
				info_log($listArr['uid'], 'thdayRank');
			}
		}
		
		if ($ok2) {
			return count($list);
		} else {
			return false;
		};
	}
	
}