<?php
class Hapyfish2_Island_Bll_Bottle
{

	// 输入宝箱id,
	public static function randbottle($idx, $num=1)
	{
		if ($idx || $idx==0) {
			$list = Hapyfish2_Island_Cache_Bottle::getAllByBottleId($idx);

			$temp = array();
			for ($i=0; $i<$num; $i++) {
				$rand = rand ($list['interval'][0]['a'], $list['interval'][19]['b']);
				foreach ($list['interval'] as $key => $val) {
					if ($val['a'] <= $rand && $rand < $val['b']) {
						$temp[] = $val['id'];
						break;
					}
				}
			}

			$indexs = array();
			foreach ($temp as $key => $val) {
				$indexs[] = $list['list'][$val];
			}

			return ($num==1) ? $indexs[0] : $indexs;
		}
		return false;
	}

	//
	public static function click($idx, $type, $uid, $betNum)
	{
		//1:免费
		//2:用钥匙
		//3:岛钻开1次
		//4:岛钻开5次
		//5:岛钻开10次

		// 需要花费钻石
		$result = array('status' => -1);

		// 黄钻用户宝石优惠
		$normal_exchange= array('3'=>4, '4'=>4, '5'=>35);
		$ygold_exchange = array('3'=>3, '4'=>3, '5'=>28);
		$numberhash = array('3'=>'1', '4'=>'5', '5'=>10);

		// 转多少次
		if (in_array($type, array(1,3))) {
			$num = 1;
		} else if ($type == 2) {
			$num = $betNum;
		} else if ($type == 4) {
			$num = 5;
		} else if ($type == 5) {
			$num = 10;
		}

		// 判断是否是黄钻
		$user = Hapyfish2_Platform_Bll_Factory::getUser($uid);
		$vipTF = $user['is_vip'] ? true : false;
		$exchange = $normal_exchange;

		// 获得玩家钻石
		$balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
		$userGold = $balanceInfo['balance'];

		// 获得玩家卡牌
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);

		// 获得玩家等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];

		/*
		 140 岛钻不足
		 110 系统错误
		 */
		if ($type == '1') {
			$todaytf = Hapyfish2_Island_Cache_Counter::getBottleTodayTF($uid);
			if ($todaytf) {
				$result['content'] = 'serverWord_110';
				return array('result'=>$result);
			}
			//Hapyfish2_Island_Cache_Counter::updateBottleTodayTF($uid);
		} else if ($type == '2') {
			if (empty($userCard['86241']) || $userCard['86241']['count']<$betNum || empty($betNum)) {
				$result['content'] = 'serverWord_110';
            	return array('result'=>$result);
			}
		} 

		$list = self::randbottle($idx, $num);
		$list = (in_array($type, array(1,3)) || ($type==2&&$betNum==1) ? array($list) : $list);

