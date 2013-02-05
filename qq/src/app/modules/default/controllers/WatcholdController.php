<?php

class WatcholdController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo '1001';
			exit;
		}

		/*
		$t = $this->_request->getParam('t');
		if (empty($t)) {
			echo '1001';
			exit;
		}

		$sig = $this->_request->getParam('sig');
		if (empty($t)) {
			echo '1001';
			exit;
		}

		$validSig = md5($uid . $t . APP_KEY);
		if ($sig != $validSig) {
			echo '1002';
			exit;
		}

		$now = time();
		if (abs($now - $t) > 1800) {
			echo '1003';
			exit;
		}*/

		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	$uid = $this->check();
    	$user = Hapyfish2_Platform_Bll_Factory::getUser($uid);
        $openid = $user['openid'];
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);
        //simulate
        $openkey = md5($t);

        $sig = md5($uid . $openid . $openkey . $t . $rnd . APP_KEY);

        $skey = $uid . '_' . $openid . '_' . $openkey . '_' . $t . '_' . $rnd . '_' . $sig;

        setcookie('hf_skey', $skey , 0, '/', '.devqq.happyfish001.com');

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }

        $this->view->uid = $uid;
        $this->render();
    }
 }
