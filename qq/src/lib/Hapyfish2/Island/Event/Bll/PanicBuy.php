<?php

class Hapyfish2_Island_Event_Bll_PanicBuy
{
	public static function getIconAct()
	{
		$nowTime = time();

		$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();

		//获取所有物品列表
		$key = 'i:e:panicbuy:alldata';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$AllData = $cache->get($key);

		if ($AllData === false) {
			try {
				$AllData = $db->getAllData();
			} catch (Exception $e) {}

			if (!$AllData) {
				return 1;
			}

			$cache->set($key, $AllData);
		}

		//获取当前出售物品
		$keyNow = 'i:e:panicbuy:now';
		$saleData = $cache->get($keyNow);

		if ($saleData === false) {
			foreach ($AllData as $vData) {
				if (($nowTime >= $vData['start_time']) && ($nowTime <= $vData['end_time'])) {
					$saleData = $vData;
					$cache->set($keyNow, $saleData, $saleData['end_time']);
					break;
				}
			}
		}

		if ($saleData === false) {
			return 1;
		}

		return 0;
	}

	//面板初始化
	public static function panicBuyInit($uid)
	{
		$result = array('status' => -1);

		$nowTime = time();

		$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();

		//获取所有物品列表
		$key = 'i:e:panicbuy:alldata';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$AllData = $cache->get($key);

		if ($AllData === false) {
			try {
				$AllData = $db->getAllData();
			} catch (Exception $e) {}

			if (!$AllData) {
				$result['content'] = 'serverWord_101';
				$resultVo['result'] = $result;

				return $resultVo;
			}

			$cache->set($key, $AllData);
		}

		//获取当前出售物品
		$keyNow = 'i:e:panicbuy:now';
		$saleData = $cache->get($keyNow);

		if ($saleData === false) {
			foreach ($AllData as $vData) {
				if (($nowTime >= $vData['start_time']) && ($nowTime <= $vData['end_time'])) {
					$saleData = $vData;
					$cache->set($keyNow, $saleData, $saleData['end_time']);
					break;
				}
			}
		}

		//$saleData不存在，不在活动期间
		if (!$saleData) {
			$result['content'] = '对不起，现在不是抢购的时间，请耐心等待！';
			$resultVo['result'] = $result;

			return $resultVo;
		}

		//没有出售价格和货币种类时返回
		if (($saleData['sale_price'] <= 0) || !$saleData['sale_type']) {
			$result['content'] = 'serverWord_101';
			$resultVo['result'] = $result;

			return $resultVo;
		}

		//获取剩余数量
		$keyNum = 'i:e:panicbuy:num';
		$hasNum = $cache->get($keyNum);

		if ($hasNum === false) {
			$hasNum = $saleData['sale_num'];
			$cache->set($keyNum, $hasNum,  $saleData['end_time']);
		}

		//获取玩家抢购状态
		$keyUser = 'i:e:panicbuy:sale:' . $uid;
		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
		$userSale = $cacheUser->get($keyUser);

		//1:可以抢购，0:不可以
		if (($userSale == 1) || ($userSale === false)) {
			$userSale = 1;
			$cacheUser->set($keyUser, $userSale);
		} else {
			$userSale = 0;
			$cacheUser->set($keyUser, $userSale, $saleData['end_time']);
		}

		//物品剩余数量为0,不能抢购
		if ($userSale == 1) {
			if ($hasNum <= 0) {
				$userSale = 0;
				//$cacheUser->set($keyUser, $userSale, $saleData['end_time']);
			}
		}

		//用户累计抢购次数
		$keybuyCount = 'i:e:panicbuy:count:' . $uid;
		$buyCount = $cacheUser->get($keybuyCount);

		if ($buyCount === false) {
			try {
				$buyCount = $db->getUserBuyCount($uid);
			} catch (Exception $e) {}

			if ($buyCount === false) {
				$buyCount = 0;
			}

			$cacheUser->set($keybuyCount, $buyCount, 3600 * 24 * 15);
		}

		if ($buyCount == 0) {
			$canBox = 0;
		} else {
			$canBox = 1;
		}

		//下一期的开始时间
		$keyNextTime = 'i:e:panicbuy:nextTime';
		$nextStartTime = $cache->get($keyNextTime);

		if ($nextStartTime == null) {
			$nextID = $saleData['sale_id'] + 1;

			foreach ($AllData as $dataValue) {
				if ($nextID == $dataValue['sale_id']) {
					$nextStartTime = $dataValue['start_time'];
				}
			}

			if ($nextStartTime != null) {
				$cache->set($keyNextTime, $nextStartTime, $saleData['end_time']);
			}
		}

		$itemIdList = array();
		$itemNumList = array();

		if ($saleData['cid']) {
			$itemIdList = explode(',', $saleData['cid']);
		}

		if ($saleData['num']) {
			$itemNumList = explode(',', $saleData['num']);
		}

		if ($saleData['coin'] == 0) {
			$saleData['coin'] = null;
		}

		if ($saleData['gold'] == 0) {
			$saleData['gold'] = null;
		}

		if ($saleData['starfish'] == 0) {
			$saleData['starfish'] = null;
		}

		$saleList = array('coin' => $saleData['coin'],
						'gem' => $saleData['gold'],
						'starfish' => $saleData['starfish'],
						'itemIdList' => $itemIdList,
						'itemNumList' => $itemNumList,
						'name' => $saleData['sale_name']
					);

		$resultVo = array('result' => array('status' => 1),
						  'canRush' => (int)$userSale,
						  'canBox' => $canBox,
						  'hasCount' => (int)$hasNum,
						  'alreadyCount' => (int)$buyCount,
						  'money' => (int)$saleData['sale_price'],
						  'moneyType' => (int)$saleData['sale_type'],
						  'time' => (int)$nextStartTime,
						  'start_time' => $saleData['start_time'],
						  'end_time' => $saleData['end_time'],
						  'item' => $saleList
					);
		return $resultVo;
	}

