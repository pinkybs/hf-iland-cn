<?php

class Hapyfish2_Island_Bll_QpointBuy
{

    /*
     *$aryBuyItem[0]=array(
		'cid' =>
    	'item_id' =>
    	'item_type' =>
    	'name' =>
    	'price' =>
    	'price_type' =>
    	'count' =>
    	'buy_time' =>
    	)
   	*/
	public static function getToken($uid, $pid, $aryBuyItem)
	{
	    $resultVo = array('status' => -1);

        //game cost
        $pointInfo = array();
        $pointInfo['content'] = json_encode($aryBuyItem);

        //upgrade plant cost
        if ($pid == '199') {
        	$pointInfo['name'] = $aryBuyItem[5]['name'].'*打包购买商品';
			$pointInfo['price'] = $aryBuyItem[0];
        }
        //upgrade boat cost
        else if ($pid == '299') {
            $pointInfo['name'] = $aryBuyItem[2]['name'].'*打包购买商品';
            $pointInfo['price'] = $aryBuyItem[2]['gem'];
        }
        //buy card cost
        else if ($pid == '399') {
            $cardInfo = $aryBuyItem[0];
            $pointInfo['name'] = $cardInfo['count'].'张'.$cardInfo['name'].'*'.'打包购买商品';
            $pointInfo['price'] = $cardInfo['price']*$cardInfo['count'];
        }
        //exlarge island
        else if ($pid == '499') {
			$pointInfo['name'] = '扩建岛屿*打包购买商品';
			$pointInfo['price'] = $aryBuyItem[3];
        }
        //sale mall
        else if ($pid == '599') {
			$pointInfo['name'] = $aryBuyItem['name'].'*打包购买商品';
			$pointInfo['price'] = $aryBuyItem['price'];
        }
        //daily login
        else if ($pid == '699') {
			$pointInfo['name'] = '登录翻牌补齐*打包购买商品';
			$pointInfo['price'] = $aryBuyItem[0];
        }
        else if ($pid == '799') {
			$pointInfo['price'] = $aryBuyItem[0];
			$pointInfo['name'] = '海盗宝箱*'.'打包购买商品';
        }
        else if ($pid == '999') {
            $pointInfo['name'] = '限时限购*'.'打包购买商品';
            $pointInfo['price'] = $aryBuyItem[0]['money'];
        } 
		else if ($pid == '1099') {
			$pointInfo['name'] = '万圣节刷新卡牌*打包购买商品';
			$pointInfo['price'] = $aryBuyItem;
        }
        
		else if ($pid == '1199') {
			$pointInfo['name'] = '万圣节补齐卡牌*打包购买商品';
			$pointInfo['price'] = $aryBuyItem[0];
        }
        else if ($pid == '1299') {
            $pointInfo['name'] = '打包购买建筑商品*打包购买商品';
            $pointInfo['price'] = $aryBuyItem['price'];
        }
        
        else if ($pid == '1399'){
        	$pointInfo['name'] = '开岛*打包购买商品';
            $pointInfo['price'] = $aryBuyItem[1]['needGold'];
        }
        else if($pid == '1499'){
        	$pointInfo['name'] = '单身节赠送建筑婚纱店*打包购买商品';
            $pointInfo['price'] = $aryBuyItem['gold'];
        }
	    else if($pid == '1599'){
        	$pointInfo['name'] = $aryBuyItem['name'].'*团购购买商品';
            $pointInfo['price'] = $aryBuyItem['gold'];
        }        
        else {
            $resultVo['content'] = '物品不存在';
            return $resultVo;
        }

	    if ((int)$pointInfo['price']<=0 || (int)$pointInfo['price'] > 1000) {
            $resultVo['content'] = '非法价格';
            return $resultVo;
        }

        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $openid = $context->get('openid');
        $openkey = $context->get('openkey');

        $rest = Qzone_RestQpointPay::getInstance();
        $rest->setUser($openid, $openkey);
        $price = $pointInfo['price'];
        $num = 1;
        $img = STATIC_HOST . '/apps/island/images/pay/items/qdian.jpg';
        $token = $rest->getQpointPayToken($pid, $pointInfo['price'], $num, $img, $pointInfo['name']);
        if ($token) {
            $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			$info = array();
            $info['payitem'] = $pid.'*'.$price.'*'.$num;
            $info['platform'] = (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) ? 1 : 2;
            $info['openid'] = $openid;
            $info['user_level'] = $userLevel;
            $info['create_time'] = time();
            $info['content'] = $pointInfo['content'];

            try {
                //register qpoint buy token
                $dal = Hapyfish2_Island_Dal_QpointBuy::getDefaultInstance();
                $row = $dal->getQpointBuy($uid, $token['token']);
                if ($row) {

                    $dal->update($uid, $token['token'], $info);
                }
                else {
                    $info['uid'] = $uid;
                    $info['token'] = $token['token'];
                    $dal->insert($uid, $info);
                }
            } catch (Exception $e) {
    			info_log('getToken:'.$e->getMessage(), 'Bll_QpointBuy_Err');
    			$resultVo['content'] = '系统错误';
                return $resultVo;
		    }

            $resultVo['status'] = 1;
            //$resultVo['token'] = $token['token'];
            $resultVo['urlParams'] = $token['url_params'];
            return $resultVo;
        }
        else {
            $resultVo['content'] = '请求支付平台失败';
            return $resultVo;
        }
	}

