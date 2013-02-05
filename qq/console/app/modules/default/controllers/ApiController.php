<?php

class ApiController extends Zend_Controller_Action
{
    protected $cuid;
    
    protected $info;
    
	function vailid()
	{
    	$skey = $_COOKIE['hf_customer_qq_skey'];
    	if (!$skey) {
    		return false;
    	}
    	
    	$tmp = split('_', $skey);
    	if (empty($tmp) || count($tmp) != 3) {
    		return false;
    	}
    	
        $uid = $tmp[0];
        $t = $tmp[1];
        $sig = $tmp[2];
        
        $vsig = md5($uid . $t . APP_SIGN_SECRET);
        if ($sig != $vsig) {
        	return false;
        }
        
        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }
        
        return array('uid' => $uid, 't' => $t);
	}
	
    function init()
    {
        $info = $this->vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }
        
        $this->info = $info;
        $this->cuid = $info['uid'];
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }
    
    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }
    
    public function indexAction()
    {
    	echo 'Customer Tools API V1.0';
    	exit;
    }
	
	public function getuserinfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getUserInfo($uid);
			$result['watch'] = HOST . '/api/watchuser?uid=' . $uid;
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getusercardinfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getUserCardInfo($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getcoinlogAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getCoinLog($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getinvitelogAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getInviteLog($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function watchuserAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getWatchUser($uid);
			if ($result['url']) {
				$this->_redirect($result['url']);
			} else {
				$this->echoError(1, 'system error');
			}
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getnoticeAction()
	{
		$type = $this->_request->getParam('type', 2);
		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->setUser($this->cuid);
		$rest->server_addr = 'http://api.island.qzoneapp.com';
		try {
			$result = $rest->getNotice($type);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function updatenoticeAction()
	{
		$id = $this->_request->getParam('id');
		if (empty($id)) {
			$this->echoError(1001, 'id can not empty');
		}
		
		$params = $this->_request->getParams();
		$info = array('id' => $id);
		if (isset($params['position'])) {
			$info['position'] = $params['position'];
		}
		if (isset($params['title'])) {
			$info['title'] = $params['title'];
		}
		if (isset($params['link'])) {
			$info['link'] = $params['link'];
		}
		if (isset($params['priority'])) {
			$info['priority'] = $params['priority'];
		}
		if (isset($params['opened'])) {
			$info['opened'] = $params['opened'];
		}
		if (isset($params['time'])) {
			$info['create_time'] = $params['time'];
		}

		$rest = new Hapyfish_Rest_Island(APP_REMOTE_ID, APP_REMOTE_KEY);
		$rest->server_addr = 'http://api.island.qzoneapp.com';
		$rest->setUser($this->cuid);
		try {
			$result = $rest->updateNotice($info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
}