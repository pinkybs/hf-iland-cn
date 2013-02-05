<?php

class Hapyfish2_Island_Event_Bll_TeamBuy
{
	const TXT001 = '团购活动已结束,请期待下一期团购';
	const TXT002 = '你已经加入此次团购,请等待购买';
	const TXT003 = '团购活动已结束,请期待下一期团购';
	const TXT004 = '团购活动报名时间已结束,请期待下一期团购';
	const TXT005 = '你已经购买过团购物品!';

	/**
	 * 获得团购的信息
	 * @param int $uid
	 * @return Array
	 */
	public static function teamBuy($uid)
	{
		$result = array('status' => -1);

		//获得团购信息
		$data = Hapyfish2_Island_Event_Cache_TeamBuy::getData();
		if (!$data) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//获取用户参加状态$attendFlag:-1:已加入;$buyFlag:1-已购买,-1:加入没有购买)
		$status = Hapyfish2_Island_Event_Cache_TeamBuy::getStatus($uid);
		if (!$status) {
			$attendFlag = -1;
			$buyFlag = -1;
		} else {
			$attendFlag = 1;
			$buyFlag = $status;
		}
		
		$nowTime = time();
		
		$gift = explode('*', $data['gid']);
		$joinLeft = $data['ok_time'] * 3600;
		$buyLeft = $data['buy_time'] * 3600;
		$canJoinTime = $data['start_time'] + $joinLeft - $nowTime;
		$canBuyTime = $data['start_time'] + $joinLeft + $buyLeft - $nowTime;
		
		//参加和购买时间已过
		if (($canJoinTime <= 0) && ($canBuyTime <= 0)) {
			Hapyfish2_Island_Event_Cache_TeamBuy::renewData(2);
			
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//已经参加人数
		$hasNum = Hapyfish2_Island_Event_Cache_TeamBuy::getNum();
		
		//前端显示人数
		$allNum = $hasNum + $data['start_num'];
		
		//价格和类型:1-coin,2-gold
		$minInfo = explode('*', $data['min_price']);
		$maxInfo = explode('*', $data['max_price']);
		
		//获得价格类型等信息
		$priceArr = self::getPriceType($allNum, $minInfo, $maxInfo, $data);
		
		//参加时间没结束
		if ($canJoinTime > 0) {
			//没有加入的用户,显示参加面板,已经加入的用户,显示可以分享
			$man = array('attendflag' => $attendFlag);
			$item = array('cid' => $gift[0],
						'description' => $data['name'],
						'pricenow' => $priceArr['nowArr'],
						'price_jie' => $priceArr['decArr'],
						'pricebefore' => $priceArr['maxArr']);
			$Action = array('buylen' => $canBuyTime,
							'Actlen' => $canJoinTime,
							'manattendnum' => $allNum,
							'downmannum' => $priceArr['downNum'],
							'minnum' => $data['min_num'],
							'maxnum' => $data['max_num'],
							'minprice' => $priceArr['minArr']);
		} else if($canJoinTime <= 0 && $canBuyTime > 0) {
			//参加时间结束,进入购买时间
			if($status == 1) {
				$result['content'] = self::TXT005;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
			
			//已经购买的人数
			$buyNum = Hapyfish2_Island_Event_Cache_TeamBuy::getBuyNum();
			
			//获得用户coin
			$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);

			//获得用户gold
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			
			//参加时间已经结束
			$man = array('attendflag' => (int)$attendFlag, 'baoshinum' => (int)$userGold, 'coinnum' => (int)$userCoin);
			$item = array('cid' => (int)$gift[0],
						'description' => $data['name'],
						'pricenow' => $priceArr['nowArr'],
						'price_jie' => $priceArr['decArr'],
						'pricebefore' => $priceArr['maxArr']);
			$Action = array('buylen' => (int)$canBuyTime,
						'Actlen' => (int)-1,
						'buyoknum' => (int)$buyNum,
						'buyokflag' => (int)$buyFlag);
		}
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'item' => $item, 'man' => $man, 'Action' => $Action);

		return $resultVo;
	}

