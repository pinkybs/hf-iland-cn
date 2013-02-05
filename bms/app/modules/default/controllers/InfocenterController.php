<?php

class InfocenterController extends Zend_Controller_Action
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
    	$this->view->super = $super;
    	$this->render();
    }
    
    public function mainAction()
    {
    	$this->render();
    }
	
    public function treeAction()
    {
    	$cuid = $this->info['uid'];
    	$accessList = Hapyfish2_Bms_Bll_Access::getAccessList($cuid);
    	$this->view->accessList = $accessList;
    	
    	$moveDataAccessList = array(1020, 1026, 1030);
    	
    	if (in_array($cuid, $moveDataAccessList)) {
    		$this->view->movedataaccess = 1;
    	} else {
    		$this->view->movedataaccess = 0;
    	}
    	
    	$this->render();
    }
}