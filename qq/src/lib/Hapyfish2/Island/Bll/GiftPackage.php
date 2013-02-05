<?php

class Hapyfish2_Island_Bll_GiftPackage
{
	/**
	 * add gift BackGround
	 * @param : integer uid
	 * @param : integer fid
	 * @param : integer id
	 * @param : integer $itemType
	 * @return: boolean
	 */
	public static function addBackGround($uid, $item_id, $item_num, $time, $itemType)
	{
		$bgInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($item_id);
		if (!$bgInfo) {
			return false;
		}

		$newBackground = array(
			'uid' => $uid,
			'bgid' => $item_id,
			'item_type' => $itemType,
			'buy_time' => $time
		);
		for($i=1; $i<=$item_num; $i++) {
			$ok = Hapyfish2_Island_Cache_Background::addNewBackground($uid, $newBackground);
		}

		return ;
	}

	/**
	 * add gift card
	 * @param : integer uid
	 * @param : integer fid
	 * @param : integer id
	 * @param : integer $itemType
	 * @return: boolean
	 */
	public static function addCard($uid, $item_id, $item_num, $time, $type)
	{
		$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($item_id);
		if (!$cardInfo) {
			return false;
		}

		for($i=1; $i<=$item_num; $i++) {
			$ok = Hapyfish2_Island_HFC_Card::addUserCard($uid, $item_id, 1);
		}

		return ;
	}

	/**
	 * add gift Building
	 * @param : integer uid
	 * @param : integer fid
	 * @param : integer id
	 * @param : integer $itemType
	 * @return: boolean
	 */
	public static function addBuilding($uid, $item_id, $item_num, $time, $itemType)
	{
		$buildingInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($item_id);
		if (!$buildingInfo) {
			return false;
		}

		$newBuilding = array(
			'uid' => $uid,
			'cid' => $item_id,
			'item_type' => $itemType,
			'status' => 0,
			'buy_time' => $time
		);

        $userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
	    $userCurrentIsland = $userVO['current_island'];
		for($i=1; $i<=$item_num; $i++) {
			$ok = Hapyfish2_Island_HFC_Building::addOne($uid, $newBuilding, $userCurrentIsland);
		}

		return ;
	}

	public static function addPlant($uid, $item_id, $item_num, $time, $itemType)
	{
		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($item_id);
		if (!$plantInfo) {
			return false;
		}

		$newPlant = array(
			'uid' => $uid,
			'cid' => $item_id,
			'item_type' => $itemType,
			'item_id' => $plantInfo['item_id'],
			'level' => $plantInfo['level'],
			'status' => 0,
			'buy_time' => $time
		);

        $userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
	    $userCurrentIsland = $userVO['current_island'];
		for($i=1; $i<=$item_num; $i++) {
			$ok = Hapyfish2_Island_HFC_Plant::addOne($uid, $newPlant, $userCurrentIsland);
		}

		return;
	}

	/**
	 * type add gift
	 * @param integer $actorUid
	 * @param integer $fid
	 * @param integer $gid
	 * @return boolean $result
	 */
	public static function addGift($uid, $item_id, $item_num)
	{
		$result = false;
		$type = substr($item_id, -2);
		$itemType = substr($item_id, -2, 1);
		$time = time();

		//itemType,1x->background,2x->building,3x->plant,4x->card
		if ($itemType == 1) {
            $result = self::addBackground($uid, $item_id, $item_num, $time, $type);
        } else if ($itemType == 2){
            $result = self::addBuilding($uid, $item_id, $item_num, $time, $type);
        } else if ($itemType == 3) {
        	$result = self::addPlant($uid, $item_id, $item_num, $time, $type);
        } else if ($itemType == 4) {
            $result = self::addCard($uid, $item_id, $item_num, $time, $type);
        }

        return $result;
	}

	/**
	 * 获取packageID
	 */
 	public static function getNewPackageId($uid)
    {
        try {
    		$dalUserSequence = Hapyfish2_Island_Dal_UserSequence::getDefaultInstance();
    		$pid = $dalUserSequence->get($uid, 'd', 1);

    		return $pid;
    	} catch (Exception $e) {
    		info_log("Exception","gift");
    	}

    	return 0;
    }

