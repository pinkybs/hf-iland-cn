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
		$sig = md5($uid . $t . APP_KEY);

		$url = HOST . '/watch?uid=' . $uid . '&t=' . $t . '&sig=' . $sig;
		$data = array('url' => $url);
		$this->echoResult($data);
    }

	public function userinfoAction()
	{
		$uid = $this->check();
		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$islandUser = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
		    'puid' => $platformUser['puid'],
			'uid' => $uid,
			'nickname' => $platformUser['name'],
			'gender' => $platformUser['gender'],
			'level' => $islandUser['level'],
			'exp' => $islandUser['exp'],
			'coin' => $islandUser['coin'],
		    'homeurl' => 'http://jianghu.taobao.com/u/' . base64_encode($platformUser['puid']) . '/front.htm'
		);

		$data['status'] = Hapyfish2_Platform_Cache_User::getStatus($uid);

		$this->echoResult($data);
	}

	public function userinfobypuidAction()
	{
		$puid = $this->_request->getParam('puid');
		if (empty($puid)) {
			$this->echoError(1001, 'puid can not empty');
		}

		try {
			$platformUidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		} catch (Exception $e) {
			$platformUidInfo = null;
		}

		if (!$platformUidInfo) {
			$this->echoError(1002, 'puid error, not app user');
			exit;
		}
		$uid = $platformUidInfo['uid'];

		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$islandUser = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
			'puid' => $platformUser['puid'],
			'uid' => $uid,
			'nickname' => $platformUser['name'],
			'gender' => $platformUser['gender'],
			'level' => $islandUser['level'],
			'exp' => $islandUser['exp'],
			'coin' => $islandUser['coin'],
			'homeurl' => 'http://jianghu.taobao.com/u/' . base64_encode($platformUser['puid']) . '/front.htm'
		);

		$data['status'] = Hapyfish2_Platform_Cache_User::getStatus($uid);

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
	
	public function logininfoAction()
	{
		$uid = $this->check();
		$data = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
		$this->echoResult($data);
	}
	
	public function appinfoAction()
	{
		$info = Hapyfish2_Island_Cache_AppInfo::getInfo();
		$this->echoResult($info);
	}
	
	public function checkappstatusAction()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$uid = 0;
		} else {
			$uid = $this->check();
		}
		$redirect = $this->_request->getParam('redirect');
		if ($redirect == '1') {
			$redirect = true;
		} else {
			$redirect = false;
		}
		$force = $this->_request->getParam('force');
		if ($force == '0') {
			$force = false;
		} else {
			$force = true;
		}
		
		$info = Hapyfish2_Island_Bll_AppInfo::checkStatus($uid, $redirect, $force);
		$this->echoResult($info);
	}

	private function extract(&$data, $fields)
	{
		$out = array();
		foreach ($data as $k => $v) {
			$tmp = array();
			foreach ($fields as $f) {
				$tmp[$f] = $v[$f];
			}
			$out[$k] = $tmp;
		}

		return $out;
	}

	public function itemlistAction()
	{
		$type = $this->_request->getParam('type', '0');
		if ($type == 1) {
			$backgroundlist = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
			$data = array(
				'backgroundlist' => $this->extract($backgroundlist, array('bgid', 'name', 'can_buy'))
			);
		} else if ($type == 2) {
			$buildinglist = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			$data = array(
				'buildinglist' => $this->extract($buildinglist, array('cid', 'name', 'can_buy'))
			);
		} else if ($type == 3) {
			$plantlist = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
			$data = array(
				'plantlist' => $this->extract($plantlist, array('cid', 'name', 'can_buy'))
			);
		} else if ($type == 4) {
			$cardlist = Hapyfish2_Island_Cache_BasicInfo::getCardList();
			$data = array(
				'cardlist' => $this->extract($cardlist, array('cid', 'name', 'can_buy'))
			);
		} else {
			$backgroundlist = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
			$buildinglist = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			$plantlist = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
			$cardlist = Hapyfish2_Island_Cache_BasicInfo::getCardList();
			$data = array(
				'backgroundlist' => $this->extract($backgroundlist, array('bgid', 'name', 'can_buy')),
				'buildinglist' => $this->extract($buildinglist, array('cid', 'name', 'can_buy')),
				'plantlist' => $this->extract($plantlist, array('cid', 'name', 'can_buy')),
				'cardlist' => $this->extract($cardlist, array('cid', 'name', 'can_buy'))
			);
		}

		$this->echoResult($data);
	}


    public function userplatforminfoAction()
	{
		$uid = $this->check();
		$info = Hapyfish2_Platform_Bll_UserMore::getInfo($uid);
		$this->echoResult($info);
	}
	
	//move data update
	public function updateuserinfomdAction()
	{
		$uid = $this->_request->getParam('uid');
		$paramsStr = $this->_request->getParam('params');

		$params = explode(',', $paramsStr);
		
        //coin
		$data['coin'] = Hapyfish2_Island_HFC_User::incUserCoin($uid, $params[1]);
		if ($data['coin'] == 1) {
			info_log('uid:' . $uid . ',coin:' . $params[1], 'moveDataInfo');
		}
		
		//gold
		$goldInfo = array(
			'uid' => $uid,
			'gold' => $params[2],
			'type' => 0
		);
		
		$data['gold'] = Hapyfish2_Island_Bll_Gold::add($uid, $goldInfo);
		if ($data['gold'] == 1) {
			info_log('uid:' . $uid . ',gold:' . $params[2], 'moveDataInfo');
		}
		
		//level
		$levelInfo = array('level' => $params[4],
							'island_level'	=> $params[5]);
//							'island_level_2'	=> $params[6],
//							'island_level_3'	=> $params[7],
//							'island_level_4'	=> $params[8]);
		
		$data['level'] = Hapyfish2_Island_HFC_User::updateUserLevel($uid, $levelInfo);
		if ($data['level'] == 1) {
			info_log('uid:' . $uid . ',level:' . json_encode($levelInfo), 'moveDataInfo');
		}
		
		//exp
		$data['exp'] = Hapyfish2_Island_HFC_User::incUserExp($uid, $params[3]);
		$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
		if ($data['exp'] == 1) {
			info_log('uid:' . $uid . ',exp:' . $params[3], 'moveDataInfo');
		}
		
		//starfish
		$data['starfish'] = Hapyfish2_Island_HFC_User::incUserStarFish($uid, $params[11]);
		if ($data['starfish'] == 1) {
			info_log('uid:' . $uid . ',starfish:' . $params[11], 'moveDataInfo');
		}		

		//login
		$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
		$activeCount = $activeResult['activeCount'];
		$loginInfo['last_login_time'] = $params[6];
		$loginInfo['active_login_count'] = $params[7];
		$loginInfo['max_active_login_count'] = $params[8];
		$loginInfo['all_login_count'] = $params[9];
		$loginInfo['star_login_count'] = $params[10];
		if ($loginInfo['active_login_count'] > $loginInfo['max_active_login_count']) {
			$loginInfo['max_active_login_count'] = $loginInfo['active_login_count'];
		}
		
		$loginInfo['today_login_count'] = 1;
		if ( $loginInfo['all_login_count'] < 8 ) {
		$loginInfo['all_login_count'] += 1;
		}

		if ( $loginInfo['star_login_count'] < 15 ) {
			$loginInfo['star_login_count'] += 1;
		}
		
		$data['login'] = Hapyfish2_Island_HFC_User::updateUserLoginInfo($uid, $loginInfo, true);
		if ($data['login'] == 1) {
			info_log('uid:' . $uid . ',login:1', 'moveDataInfo');
		}
		
		info_log(json_encode($data), 'moveDataOK');
		
		$this->echoResult($data);
	}
	
	public function updateuserachievementmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$paramsStr = $this->_request->getParam('params');

		$params = explode(',', $paramsStr);
        
        $userAchievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
		
        $userAchievement['num_1'] = $params[1];
        $userAchievement['num_2'] = $params[2];
        $userAchievement['num_3'] = $params[3];
        $userAchievement['num_4'] = $params[4];
        $userAchievement['num_5'] = $params[5];
        $userAchievement['num_6'] = $params[6];
        $userAchievement['num_7'] = $params[7];
        $userAchievement['num_8'] = $params[8];
        $userAchievement['num_9'] = $params[9];
        $userAchievement['num_10'] = $params[10];
        $userAchievement['num_11'] = $params[11];
        $userAchievement['num_12'] = $params[12];
        $userAchievement['num_13'] = $params[13];
        $userAchievement['num_14'] = $params[14];
        $userAchievement['num_15'] = $params[15];
        $userAchievement['num_16'] = $params[16];
        $userAchievement['num_17'] = $params[17];
        $userAchievement['num_18'] = $params[18];
        $userAchievement['num_19'] = $params[19];
        $userAchievement['num_20'] = $params[20];
        $userAchievement['num_21'] = $params[21];
		$userAchievement['num_22'] = $params[22];
		$userAchievement['num_23'] = $params[23];
		$userAchievement['num_24'] = $params[24];
		$userAchievement['num_25'] = $params[25];
		$userAchievement['num_26'] = $params[26];
		$userAchievement['num_27'] = $params[27];
		$userAchievement['num_28'] = $params[28];
		$userAchievement['num_29'] = $params[29];
		$userAchievement['num_30'] = $params[30];
		$userAchievement['num_31'] = $params[31];
		$userAchievement['num_32'] = $params[32];
		$userAchievement['num_33'] = $params[33];
		$userAchievement['num_34'] = $params[34];
		$userAchievement['num_35'] = $params[35];
		$userAchievement['num_36'] = $params[36];
		$userAchievement['num_37'] = $params[37];
		$userAchievement['num_38'] = $params[38];
		$userAchievement['num_40'] = $params[39];
		$userAchievement['num_41'] = $params[40];
		$userAchievement['num_42'] = $params[40];
        
        try {
			Hapyfish2_Island_HFC_Achievement::updateUserAchievement($uid, $userAchievement);
			
			//task id 3015,task type 13
			$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3015);
			if ( $checkTask['status'] == 1 ) {
				$result['finishTaskId'] = $checkTask['finishTaskId'];
			}
		} catch (Exception $e) {
		}
        
		$data = array(1);
		$this->echoResult($data);
	}
	
	public function updateuserbackgroundmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$paramsStr = $this->_request->getParam('params');

		$params = explode(',', $paramsStr);
        
        foreach ($params as $bgid) {
        	$background = array();
			$background = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($bgid);
			
			if ($background) {
				Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $bgid, 1);
			} else {
				info_log($bgid, 'moveDataNullBackground-' . $uid);
			}
        }
        
		$data = array(1);
		$this->echoResult($data);
	}
	
	public function updateuserbuildingmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$paramsStr = $this->_request->getParam('params');

		$params = explode(',', $paramsStr);
        
        foreach ($params as $key => $cid) {
			$building = array();
			$building = Hapyfish2_Island_Cache_BasicInfo::getBackgoundInfo($cid);
        	
			if (!$building) {
				info_log($cid, 'moveDataNullBuilding-' . $uid);
			} else {
				Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $cid, 1);
			}
			
        	
        }
        
		$data = array(1);
		$this->echoResult($data);
	}
	
	public function updateuserplantmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$paramsStr = $this->_request->getParam('params');

		$params = explode(',', $paramsStr);
        
        foreach ($params as $key => $cid) {
			$plant = array();
			$plant = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
			
			if (!$plant) {
				info_log($cid, 'moveDataNullPlant-' . $uid);
			} else {
        		Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $cid, 1);
			}
        }
        
		$data = array(1);
		$this->echoResult($data);
	}
	
	public function updateusercardmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$cidStr = $this->_request->getParam('cid');
		$countStr = $this->_request->getParam('count');

		$cids = explode(',', $cidStr);
		$counts = explode(',', $countStr);
        
        foreach ($cids as $key => $cid) {
        	$card = array();
			$card = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
			
			if (!$card) {
				foreach ($counts as $ckey => $count) {
					if ($ckey == $key) {
						info_log($cid . ':' . $count, 'moveDataNullCard-' . $uid);
					}
				}
			} else {
				foreach ($counts as $ckey => $count) {
					if ($ckey == $key) {
						Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $cid, $count);
					}
				}
			}
        }
        
		$data = array(1);
		$this->echoResult($data);
	}
	
	public function updateuserdockmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$position_id_str = $this->_request->getParam('position_id');
		$unlock_ship_ids_str = $this->_request->getParam('unlock_ship_ids');

		$position_ids = explode(',', $position_id_str);
		$unlock_ship_ids = explode('*', $unlock_ship_ids_str);

		$dalDock = Hapyfish2_Island_Dal_Dock::getDefaultInstance();
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		
		foreach ($position_ids as $key => $position_id) {
			$info = array();
			foreach ($unlock_ship_ids as $ukey => $unlock_ship_id) {
				if ($key == $ukey) {
					$info = array('position_id' => $position_id, 'unlock_ship_ids' => $unlock_ship_id);
					try {
						$dock = 0;
						$dock = $dalDock->getDockData($uid, $info);
						
						if (!$dock) {
							$dalDock->expandPosition($uid, $info['position_id']);
						}
						
						$dalDock->updateDockData($uid, $info);
					} catch (Exception $e) {}
					
					$key = 'i:u:dock:' . $uid . ':' . $position_id;
					$cache->delete($key);
				}
			}
		}
        
		$data = array(1);
		$this->echoResult($data);
	}
	
	public function updateuserislandmdAction()
	{
		$uid = $this->_request->getParam('uid');
		$params = $this->_request->getParam('params');
        
        $userIslandInfo = Hapyfish2_Island_HFC_User::getUserIsland($uid);
        
        $userIslandInfo['unlock_island'] = $params;
        
		Hapyfish2_Island_HFC_User::updateUserIsland($uid, $userIslandInfo);
        
		$data = array(1);
		$this->echoResult($data);
	}
}