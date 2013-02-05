<?php
require_once(CONFIG_DIR . '/language.php');

class Hapyfish2_Island_Bll_CompoundSuperVisitor
{
	public static function getSuperVisitor($uid, $ownerUid = null)
	{
		//接待自己家游客
		if ( !$ownerUid || $uid == $ownerUid ) {
			$svInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::get($uid);
			$result = self::getSVInfo($svInfo);
		}//偷取好友家游客
		else {
			$svInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::getMoochSVInfo($ownerUid);
			$result = self::getMoochSVInfo($svInfo, $uid);
		}
		
		return array('spVisitors' => $result);
	}
	
	public static function getMoochSuperVisitor($uid)
	{
		$svInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::get($uid);
		$result = self::getSVInfo($svInfo);
		return array('spVisitors' => $result);
	}
	
	/**
	 * update super visitor
	 * 
	 */
	public static function updateSuperVisitor($uid, $sid)
	{
		//get SV num，根据船只类型获取小人数量信息
		$svnumInfo = self::getSuperVisitorNumBySid($sid);
		
		$newSuperVisitor = array();
		$nowTime = time();
		//普通小人数据生成，id类型为1
		for ( $i=1; $i<=$svnumInfo['sNum']; $i++ ) {
			$sid = $nowTime.'1'.$i.'_compound';
			$cid = rand(1, 10);
			//游客实例id，游客类id
			$newSuperVisitor[] = array($sid, $cid);
		}
		
        /*$remainSvNum = Hapyfish2_Island_Cache_CompoundSuperVisitor::getTodayRemainSvNum($uid);
        if ( $remainSvNum > 0 ) {
        	$nNum = $svnumInfo['nNum'] > $remainSvNum ? $remainSvNum : $svnumInfo['nNum'];
			//特殊需求小人数据生成，id类型为2
			for ( $j=1; $j<=$nNum; $j++ ) {
				//小人需求类型
				$demandId = rand(1, 10);
				$nid = $nowTime.'2'.$j;
				$cid = rand(1, 10);
				$newSuperVisitor[] = array($nid, $cid, $demandId);
			}
        }*/
		
        if ( !empty($newSuperVisitor) ) {
			//update
			Hapyfish2_Island_Cache_CompoundSuperVisitor::add($uid, $newSuperVisitor);
			return self::getSVInfo($newSuperVisitor);
        }
        else {
        	return array();
        }
	}

	/**
	 * update mooch super visitor
	 * 
	 */
	public static function updateMoochSuperVisitor($uid, $ownerUid, $pid)
	{
		$newSuperVisitor = array();
		$nowTime = time();
		//普通小人数据生成，id类型为1
		$sid = $nowTime.'1'.$uid.$pid;
		$cid = rand(1, 10);
		//游客实例id，游客类id
		$newSuperVisitor[] = array($sid, $cid);
		
		//update
		Hapyfish2_Island_Cache_CompoundSuperVisitor::addMoochSvInfo($ownerUid, $newSuperVisitor);
		return self::getMoochSVInfo($newSuperVisitor, $uid);
	}
	
