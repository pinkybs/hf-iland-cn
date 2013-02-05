<?php

class MaintenanceController extends Zend_Controller_Action
{	
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
        $this->platform = $this->_request->getParam('platform');
        $this->platfromInfo = Hapyfish2_Bms_Bll_Platform::getInfoByName($this->platform);
        $this->view->platform = $this->platform;
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }
    
    public function indexAction()
    {
    	$this->render();
    }
    
    public function noticeAction()
    {
        $cuid = $this->info['uid'];
    	$pid = $this->platfromInfo['pid'];
    	$access = Hapyfish2_Bms_Bll_Access::getAccess($cuid, $pid);
    	if ($access['project'] == 'ipanda') {
    		$this->render('ipandanotice');
    	} else {
    		$this->render();
    	}
    }
    
    public function compensationAction()
    {
        $cuid = $this->info['uid'];
    	$pid = $this->platfromInfo['pid'];
    	$access = Hapyfish2_Bms_Bll_Access::getAccess($cuid, $pid);
        if ($access['project'] == 'ipanda') {
    		$this->render('ipanda/compensation');
    	} else {
    		$this->render();
    	}
    }
    
    public function feedAction()
    {
        $cuid = $this->info['uid'];
    	$pid = $this->platfromInfo['pid'];
    	$access = Hapyfish2_Bms_Bll_Access::getAccess($cuid, $pid);
        if ($access['project'] == 'ipanda') {
    		$this->render('ipanda/feed');
    	} else {
    		$this->render();
    	}
    }
    
    public function paysettingAction()
    {
    	$this->view->sectionNum = 4;
    	$this->view->sectionItemNum = 6;
    	$this->render();
    }
    
    public function movedataAction()
    {
		$cuid = $this->info['uid'];
		$pid = $this->platfromInfo['pid'];
    	$accessList = Hapyfish2_Bms_Bll_Access::getAccessList($cuid);
    	
    	$this->view->accessList = $accessList;
    	$this->view->thispid = $pid; 	
    	$this->render();
    }
    
}