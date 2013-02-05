<?php

class AccountapiController extends Zend_Controller_Action
{	
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
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
    
    public function getaccountAction()
    {
		$uid = $this->cuid;
    	$account = Hapyfish2_Bms_Bll_Auth::getAccount($uid);
		$result['data'] = $account;
		$this->echoResult($result);
    }
    
    public function updateaccountAction()
    {
    	$uid = $this->cuid;
    	$pwd = $this->_request->getParam('pwd', '');
    	if (empty($pwd) || $pwd == '******') {
    		$this->echoResult(array('data' => false));
    	}
    	
    	$info = array('pwd' => $pwd);
    	$ok = Hapyfish2_Bms_Bll_Auth::updateAccount($uid, $info);
    	$result['data'] = $ok;
    	$this->echoResult($result);
    }
    
    public function getloginlogAction()
    {
    	$uid = $this->cuid;
    	$logs = Hapyfish2_Bms_Bll_Log::getLogin($uid);
    	$result['data'] = $logs;
    	$this->echoResult($result);
    }
    
    public function getoperationlogAction()
    {
    	$uid = $this->cuid;
    	$logs = Hapyfish2_Bms_Bll_Log::getOperation($uid);
    	$result['data'] = $logs;
    	$this->echoResult($result);
    }
	
}