	/**
	 * get super visitor gift
	 * 点击游客获取奖励
	 * 
	 */
	public static function getSuperVisitorGift($uid, $id)
	{
		$resultVo = array('result' => array('status' => -1));
		
		//get user super visitor info
		$svData = array();
		$svInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::get($uid);
		
		//check has this super visitor,and check outtime
		foreach ( $svInfo as $i => $v ) {
			if ( $v[0] == $id ) {
				$svData = $v;
				unset($svInfo[$i]);
				break;
			}
		}
		
		if ( empty($svData) ) {
			$resultVo['result']['content'] = 'serverWord_202';
			return $resultVo;
		}
		
		$nowTime = time();
		$createTime = substr($svData[0], 0, 10);
		$outTime = 2*60 - ($nowTime-$createTime) + 1*60;
		if ( $outTime <= 0 ) {
			$resultVo['result']['content'] = 'serverWord_202';
			return $resultVo;
		}
		
		//if普通兴奋小人，else特殊需求小人
		if ( !isset($svData[2]) ) {
			//get rand award info
			$award = self::getRandAward();
			if ( $award['addExp'] > 0 ) {
				Hapyfish2_Island_HFC_User::incUserExp($uid, $award['addExp']);
				$resultVo['result']['expChange'] = $award['addExp'];
			
		        try {
			        //check level up
		        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
		            $resultVo['result']['levelUp'] = $levelUp['levelUp'];
		        } catch (Exception $e) {
		        }
			}
			if ( $award['addCoin'] > 0 ) {
				Hapyfish2_Island_HFC_User::incUserCoin($uid, $award['addCoin']);
				$resultVo['result']['coinChange'] = $award['addCoin'];
			}

			//获取收集物
			$newCollection = self::getSvGift($uid);
			if ( $newCollection > 0 ) {
				//type->7:合成材料
				$resultVo['svAward'] = array(array('type'=>7,'cid'=>$newCollection,'num'=>1));
			}

			//report log,兴奋游客点击数
			//$logger = Hapyfish2_Util_Log::getInstance();
			//$logger->report('502', array($uid));
		}
		else {
			$demandId = $svData[2];
			//get demand info by id
			$demandInfo = Hapyfish2_Island_Cache_BasicInfo::getSVDemandListById($demandId);
			
			if ( !$demandInfo ) {
				$resultVo['result']['content'] = 'serverWord_202';
				return $resultVo;
			}
			
			//get need info
			$needArray = self::transformData($demandInfo['needs']);
			$needCoin = $needArray['coin'];
			$needGold = $needArray['gold'];
			$needStar = $needArray['star'];
			$needItem = $needArray['item'];

			//check need list
			if ( $needCoin > 0 ) {
		        $userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
		        if ($userCoin < $needCoin) {
					$resultVo['result']['content'] = 'serverWord_137';
					return $resultVo;
		        }
			}
			if ( $needGold > 0 ) {
		        $balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
		        if (!$balanceInfo) {
					$resultVo['result']['content'] = 'serverWord_1002';
					return $resultVo;
		        }
				$userGold = $balanceInfo['balance'];
				if ($userGold < $needGold) {
					$resultVo['result']['content'] = 'serverWord_140';
					return $resultVo;
				}
			}
			if ( !empty($needItem) ) {
				//暂时仅限道具卡,type=41
				$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
				foreach ( $needItem as $item ) {
					$cid = $item['cid'];
					$num = $item['num'];
					if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < $num) {
						$resultVo['result']['content'] = 'serverWord_105';
						return $resultVo;
					}
				}
			}
			if ( $needStar > 0 ) {
		        $userStarFish = Hapyfish2_Island_HFC_User::getUserStarFish($uid);
		        if ($userStarFish < $needStar) {
					$resultVo['result']['content'] = 'serverWord_203';
					return $resultVo;
		        }
			}

			//get award info
			$awardArray = self::transformData($demandInfo['awards']);
			$awardCoin = $awardArray['coin'];
			$awardGold = $awardArray['gold'];
			$awardStar = $awardArray['star'];
			$awardItem = $awardArray['item'];
			$awardExp  = $awardArray['exp'];
			
			//start
			try {
				//发放奖励
				if ( $awardCoin > 0 ) {
					Hapyfish2_Island_HFC_User::incUserCoin($uid, $awardCoin);
					$resultVo['result']['coinChange'] = $awardCoin;
				}
				if ( $awardGold > 0 ) {
					//type=5,特殊小人需求奖励
					$goldInfo = array('gold' => $awardGold, 'type' => 5, 'time' => $nowTime);
					Hapyfish2_Island_Bll_Gold::add($uid, $goldInfo);
					$resultVo['result']['goldChange'] = $awardGold;
				}
				if ( $awardExp > 0 ) {
					Hapyfish2_Island_HFC_User::incUserExp($uid, $awardExp);
					$resultVo['result']['expChange'] = $awardExp;
			        try {
				        //check level up
			        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
			            $resultVo['result']['levelUp'] = $levelUp['levelUp'];
			        } catch (Exception $e) {
			        }
				}
				if ( $awardStar > 0 ) {
					Hapyfish2_Island_Bll_StarFish::add($uid, $awardStar, LANG_PLATFORM_BASE_TXT_17, $nowTime);
				}
				if ( !empty($awardItem) ) {
					$bllCompensation = new Hapyfish2_Island_Bll_Compensation();	
					foreach ( $awardItem as $aItem ) {
						$cid = $aItem['cid'];
						$num = $aItem['num'];
						$bllCompensation->setItem($cid, $num);
					}
					$bllCompensation->sendOne($uid, '', false);
					$resultVo['result']['itemBoxChange'] = true;
				}
				
				//扣除需求物品
				if ( $needCoin > 0 ) {
					$okCoin = Hapyfish2_Island_HFC_User::decUserCoin($uid, $needCoin);
					if ($okCoin) {
						//add log
						Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $needCoin, LANG_PLATFORM_BASE_TXT_16, $nowTime);
						$resultVo['result']['coinChange'] = -$needCoin;
					}
				}
				if ( $needGold > 0 ) {
					$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
					$userLevel = $userLevelInfo['level'];
					$goldInfo = array(
		        		'uid' => $uid,
		        		'cost' => $needGold,
		        		'summary' => LANG_PLATFORM_BASE_TXT_16,
		        		'user_level' => $userLevel,
		        		'cid' => 0,
		        		'num' => 1
		        	);
		        	$okGold = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		        	$resultVo['result']['goldChange'] = -$needGold;
		        	if (!$okGold) {
		        	}
				}
				if ( $needStar > 0 ) {
					$okStar = Hapyfish2_Island_Bll_StarFish::consume($uid, $needStar, LANG_PLATFORM_BASE_TXT_16, $nowTime);
					if ( !$okStar ) {
					}
				}
				if ( !empty($needItem) ) {
					//暂时仅限道具卡,type=41
					foreach ( $needItem as $nItem ) {
						$cid = $nItem['cid'];
						$num = $nItem['num'];
						Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, $num);
					}
					$resultVo['result']['itemBoxChange'] = true;
				}
				