	//0: 成功 1: 系统繁忙 2: token已过期 3: token不存在
	public static function completeBuy($uid, $info)
	{
	    try {
    	    $dal = Hapyfish2_Island_Dal_QpointBuy::getDefaultInstance();
            $row = $dal->getQpointBuy($uid, $info['token']);
    	    if (!$row) {
                return 3;
            }
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Bll_QpointBuy_Err');
			return 1;
		}

        $yearmonth = date('Ym', $info['create_time']);
        try {
            $dalLog = Hapyfish2_Island_Dal_QpointBuyDone::getDefaultInstance();
            $rowLog = $dalLog->getQpointBuyDone($uid, $info['bill_no'], $yearmonth);
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Bll_QpointBuy_Err');
			return 1;
		}
        //had already done
        if ($rowLog) {
            return 0;
        }

        $aryItem = explode('*', $info['payitem']);
        $info['item_id'] = $aryItem[0];
        $info['item_num'] = $aryItem[2];
        $info['platform'] = $row['platform'];
        $info['openid'] = $row['openid'];
        $info['user_level'] = $row['user_level'];
        $info['content'] = $row['content'];

        //dispense items
        $pid = $aryItem[0];
        $ok = self::_dispenseItems($uid, $pid, $row['content']);
        if ($ok) {
            try {
                //insert buy complete log
                $dalLog->insert($uid, $info);
            } catch (Exception $e) {
    			info_log('completeBuy:step2:'.$e->getMessage(), 'Bll_QpointBuy_Err');
		    }
        }
        else {
            info_log('completeBuy:step1:'.'robot->sendOne failed', 'Bll_QpointBuy_Err');
            return 1;
        }
        return 0;
	}

