<?php

class SysController extends Zend_Controller_Action
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
        $this->view->ts = $info['t'];
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }
    
    public function indexAction()
    {
    	$cuid = $this->info['uid'];
    	$super = Hapyfish2_Bms_Bll_Auth::isSuper($cuid);
    	if (!$super) {
    		$this->_redirect('/');
    		exit;
    	}

    	$this->render();
    }

}