<?php

/**
 * application callback controller
 *
 * @copyright  Copyright (c) 2009 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/08/07    HLJ
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
    	exit;
    }

    //new pay method ****************
	public function paydoneAction()
    {
    	//{"sign":"223c95a5d1c7539806e0f866dcb37656","status":"1","message":null,"order_id":"2283436","sign_time":"Wed Jan 05 13:43:23 CST 2011","outer_order_id":"5432","proxy_code":"HAPPYFISH"}
    	$sig = $_GET['sign'];
    	$status = $_GET['status'];
    	$trade_no = $_GET['order_id'];
    	$out_trade_no = $_GET['outer_order_id'];
    	$callbackurl = HOST . '/callback/paydone?';
    	if ($sig != md5($callbackurl.$out_trade_no) || empty($status)) {
    		echo 'validate failed';
    		exit();
    	}

    	try {
            info_log(json_encode($_GET), 'newPayApi_syn_'.date('Ymd'));
    	}
        catch (Exception $e) {
            err_log('callback:paydone:infolog:err:'.$e->getMessage());
        }

        $aryInfo = explode('_', $out_trade_no);
        $puid = $aryInfo[2];
        $ok = true;
        $rowUser = Hapyfish2_Platform_Bll_UidMap::getUser($puid);
        $order = Hapyfish2_Island_Bll_Payment::getOrder($rowUser['uid'], $out_trade_no);
        if ($order && $order['status'] == 0) {
            $ok = Hapyfish2_Island_Bll_Payment::completeOrder($rowUser['uid'], $order);
            if ($ok == 0) {
            	$amount = $order['amount'];
                //file log
                $log = Hapyfish2_Util_Log::getInstance();
                $log->report('tb2paydone', array($out_trade_no, $amount, $trade_no, $rowUser['uid'], 2));
            }
        }

        $this->_redirect('/pay/payfinish');
        exit;
    }

    //new pay method notify repeat ****************
	public function paydonenotifyAction()
    {
    	//{"sign":"223c95a5d1c7539806e0f866dcb37656","status":"1","message":null,"order_id":"2283436","sign_time":"Wed Jan 05 13:43:23 CST 2011","outer_order_id":"5432","proxy_code":"HAPPYFISH"}
    	$sig = $_POST['sign'];
    	$status = $_POST['status'];
    	$trade_no = $_POST['order_id'];
    	$out_trade_no = $_POST['outer_order_id'];
    	$callbackurl = HOST . '/callback/paydonenotify';
    	if ($sig != md5($callbackurl.$out_trade_no) || empty($status)) {
    		echo 'validate failed';
    		exit();
    	}

		try {
            info_log(Zend_Json::encode($_POST), 'newPayApi_asyn_'.date('Ymd'));
    	}
        catch (Exception $e) {
            err_log('callback:paydone:infolog:err:'.$e->getMessage());
        }
        

        $aryInfo = explode('_', $out_trade_no);
        $puid = $aryInfo[2];
        $ok = true;
        $rowUser = Hapyfish2_Platform_Bll_UidMap::getUser($puid);
        $order = Hapyfish2_Island_Bll_Payment::getOrder($rowUser['uid'], $out_trade_no);
        if ($order && $order['status'] == 0) {
            $ok = Hapyfish2_Island_Bll_Payment::completeOrder($rowUser['uid'], $order);
            if ($ok == 0) {
            	$amount = $order['amount'];
                //file log
                $log = Hapyfish2_Util_Log::getInstance();
                $log->report('tb2paydone', array($out_trade_no, $amount, $trade_no, $rowUser['uid'], 3));
            }
        }

        $this->_redirect('/pay/payfinish');
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
    
    public function gettry8dataAction()
    {
    	$link = 'http://itry.try8.info/taobao/direct/ri.php?action=ajax_kldz';
    	$data = file_get_contents($link);
    	Hapyfish2_Island_Event_Cache_Christmas::updateZhuanbao($data);
    	echo $data;
    	exit;
    }

}