    /*
     *$aryItem[0]=array(
		'cid' =>
    	'item_id' =>
    	'item_type' =>
    	'name' =>
    	'price' =>
    	'price_type' =>
    	'count' =>
    	'buy_time' =>
    	)
   	*/
    private static function _dispenseItems($uid, $pid, $content)
	{

	    $aryItem = json_decode($content, true);
	    $time = time();
	     //upgrade plant cost
	    if ($pid == '199') {
	    	$ok = Hapyfish2_Island_HFC_Plant::updateOne($uid, $aryItem[1], $aryItem[2], true);
			if($ok){
				self::addLog($uid, $aryItem[0], $aryItem[5]['name'], $pid);
				Hapyfish2_Island_Cache_Plant::reloadAllByItemKind($uid);
				Hapyfish2_Island_HFC_User::updateUserIsland($uid, $aryItem[3]);
				Hapyfish2_Island_HFC_User::incUserExp($uid, $aryItem[4]);
				try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $aryItem[0]);
					//task id 3068,task type 19
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
				} catch (Exception $e) {
				}
			}	
	    }
	    //upgrade boat cost
        else if ($pid == '299') {
            $ok = Hapyfish2_Island_Cache_Dock::unlockShip($uid, $aryItem[0], $aryItem[1], $aryItem[3]);
            if($ok){
            	self::addLog($uid, $aryItem[2]['gem'], $aryItem[2]['name'], $pid);
            	$ok2 = Hapyfish2_Island_HFC_User::incUserExp($uid, $aryItem[4]);
            	$ukey = 'i:u:sf:u:'.$uid;
		   		$scache = Hapyfish2_Cache_Factory::getMC($uid);
		  		$scache->set($ukey, 'unlockShip');
            }
        }
        //exlarge island
	    else if ($pid == '499') {
			$ok = Hapyfish2_Island_HFC_User::updateUserLevel($uid, $aryItem[1]);
					//update builing and plant coordinate
			if($ok){
				self::addLog($uid, $aryItem[3], '扩建岛屿', $pid);
				Hapyfish2_Island_HFC_Plant::upgradeCoordinate($uid, $aryItem[0]);
				Hapyfish2_Island_HFC_Building::upgradeCoordinate($uid, $aryItem[0]);
					//report log
				$logger = Hapyfish2_Util_Log::getInstance();
				$logger->report('3001', array($uid, $aryItem[2], $aryItem[1], $aryItem[3]));
				$islandLevelInfo = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfo($aryItem[4]);
				if ($islandLevelInfo) {
					$achievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
					if ( $achievement['num_15'] < $islandLevelInfo['max_visitor'] ) {
						$achievement['num_15'] = $islandLevelInfo['max_visitor'];
						Hapyfish2_Island_HFC_Achievement::saveUserAchievement($uid, $achievement);
				        try {
							//task id 3007,task type 15
							Hapyfish2_Island_Bll_Task::checkTask($uid, 3007);
				        } catch (Exception $e) {
				        }
					}
				}
			}
        }
        else if($pid == '599'){
        	$robot = new Hapyfish2_Island_Bll_Compensation();
			foreach ($aryItem['item'] as $aryItem1) {
				$robot->setItem($aryItem1['item_id'], $aryItem1['item_num']);
			}
			$robot->setFeedTitle($aryItem['name']);
			$ok = $robot->sendOne($uid, '成功购买特卖礼包：');
			self::addLog($uid, $aryItem['price'], $aryItem['name'], $pid);
        }
		else if($pid == '699'){
			$keyp = 'i:u:Fragments：p'.$uid;
    		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$robot = new Hapyfish2_Island_Bll_Compensation();
			$robot->setItem($aryItem[1]['cid'], 1);
			$title = '恭喜你通过补齐兑换';
			$ok = $robot->sendOne($uid, $title);
			if($ok){
				self::addLog($uid, '登录翻牌补齐', $aryItem[0], $pid);
				$aryItem[2]['fragment_num'] = 0;
				$aryItem[2]['polish_num'] = $aryItem[3]['polish_num'] + 1;
				if($time >= $aryItem[3]['polish_time']){
					$aryItem[2]['polish_time'] = strtotime("+ 30 days");
					$cache->delete($keyp);
				}
				Hapyfish2_Island_Bll_Fragments::updateFragments($uid, $aryItem[2]);
			}
        }
        else if($pid == '799'){
        	$ok = Hapyfish2_Island_Bll_Bottle::itemstouser($aryItem[1], $uid);
        	self::addLog($uid, $aryItem[0], '海盗宝箱', $pid);
			// 加入队列，长度20
			$userinit = Hapyfish2_Island_Bll_User::getUserInit($uid);
			$queue = array('name'=>$userinit['name'], 'time'=>time(), 'list'=>$aryItem[2]);
			Hapyfish2_Island_Cache_BottleQueue::unshift($queue,$uid);
			$log = Hapyfish2_Util_Log::getInstance();
			//COIN:10,STARFISH:10,PLANT:132,232
			$payment = 'GOLD';
			$log->report('bottle', array($uid, $payment, $aryItem[3], $aryItem[4], $aryItem[0], $aryItem[5],
			$aryItem[6], join(',', $aryItem[7]), join(',', $aryItem[7]),
			join(',', $aryItem[9])));


			
	        //update achievement task,3096
	        try {
	        	Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_42', $aryItem[3]);
				//task id 3096,task type num_42
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3096);
	        } catch (Exception $e) {
	        }
        }
        else if($pid == '999')
        {
	        $compensation = new Hapyfish2_Island_Bll_Compensation();
			$saleCid = array();
			$saleNum = array();
			$dataVo = $aryItem[0];
			$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
			$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();
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
							$giftInfo = Hapyfish2_Island_Cache_BasicInfo::getDetailInfo($cid);
							$saleName[] = $giftInfo['name'] . 'x' . $toSend['num'];
							$saleCid[] = $toSend['cid'];
							$saleNum[] = $toSend['num'];
	
							break;
						}
					}
				}
			}
	
			$ok = $compensation->sendOne($uid, '恭喜你获得：');
			if($ok){
				self::addLog($uid, $aryItem[0]['money'], '限时限购',  $pid);
				
				if (is_array($saleName)) {
					$saleName = join(',', $saleName);
				}
		
				if (is_array($saleCid)) {
					$saleCid = join(',', $saleCid);
				}
		
				if (is_array($saleNum)){
					$saleNum = join(',', $saleNum);
				}
				$buyTime = date('Y-m-d H:i:s', $time);
		
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
			}
			
	
	
        }
		else if($pid == '1099'){
			$ok = Hapyfish2_Island_Event_Cache_HallWitches::refrushCardChance($uid, 1);
		//扣除宝石
		if($ok){
			self::addLog($uid, $aryItem, '万圣节刷新卡牌', $pid);
			try {
				Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $aryItem);
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
	        } catch (Exception $e) {}
		}
	 } 
	 else if($pid == '1199'){
	 	
	 	$compensation = new Hapyfish2_Island_Bll_Compensation();
		if ($aryItem[1]['coin'] > 0) {
			$compensation->setCoin($aryItem[1]['coin']);
		}
		if($aryItem[1]['gem'] > 0){
			$compensation->setGold($aryItem[1]['gem']);
		}
		if($aryItem[1]['starfish'] > 0){
			$compensation->setStarfish($aryItem[1]['starfish']);
		}

		if ($aryItem[1]['itemId'] && $aryItem[1]['itemNum']) {
			$compensation->setItem($aryItem[1]['itemId'], $aryItem[1]['itemNum']);
		}

		$ok = $compensation->sendOne($uid, '恭喜你通过补齐兑换获得：');
		if($ok){
			self::addLog($uid, $aryItem[0], '万圣节补齐卡牌', $pid);
			foreach ($aryItem[2] as $lackKey => $lackVa) {
				foreach ($aryItem[3] as $cardID => $cardNum) {
					if ($cardID == $lackKey) {
						$aryItem[3][$cardID] = $cardNum + $lackVa;
						break;
					}
				}
			}

		//写入缓存
		Hapyfish2_Island_Event_Cache_HallWitches::decCard($uid, $aryItem[3]);

		//扣除玩家卡牌
		foreach ($aryItem[2]['needData'] as $needData) {
			foreach ($aryItem[3] as $cardID => $cardNum) {
				if ($needData['cardid'] == $cardID) {
					$aryItem[3][$cardID] = $cardNum - $needData['maxCount'];
					break;
				}
			}
		}

		//写入缓存
		Hapyfish2_Island_Event_Cache_HallWitches::decCard($uid, $aryItem[3]);

		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		$nowTime = time();
			try {
				Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $aryItem[0]);

				//task id 3068,task type 19
				$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
				if ( $checkTask['status'] == 1 ) {
					$result['finishTaskId'] = $checkTask['finishTaskId'];
				}
	        } catch (Exception $e) {}
		$report = array($uid, $aryItem[4], $aryItem[0]);

		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('406', $report);
		}
		//为玩家补齐卡牌
	 }
        //buy island array (diy)
        else if ($pid == '1299') {
        	$ok = self::completeBuyIslandArray($uid, $pid, $aryItem);
        }
        else if ($pid == '1399')
        {
        	$islandInfo = $aryItem[0];
        	$openIslandInfo = $aryItem[1];
        	$userLevelInfo = $aryItem[2];
        	$now = $time;
        	$ok = Hapyfish2_Island_HFC_User::updateFieldUserIsland($uid, $islandInfo);
        	self::addLog($uid, $aryItem[1]['needGold'], '开岛', $pid);
        	if($ok){
        		Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $openIslandInfo['needCoin']);
				Hapyfish2_Island_HFC_User::updateUserLevel($uid, $userLevelInfo);
				switch ( $aryItem[3] ) {
	        		case 2 :
			        	$newBackground1 = array( 'uid' => $uid, 'bgid' => $aryItem[4], 'buy_time' => $now, 'item_type' => 11);
			        	$newBackground2 = array( 'uid' => $uid, 'bgid' => 23212, 'buy_time' => $now, 'item_type' => 12);
			        	$newBackground3 = array( 'uid' => $uid, 'bgid' => 22213, 'buy_time' => $now, 'item_type' => 13);
			        	$newPlant = array('uid' => $uid, 'cid' => 87032, 'status' => 0, 'item_id' => 870, 'level' => 3, 'buy_time' => $now, 'item_type' => 32);
			        	break;
	        		case 3 :
			        	$newBackground1 = array( 'uid' => $uid, 'bgid' => $aryItem[5], 'buy_time' => $now, 'item_type' => 11);
			        	$newBackground2 = array( 'uid' => $uid, 'bgid' => 23212, 'buy_time' => $now, 'item_type' => 12);
			        	$newBackground3 = array( 'uid' => $uid, 'bgid' => 22213, 'buy_time' => $now, 'item_type' => 13);
			        	$newPlant = array('uid' => $uid, 'cid' => 87532, 'status' => 0, 'item_id' => 875, 'level' => 3, 'buy_time' => $now, 'item_type' => 32);
	        			break;
	        		case 4 :
			        	$newBackground1 = array( 'uid' => $uid, 'bgid' => $aryItem[6], 'buy_time' => $now, 'item_type' => 11);
			        	$newBackground2 = array( 'uid' => $uid, 'bgid' => 23212, 'buy_time' => $now, 'item_type' => 12);
			        	$newBackground3 = array( 'uid' => $uid, 'bgid' => 22213, 'buy_time' => $now, 'item_type' => 13);
			        	$newPlant = array('uid' => $uid, 'cid' => 87832, 'status' => 0, 'item_id' => 878, 'level' => 3, 'buy_time' => $now, 'item_type' => 32);
	        			break;
	        	}
	
	        	Hapyfish2_Island_Cache_Background::addNewBackground($uid, $newBackground1);
	        	Hapyfish2_Island_Cache_Background::addNewBackground($uid, $newBackground2);
	        	Hapyfish2_Island_Cache_Background::addNewBackground($uid, $newBackground3);
	        	Hapyfish2_Island_HFC_Plant::addOne($uid, $newPlant);
	        	$priceTypeLog = 2;
	        	$price = $openIslandInfo['needGold'];
	        	$logger = Hapyfish2_Util_Log::getInstance();
				$logger->report('301', array($uid, $aryItem[3], $priceTypeLog, $price));
        	}
        }
        else if ($pid == '1499')
        {
        	$fids = json_decode($aryItem['fidsArr']);
        	
            $nowTime = time();
    		$falseTime = strtotime('2011-11-18 23:59:59');
        	
    		//get plant by cid
        	$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($aryItem['cid']);
    		
        	foreach ($fids as $fid) {
	        	Hapyfish2_Island_Bll_GiftPackage::addGift($fid, $aryItem['cid'], 1);
	
				//记录赠送好友的ID
				$key = 'ev:BlackDay:to:' . $fid . $uid;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$count = $cache->get($key);
					
				if ($count === false) {			
					$cache->set($key, 1, $falseTime);
				} else {
					$count++;
					$cache->set($key, $count, $falseTime);
				}
				
				//记录购买婚纱店人数
				$keyBuyNum = 'ev:BlackDay:buyNum';
				$cacheMC = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
				$buyNum = $cacheMC->get($keyBuyNum);
				
				if ($buyNum === false) {
					$buyNum = 1;
					$cacheMC->set($keyBuyNum, $buyNum, $falseTime);
				} else {
					$buyNum++;
					$cacheMC->set($keyBuyNum, $buyNum, $falseTime);
				}
			
				$user = Hapyfish2_Platform_Bll_User::getUser($uid);
				
				$title = '你的好友<font color="#379636">' . $user['nickname'] . '</font>赠送给你一个<font color="#FF0000">' . $plantInfo['name'] . '</font>';
				
				//发feed
				$feed = array('uid' => $fid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $fid,
							'type' => 3,
							'title' => array('title' => $title),
							'create_time' => $nowTime);
			
				Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
				
				info_log($uid . '->' . $fid, 'BlackDay');
        	}
        	
        	$ok = true;
        }
         else if ($pid == '1599') {
         	//teambuy info
         	//$result = array();
         	Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $aryItem['cid'], $aryItem['num']);
			//发送Feed
			$feed = '成功购买了'.$aryItem['name'];
        	$minifeed = array(
							'uid' => $uid,
							'template_id' => 0,
							'actor' => $uid,
							'target' => $uid,
							'title' => array('title' => $feed),
							'type' => 3,
							'create_time' => $time
						);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);  
				
			Hapyfish2_Island_Event_Cache_TeamBuy::updateStatus($uid);
			
			//$result['itemBoxChange'] = true;
			//$result['status'] = 1;
			
			//$resultVo = array('result' => $result, 'attendflag' => 1, 'buyokflag' => 1, 'buylen' => -1, 'Actlen' => -1);
			//return $resultVo;
			$ok = true;
         }
        else {
    	    $item = array();
    	    $robot = new Hapyfish2_Island_Bll_Compensation();
    	    foreach ($aryItem as $item) {
    	        $cid = $item['cid'];
    	        $itemType = substr($cid, -2, 1);
        	    //game item
        	    //itemType,1x->background,2x->building,3x->plant,4x->card
        		if ($itemType == '1') {
                    $itemInfo = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($cid);
        		}
                else if ($itemType == '2') {
                    $itemInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($cid);
                }
                else if ($itemType == '3') {
                    $itemInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
                }
                else if ($itemType == '4') {
                    $itemInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
                }
                $robot->setItem($cid, $item['count']);
    	    }
    	    $ok = $robot->sendOne($uid, '成功购买商品');
    	    $cost = $aryItem[0]['price'] * $aryItem[0]['count'];
    	    $summary = $aryItem[0]['count'].'张'.$aryItem[0]['name'];
    	    self::addLog($uid, $cost, $summary, $pid);
        }

        return $ok;

	}

