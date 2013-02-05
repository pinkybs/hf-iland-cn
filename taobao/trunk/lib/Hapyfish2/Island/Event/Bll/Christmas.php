<?php

/**
 * Event Christmas
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/25    zhangli
*/
class Hapyfish2_Island_Event_Bll_Christmas
{
	const TXT001 = '不能重复领取！';
	const TXT002 = '所需好友数量不足，不能领取！';
	const TXT003 = '恭喜你获得：';
	const TXT004 = '所需物品不足，不能领取！';
	const TXT005 = '对不起，您的卡片不足~';
	
	/**
	 * @初始化面板
	 * @param int $uid
	 * @return Array
	 */
	public static function christmasinit($uid)
	{
		$result = array('status' => -1);
		
		$nowTime = time();
									
		//第一阶段开放截止时间12-07 24   1323273599
		//第二阶段开放截止时间12-15 24   1323964799
		//第三阶段开放截止时间12-22 24   1324569599
		$mapOneTime = strtotime('2011-12-27 23:59:59');
		$mapTwoTime = strtotime('2011-12-27 23:59:59');
		$mapThreeTime = strtotime('2011-12-27 23:59:59');

		$timeOne = $mapOneTime - $nowTime;
		$timeTwo = $mapTwoTime - $nowTime;
		$timeThree = $mapThreeTime - $nowTime;
		
		$merryChristmasMapVo = array(array('mapId' => 1, 'isopen' => 0, 'time' => '1323273599', 'start' => '1322212676'),
									array('mapId' => 2, 'isopen' => 0, 'time' => '1323964799', 'start' => '1322212676'),
									array('mapId' => 3, 'isopen' => 0, 'time' => '1323964799', 'start' => '1322212676'));							

		//点击任务状态
		$merryChristmasTaskVo = Hapyfish2_Island_Event_Cache_Christmas::getChristmasOnceRequest($uid);
		
		foreach ($merryChristmasMapVo as $key => $merryChristmasMap) {
			if (($nowTime >= $merryChristmasMap['start']) && $nowTime <= $merryChristmasMap['time']) {
				$merryChristmasMapVo[$key]['isopen'] = 1;
			} else if ($nowTime > $merryChristmasMap['time']) {
				$merryChristmasMapVo[$key]['isopen'] = 2;
			} else {
				$merryChristmasMapVo[$key]['isopen'] = 0;
			}
			
			if ($key == 0) {
				$merryChristmasMapVo[$key]['time'] = $timeOne;
			} else if ($key == 1) {
				$merryChristmasMapVo[$key]['time'] = $timeTwo;
			} else {
				$merryChristmasMapVo[$key]['time'] = $timeThree;
			}
			
			unset($merryChristmasMapVo[$key]['start']);
		}

		$result['status'] = 1;
		$resultVo['result'] = $result;
		$resultVo['merryChristmasMapVo'] = $merryChristmasMapVo;
		$resultVo['merryChristmasTaskVo'] = $merryChristmasTaskVo;
		return $resultVo;
	}
	
	/**
	 * @获取需求的建筑
	 * @param int $uid
	 * @return Array
	 */
	public static function christmasGetPlant($uid)
	{
		//用户拥有建筑
		$merryChristmasItemVo = array();
		$list = array(127041, 127141, 66821, 64932, 65032, 65132, 65531, 65631, 65731, 128032, 128132, 128432, 128532, 128632,
					128732, 129032, 129132, 129232, 129332, 129432, 129532, 129832, 130732, 130632, 130832, 130932, 131032, 130532,
					131132, 131232, 131332, 131532, 131432);

		foreach ($list as $lkey => $cid) {
			$itemType = substr($cid, -2, 1);
			
			if ($itemType == 1) {
				$num = 0;
				$db = Hapyfish2_Island_Dal_Background::getDefaultInstance();
				$num = $db->getOneNum($uid, $cid);
				
				$merryChristmasItemVo[$lkey]['cid'] = (int)$cid;
				$merryChristmasItemVo[$lkey]['num'] = (int)$num;
			} else if ($itemType == 2) {
				$num = 0;
				$db = Hapyfish2_Island_Dal_Building::getDefaultInstance();
				$num = $db->getOneNum($uid, $cid);
				
				$merryChristmasItemVo[$lkey]['cid'] = (int)$cid;
				$merryChristmasItemVo[$lkey]['num'] = (int)$num;
			} else if ($itemType == 3) {
				$num = 0;
				$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
				$num = $db->getOneNum($uid, $cid);
				
				$merryChristmasItemVo[$lkey]['cid'] = (int)$cid;
				$merryChristmasItemVo[$lkey]['num'] = (int)$num;
			} else if ($itemType == 4) {
				$num = 0;
				$db = Hapyfish2_Island_Dal_Card::getDefaultInstance();
				$num = $db->getOneNum($uid, $cid);
				
				$merryChristmasItemVo[$lkey]['cid'] = (int)$cid;
				$merryChristmasItemVo[$lkey]['num'] = (int)$num;
			}
		}
		
		$result = array('status' => 1);
		$resultVo['result'] = $result;
		$resultVo['merryChristmasItemVo'] = $merryChristmasItemVo;
		return $resultVo;
	}
	
