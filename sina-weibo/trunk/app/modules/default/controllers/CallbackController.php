<?php

/**
 * callback method for action callback
 * @author liju.hu@hapyfish.com
 */
class CallbackController extends Zend_Controller_Action
{

	/**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'callback';
    	/*echo '<br/>';
    	echo md5('1107001000001951'.'|'.'401380203'.'|'.'2138401295'.'|'.'100'.'|'.APP_SECRET);
    	echo md5('1107001000001951'.'|'.'401380203'.'|'.'2138401295'.'|'.'100'.'|'.'hf193e0c');
    	echo '<br/>54b27fa23edbb8bbb3a3c7e89e841700';*/
    	exit;
    }

	//pay method
	public function paydoneAction()
    {
    	//支付安全密码  hf193e0c

    	$orderId = $_POST['order_id'];
    	$appkey = $_POST['appkey'];
    	$puid = $_POST['order_uid'];
    	$amount = $_POST['amount'];
    	$sign = $_POST['sign'];

    	header("HTTP/1.0 401 Invalid");
    	/*if ($sig != md5($orderId.'|'.$appkey.'|'.$puid.'|'.$amount.'|'.APP_SECRET)) {
    		echo 'validate failed';
    		exit;
    	}*/

    	$rowUser = Hapyfish2_Platform_Bll_UidMap::getUser($puid);
    	if (empty($rowUser)) {
    	    echo 'user not exist';
    	    exit;
    	}

		//get status from platform api
		$rest = SinaWeibo_Client::getInstance();
        //$rest->setUser($this->info['session_key']);
        $sign = md5($orderId .'|'. APP_SECRET);

        $payStatus = $rest->getPayStatus($orderId, $puid, APP_KEY, $sign);
        if (empty($payStatus) || $payStatus['order_status']!=1) {
            echo 'not finished';
    	    exit;
        }

        info_log(Zend_Json::encode($_POST), 'payment_cb');
        $ok = Hapyfish2_Island_Bll_Payment::completeOrder($rowUser['uid'], $orderId);
        if ($ok == 0 || $ok == 3) {
            if ($ok == 0) {
                 //file log
                $log = Hapyfish2_Util_Log::getInstance();
                $log->report('wbpaydone', array($rowUser['uid'], $orderId, $amount, $puid));
            }
            header("HTTP/1.0 200 OK");
            echo 'OK';
    		exit;
        }

        echo 'failed';
        exit;
    }

    /**
     * magic function
     *   if call the function is undefined,then echo undefined
     *
     * @param string $methodName
     * @param array $args
     * @return void
     */
    function __call($methodName, $args)
    {
        echo 'undefined method name: ' . $methodName;
        exit;
    }
}