	/**
	 * 加入团购
	 * @param int $uid
	 * @return Array
	 */
	public static function joinTeamBuy($uid)
	{
		$result = array('status' => -1);
		
		//获得团购信息
		$data = Hapyfish2_Island_Event_Cache_TeamBuy::getData();
		if (!$data) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//获取用户参加状态
		$status = Hapyfish2_Island_Event_Cache_TeamBuy::getStatus($uid);
		if ($status) {
			$result['content'] = self::TXT002;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$nowTime = time();
		
		$gift = explode('*', $data['gid']);
		$joinLeft = $data['ok_time'] * 3600;
		$buyLeft = $data['buy_time'] * 3600;
		$canJoinTime = $data['start_time'] + $joinLeft - $nowTime;
		$canBuyTime = $data['start_time'] + $joinLeft + $buyLeft - $nowTime;
		
		//参加时间已过
		if ($canJoinTime <= 0) {
			if ($canBuyTime <= 0) {
				//购买时间已过
				Hapyfish2_Island_Event_Cache_TeamBuy::renewData(2);
			
				$result['content'] = self::TXT003;
				$resultVo = array('result' => $result);
				return $resultVo;
			} else {
				$result['content'] = self::TXT004;
				$resultVo = array('result' => $result);
				return $resultVo;
			}
		}
		
		//已经参加人数
		$hasNum = Hapyfish2_Island_Event_Cache_TeamBuy::getNum();
		
		//前端显示人数
		$allNum = $hasNum + $data['start_num'];
		
		//价格和类型:1-coin,2-gold
		$minInfo = explode('*', $data['min_price']);
		$maxInfo = explode('*', $data['max_price']);
		
/*
		//get user gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
*/
		//get user con
		$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
		
		//获得价格类型等信息
		$priceArr = self::getPriceType($allNum, $minInfo, $maxInfo, $data);
		
		//当前价格类型coin,不足返回
		if(1 == $priceArr['nowArr']['price_type']) {
			if($priceArr['nowArr']['price'] > $userCoin) {
				$result['content'] = 'serverWord_137';

				$resultVo = array('result' => $result,
								'attendflag' => -1,
								'manattendnum' => $allNum,
								'downmannum' => $priceArr['downNum'],
								'pricenownum' => $priceArr['nowArr'],
								'pricebefore' => $priceArr['maxArr'],
								'pricejie' => $priceArr['decArr']);

				return $resultVo;
			}
		}
/*
		//当前价格类型gold,不足返回
		if(2 == $priceArr['nowArr']['price_type']) {
			if($priceArr['nowArr']['price'] > $userGold) {
				$resultVo = array('result' => $result,
								'manattendnum' => $allNum,
								'downmannum' => $priceArr['downNum'],
								'pricenownum' => $priceArr['nowArr'],
								'pricebefore' => $priceArr['maxArr'],
								'pricejie' => $priceArr['decArr'],
								'attendflag' => -1);

				return $resultVo;
			}
		}
*/		
		//参加团购
		Hapyfish2_Island_Event_Cache_TeamBuy::addStatus($uid);
		
		//参加人数加1
		$allNum++;
		
		//重新获得价格类型等信息
		$priceArr = self::getPriceType($allNum, $minInfo, $maxInfo, $data);
		
		$result['status'] = 1;
		$resultVo = array('result' => $result,
						'pricenownum' => $priceArr['nowArr'],
						'pricebefore' => $priceArr['maxArr'],
						'pricejie' => $priceArr['decArr'],
						'downmannum' => $priceArr['downNum'],
						'manattendnum' => $allNum,
						'attendflag' => 1);

		return $resultVo;
		
	}

	/**
	 * 购买物品
	 * @param int $uid
	 * @return Array
	 */
	public static function buyGoods($uid)
	{
		$result = array('status' => -1);

		//获得团购信息
		$data = Hapyfish2_Island_Event_Cache_TeamBuy::getData();
		if (!$data) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//获取参加状态
		$status = Hapyfish2_Island_Event_Cache_TeamBuy::getStatus($uid);
		if (1 == $status) {
			//已经购买
			Hapyfish2_Island_Event_Cache_TeamBuy::renewData(2);
			
			$result['content'] = self::TXT005;
			$resultVo = array('result' => $result);
			return $resultVo;
		} else if (!$status) {
			//用户没有参加
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$nowTime = time();
		
		$gift = explode('*', $data['gid']);
		$joinLeft = $data['ok_time'] * 3600;
		$buyLeft = $data['buy_time'] * 3600;
		$canJoinTime = $data['start_time'] + $joinLeft - $nowTime;
		$canBuyTime = $data['start_time'] + $joinLeft + $buyLeft - $nowTime;
		
		//还没到购买时间
		if ($canJoinTime > 0) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//参加时间已过
		if (($canJoinTime <= 0) && ($canBuyTime <= 0)) {
			//购买时间已过
			Hapyfish2_Island_Event_Cache_TeamBuy::renewData(2);
		
			$result['content'] = self::TXT003;
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//已经参加人数
		$hasNum = Hapyfish2_Island_Event_Cache_TeamBuy::getNum();
		
		//前端显示人数
		$allNum = $hasNum + $data['start_num'];
		
		//价格和类型:1-coin,2-gold
		$minInfo = explode('*', $data['min_price']);
		$maxInfo = explode('*', $data['max_price']);
/*		
		//get user gold
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
*/

		//get user con
		$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
		
		//获得价格类型等信息
		$priceArr = self::getPriceType($allNum, $minInfo, $maxInfo, $data);
		
		//已经购买的人数
		$buyNum = Hapyfish2_Island_Event_Cache_TeamBuy::getBuyNum();
		
		//获得物品信息
		$giftType = substr($gift[0], -2, 1);

		if (1 == $giftType) {
			$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($gift[0]);
		} else if (2 == $giftType) {
			$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($gift[0]);
		} else if (3 == $giftType) {
			$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($gift[0]);
		} else if (4 == $giftType) {
			$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($gift[0]);
		}

		//购买价格类型gold
		if(2 == $priceArr['nowArr']['price_type']) {
			
			/*
			//宝石不足,返回
			if($priceArr['nowArr']['price'] > $userGold) {
				$resultVo = array('result' => $result,
								'attendflag' => 1,
								'pricebefore' => $priceArr['maxArr'],
								'pricenownum' => $priceArr['nowArr'],
								'price_jie' => $priceArr['decArr'],
								'buyoknum' => $buyNum,
								'buyokflag' => -1,
								'buylen' => -1,
								'Actlen' => -1);

				return $resultVo;
			}
			*/
			$itemInfo = array(
				'name'	=>	$giftInfo['name'],
				'cid'	=>	$gift[0],
				'num'	=>	$gift[1],
				'gold'	=>	$priceArr['nowArr']['price']
			);
			
			$result = Hapyfish2_Island_Bll_QpointBuy::getToken($uid, 1599, $itemInfo);
			return array('result'=>$result, 'buyokflag'=>1);
			
			/*
			//发放物品
			$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $gift[0], $gift[1]);

			//发放成功加log,减少gold
			if($ok) {
				//获取用户等级
				$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
				$userLevel = $userLevelInfo['level'];					
			
				//减少gold
				$goldInfo = array (
					'uid' => $uid,
					'cost' => $priceArr['nowArr']['price'],
					'summary' => '购买团购物品' . $giftInfo['name'],
					'user_level' => $userLevel,
					'cid' => $gift[0],
					'num' => 1
				);
		        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);

		        if($ok2) {
					try {
						Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldInfo['cost']);
					
						//task id 3068,task type 19
						$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
						if ( $checkTask['status'] == 1 ) {
							$result['finishTaskId'] = $checkTask['finishTaskId'];
						}
			        } catch (Exception $e) {
			
			        }
		        }
		        
				$Num = $priceArr['nowArr']['price'] . '宝石';

				$result['goldChange'] = -$priceArr['nowArr']['price'];
				$result['itemBoxChange'] = true;
			}
			*/
		} else {
			//价格类型coin,数量不足返回
			if($priceArr['nowArr']['price'] > $userCoin) {
				$result['content'] = 'serverWord_137';

				$resultVo = array('result' => $result,
								'attendflag' => 1,
								'pricebefore' => $priceArr['maxArr'],
								'pricenownum' => $priceArr['nowArr'],
								'price_jie' => $priceArr['decArr'],
								'buyoknum' => $buyNum,
								'buyokflag' => -1,
								'buylen' => -1,
								'Actlen' => -1);

				return $resultVo;
			}

			//发放物品
			$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $gift[0], $gift[1]);

			//发放成功,减少coin,加log
			if($ok) {
				Hapyfish2_Island_HFC_User::decUserCoin($uid, $priceArr['nowArr']['price']);
				//add log
				$summary = '购买团购物品' . $giftInfo['name'];
				Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $priceArr['nowArr']['price'], $summary, $nowTime);

				//update user buy coin
		        try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $priceArr['nowArr']['price']);
				
					//task id 3012,task type 14
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3001);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
		        } catch (Exception $e) {
		
		        }
				
				$Num = $priceArr['nowArr']['price'] . '金币';

				$result['coinChange'] = -$priceArr['nowArr']['price'];
				$result['itemBoxChange'] = true;
			}
		}
		
		//更新用户团购状态
		/*
		if ($ok2) {
			Hapyfish2_Island_Event_Cache_TeamBuy::updateStatus($uid);
			
			//发送Feed
        	$minifeed = array(
							'uid' => $uid,
							'template_id' => 106,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('info' => $Num, 'name' => $giftInfo['name']),
							'type' => 3,
							'create_time' => $nowTime
						);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
		}
		*/
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'attendflag' => 1, 'buyokflag' => 1, 'buylen' => -1, 'Actlen' => -1);

		return $resultVo;
		
	}

	/**
	 *
	 * 判断团购的价格和类型
	 * @param $uids
	 */
	public static function getPriceType($joinNum, $min, $max, $info)
	{
		//金币和宝石降价比例
		$scale_gold = explode(':', $info['scale_gold']);
		$scale_coin = explode(':', $info['scale_coin']);

		//原价类型max:1-coin,2-gold
		if($max[1] == 2) {
			//最低价类型min:1-coin,2-gold
			if($min[1] == 2) {
				$price_type = 2;

				//加入人数少于最多人数
				if($joinNum < $info['max_num']) {
					$price_Now = $max[0] - (floor($joinNum / $scale_gold[1]) * $scale_gold[0]);
					$price_Dec = $max[0] - $price_Now;
					$downManNum = $scale_gold[1] - ($joinNum % $scale_gold[1]);
				} else {
					$price_Now = $min[0];
					$price_Dec = $max[0] - $min[0];
					$downManNum = 0;
				}

				$max_price = array('price_type' => (int)$price_type, 'price' => (int)$max[0]);
				$min_price = array('price_type' => (int)$price_type, 'price' => (int)$min[0]);
				$now_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Now);
				$dec_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Dec);
			} else {
				//加入人数少于变为金币类型人数
				if($joinNum < $info['bec_num']) {
					$price_type = 2;

					$price_Now = $max[0] - (floor($joinNum / $scale_gold[1]) * $scale_gold[0]);
					$price_Dec = $max[1] - $price_Now;
					$downManNum = $scale_gold[1] - ($joinNum % $scale_gold[1]);

					$max_price = array('price_type' => (int)$price_type, 'price' => (int)$max[0]);
					$min_price = array('price_type' => (int)$min[1], 'price' => (int)$min[0]);
					$now_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Now);
					$dec_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Dec);
				} else if($joinNum >= $info['bec_num'] && $joinNum < $info['max_num']) {
					$price_Now = $info['bec_price'] - (floor(($joinNum - $info['bec_num'])/$scale_coin[1]) * $scale_coin[0]);
					$downManNum = $scale_coin[1] - (($joinNum - $info['bec_num']) % $scale_coin[1]);
					$price_Dec = $max[0] - $price_Now;

					$max_price = array('price_type' => (int)$max[1], 'price' => (int)$max[0]);
					$min_price = array('price_type' => (int)$min[1], 'price' => (int)$min[0]);
					$now_price = array('price_type' => (int)$min[1], 'price' => (int)$price_Now);
					$dec_price = array('price_type' => (int)$max[1], 'price' => (int)$price_Dec);
				} else {
					$downManNum = 0;
					$price_Dec = $max[0] - $min[0];

					$max_price = array('price_type' => (int)$max[1], 'price' => (int)$max[0]);
					$min_price = array('price_type' => (int)$min[1], 'price' => (int)$min[0]);
					$now_price = array('price_type' => (int)$min[1], 'price' => (int)$min[0]);
					$dec_price = array('price_type' => (int)$max[1], 'price' => (int)$price_Dec);
				}
			}
		} else {
			$price_type = 1;

			if($joinNum < $info['max_num']) {
				$price_type = 1;

				$price_Now = $max[0] - (floor($joinNum / $scale_coin[1]) * $scale_coin[0]);
				$price_Dec = $max[0] - $price_Now;
				$downManNum = $scale_coin[1] - ($joinNum % $scale_coin[1]);

				$now_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Now);
				$dec_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Dec);
			} else {
				$downManNum = 0;
				$price_Dec = $max[0] - $min[0];

				$now_price = array('price_type' => (int)$price_type, 'price' => (int)$min[0]);
				$dec_price = array('price_type' => (int)$price_type, 'price' => (int)$price_Dec);
			}
			$max_price = array('price_type' => (int)$price_type, 'price' => (int)$max[0]);
			$min_price = array('price_type' => (int)$price_type, 'price' => (int)$min[0]);
		}

		$resultVo = array('downNum' => (int)$downManNum,
						'maxArr' => $max_price,
						'minArr' => $min_price,
						'nowArr' => $now_price,
						'decArr' => $dec_price);

		return $resultVo;
	}

	/**
	 * @验证icon是否显示
	 * @return 0/1
	 */
	public static function checkIcon($uid)
	{
		//获得团购信息
		$data = Hapyfish2_Island_Event_Cache_TeamBuy::getData();
		if (!$data) {
			$icon = 1;
		}
		
		//获取参加状态
		$status = Hapyfish2_Island_Event_Cache_TeamBuy::getStatus($uid);
		
		$nowTime = time();
		
		$gift = explode('*', $data['gid']);
		$joinLeft = $data['ok_time'] * 3600;
		$buyLeft = $data['buy_time'] * 3600;
		$canJoinTime = $data['start_time'] + $joinLeft - $nowTime;
		$canBuyTime = $data['start_time'] + $joinLeft + $buyLeft - $nowTime;
		
		if (($canBuyTime > 0) || ($canJoinTime > 0)) {
			if ((!$status) || ($status == -1)) {
				$icon = 0;
			} else {
				$icon = 1;
			}
		} else {
			$icon = 1;
		}
		
		return $icon;
	}
	
}