	/**
	 * @领取奖励
	 * @param int $uid
	 * @param int $taskId
	 * return Array
	 */
	public static function christmasTask($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//任务面板ID错误
		if (!in_array($taskId, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		switch ($taskId) {
			case 1:
				//第一个任务兑换艾纽
				$data = self::getCollectGift($uid, $taskId);
			break;
			case 2:
				//第二个任务用绿球兑换小鹿
				$data = self:: getChrismasFawn($uid, $taskId);
			break;
			case 3:
				//第三个任务兑换爱斯基摩冰屋
				$data = self::getChrismasIceHouse($uid, $taskId);
			break;
			case 4:
				//第四个任务兑换牛仔女孩
				$data = self::getWesternGirl($uid, $taskId);
			break;
			case 5:
				//第五个任务用红球兑换圣诞麋鹿-鲁道夫
				$data = self:: getRudolf($uid, $taskId);
			break;
			case 6:
				//第六个任务兑换啤酒屋
				$data = self::getBeerHouse($uid, $taskId);
			break;
			case 7:
				//第七个任务兑换圣诞车
				$data = self::getChrismasCart($uid, $taskId);
			break;
			case 8:
				//第八个任务兑换圣诞雪人
				$data = self::getChrismasSnowMan($uid, $taskId);
			break;
			case 9:
				//第九个任务兑换圣诞树
				$data = self::getChrismasTree($uid, $taskId);
			break;
			case 10:
				//第十个任务兑换悬空城堡
				$data = self::getChrismasCastle($uid, $taskId);
			break;
		}
		
		//任务兑换失败
		if ($data['status'] != 1) {
			$result['content'] = $data['content'];
			$resultVo = array('result' => $result);
			return $resultVo;
		}

		$result['status'] = $data['status'];
		$result['itemBoxChange'] = true;
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @圣诞节收集
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getCollectGift($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getCollectGiftFlag($uid, $taskId);
	
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表(下雪玩具店,雪橇车,雪地靴商店,积雪城堡,圣诞水晶球)
		$list = array(array(64932, 65032, 65132), array(128132), array(128032), array(65531, 65631, 65731), array(66821));
		foreach ($list as $cids) {
			$id = 0;
			foreach ($cids as $cid) {
				$hasNum = 0;
				$itemType = substr($cid, -2, 1);
			
				if ($itemType == 1) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Background::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				} else if ($itemType == 2) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Building::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				} else if ($itemType == 3) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				} else if ($itemType == 4) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Card::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				}

				$id += $hasNum;
		
				if ($id > 0) {
					break;
				}
			}

			//收集物品不足
			if ($id <= 0) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//爱基斯摩女孩-艾纽
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(128532, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addCollectGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasCollectOK');
		} else {
			info_log($uid, 'chrismasCollentErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @兑换小鹿
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getChrismasFawn($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getChrismasFawnFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		
		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ( $item['count'] > 0 ) {
					$cardVo[$cid] = $item['count'];
				}
			}
		}
		
		$hasCount = 0;
		//圣诞绿球 127041
		foreach ($cardVo as $cardID => $cardNum) {
			if (127041 == $cardID) {
				$hasCount = $cardNum;
				break;
			}
		}
		
		//圣诞绿球不足
		if ($hasCount < 30) {
			$result['content'] = self::TXT004;
			return $result;
		}
		
		//发放圣诞麋鹿彗星
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(128432, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addChrismasFawnFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasFawnOK');
		} else {
			info_log($uid, 'chrismasFawnErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
		
	}
	
	/**
	 * @兑换圣诞节冰屋
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getChrismasIceHouse($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getChrismasIceHouseFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表
		$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		
		//爱基斯摩女孩-艾纽、圣诞麋鹿彗星
		$list = array(128532, 128432);
		foreach ($list as $cid) {
			$id = 0;
			$id = $db->getOneId($uid, $cid);
			//收集物品不足
			if (!$id) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//爱斯基摩冰屋
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(127732, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addChrismasIceHouseFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasIceHouseOK');
		} else {
			info_log($uid, 'chrismasIceHouseErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @圣诞节第四个任务
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getWesternGirl($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
	
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表(啤酒屋,扑克店,奶牛场,飞镖店,印第安人的帐篷)
		$list = array(array(129032), array(129232), array(128732), array(129432), array(129332));
		foreach ($list as $cids) {
			$id = 0;
			foreach ($cids as $cid) {
				$hasNum = 0;
				$itemType = substr($cid, -2, 1);
			
				if ($itemType == 1) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Background::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				} else if ($itemType == 2) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Building::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				} else if ($itemType == 3) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				} else if ($itemType == 4) {
					$num = 0;
					$db = Hapyfish2_Island_Dal_Card::getDefaultInstance();
					$hasNum = $db->getOneNum($uid, $cid);
				}

				$id += $hasNum;
		
				if ($id > 0) {
					break;
				}
			}

			//收集物品不足
			if ($id <= 0) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//牛仔女孩简妮
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(129832, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasWesternGirlOK');
		} else {
			info_log($uid, 'chrismasWesternGirlErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @第五个任务兑换鲁道夫
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getRudolf($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		
		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ( $item['count'] > 0 ) {
					$cardVo[$cid] = $item['count'];
				}
			}
		}
		
		$hasCount = 0;
		//圣诞红球 127141
		foreach ($cardVo as $cardID => $cardNum) {
			if (127141 == $cardID) {
				$hasCount = $cardNum;
				break;
			}
		}
		
		//圣诞绿球不足
		if ($hasCount < 30) {
			$result['content'] = self::TXT004;
			return $result;
		}
		
		//发放鲁道夫
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(129532, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasRudolfOK');
		} else {
			info_log($uid, 'chrismasRudolfErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
		
	}
	
	/**
	 * @第六个任务兑换啤酒屋
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getBeerHouse($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表
		$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		
		//西部女孩、鲁道夫
		$list = array(129832, 129532);
		foreach ($list as $cid) {
			$id = 0;
			$id = $db->getOneId($uid, $cid);
			//收集物品不足
			if (!$id) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//啤酒屋
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(129132, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasBeerHouseOK');
		} else {
			info_log($uid, 'chrismasBeerHouseErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @第七个任务兑换圣诞车
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getChrismasCart($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表
		$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		
		//彗星麋鹿、麋鹿—鲁道夫
		$list = array(128432, 129532);
		foreach ($list as $cid) {
			$id = 0;
			$id = $db->getOneId($uid, $cid);
			//收集物品不足
			if (!$id) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//圣诞车
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(131532, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasCartOK');
		} else {
			info_log($uid, 'chrismasCartErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @第八个任务兑换圣诞雪人
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getChrismasSnowMan($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表
		$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		
		//兔女郎邦妮、寻路女孩塔娜莎
		$list = array(131232, 130932);
		foreach ($list as $cid) {
			$id = 0;
			$id = $db->getOneId($uid, $cid);
			//收集物品不足
			if (!$id) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//圣诞雪人
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(131332, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasSnowManOK');
		} else {
			info_log($uid, 'chrismasSnowManErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @第九个任务兑换圣诞树
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getChrismasTree($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收集的物品列表
		$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		
		//5个公主
		$list = array(130732, 130632, 130832, 131032, 130532);
		foreach ($list as $cid) {
			$id = 0;
			$id = $db->getOneId($uid, $cid);
			//收集物品不足
			if (!$id) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//圣诞树
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(131432, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasTreeOK');
		} else {
			info_log($uid, 'chrismasTreeErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @第十个任务兑换浮空城堡
	 * @param int $uid
	 * @param int $taskId
	 * @return Array
	 */
	public static function getChrismasCastle($uid, $taskId)
	{
		$result = array('status' => -1);
		
		//判断是否已经领取过
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getGiftFlag($uid, $taskId);
		if ($flag == true) {
			$result['content'] = self::TXT001;
			return $result;
		}
		
		//收圣诞车,雪人,圣诞树,吉娜,圣诞芭比
		$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		
		$list = array(128632, 131132, 131332, 131432, 131532);
		foreach ($list as $cid) {
			$id = 0;
			$id = $db->getOneId($uid, $cid);
			//收集物品不足
			if (!$id) {
				$result['content'] = self::TXT004;
				return $result;
			}
		}
		
		//浮空城堡
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(131732, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addGiftFlag($uid, $taskId);
			
			//记录这个任务已经完成
			Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 2);
			
			info_log($uid, 'chrismasCastleOK');
		} else {
			info_log($uid, 'chrismasCastleErr');
			
			$result['content'] = 'serverWord_101';
			return $result;
		}
		
		$result['status'] = 1;
		return $result;
	}
	
	/**
	 * @圣诞节购买物品
	 * @param int $uid
	 * @param int $cid
	 * @param int $num
	 * @return Array
	 */
	public static function christmasComplete($uid, $cid, $num)
	{
		$result = array('status' => -1);
		
		//数量不能为空
		if ($num <= 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//get plant by cid
		$itemType = substr($cid, -2, 1);
		if ($itemType == 1) {
			$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($cid);
		} else if ($itemType == 2) {
			$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($cid);
		} else if ($itemType == 3) {
			$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
		} else if ($itemType == 4) {
			$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
		}
		
		if (!in_array($cid, array(127041, 127141, 130441))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		if (!$plantInfo || !$plantInfo['price'] || !$plantInfo['price_type']) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$needGood = 0;
		$needCoin = 0;
		$nowTime = time();
		
		//金币购买
		if (!$plantInfo['price_type'] == 1) {
			$needCoin = $num * $plantInfo['price'];
			
			//获得用户coin
			$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
			
			//金币不足
			if($userCoin < $needCoin) {
				$result['content'] = 'serverWord_137';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
			
			//发送物品
			$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $cid, $num);
			
			if ($ok) {
				$ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $needCoin);
				
				if ($ok2) {
					//add log
					$summary = LANG_PLATFORM_BASE_TXT_13 . $plantInfo['name'] . 'x' .  $num;
					Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $needCoin, $summary, $nowTime);
			        try {
						Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $needCoin);

						//task id 3012,task type 14
						$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3001);
						if ( $checkTask['status'] == 1 ) {
							$result['finishTaskId'] = $checkTask['finishTaskId'];
						}
			        } catch (Exception $e) {}
			        
					$title = '恭喜你花费<font color="#379636">' . $needCoin . '金币</font>，购买<font color="#FF0000">' . $plantInfo['name'] . 'x' . $num . '</font>';
					$feed = array(
								'uid' => $uid,
								'actor' => uid,
								'target' => uid,
								'template_id' => 0,
								'title' => array('title' => $title),
								'type' => 3,
								'create_time' => $nowTime
							);
					Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
			        
			        info_log($uid . ',' . $plantInfo['name'] . 'x' . $num, 'chrimascomplete');
				} else {
					info_log($uid . ',' . $plantInfo['name'] . 'x' . $num, 'chrimascompleteErr');
					
		    		$result['content'] = 'serverWord_101';
	    			$resultVo = array('result' => $result);
	    			return $resultVo;
				}
			} else {
				info_log($uid . ',' . $plantInfo['name'] . 'x' . $num, 'chrimascompleteErr');
					
	    		$result['content'] = 'serverWord_101';
    			$resultVo = array('result' => $result);
    			return $resultVo;
			}
		} else {
			$needGood = $num * $plantInfo['price'];
			
	    	//获得用户gold
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
	
			//宝石不足
			if ($userGold < $needGood) {
	    		$result['content'] = 'serverWord_140';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
			
			//发送物品
			$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $cid, $num);
			
			if ($ok) {
				//获取用户等级
				$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
				$userLevel = $userLevelInfo['level'];
				
				//扣除宝石
				$goldInfo = array('uid' => $uid,
								'cost' => $needGood,
								'summary' => LANG_PLATFORM_BASE_TXT_13 . $plantInfo['name'] . 'x' .  $num,
								'user_level' => $userLevel,
								'create_time' => $nowTime,
								'cid' => $cid,
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
			        
        			$title = '恭喜你花费<font color="#379636">' . $needGood . '宝石</font>，购买<font color="#FF0000">' . $plantInfo['name'] . 'x' . $num . '</font>';
					$feed = array(
								'uid' => $uid,
								'actor' => uid,
								'target' => uid,
								'template_id' => 0,
								'title' => array('title' => $title),
								'type' => 3,
								'create_time' => $nowTime
							);
					Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
			        
			        info_log($uid . ',' . $plantInfo['name'] . 'x' . $num, 'chrimascomplete');
			        
					//report log
					$logger = Hapyfish2_Util_Log::getInstance();
					$logger->report('204', array($uid, $cid, $num, 2, $needGood));
				} else {
					info_log($uid . ',' . $plantInfo['name'] . 'x' . $num, 'chrimascompleteErr');
					
		    		$result['content'] = 'serverWord_101';
	    			$resultVo = array('result' => $result);
	    			return $resultVo;
				}
			} else {
				info_log($uid . ',' . $plantInfo['name'] . 'x' . $num, 'chrimascompleteErr');
				
	    		$result['content'] = 'serverWord_101';
	    		$resultVo = array('result' => $result);
	    		return $resultVo;
			}
		}
		
		$result['status'] = 1;
		$result['coinChange'] = -$needCoin;
		$result['goldChange'] = -$needGood;
		$result['itemBoxChange'] = true;
    	$resultVo = array('result' => $result);
	    		
    	return $resultVo;
	}
	
