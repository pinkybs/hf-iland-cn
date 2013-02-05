<?php

class IndexController extends Zend_Controller_Action
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
	
    public function init()
    {
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }
	
	function setAuthed($uid)
	{
        $t = time();
        $sig = md5($uid . $t . APP_SIGN_SECRET);
        
        $skey = $uid .  '_' . $t . '_' . $sig;
        
        setcookie('hf_customer_qq_skey', $skey , 0, '/', '.console.island.qzoneapp.com');
	}
	
	public function indexAction()
	{
		$info = $this->vailid();
        if ($info) {
			$this->_redirect('/infocenter');
			exit;
        }
        $this->view->errtype = $this->_request->getParam('errtype', '0');
        $this->render();
	}
	
	public function loginAction()
	{
		$uin = $this->_request->getPost('uin');
		$pwd = $this->_request->getPost('pwd');
		
		$user = Hapyfish_Island_Customer_Bll_Auth::login($uin, $pwd);
		if (!$user) {
			Hapyfish_Island_Customer_Bll_Log::loginerror($uin, $pwd);
			$this->_redirect('/?errtype=1');
		}
		$uid = $user['uid'];
		$this->setAuthed($uid);
		
		//add login log
		Hapyfish_Island_Customer_Bll_Log::login($uid);
		
		$this->_redirect('/infocenter');
		exit;
	}
	
	public function logoutAction()
	{
		setcookie('hf_customer_qq_skey', '' , 0, '/', '.console.island.qzoneapp.com');
		$this->_redirect('/');
		exit;
	}
	
}