	//购买物品
	public static function getPanicBuyGift($uid)
	{
		$result = array('status' => -1);

		$nowTime = time();

		$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();

		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);

		$dataVo = self::panicBuyInit($uid);

		//当前时间不在抢购时间之内
		if ($dataVo['result']['status'] == -1) {
			return $dataVo;
		}

		//已经抢购了不能再买
		if ($dataVo['canRush'] == 0) {
			$result['content'] = '对不起，您已经抢购了本期物品，请耐心等待下一期！';
			$resultVo['result'] = $result;

			return $resultVo;
		}

        //owner platform info,黄钻系统
        $platformUser = Hapyfish2_Platform_Bll_Factory::getUser($uid);
        $isPlatformVip = false;
        if ( $platformUser['is_year_vip'] || $platformUser['is_vip'] ) {
        	$isPlatformVip = true;
        }

		//确定货币类型
		if ($dataVo['moneyType'] == 1) {
	        $userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
	        if ($userCoin < $dataVo['money']) {
	        	$result = array('status' => 2, 'content' => 'serverWord_137');
	            $resultVo['result'] = $result;

	            return $resultVo;
	        }
		} else {
			if ($dataVo['item']['coin'] > 0) {
				$saleName[] = $dataVo['item']['coin'] . '金币';
				$saleNum[] = $dataVo['item']['coin'];
			}
	
			if($dataVo['item']['gem'] > 0) {
				$saleName[] = $dataVo['item']['gem'] . '宝石';
				$saleNum[] = $dataVo['item']['gem'];
			}
	
			if($dataVo['item']['starfish'] > 0) {
				$saleName[] = $dataVo['item']['starfish'] . '海星';
				$saleNum[] = $dataVo['item']['starfish'];
			}
	
			if ($dataVo['item']['itemIdList'] && $dataVo['item']['itemNumList']) {
				$cidData = $dataVo['item']['itemIdList'];
				$cidNum = $dataVo['item']['itemNumList'];
				foreach ($cidData as $keyCX => $cid) {
					$toSend = array();
					foreach ($cidNum as $keyNC => $cnum) {
						if ($keyCX == $keyNC) {
							$toSend = array('cid' => $cid, 'num' => $cnum);
							$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getDetailInfo($cid);
							$saleName[] = $giftInfo['name'] . 'x' . $toSend['num'];
							$saleCid[] = $toSend['cid'];
							$saleNum[] = $toSend['num'];
	
							break;
						}
					}
				}
			}
			if (is_array($saleName)) {
				$saleName = join(',', $saleName);
			}
	
			if (is_array($saleCid)) {
				$saleCid = join(',', $saleCid);
			}
	
			if (is_array($saleNum)){
				$saleNum = join(',', $saleNum);
			}
			$message = array('sendname' => $saleName);
	        $feed = Hapyfish2_Island_Bll_Activity::send('PANIC_BUY', $uid, $message);
	        $result = Hapyfish2_Island_Bll_QpointBuy::getToken($uid, 999, array($dataVo));
			return array('result' => $result, 'feed' => $feed);
		}
		
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		$saleCid = array();
		$saleNum = array();