	/**
	 * @获取邀请的好友列表
	 * @param int $uid
	 * @return Array
	 */
	public static function christmasFidList($uid)
	{
		$list = array();
		$fidArr = array();
		
		//$time = strtotime('2011-12-02 12:00:00');
		$time = strtotime('2011-11-02 12:00:00');
		try {
			$db = Hapyfish2_Island_Event_Dal_Christmas::getDefaultInstance();
			$list = $db->getAllFidList($uid, $time);
		} catch (Exception $e) {}

		if ($list) {
			foreach ($list as $key => $fid) {
				$user = Hapyfish2_Platform_Bll_User::getUser($fid);
				$fidArr[$key]['uid'] = $fid;
				$fidArr[$key]['face'] = $user['figureurl'];
				$fidArr[$key]['name'] = $user['name'];
			}
		}

		$inviteNum = count($fidArr);
		
		if ($inviteNum < 5) {
			switch ($inviteNum) {
				case 0:
					$fidArr = array('0' => array('uid' => '', 'face' => '', 'name' => ''),
									'1' => array('uid' => '', 'face' => '', 'name' => ''),
									'2' => array('uid' => '', 'face' => '', 'name' => ''),
									'3' => array('uid' => '', 'face' => '', 'name' => ''),
									'4' => array('uid' => '', 'face' => '', 'name' => ''));
				break;
				case 1:
					$fidArrNew = array('1' => array('uid' => '', 'face' => '', 'name' => ''),
									'2' => array('uid' => '', 'face' => '', 'name' => ''),
									'3' => array('uid' => '', 'face' => '', 'name' => ''),
									'4' => array('uid' => '', 'face' => '', 'name' => ''));
									
					$fidArr += 	$fidArrNew;			
				break;
				case 2:
					$fidArrNew = array('2' => array('uid' => '', 'face' => '', 'name' => ''),
									'3' => array('uid' => '', 'face' => '', 'name' => ''),
									'4' => array('uid' => '', 'face' => '', 'name' => ''));
									
					$fidArr += 	$fidArrNew;	
				break;
				case 3:
					$fidArrNew = array('3' => array('uid' => '', 'face' => '', 'name' => ''),
									'4' => array('uid' => '', 'face' => '', 'name' => ''));
									
					$fidArr += 	$fidArrNew;	
				break;
				case 4:
					$fidArrNew = array('4' => array('uid' => '', 'face' => '', 'name' => ''));
									
					$fidArr += 	$fidArrNew;	
				break;
			}
		}
		
		$resultVo = array('result' => 1, 'merryChristmasUserVo' => $fidArr);
		
		return $resultVo;
	}
	
