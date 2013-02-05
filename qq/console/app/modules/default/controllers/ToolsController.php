<?php

class ToolsController extends Zend_Controller_Action
{
	function vailid()
	{
    	$skey = $_COOKIE['hf_customer_qq_skey'];
    	if (!$skey) {
    		return false;
    	}
    	
    	$tmp = split('_', $skey);
    	if (empty($tmp) || count($tmp) != 3) {
    		return false;
    	}
    	
        $uid = $tmp[0];
        $t = $tmp[1];
        $sig = $tmp[2];
        
        $vsig = md5($uid . $t . APP_SIGN_SECRET);
        if ($sig != $vsig) {
        	return false;
        }
        
        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }
        
        return array('uid' => $uid, 't' => $t);
	}
	
    function init()
    {
        $info = $this->vailid();
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
    	$this->render();
    }
	
    public function csAction()
    {
    	$this->render();
    }
    
    public function maintenanceAction()
    {
    	$this->render();
    }
    
    public function operationAction()
    {
    	$this->render();
    }
	
}