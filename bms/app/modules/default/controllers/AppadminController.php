<?php

class AppadminController extends Zend_Controller_Action
{
    public $info;
	function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }
        
        $this->info = $info;
        $this->view->cuid = $info['uid'];
        $this->platform = $this->_request->getParam('platform');
        $this->view->platform = $this->platform;
        $this->view->ts = $info['t'];
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }
    
    public function changestatusAction()
    {
    	$this->render();
    }

}