	/**
	 * @领取邀请礼物
	 * @param int $uid
	 * @return Array
	 */
	public static function christmasGetInviteGift($uid)
	{
		$result = array('status' => -1);
		
		//判断用户是否已经领取
		$flag = Hapyfish2_Island_Event_Cache_Christmas::getInviteGiftFlag($uid);
		if ($flag) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//邀请列表
		$list = array();
		
		$time = strtotime('2011-12-01 12:00:00');
		try {
			$db = Hapyfish2_Island_Event_Dal_Christmas::getDefaultInstance();
			$list = $db->getAllFidList($uid, $time);
		} catch (Exception $e) {}
	
		$fidNum = count($list);
	
		//好友数不足5人不能领取
		if ($fidNum < 5) {
			$result['content'] = self::TXT002;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem(127041, 15);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//记录已经领取过
			Hapyfish2_Island_Event_Cache_Christmas::addInviteGiftFlag($uid);
			
			info_log($uid, 'chrismasInviteGiftOK');
		} else {
			info_log($uid, 'chrismasInviteGiftErr');
			
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
	 * @兑换公主
	 * @param int $uid
	 * @param int $id
	 * @return Array
	 */
	public static function toExchangePrincess($uid, $id)
	{
		$result = array('status' => -1);
	
		//只能是5种公主的ID
		if (!in_array($id, array(1, 2, 3, 4, 5))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
				
		//get cards
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
	
		$cardVo = array();
		if ($userCard) {
			foreach ($userCard as $cid => $item) {
				if ($item['count'] > 0) {
					$cardVo[] = array($cid, $item['count']);
					$cardList[] = $cid;
				}
			}
		}

		//当前要兑换的公主cid和所需圣诞彩球
		$data = Hapyfish2_Island_Event_Cache_Christmas::getPrincessData($id);
		if (!$data) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}

		//没有需要的卡片
		foreach ($data['list'] as $needCid => $needNum) {
			if (!in_array($needCid, $cardList)) {
				$result['content'] = self::TXT005;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//计算彩球数量
		foreach ($data['list'] as $needCid => $needNum) {
			foreach ($cardVo as $card) {
				if ($needCid == $card[0]) {
					//所需卡片不足
					if ($card[1] < $needNum) {
						$result['content'] = self::TXT005;
						$resultVo = array('result' => $result);
						return $resultVo;
					}
					
					break;
				}
			}
		}

		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		
		$compensation->setItem($data['cid'], 1);
		$ok = $compensation->sendOne($uid, self::TXT003);

		if ($ok) {
			//减少用户卡片
			foreach ($data['list'] as $needCid => $needNum) {
				$result = Hapyfish2_Island_HFC_Card::useUserCard($uid, $needCid, $needNum);
				if (!$result) {
					$resultVo['content'] = 'serverWord_110';
					return array('resultVo' => $resultVo);
				}	
			}

			info_log($uid . ':' . $id, 'chrismasToExOK');
		} else {
			info_log($uid . ':' . $id, 'chrismasToExErr');
			
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$result = array('status' => 1, 'itemBoxChange' => true);
		$resultVo['result'] = $result;
		
		return $resultVo;
	}
	
	/**
	 * @赛鹿
	 * @param int $uid
	 * @param Array $id
	 * @return Array
	 */
	public static function chrismasMatchFawn($uid, $deerListArr)
	{
		$result = array('status' => -1);

		//五只鹿的序号
		foreach ($deerListArr as $deerID) {
			if (!in_array($deerID, array(1, 2, 3, 4, 5))) {
				$result['content'] = 'serverWord_101';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
	
		//赛鹿卡
		$cidFawn = 130441;
		
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);

		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ( $item['count'] > 0 ) {
					$cardVo[] = array($cid, $cid, $item['count']);
				}
			}
		}

		//赛鹿需要的参赛卡
		$needCard = count($deerListArr) ;
		$needCardNum = 0;
		if ($needCard == 1) {
			$needCardNum = 2;
		} else if ($needCard == 2) {
			$needCardNum = 4;
		} else if ($needCard == 3) {
			$needCardNum = 8;
		} else if ($needCard == 4) {
			$needCardNum = 14;
		} else if ($needCard == 5) {
			$needCardNum = 20;
		} else {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}

		$hasCard = array('cid' => $cidFawn, 'count' => 0);
		foreach ($lstCard as $ck => $card) {
			if ($cidFawn == $ck) {
				$hasCard['cid'] = $ck;
				$hasCard['count'] = $card['count'];
				break;
			}
		}
	
		//卡片不足
		if ($hasCard['count'] < $needCardNum) {
			$result['content'] = self::TXT005;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//计算排名
		$rankList = self::getRankList();

		$reward = array();
		foreach ($deerListArr as $deerID) {
			foreach ($rankList as $key => $rank) {
				if ($deerID == $rank['id']) {
					$reward[$key + 1] = $rank['id'];
					break;
				}
			}
		}

		krsort($reward);
		
		$nowTime = time();
		$awardlist = array();
	
		foreach ($reward as $rk => $rv) {
			$ok	= false;
				
			if ($rk == 1) {
				//第一名奖励金色彩球
				$cid = 127641;
				$num = 1;
				$cardName = '金色圣诞彩球';
				
				$ok = Hapyfish2_Island_HFC_Card::addUserCard($uid, $cid, $num);
				$awardlist[] = array($cid, $num);
			} else if ($rk == 2) {
				//第二名奖励银色彩球
				$cid = 127541;
				$num = 1;
				$cardName = '银色圣诞彩球';
				
				$ok = Hapyfish2_Island_HFC_Card::addUserCard($uid, $cid, $num);
				$awardlist[] = array($cid, $num);
			} else if ($rk == 3) {
				//第三名奖励紫色彩球
				$cid = 127441;
				$num = 1;
				$cardName = '紫色圣诞彩球';
				
				$ok = Hapyfish2_Island_HFC_Card::addUserCard($uid, $cid, $num);
				$awardlist[] = array($cid, $num);
			} else if ($rk == 4) {
				//第四名奖励粉色彩球
				$cid = 127341;
				$num = 1;
				$cardName = '粉色圣诞彩球';
						
				$ok = Hapyfish2_Island_HFC_Card::addUserCard($uid, $cid, $num);
				$awardlist[] = array($cid, $num);
			} else if ($rk == 5) {
				//第五名奖励蓝色彩球
				$cid = 127241;
				$num = 1;
				$cardName = '蓝色圣诞彩球';

				$ok = Hapyfish2_Island_HFC_Card::addUserCard($uid, $cid, $num);
				$awardlist[] = array($cid, $num);
			}

			if ($ok) {
				//发feed
				$feed = array('uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'type' => 3,
							'title' => array('title' => '恭喜你在本次比赛中获得<font color="#FF0000">第' . $rk . '名</font>,奖励:<font color="#379636">' . $cardName . 'x' . $num . '</font>'),
							'create_time' => $nowTime);
			
				Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
				
				info_log($uid . ':' . $rv . ',' . $rk . ':' . $cardName . 'x' . $num, 'chrismasMatchFawnOK');
			} else {
				info_log($uid . ':' . $rv . ',' . $rk . ':' . $cardName . 'x' . $num, 'chrismasMatchFawnOK');
		
				$result['content'] = 'serverWord_101';
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//扣除卡片
		$decCard = Hapyfish2_Island_HFC_Card::useUserCard($uid, $cidFawn, $needCardNum);
		if (!$decCard) {
			$result['content'] = 'serverWord_110';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		info_log($uid . ':' . $cidFawn . ',' . $needCardNum, 'chrismasMatchFawnDecCard');
		
		$enddeerlist = array();
		foreach ($rankList as $rankVa) {
			$enddeerlist[] = array($rankVa['id'], $rankVa['rankId']);
		}

		$result['status'] = 1;
		$result['itemBoxChange'] = true;
		$resultVo['result'] = $result;
		$resultVo['awardlist'] = $awardlist;
		$resultVo['enddeerlist'] = $enddeerlist;
		
		return $resultVo;
	}
	
	/**
	 * @计算排名
	 * @return Array
	 */
	public static function getRankList()
	{
		$fawnLists = array(array('id' => 1, 'rankId' => 0),
						array('id' => 2, 'rankId' => 0),
						array('id' => 3, 'rankId' => 0),
						array('id' => 4, 'rankId' => 0),
						array('id' => 5, 'rankId' => 0));

		foreach ($fawnLists as $fawn => $fawnList) {
			$rankNum = mt_rand(1, 1000);
			$fawnLists[$fawn]['rankId'] = $rankNum;
		}

		foreach ($fawnLists as $fk => $fv) {
			$rank[$fk] = $fv['rankId'];
		}
	
		array_multisort($rank, SORT_ASC, $fawnLists);
	
		foreach ($fawnLists as $key => $value) {
			$fawnLists[$key]['id'] = $value['id'];
			$fawnLists[$key]['rankId'] = $key + 1;
		}
		
		return $fawnLists;
	}

}