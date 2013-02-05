<?php

require_once(CONFIG_DIR . '/language.php');

class Hapyfish2_Island_Bll_Payment
{

 	public static function createPayOrderId($puid)
    {
        //seconds 10 lens
        $ticks = time();

        //server id, 1 lens 0~9
        if (defined('SERVER_ID')) {
            $serverid = SERVER_ID;
        } else {
            $serverid = '0';
        }

        //max 9 lens
        //$this->user_id
        return $ticks . '_' . $serverid . '_' . $puid;
    }

	public static function getOrder($uid, $orderid)
	{
		try {
			$dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
			return $dalPayOrder->getOrder($uid, $orderid);
		} catch (Exception $e) {
		    info_log('getOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}
	}

    public static function createOrder($orderId, $uid, $amount, $gold, $tradeNo, $createTime)
    {
//        if ($amount < 10) {
//            return false;
//        }

    	if ($amount <= 0) {
    		return false;
    	}

        //add db
		$info = array(
			'orderid' => $orderId,
			'amount' => $amount,
			'gold' => $gold,
			'trade_no' => $tradeNo,
			'order_time' => $createTime,
			'uid' => $uid
		);

		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$info['user_level'] = $userLevelInfo['level'];

        try {
			$dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
			$dalPayOrder->regOrder($uid, $info);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'create-payorder-err');
			return false;
		}

		return true;
    }

    public static function completeOrder($uid, $order)
    {
        $completed = false;
        $dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();

        $ok = false;
		//发宝石
		try {
		    $userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		    $orderid = $order['orderid'];
		    $gold = $order['gold'];
			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
			$dalUser->incGold($uid, $gold);
			Hapyfish2_Island_HFC_User::reloadUserGold($uid);
			
			//update by hdf add send gold log start
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('801', array($uid, $gold, 1));
			//end				
			$ok = true;
		} catch (Exception $e) {
			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.1');
			return 1;
		}

		if ($ok) {
			$time = time();
			//更新订单状态
			$updateinfo = array('status' => 1, 'complete_time' => $time);
			Hapyfish2_Island_Bll_Vip::insertGem($uid, $order['amount']);
			$loginfo = array(
				'uid' => $uid, 'orderid' => $orderid, 'pid' => $order['trade_no'],
				'amount' => $order['amount'], 'gold' => $gold,
				'create_time' => $time, 'user_level' => $order['user_level'],
				'pay_before_gold' => $userGold,
				'summary' => $order['amount'].'RMB购买'.$gold.'宝石'
			);
			try {
				$dalPayOrder->completeOrder($uid, $orderid, $updateinfo);
				//更新充值记录
				$dalPaymentLog = Hapyfish2_Island_Dal_PaymentLog::getDefaultInstance();
				$dalPaymentLog->insert($uid, $loginfo);
			} catch (Exception $e) {
				info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.2');
			}



			//添加一元店充值信息
			if ($order['amount'] == 1) {
				Hapyfish2_Island_Event_Bll_OneGoldShop::setPayInfo($uid);
			} else {
				//充值送
				self::chargeGift($uid, $order['amount'], $time);
			}
			Hapyfish2_Island_Cache_Fish::updateUnlock5($uid);
			return 0;
		}

		info_log('[' . $uid . ':' . $orderid . ']' . 'completeOrderFailed', 'payment.err.confirm.3');
		return 1;
    }

    public static function chargeGift($uid, $amount, $nowTime)
	{
		if (!$nowTime) {
			$nowTime = time();
		}
		
    	$robot = new Hapyfish2_Island_Bll_Compensation();
    	$robot->setUid($uid);
//    	$valDayEnd = strtotime('2012-02-19 23:59:59');
//    	
//    	if ($nowTime < $valDayEnd) {
//			if ($amount == 10) {
//				$robot->setItem(141541, 10);
//	    	 	$robot->setItem(104232, 1);
//			} else if ($amount == 20) {
//				$robot->setItem(141541, 30);
//	    	 	$robot->setItem(103432, 1);
//			} else if ($amount == 50) {
//	    	 	$robot->setItem(141541, 50);
//	    	 	$robot->setItem(104232, 1);
//	    	 	$robot->setItem(103432, 1);
//			} else if ($amount == 100) {
//	    	 	$robot->setItem(141541, 100);
//	    	 	$robot->setItem(104232, 1);
//	    	 	$robot->setItem(103432, 1);
//			}
//    	} else {
			if ($amount == 10) {
				$robot->setItem(134141, 5);
				$robot->setItem(183741, 10);
			} else if ($amount == 20) {
				$robot->setItem(134141, 10);
	    	 	Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 17, 3);
	    	 	Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 10, 3);
	    	 	$robot->setItem(183741, 25);
			} else if ($amount == 50) {
	    	 	$robot->setItem(134141, 25);
	    	 	Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 17, 9);
	    	 	Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 10, 9);
	    	 	$robot->setItem(197541, 1);
	    	 	$robot->setItem(183741, 60);
	    	 	
			} else if ($amount == 100) {
	    	 	$robot->setItem(134141, 50);
	    	 	Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 17, 20);
	    	 	Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 10, 20);
	    	 	$robot->setItem(197541, 1);
	    	 	$robot->setItem(183741, 120);
			}
//    	}
    	 
    	$robot->send(LANG_PLATFORM_BASE_TXT_36);

	    return true;
	}
}