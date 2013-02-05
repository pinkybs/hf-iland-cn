<?php

class ChartapiController extends Zend_Controller_Action
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
    	$this->platform = $this->_request->getParam('platform');
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    protected function echoResult($data)
    {
    	echo $data;
    	exit;
    }
    
    public function mainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
			$info = Hapyfish2_Island_Bll_Day::getMainRange($platform, $begin, $end, false);
			$data = Hapyfish2_Island_Bll_Chart::createMainContent($info);
			$this->echoResult($data);
		} catch (Exception $e) {
			$this->echoResult('');
		}
    }
	
}