				//update user today remain sv num
				$remainSvNum = Hapyfish2_Island_Cache_CompoundSuperVisitor::getTodayRemainSvNum($uid);
				$newSvNum = $remainSvNum - 1;
				Hapyfish2_Island_Cache_CompoundSuperVisitor::updateTodayRemainSvNum($uid, $newSvNum);
			
			} catch (Exception $e) {
				$resultVo['result']['content'] = 'serverWord_110';
				return $resultVo;
			}
			
			//report log,需求游客完成数
			//$logger = Hapyfish2_Util_Log::getInstance();
			//uid,需求id
			//$logger->report('503', array($uid, $demandId));
		}
		
		//update
		Hapyfish2_Island_Cache_CompoundSuperVisitor::update($uid, $svInfo);
		$resultVo['result']['status'] = 1;
		
		return $resultVo;
	}
	
	/**
	 * get friend super visitor gift
	 * 点击好友家游客获取奖励
	 * 
	 */
	public static function getMoochSuperVisitorGift($uid, $ownerUid, $id)
	{
		$resultVo = array('result' => array('status' => -1));
		
		//get user super visitor info
		$svData = array();
		$svInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::getMoochSvInfo($ownerUid);
		
		//check has this super visitor,and check outtime
		foreach ( $svInfo as $i => $v ) {
			if ( $v[0] == $id ) {
				$svData = $v;
				unset($svInfo[$i]);
				break;
			}
		}
		
		if ( empty($svData) ) {
			$resultVo['result']['content'] = 'serverWord_202';
			return $resultVo;
		}
		
		$nowTime = time();
		$createTime = substr($svData[0], 0, 10);
		$outTime = 2*60 - ($nowTime-$createTime) + 1*60;
		if ( $outTime <= 0 ) {
			$resultVo['result']['content'] = 'serverWord_202';
			return $resultVo;
		}
		
		$tmp = substr($svData[0], 11);
		$createUid = substr($tmp, 0, -1);
		if ( $createUid != $uid ) {
			$resultVo['result']['content'] = 'serverWord_202';
			return $resultVo;
		}			
		
		//普通兴奋小人
		//get rand award info
		$award = self::getRandAward();
		
		if ( $award['addExp'] > 0 ) {
			Hapyfish2_Island_HFC_User::incUserExp($uid, $award['addExp']);
			$resultVo['result']['expChange'] = $award['addExp'];
		
	        try {
		        //check level up
	        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
	            $resultVo['result']['levelUp'] = $levelUp['levelUp'];
	        } catch (Exception $e) {
	        }
		}
		if ( $award['addCoin'] > 0 ) {
			Hapyfish2_Island_HFC_User::incUserCoin($uid, $award['addCoin']);
			$resultVo['result']['coinChange'] = $award['addCoin'];
		}
		
		$newCollection = self::getNewCollection($uid);
		if ( $newCollection > 0 ) {
			$resultVo['svAward'] = array(array('type'=>5,'cid'=>$newCollection,'num'=>1));
		}
		
		//update
		Hapyfish2_Island_Cache_CompoundSuperVisitor::updateMoochSvInfo($ownerUid, $svInfo);
		$resultVo['result']['status'] = 1;
		
		//report log,好友家兴奋游客点击数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('504', array($uid));
		
		return $resultVo;
	}
	
	/**
	 * get user collection info
	 * 
	 * @param int $uid
	 * @return array
	 */
	public static function getUserCollection($uid)
	{
		$lstColn = Hapyfish2_Island_Cache_CompoundSuperVisitor::getUserCollection($uid);
		
		$colnVo = array();
		if ($lstColn) {
			foreach ($lstColn as $cid => $item) {
				$colnVo[] = array($cid, $item['count']);
			}
		}
		
		return array('collections' => $colnVo);
	}
	
	public static function getSvGift($uid)
	{
        $randGift = rand(1,100);
        if ( $randGift < 51 ) {
            return 0;
        }
		$list = Hapyfish2_Island_Cache_CompoundSuperVisitor::getSvGiftRandArray();
		$rand = array_rand($list);
        $giftId = $list[$rand];
		
        //获取收集物品相关信息
        $collectionInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::getSvGiftById($giftId);
        $cid = $collectionInfo['cid'];
        
        $count = 1;
        //check user collection count
        $userTodayColnList = Hapyfish2_Island_Cache_CompoundSuperVisitor::getUserTodayCollection($uid);
        if ( isset($userTodayColnList[$cid]['count']) && $userTodayColnList[$cid]['count'] >= $collectionInfo['max_num'] ) {
            return 0;
        }
        
        if ( isset($userTodayColnList[$cid]) ) {
            //当前拥有数越多，获得概率越小
            $randGet = rand(1, 100);
            //当前拥有1个，则获得概率为 30%
            if ( $userTodayColnList[$cid]['count'] == 1 ) {
                if ( $randGet < 71 ) {
                    return 0;
                }
            }
            else if ( $userTodayColnList[$cid]['count'] == 2 ) {
                if ( $randGet < 81 ) {
                    return 0;
                }
            }
            else if ( $userTodayColnList[$cid]['count'] == 3 ) {
                if ( $randGet < 91 ) {
                    return 0;
                }
            }
        }
        
        //获得收集物
        $ok = Hapyfish2_Island_Bll_Compound::addMaterialByNum($uid, $cid, $count);
        
        if ( $ok ) {
            if ( isset($userTodayColnList[$cid]) ) {
                $userTodayColnList[$cid]['count'] += $count;
            }
            else {
                $userTodayColnList[$cid] = array('count' => $count);
            }
            
            Hapyfish2_Island_Cache_CompoundSuperVisitor::updateUserTodayCollection($uid, $userTodayColnList);
            //report log,玩家获取收集物的log
            //$logger = Hapyfish2_Util_Log::getInstance();
            //$logger->report('501', array($uid, $cid, $count));
        }
        return $cid;
	}
	
	
	/**
	 * get new collection
	 * 
	 * @param int $uid
	 * @return array
	 */
	public static function getNewCollection($uid)
	{
		//获取随机收集物id
		$list = Hapyfish2_Island_Cache_BasicInfo::getCollectionRandArray();
		$rand = array_rand($list);
		$cid = $list[$rand];
		
		if ( $cid == 0 ) {
			return 0;
		}
		
		//获取收集物品相关信息
		$collectionInfo = Hapyfish2_Island_Cache_BasicInfo::getCollectionById($cid);
				
		$count = 1;
		//check user collection count
		$userTodayColnList = Hapyfish2_Island_Cache_CompoundSuperVisitor::getUserTodayCollection($uid);
		if ( isset($userTodayColnList[$cid]['count']) && $userTodayColnList[$cid]['count'] >= $collectionInfo['max_num'] ) {
			return 0;
		}
		
		if ( isset($userTodayColnList[$cid]) ) {
			//当前拥有数越多，获得概率越小
			$randGet = rand(1, 100);
			//当前拥有1个，则获得概率为 30%
			if ( $userTodayColnList[$cid]['count'] == 1 ) {
				if ( $randGet < 71 ) {
					return 0;
				}
			}
			else if ( $userTodayColnList[$cid]['count'] == 2 ) {
				if ( $randGet < 81 ) {
					return 0;
				}
			}
			else if ( $userTodayColnList[$cid]['count'] == 3 ) {
				if ( $randGet < 91 ) {
					return 0;
				}
			}
        }
        
        //两个可兑换宝石物品的收集物，概率特殊计算
        if ( $cid == 16 || $cid == 20 ) {
        	//获取今日所有玩家的总获得信息
        	$todaySvInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::getTodayAllUserSvInfo();
        	//当兑换公式13，当日兑满3000之后，不再出现收集物16
        	if ( $cid == 16 ) {
        		if ( $todaySvInfo['gid_13'] >= 3000 ) {
        			return 0;
        		}
        	}
        	else {
                if ( $todaySvInfo['gid_20'] >= 3000 ) {
                    return 0;
                }
        	}
        }
		
		$ok = Hapyfish2_Island_Cache_CompoundSuperVisitor::addUserCollection($uid, $cid, $count);
		if ( $ok ) {
			if ( isset($userTodayColnList[$cid]) ) {
				$userTodayColnList[$cid]['count'] += $count;
			}
			else {
				$userTodayColnList[$cid] = array('count' => $count);
			}
			
			Hapyfish2_Island_Cache_CompoundSuperVisitor::updateUserTodayCollection($uid, $userTodayColnList);
			//report log,玩家获取收集物的log
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('501', array($uid, $cid, $count));
		}
		return $cid;
	}
	
	/**
	 * change collection,兑换
	 * 
	 * @param int $uid
	 * @param int $gid
	 * @return array
	 */
	public static function changeCollection($uid, $gid)
	{
		$resultVo = array('resultVo' => array('status' => -1));
		
		//获取兑换公式信息
		$groupInfo = Hapyfish2_Island_Cache_BasicInfo::getCollectionGroupById($gid);
		if ( !$groupInfo ) {
			$resultVo['resultVo']['content'] = 'serverWord_101';
			return $resultVo;
		}
		
		//get user collection info
		$userCollection = Hapyfish2_Island_Cache_CompoundSuperVisitor::getUserCollection($uid);
		
		//收集物的cid和数量，对应收集物数据例: [[cid,num],[11,1],[12,1],[35,1],[13,10]]
		$needs = json_decode($groupInfo['needs']);
		$varItem = array();
		foreach ( $needs as $var ) {
			$cid = $var[0];
			$num = $var[1];
			if ( $userCollection[$cid]['count'] < $num ) {
				$resultVo['resultVo']['content'] = 'serverWord_204';
				return $resultVo;
			}
			$userCollection[$cid]['count'] -= $num;
			$userCollection[$cid]['update'] = 1;
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
			$feedTitle = LANG_PLATFORM_BASE_TXT_19;		
			if ( $awardCoin > 0 ) {
				Hapyfish2_Island_HFC_User::incUserCoin($uid, $awardCoin);
				$feedTitle .= $awardCoin.LANG_PLATFORM_BASE_TXT_01.' ';
				$resultVo['resultVo']['coinChange'] = $awardCoin;
			}
			if ( $awardGold > 0 ) {
				//type = 6,收集物品兑换
				$goldInfo = array('gold' => $awardGold, 'type' => 6, 'time' => $nowTime);
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
				//暂时仅限道具卡,type=41
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

			//扣除收集物品
			Hapyfish2_Island_Cache_CompoundSuperVisitor::updateUsercollection($uid, $userCollection, true);
			
			//记录当日所有玩家的公式兑换总次数
			if ( $gid == 13 || $gid == 20 ) {
				$key = 'gid_'.$gid;
	            $todaySvInfo = Hapyfish2_Island_Cache_CompoundSuperVisitor::getTodayAllUserSvInfo();
                $todaySvInfo[$key]++;
	            $todaySvInfo['time'] = time();
	            Hapyfish2_Island_Cache_CompoundSuperVisitor::updateTodayAllUserSvInfo($todaySvInfo);
			}
			
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
        $logger->report('505', array($uid, $gid));
            
		return $resultVo;
	}
	
	public static function getSVInfo($data)
	{
		$info = array();
		$nowTime = time();
		
		foreach ( $data as $v ) {
			$createTime = substr($v[0], 0, 10);
			$outTime = 2*60 - ($nowTime - $createTime);
			//$outTime = 10*60 - ($nowTime - $createTime);
			
			if ( $outTime > 0 ) {
				if ( isset($v[2]) ) {
					$awardAble = false;
					$demandId = $v[2];
				}
				else {
					$awardAble = true;
					$demandId = 0;
				}
				$info[] = array('cid' => $v[1],
								'id' => $v[0],
								'outTime' => $outTime,
								'awardAble' => $awardAble,
								'demandId' => $demandId);
			}
		}
		return $info;
	}
	
	public static function getMoochSVInfo($data, $uid)
	{
		$info = array();
		$nowTime = time();
		foreach ( $data as $v ) {
			$createTime = substr($v[0], 0, 10);
			$outTime = 2*60 - ($nowTime - $createTime);
			//$outTime = 10*60 - ($nowTime - $createTime);
			$tmp = substr($v[0], 11);
			$createUid = substr($tmp, 0, -1);
			
			if ( $outTime > 0 && $createUid == $uid ) {
				$info[] = array('cid' => $v[1],
								'id' => $v[0],
								'outTime' => $outTime,
								'awardAble' => true,
								'demandId' => 0);
			}
		}
		return $info;
	}
	
	/**
	 * transform data,data: "[[type,num,cid],[1,50,0]]"
	 * 
	 */
	public static function transformData($data) 
	{
		$array = json_decode($data);
		$varCoin = $varGold = $varStar = $varExp = 0;
		$varItem = array();
		foreach ( $array as $var ) {
			$type = $var[0];
			$num = $var[1];
			$cid = $var[2];
			//TYPE->1:金币，2，宝石，3，道具或建筑，4，EXP，5:收集物，6:海星
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
				$varExp += $num;
			}
			elseif ( $type == 5 ) {
				//未开放收集物
				$varCollection += $num;
			}
			elseif ( $type == 6 ) {
				$varStar += $num;
			}
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
	 * get super visitor num,by ship id
	 * 
	 */
	public static function getSuperVisitorNumBySid($sid)
	{
		//sMinNum,sMaxNum->普通小人，点击获取随机经验或金币
		//nMinNum,nMaxNum->特殊需求小人，点击需物品交换
		$array = array(
					'135132' => array('sMinNum' => 1, 'sMaxNum' => 3, 'nNum1' => 0, 'nNum2' => 0, 'nNum3' => 0, 'npro1' => 100, 'npro2' => 0, 'npro3' => 0),
				    '135232' => array('sMinNum' => 2, 'sMaxNum' => 4, 'nNum1' => 0, 'nNum2' => 1, 'nNum3' => 0, 'npro1' => 50, 'npro2' => 100, 'npro3' => 0),
				    '135332' => array('sMinNum' => 3, 'sMaxNum' => 5, 'nNum1' => 0, 'nNum2' => 1, 'nNum3' => 0, 'npro1' => 50, 'npro2' => 100, 'npro3' => 0),
				    '135432' => array('sMinNum' => 4, 'sMaxNum' => 6, 'nNum1' => 0, 'nNum2' => 1, 'nNum3' => 0, 'npro1' => 50, 'npro2' => 100, 'npro3' => 0),
				    '135532' => array('sMinNum' => 6, 'sMaxNum' => 8, 'nNum1' => 0, 'nNum2' => 1, 'nNum3' => 0, 'npro1' => 50, 'npro2' => 100, 'npro3' => 0)
				);
		$randData = $array[$sid];
		$result = array();
		$result['sNum'] = rand($randData['sMinNum'], $randData['sMaxNum']);
		
		$rand = rand(1, 100);
		if ( $rand <= $randData['npro1'] ) {
			$result['nNum'] = $randData['nNum1'];
		}
		else if ( $rand <= $randData['npro2'] ) {
			$result['nNum'] = $randData['nNum2'];
		}
		else {
			$result['nNum'] = $randData['nNum3'];
		}
		//测试专用
		//$result['nNum'] = 3;
		//$result['sNum'] = 3;
				
		return $result;
	}
	
	/**
	 * get rand award
	 * 根据概率获得不同奖励
	 */
	public static function getRandAward()
	{
		$addExp = 0;
		$addCoin = 0;
		$rand = rand(1, 100);
		//概率分配
		switch ($rand) {
			case $rand < 26 :
				$addExp = 1;
				break;
			case $rand < 41 :
				$addExp = 2;
				break;
			case $rand < 51 :
				$addExp = 3;
				break;
			case $rand < 76 :
				$addCoin = rand(1, 50);
				break;
			case $rand < 91 :
				$addCoin = rand(51, 80);
				break;
			default :
				$addCoin = rand(81, 100);
				break;
		}
		
		return array('addExp' => $addExp, 'addCoin' => $addCoin);
	}
	
	public static function initCollection()
	{
        $collectionClass = Hapyfish2_Island_Bll_BasicInfo::getCollectionList();
        $collectionGroups = Hapyfish2_Island_Bll_BasicInfo::getCollectionGroups();
        return array('collectionClass' => $collectionClass, 'collectionGroups' => $collectionGroups);
	}

	public static function initSuperVisitor()
	{
        $svDemandClass = Hapyfish2_Island_Bll_BasicInfo::getSVDemandList();
        $spVisitorClass = Hapyfish2_Island_Bll_BasicInfo::getSVisitorList();
        return array('svDemandClass' => $svDemandClass, 'spVisitorClass' => $spVisitorClass);
	}
}