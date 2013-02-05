<?php

class OpenapiController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}

		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
			exit;
		}

		return $uid;
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

    public function noopAction()
    {
    	$data = array('id' => SERVER_ID, 'time' => time());
    	$this->echoResult($data);
    }

    public function watchuserAction()
    {
		$uid = $this->check();
		$t = time();
    	$platform = $this->_request->getParam('platform');
		if (empty($platform)) {
			$platform = 'pengyou';
		}
		$sig = md5($uid . $t . $platform . APP_KEY);
		if ($platform == 'qzone') {
			$url = QZONE_HOST . '/watch?uid=' . $uid . '&t=' . $t . '&platform=' . $platform . '&sig=' . $sig;
		} else {
			$url = PENGYOU_HOST . '/watch?uid=' . $uid . '&t=' . $t . '&platform=' . $platform . '&sig=' . $sig;
		}
		
		$data = array('url' => $url);
		$this->echoResult($data);
    }

	public function userinfoAction()
	{
		$uid = $this->check();
		$platform = $this->_request->getParam('platform');
		if (empty($platform)) {
			$platform = 'pengyou';
		}
		if ($platform == 'qzone') {
			$platformUser = Hapyfish2_Platform_Bll_UserQzone::getUser($uid);
		} else {
			$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		}
		
		$islandUser = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
			'uid' => $uid,
			'nickname' => $platformUser['nickname'],
			'gender' => $platformUser['gender'],
			'level' => $islandUser['level'],
			'exp' => $islandUser['exp'],
			'coin' => $islandUser['coin']
		);

		$data['status'] = Hapyfish2_Platform_Bll_Factory::getStatus($uid);

		$this->echoResult($data);
	}

	public function usercardinfoAction()
	{
		$uid = $this->check();
		$cardInfoList = Hapyfish2_Island_Cache_BasicInfo::getCardList();
		$userCardInfo = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		$cards = array();
		if ($userCardInfo) {
			foreach ($userCardInfo as $cid => $item) {
				if ($item['count'] > 0) {
					$cards[] = array(
						'cid' => $cid,
						'name' => $cardInfoList[$cid]['name'],
						'introduce' => $cardInfoList[$cid]['introduce'],
						'count' => $item['count']
					);
				}
			}
		}
		$data = array(
			'cards' => $cards
		);

		$this->echoResult($data);
	}

	public function coinlogAction()
	{
		$uid = $this->check();
		$time = time();
		$year = $this->_request->getParam('year');
		if (!$year) {
			$year = date('Y');
		}
		$month = $this->_request->getParam('month');
		if (!$month) {
			$month = date('n');
		}
		$limit = $this->_request->getParam('limit');
		if (!$limit) {
			$limit = 100;
		}

		$logs = Hapyfish2_Island_Bll_ConsumeLog::getCoin($uid, $year, $month, $limit);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}

	public function goldlogAction()
	{
		$uid = $this->check();
		$time = time();
		$year = $this->_request->getParam('year');
		if (!$year) {
			$year = date('Y');
		}
		$month = $this->_request->getParam('month');
		if (!$month) {
			$month = date('n');
		}
		$limit = $this->_request->getParam('limit');
		if (!$limit) {
			$limit = 100;
		}

		$logs = Hapyfish2_Island_Bll_ConsumeLog::getGold($uid, $year, $month, $limit);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}

	public function invitelogAction()
	{
		$uid = $this->check();
		$logs = Hapyfish2_Island_Bll_InviteLog::getAll($uid);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}

	public function leveluplogAction()
	{
		$uid = $this->check();
		$logs = Hapyfish2_Island_Bll_LevelUpLog::getAll($uid);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}

	public function itemlistAction()
	{
		$type = $this->_request->getParam('type', '0');
		if ($type == 1) {
			$backgroundlist = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
			$data = array('backgroundlist' => $backgroundlist);
		} else if ($type == 2) {
			$buildinglist = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			$data = array('buildinglist' => $buildinglist);
		} else if ($type == 3) {
			$plantlist = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
			$data = array('plantlist' => $plantlist);
		} else if ($type == 4) {
			$cardlist = Hapyfish2_Island_Cache_BasicInfo::getCardList();
			$data = array('cardlist' => $cardlist);
		} else {
			$cardlist = Hapyfish2_Island_Cache_BasicInfo::getCardList();
			$backgroundlist = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
			$buildinglist = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			$plantlist = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
			$data = array('backgroundlist' => $backgroundlist, 'buildinglist' => $buildinglist, 'plantlist' => $plantlist, 'cardlist' => $cardlist);
		}

		$this->echoResult($data);
	}

	//move data
	public function getuserinfoallmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_User::getDefaultInstance();
            $data = $db->getUserInfoAll($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-1');
		}
		
		$this->echoResult($data);
	}

	public function getuserachievementmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_Achievement::getDefaultInstance();
            $data = $db->getAllData($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-2');
		}
		
		$this->echoResult($data);
	}
	
	public function getuserbackgroundmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_Background::getDefaultInstance();
            $data = $db->getAllData($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-3');
		}
		
		$this->echoResult($data);
	}
	
	public function getuserbuildingmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_Building::getDefaultInstance();
            $data = $db->getAllData($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-4');
		}
		
		$this->echoResult($data);
	}
	
	public function getusercardmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_Card::getDefaultInstance();
            $data = $db->getAllCard($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-5');
		}
		
		$this->echoResult($data);
	}
	
	public function getuserdockmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_Dock::getDefaultInstance();
            $data = $db->getUserDockData($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-6');
		}
		
		$this->echoResult($data);
	}
	
	public function getuserislandmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
            $data = $db->getUserIslandData($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-7');
		}
		
		$this->echoResult($data);
	}
	
	public function getuserplantmdAction()
	{
		$uid = $this->check();
		
		$data = array();
		
		try {
		    $db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
            $data = $db->getAllCidRow($uid);
		} catch (Exception $e) {
			info_log($e, 'moveData-8');
		}
		
		$this->echoResult($data);
	}
	
}