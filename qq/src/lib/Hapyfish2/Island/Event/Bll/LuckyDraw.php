<?php

class Hapyfish2_Island_Event_Bll_LuckyDraw
{
	const TXT001 = '活动暂未开放！请耐心等待！';
	const TXT002 = '很遗憾，活动已结束不能领取抽奖码！';
	const TXT003 = '您已领取过奖励，快去仓库看看吧！';
	const TXT004 = '对不起，您不符合领取条件！';
	const TXT005 = '对不起，你还没有金字塔，赶快去收集吧！';
	const TXT006 = '不好意思，你还没有达成升级条件！赶快去玩收集吧！';
	const TXT007 = '对不起，您还没有五星金字塔';
	const TXT008 = '恭喜你获得了';
	const TXT009 = '对不起，您不符合升级条件！';
	const TXT010 = '您已经有抽奖码了，赶快去抽奖吧！';
	const TXT011 = '加速加时道具卡类x100张,金币100万,建设卡1套,实物3选1';
	const TXT012 = '加速加时道具卡类x50张,金币 50万,建设卡1套';
	const TXT013 = '加速加时道具卡类x20张,金币 20万';
	const TXT014 = '加速加时道具卡类x10张';

	/**
	 * get action info
	 *
	 * @param integer $uid
	 * return array
	 */
	public static function luckyDraw($uid)
	{
		$result = array('status' => -1);

		$key = 'IpadCollect';
		$cache = Hapyfish2_Cache_LocalCache::getInstance();
		$luckyDrawCollent = $cache->get($key);

		if(!$luckyDrawCollent) {
			$dalLucky = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
			$luckyDrawCollent = $dalLucky->getLuckyDrawInfo();

			if(!$luckyDrawCollent) {
				$result['content'] = self::TXT001;
				$resultVo = array('result' => $result);

				return $resultVo;
			} else {
				$cache->set($key, $luckyDrawCollent);
			}
		}

		$nowTime = time();

		$result = array('status' => 1);
		$resultVo['result'] = $result;

		$temp1 = array();
		$temp2 = array();

		$dalb = Hapyfish2_Island_Dal_Building::getDefaultInstance();
		$temp2 = $dalb->getAllBid($uid);
		$dalp = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$temp1 = $dalp->getAllCid($uid);

		$tempVP = array();
		$tempVB = array();

		if(!empty($temp1)) {
			foreach ($temp1 as $temp) {
				foreach ($temp as $vs) {
					$tempVP[] = $vs;
				}
			}
		}

		if(!empty($temp2)) {
			foreach ($temp2 as $temp2A) {
				foreach ($temp2A as $ak) {
					$tempVB[] = $ak;
				}
			}
		}

		$Getkey = 'hasGetLuckyGift_' . $uid;
		$Getcache = Hapyfish2_Cache_LocalCache::getInstance();
		$luckyGiftSty = $Getcache->get($Getkey);

		if(!$luckyGiftSty) {
			$dalLuckySty = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
			$luckyGiftSty = $dalLuckySty->luckyDrawGift($uid);
			if($luckyGiftSty) {
				$Getcache->set($Getcache, 1);
			} else {
				$dalCDK = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
				$CD_key = $dalCDK->checkCDKey($uid);

				$resultVo['endTime'] = $luckyDrawCollent['end_time'];
				$resultVo['giftLevel'] = -1;
				$resultVo['CD_Key'] = $CD_key;
				$resultVo['lucky_CDK'] = $luckyDrawCollent['lucky_cdkey'];

				$dalJoinNum = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
				$hasJoinNum = $dalJoinNum->getJoinNum();
				$resultVo['hasNum'] = $hasJoinNum;

				$items[1] = explode('*', $luckyDrawCollent['item_1']);
				$items[1]['ids'] = explode(',', $items[1][0]);
				$items[2] = explode('*', $luckyDrawCollent['item_2']);
				$items[2]['ids'] = explode(',', $items[2][0]);
				$items[3] = explode('*', $luckyDrawCollent['item_3']);
				$items[3]['ids'] = explode(',', $items[3][0]);
				$items[4] = explode('*', $luckyDrawCollent['item_4']);
				$items[4]['ids'] = explode(',', $items[4][0]);

				$item_5 = explode('&', $luckyDrawCollent['item_5']);
				$news = explode('*', $item_5[0]);
				for($c=0; $c<count($news); $c++) {
					$newItems[] = explode(',', $news[$c]);
				}

				foreach ($newItems as $newItem) {
					foreach ($newItem as $ite) {
						$items[5]['ids'][] = $ite;
					}
				}

				foreach ($items as $i=>$v) {
					$cid_1 = -1;
					for($j=0; $j<count($v['ids']); $j++) {
						if(in_array($v['ids'][$j], $tempVP) || in_array($v['ids'][$j], $tempVB)) {
							$cid_1 = $v['ids'][$j];
						} else {
							$cid_2 = $v['ids'][0];
						}
					}

					if($cid_1 != -1) {
						$cid = $cid_1;
						$hasGet = true;
					} else {
						$cid = $cid_2;
						$hasGet = false;
					}

					$giftType = substr($cid, -2, 1);
					if ($giftType == 1) {
						$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($cid);
					} else if ($giftType == 2) {
						$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($cid);
					} else if ($giftType == 3) {
						$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
					} else if ($giftType == 4) {
						$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
					}

					if($i == 5) {
						$resultVo['data'][] = array('window' => 'window' . $i, 'cid' => $cid, 'name' => $giftInfo['name'], 'tip' => $item_5[1], 'hasGet' => $hasGet);
					} else {
						$resultVo['data'][] = array('window' => 'window' . $i, 'cid' => $cid, 'name' => $giftInfo['name'], 'tip' => $v[1], 'hasGet' => $hasGet);
					}
				}

				$resultVo['data'][] = array('window' => 'window6', 'cid' => $luckyDrawCollent['item_lucky_id']);

				$resultVo['gift']['1'] = self::TXT011;
				$resultVo['gift']['2'] = self::TXT012;
				$resultVo['gift']['3'] = self::TXT013;
				$resultVo['gift']['4'] = self::TXT014;

				return $resultVo;
			}
		}

		$KTV_MAX = 60431;

		$dalCDK = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
		$CD_key = $dalCDK->checkCDKey($uid);

		if(in_array($KTV_MAX, $tempVB) || in_array($KTV_MAX, $tempVP)) {
			$giftLevel = 5;
			$cid = $KTV_MAX;

			if($CD_key != -1) {
				$resultVo['result'] = $result;
				$resultVo['endTime'] = $luckyDrawCollent['end_time'];
				$resultVo['CD_Key'] = $CD_key;
				$resultVo['lucky_CDK'] = $luckyDrawCollent['lucky_cdkey'];

				$dalJoinNum = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
				$hasJoinNum = $dalJoinNum->getJoinNum();
				$resultVo['hasNum'] = $hasJoinNum;

				$resultVo['gift']['1'] = self::TXT011;
				$resultVo['gift']['2'] = self::TXT012;
				$resultVo['gift']['3'] = self::TXT013;
				$resultVo['gift']['4'] = self::TXT014;

				return $resultVo;
			}
		} else {
			$KTVs = array('3' => 60231, '4' => 60331);

			foreach ($KTVs as $n=>$KTV) {
				if(in_array($KTV, $tempVP) || in_array($KTV, $tempVB)) {
					$giftLevel = $n;
					$cid = $KTV;
				}
			}
		}

		$resultVo['endTime'] = $luckyDrawCollent['end_time'];
		$resultVo['giftLevel'] = $giftLevel;
		$resultVo['CD_Key'] = $CD_key;
		$resultVo['lucky_CDK'] = $luckyDrawCollent['lucky_cdkey'];

		$dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$plantMess = $dalPlant->checkUseing($uid, $cid);

        //get user current island info
        $userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);
	    $userCurrentIsland = $userVO['current_island'];
		$plantMessage = Hapyfish2_Island_HFC_Plant::getOne($uid, $plantMess['id'], 1, $userCurrentIsland);
		
