<?php

class Hapyfish2_Island_Bll_User
{
	public static function getUserGold($uid)
	{
		$rest = Qzone_Rest::getInstance();
		$session_key = Hapyfish2_Island_Cache_CustomData::get($uid, 'skey');
		$rest->setUser($uid, $session_key);
		return $rest->getPayBalance();
	}

	public static function getUserInit($uid)
	{
        //owner platform info
        $user = Hapyfish2_Platform_Bll_User::getUser($uid);

		$userHelp = Hapyfish2_Island_Cache_UserHelp::getHelpInfo($uid);
		$helpList = $userHelp['helpList'];
		if ( $userHelp['completeCount'] == 8 ) {
			$help = array();
			$finishOrder = array();
		}
		else {
			$help = array($helpList[1], $helpList[2], $helpList[3], $helpList[4], $helpList[5], $helpList[6], $helpList[7], $helpList[8]);
			$finishOrder = $userHelp['finishOrder'];
		}

		$actState = Hapyfish2_Island_Bll_Act::get($uid);
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);

		return array(
			'uid' => $userVO['uid'],
			'name' => $user['name'],
			'exp' => $userVO['exp'],
		    'maxExp' => $userVO['next_level_exp'],
			'level' => $userVO['level'],
			'islandLevel' => $userVO['island_level'],
			'praise' => $userVO['praise'],
			'face' => $user['figureurl'],
			'smallFace' => $user['figureurl'],
			'sitLink' => 'http://www.renren.com/home?id=' . $user['puid'],
			'coin' => $userVO['coin'],
			'money' => $userVO['gold'],
		    'presentNum' => 0,
		    'help' => $help,
		    'helpFinishOrderList' => $finishOrder,
            'actState' => $actState
		);
	}

	public static function readTitle($uid, $ownerUid)
	{
        $userTitle = Hapyfish2_Island_HFC_User::getUserTitle($ownerUid);
        if ($uid != $ownerUid) {
            $result = array('currentTitle' => $userTitle['title']);
        }
        else {
        	$userTitles = array();
        	if (!empty($userTitle['title_list'])) {
        		$tmp = split(',', $userTitle['title_list']);
        		foreach ($tmp as $id) {
        			$userTitles[] = array('title' => $id);
        		}
        	}

            $result = array('userTitles' => $userTitles, 'currentTitle' => $userTitle['title']);
        }
        return $result;
	}

	public static function changeTitle($uid, $titleId)
	{
    	$result = array('status' => -1);

    	try {
	    	$userTitle = Hapyfish2_Island_HFC_User::getUserTitle($uid);
	    	$titleList = $userTitle['title_list'];
	    	$curTitle = $userTitle['title'];

	    	if (empty($titleList)) {
				$result['content'] = 'serverWord_149';
				return $result;
	    	}

	    	if ($titleId == $curTitle) {
				$result['content'] = 'serverWord_149';
				return $result;
	    	}

	    	$list = split(',', $titleList);

	    	if (!in_array($titleId, $list)) {
				$result['content'] = 'serverWord_149';
				return $result;
	    	}

	    	$userTitle['title'] = $titleId;
	    	Hapyfish2_Island_HFC_User::updateUserTitle($uid, $userTitle);

	        $result['status'] = 1;
        }
        catch (Exception $e) {
            $result['status'] = -1;
            $result['content'] = 'serverWord_110';
            $result = array('result' => $result);
            return $result;
        }

        return $result;
	}

	public static function changehelp($uid, $help)
	{
        $result = array('status' => -1);

        if (!in_array($help, array('1','2','3','4','5','6','7','8')) ) {
            return $result;
        }
        //get user help info
        $userHelp = Hapyfish2_Island_Cache_UserHelp::getHelpInfo($uid);
        $helpList = $userHelp['helpList'];
        $comCount = $userHelp['completeCount'];
        $finishOrder = $userHelp['finishOrder'];

        if ( $comCount >= 8 || $helpList[$help] == 1 ) {
            $result['status'] = 1;
            return $result;
        }
        if ( $comCount < 7 && $help == 8 ) {
            $result['status'] = 1;
            return $result;
        }

		$helpList[$help] = 1;
		$finishOrder[] = (int)$help;
		Hapyfish2_Island_Cache_UserHelp::updateHelp($uid, $helpList, $finishOrder);
		$result['status'] = 1;

        if ( $help == 8 ) {
        	$nowTime = time();
        	$pid = Hapyfish2_Island_Bll_GiftPackage::getNewPackageId($uid);
            $giftInfo = array('to_uid' => $uid,
            			  'pid' => $pid,
                		  'gift_type' => 8,
                		  'coin' => 5000,
                		  'item_data' => '26441*3,3931*1,16031*1',
                		  'send_time' => $nowTime);
	        $dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
	        $dalGift->insert($uid, $giftInfo);
	        $giftId = $pid;

	        $giftCardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo(26441);
	        $giftPlantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo(3931);
	        $minifeed = array('uid' => $uid,
	                              'template_id' => 32,
	                              'actor' => $uid,
	                              'target' => $uid,
	                              'title' => array('coin' => 3000, 'item' => $giftCardInfo['name']. ' ' . $giftPlantInfo['name']),
	                              'type' => 3,
	                              'create_time' => $nowTime);
	        Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
        }

		switch ( $comCount ) {
			case 1 :
				$addExp = 15;
				break;
			case 2 :
				$addExp = 20;
				break;
			case 3 :
				$addExp = 25;
				break;
			case 4 :
				$addExp = 30;
				break;
			case 5 :
				$addExp = 35;
				break;
			case 6 :
				$addExp = 40;
				break;
			default :
				$addExp = 10;
				break;
		}
		Hapyfish2_Island_HFC_User::incUserExp($uid, $addExp);
		$result['expChange'] = $addExp;

        try {
	        //check level up
        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
            $result['levelUp'] = $levelUp['levelUp'];
            $result['islandLevelUp'] = $levelUp['islandLevelUp'];
        } catch (Exception $e) {
        }

        return $result;
	}

	/**
	 * change user help
	 *
	 * @param integer $uid
	 * @param integer $help
	 * @return array
	 */
    public static function getHelpGift($uid, $help)
    {
        $result = array('status' => -1);

        if ( !in_array($help, array('1','2','3','4','5','6','7','8')) ) {
            return $result;
        }

        //get user help info
        $userHelpInfo = Hapyfish2_Island_Cache_UserHelp::getHelpInfo($uid);

        if ( $userHelpInfo[$help] != 1 ) {
            $result['status'] = 1;
            return $result;
        }
        $help1 = $userHelpInfo[1] == 2 ? 1 : 0;
        $help2 = $userHelpInfo[2] == 2 ? 1 : 0;
        $help3 = $userHelpInfo[3] == 2 ? 1 : 0;
        $help4 = $userHelpInfo[4] == 2 ? 1 : 0;
        $help5 = $userHelpInfo[5] == 2 ? 1 : 0;
        $help7 = $userHelpInfo[7] == 2 ? 1 : 0;
        $help8 = $userHelpInfo[8] == 2 ? 1 : 0;

        $helpTotal = $help1 + $help2 + $help3 + $help4 + $help5 + $help7 + $help8;

        try {
			$userHelpInfo[$help] = 2;
			Hapyfish2_Island_Cache_UserHelp::updateHelp($uid, $userHelpInfo);
			$result['status'] = 1;

        	$nowTime = time();
        	$helpTotal = $helpTotal + 1;
	        	if ( $helpTotal == 1 ) {
	        		$coin = 50;
	        		$exp = 50;
	        		$itemData = '7821*1';
	        		$itemId = 7821;
	            }
	        	else if ( $helpTotal == 2 ) {
	        		$coin = 80;
	        		$exp = 100;
	        		$itemData = '6521*1';
	        		$itemId = 6521;
	            }
	        	else if ( $helpTotal == 3 ) {
	        		$coin = 110;
	        		$exp = 200;
	        		$itemData = '6721*1';
	        		$itemId = 6721;
	            }
	        	else if ( $helpTotal == 4 ) {
	        		$coin = 140;
	        		$exp = 300;
	        		$itemData = '6621*1';
	        		$itemId = 6621;
	            }
	        	else if ( $helpTotal == 5 ) {
	        		$coin = 170;
	        		$exp = 400;
	        		$itemData = '6921*1';
	        		$itemId = 6921;
	            }
	        	else if ( $helpTotal == 6 ) {
	        		$coin = 200;
	        		$exp = 500;
	        		$itemData = '7021*1';
	        		$itemId = 7021;
	            }
	        	else if ( $helpTotal == 7 ) {
	        		$coin = 230;
	        		$exp = 600;
	        		$itemData = '7421*1';
	        		$itemId = 7421;
	            }
	            //send gift
	            $pid = Hapyfish2_Island_Bll_GiftPackage::getNewPackageId($uid);
	        	$giftInfo = array('to_uid' => $uid,
	        					  'pid' => $pid,
	        					  'gift_type' => 8,
	        					  'coin' => $coin,
	        					  'exp' => $exp,
	        					  'item_data' => $itemData,
	        					  'send_time' => $nowTime);
		        $dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
		        $dalGift->insert($uid, $giftInfo);
		        $giftId = $pid;

        	    switch ( $help ) {
	        		case 1 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_20;
	        			break;
	        		case 2 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_21;
	        			break;
	        		case 3 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_22;
	        			break;
	        		case 4 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_23;
	        			break;
	        		case 5 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_24;
	        			break;
	        		case 7 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_25;
	        			break;
	        		case 8 :
	        			$feedType = LANG_PLATFORM_BASE_TXT_26;
	        			break;
	        	}
	        	$buildingInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($itemId);

	        $minifeed = array('uid' => $uid,
	                              'template_id' => 33,
	                              'actor' => $uid,
	                              'target' => $uid,
	                              'title' => array('feedType' => $feedType, 'coin' => $coin, 'exp' => $exp, 'item' => $buildingInfo['name']),
	                              'type' => 3,
	                              'create_time' => $nowTime);
	        Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);

	        $result['status'] = 1;
	        return array('result' => $result, 'packId' => $giftId);

        } catch (Exception $e) {
            info_log('[changeHelp]:' . $e->getMessage(), 'Hapyfish_Island_Bll_User');
            return $result;
        }
    }

	public static function checkLevelUp($uid)
	{
        $levelUp = false;
        $giftName = '';
        $islandLevelUp = false;

        $default = array(
        	'levelUp' => $levelUp,
            'islandLevelUp' => $islandLevelUp,
            'giftName' => $giftName,
        	'feed' => null
        );

		$user = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'level' => 1));
		if (!$user) {
			return $default;
		}

		$userLevel = $user['level'];
		$nextLevelExp = Hapyfish2_Island_Cache_BasicInfo::getUserLevelExp($userLevel + 1);
		if (!$nextLevelExp) {
			return $default;
		}

		if ($user['exp'] < $nextLevelExp) {
			return $default;
		}

		$levelUp = true;
		$user['level'] += 1;
		$userLevelInfo = array('level' => $user['level'], 'island_level' => $user['island_level']);
		$nextIslandLevel = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfoByUserLevel($user['level']);
		if ($user['island_level'] < $nextIslandLevel) {
			$islandLevelUp = true;
			$user['island_level'] += 1;
			$userLevelInfo['island_level'] += 1;
		}

		$ok = Hapyfish2_Island_HFC_User::updateUserLevel($uid, $userLevelInfo);
		if ($ok) {
			$now = time();
			Hapyfish2_Island_Bll_LevelUpLog::add($uid, $userLevel, $user['level']);

			if ($islandLevelUp) {
				//update builing and plant coordinate
				Hapyfish2_Island_HFC_Plant::upgradeCoordinate($uid);
				Hapyfish2_Island_HFC_Building::upgradeCoordinate($uid);
			}

			//升级送2颗宝石
			$goldInfo = array('gold' => 2, 'type' => 1, 'time' => $now);
			Hapyfish2_Island_Bll_Gold::add($uid, $goldInfo);

			$gift = Hapyfish2_Island_Cache_BasicInfo::getGiftByUserLevel($user['level']);

			if ($gift) {
				$giftName = $gift['name'];

				$itemDataStr = $gift['cid'] . "*" . '1';
				$pid = Hapyfish2_Island_Bll_GiftPackage::getNewPackageId($uid);

				$info = array('to_uid' => $uid,
								'pid' => $pid,
								'gift_type' => 1,
								'item_data'	=> $itemDataStr,
								'send_time' => time());

				//insert gift
	        	$dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
	        	$packId = $dalGift->insert($uid, $info);

	            $minifeed = array(
	            	'uid' => $uid,
					'template_id' => 8,
					'actor' => $uid,
					'target' => $uid,
	            	'title' => array('level' => $user['level'], 'giftName' => $giftName." 2".LANG_PLATFORM_BASE_TXT_02),
					//'title' => array('level' => $user['level'], 'giftName' => $giftName." 2宝石"),
					'type' => 3,
					'create_time' => $now
	            );
	            Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			}
		}

		if ($islandLevelUp) {
			$islandLevelInfo = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfo($user['island_level']);
			if ($islandLevelInfo) {
				$achievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
				$achievement['num_15'] = $islandLevelInfo['max_visitor'];
				Hapyfish2_Island_HFC_Achievement::saveUserAchievement($uid, $achievement);

		        try {
					//task id 3007,task type 15
					Hapyfish2_Island_Bll_Task::checkTask($uid, 3007);
		        } catch (Exception $e) {
		        }
			}

		}

        $result = array(
        	'levelUp' => $levelUp,
			'islandLevelUp' => $islandLevelUp,
			'giftName' => $giftName,
        	'feed' => null
        );
        if ($levelUp) {
        	$result['newLevel'] = $user['level'];

            //update achievement task,22
	        try {
	        	Hapyfish2_Island_HFC_Achievement::updateUserAchievementByFieldData($uid, 'num_22', $user['level']);

				//task id 3050,task type 22
				Hapyfish2_Island_Bll_Task::checkTask($uid, 3050);
	        } catch (Exception $e) {
	        }

        	//等级大礼包
            $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);

            $userLevel = $userLevelInfo['level'] % 5;

            if( $userLevel == 0 ) {
				//get level gift
				$levelGift = Hapyfish2_Island_Cache_BasicInfo::getStepGiftByUserLevel($userLevelInfo['level']);
				$itemId = explode(",", $levelGift['item_id']);
        		$itemNum = explode(",", $levelGift['item_num']);
        		$itemData = array();
        		for($i = 0; $i < sizeof($itemId); $i++) {
        			$itemData[] .= $itemId[$i] . "*" . $itemNum[$i];
        		}
        		$itemDataStr = join(",", $itemData);
				$pid = Hapyfish2_Island_Bll_GiftPackage::getNewPackageId($uid);

	  			$info = array('to_uid' => $uid,
							  'gift_type' => 9,
	  						  'pid'		=> $pid,
							  'coin' => $levelGift['coin'],
							  'gold' => $levelGift['gold'],
							  'item_data' => $itemDataStr,
							  'send_time' => time());

				//insert gift
				try {
	        		$dalGift = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
            		$packId = $dalGift->insert($uid, $info);
				} catch (Exception $e) {

				}

				$item = explode(',', $info['item_data']);
				$gift = "";
				if( count($item) > 2 ) {
					for( $i=2; $i<count($item); $i++ ) {
						//get gift name
						$typeArr = explode("*", $item[$i]);
						$type = $typeArr[0];
						$giftType = substr($type, -2, 1);

						switch( $giftType ) {
							case 1:
								$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($type);
							break;
							case 2:
								$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($type);
							break;
							case 3:
								$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($type);
							break;
							case 4:
								$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($type);
							break;
						}

						if( $i == 2 ) {
							$giftName_2 = $giftInfo['name'];
						}
						else if( $i == 3 ) {
							$giftName_3 = $giftInfo['name'];
						}

						if( $i == 2 ) {
							$gift = " " . $giftName_2;
						}
						else if( $i == 3 ) {
							$gift = " " . $giftName_2 . " ". $giftName_3;
						}
					}
				}

				if($levelGift['gold']) {
					$template_id = 108;
					$title = array('level' => $userLevelInfo['level'],
									'coin' => $levelGift['coin'],
									'gold' => $levelGift['gold'],
									'gift' => $gift);
				} else {
					$template_id = 104;
					$title = array('level' => $userLevelInfo['level'],
									'coin' => $levelGift['coin'],
									'gift' => $gift);
				}

				$minifeed = array('uid' => $uid,
                              'template_id' => $template_id,
                              'actor' => $uid,
                              'target' => $uid,
                              'title' => $title,
                              'type' => 3,
                              'create_time' => time());

           	 	Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			}

        	if ($islandLevelUp) {
            	$result['newIslandLevel'] = $user['island_level'];
            	$result['feed'] = Hapyfish2_Island_Bll_Activity::send('ISLAND_LEVEL_UP', $uid);
        	} else {
        		$result['feed'] = Hapyfish2_Island_Bll_Activity::send('USER_LEVEL_UP', $uid, array('level' => $user['level']));
        	}
        }

        return $result;
	}

	/**
	 * join user
	 *
	 * @param integer $uid
	 * @return boolean
	 */
	public static function joinUser($uid)
	{
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		if (empty($user)) {
			return false;
		}

		$step = 0;
		$today = date('Ymd');
		try {
			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
			$dalUserSequence = Hapyfish2_Island_Dal_UserSequence::getDefaultInstance();
			$dalBackground = Hapyfish2_Island_Dal_Background::getDefaultInstance();
			$dalBuilding = Hapyfish2_Island_Dal_Building::getDefaultInstance();
			$dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
			$dalDock = Hapyfish2_Island_Dal_Dock::getDefaultInstance();
			$dalCard = Hapyfish2_Island_Dal_Card::getDefaultInstance();
			$dalCardStatus = Hapyfish2_Island_Dal_CardStatus::getDefaultInstance();
			$dalUserIsland = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
			$dalAchievement = Hapyfish2_Island_Dal_Achievement::getDefaultInstance();
			$dalAchievementDaily = Hapyfish2_Island_Dal_AchievementDaily::getDefaultInstance();

			$dalUser->init($uid);
			$step++;
			$dalUserSequence->init($uid);
			$step++;
			$dalBackground->init($uid);
			$step++;
			$dalBuilding->init($uid);
			$step++;
			$dalPlant->init($uid);
			$step++;
			$dalDock->init($uid);
			$step++;
			$dalUserIsland->init($uid);
			$step++;
			$dalCard->init($uid);
			$step++;
			$dalCardStatus->init($uid);
			$step++;
			$dalAchievement->init($uid);
			$step++;
			$dalAchievementDaily->init($uid, $today);
			$step++;
		}
		catch (Exception $e) {
			info_log('[' . $step . ']' . $e->getMessage(), 'island.user.init');
            return false;
		}

		Hapyfish2_Island_Cache_User::setAppUser($uid);

		return true;
	}

	/**
	 * update user today info
	 *
	 * @param integer $uid
	 */
	public static function updateUserTodayInfo($uid)
	{
		$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
		if (!$loginInfo) {
			return;
		}

		$lastLoginTime = $loginInfo['last_login_time'];
		$now = time();
		$todayTime = strtotime(date('Y-m-d', $now));

		$activeCount = -1;
        if ($todayTime > $lastLoginTime) {
        	$userTitleInfo = Hapyfish2_Island_HFC_User::getUserTitle($uid);
            if ($userTitleInfo && $userTitleInfo['title'] > 0) {
				$taskInfo = Hapyfish2_Island_Cache_BasicInfo::getAchievementTaskInfoByTitle($userTitleInfo['title']);
	            if ($taskInfo) {
	            	Hapyfish2_Island_HFC_User::incUserExpAndCoin($uid, $taskInfo['exp'], $taskInfo['coin']);
	            	if ($taskInfo['coin'] > 0) {
	            		if ($taskInfo['exp'] > 0) {
	            			$template_id = 103;
	            			$feedTitle = array('coin' => $taskInfo['coin'], 'exp' => $taskInfo['exp']);
	            		} else {
	            			$template_id = 101;
	            			$feedTitle = array('coin' => $taskInfo['coin']);
	            		}
	            	} else {
	            		$template_id = 102;
	            		$feedTitle = array('exp' => $taskInfo['exp']);
	            	}
	            	$feedTitle['title'] = Hapyfish2_Island_Cache_BasicInfo::getTitleName($userTitleInfo['title']);

                	$feed = array(
                		'uid' => $uid,
						'template_id' => $template_id,
						'actor' => $uid,
						'target' => $uid,
						'title' => $feedTitle,
						'type' => 3,
						'create_time' => $now
                	);
					Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
	            }
	        }

            $activeResult = self::loginActivity($uid, $loginInfo, $todayTime, $now);
            $activeCount = $activeResult['activeCount'];
            $loginInfo['active_login_count'] = $activeResult['newActiveCount'];
            if ($loginInfo['active_login_count'] > $loginInfo['max_active_login_count']) {
            	$loginInfo['max_active_login_count'] = $loginInfo['active_login_count'];
            }
            $loginInfo['last_login_time'] = $now;
            $loginInfo['today_login_count'] = 1;

			if ( $loginInfo['all_login_count'] < 8 ) {
            	$loginInfo['all_login_count'] += 1;
            }

			if ( $loginInfo['star_login_count'] < 15 ) {
            	$loginInfo['star_login_count'] += 1;
            }

            Hapyfish2_Island_HFC_User::updateUserLoginInfo($uid, $loginInfo, true);

            //add log
			$logger = Hapyfish2_Util_Log::getInstance();
			$userInfo = Hapyfish2_Platform_Cache_User::getUser($uid);
			$joinTime = $userInfo['create_time'];
			$gender = $userInfo['gender'];
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			$logger->report('101', array($uid, $joinTime, $gender, $userLevel));

			$ids = array(0, 1, 2);

			foreach ($ids as $id) {
				$key = 'i:u:flashstrom' . $uid . $id;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$cache->set($key, -1);
			}
			
			//登陆补偿截止时间2011-10-26 23:59:59
        	if(time() <= 1319644799 ){
	            $gained = Hapyfish2_Island_Bll_CompensationEvent::isGained($uid, 7);
	            if (!$gained) {
	            	Hapyfish2_Island_Bll_CompensationEvent::gain($uid, 7);
	            }
            }
        } else {
        	$loginInfo['last_login_time'] = $now;
        	$loginInfo['today_login_count'] += 1;
        	Hapyfish2_Island_HFC_User::updateUserLoginInfo($uid, $loginInfo);
        }

        $showViewNews = Hapyfish2_Island_Cache_User::showEZine($uid, $todayTime);
        return array('activeCount' => $activeCount, 'showViewNews' => $showViewNews);
	}

    /**
     * init swf list
     *
     * @param integer $uid
     */
	public static function loginActivity($uid, $loginInfo, $todayTime, $now)
	{
		$activeCount = -1;
		$newActiveCount = 1;

		if ($loginInfo['last_login_time'] + 24*3600 < $todayTime) {
			$interval = Hapyfish2_Island_Cache_BasicInfo::getActLoginInterval();
			if ($interval > 0) {
				if ($loginInfo['last_login_time'] + 24*3600 + $interval > $todayTime) {
					$activeCount = $loginInfo['active_login_count'];
					$newActiveCount = $activeCount + 1;
				} else {
					$activeCount = 0;
				}
			} else {
				$activeCount = 0;
			}
		} else if ($loginInfo['last_login_time'] < $todayTime && $loginInfo['active_login_count'] > 0) {
			$activeCount = $loginInfo['active_login_count'];
			$newActiveCount = $activeCount + 1;
			if ($activeCount > 5) {
				$activeCount = 5;
			}

			//连续登陆奖励 todo here
		}

		return array('newActiveCount' => $newActiveCount, 'activeCount' => $activeCount);
	}

    /**
     * save photo
     *
     * @param integer $uid
     */
	public static function savePhoto($uid)
	{
		$result = array('status');
        //update achievement task,27
        try {
        	Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_27', 1);
			//task id 3071,task type 27
			$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3071);
			if ( $checkTask['status'] == 1 ) {
				$result['finishTaskId'] = $checkTask['finishTaskId'];
			}
			$result['status'] = 1;
        } catch (Exception $e) {
        }
        return $result;
	}

	/**
     * get star gift
     *
     * @param integer $uid
     * @param integer $sid
     */
	public static function getStarGift($uid, $sid)
	{
		$result = array('status' => -1);
		//1-摩羯,2-水瓶,3-双鱼,4-白羊,5-金牛,6-双子,7-巨蟹,8-狮子,9-处女,10-天秤,11-天蝎,12-射手
		//get user login info, star_login_count
		$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
		$starDays = $loginInfo['star_login_count'];

		if ( $starDays < 15 ) {
			$result['content'] = '您的累计登录时间不足15天，请先满足了再来吧。';
			return $result;
		}

		//get user star info
		$starResult = Hapyfish2_Island_Cache_UserStar::getStarInfo($uid);
		$starList = $starResult['starList'];
		if ( $starList[$sid] == 2 ) {
			$result['content'] = '对不起，不能重复领取星座奖励哦，请换一个再来吧。';
			return $result;
		}
		else if ( $starList[$sid] == 0 ) {
			$result['content'] = '对不起，这个星座还没有开通，请换一个再来吧。';
			return $result;
		}

		$starDb = $starResult['starDb'];
		$starDb[$sid] = 1;
		//update user star info
		Hapyfish2_Island_Cache_UserStar::updateStar($uid, $starDb);

		$starPlant = array('1' => 74632, '2' => 74732, '3' => 75532, '4' => 80432, '5' => 85132, '6' => 85232,
						   '7' => 85332, '8' => 85432, '9' => 85532, '10' => 85632, '11' => 85732, '12' => 85832);

		$plantId = $starPlant[$sid];
        $itemId = substr($plantId, -2, 2);
		$newPlant = array(
			'uid' => $uid,
			'cid' => $plantId,
			'item_id' => $itemId,
			'x' => 0,
			'y' => 0,
			'z' => 0,
			'mirro' => 0,
			'can_find' => 0,
			'level' => 5,
			'status' => 0,
			'buy_time' => time(),
			'item_type' => 32
		);
		Hapyfish2_Island_HFC_Plant::addOne($uid, $newPlant);

		$starInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($plantId);

		$feed = array('uid' => $uid,
					'template_id' => 107,
					'actor' => $uid,
					'target' => $uid,
					'title' => array('name' => $starInfo['name']),
					'type' => 3,
					'create_time' => time());
		Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);

		//update user login info
		$loginInfo['star_login_count'] = 0;
		Hapyfish2_Island_HFC_User::updateUserLoginInfo($uid, $loginInfo, true);

		$result['status'] = 1;

		return $result;
	}

    /**
     * read star gift
     *
     * @param integer $uid
     */
	public static function readStarGift($uid)
	{
		//get user login info, star_login_count
		$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
		$starDays = $loginInfo['star_login_count'];

		//get user star info
		$starResult = Hapyfish2_Island_Cache_UserStar::getStarInfo($uid);
		$starList = $starResult['starList'];
		$starInfo = array($starList[1], $starList[2], $starList[3], $starList[4], $starList[5], $starList[6],
						  $starList[7], $starList[8], $starList[9], $starList[10], $starList[11], $starList[12]);

		$result = array('days' => $starDays, 'list' => $starInfo);
		return $result;
	}

}