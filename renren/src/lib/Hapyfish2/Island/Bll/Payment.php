<?php

class Hapyfish2_Island_Bll_Payment
{

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

    public static function regOrder($app_id, $uid, $session_key, $amount)
    {
        try {
            $renren = Xiaonei_Renren::getInstance();
            if ($renren) {
                $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
                $renren->setUser($rowUser['puid'], $session_key);
                $gold = $amount * 10;
                if ($amount == 10) {
                	$gold += 5;
                } else if ($amount == 20) {
                	$gold += 10;
                } else if ($amount == 50) {
                	$gold += 80;
                } else if ($amount == 100) {
					$gold += 200;
                }/** else if ($amount == 500) {
					$gold += 800;
                }*/
                $desc = $gold . '个宝石';
                $order = $renren->getPayOrderToken($amount, $desc);
                if ($order) {
                    $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		            $order['user_level'] = $userLevelInfo['level'];
                    $order['uid'] = $uid;
                    $order['amount'] = $amount;
                    $order['gold'] = $gold;
                    $order['order_time'] = time();
                    $dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
                    $dalPayOrder->regOrder($uid, $order);

                    return $order;
                }
            }

        }catch (Exception $e) {
			info_log('regOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
        }

        return null;
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
			$ok = true;
		} catch (Exception $e) {
			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.1');
			return 1;
		}

		if ($ok) {
			$time = time();
			//更新订单状态
			$updateinfo = array('status' => 1, 'complete_time' => $time);

			$loginfo = array(
				'uid' => $uid, 'orderid' => $orderid, 'pid' => $order['token'],
				'amount' => $order['amount'], 'gold' => $gold,
				'create_time' => $time, 'user_level' => $order['user_level'],
				'pay_before_gold' => $userGold,
				'summary' => $order['amount'].'人人豆购买'.$gold.'宝石'
			);
			try {
				$dalPayOrder->completeOrder($uid, $orderid, $updateinfo);
				//更新充值记录
				$dalPaymentLog = Hapyfish2_Island_Dal_PaymentLog::getDefaultInstance();
				$dalPaymentLog->insert($uid, $loginfo);
			} catch (Exception $e) {
				info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.2');
			}

			//充值送
			self::chargeGift($uid, $order['amount']);
			return 0;
		}

		info_log('[' . $uid . ':' . $orderid . ']' . 'completeOrderFailed', 'payment.err.confirm.3');
		return 1;
    }

    public  static function chargeGift($uid, $amount)
	{
	    if ($amount == 10) {
			//add card
        	//Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 86241, 2);
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 119432, 1);
			
			$toSend = '5星建筑吸血鬼男爵';
        } else if ($amount == 20) {
            //add card
			//Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 86241, 5);
			//add plant
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 119532, 1);

            $toSend = '5星建筑吸血鬼夫人';
        } else if ($amount == 50) {
			//add card
			//Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 86241, 15);
			//add plant
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 119432, 1);
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 119532, 1);

            $toSend = '5星建筑吸血鬼男爵、5星建筑吸血鬼夫人';
        } else if ($amount == 100) {
			//add card
			//Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 86241, 40);
			//add plant
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 119432, 1);
			Hapyfish2_Island_Bll_GiftPackage::addGift($uid, 119532, 1);
			//add coin
            //Hapyfish2_Island_HFC_User::incUserCoin($uid, 100000);
            $toSend = '5星建筑吸血鬼男爵、5星建筑吸血鬼夫人';
        }
        
		$title = '恭喜你获得了充值送物品<font color="#FF0000">' . $toSend . '</font>';

		$minifeed = array('uid' => $uid,
						'template_id' => 0,
						'actor' => $uid,
						'target' => $uid,
						'title' => array('title' => $title),
						'type' => 3,
						'create_time' => time());

		Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
        
	    return true;
	}
}