		if ( $plantMessage['status'] != $userCurrentIsland ) {
			$resultVo['isOnIsland'] = 0;
		}
		else {
			$resultVo['isOnIsland'] = 1;
		}
		
		$resultVo['buildingId'] = $plantMessage['id'] . $plantMessage['item_type'];

		$dalJoinNum = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
		$hasJoinNum = $dalJoinNum->getJoinNum();
		$resultVo['hasNum'] = $hasJoinNum;

		$animals[1] = array(86332);
		$animals[2] = array(86432);
		$animals[3] = array(86532);
		$animals[4] = array(86632);
		$animals[5] = array(86732,86832,86932);

		foreach ($animals as $kv=>$animal) {
			$nid_1 = -1;
			for($k=0; $k<count($animal); $k++) {
				if(in_array($animal[$k], $tempVP) || in_array($animal[$k], $tempVB)) {
					$nid_1 = $animal[$k];
				}
			}

			if($nid_1 != -1) {
				$nid = $nid_1;
				$hasGetAnimal = true;
			} else {
				$nid = $animal[0];
				$hasGetAnimal = false;
			}

			$animalNews = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($nid);
			$tips = '“' . $animalNews['name'] . '”' . '海盗宝箱获得';

			$resultVo['data'][] = array('window' => 'window' . $kv, 'cid' => $nid, 'name' => $tips, 'hasGet' => $hasGetAnimal);
		}

