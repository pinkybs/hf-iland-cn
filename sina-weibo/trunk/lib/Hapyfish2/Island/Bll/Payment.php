<?php

class Hapyfish2_Island_Bll_Payment
{

    public static $wbPrePayId = '1107001';

    public static $wbPayType = array(
			'1' => array('name' => '20微币兑换200宝石',   'amount' => 2000,  'gold' => 200,  'code' => '102'),
			'2' => array('name' => '50微币兑换500宝石',   'amount' => 5000,  'gold' => 500,  'code' => '202'),
			'3' => array('name' => '80微币兑换800宝石',   'amount' => 8000,  'gold' => 800,  'code' => '302'),
			'4' => array('name' => '100微币兑换1000宝石', 'amount' => 10000, 'gold' => 1000, 'code' => '402'));
    
	public static $wbPayTypeBD = array(
			'1' => array('name' => '10微币兑换110宝石',   'amount' => 1000,  'gold' => 110,  'code' => '102'),
			'2' => array('name' => '20微币兑换230宝石',   'amount' => 2000,  'gold' => 230,  'code' => '202'),
			'3' => array('name' => '50微币兑换600宝石',   'amount' => 5000,  'gold' => 600,  'code' => '302'),
			'4' => array('name' => '100微币兑换1200宝石', 'amount' => 10000, 'gold' => 1200, 'code' => '402'));

    public static function createOrderId($uid)
    {
        try {
			$dalSeq = Hapyfish2_Platform_Dal_SeqPayorder::getDefaultInstance();
            $seqId = $dalSeq->getSequence($uid);
            $dbNo = $uid % DATABASE_NODE_NUM;
            $orderId = self::$wbPrePayId . str_pad($seqId, 8, '0', STR_PAD_LEFT) . $dbNo;
		} catch (Exception $e) {
		    info_log('Hapyfish2_Island_Bll_Payment:createOrderId:' . $e->getMessage(), 'payment-err');
			$orderId = '';
		}
        return $orderId;
    }

    public static function getOrder($uid, $orderid)
	{
		try {
			$dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
			return $dalPayOrder->getOrder($uid, $orderid);
		} catch (Exception $e) {
			return null;
		}
	}

    public static function createOrder($orderId, $uid, $amount, $gold, $tradeNo, $puid, $createTime)
    {
        if (empty($createTime)) {
            $createTime = time();
        }

        //add db
		$info = array(
			'orderid' => $orderId,
			'amount' => $amount,
			'gold' => $gold,
			'trade_no' => $tradeNo,
			'order_time' => $createTime,
			'uid' => $uid,
		    'puid' => $puid
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

    public static function completeOrder($uid, $orderid)
	{

        $rowOrder = self::getOrder($uid, $orderid);
		if (empty($rowOrder)) {
			return 2;
		}

		if ($rowOrder['status'] != 0) {
			return 3;
		}

		$gold = $rowOrder['gold'];
		if ($gold <= 0) {
			return 1;
		}

		$userGoldBefore = Hapyfish2_Island_HFC_User::getUserGold($uid);
		$ok = false;
		//发宝石
		try {
			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
			$dalUser->incGold($uid, $gold);
			Hapyfish2_Island_HFC_User::reloadUserGold($uid);
			
			//update by hudanfeng add send gold log start
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('801', array($uid, $gold, 1));
			//end			
			$ok = true;
		} catch (Exception $e) {
			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.1');
			return 1;
		}

		if ($ok) {
		    require_once(CONFIG_DIR . '/language.php');

			$time = time();
			$dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
			$dalPaymentLog = Hapyfish2_Island_Dal_PaymentLog::getDefaultInstance();
			//更新订单状态
			$updateinfo = array('status' => 1, 'complete_time' => $time);
			$summary = '';
			
			
			/*
			//单身节充值加码
			if ($time > 1322668799) {
				foreach (self::$wbPayType as $payInfo) {
				    if ($payInfo['amount'] == $rowOrder['amount']) {
				        $summary = $payInfo['name'];
				        break;
				    }
				}
			} else {
				foreach (self::$wbPayTypeBD as $payInfo) {
				    if ($payInfo['amount'] == $rowOrder['amount']) {
				        $summary = $payInfo['name'];
				        break;
				    }
				}
			}
			*/
			foreach (self::$wbPayType as $payInfo) {
			    if ($payInfo['amount'] == $rowOrder['amount']) {
			        $summary = $payInfo['name'];
			        break;
			    }
			}		
				
			$loginfo = array(
				'uid' => $uid, 'orderid' => $orderid, 'pid' => $rowOrder['trade_no'],
				'amount' => $rowOrder['amount'], 'gold' => $rowOrder['gold'],
				'create_time' => $time, 'user_level' => $rowOrder['user_level'],
				'pay_before_gold' => $userGoldBefore,
				'summary' => $summary
			);
			try {
				$dalPayOrder->completeOrder($uid, $orderid, $updateinfo);
				//更新充值记录
				$dalPaymentLog->insert($uid, $loginfo);
			} catch (Exception $e) {
				info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.2');
			}

			//充值送
		    try {
			    //addition item gift after pay
			    self::_sendAdditionItem($uid, $rowOrder['amount']);
			} catch (Exception $e) {
				info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.additem');
			}

			return 0;
		}

		return 1;
	}

    private static function _sendAdditionItem($uid, $amount)
	{
		$send = new Hapyfish2_Island_Bll_Compensation();	
		$time = time();
		if($amount == 2000)
		{	/*		
			if($time <= 1322668799){
				$send->setItem('74841', 3);
			}else{
				$send->setItem('74841', 2);
			}
			*/
			$send->setItem('86241', 3);
			$send->setItem('110441', 3);
			$send->sendOne($uid, "充值送:");		
		}
		elseif($amount == 5000)
		{
			/*
			if($time <= 1322668799){
				$send->setItem('67441', 5);
			}else{
				$send->setItem('67441',3);
			}
			*/
			$send->setItem('86241', 3);
			$send->setItem('110441', 5);
			$send->setItem('134832', 1);
			$send->sendOne($uid, "充值送:");
		}
		elseif($amount == 8000)
		{
			/*
			if($time <= 1322668799){
				$send->setItem('67441', 10);
				$send->setItem('74841', 10);
			}else{
				$send->setItem('67441',5);
				$send->setItem('74841', 5);
			}
			*/
			$send->setItem('74841', 10);
			$send->setItem('86241', 10);
			$send->setItem('134932', 1);
			$send->sendOne($uid, "充值送:");
		}
		elseif($amount == 10000)
		{
			/*
			if($time <= 1322668799){
				$send->setItem('67441', 20);
				$send->setItem('74841', 20);
			}else{
				$send->setItem('67441', 10);
				$send->setItem('74841', 10);
			}
			*/
			$send->setItem('67441', 10);
			$send->setItem('74841', 10);
			$send->setItem('86241', 10);
			$send->setItem('135032', 1);
			$send->sendOne($uid, "充值送:");
		}

	    return true;
	}
}