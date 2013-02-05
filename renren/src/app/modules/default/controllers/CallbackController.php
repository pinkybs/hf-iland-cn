<?php

/**
 * application callback controller
 *
 * @copyright  Copyright (c) 2009 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/08/07    HLJ
 */
class CallbackController extends Zend_Controller_Action
{
    private $_xn_params;

    private $_renren;

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'callback';
    	exit;
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

    private function get_valid_xn_params($params, $timeout = null, $namespace = 'xn_sig')
    {
        if (empty($params)) {
            return array();
        }

        $prefix = $namespace . '_';
        $prefix_len = strlen($prefix);
        $xn_params = array();

        foreach ($params as $name => $val) {
            if (strpos($name, $prefix) === 0) {
                $xn_params[substr($name, $prefix_len)] = $val;
            }
        }

        // validate that the request hasn't expired. this is most likely
        // for params that come from $_COOKIE
        if ($timeout && (!isset($xn_params['time']) || time() - $xn_params['time'] > $timeout)) {
            return array();
        }

        // validate that the params match the signature
        $signature = isset($params[$namespace]) ? $params[$namespace] : null;

        if (!$signature || (!$this->_renren->verifySignature($xn_params, $signature))) {
            return array();
        }

        return $xn_params;
    }

    private function validate_xn_params()
    {
        //$this->_xn_params = $this->get_valid_xn_params($_POST, 48*3600, 'xn_sig');
        $info = $this->vailid();

        if (!$info) {
			return false;
        }

        $this->_xn_params = array(
        	'user' => $info['uid'],
        	'session_key' => $info['session_key']
        );

        return true;
    }

    public function payAction()
    {
 //debug_log(json_encode($_POST));
        $app_id = APP_ID;
        $this->_renren = Xiaonei_Renren::getInstance();

        if (!$this->_renren) {
            debug_log('app id error');
            exit;
        }

        if (!$this->validate_xn_params()) {
            debug_log('signature error');
            exit;
        }

        $amount = (int)$_POST['amount'];
        $uid = $this->_xn_params['user'];
        $session_key = $this->_xn_params['session_key'];

        if ($amount > 0) {
            $order = Hapyfish2_Island_Bll_Payment::regOrder($app_id, $uid, $session_key, $amount);
            if ($order) {
                echo Zend_Json::encode($order);
                exit;
            }
        }

        exit;
    }

    public function ordercompletedAction()
    {
//info_log(json_encode($_POST), 'paycallback');
        $puid = $_POST['xn_sig_user'];
        $session_key = $_POST['xn_sig_session_key'];
        $order_id = $_POST['xn_sig_order_id'];
        $skey = $_POST['xn_sig_skey'];

        //xn_sig_password
        //xn_sig_order_number

        //debug_log('ordercompleted: ' . json_encode($_POST));

        if (empty($puid) || empty($session_key) || empty($order_id) || empty($skey)) {
            exit;
        }


        $validskey = md5('1234!@#$' . $puid);
        if ($validskey != $skey) {
            debug_log($puid. ':' . $order_id . ':' . $skey);
            exit;
        }

        $renren = Xiaonei_Renren::getInstance();

        if ($renren) {
            $renren->setUser($puid, $session_key);
            $completed = $renren->isOrderCompleted($order_id);

            if ($completed) {
                $ok = true;
                $rowUser = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
                $order = Hapyfish2_Island_Bll_Payment::getOrder($rowUser['uid'], $order_id);
                if ($order && $order['status'] == 0) {
                    $ok = Hapyfish2_Island_Bll_Payment::completeOrder($rowUser['uid'], $order);
                }

                if ($ok == 0) {
                	$amount = $order['amount'];
                    $result = array('app_res_user' => $puid, 'app_res_order_id' => $order_id, 'app_res_amount' => $amount);

                    //file log
    	            $log = Hapyfish2_Util_Log::getInstance();
                    $log->report('paydone', array($order_id, $amount, $rowUser['token'], $rowUser['uid']));

                    echo json_encode($result);
                }
            }
        }

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