		if ($dataVo['item']['coin'] > 0) {
			$compensation->setCoin($dataVo['item']['coin']);
			$saleName[] = $dataVo['item']['coin'] . '金币';
			$saleNum[] = $dataVo['item']['coin'];
		}

		if($dataVo['item']['gem'] > 0) {
			//$compensation->setGold($dataVo['item']['gold']);
			$saleName[] = $dataVo['item']['gem'] . '宝石';
			$saleNum[] = $dataVo['item']['gem'];
		}

		if($dataVo['item']['starfish'] > 0) {
			$compensation->setStarfish($dataVo['item']['starfish']);
			$saleName[] = $dataVo['item']['starfish'] . '海星';
			$saleNum[] = $dataVo['item']['starfish'];
		}

		if ($dataVo['item']['itemIdList'] && $dataVo['item']['itemNumList']) {
			$cidData = $dataVo['item']['itemIdList'];
			$cidNum = $dataVo['item']['itemNumList'];
			foreach ($cidData as $keyCX => $cid) {
				$toSend = array();
				foreach ($cidNum as $keyNC=> $cnum) {
					if ($keyCX == $keyNC) {
						$toSend = array('cid' => $cid, 'num' => $cnum);

						$compensation->setItem($toSend['cid'], $toSend['num']);

						//get gift name
						$giftType = substr($toSend['cid'], -2, 1);

						if ($giftType == 1) {
							$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($toSend['cid']);
						} else if ($giftType == 2) {
							$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($toSend['cid']);
						} else if ($giftType == 3) {
							$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($toSend['cid']);
						} else if ($giftType == 4) {
							$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($toSend['cid']);
						}

						$saleName[] = $giftInfo['name'] . 'x' . $toSend['num'];
						$saleCid[] = $toSend['cid'];
						$saleNum[] = $toSend['num'];

						break;
					}
				}
			}
		}

		$compensation->sendOne($uid, '恭喜你获得：');

		if (is_array($saleName)) {
			$saleName = join(',', $saleName);
		}

		if (is_array($saleCid)) {
			$saleCid = join(',', $saleCid);
		}