	/**
	 * send gift
	 * @param array $g
	 * @param array $fids (friend uid)
	 * @return boolean
	 */
	public static function sendGift($gid, $uid, $fids, $countInfo, $type)
	{
	    if (empty($fids)) {
			return 0;
	    }

	    $time = time();
	    $count = 0;
		foreach ($fids as $fid) {
			$itemDataStr = $gid."*".'1';
			$pid = Hapyfish2_Island_Bll_GiftPackage::getNewPackageId($uid);

			if($type == 1) {
				$info = array('to_uid' => $fid,
							'from_uid' => $uid,
							'gift_type' => 3,
							'pid' => $pid,
							'item_data'	=> $itemDataStr,
							'send_time' => $time);
			}
			else {
				$info = array('to_uid' => $fid,
							'from_uid' => $uid,
							'gift_type' => 4,
							'pid' => $pid,
							'item_data'	=> $itemDataStr,
							'send_time' => $time);
			}

			//insert gift
        	$dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
            $ok = $dalGift->insert($fid, $info);

            if ($ok) {
            	$count++;
				$feed = array(
					'uid' => $fid,
					'template_id' => 9,
					'actor' => $uid,
					'target' => $fid,
					'type' => 3,
					'title' => '',
					'create_time' => $time
				);
				Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
			}

			Hapyfish2_Island_HFC_AchievementDaily::updateUserAchievementDailyByField($uid, 'num_4', 1);
			Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_4', 1);
		}

		if ($type == 1) {
			$countInfo['count'] -= $count;
			if($count > 0) {
				Hapyfish2_Island_Cache_Counter::updateSendGiftCount($uid, $countInfo);
			}
		}

		return $count;
	}

	/**
	 * insert send gift log
	 */
	public static function insertGiftLog($uid, $gid, $fids, $gtype)
	{
		$now = time();
		if($fids) {
			foreach ($fids as $fid) {
				$infoPost = array('to_uid' => $fid,
								'gid' => $gid,
								'gtype' => $gtype,
								'create_time' => $now);

				Hapyfish2_Island_Cache_GiftPackage::insertPostGiftLog($uid, $infoPost);

				$infoGet = array('from_uid' => $uid,
								  'gid' => $gid,
								  'gtype' => $gtype,
								  'create_time' => $now);

				//update user gift log status
        		Hapyfish2_Island_Cache_GiftPackage::insertGetGiftLog($fid, $infoGet);
			}
		}
	}

	/**
	 * get giftlist
	 *
	 * @param integer uid
	 * @return array
	 */
	public static function getList($uid)
	{
		$result = array('status' => -1);

		//read giftVOLists
		$dalGiftPackage = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
		$giftVOLists = $dalGiftPackage->getList($uid);

		$giftList = array();
		$giftVo = array();
		$key = 'i:u:e:getgiftpackageList:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $lastReadTime = $cache->get($key);
        if(empty($lastReadTime))
        {
        	$lastReadTime = time() - 3600;
        }
		foreach ( $giftVOLists as $giftVOList ) {
			$giftVo['type'] = $giftVOList['gift_type'];
			$giftVo['sendTime'] = $giftVOList['send_time'];
			if ($giftVOList['send_time'] > $lastReadTime) {
				$giftVo['newFlag'] = 1;
			} else {
				$giftVo['newFlag'] = 0;
			}

			switch ($giftVOList['gift_type']) {
				case 1:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '升级奖励';
					$giftVo['sendUserName'] = '系统';
				break;
				case 2:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '称号奖励';
					$giftVo['sendUserName'] = '系统';
				break;
				case 7:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '连续登陆奖励';
					$giftVo['sendUserName'] = '系统';
				break;
				case 8:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '新手引导奖励';
					$giftVo['sendUserName'] = '系统';
				break;
				case 9:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '等级大礼包';
					$giftVo['sendUserName'] = '系统';
				break;
				case 10:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '新手时间礼物';
					$giftVo['sendUserName'] = '系统';
				break;
				case 11:
					$giftVo['id'] = $giftVOList['pid'];
					$giftVo['sendReason'] = '等级大礼包';
					$giftVo['sendUserName'] = '系统';
				break;
				default:
					$giftVo['sendUserId'] = $giftVOList['from_uid'];
					$giftVo['id'] = $giftVOList['pid'];

					$userInfo = Hapyfish2_Platform_Bll_Factory::getUser($giftVOList['from_uid']);
					$giftVo['sendUserName'] = $userInfo['nickname'];
					$giftVo['sendReason'] = $userInfo['nickname'].'赠';
			}

			$giftVo['itemList'] = array();
			$giftList[] = $giftVo;
		}
		$cache->set($key,time());
		$result['status'] = 1;
		$resultVo['result'] = $result;
		$resultVo['giftVOList'] = $giftList;

		return $resultVo;
	}

