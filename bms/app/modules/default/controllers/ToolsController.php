<?php

class ToolsController extends Zend_Controller_Action
{	
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }
        
        $this->info = $info;
        $platform = $this->_request->getParam('platform');
        if (empty($platform)) {
        	$platform = 'kaixin001';
        }
        $this->platform = $platform;
        $this->platfromInfo = Hapyfish2_Bms_Bll_Platform::getInfoByName($platform);
        $this->view->platform = $platform;
        $this->view->cuid = $info['uid'];
        $this->view->ts = $info['t'];
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }
	
    public function csAction()
    {
    	$cuid = $this->info['uid'];
    	$pid = $this->platfromInfo['pid'];
    	$access = Hapyfish2_Bms_Bll_Access::getAccess($cuid, $pid);
    	if ($access['project'] == 'ipanda') {
    		$this->render('ipandacs');
    	} else if ($access['project'] == 'alchemy') {
    		$this->render('alchemycs');
    	} else {
    		$this->render();
    	}
    }

    public function qqcsAction()
    {
    	$this->render();
    }
	
}