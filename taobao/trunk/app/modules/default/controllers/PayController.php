<?php

class PayController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    protected $_aryPay = array(array('id' => 11, 'name' => '10个宝石', 'price' => 100, 'gold' => 10),
    						   array('id' => 1, 'name' => '100个宝石', 'price' => 1000, 'gold' => 105),
                         	   array('id' => 2, 'name' => '200个宝石', 'price' => 2000, 'gold' => 210),
                         	   array('id' => 3, 'name' => '500个宝石', 'price' => 5000, 'gold' => 580),
                         	   array('id' => 4, 'name' => '1000个宝石', 'price' => 10000, 'gold' => 1200),
                         	   array('id' => 5, 'name' => '5000个宝石', 'price' => 50000, 'gold' => 5800));

	//加码
    protected $_aryPayDay = array(array('id' => 11, 'name' => '10个宝石', 'price' => 100, 'gold' => 10),
		    						   array('id' => 1, 'name' => '100个宝石', 'price' => 1000, 'gold' => 120),
		                         	   array('id' => 2, 'name' => '200个宝石', 'price' => 2000, 'gold' => 260),
		                         	   array('id' => 3, 'name' => '500个宝石', 'price' => 5000, 'gold' => 700),
		                         	   array('id' => 4, 'name' => '1000个宝石', 'price' => 10000, 'gold' => 1500));

    public function init()
    {
    	$info = $this->vailid();
        if (!$info) {
            echo '<html><body><script type="text/javascript">window.top.location="http://yingyong.taobao.com/show.htm?app_id=73015";</script></body></html>';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->uid = $info['uid'];
        $this->view->platformUid = $info['puid'];
    }

	protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	if (!$skey) {
    		return false;
    	}

    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $puid = $tmp[1];
        $session_key = base64_decode($tmp[2]);
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $puid . $session_key . $t . APP_KEY);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_KEY);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
    }

	public function topAction()
	{
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);

		$nowTime = time();
		$valDayEnd = strtotime('2012-02-19 23:59:59');

		if ($nowTime < $valDayEnd) {
			$changestatus = 1;
		} else {
			$changestatus = 2;
		}

		$this->view->nowtime = $nowTime;
		$this->view->changestatus = $changestatus;
		$this->view->user = $user;
		$this->render();
	}

    public function logAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);

    	$logs = Hapyfish2_Island_Bll_PaymentLog::getPayment($uid, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->user = $user;
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }

    //go to taobao payment page
    public function gopayAction()
    {
    	$uid = $this->uid;
        $buy_type = $_GET;
        $buy_type = array_keys($buy_type);

        //$buy_type
        //["callback\/apipay","btnOrder1","uid"]
        $buy_type = str_replace('btnOrder', '', $buy_type[1]);

        //check buy_type
        $price = 0;

		//加码
		$nowTime = time();
		$changeStartTime = strtotime('2012-01-18 00:00:01');
		$changeEndTime = strtotime('2012-01-26 23:59:59');

		if (($nowTime >= $changeStartTime) && ($nowTime <= $changeEndTime)) {
			$changestatus = 1;
		} else {
			$changestatus = 2;
		}

        //以分为单位
		if ($changestatus == 1) {
			$payment = $this->_aryPayDay;
		} else {
			$payment = $this->_aryPay;
		}

        $gold = 0;
        $itemName = '';
        foreach ($payment as $item) {
            if ($buy_type == $item['id']) {
                $price = $item['price'];
                $gold = $item['gold'];
                $itemName = $item['name'];
                break;
            }
        }

        if ($price <= 0 || empty($buy_type) || empty($itemName)) {
            exit;
        }

        $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
        $orderId = Hapyfish2_Island_Bll_Payment::createPayOrderId($rowUser['puid']);
        $buyer_time = time();
        //call api
        $params = array();
        $params['format'] = 'json';
        $params['item_id'] = $buy_type;
        $params['item_version_id'] = 2;
        $params['total_price'] = $price;
   	 	/*if ($uid == 10650884) {
        	$params['total_price'] = 1;
        }*/
        $params['item_name'] = $itemName;
        $params['item_version_name'] = '宝石';
        $params['page_ret_url'] = HOST . '/callback/paydone?';
        $params['proxy_code'] = 'HAPPYFISH';
        $params['outer_order_id'] = $orderId;
        $params['buyer_time'] = $buyer_time;
        $params['description'] = '';
        $params['alipay_id'] = '374052723';//'2088302111803535';
        $rest = Taobao_Rest::getInstance();
        $rest->setUser($puid, $this->info['session_key']);
        $data = $rest->jianghu_getVasIsvUrl($params);
        if (empty($data) || !is_array($data) || !isset($data['vas_isv_url_get_response']['vas_isv_url'])) {
            info_log('101' ,'tb2payfailed');
        	echo '<html><body>request timeout,please try again later.</body></html>';
            exit;
        }
  		$dataUrl = $data['vas_isv_url_get_response']['vas_isv_url'];
        try {
	        if (isset($dataUrl['aplipay_isv_address'])) {
		        //create pay order
		        $amount = (int)($price/100);
		        $tradeNo = $dataUrl['order_id'];
		        $rst = Hapyfish2_Island_Bll_Payment::createOrder($orderId, $uid, $amount, $gold, $tradeNo, $buyer_time);
                if ($rst) {
                    return $this->_redirect($dataUrl['aplipay_isv_address']);
                }
                info_log('102' ,'tb2payfailed');
		        $msg = isset($dataUrl['message']) ? $dataUrl['message'] : '支付失败';
        		echo "<html><body>$msg</body></html>";
	            exit;
		    }
	        else {
	        	if (1 == $dataUrl['status']) {
    	        	//create pay order
    		        $amount = (int)($price/100);
    		        $tradeNo = $dataUrl['order_id'];
    		        $rst = Hapyfish2_Island_Bll_Payment::createOrder($orderId, $uid, $amount, $gold, $tradeNo, $buyer_time);
                    if ($rst) {
                        $order = Hapyfish2_Island_Bll_Payment::getOrder($uid, $orderId);
                        if ($order['status'] == 1) {
                            return $this->_redirect('/pay/payfinish');
                        }
                        $payRst = Hapyfish2_Island_Bll_Payment::completeOrder($uid, $order);
                        if ($payRst == 0) {
                            $log = Hapyfish2_Util_Log::getInstance();
		                    $log->report('tb2paydone', array($orderId, $amount, $tradeNo, $uid, 1));
                            return $this->_redirect('/pay/payfinish');
                        }
                        else {
                            info_log('103' ,'tb2payfailed');
                        }
                    }
                    else {
                        info_log('102' ,'tb2payfailed');
                    }
			        //$msg = $dataUrl['message'];
			        $msg = isset($dataUrl['message']) ? $dataUrl['message'] : '支付失败';
	        		echo "<html><body>$msg</body></html>";
		            exit;
	        	}
	        	else {
	        	    //info_log('104:'.json_encode($dataUrl) ,'tb2payfailed');
	        	    info_log('104:'.json_encode($data) ,'tb2payfailed');
	        		$msg = isset($dataUrl['message']) ? $dataUrl['message'] : '支付失败';
	        		return $this->_redirect('http://pay.taobao.com/account/pay_for_account.htm');
	        		//echo "<html><body>$msg</body></html>";
		            exit;
	        	}
	        }
        } catch (Exception $e) {
            echo '-100';
            exit;
        }

        info_log('105' ,'tb2payfailed');
		echo "<html><body>Please retry.</body></html>";
		exit;
    }

    public function listorderAction()
    {
        $uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);

        $dalPay = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
        $lstPay = $dalPay->listOrder($uid, 1, 20);
		if ($lstPay) {
			$count = count($lstPay);
		} else {
			$count = 0;
		}
		$this->view->logs = $lstPay;
        $this->view->count = $count;
        $this->view->user = $user;
        $this->render();
    }

    public function getorderAction()
    {
    	$uid = $this->uid;
    	$orderid = $this->_request->getParam('id');
    	$rowPay = Hapyfish2_Island_Bll_Payment::getOrder($uid, $orderid);
    	if (empty($rowPay) || $rowPay['uid'] != $uid) {
    		echo '<html><body>Failed!<br/><a href="/pay/listorder">back</a></body></html>';
    		exit;
    	}
    	$rest = Taobao_Rest::getInstance();
        $rest->setUser($uid, $this->info['session_key']);
        $data = $rest->jianghu_getVasIsvInfo($rowPay['orderid'], 'HAPPYFISH', $rowPay['order_time']);
    	if (empty($data) || !is_array($data) || !isset($data['vas_isv_info'])) {
    		echo Zend_Json::encode($data);
        	echo '<html><body>Failed!<br/><a href="/pay/listorder">back</a></body></html>';
    		exit;
        }

        if (1 == $data['vas_isv_info']['status']) {
        	if (0 == $rowPay['status']) {
        		//trade check success,insert into paylog
		        $ok = Hapyfish2_Island_Bll_Payment::completeOrder($uid, $rowPay);
        	}
        	$msg = "订单号：$orderid 支付已完成";
        }
        else {
        	$msg = "订单号：$orderid 支付未完成";
        }

    	echo '<html><body>'.$msg.'<br/><a href="/pay/listorder">back</a></body></html>';
    	exit;
    }

    public function payfinishAction()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->render();
    }
}