		if (is_array($saleNum)){
			$saleNum = join(',', $saleNum);
		}

		
		$ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $dataVo['money']);
		if ($ok2) {
			//add log
			$summary = '购买' . $saleName;
			Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $dataVo['money'], $summary, $nowTime);
		}

		$buyTime = date('Y-m-d H:i:s', $nowTime);

		$report = array($uid, $buyTime, $saleName);

		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('405', $report);

		//剩余数量减少
		$nowNum = $dataVo['hasCount'] - 1;
		$keyNum = 'i:e:panicbuy:num';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->set($keyNum, $nowNum, $dataVo['end_time']);

		//更新用户状态
		$keyUser = 'i:e:panicbuy:sale:' . $uid;
		$cacheUser->set($keyUser, 0, $dataVo['end_time']);

		//用户累计抢购次数
		$nowhasCount = $dataVo['alreadyCount'] + 1;
		$keybuyCount = 'i:e:panicbuy:count:' . $uid;
		$cacheUser->set($keybuyCount, $nowhasCount, 3600 * 24 * 15);

		try {
			$db->updateUserBuyCount($uid, $nowhasCount);
		} catch (Exception $e) {}

        //send activity
        try {
        	$message = array('sendname' => $saleName);
	        $feed = Hapyfish2_Island_Bll_Activity::send('PANIC_BUY', $uid, $message);
        } catch (Exception $e) {}

		$coinChange = 0;
		$goldChange = 0;
		if ($dataVo['moneyType'] == 1) {
			$coinChange = $dataVo['item']['coin'] - $dataVo['money'];
		} else if ($dataVo['moneyType'] == 2) {
			$goldChange = $dataVo['item']['gem'] - $dataVo['money'];
		}

		$result = array('status' => 1, 'coinChange' => $coinChange, 'goldChange' => $goldChange);
		$resultVo = array('result' => $result, 'feed' => $feed);

		return $resultVo;
	}

	//宝箱初始化
	public static function panicBuyBox($uid)
	{
		$result = array('status' => -1);

		$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();

		//用户累计抢购次数
		$keybuyCount = 'i:e:panicbuy:count:' . $uid;
		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
		$buyCount = $cacheUser->get($keybuyCount);

		if ($buyCount === false) {
			try {
				$buyCount = $db->getUserBuyCount($uid);
			} catch (Exception $e) {}

			if ($buyCount === false) {
				$buyCount = 0;
			}

			$cacheUser->set($keybuyCount, $buyCount, 3600 * 24 * 15);
		}

		//用户已经领取到哪一期礼包的标记
		$keyOB = 'i:e:panicbuy:qishu:' . $uid;
		$hasCountBox = $cacheUser->get($keyOB);

		if ($hasCountBox == false) {
			$hasCountBox = $db->hasCountBox($uid);

			$cacheUser->set($keyOB, $hasCountBox, 3600 * 24 * 15);
		}

		//获取礼包列表
		$keyBox = 'i:e:panicbuy:box:' . $uid;
		$boxVo = $cacheUser->get($keyBox);

		if ($boxVo === false) {
			try {
				$dataVo = $db->getBoxVo($hasCountBox);
			} catch (Exception $e) {
				$result['content'] = 'serverWord_101';
				$resultVo['result'] = $result;

				return $resultVo;
			}

			if ($dataVo === false) {
				$result['content'] = 'serverWord_101';
				$resultVo['result'] = $result;

				return $resultVo;
			} else {
				$botDataNew = array();

				foreach ($dataVo as $data) {
					$botData['needTimes'] = $data['idx'];
					$botData['isGet'] = 0;

					if ($data['gold'] && ($data['gold'] > 0)) {
						$botData['gem'] = $data['gold'];
					}

					if ($data['coin'] && ($data['coin'] > 0)) {
						$botData['coin'] = $data['coin'];
					}

					if ($data['starfish'] && ($data['starfish'] > 0)) {
						$botData['starfish'] = $data['starfish'];
					}

					if ($data['sale_data']) {
						$botData['itemIdList'] = array();
						$botData['itemNumList'] = array();

						$msgCid = explode(',', $data['sale_data']);

						foreach ($msgCid as $vaCid) {
							$toCid = explode('*', $vaCid);

							$botData['itemIdList'][] = $toCid[0];
							$botData['itemNumList'][] = $toCid[1];
						}
					}

					$list[] = $botData;
				}
			}
		}

		//礼包领取状态
		$keyBoxHas = 'i:e:panicbuy:box:has:' . $uid;
		$hasGet = $cacheUser->get($keyBoxHas);

		if ($hasGet == false) {
			try {
				$hasGetsky = $db->getPanicBox($uid);
			} catch (Exception $e) {}

			if ($hasGetsky == false) {
				foreach ($list as $itemVo) {
					$hasGet[$itemVo['needTimes']] = 0;
				}

				foreach ($hasGet as $keyGetk => $valueGetk) {
					$getVo[] = $keyGetk . '*' . $valueGetk;
				}

				$boxStr = join(',', $getVo);

				try {
					$hasGetsky = $db->updatePanicBox($uid, $boxStr);
				} catch (Exception $e) {}
			} else {
				$hasGetskyNewVo = explode(',', $hasGetsky);

				foreach ($hasGetskyNewVo as $NewVo) {
					$NewDataVo = array();
					$NewDataVo = explode('*', $NewVo);

					$hasGet[$NewDataVo[0]] = $NewDataVo[1];
				}
			}

			$cacheUser->set($keyBoxHas, $hasGet, 3600 * 24 * 15);
		}

		//计算礼包领取状态
		foreach ($list as $listKey => $listItem) {
			foreach ($hasGet as $ks => $vs) {
				if ($ks == $listItem['needTimes']) {
					$list[$listKey]['isGet'] = $vs;
					break;
				}
			}
		}

		$result = array('status' => 1);
		$resultVo = array('result' => $result,
						'hasCount' => $buyCount,
						'list' => $list);

		return $resultVo;

	}

	//领取宝箱
	public static function getPanicBuyBox($uid, $idx)
	{
		$result = array('status' => -1);

		$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();
		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);

		//没有宝箱的id号返回
		if ($idx == false) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);

			return $resultVo;
		}

		$dataVo = self::panicBuyBox($uid);

		$trueID = $idx - 1;

		foreach ($dataVo['list'] as $saleKey => $saleVa) {
			if ($saleKey == $trueID) {
				$saleData = $saleVa;
				break;
			}
		}

		if ($saleData === false) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);

			return $resultVo;
		}

		//已经领取
		if ($saleData['isGet'] == 1) {
			$result['content'] = '对不起，您已经领取了这个宝箱，不能重复领取';
			$resultVo = array('result' => $result);

			return $resultVo;
		}

		//次数不够
		if ($dataVo['hasCount'] < $saleData['needTimes']) {
			$result = array('status' => 2, 'content' => '对不起，你参加抢购的次数不足以领取宝箱');
			$resultVo = array('result' => $result);

			return $resultVo;
		}

		$compensation = new Hapyfish2_Island_Bll_Compensation();

		if ($saleData['coin'] && ($saleData['coin'] > 0)) {
			$compensation->setCoin($saleData['coin']);
		}

		if ($saleData['gem'] && ($saleData['gem'] > 0)) {
			//$compensation->setGold($saleData['gem']);
		}

		if ($saleData['starfish'] && ($saleData['starfish'] > 0)) {
			$compensation->setStarFish($saleData['starfish']);
		}

		if ($saleData['itemIdList'] && $saleData['itemNumList']) {
			foreach ($saleData['itemIdList'] as $itemkey => $itemVa) {
				foreach ($saleData['itemNumList'] as $itemNumkey => $itemNumVa) {
					if ($itemkey == $itemNumkey) {
						$cid = $itemVa;
						$num = $itemNumVa;

						$compensation->setItem($cid, $num);
						break;
					}
				}
			}
		}

		$compensation->sendOne($uid, '恭喜你获得：');

		//礼包领取状态
		$keyBoxHas = 'i:e:panicbuy:box:has:' . $uid;
		$hasGet = $cacheUser->get($keyBoxHas);

		if ($hasGet === false) {
			try {
				$hasGetsky = $db->getPanicBox($uid);
			} catch (Exception $e) {}

			if ($hasGetsky == false) {
				foreach ($dataVo['list'] as $itemVo) {
					$dataVo['list']['needTimes'] = 0;
				}

				foreach ($hasGet as $keyGetk => $valueGetk) {
					$getVo[] = $keyGetk . '*' . $valueGetk;
				}

				$boxStr = join(',', $getVo);

				try {
					$hasGetsky = $db->updatePanicBox($uid, $boxStr);
				} catch (Exception $e) {}
			} else {
				$hasGetskyNewVo = explode(',', $hasGetsky);

				foreach ($hasGetskyNewVo as $NewVo) {
					$NewDataVo = array();
					$NewDataVo = explode('*', $NewVo);

					$hasGet[$NewDataVo[0]] = $NewDataVo[1];
				}
			}

			$cacheUser->set($keyBoxHas, $hasGet, 3600 * 24 * 15);
		}

		foreach ($hasGet as $dk => $dv) {
			if ($dk == $saleData['needTimes']) {
				$hasGet[$dk] = 1;
				break;
			}
		}

		//如果全部领取,礼包更新为下一组
		$dkTotal = 0;
		foreach ($hasGet as $sk) {
			$dkTotal += $sk;
		}

		if ($dkTotal == 3) {
			$keyOB = 'i:e:panicbuy:qishu:' . $uid;
			$hasCountBox = $cacheUser->get($keyOB);

			if ($hasCountBox == false) {
				try {
					$hasCountBox = $db->hasCountBox($uid);
				} catch (Exception $e) {}

				$cacheUser->set($keyOB, $hasCountBox, 3600 * 24 * 15);
			}

			$stay = $hasCountBox + 1;

			try {
				$db->updateCountBox($uid, $stay);
			} catch (Exception $e) {}

			$cacheUser->set($keyOB, $stay, 3600 * 24 * 15);

			$cacheUser->delete($keyBoxHas);
			$keyBox = 'i:e:panicbuy:box:' . $uid;
			$cacheUser->delete($keyBox);

			try {
				$db->updatePanicBox($uid, 0);
			} catch (Exception $e) {}
		} else {
			foreach ($hasGet as $keyGetk => $valueGetk) {
				$getVo[] = $keyGetk . '*' . $valueGetk;
			}

			$cacheUser->set($keyBoxHas, $hasGet, 3600 * 24 * 15);

			//更新用户DB和缓存的宝箱领取状态
			try {
				$boxStr = join(',', $getVo);
				$hasGetsky = $db->updatePanicBox($uid, $boxStr);
			} catch (Exception $e) {}
		}

		$nowTime = time();

		$buyTime = date('Y-m-d H:i:s', $nowTime);

		$report = array($uid, $buyTime, $saleData['needTimes']);

		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('406', $report);

        //send activity
        try {
	        $feed = Hapyfish2_Island_Bll_Activity::send('PANIC_BOX', $uid);
        } catch (Exception $e) {}

		$result = array('status' => 1);
		$resultVo = array('result' => $result,
						'coinChange' => $saleData['coin'],
						'alreadyCount' => $dataVo['hasCount'],
						'feed' => $feed);

		return $resultVo;
	}
}