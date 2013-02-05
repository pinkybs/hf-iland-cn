<?php

class Hapyfish2_Island_Bll_Gold
{
	public static function get($uid, $needVip = false)
	{
		$context = Hapyfish2_Util_Context::getDefaultInstance();
        $openid = $context->get('openid');
        $openkey = $context->get('openkey');

		//$qzone = Qzone_Rest::getInstance();
		//$qzone = Qzone_Factory::getRest();
		$qzone = Qzone_RestQzone::getInstance();
        $qzone->setUser($openid, $openkey);

        return $qzone->getPayBalance($needVip);
	}

	public static function addPayOrderFlow($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Island_Dal_ConsumeLog::getDefaultInstance();
			$dalLog->insertPayOrderFlow($uid, $info);
		} catch (Exception $e) {
			$msg = json_encode($info);
			info_log($msg, 'addPayOrderFlow-error');
			info_log($e->getMessage(), 'addPayOrderFlow-error-debug');
		}
	}

	public static function addGoldLog($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Island_Dal_ConsumeLog::getDefaultInstance();
			$dalLog->insertGold($uid, $info);
		} catch (Exception $e) {
			$msg = json_encode($info);
			info_log($msg, 'addGoldLog-error');
			info_log($e->getMessage(), 'addGoldLog-error-debug');
		}
	}

	//预付
	public static function consume($uid, &$payInfo)
	{
		$context = Hapyfish2_Util_Context::getDefaultInstance();
        $openid = $context->get('openid');
        $openkey = $context->get('openkey');
        $items = array($payInfo['item_id'] => $payInfo['item_num']);
        $time = time();

		//$qzone = Qzone_Rest::getInstance();
		$qzone = Qzone_Factory::getRest();
        $qzone->setUser($openid, $openkey);

        $billno = $qzone->pay($items, $payInfo['amount']);
        $isErr = $qzone->isErr();
        $code = $qzone->getCode();
        $payInfo['time'] = $time;
        $payInfo['openid'] = $openid;
        $payInfo['cmd'] = 6;
        $payInfo['result'] = $code;
        $payInfo['platform'] = (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) ? 1 : 2;
        if ($isErr) {
        	$payInfo['bill_no'] = '';
        	self::addPayOrderFlow($uid, $payInfo);
        	return false;
        } else {
        	$payInfo['bill_no'] = $billno;
        	self::addPayOrderFlow($uid, $payInfo);
        	return true;
        }
	}

	//确认
	public static function consumeComfirm($uid, &$payInfo, $goldInfo)
	{
		$context = Hapyfish2_Util_Context::getDefaultInstance();
        $openid = $context->get('openid');
        $openkey = $context->get('openkey');
        $time = time();

		//$qzone = Qzone_Rest::getInstance();
		$qzone = Qzone_Factory::getRest();
        $qzone->setUser($openid, $openkey);

        $billno = $qzone->payConfirm($payInfo['bill_no'], $payInfo['amount']);
        $code = $qzone->getCode();
        $payInfo['time'] = $time;
        $payInfo['cmd'] = 7;
        $payInfo['result'] = $code;

        self::addPayOrderFlow($uid, $payInfo);

        $goldInfo['create_time'] = $time;
        self::addGoldLog($uid, $goldInfo);

        return true;
	}

	//冲正，回退
	public static function consumeCancel($uid, &$payInfo)
	{
		$context = Hapyfish2_Util_Context::getDefaultInstance();
        $openid = $context->get('openid');
        $openkey = $context->get('openkey');
        $time = time();

		//$qzone = Qzone_Rest::getInstance();
		$qzone = Qzone_Factory::getRest();
        $qzone->setUser($openid, $openkey);

        $billno = $qzone->payCancel($payInfo['bill_no'], $payInfo['amount']);
        $code = $qzone->getCode();
        $payInfo['time'] = $time;
        $payInfo['cmd'] = 8;
        $payInfo['result'] = $code;

        self::addPayOrderFlow($uid, $payInfo);

        return true;
	}
}