	/**
	 * open one gift package
	 *
	 * @param int uid
	 * @param int pid
	 * @return array
     */
	public static function openOne($uid, $pid)
	{
		$result = array('status' => -1);

		if(empty($pid) ) {
			$result['content'] = '礼包不存在!';
            return $result;
		}

		//get gift
		try {
			$dalGiftPackage = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
			$gift = $dalGiftPackage->getOne($uid, $pid);
		} catch (Exception $e) {
			$result['content'] = '读取礼包数据出错!';
			return $result;
		}

		if (empty($gift)) {
			$result['content'] = '礼包为空!';
            return $result;
		}

		$giftVo = array();
		$result = array('status' => 1);

		$giftVo['type'] = $gift['gift_type'];
		$giftVo['sendTime'] = $gift['send_time'];

		switch ($gift['gift_type']) {
			case 1:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '升级奖励';
				$giftVo['sendUserName'] = '系统';
			break;
			case 2:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '称号奖励';
				$giftVo['sendUserName'] = '系统';
			break;
			case 7:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '连续登陆奖励';
				$giftVo['sendUserName'] = '系统';
			break;
			case 8:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '新手引导奖励';
				$giftVo['sendUserName'] = '系统';
			break;
			case 9:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '等级大礼包';
				$giftVo['sendUserName'] = '系统';
			break;
			case 10:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '新手时间礼物';
				$giftVo['sendUserName'] = '系统';
			break;
			case 11:
				$giftVo['id'] = $gift['pid'];
				$giftVo['sendReason'] = '等级大礼包';
				$giftVo['sendUserName'] = '系统';
			break;
			default:
				$giftVo['sendUserId'] = $gift['from_uid'];
				$giftVo['id'] = $gift['pid'];

				$userInfo = Hapyfish2_Platform_Bll_Factory::getUser($gift['from_uid']);
				$giftVo['sendUserName'] = $userInfo['nickname'];
				$giftVo['sendReason'] = $userInfo['nickname'].'赠';
		}

		$itemList = array();

		if ($gift['coin'] > 0) {
			$itemList[] = array('coin' => $gift['coin']);
			$result['coinChange'] = $gift['coin'];
			Hapyfish2_Island_HFC_User::incUserCoin($uid, $gift['coin']);
		}

		//海星添加
		if ($gift['starfish'] > 0) {
			$itemList[] = array('starfish' => $gift['starfish']);
			$result['starfishChange'] = $gift['starfish'];
			Hapyfish2_Island_HFC_User::incUserStarFish($uid, $gift['starfish']);
		}

		if ($gift['exp'] > 0) {
			$itemList[] = array('exp' => $gift['exp']);
			$result['expChange'] = $gift['exp'];

			Hapyfish2_Island_HFC_User::incUserExp($uid, $gift['exp']);

	        try {
	        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
	            $result['levelUp'] = $levelUp['levelUp'];
	            $result['islandLevelUp'] = $levelUp['islandLevelUp'];
	    	if ($levelUp['feed']) {
            	$result['feed'] = $levelUp['feed'];
            }
	        } catch (Exception $e) {
	        }
		}
		if(!empty($gift['item_data'])) {
			$items = explode(',', $gift['item_data']);
			foreach ($items as $v) {
				$item = explode('*', $v);
				self::addGift($uid, $item[0], $item[1]);
				$itemList[] = array('itemId' => $item[0], 'itemNum' => $item[1]);
			}
		}

		//delete gift
		try {
			$dalGiftPackage->delete($uid, $pid);
		} catch (Exception $e) {

		}

		//统计阶段性礼物的领取信息
		if ( $gift['gift_type'] == 9 ) {
			try {
				//report log
				$logger = Hapyfish2_Util_Log::getInstance();
				$logger->report('3003', array($uid, $gift['coin']));
			} catch (Exception $e) {
			}
		}
		
		$giftVo['itemList'] = $itemList;

		$resultVo = array('giftVo' => $giftVo,
						  'result' => $result);

		return $resultVo;
	}

	public static function getNum($uid)
	{
		try {
			$dalGiftPackage = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
			return $dalGiftPackage->getNum($uid);
		} catch (Exception $e) {
		}

		return 0;
	}