/***********************  Nick for Qpoint diy ************************************/
	
	/**
	 * complete buy island array(diy 购买模块)
	 * 
	 * @param int $uid
	 * @param int $pid
	 * @param array $content
	 */
	private static function completeBuyIslandArray($uid, $pid, $content)
	{
        $isVip = $content['isVip'];
        $userLevel = $content['userLevel'];
        $userCurrentIsland = $content['userCurrentIsland'];
        $buyBackgroundAry = $content['backgroundAry'];
        $buyBuildingAry = $content['buildingAry'];
        $buyPlantAry = $content['plantAry'];
        
        //buy array
        $resultBuyBackground = self::_buyBackgroundOnIsland($uid, $buyBackgroundAry, $isVip, $userLevel, $userCurrentIsland);
        $resultByBuilding = self::_buyBuildingOnIsland($uid, $buyBuildingAry, $isVip, $userLevel, $userCurrentIsland);
        $resultBuyPlant = self::_buyPlantOnIsland($uid, $buyPlantAry, $isVip, $userLevel, $userCurrentIsland);
        
        //check praise
        $praiseChange = $resultByBuilding['praise'] + $resultBuyPlant['praise'];
        if ($praiseChange > 0) {
            $userIsland = Hapyfish2_Island_HFC_User::getUserIsland($uid);
            switch ( $userCurrentIsland ) {
                case 2 :
                    $praise = $userIsland['praise_2'];
                    break;
                case 3 :
                    $praise = $userIsland['praise_3'];
                    break;
                case 4 :
                    $praise = $userIsland['praise_4'];
                    break;
                default :
                    $praise = $userIsland['praise'];
                    break;
            }

            Hapyfish2_Island_HFC_User::changeIslandPraise($uid, $praiseChange, $userCurrentIsland, $userIsland);
        }

        $costCoin = $resultBuyBackground['coin'] + $resultByBuilding['coin'] + $resultBuyPlant['coin'];
        $costGold = $resultBuyBackground['gold'] + $resultByBuilding['gold'] + $resultBuyPlant['gold'];

        //update user achievement praise
        if ($praiseChange > 0) {
            $userPraise = $praise + $praiseChange;
            $userAchievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
            $userAchievementPraise = $userAchievement['num_13'];
            if ($userAchievementPraise < $userPraise) {
                $userAchievement['num_13'] = $userPraise;
                try {
                    Hapyfish2_Island_HFC_Achievement::updateUserAchievement($uid, $userAchievement);
                    //task id 3015,task type 13
                    $checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3015);
                    /*if ( $checkTask['status'] == 1 ) {
                        $result['finishTaskId'] = $checkTask['finishTaskId'];
                    }*/
                } catch (Exception $e) {
                }
            }
        }

        //update user achievement plant count
        $buyPlantCount = $resultBuyPlant['count'];
        if ($buyPlantCount > 0) {
            try {
                Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_17', $buyPlantCount);
                //task id 3030,task type 17
                $checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3030);
                /*if ( $checkTask['status'] == 1 ) {
                    $result['finishTaskId'] = $checkTask['finishTaskId'];
                }*/
            } catch (Exception $e) {
            }
        }

        //update user buy coin
        if ($costCoin > 0) {
            try {
                Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_14', $costCoin);
                //task id 3012,task type 14
                $checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3001);
                /*if ( $checkTask['status'] == 1 ) {
                    $result['finishTaskId'] = $checkTask['finishTaskId'];
                }*/
            } catch (Exception $e) {
            }
        }

        //update user buy gold
        if ($costGold > 0) {
            try {
                Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $costGold);
                //task id 3068,task type 19
                $checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
                /*if ( $checkTask['status'] == 1 ) {
                    $result['finishTaskId'] = $checkTask['finishTaskId'];
                }*/
            } catch (Exception $e) {
            }
        }

        return true;
	}
	
    public static function _buyBackgroundOnIsland($uid, $buyBackgroundAry, $isVip = 0, $userLevel = 0, $userCurrentIsland)
    {
        $result = array ('coin' => 0, 'gold' => 0, 'count' => 0);

        $now = time();
        $logger = Hapyfish2_Util_Log::getInstance();

        foreach ($buyBackgroundAry as $background) {
            $newBackground = array(
                'uid' => $uid,
                'bgid' => $background['cid'],
                'buy_time' => $background['buy_time'],
                'item_type' => $background['item_type']
            );

            if ($background['price_type'] == 1) {
                $price = $background['price'];

                $ok = Hapyfish2_Island_Cache_Background::addNewBackgroundOnIsland($uid, $newBackground, $userCurrentIsland);
                if ($ok) {
                    $ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $price);
                    if ($ok2) {
                        //add log
                        $summary = '购买' . $background['name'];
                        Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $price, $summary, $now);
                        $result['coin'] += $price;
                    }
                    $result['count']++;
                    //report log
                    $logger->report('201', array($uid, 1, $background['cid'], 1, $price));
                }
            } else if ($background['price_type'] == 2) {
                $price = $background['price'];
                $payInfo = array(
                    'amount' => $price,
                    'is_vip' => $isVip,
                    'item_id' => $background['cid'],
                    'item_num' => 1,
                    'uid' => $uid,
                    'user_level' => $userLevel
                );

                    $ok2 = Hapyfish2_Island_Cache_Background::addNewBackgroundOnIsland($uid, $newBackground, $userCurrentIsland);
                    if ($ok2) {
                        $summary = '购买' . $background['name'];
                        self::addLog($uid, $payInfo['amount'], $summary, $payInfo['item_id']);
                        
                        $result['gold'] += $price;
                        $result['count']++;
                        //report log
                        $logger->report('201', array($uid, 1, $background['cid'], 2, $price));
                    }
            }
        }
        return $result;
    }
	
    public static function _buyBuildingOnIsland($uid, $buyBuildingAry, $isVip = 0, $userLevel = 0, $userCurrentIsland)
    {
        $result = array ('coin' => 0, 'gold' => 0, 'count' => 0, 'praise' => 0);

        $now = time();
        $logger = Hapyfish2_Util_Log::getInstance();

        foreach ($buyBuildingAry as $building) {
            $newBuilding = array(
                'uid' => $uid,
                'cid' => $building['cid'],
                'x' => $building['x'],
                'y' => $building['y'],
                'z' => $building['z'],
                'mirro' => $building['mirro'],
                'status' => $userCurrentIsland,
                'buy_time' => $building['buy_time'],
                'item_type' => $building['item_type']
            );

            if ($building['price_type'] == 1) {
                $price = $building['price'];

                $ok = Hapyfish2_Island_HFC_Building::addOne($uid, $newBuilding, $userCurrentIsland);
                if ($ok) {
                    $ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $price);
                    if ($ok2) {
                        //add log
                        $summary = '购买' . $building['name'];
                        Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $price, $summary, $now);
                        $result['coin'] += $price;
                    }
                    $result['count']++;
                    $result['praise'] += $building['add_praise'];
                    //report log
                    $logger->report('202', array($uid, 1, $building['cid'], 1, $price));
                }
            } else if ($building['price_type'] == 2) {
                $price = $building['price'];
                $payInfo = array(
                    'amount' => $price,
                    'is_vip' => $isVip,
                    'item_id' => $building['cid'],
                    'item_num' => 1,
                    'uid' => $uid,
                    'user_level' => $userLevel
                );

                    $ok2 = Hapyfish2_Island_HFC_Building::addOne($uid, $newBuilding, $userCurrentIsland);
                    if ($ok2) {
                        $summary = '购买' . $building['name'];
                        self::addLog($uid, $payInfo['amount'], $summary, $payInfo['item_id']);
                        
                        $result['gold'] += $price;
                        $result['count']++;
                        $result['praise'] += $building['add_praise'];
                        //report log
                        $logger->report('202', array($uid, 1, $building['cid'], 2, $price));
                    }
            }
        }

        return $result;
    }
    
    public static function _buyPlantOnIsland($uid, $buyPlantAry, $isVip = 0, $userLevel = 0, $userCurrentIsland)
    {
        $result = array ('coin' => 0, 'gold' => 0, 'count' => 0, 'praise' => 0);

        $now = time();
        $logger = Hapyfish2_Util_Log::getInstance();

        foreach ($buyPlantAry as $plant) {
            $newPlant = array(
                'uid' => $uid,
                'cid' => $plant['cid'],
                'item_id' => $plant['item_id'],
                'x' => $plant['x'],
                'y' => $plant['y'],
                'z' => $plant['z'],
                'mirro' => $plant['mirro'],
                'can_find' => $plant['canFind'],
                'level' => $plant['level'],
                'status' => $userCurrentIsland,
                'buy_time' => $plant['buy_time'],
                'item_type' => $plant['item_type']
            );
            
            if ($plant['price_type'] == 1) {
                $price = $plant['price'];

                $ok = Hapyfish2_Island_HFC_Plant::addOne($uid, $newPlant, $userCurrentIsland);
                if ($ok) {
                    $ok2 = Hapyfish2_Island_HFC_User::decUserCoin($uid, $price);
                    if ($ok2) {
                        //add log
                        $summary = '购买' . $plant['name'];
                        Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $price, $summary, $now);
                        $result['coin'] += $price;
                    }
                    $result['count']++;
                    $result['praise'] += $plant['add_praise'];
                    //report log
                    $logger->report('203', array($uid, 1, $plant['cid'], 1, $price));
                }
            } else if ($plant['price_type'] == 2) {
                $price = $plant['price'];
                $payInfo = array(
                    'amount' => $price,
                    'is_vip' => $isVip,
                    'item_id' => $plant['cid'],
                    'item_num' => 1,
                    'uid' => $uid,
                    'user_level' => $userLevel
                );

                    $ok2 = Hapyfish2_Island_HFC_Plant::addOne($uid, $newPlant, $userCurrentIsland);
                    if ($ok2) {
                        $summary = '购买' . $plant['name'];
                        self::addLog($uid, $payInfo['amount'], $summary, $payInfo['item_id']);
                        
                        $result['gold'] += $price;
                        $result['count']++;
                        $result['praise'] += $plant['add_praise'];
                        //report log
                        $logger->report('203', array($uid, 1, $plant['cid'], 2, $price));
                        
						//单身节活动--记录婚纱店购买人数
						$endTime = strtotime('2011-11-18 23:59:59');
						if (($now <= $endTime) && ($plant['cid'] == 121032)) {
							$key = 'ev:BlackDay:buyNum';
							$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
							$count = $cache->get($key);
							
							$falseTime = strtotime('2011-11-19 23:59:59');
							
							if ($count === false) {
								$count = 1;
								$cache->set($key, $count, $falseTime);
							} else {
								$count++;
								$cache->set($key, $count, $falseTime);
							}
						}
                    }
            }
        }

        return $result;
    }
    
