<?php

class MaintenanceapiController extends Zend_Controller_Action
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
    
    public function indexAction()
    {
    	echo 'Customer Tools API V1.0';
    	exit;
    }
	
	public function getitemlistAction()
	{
		$type = $this->_request->getParam('type');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getItemList($type);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getnoticeAction()
	{
		$type = $this->_request->getParam('type', 2);
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getNotice($type);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function refreshnoticeAction()
	{
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->refresh('notice');
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

		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->updateNotice($info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function senditemAction()
	{
		$uid = $this->_request->getParam('uid');
		$ouid = $this->cuid;
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}
		$params = $this->_request->getParams();
		$info = array();
		$info['uid'] = $uid;
		if (isset($params['gold'])) {
			$info['gold'] = $params['gold'];
		}
		if (isset($params['coin'])) {
			$info['coin'] = $params['coin'];
		}
		if (isset($params['love'])) {
			$info['love'] = $params['love'];
		}
		if (isset($params['starfish'])) {
			$info['starfish'] = $params['starfish'];
		}
		if (isset($params['item'])) {
			$info['item'] = $params['item'];
		}
		if (isset($params['feed'])) {
			$info['feed'] = $params['feed'];
		}
		if (isset($params['sendfeed'])) {
			$info['sendfeed'] = $params['sendfeed'];
		}
		
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->sendItem($info);
			if($result['num'] > 0){
				Hapyfish2_Bms_Bll_Log::operation($ouid, $this->platform, '发送物品信息：' . json_encode($info));
			}
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function sendfeedAction()
	{
		$uid = $this->_request->getParam('uid');
		$cuid = $this->cuid;
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}
		$params = $this->_request->getParams();
		$info = array();
		$info['uid'] = $uid;
		if (isset($params['feed'])) {
			$info['feed'] = $params['feed'];
		}

		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->sendFeed($info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getpaysettingAction()
	{
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getPaysetting();
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function updatepaysettingAction()
	{
		$id = $this->_request->getParam('id');
		if ($id == null) {
			$this->echoError(2001, 'id can not empty');
		}
		
		$params = $this->_request->getParams();
		$info = array('id' => $id);
		if (isset($params['section'])) {
			$info['section'] = $params['section'];
		}
		if (isset($params['end_time'])) {
			$info['end_time'] = $params['end_time'];
		}
		if (isset($params['note'])) {
			$info['note'] = $params['note'];
		}
		if (isset($params['next_id'])) {
			$info['next_id'] = $params['next_id'];
		}

		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->updatePaysetting($info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
}