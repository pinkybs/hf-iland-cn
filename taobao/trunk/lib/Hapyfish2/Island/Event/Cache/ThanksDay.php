<?php

/**
 * Event ThanksDay
 *
 * @package    Island/Event/Cache
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/14    zhangli
*/
class Hapyfish2_Island_Event_Cache_ThanksDay
{
	const LIFE_TIME_ONE_MONTH = 2592000;

	/**
	 * @获取用户的雕像等级
	 * @param int $uid
	 * @return Array
	 */
	public static function getPlantLevel($uid)
	{
		$key = 'ev:thday:build:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		if ($data === false) {
			$data = 0;
		}

		return $data;
	}

	/**
	 * @升级雕像
	 * @param int $uid
	 * @param int $plantLevl
	 */
	public static function addPlantLevel($uid, $plantLevl)
	{
		$key = 'ev:thday:build:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $plantLevl, self::LIFE_TIME_ONE_MONTH);
	}

	/**
	 * @获取用户拥有的爱心值(兑换物品)
	 * @param int $uid
	 * @return int
	 */
	public static function hasLove($uid)
	{
		$key = 'ev:thday:love:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);		
		$data = $cache->get($key);

		if ($data === false) {
			$data = 0;
		}

		return $data;
	}

	/**
	 * @更新用户的爱心值
	 * @param int $uid
	 * @param int $love
	 */
	public static function renewHasLove($uid, $love)
	{
		$key = 'ev:thday:love:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		$data += $love;
		$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);

		//用户每次爱心值增加都会更新用户的爱心值max
		if ($love > 0) {
			self::addLoveMax($uid, $love);
		}
	}

	/**
	 * @5级雕像每天可以获取100爱心
	 * @param int $uid
	 * @return boolean
	 */
	public static function canAddLove($uid)
	{
		$key = 'ev:thday:addlove:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		return $data;
	}

	/**
	 * @5级雕像每天获得100爱心值标记
	 * @param int $uid
	 */
	public static function addLoveFlag($uid)
	{
		//每天的23:59:59清空
		$logDate = date('Y-m-d');
		$dtDate = $logDate . ' 23:59:59';
		$falseTime = strtotime($dtDate);

		$key = 'ev:thday:addlove:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, 1, $falseTime);
	}

	/**
	 * @获取用户拥有的爱心值max(升级建筑)
	 * @param int $uid
	 * @return int
	 */
	public static function hasLoveMax($uid)
	{
		$key = 'ev:thday:loveMax:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		if ($data === false) {
			$data = 0;
		}

		return $data;
	}

	/**
	 * @增加用户的爱心值max
	 * @param int $uid
	 * @param int $love
	 */
	public static function addLoveMax($uid, $love)
	{
		$key = 'ev:thday:loveMax:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		$db = Hapyfish2_Island_Event_Dal_ThanksDay::getDefaultInstance();
		
		if ($data === false) {
			$data = $db->getLoveMax($uid);
		}
		
		if ($data === false) {
			$data = $love;
		} else {
			$data += $love;
		}
		
		$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);

		//记录用户爱心值
		$maxLove = $db->getLoveMax($uid);
	
		if (!$maxLove) {
			try {
				$db->incLoveMax($uid, $data);
			} catch (Exception $e) {}			
		} else {
			try {
				$db->renewLoveMax($uid, $data);
			} catch (Exception $e) {}
		}
		
		//用户每次爱心值max变动都去判断是否会进入排行榜
		self::reloadRandList($uid, $data);
	}

	/**
	 * @根据机器人ID获取机器人信息
	 * @param int $id
	 * @return Array
	 */
	public static function getRobotData($id)
	{
		$key = 'ev:thday:robot';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$dataVo = $cache->get($key);

		if ($dataVo === false) {
			$dataVo[1]['needGold'] = 3;
			$dataVo[1]['time'] = 3600 * 4;

			$dataVo[2]['needGold'] = 8;
			$dataVo[2]['time'] = 3600 * 8;


			$dataVo[3]['needGold'] = 28;
			$dataVo[3]['time'] = 3600 * 24;

			$cache->set($key, $dataVo, self::LIFE_TIME_ONE_MONTH);
		}

		foreach ($dataVo as $rid => $data) {
			if ($rid == $id) {
				$robotData = $data;
				break;
			}
		}

		return $robotData;
	}

	/**
	 * @获取当前工地信息
	 * @param int $uid
	 * @param int $siteId
	 * @return boolean
	 */
	public static function getSiteById($uid, $siteId)
	{
		$key = 'ev:thday:site:' . $siteId . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		if ($data === false) {
			$result = true;
		} else {
			$result = false;
		}

		return $result;
	}

	/**
	 * @机器人入驻工地
	 * @param int $uid
	 * @param int $siteId
	 * @param int $id
	 * @param array $robotData
	 */
	public static function incSite($uid, $siteId, $id, $robotData)
	{
		$nowTime = time();

		$data['uid'] = $id;
		$data['smallFace'] = '';
		$data['reTime'] = $nowTime + $robotData['time'];

		$key = 'ev:thday:site:' . $siteId . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data, $data['reTime']);

		//设置次工地可以获得爱心值标记
		self::incSiteFlag($uid, $siteId, $id, $nowTime);

	}

	/**
	 * @入驻好友工地
	 * @param int $fid
	 * @param int $uid
	 * @param int $siteId
	 */
	public static function incFidSite($fid, $uid, $siteId)
	{
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);

		$nowTime = time();
		$endTime = $nowTime + 3600 * 4;

		$data['uid'] = $uid;
		$data['smallFace'] = $user['figureurl'];
		$data['reTime'] = $endTime;

		$key = 'ev:thday:site:' . $siteId . $fid;
		$cache = Hapyfish2_Cache_Factory::getMC($fid);
		$cache->set($key, $data, $data['reTime']);

		//设置次工地可以获得爱心值标记
		self::incSiteFlag($fid, $siteId, 4, $nowTime);
		
		//每个人每天只能入驻同一个好友一个工地
		//self::incSameFidSite($uid, $fid);
	}

	/**
	 * @设置每个人每天只能入驻同一个好友一个工地
	 * @param int $uid
	 * @param int $fid
	 */
	public static function incSameFidSite($uid, $fid)
	{
		//每天的23:59:59清空
		$logDate = date('Y-m-d');
		$dtDate = $logDate . ' 23:59:59';
		$endTime = strtotime($dtDate);
		
		$key = 'ev:thday:site:once:' . $uid . $fid;
		$cache = Hapyfish2_Cache_Factory::getMC($fid);
		$cache->set($key, 1, $endTime);
	}
	
	/**
	 * @获取是否入驻过这个好友的工地
	 * @param int $uid
	 * @param int $fid
	 * @return boolean
	 */
	public static function getSameFidSite($uid, $fid)
	{
		$key = 'ev:thday:site:once:' . $uid . $fid;
		$cache = Hapyfish2_Cache_Factory::getMC($fid);
		$data = $cache->get($key);
		
		$ok = true;
		
		if ($data == 1) {
			$ok = false;
		}
		
		return $ok;
	}
	
	/**
	 * @设置次工地可以获得爱心值标记
	 * @param int $uid
	 * @param int $siteId
	 * @param int $tid
	 * @param int $nowTime
	 */
	public static function incSiteFlag($fid, $siteId, $tid, $nowTime)
	{
		$data = array('tid' => $tid, 'start' => $nowTime);

		$key = 'ev:thday:site:flag:' . $siteId . $fid;
		$cache = Hapyfish2_Cache_Factory::getMC($fid);
		$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);
	}

	/**
	 * @获取工地可以获取的爱心值信息
	 * @param int $uid
	 * @param int $siteId
	 * @return Array
	 */
	public static function getSiteFlag($uid, $siteId)
	{
		$key = 'ev:thday:site:flag:' . $siteId . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
	
		return $data;
	}

	/**
	 * @删除可以获取的爱心值信息
	 * @param int $uid
	 * @param int $siteId
	 */
	public static function resetSiteFlag($uid, $siteId)
	{
		$key = 'ev:thday:site:flag:' . $siteId . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
	}

	/**
	 * @刷新工地可以获取的爱心值信息
	 * @param int $uid
	 * @param int $siteId
	 * @param int $newStart
	 */
	public static function refreshSiteFlag($uid, $siteId, $newStart)
	{
		$key = 'ev:thday:site:flag:' . $siteId . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		$data['start'] = $newStart;

		$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);
	}

	/**
	 * @解雇好友
	 * @param int $uid
	 * @param int $siteId
	 */
	public static function delSite($uid, $siteId)
	{
		$key = 'ev:thday:site:' . $siteId . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);

		//清除工地预设工资
		self::resetSiteFlag($uid, $siteId);
	}

	/**
	 * @获取用户入驻好友工地次数
	 * @param int $uid
	 */
	public static function getInSiteNum($uid)
	{
		$key = 'ev:thday:site:num:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$num = $cache->get($key);

		if ($num === false) {
			$num = 0;

			//每天的23:59:59清空
			$logDate = date('Y-m-d');
			$dtDate = $logDate . ' 23:59:59';
			$endTime = strtotime($dtDate);

			$cache->set($key, $num, $endTime);
		}

		return $num;
	}

	/**
	 * @记录入驻好友工地次数
	 * @param int $uid
	 */
	public static function addInSiteNum($uid)
	{
		$key = 'ev:thday:site:num:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$num = $cache->get($key);

		if ($num === false) {
			$num = 0;
		}

		//每天的23:59:59清空
		$logDate = date('Y-m-d');
		$dtDate = $logDate . ' 23:59:59';
		$endTime = strtotime($dtDate);

		$num++;
		$cache->set($key, $num, $endTime);
	}
	
	/**
	 * @工地列表
	 * @param int $uid
	 * @return Array
	 */
	public static function siteList($uid)
	{
		$nowTime = time();
		$incLove = 0;
		
		$keyOne = 'ev:thday:site:' . 1 . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$dataOne = $cache->get($keyOne);

		if ($dataOne === false) {
			$dataOne = array();

			//判断是否有爱心值可以收取
			$siteOneLove = self::getSiteFlag($uid, 1);

			if ($siteOneLove) {
				if (in_array($siteOneLove['tid'], array(1, 2, 3))) {
					if ($siteOneLove['tid'] == 1) {
						$addLove = 15;
					} else if ($siteOneLove['tid'] == 2) {
						$addLove = 30;
					} else {
						$addLove = 90;
					}
				} else {
					$addLove = 10;
				}
				
				$incLove += $addLove;
				
				//清空可以收取的爱心值
				self::resetSiteFlag($uid, 1);
			}
		} else {
			//判断是否有爱心值可以收取
			$siteOneLove = self::getSiteFlag($uid, 1);
			$revot = $nowTime - $siteOneLove['start'];

			$addLoveline = floor($revot / (3600 * 4));
			//$addLoveline = floor($revot / 600);

			if ($addLoveline > 0) {
			
				if (in_array($dataOne['tid'], array(1, 2, 3))) {
					$addLove = $addLoveline * 15;
				} else {
					$addLove = $addLoveline * 10;
				}
			}

			$incLove += $addLove;
 		
			if ($addLove > 0) {
				//更新可领取的爱心数据
				$startOneTime = $siteOneLove['start'] + $addLoveline * 3600 * 4;
				//$startOneTime = $siteOneLove['start'] + $addLoveline * 600;
				self::refreshSiteFlag($uid, 1, $startOneTime);
			}
			
			$dataOne['reTime'] -= $nowTime;
		}

		$keyTwo = 'ev:thday:site:' . 2 . $uid;
		$dataTwo = $cache->get($keyTwo);

		if ($dataTwo === false) {
			$dataTwo = array();

			//判断是否有爱心值可以收取
			$siteTwoLove = self::getSiteFlag($uid, 2);

			if ($siteTwoLove) {
				if (in_array($siteTwoLove['tid'], array(1, 2, 3))) {
					if ($siteTwoLove['tid'] == 1) {
						$addTwoLove = 15;
					} else if ($siteTwoLove['tid'] == 2) {
						$addTwoLove = 30;
					} else {
						$addTwoLove = 90;
					}
				} else {
					$addTwoLove = 10;
				}
				
				$incLove += $addTwoLove;
				
				//清空可以收取的爱心值
				self::resetSiteFlag($uid, 2);
			}
		} else {
			//判断是否有爱心值可以收取
			$siteTwoLove = self::getSiteFlag($uid, 2);
			$revotTwo = $nowTime - $siteTwoLove['start'];

			$addLovelineTwo = floor($revotTwo / (3600 * 4));
			//$addLovelineTwo = floor($revotTwo / 600);
	
			if ($addLovelineTwo > 0) {
				if (in_array($siteTwoLove['tid'], array(1, 2, 3))) {
					$addTwoLove = $addLovelineTwo * 15;
				} else {
					$addTwoLove = $addLovelineTwo * 10;
				}
			}
			
			$incLove += $addTwoLove;
			
			if ($addTwoLove > 0) {
				//更新可领取的爱心数据
				$startTwoTime = $siteTwoLove['start'] + $addLovelineTwo * 3600 * 4;
				//$startTwoTime = $siteTwoLove['start'] + $addLovelineTwo * 600;
				self::refreshSiteFlag($uid, 2, $startTwoTime);
			}
			
			$dataTwo['reTime'] -= $nowTime;
		}

		$keyThree = 'ev:thday:site:' . 3 . $uid;
		$dataThree = $cache->get($keyThree);

		if ($dataThree === false) {
			$dataThree = array();

			//判断是否有爱心值可以收取
			$siteThreeLove = self::getSiteFlag($uid, 3);

			if ($siteThreeLove) {
				if (in_array($siteThreeLove['tid'], array(1, 2, 3))) {
					if ($siteThreeLove['tid'] == 1) {
						$addThreeLove = 15;
					} else if ($siteThreeLove['tid'] == 2) {
						$addThreeLove = 30;
					} else {
						$addThreeLove = 90;
					}
				} else {
					$addThreeLove = 10;
				}
				
				$incLove += $addThreeLove;
				
				//清空可以收取的爱心值
				self::resetSiteFlag($uid, 3);
			}
		} else {
			//判断是否有爱心值可以收取
			$siteThreeLove = self::getSiteFlag($uid, 3);
			$revotThree = $nowTime - $siteThreeLove['start'];			

			$addLovelineThree = floor($revotThree / (3600 * 4));
			//$addLovelineThree = floor($revotThree / 600);
	
			if ($addLovelineThree > 0) {
				if (in_array($siteThreeLove['tid'], array(1, 2, 3))) {
					$addLoveThree = $addLovelineThree * 15;
				} else {
					$addLoveThree = $addLovelineThree * 10;
				}
			}
	
			$incLove += $addLoveThree;
			
			if ($addLoveThree > 0) {
				//更新可领取的爱心数据
				$startTimeThree = $siteThreeLove['start'] + $addLovelineThree * 3600 * 4;
				//$startTimeThree = $siteThreeLove['start'] + $addLovelineThree * 600;
				self::refreshSiteFlag($uid, 3, $startTimeThree);
			}
			
			$dataThree['reTime'] -= $nowTime;
		}

		$keyFour = 'ev:thday:site:' . 4 . $uid;
		$dataFour = $cache->get($keyFour);

		if ($dataFour === false) {
			$dataFour = array();

			//判断是否有爱心值可以收取
			$siteFourLove = self::getSiteFlag($uid, 4);

			if ($siteFourLove) {
				if (in_array($siteFourLove['tid'], array(1, 2, 3))) {
					if ($siteFourLove['tid'] == 1) {
						$addLoveFour = 15;
					} else if ($siteFourLove['tid'] == 2) {
						$addLoveFour = 30;
					} else {
						$addLoveFour = 90;
					}
				} else {
					$addLoveFour = 10;
				}
				
				$incLove += $addLoveFour;
				
				//清空可以收取的爱心值
				self::resetSiteFlag($uid, 4);
			}
		} else {
			//判断是否有爱心值可以收取
			$siteFourLove = self::getSiteFlag($uid, 4);
			$revotFour = $nowTime - $siteFourLove['start'];

			$addLovelineFour = floor($revotFour / (3600 * 4));
			//$addLovelineFour = floor($revotFour / 600);

			if ($addLovelineFour > 0) {
				if (in_array($dataFour['tid'], array(1, 2, 3))) {
					$addLovelineFour = $addLovelineFour * 15;
				} else {
					$addLove = $addLovelineFour * 10;
				}
			}
	
			$incLove += $addLovelineFour;
			
			if ($addLovelineFour > 0) {
				//更新可领取的爱心数据
				$startTimeFour = $siteFourLove['start'] + $addLovelineFour * 3600 * 4;
				//$startTimeFour = $siteFourLove['start'] + $addLovelineFour * 600;
				self::refreshSiteFlag($uid, 4, $startTimeFour);
			}
			
			$dataFour['reTime'] -= $nowTime;
		}

		//增加爱心值
		if ($incLove > 0) {
			self::renewHasLove($uid, $incLove);
		}
		
		$dataVO[] = $dataOne;
		$dataVO[] = $dataTwo;
		$dataVO[] = $dataThree;
		$dataVO[] = $dataFour;

		return $dataVO;
	}

	/**
	 * @获取兑换列表
	 * @return Array
	 */
	public static function getGiftList()
	{
		$key = 'ev:thday:gift';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');	
		$data = $cache->get($key);

		if ($data === false) {
			$data[1]['needLove'] = 260;
			$data[1]['cid'] = array(124732, 26441);
			$data[1]['num'] = array(1, 3);

			$data[2]['needLove'] = 550;
			$data[2]['cid'] = array(124532, 67441, 74841);
			$data[2]['num'] = array(1, 5, 5);

			$data[3]['needLove'] = 1130;
			$data[3]['cid'] = array(124632, 126732);
			$data[3]['num'] = array(1, 1);

			$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);
		}

		return $data;
	}

	/**
	 * @刷新排行榜,前100名写入缓存
	 * @param int $uid
	 * @param int $love
	 */
	public static function reloadRandList($uid, $love)
	{
		$ok = false;

		//获取排行榜数据
		$data = self::getRanList();

		//存储原排行榜,用于计算升降值
		$dataOld = $data;

		//判断用户是否已经在排行榜中
		$inRank = false;
		foreach ($data as $rank) {
			if ($uid == $rank['uid']) {
				$inRank = true;
				break;
			}
		}

		if (count($data) < 100) {
			if ($inRank == true) {
				//当前排行榜人数不足100,但是用户已经在名单中,进行更新
				$ok = true;
	
				foreach ($data as $rankKey => $rankData) {
					if ($rankData['uid'] == $uid) {
						$data[$rankKey]['loveLevel'] = $love;
						break;
					}
				}
			} else {
				//当前排行榜人数不够100并且用户不在当前排行榜中,直接加入
				$ok = true;
	
				//获取用户等级
				$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
				$userLevel = $userLevelInfo['level'];
	
				$user = Hapyfish2_Platform_Bll_User::getUser($uid);
	
				$newData['uid'] = $uid;
				$newData['name'] = $user['name'];
				$newData['level'] = $userLevel;
				$newData['smallFace'] = $user['figureurl'];
				$newData['loveLevel'] = $love;
				$newData['trend'] = 1;
				$newData['homeurl'] = 'http://jianghu.taobao.com/u/' . base64_encode($user['puid']) . '/front.htm';
	
				array_push($data, $newData);
			}
		} else {
			//已经在排行榜中
			if ($inRank == true) {
				$ok = true;
				foreach ($data as $incKey => $incData) {
					if ($incData['uid'] == $uid) {
						$data[$incKey]['loveLevel'] = $love;
						break;
					}
				}
			} else {
				//获取排行榜最后一名,如果用户的爱心值多余最后一名则进行更新
				$minLove = self::getMinLine();
	
				if ($love > $minLove) {
					$ok = true;
	
					//获取用户等级
					$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
					$userLevel = $userLevelInfo['level'];
	
					$user = Hapyfish2_Platform_Bll_User::getUser($uid);
	
					$newData['uid'] = $uid;
					$newData['name'] = $user['name'];
					$newData['level'] = $userLevel;
					$newData['smallFace'] = $user['figureurl'];
					$newData['loveLevel'] = $love;
					$newData['trend'] = 1;
					$newData['homeurl'] = 'http://jianghu.taobao.com/u/' . base64_encode($user['puid']) . '/front.htm';
	
					array_push($data, $newData);
				}
			}
		}

		if ($ok == true) {
			foreach ($data as $keyRank => $value){
				$RankUid[$keyRank] = $value['uid'];
				$RankName[$keyRank] = $value['name'];
				$RankLevel[$keyRank] = $value['level'];
				$RankFace[$keyRank] = $value['smallFace'];
				$RankLove[$keyRank] = $value['loveLevel'];
				$RankTrend[$keyRank] = $value['trend'];
				$RankPage[$keyRank] = $value['homeurl'];
			}

			array_multisort($RankLove, SORT_DESC, $data);

			//删除多余100名的数据
			if (count($data) > 100) {
				foreach ($data as $keyRK => $valRK) {
					if ($keyRK >= 100) {
						unset($data[$keyRK]);
					}
				}
			}
			
			//计算每名用户的名次升降
			foreach ($data as $newKey => $newRK) {
				foreach ($dataOld as $oldKey => $oldVal) {
					if ($oldVal['uid'] == $newRK['uid']) {
						if ($newKey > $oldKey) {
							$data[$newKey]['trend'] = 0;
						} else {
							$data[$newKey]['trend'] = 1;
						}
						break;
					}
				}
			}
			
			if (!empty($data[0])) {
				//刷新排行榜
				$key = 'ev:thday:rank:new';
				$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
				$cache->set($key, $data, self::LIFE_TIME_ONE_MONTH);
	
				//刷新最后一名的爱心数
				$newMinLine = end($data);
				self::renewMinLine($newMinLine['loveLevel']);
			} else {
                info_log('err-data:' . json_encode($data), 'thanksDay');
			}
		}
	}

	/**
	 * @获取排行榜最后一名
	 * @return int
	 */
	public static function getMinLine()
	{
		$key = 'ev:thday:rank:min';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$minLine = $cache->get($key);

		if ($minLine === false) {
			$rankList = self::getRanList();
			$minList = end($rankList);
			$minLine = $minList['loveLevel'];

			$cache->set($key, $minLine, self::LIFE_TIME_ONE_MONTH);
		}

		return $minLine;
	}

	/**
	 * @刷新排行榜最后一名爱心值
	 * @param int $minLine
	 */
	public static function renewMinLine($minLine)
	{
		$key = 'ev:thday:rank:min';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->set($key, $minLine, self::LIFE_TIME_ONE_MONTH);
	}

	/**
	 * @排行榜
	 * @return Array
	 */
	public static function getRanList()
	{
		$key = 'ev:thday:rank:new';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$data = $cache->get($key);

		if ($data === false) {
			$data = array();
		}

		return $data;
	}

	/**
	 * @沙雕的数据
	 * @return Array
	 */
	public static function aryPlant()
	{
		$aryPlant = array(array('level' => 1, 'needLove' => 260),
						  array('level' => 2, 'needLove' => 550),
						  array('level' => 3, 'needLove' => 870),
						  array('level' => 4, 'needLove' => 1220),
						  array('level' => 5, 'needLove' => 1600));

		return 	$aryPlant;
	}

}