		$resultVo['data'][] = array('window' => 'window6', 'cid' => $cid);

		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);

		$resultVo['data'][] = array('window' => 'window7', 'cid' => $plantInfo['next_level_cid']);

		$resultVo['gift']['1'] = self::TXT011;
		$resultVo['gift']['2'] = self::TXT012;
		$resultVo['gift']['3'] = self::TXT013;
		$resultVo['gift']['4'] = self::TXT014;

		return $resultVo;
	}

	/**
	 * get luckyDraw gift
	 *
	 * @param integer $uid
	 * return array
	 */
	public static function getLuckyDrawGift($uid)
	{
		$result = array('status' => -1);

		$key = 'IpadCollect';
		$cache = Hapyfish2_Cache_LocalCache::getInstance();
		$luckyDrawCollent = $cache->get($key);

		if(!$luckyDrawCollent) {
			$dalLucky = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
			$luckyDrawCollent = $dalLucky->getLuckyDrawInfo();

			if(!$luckyDrawCollent) {
				$result['content'] = self::TXT001;
				$resultVo = array('result' => $result);

				return $resultVo;
			} else {
				$cache->set($key, $luckyDrawCollent);
			}
		}

		$nowTime = time();

		$dalLuckySty = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
		$luckyGiftSty = $dalLuckySty->luckyDrawGift($uid);
		if($luckyGiftSty) {
			$result['content'] = self::TXT003;
			$resultVo = array('result' => $result);

			return $resultVo;
		}

		$items[1] = explode('*', $luckyDrawCollent['item_1']);
		$items[1]['ids'] = explode(',', $items[1][0]);
		$items[2] = explode('*', $luckyDrawCollent['item_2']);
		$items[2]['ids'] = explode(',', $items[2][0]);
		$items[3] = explode('*', $luckyDrawCollent['item_3']);
		$items[3]['ids'] = explode(',', $items[3][0]);
		$items[4] = explode('*', $luckyDrawCollent['item_4']);
		$items[4]['ids'] = explode(',', $items[4][0]);

		$item_5 = explode('&', $luckyDrawCollent['item_5']);
		$news = explode('*', $item_5[0]);
		for($c=0; $c<count($news); $c++) {
			$newItems[] = explode(',', $news[$c]);
		}

		foreach ($newItems as $newItem) {
			foreach ($newItem as $ite) {
				$items[5]['ids'][] = $ite;
			}
		}

		$temp1 = array();
		$temp2 = array();

		$dalb = Hapyfish2_Island_Dal_Building::getDefaultInstance();
		$temp2 = $dalb->getAllBid($uid);
		$dalp = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$temp1 = $dalp->getAllCid($uid);

		if(!empty($temp1)) {
			foreach ($temp1 as $temp) {
				foreach ($temp as $vs) {
					$tempVP[] = $vs;
				}
			}
		}

		if(!empty($temp2)) {
			foreach ($temp2 as $temp2A) {
				foreach ($temp2A as $ak) {
					$tempVB[] = $ak;
				}
			}
		}

		$hasLuckyNum = array();
		foreach ($items as $ks=>$item) {
			for($i=0; $i<count($item['ids']); $i++) {
				if(in_array($item['ids'][$i], $tempVP) || in_array($item['ids'][$i], $tempVB)) {
					$hasLuckyNum[$ks]['Num'] = 1;
				}
			}
		}

		$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($luckyDrawCollent['item_lucky_id']);

		if(count($hasLuckyNum) == 5) {
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $luckyDrawCollent['item_lucky_id'], 1);

			$dalLuckyGift = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
			$dalLuckyGift->insert($uid);

			$Getkey = 'hasGetLuckyGift_' . $uid;
			$Getcache = Hapyfish2_Cache_LocalCache::getInstance();
			$Getcache->set($Getkey, 1);

			$feed = self::TXT008 .' <font color="#FF0000">' . $giftInfo['name'] . '</font>';

			$minifeed = array('uid' => $uid,
	                          'template_id' => 0,
	                          'actor' => $uid,
	                          'target' => $uid,
	                          'title' => array('title' => $feed),
	                          'type' => 3,
	                          'create_time' => time());
	        Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
		} else {
			$result['content'] = self::TXT004;
			$resultVo = array('result' => $result);

			return $resultVo;
		}

		$result = array('status' => 1);

		$resultVo['result'] = $result;
		$resultVo['cid'] = $luckyDrawCollent['item_lucky_id'];
		$resultVo['giftName'] = $giftInfo['name'];

		return $resultVo;
	}

	/**
	 * get lucky draw CD key
	 */
	public static function getCDKey($uid)
	{
		$result = array('status' => -1);

		$dalKey = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
		$hasKey = $dalKey->checkCDKey($uid);

		$keys = 'IpadCollect';
		$caches = Hapyfish2_Cache_LocalCache::getInstance();
		$luckyDrawCollent = $caches->get($keys);

		if(!$luckyDrawCollent) {
			$luckyDrawCollent = $dalKey->getLuckyDrawInfo();

			if($luckyDrawCollent) {
				$caches->set($keys, $luckyDrawCollent);
			}
		}

		$nowTime = time();
		if($nowTime > $luckyDrawCollent['end_time']) {
			$result['content'] = self::TXT002;
			$resultVo = array('result' => $result, 'CD_Key' => $hasKey);

			return $resultVo;
		}

		if($hasKey > 0) {
			$result['content'] = self::TXT010;
			$resultVo = array('result' => $result, 'CD_Key' => $hasKey);

			return $resultVo;
		}

		$temp1 = array();
		$temp2 = array();

		$dalb = Hapyfish2_Island_Dal_Building::getDefaultInstance();
		$temp2 = $dalb->getAllBid($uid);
		$dalp = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$temp1 = $dalp->getAllCid($uid);

		if(!empty($temp1)) {
			foreach ($temp1 as $temp) {
				foreach ($temp as $vs) {
					$tempVP[] = $vs;
				}
			}
		}

		if(!empty($temp2)) {
			foreach ($temp2 as $temp2A) {
				foreach ($temp2A as $ak) {
					$tempVB[] = $ak;
				}
			}
		}

		if(!in_array(60431, $tempVP) && !in_array(60431, $tempVB)) {
			$result['content'] = self::TXT007;

			return $result;
		}

		$key = 'hasCDKey_' . $uid;
		$cache = Hapyfish2_Cache_LocalCache::getInstance();
		$CDKey = $cache->get($key);

		if(!$CDKey) {
			$dalJoinNum = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
			$hasNum = $dalJoinNum->getJoinNum();

			$first = floor($hasNum / 1000);

			$dalCDKey = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
			$CDK = $dalCDKey->getCDKey($first);

			$CDKey = $first . $CDK;

			if($CDKey != -1) {
				$dalCDKey->update($uid, $CDKey);
				$cache->set($key, $CDKey);
			}
		}

		$result = array('status' => 1);
		$resultVo = array('result' => $result, 'CD_Key' => $CDKey);

		return $resultVo;
	}
}