/***********************  Nick for Qpoint diy ************************************/
    
	public static function addLog($uid, $cost, $name, $pid)
	{
        $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
        $userLevel = $userLevelInfo['level'];
		
        $platformUser = Hapyfish2_Platform_Bll_Factory::getUser($uid);
        $isPlatformVip = 0;
        if ( $platformUser['is_year_vip'] || $platformUser['is_vip'] ) {
            $isPlatformVip = 1;
        }
        
        $time = time();
        
        //用户页面显示Log
		$goldInfo = array(
             'uid' => $uid,
             'cost' => $cost,
             'summary' => $name,
             'billno' => 0,
             'is_vip' => $isPlatformVip,
             'user_level' => $userLevel,
             'cid' => $pid,
             'num' => 1,
			 'create_time'=>$time
		);
        Hapyfish2_Island_Bll_Gold::addGoldLog($uid, $goldInfo);
       
        if ( $isPlatformVip == 1 ) {
        	$cost = round($cost * 0.8);
        }
        //后台统计，宝石消费记录
        $payInfo = array(
            'amount' => $cost,
            'is_vip' => $isPlatformVip,
            'item_id' => $pid,
            'item_num' => 1,
            'uid' => $uid,
            'user_level' => $userLevel,
            'time' => $time,
            'cmd' => 7,
            'result' => 0
        );
        Hapyfish2_Island_Bll_Gold::addPayOrderFlow($uid, $payInfo);
	}
}