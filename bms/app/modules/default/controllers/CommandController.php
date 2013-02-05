<?php

class CommandController extends Zend_Controller_Action
{
	function init()
    {
    	$this->cuid = '1';
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
	
	public function updateAction()
	{
		$isTest = $this->_request->getParam('test', '1');
		if ($isTest == '1') {
			Hapyfish2_Island_Bll_Command::updatePHPSource4Test($this->platform, $this->cuid);
		}
		exit;
	}
	
}