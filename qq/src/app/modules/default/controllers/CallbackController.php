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


    public function payforqpointAction()
    {
        //info_log($_SERVER['SERVER_PORT'], 'qpointcallback');
        //echo 'this is callback';
        if ('9001' != $_SERVER['SERVER_PORT']) {
            exit;
        }
        //info_log(json_encode($_REQUEST), 'qpointcallback');

        $openid = $this->_request->getParam('openid');
        $appid = $this->_request->getParam('appid');
        $ts = $this->_request->getParam('ts');
        $payitem = $this->_request->getParam('payitem');
        $amt = $this->_request->getParam('amt');
        $token = $this->_request->getParam('token');
        $billno = $this->_request->getParam('billno');
        $sig = $this->_request->getParam('sig');
        $payamt_coins = $this->_request->getParam('payamt_coins');
        $pubacct_payamt_coins = $this->_request->getParam('pubacct_payamt_coins');

        if ($appid != APP_ID) {
            $rst = array('ret'=>4, 'msg'=>'请求参数错误：appid');
            echo json_encode($rst);
    	    exit;
        }

        //check sig
        $params = array();
        $params['appid'] = $appid;
        $params['openid'] = $openid;
        $params['payitem'] = $payitem;
        $params['amt'] = $amt;
        $params['token'] = $token;
        $params['billno'] = $billno;
        $params['ts'] = $ts;
        ksort($params);
        //generate sig
        $strParam = '';
        foreach ($params as $key=>$val) {
            $strParam .= $key . $val;
        }

        if (strtoupper($sig) != strtoupper(md5($strParam.APP_KEY))) {
            $rst = array('ret'=>4, 'msg'=>'请求参数错误：sig');
            echo json_encode($rst);
    	    exit;
        }

        $rowUser = Hapyfish2_Platform_Bll_UidMap::getUser($openid);
        if (empty($rowUser)) {
            $rst = array('ret'=>4, 'msg'=>'请求参数错误：openid');
            echo json_encode($rst);
    	    exit;
        }

        $uid = $rowUser['uid'];
        $info = array();
        $info['uid'] = $uid;
        $info['token'] = $token;
        $info['bill_no'] = $billno;
        $info['tot_amount'] = $amt/10;
        $info['payamt_coins'] = (int)$payamt_coins;
        $info['pubacct_payamt_coins'] = (int)$pubacct_payamt_coins;
        $info['payitem'] = $payitem;
        $info['create_time'] = $ts;
        $result = Hapyfish2_Island_Bll_QpointBuy::completeBuy($uid, $info);

        if ($result == 0) {
            $rst = array('ret'=>0, 'msg'=>'OK');
        }
        else {
            if ($result == 2) {
                $rst = array('ret'=>2, 'msg'=>'token已过期');
            }
            else if ($result == 3) {
                $rst = array('ret'=>3, 'msg'=>'token不存在');
            }
            else {
                $rst = array('ret'=>1, 'msg'=>'系统繁忙');
            }
        }
        echo json_encode($rst);
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