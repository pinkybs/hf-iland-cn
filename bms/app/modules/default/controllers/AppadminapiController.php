<?php

class AppadminapiController extends Zend_Controller_Action
{
    protected $cuid;
    
    protected $info;
	
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }
        
        $this->info = $info;
        $this->cuid = $info['uid'];
        $this->platform = $this->_request->getParam('platform');
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
	
	public function getappinfoAction()
	{
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getAppInfo();
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function updateappinfoAction()
	{		
		$params = $this->_request->getParams();
		$info = array();
		if (isset($params['app_id'])) {
			$info['app_id'] = $params['app_id'];
		}
		if (isset($params['app_name'])) {
			$info['app_name'] = $params['app_name'];
		}
		if (isset($params['app_title'])) {
			$info['app_title'] = $params['app_title'];
		}
		if (isset($params['app_link'])) {
			$info['app_link'] = $params['app_link'];
		}
		if (isset($params['app_host'])) {
			$info['app_host'] = $params['app_host'];
		}
		if (isset($params['app_status'])) {
			$info['app_status'] = $params['app_status'];
		}
		if (isset($params['maintance_notice'])) {
			$info['maintance_notice'] = $params['maintance_notice'];
		}
		if (isset($params['white_ip_list'])) {
			$info['white_ip_list'] = $params['white_ip_list'];
		}
		if (isset($params['black_ip_list'])) {
			$info['black_ip_list'] = $params['black_ip_list'];
		}
		if (isset($params['dev_id_list'])) {
			$info['dev_id_list'] = $params['dev_id_list'];
		}
		if (isset($params['test_id_list'])) {
			$info['test_id_list'] = $params['test_id_list'];
		}
		if (isset($params['external_api_key'])) {
			$info['external_api_key'] = $params['external_api_key'];
		}
		if (isset($params['external_api_secret'])) {
			$info['external_api_secret'] = $params['external_api_secret'];
		}
		if (isset($params['external_open'])) {
			$info['external_open'] = $params['external_open'];
		}
		
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->updateAppInfo($info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
}