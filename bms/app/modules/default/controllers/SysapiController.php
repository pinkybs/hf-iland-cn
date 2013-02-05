<?php

class SysapiController extends Zend_Controller_Action
{	
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }
        
        $super = Hapyfish2_Bms_Bll_Auth::isSuper($info['uid']);
        if (!$super) {
        	$this->echoError(-1, 'error');
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
    
    public function getplatformAction()
    {
		$list = Hapyfish2_Bms_Bll_Platform::getList();
		$result['data'] = $list;
		$this->echoResult($result);
    }
    
    public function updateplatformAction()
    {
    	$pid = $this->_request->getParam('pid');
    	$title = $this->_request->getParam('title');
    	$desp = $this->_request->getParam('desp');
    	$index = $this->_request->getParam('index');
    	$info = array('title' => $title, 'desp' => $desp, 'index' => $index);
    	$ok = Hapyfish2_Bms_Bll_Platform::updatePlatform($pid, $info);
    	$result['data'] = $ok;
    	$this->echoResult($result);
    }
    
    public function newplatformAction()
    {
    	$pid = $this->_request->getParam('pid');
    	$name = $this->_request->getParam('name');
    	$title = $this->_request->getParam('title');
    	$desp = $this->_request->getParam('desp');
    	$index = $this->_request->getParam('index');
    	$info = array('pid' => $pid, 'name' => $name, 'title' => $title, 'desp' => $desp, 'index' => $index);
    	$ok = Hapyfish2_Bms_Bll_Platform::newPlatform($info);
    	$result['data'] = $ok;
    	$this->echoResult($result);
    }
    
    public function newaccountAction()
    {
    	$name = $this->_request->getParam('name');
    	$pwd = $this->_request->getParam('pwd');
    	$status = $this->_request->getParam('status');
    	$real_name = $this->_request->getParam('real_name');
    	$info = array('name' => $name, 'pwd' => $pwd, 'status' => $status, 'real_name' => $real_name);
    	$ret = Hapyfish2_Bms_Bll_Auth::addAccount($info);
    	$result['data'] = $ret;
    	$this->echoResult($result);
    }
    
    public function getaccountAction()
    {
		$list = Hapyfish2_Bms_Bll_Auth::getAccountList();
		$result['data'] = $list;
		$this->echoResult($result);
    }
    
    public function updateaccountAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$pwd = $this->_request->getParam('pwd', '');
    	$status = $this->_request->getParam('status');
    	$info = array('status' => $status);
    	if (!empty($pwd) && $pwd != '******') {
    		$info['pwd'] = $pwd;
    	}
    	
    	$ok = Hapyfish2_Bms_Bll_Auth::updateAccount($uid, $info);
    	$result['data'] = $ok;
    	$this->echoResult($result);
    }
    
    public function getaccessAction()
    {
		$uid = $this->_request->getParam('uid');
    	$accessList = Hapyfish2_Bms_Bll_Access::getAccessList($uid);
    	$platformList = Hapyfish2_Bms_Bll_Platform::getList();
		$result['access'] = $accessList;
		$result['platform'] = $platformList;
		$this->echoResult($result);
    }
    
    public function updateaccessAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$pid = $this->_request->getParam('pid');
    	$m1 = $this->_request->getParam('m1');
    	$m2 = $this->_request->getParam('m2');
    	$m3 = $this->_request->getParam('m3');
    	$m4 = $this->_request->getParam('m4');
    	
    	$info = array('m_1' => $m1, 'm_2' => $m2, 'm_3' => $m3, 'm_4' => $m4);
    	$ok = Hapyfish2_Bms_Bll_Access::updateAccess($uid, $pid, $info);
    	
    	$result['data'] = $ok;
    	$this->echoResult($result);
    }
    
    public function deleteaccessAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$pid = $this->_request->getParam('pid');
    	
    	$ok = Hapyfish2_Bms_Bll_Access::deleteAccess($uid, $pid);
    	
    	$result['data'] = $ok;
    	$this->echoResult($result);
    }
    
    public function newaccessAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$pid = $this->_request->getParam('pid');
    	$m1 = $this->_request->getParam('m1');
    	$m2 = $this->_request->getParam('m2');
    	$m3 = $this->_request->getParam('m3');
    	$m4 = $this->_request->getParam('m4');
    	
    	$info = array('uid' => $uid, 'pid' => $pid, 'm_1' => $m1, 'm_2' => $m2, 'm_3' => $m3, 'm_4' => $m4);
    	$ok = Hapyfish2_Bms_Bll_Access::addAccess($info);
    	if ($ok) {
    		$access = Hapyfish2_Bms_Bll_Access::getAccess($uid, $pid);
    	} else {
    		$access = null;
    	}
    	$platformList = Hapyfish2_Bms_Bll_Platform::getList();
    	$result['access'] = $access;
    	$result['platform'] = $platformList;
    	$this->echoResult($result);
    }
	
}