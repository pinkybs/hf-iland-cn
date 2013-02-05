<?php

class ApiController extends Zend_Controller_Action
{
    protected $cuid;

    protected $info;

    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }

        $this->info = $info;
        $this->cuid = $info['uid'];
        $this->platform = $this->_request->getParam('platform');
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }

    public function indexAction()
    {
    	echo 'Customer Tools API V1.0';
    	exit;
    }

	public function getuserinfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$ispuid = $this->_request->getParam('ispuid', '0');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			if ($ispuid == '1') {
				$result = $rest->getUserInfoByPUID($uid);
			} else {
				$result = $rest->getUserInfo($uid);

				if($this->platform == 'taobao') {
					$userInfoMore = $rest->getUserPlatformInfo($uid);
					$result['email'] = $userInfoMore['info']['email'];
				}
			}
			
			$result['watch'] = HOST . '/api/watchuser?platform=' . $this->platform . '&uid=' . $result['uid'];
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getuserplatforminfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getUserPlatformInfo($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getlogininfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getLoginInfo($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getqquserinfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$isqzone = $this->_request->getParam('isqzone', '0');
		if ($isqzone == '1') {
			$pf = 'qzone';
		} else {
			$pf = 'pengyou';
		}
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getUserInfo($uid, array('platform' => $pf));
			$result['watch'] = HOST . '/api/watchqquser?platform=' . $this->platform . '&isqzone=' . $isqzone . '&uid=' . $result['uid'];
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getusercardinfoAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getUserCardInfo($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getcoinlogAction()
	{
		$uid = $this->_request->getParam('uid');
		$isPrev = $this->_request->getParam('prev');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
		    $params = array();
		    if ($isPrev) {
		        $time = time();
		        $year = date('Y', $time);
		        $month = (int)date('n', $time);
		        //last month
		        $selTime = mktime(0, 0, 0, $month, 1, $year) - 86400;
		        $params['year'] = date('Y', $selTime);
		        $params['month'] = date('n', $selTime);
		        $params['limit'] = 200;
		    }
			$result = $rest->getCoinLog($uid, $params);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getgoldlogAction()
	{
		$uid = $this->_request->getParam('uid');
		$isPrev = $this->_request->getParam('prev');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
		    $params = array();
		    if ($isPrev) {
		        $time = time();
		        $year = date('Y', $time);
		        $month = (int)date('n', $time);
		        //last month
		        $selTime = mktime(0, 0, 0, $month, 1, $year) - 86400;
		        $params['year'] = date('Y', $selTime);
		        $params['month'] = date('n', $selTime);
		        $params['limit'] = 200;
		    }
			$result = $rest->getGoldLog($uid, $params);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getpaylogAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getPayLog($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getdonatelogAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getDonateLog($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getinvitelogAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getInviteLog($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getleveluplogAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getLevelUpLog($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getitemlistAction()
	{
		$type = $this->_request->getParam('type');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getItemList($type);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function blockuserAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->blockUser($uid);
			Hapyfish2_Bms_Bll_Log::operation($this->cuid, $this->platform, '封号:' . $uid);
			info_log($uid, 'blockuser');
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function unblockuserAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->unblockUser($uid);
			Hapyfish2_Bms_Bll_Log::operation($this->cuid, $this->platform, '解封:' . $uid);
			info_log($uid, 'unblockuser');
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getpraiseAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getPraise($uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function fixpraiseAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->fixPraise($uid);
			Hapyfish2_Bms_Bll_Log::operation($this->cuid, $this->platform, '修复装饰度:' . $uid);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function watchuserAction()
	{
		$uid = $this->_request->getParam('uid');
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getWatchUser($uid);
			if ($result['url']) {
				Hapyfish2_Bms_Bll_Log::operation($this->cuid, $this->platform, '模拟登录用户小岛:' . $uid);
				$this->_redirect($result['url']);
			} else {
				$this->echoError(1, 'system error');
			}
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function watchqquserAction()
	{
		$uid = $this->_request->getParam('uid');
		$isqzone = $this->_request->getParam('isqzone', '0');
		if ($isqzone == '1') {
			$pf = 'qzone';
		} else {
			$pf = 'pengyou';
		}
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->getWatchUser($uid, array('platform' => $pf));
			if ($result['url']) {
				Hapyfish2_Bms_Bll_Log::operation($this->cuid, $this->platform, '模拟登录用户小岛(' . $pf . '):' . $uid);
				$this->_redirect($result['url']);
			} else {
				$this->echoError(1, 'system error');
			}
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function getnoticeAction()
	{
		$type = $this->_request->getParam('type', 2);
		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		$rest->server_addr = 'http://api.island.qzoneapp.com';
		try {
			$result = $rest->getNotice($type);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

	public function updatenoticeAction()
	{
		$id = $this->_request->getParam('id');
		if (empty($id)) {
			$this->echoError(1001, 'id can not empty');
		}

		$params = $this->_request->getParams();
		$info = array('id' => $id);
		if (isset($params['position'])) {
			$info['position'] = $params['position'];
		}
		if (isset($params['title'])) {
			$info['title'] = $params['title'];
		}
		if (isset($params['link'])) {
			$info['link'] = $params['link'];
		}
		if (isset($params['priority'])) {
			$info['priority'] = $params['priority'];
		}
		if (isset($params['opened'])) {
			$info['opened'] = $params['opened'];
		}
		if (isset($params['time'])) {
			$info['create_time'] = $params['time'];
		}

		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		try {
			$result = $rest->updateNotice($info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}

}