<?php

class PayController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    public function init()
    {
    	$info = $this->vailid();
        if (!$info) {
            echo '<html><body><script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script></body></html>';
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
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
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
	        $vsig = md5($uid . $puid . $session_key . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
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
		$changeTime = strtotime('2011-11-30 23:59:59');

		$this->view->nowtime = $nowTime;
		$this->view->changetime = $changeTime;
		
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


	public function orderAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		$result = array('status' => 0);
		if (empty($type) || $type<1 || $type>4) {
		    $result['msg'] = 'invalide';
		    echo json_encode($result);
			exit;
		}

		$rest = SinaWeibo_Client::getInstance();
		try {
            $rest->setUser($this->info['session_key']);
		} catch (Exception $e) {
            $result['msg'] = 'invalide';
		    echo json_encode($result);
			exit;
        }
		$time = time();
        if($time <= 1322668799){
        	$aryType = Hapyfish2_Island_Bll_Payment::$wbPayTypeBD;
        }else{
        	$aryType = Hapyfish2_Island_Bll_Payment::$wbPayType;
        }
        //create order id
		$orderId = Hapyfish2_Island_Bll_Payment::createOrderId($uid);
		if (empty($orderId)) {
		    $result['msg'] = 'get orderid failed';
		    echo json_encode($result);
			exit;
		}

		$amount = $aryType[$type]['amount'];
		$gold = $aryType[$type]['gold'];
		$desc = $aryType[$type]['name'];//urlencode($aryType[$type]['name']);
		$sign = md5($orderId.'|'.$amount.'|' . $desc . '|' . APP_SECRET);
		//get token from wb rest api
        $rowToken = $rest->getPayToken($orderId, $amount, $desc, $sign);

        if ($rowToken) {
            $token = $rowToken['token'];
            $puid = $rowToken['order_uid'];
            $rst = Hapyfish2_Island_Bll_Payment::createOrder($orderId, $uid, $amount, $gold, $token, $puid, time());
            if ($rst) {
                $info = array('token'=>$token, 'amount'=>$amount, 'order_id'=>$orderId, 'desc'=>$desc);
                $result['status'] = 1;
                $result['info'] = $info;
                echo json_encode($result);
			    exit;
            }
        }

        $result['msg'] = 'failed get token';
	    echo json_encode($result);
		exit;
	}

    public function orderstatusAction()
	{
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);

		$this->view->user = $user;
		$this->render();
	}

    public function searchorderAction()
	{
		$uid = $this->uid;
		$orderId = $this->_request->getParam('orderid');
		$result = array('status' => -1);
		if (empty($orderId) || strlen($orderId) != 16 || !is_numeric($orderId)) {
		    $result['msg'] = 'invalide';
		    echo json_encode($result);
			exit;
		}

		//is validate order id
		if (substr($orderId, 0, 7) != Hapyfish2_Island_Bll_Payment::$wbPrePayId) {
		    $result['msg'] = 'not valid order id';
		    echo json_encode($result);
			exit;
		}

		//is not self's order
		if (substr($uid, -1, 1) != substr($orderId, -2, 1)) {
            $result['msg'] = 'not your order id';
		    echo json_encode($result);
			exit;
		}

		//order not exist
		$rowOrder = Hapyfish2_Island_Bll_Payment::getOrder($uid, $orderId);
		if (empty($rowOrder)) {
            $result['msg'] = 'order id not found';
		    echo json_encode($result);
			exit;
		}

		//already done
		if (1 == $rowOrder['status']) {
		    $result['status'] = 1;
		    echo json_encode($result);
			exit;
		}

		$puid = $this->info['puid'];
		//get status from platform api
		$rest = SinaWeibo_Client::getInstance();
		try {
            $rest->setUser($this->info['session_key']);
		} catch (Exception $e) {
            $result['msg'] = 'session out';
		    echo json_encode($result);
			exit;
        }
        $sign = md5($orderId .'|'. APP_SECRET);

        $payStatus = $rest->getPayStatus($orderId, $puid, APP_KEY, $sign);
        if (empty($payStatus)) {
            $result['status'] = -2;
            $result['msg'] = 'api request failed';
		    echo json_encode($result);
			exit;
        }

        $key = 'paysrhlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
	    if (!$ok) {
            $result['msg'] = 'too fast';
		    echo json_encode($result);
			exit;
		}

        //order complete
        if (1 == $payStatus['order_status']) {
            $ok = Hapyfish2_Island_Bll_Payment::completeOrder($uid, $orderId);
            if ($ok == 0) {
                 //file log
                $log = Hapyfish2_Util_Log::getInstance();
                $log->report('wbpaydone_repair', array($uid, $orderId, $rowOrder['amount'], $puid));
            }
        }

        //release lock
        $lock->unlock($key);

        $result['status'] = $payStatus['order_status'];
        echo json_encode($result);
		exit;
	}

}