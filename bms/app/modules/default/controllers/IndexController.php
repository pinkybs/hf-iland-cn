<?php

class IndexController extends Zend_Controller_Action
{
	
    public function init()
    {
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }
	
	public function indexAction()
	{
		$info = Hapyfish2_Bms_Bll_Auth::vailid();
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
		
		$user = Hapyfish2_Bms_Bll_Auth::login($uin, $pwd);
		if (!$user) {
			$this->_redirect('/?errtype=1');
		}
		$uid = $user['uid'];
		Hapyfish2_Bms_Bll_Auth::setVerified($uid);

		$this->_redirect('/infocenter');
		exit;
	}
	
	public function logoutAction()
	{
		Hapyfish2_Bms_Bll_Auth::setUnverified();
		$this->_redirect('/');
		exit;
	}
	
}