	/**
	 * insert new user gift (time)
	 * @return : boolean
	 * */
	public static function getNewUserGift($giftInfo)
	{
		if( !empty($giftInfo['item_data']) ) {
			$pid = Hapyfish2_Island_Bll_GiftPackage::getNewPackageId($giftInfo['uid']);
	        $info = array('to_uid' => $giftInfo['uid'],
	        			  'pid'	   => $pid ,
	        	  		  'gift_type' => 10,
	        	          'send_time' => time(),
	        	  		  'item_data' => $giftInfo['item_data']);


	        $dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();

	        $ret = $dalGift->insert($giftInfo['uid'],$info);

			$minifeed = array('uid' => $info['to_uid'],
	                          'template_id' => 105,
	                          'actor' => $info['to_uid'],
	                          'target' => $info['to_uid'],
	                          'title' => array('type' => $giftInfo['type']),
	                          'type' => 3,
	                          'create_time' => time());

           	 Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);

		}
        return true;
	}

	/**
	 * get Level Gift
	 *
	 * @param inter uid
	 * @return array
     */
	public static function getLevelGift($uid)
	{
		$result = array('status' => -1);

		//get user info
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);

		$resultVoList = array();

		$userLevel = $userLevelInfo['level'] % 5;

		if( $userLevel == 0 ) {
        	$result = array('status' => 1);

        	//get level gift
			$dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
			$levelGift = $dalGift->getLevelGiftNew($userLevelInfo['level']);

			$pack = array('to_uid' => $uid,
						  'coin' => $levelGift['coin'],
						 // 'starfish' => $levelGift['starfish'],
						  'item_data' => $levelGift['item_data']);

			//owner platform info,黄钻系统
		   	$platformUser = Hapyfish2_Platform_Bll_Factory::getUser($uid);
		    if ( $platformUser['is_year_vip'] || $platformUser['is_vip'] ) {
		     	$pack['coin'] *= 2;

		     	$items = explode(',', $pack['item_data']);
		     	$newItem = array();
	        	foreach ($items as $v) {
					$item = explode('*', $v);
					$newNum = $item[1] * 2;
					$newItem[] = $item[0] . '*' . $newNum;
				}
				$pack['item_data'] = implode(',', $newItem);
		    }

			//get gift id
			$giftPackId = $dalGift->getGiftId($uid,$pack);
		}
		$resultVoList['result'] = $result;
		$resultVoList['giftPackId'] =  $giftPackId;

		return $resultVoList;
	}

	/**
	 * get user has gift log
	 */
	public static function getGiftLog($uid)
	{
		$gifts = Hapyfish2_Island_Cache_GiftPackage::getGiftLogData($uid);
		if (!$gifts) {
			return array();
		}

		$giftList = array();
		$giftListNew = array();
		foreach( $gifts as $gift ) {
			$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getGiftInfo($gift['gid']);

			$giftList['from_uid'] = $gift['from_uid'];
			$giftList['gift_name'] = $giftInfo['name'];
			$giftList['price'] = $giftInfo['price'];
			$giftList['gtype'] = $gift['gtype'];
			$giftList['create_time'] = $gift['create_time'];

			//get island name
			$userInfo = Hapyfish2_Platform_Bll_Factory::getUser($giftList['from_uid']);

			$giftListNew[] = array('from_name' => $userInfo['nickname'],
									'name' => $giftList['gift_name'],
									'price' => $giftList['price'],
									'gtype' => $giftList['gtype'],
									'create_time' => $giftList['create_time']);
		}
		return $giftListNew;
	}

	/**
	 * get user has gift log num
	 */
	public static function getGiftLogCount($uid)
	{
		$LogNum = Hapyfish2_Island_Cache_GiftPackage::getGiftLogCount($uid);

		return $LogNum;
	}

	/**
	 * get user post gift log
	 */
	public static function postGiftLog($uid)
	{
		$gifts = Hapyfish2_Island_Cache_GiftPackage::postGiftLogData($uid);
		if (!$gifts) {
			return array();
		}

		$giftList = array();
		$giftListNew = array();
		foreach( $gifts as $gift ) {
			$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getGiftInfo($gift['gid']);

			$giftList['to_uid'] = $gift['to_uid'];
			$giftList['gift_name'] = $giftInfo['name'];
			$giftList['price'] = $giftInfo['price'];
			$giftList['gtype'] = $gift['gtype'];
			$giftList['create_time'] = $gift['create_time'];

			//get name
			$userInfo = Hapyfish2_Platform_Bll_Factory::getUser($giftList['to_uid']);

			$giftListNew[] = array('to_name' => $userInfo['nickname'],
									'name' => $giftList['gift_name'],
									'price' => $giftList['price'],
									'gtype' => $giftList['gtype'],
									'create_time' => $giftList['create_time']);
		}

		return $giftListNew;
	}

	/**
	 * get user post gift log num
	 */
	public static function postGiftLogCount($uid)
	{
		$LogNum = Hapyfish2_Island_Cache_GiftPackage::postGiftLogCount($uid);

		return $LogNum;
	}
}