		$goldCost = 0;
		$ok2 = false;
		// 扣除玩家资源
		if ($type == 1) {
			$ok2 = self::itemstouser($list, $uid);
			if ($ok2) {
				Hapyfish2_Island_Cache_Counter::updateBottleTodayTF($uid);
			}
		} else if ($type == 2) {
			$ok2 = self::itemstouser($list, $uid);
			if ($ok2) {
				Hapyfish2_Island_HFC_Card::useUserCard($uid, '86241', $betNum);
			}
		} else if (in_array($type, array(3,4,5)) ) {
			$goldCost = $exchange[$type];
			$temp = array();
			$sumcoin = $sumstarfish = 0;
			$sumplant = $sumbuilding = $sumcard = array();
			foreach ($list as $key => $val) {
				$tips = ($val['btl_tips'] ? $val['btl_tips'] : '');
				switch ($val['type']) {
					case 'COIN' :
						$temp[] = array('coin'=>(int)$val['coin'], 'tips'=>$tips);
						$sumcoin = $sumcoin +  (int)$val['coin'];
						break;
					case 'GOLD' :
						$temp[] = array('gem'=>(int)$val['gold'], 'tips'=>$tips);
						break;
					case 'STARFISH' :
						$temp[] = array('starfish'=>(int)$val['starfish'], 'tips'=>$tips);
						$sumstarfish = $sumstarfish + (int)$val['starfish'];
						break;
					case 'CARD';
						$temp[] = array('itemId'=>(int)$val['item_id'], 'itemNum'=>(int)$val['num'], 'tips'=>$tips);
						$sumcard[] = $val['item_id'] . '*' . $val['num'];
						break;
					case 'PLANT' :
						$temp[] = array('itemId'=>(int)$val['item_id'], 'itemNum'=>(int)$val['num'], 'tips'=>$tips);
						//$sumplant = $val['item_id'] . '*' . $val['num'];
						$sumplant[] = $val['item_id'] . '*' . $val['num'];
						break;
					case 'BUILDING' :
						$temp[] = array('itemId'=>(int)$val['item_id'], 'itemNum'=>(int)$val['num'], 'tips'=>$tips);
						//$sumbuilding = $val['item_id'] . '*' . $val['num'];
						$sumbuilding[] = $val['item_id'] . '*' . $val['num'];
						break;
				}
			}
			$result = Hapyfish2_Island_Bll_QpointBuy::getToken($uid, 799, array($goldCost, $list, $temp, $num, $vipTF, $sumcoin, $sumstarfish, $sumcard, $sumplant, $sumbuilding ));
			// 加入队列，长度20
			return array('result'=>$result, 'list'=>$temp);
		}
		// 成功
		if ($ok2) {
			$temp = array();
			$sumcoin = $sumstarfish = 0;
			$sumplant = $sumbuilding = $sumcard = array();
			foreach ($list as $key => $val) {
				$tips = ($val['btl_tips'] ? $val['btl_tips'] : '');
				switch ($val['type']) {
					case 'COIN' :
						$temp[] = array('coin'=>(int)$val['coin'], 'tips'=>$tips);
						$sumcoin = $sumcoin +  (int)$val['coin'];
						break;
					case 'GOLD' :
						$temp[] = array('gem'=>(int)$val['gold'], 'tips'=>$tips);
						break;
					case 'STARFISH' :
						$temp[] = array('starfish'=>(int)$val['starfish'], 'tips'=>$tips);
						$sumstarfish = $sumstarfish + (int)$val['starfish'];
						break;
					case 'CARD';
						$temp[] = array('itemId'=>(int)$val['item_id'], 'itemNum'=>(int)$val['num'], 'tips'=>$tips);
						$sumcard[] = $val['item_id'] . '*' . $val['num'];
						break;
					case 'PLANT' :
						$temp[] = array('itemId'=>(int)$val['item_id'], 'itemNum'=>(int)$val['num'], 'tips'=>$tips);
						//$sumplant = $val['item_id'] . '*' . $val['num'];
						$sumplant[] = $val['item_id'] . '*' . $val['num'];
						break;
					case 'BUILDING' :
						$temp[] = array('itemId'=>(int)$val['item_id'], 'itemNum'=>(int)$val['num'], 'tips'=>$tips);
						//$sumbuilding = $val['item_id'] . '*' . $val['num'];
						$sumbuilding[] = $val['item_id'] . '*' . $val['num'];
						break;
				}
			}

			// 加入队列，长度20
			$userinit = Hapyfish2_Island_Bll_User::getUserInit($uid);
			$queue = array('name'=>$userinit['name'], 'time'=>time(), 'list'=>$temp);
			Hapyfish2_Island_Cache_BottleQueue::unshift($queue,$uid);

			// 记录到日志
			$log = Hapyfish2_Util_Log::getInstance();
			//COIN:10,STARFISH:10,PLANT:132,232

			// 计算付费方式
			if ($type == 1) {
				$payment = 'FREE';
			} else if ($type == 2) {
				$payment = 'KEY';
			} else if ($type == 3 || $type == 4 || $type == 5) {
				$payment = 'GOLD';
			}
			$log->report('bottle', array($uid, $payment, $num, $vipTF, $goldCost, $sumcoin,
			$sumstarfish, join(',', $sumcard), join(',', $sumplant),
			join(',', $sumbuilding)));


			$result['status'] = '1';
			if (!empty($goldCost)) {
				$result['goldChange'] = -$goldCost;
			}
			
	        //update achievement task,3096
			$addNum = $num;
	        try {
	        	Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_42', $addNum);
				//task id 3096,task type num_42
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3096);
				if ( $checkTask['status'] == 1 ) {
					$result['finishTaskId'] = $checkTask['finishTaskId'];
				}
				$result['status'] = 1;
	        } catch (Exception $e) {
	        }
			
			return array('result'=>$result, 'list'=>$temp);

		} else {
			$result['content'] = 'serverWord_110';
        	return array('result'=>$result);
		}

		return array('result'=>$result);
	}

	public static function itemstouser($list, $uid)
	{

		$com = new Hapyfish2_Island_Bll_Compensation();
		$coin = 0;
		$starfish = 0;
		$hash = array();
		foreach ($list as $key => $val) {
			switch ($val['type']) {
				case 'COIN' :
					$coin = $coin + $val['coin'];
					break;
				case 'STARFISH' :
					$starfish = $starfish + $val['starfish'];
					break;
				case 'BUILDING' :
				case 'PLANT' :
				case 'CARD' :
					if ( empty($hash[$val['item_id']])) {
						$hash[$val['item_id']] = $val['num'];
					} else {
						$hash[$val['item_id']] = $hash[$val['item_id']] + $val['num'];
					}

					break;
			}
		}

		$com->setCoin($coin);
		$com->setStarFish($starfish);
		if ($hash) {
			foreach ($hash as $key => $val) {
				$com->setItem($key, $val);
			}
		}
		return $com->sendOne($uid,'');
	}


}