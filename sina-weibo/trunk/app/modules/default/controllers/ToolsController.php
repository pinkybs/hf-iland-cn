<?php

class ToolsController extends Zend_Controller_Action
{
	function vaild()
	{

	}

    protected function getClientIP()
    {
    	$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }

	function check()
	{
	    $ip = $this->getClientIP();
		if ($ip != '27.115.48.202' && $ip != '116.247.76.102') {
			echo 'no permission';
			exit;
		}

		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo 'uid can not empty';
			exit;
		}

		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

    public function checkistest()
    {
        if ( STATIC_HOST == 'http://static.hapyfish.com/weibo' ) {
	        echo 'false';
	        exit;
        }
    }
	
	public function addcoinAction()
	{
		$this->checkistest();
		$uid = $this->check();
		$coin = $this->_request->getParam('coin');
		if (empty($coin) || $coin <= 0) {
			echo 'add coin error, must > 1';
			exit;
		}

		Hapyfish2_Island_HFC_User::incUserCoin($uid, $coin);

		echo 'OK';
		exit;
	}

	public function addgoldAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$gold = $this->_request->getParam('gold');
		if (empty($gold) || $gold <= 0) {
			echo 'add gold error, must > 1';
			exit;
		}

		$goldInfo = array(
			'uid' => $uid,
			'gold' => $gold,
			'type' => 0
		);
		Hapyfish2_Island_Bll_Gold::add($uid, $goldInfo);
		
		//update by hudanfeng add send gold log start
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('801', array($uid, $gold, 0));
		//end
			
		echo 'OK';
		exit;
	}

    public function decgoldAction()
    {
        $uid = $this->check();
        $gold = $this->_request->getParam('gold');
        if (empty($gold) || $gold <= 0) {
            echo 'dec gold error, must > 1';
            exit;
        }

        try {
            $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
            $dalUser->decGold($uid, $gold);

            Hapyfish2_Island_HFC_User::reloadUserGold($uid);
        } catch (Exception $e) {
            return 'false';
        }
        
        echo 'OK';
        exit;
    }
	
	public function addstarfishAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$starfish = $this->_request->getParam('starfish');
		if (empty($starfish) || $starfish <= 0) {
			echo 'add starfish error, must > 1';
			exit;
		}

		Hapyfish2_Island_HFC_User::incUserStarFish($uid, $starfish);

		echo 'OK';
		exit;
	}

	public function addexpAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$exp = $this->_request->getParam('exp');
		if (empty($exp) || $exp <= 0) {
			echo 'add exp error, must > 1';
			exit;
		}

		Hapyfish2_Island_HFC_User::incUserExp($uid, $exp);

		echo 'OK';
		exit;
	}

	public function addcardAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$cid = $this->_request->getParam('cid');
		if (empty($cid)) {
			echo 'card id[cid] can not empty';
			exit;
		}

		$count = $this->_request->getParam('count');
		if (empty($count) || $count <= 0) {
			echo 'add card number[count] error, must > 1';
			exit;
		}

		$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
		if (!$cardInfo) {
			echo 'card id[cid] error, not exists';
			exit;
		}

		Hapyfish2_Island_HFC_Card::addUserCard($uid, $cid, $count);

		echo 'OK';
		exit;
	}

	public function addachievementAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$num = $this->_request->getParam('num');
		if (empty($num)) {
			echo 'num can not empty';
			exit;
		}

		if ($num <=0 || $num > 17) {
			echo 'num error, must > 0 and < 18';
			exit;
		}

		$count = $this->_request->getParam('count');
		if (empty($count) || $count <= 0) {
			echo 'add count error, must > 1';
			exit;
		}

		$field = 'num_' . $num;
		Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, $field, $count);

		echo 'OK';
		exit;
	}

	public function adddailyachievementAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$num = $this->_request->getParam('num');
		if (empty($num)) {
			echo 'num can not empty';
			exit;
		}

		if ($num <=0 || $num > 17) {
			echo 'num error, must > 0 and < 18';
			exit;
		}

		$count = $this->_request->getParam('count');
		if (empty($count) || $count <= 0) {
			echo 'add count error, must > 1';
			exit;
		}

		$field = 'num_' . $num;
		Hapyfish2_Island_HFC_AchievementDaily::updateUserAchievementDailyByField($uid, $field, $count);

		echo 'OK';
		exit;
	}

	public function cleardailytaskAction()
	{
		$uid = $this->check();
		Hapyfish2_Island_Cache_TaskDaily::clearAll($uid);

		echo 'OK';
		exit;
	}

	public function changelevelAction()
	{
		$uid = $this->check();
		$level = $this->_request->getParam('level');
		if (empty($level)) {
			echo 'level can not empty';
			exit;
		}

		if ($level <=0 || $level > 200) {
			echo 'level error, level > 0 and < 200';
			exit;
		}

		$levelInfo = array('level' => $level);
		$islandLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$curIslandLevel = $islandLevelInfo['island_level'];

		$levelInfo['island_level'] = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfoByUserLevel($level, 1);
		$levelInfo['island_level_2'] = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfoByUserLevel($level, 2);
		$levelInfo['island_level_3'] = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfoByUserLevel($level, 3);
		$levelInfo['island_level_4'] = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfoByUserLevel($level, 4);

		Hapyfish2_Island_HFC_User::updateUserLevel($uid, $levelInfo);
		$exp = Hapyfish2_Island_Cache_BasicInfo::getUserLevelExp($level);
		Hapyfish2_Island_HFC_User::updateUserExp($uid, $exp + 1, true);

		$step = $levelInfo['island_level'] - $curIslandLevel;

		Hapyfish2_Island_HFC_Plant::upgradeCoordinate($uid, $step);
		Hapyfish2_Island_HFC_Building::upgradeCoordinate($uid, $step);
		echo 'OK';
		exit;
	}

	public function changelevelnoislandAction()
	{
		$uid = $this->check();
		$level = $this->_request->getParam('level');
		if (empty($level)) {
			echo 'level can not empty';
			exit;
		}

		if ($level <=0 || $level > 200) {
			echo 'level error, level > 0 and < 200';
			exit;
		}

		$levelInfo = array('level' => $level);
		$islandLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$curIslandLevel = $islandLevelInfo['island_level'];

		$levelInfo['island_level'] = $islandLevelInfo['island_level'];
		$levelInfo['island_level_2'] = $islandLevelInfo['island_level_2'];
		$levelInfo['island_level_3'] = $islandLevelInfo['island_level_3'];
		$levelInfo['island_level_4'] = $islandLevelInfo['island_level_4'];

		Hapyfish2_Island_HFC_User::updateUserLevel($uid, $levelInfo);
		$exp = Hapyfish2_Island_Cache_BasicInfo::getUserLevelExp($level);
		Hapyfish2_Island_HFC_User::updateUserExp($uid, $exp + 1, true);

		$step = $levelInfo['island_level'] - $curIslandLevel;

		Hapyfish2_Island_HFC_Plant::upgradeCoordinate($uid, $step);
		Hapyfish2_Island_HFC_Building::upgradeCoordinate($uid, $step);
		echo 'OK';
		exit;
	}

	public function clearhelpAction()
	{
		$uid = $this->check();
		Hapyfish2_Island_Cache_UserHelp::clearHelp($uid);
		echo 'OK';
		exit;
	}
	public function inituserhelpAction()
	{
		$uid = $this->check();
		$info = array('help' => '' ,'help_gift' => '');
		$dalUserHelp = Hapyfish2_Island_Dal_UserHelp::getDefaultInstance();
        $dalUserHelp->update($uid, $info);

		echo 'OK';
		exit;
	}

	public function loadnoticeAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadNoticeList();
		print_r($list);
		exit;
	}

	public function loadlocalnoticeAction()
	{
		$key = 'island:pubnoticelist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);

		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false, 900);
		print_r($list);
		exit;
	}

	public function updatenoticeAction()
	{
		$id = $this->_request->getParam('id');
		$title = $this->_request->getParam('title');
		$link = $this->_request->getParam('link');
		$time = time();

		$info = array('title' => $title, 'link' => $link, 'create_time' => $time);
		try {
			$dalBasic = Hapyfish2_Island_Dal_BasicInfo::getDefaultInstance();
			$dalBasic->updateNoticeList($id, $info);
		} catch (Exception $e) {
			echo 'false';
			exit;
		}

		echo 'OK';
		print_r($info);
		exit;
	}

	public function loadfeedtemplateAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadFeedTemplate();
		$key = 'island:feedtemplate';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);
		echo 'OK';
		exit;
	}

	public function loadallAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadFeedTemplate();
		$key = 'island:feedtemplate';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadShipList();
		$key = 'island:shiplist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadBuildingList();
		$key = 'island:buildinglist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadPlantList();
		$key = 'island:plantlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadBackgroundList();
		$key = 'island:backgroundlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadCardList();
		$key = 'island:cardlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadDockList();
		$key = 'island:docklist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadUserLevelList();
		$key = 'island:userlevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadIslandLevelList();
		$key = 'island:islandlevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadGiftLevelList();
		$key = 'island:giftlevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadAchievementTaskList();
		$key = 'island:achievementtasklist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadBuildTaskList();
		$key = 'island:buildtasklist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadDailyTaskList();
		$key = 'island:dailytasklist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadShipPraiseList();
		$key = 'island:shippraiselist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadTitleList();
		$key = 'island:titlelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadNoticeList();
		$key = 'island:pubnoticelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Island_Cache_BasicInfo::loadGiftList();
		$key = 'island:giftlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		echo 'ok';
		exit;
	}

	public function loadgiftAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadGiftList();
		$key = 'island:giftlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list);
		echo 'OK';
		exit;
	}

	public function fixAction()
	{
		$uid = $this->check();
		$data = Hapyfish2_Island_HFC_Plant::getOnIsland($uid);
		$plants = $data['plants'];
		$praise = 0;
		$plantInfoList = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		foreach ($plants as $plant) {
			$praise += $plantInfoList[$plant['cid']]['add_praise'];
		}

		$buildings = Hapyfish2_Island_HFC_Building::getOnIsland($uid);
		$buildingInfoList = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
		foreach ($buildings as $building) {
			$praise += $buildingInfoList[$building['cid']]['add_praise'];
		}

		echo '<br/>cal: ' . $praise;
		$useIsland = Hapyfish2_Island_HFC_User::getUserIsland($uid);
		$curPraise = $useIsland['praise'];

		echo '<br/>current: ' . $curPraise;

		if ($curPraise != $praise) {
			$useIsland['praise'] = $praise;
			Hapyfish2_Island_HFC_User::updateUserIsland($uid, $useIsland, true);
			echo '<br/>save praise';
		}

		$achi = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
		echo '<br/>achi: ' . $achi['num_13'];
		if ($achi['num_13'] != $praise) {
			$achi['num_13'] = $praise;
			Hapyfish2_Island_HFC_Achievement::saveUserAchievement($uid, $achi);
		}

		echo '<br/>num_15: ' . $achi['num_15'];
		$user = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'level' => 1));
		$islandLevelInfo = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfo($user['island_level']);
		if ($achi['num_15'] != $islandLevelInfo['max_visitor']) {
			$achi['num_15'] = $islandLevelInfo['max_visitor'];
			Hapyfish2_Island_HFC_Achievement::saveUserAchievement($uid, $achi);
		}

		$realDockPositionCount = Hapyfish2_Island_Cache_Dock::getPositionCount($uid);
		echo '<br/>position_count: ' . $useIsland['position_count'];
		if ($realDockPositionCount && $useIsland['position_count'] != $realDockPositionCount) {
			$useIsland['position_count'] = $realDockPositionCount;
			Hapyfish2_Island_HFC_User::updateUserIsland($uid, $useIsland, true);
		}

		echo '<br/>num_11: ' . $achi['num_11'];

		$userUnlockShipCount = Hapyfish2_Island_Cache_Dock::getUnlockShipCount($uid);
		print_r($userUnlockShipCount);

		Hapyfish2_Island_Cache_Dock::reloadUnlockShipCount($uid);

		exit;
	}

	public function fix2Action()
	{
		$uid = $this->check();
		$buildings = Hapyfish2_Island_HFC_Building::getOnIsland($uid);
		$fixed = false;
		if ($buildings) {
			$builingInfoList = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			foreach ($buildings as $building) {
				$item_type = $builingInfoList[$building['cid']]['item_type'];
				if ($item_type != $building['item_type']) {
					$fixed = true;
					$building['item_type'] = $item_type;
					$building['mirro'] = 0;
					Hapyfish2_Island_HFC_Building::updateOne($uid, $building['id'], $building, true);
				}
			}
		}

		echo $fixed ? 'true' : 'false';
		exit;
	}

	public function addgiftsendcountAction()
	{
		$uid = $this->check();
		$count = $this->_request->getParam('count');
		if (empty($count)) {
			echo 'count can not empty';
			exit;
		}

		if ($count <=0 || $count > 100) {
			echo 'count error, count > 0 and < 100';
			exit;
		}

		$giftSendCountInfo = Hapyfish2_Island_Cache_Counter::getSendGiftCount($uid);
		$giftSendCountInfo['count'] += $count;
		Hapyfish2_Island_Cache_Counter::updateSendGiftCount($uid, $giftSendCountInfo);
		echo 'OK';
		exit;
	}

	public function watchuserAction()
	{
		$uid = $this->check();
		$t = time();
		$sig = md5($uid . $t . APP_KEY);

		$this->_redirect('http://main.island.qzoneapp.com/watch?uid=' . $uid . '&t=' . $t . '&sig=' . $sig);
		exit;
	}

	public function userinfoAction()
	{
		$uid = $this->check();
		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$islandUser = Hapyfish2_Island_HFC_User::getUser($uid, array('exp', 'coin', 'level'));
		$data = array(
			'face' => $platformUser['figureurl'],
			'uid' => $uid,
			'nickname' => $platformUser['nickname'],
			'gender' => $platformUser['gender'],
			'level' => $islandUser['level'],
			'exp' => $islandUser['exp'],
			'coin' => $islandUser['coin']
		);

		echo json_encode($data);
		exit;
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
		echo json_encode($logs);
		exit;
	}

	public function upgradecoordinateAction()
	{
		$uid = $this->check();
		//Hapyfish2_Island_HFC_Plant::upgradeCoordinate($uid);
		Hapyfish2_Island_HFC_Building::upgradeCoordinate($uid);
		echo 'ok';
		exit;
	}

	public function p2Action()
	{
		$uid = $this->check();
		$data = Hapyfish2_Island_Cache_Background::getAll($uid);
		foreach ($data as $item) {
			if ($item['id'] > 1000) {
				Hapyfish2_Island_Cache_Background::delBackground($uid, $item['id']);
			}
		}
		print_r($data);

		exit;
	}

	public function p3Action()
	{
		$uid = $this->check();
		$fieldInfo = array();

		//25411, 1, 23212, 2, 22213, 3, 25914, 4
            //island
		$fieldInfo['bg_island'] = 25411;
		$fieldInfo['bg_island_id'] = 1;

            //sky
		$fieldInfo['bg_sky'] = 23212;
		$fieldInfo['bg_sky_id'] = 2;

            //sea
		$fieldInfo['bg_sea'] = 22213;
		$fieldInfo['bg_sea_id'] = 3;

            //dock
		$fieldInfo['bg_dock'] = 25914;
		$fieldInfo['bg_dock_id'] = 4;

		$ok = Hapyfish2_Island_HFC_User::updateFieldUserIsland($uid, $fieldInfo);

		echo $ok ? 'OK' : 'Flase';
		$d = Hapyfish2_Island_HFC_User::getUserIsland($uid);
		print_r($d);
		exit;
	}

	public function clearremindAction()
	{
		$uid = $this->check();
		Hapyfish2_Island_Cache_Remind::flush($uid);
		echo 'OK';
		exit;
	}

	public function addinviteAction()
	{
		$uid = $this->check();
		$fid = $this->_request->getParam('fid');
		if (empty($fid)) {
			echo 'fid can not empty';
			exit;
		}

		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($fid);
		if (!$isAppUser) {
			echo 'fid error, not app user';
			exit;
		}
		Hapyfish2_Island_Bll_InviteLog::add($uid, $fid);
		echo 'OK';
		exit;
	}

	public function loginactiveAction()
	{
		$uid = $this->check();
		$starDays = (int)$this->_request->getParam('starDays', 1);
		$days = (int)$this->_request->getParam('days', 1);
		$loginCount = (int)$this->_request->getParam('loginCount', 1);
		$loginInfo = array(
			'last_login_time' => time() - 86400,
			'active_login_count' => $days,
			'max_active_login_count' => 5,
			'today_login_count' => 0,
			'all_login_count' => $loginCount,
			'star_login_count' => $starDays
		);
		Hapyfish2_Island_HFC_User::updateUserLoginInfo($uid, $loginInfo, true);

		$key = 'i:u:ezinecount:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $data[2] = 0;
        $cache->set($key, $data, 864000);

		echo 'OK';
		exit;
	}

	public function clearezAction()
	{
		$uid = $this->check();
		$key = 'i:u:ezinecount:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo 'OK';
		exit;
	}

	public function cleardlyawardAction()
	{
		//var_dump(1);
		$uid = $this->check();
		$mckey = Hapyfish2_Island_Bll_DailyAward::$_mcKeyPrex . $uid;
	    $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($mckey);
		echo 'OK';
		exit;
	}
	public function updatetaskAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		//get user achievement info
        $userAchievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
        echo json_encode($userAchievement);
		$taskType = $this->_request->getParam('taskType');
		$num = $this->_request->getParam('num');
		$taskType = 'num_' . $taskType;
		Hapyfish2_Island_HFC_Achievement::updateUserAchievementByFieldData($uid, $taskType, $num);

        $dalTask = Hapyfish2_Island_Dal_Task::getDefaultInstance();
        $dalTask->clear($uid);

	    $cache = Hapyfish2_Cache_Factory::getMC($uid);
	    $key = 'i:u:alltask:' . $uid;
		$cache->delete($key);

		$titleInfo = array('title' => 0, 'title_list' => '');
        Hapyfish2_Island_HFC_User::updateUserTitle($uid, $titleInfo);

        Hapyfish2_Island_Cache_Task::updateUserOpenTask(uid, array());
        $keyOpen = 'i:u:openTask2:' . $uid;
        $cache->delete($keyOpen);

		echo 'OK';
		exit;
	}

	public function clearlogininfoAction()
	{
	 $uid = $this->check();
	 $key = 'i:u:login:' . $uid;
     $cache = Hapyfish2_Cache_Factory::getHFC($uid);
	 $data = $cache->delete($key);
	 echo $uid.'--OK';
	 exit;
	}

	public function loginactivenewsAction()
	{
		$uid = $this->check();
		$starDays = (int)$this->_request->getParam('starDays', 1);
		$days = (int)$this->_request->getParam('days', 1);
		$loginCount = (int)$this->_request->getParam('loginCount', 1);
		$loginInfo = array(
			'last_login_time' => time() - 86400,
			'active_login_count' => $days,
			'max_active_login_count' => 5,
			'today_login_count' => 0,
			'all_login_count' => $loginCount,
			'star_login_count' => $starDays
		);
		Hapyfish2_Island_HFC_User::updateUserLoginInfo($uid, $loginInfo, true);
		echo 'OK';
		exit;
	}

	public function addboatAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		$positionId = $this->_request->getParam('pid');

		if ( !in_array($positionId, array(4,5,6,7,8)) ) {
			echo 'False';
			exit;
		}

		Hapyfish2_Island_HFC_Dock::expandPosition($uid, $positionId, 10);

		echo 'OK';
		exit;
	}

	public function updatetimegiftAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		$step = $this->_request->getParam('step');

		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		$val['state'] = $step;
		$val['time_at'] = time();
		$cache->set($key, $val, 100000);

		echo 'OK';
		exit;
	}

	public function updatebiggiftlevelAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');

		Hapyfish2_Island_Cache_User::updateUserNextBigGiftLevel($uid, 5);
		echo "OK";
		exit;
	}

	public function getusercacheAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');

		$mckey = Hapyfish2_Island_Bll_DailyAward::$_mcKeyPrex . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $dailyReward = $cache->get($mckey);

		print_r('<pre>');print_r($dailyReward);print_r('</pre>');

		echo 'OK';
		exit;
	}
	
	public function addcollectionAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		$cid = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
		$clear = $this->_request->getParam('clear', null);
		
		if ( $clear ) {
			Hapyfish2_Island_Cache_SuperVisitor::updateUsercollection($uid, array(), true);
		}
		else {
			Hapyfish2_Island_Cache_SuperVisitor::addUserCollection($uid, $cid, $num);
		}
    	echo "ok";
    	exit;
    	
	}

	public function getsvnumAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
    	$result = Hapyfish2_Island_Cache_SuperVisitor::getTodayRemainSvNum($uid);
    	echo $result;
    	echo '<br/>';
    	echo "ok";
    	exit;
	}
	
	public function updatesvnumAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		$num = $this->_request->getParam('num');
    	Hapyfish2_Island_Cache_SuperVisitor::updateTodayRemainSvNum($uid, $num);
    	echo "ok";
    	exit;
	}

	public function gettodaycollectionAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		
    	$result = Hapyfish2_Island_Cache_SuperVisitor::getUserTodayCollection($uid, array());
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($result);
    	exit;
	}
	public function cleartodaycollectionAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		
    	Hapyfish2_Island_Cache_SuperVisitor::updateUserTodayCollection($uid, array());
    	echo "ok";
    	exit;
	}
	public function getcollectionrandAction()
	{
        $uid = $this->check();
		$list = Hapyfish2_Island_Cache_BasicInfo::getCollectionRandArray();
		$rand = array_rand($list);
		echo json_encode($list);
    	exit;
	}
	
	public function clearfansaistAction()
	{
		$key = 'i:u:e:f:g:l';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		$info = $cache->get($key);
		print"<pre>";
		print_r($info);
		print"</pre>";
		exit;
	}
	
	public function clearuserfansAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:e:f:g:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($key);
        $dal = Hapyfish2_Island_Event_Dal_fansGift::getDefaultInstance();
        $dal->delete($uid);
        echo "OK";
        exit;
	}
	
	public function clearawardconfigAction()
	{
		$key = 'i:award:config';
    	$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
    	$config = $cache->delete($key);
    	echo "ok";
    	exit;
	}
	
	public function addqixititleAction()
	{
		$uids = array(74680,1164,297143,1140,36805,22122,128040,19603,232853,459111,141957,195123,149461,505235,380441,227415,1582,4772,336112,311947,27973,387784,104437,173485,233883,1663,256050,123360,189431,173210,307042,70923,150023,276662,1271,226550,9447,86354,16910,6415,9365,61811,4284,3993,243081,22365,546480,46443,302323,310722,288121,317913,348967,116653,262001,224764,225805,225510,451493,296434,348250,294742,49240,231092,9942,500454,170080,6237,118407,19161,145253,218312,312485,247874,22924,270362,224365,320157,455752,321130,1173,449652,91350,286950,2394,13573,64873,506497,116202,255995,342932,89543,277375,95431,102404,251065,327242,409190,107170,180131);
		$titleId = 99;
		
		$nowTime = time();
		
		$feed = '恭喜你获得七夕称号<font color="#FF0000"> 鹊桥之恋</font>';
		
		foreach ($uids as $uid) {
			Hapyfish2_Island_HFC_User::gainTitle($uid, $titleId, true);
			
        	$minifeed = array(
						'uid' => $uid,
						'template_id' => 0,
						'actor' => $uid,
						'target' => $uid,
						'title' => array('title' => $feed),
						'type' => 3,
						'create_time' => $nowTime
					);

			Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
		}
		
		echo 'OK';
		exit;
	}

	public function clearinviteAction()
	{
		$uid = $this->_request->getParam('uid');
		$id = $this->_request->getParam('id');
		
		$key = 'i:e:invite:status:' . $id . ':' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	public function clearinvitesessionAction()
	{
		$uid = $this->_request->getParam('uid');
		$tid = $this->_request->getParam('tid');
		$id = $this->_request->getParam('id');
		
		$pkey = 'i:e:session:key:' . $uid;
		$mcache = Hapyfish2_Cache_Factory::getMC($uid);
		$mcache->delete($pkey);
		
		echo 'OK';
		exit;
	}
	
	public function getsessionAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$pkey = 'i:e:session:key:' . $uid;
		$mcache = Hapyfish2_Cache_Factory::getMC($uid);		
		$resultData = $mcache->get($pkey);
		
		print_r('<pre>');print_r($resultData);print_r('</pre>');
		echo 'OK';
		exit;
	}
	
	public function chengesessionAction()
	{
		$uid = $this->_request->getParam('uid');
		$num = $this->_request->getParam('num', 0);
		
		$new = array('id' => 10049, 
				'name' => '乐鱼活动测试一', 
				'prize' => array(
						array('id' => 1, 
							'name' => '金币', 
							'target_value' => 5, 
							'current_value' => $num),
						 array('id' => 2, 
					 		'name' => '卡', 
					 		'target_value' => 15,
						 	'current_value' => $num), 
						 array('id' => 3, 
						 	'name' => '岛皮', 
						 	'target_value' => 30, 
						 	'current_value' => $num)));
						 
		$pkey = 'i:e:session:key:' . $uid;
		$mcache = Hapyfish2_Cache_Factory::getMC($uid);
		$mcache->set($pkey, $new);

		echo 'OK';
		exit;
	}
	
	public function clearoneteambuyAction()
	{
		$uid = $this->_request->getParam('uid');
		
//		$dalTeamBuy = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
//		$dalTeamBuy->clearOneUser($uid);
		
    	$keys = 'i:e:teambuy:buygood:' . $uid;
		$caches = Hapyfish2_Cache_Factory::getMC($uid);
		$caches->delete($keys);
		
		echo $uid;
		exit;
	}
	
	public function gettodaysvinfoAction()
	{
        $uid = $this->check();
		$todaySvInfo = Hapyfish2_Island_Cache_SuperVisitor::getTodayAllUserSvInfo();
		echo json_encode($todaySvInfo);
		exit;
	}
	
	public function updatetodaysvinfoAction()
	{
        $uid = $this->check();
        $cid_13 = $this->_request->getParam('gid_13');
        $cid_20 = $this->_request->getParam('gid_20');
        $clear = $this->_request->getParam('clear');
        
        $todaySvInfo = Hapyfish2_Island_Cache_SuperVisitor::getTodayAllUserSvInfo();
        
        if ( $cid_13 ) {
        	$todaySvInfo['gid_13'] = $cid_13;
        }
        if ( $cid_20 ) {
            $todaySvInfo['gid_20'] = $cid_20;
        }
        $todaySvInfo['time'] = time();
        
        if ( $clear == 1 ) {
        	$todaySvInfo = array('time' => 0);
        }
        
        Hapyfish2_Island_Cache_SuperVisitor::updateTodayAllUserSvInfo($todaySvInfo);
        
        echo json_encode($todaySvInfo);
        exit;
	}
	
    public function teambuytestAction()
    {
        $uid = $this->_request->getParam('uid');
        $state = $this->_request->getParam('state');
        $new = $this->_request->getParam('new', 0);
        
        if ( $new == 1 ) {
            try {
               $dalDB = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
               $stateTest = $dalDB->getJoinTeamBuyInfo($uid);
               if ( $stateTest != $state ) {
                    $tempUser = $dalDB->selectTeamBuyUserTemp($uid);
                    if ( !$tempUser ) {
                        $dalDB->insertTeamBuyUserTemp($uid, $state);
                    }
               }
            } catch (Exception $e) {
            }
        }
                
        //$result = Hapyfish2_Island_Event_Bll_TeamBuy::buyGoodsTest($uid);

        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode('ok');
        exit;
    }
    
    //删除接待游客数
    public function clearvisitnumAction()
    {
    	$uid = $this->_request->getParam('uid');
    	
    	$key = 'i:u:visitor:num:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo "ok";
		exit;
    }
    
    //减少用户宝石
	public function decusergoldAction()
	{
		$uid = $this->_request->getParam('uid');
		$decGold = $this->_request->getParam('gold');
		
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		if ($userGold < $decGold) {
			$decGold = $userGold;
		}
		
		$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
		$ok = $dalUser->decGold($uid, $decGold);
		
		Hapyfish2_Island_HFC_User::reloadUserGold($uid);
		
		$result = $ok ? 'OK' : 'not';
		echo $result;
		exit;
	}
    
	//设置婚纱店购买人数
	public function setbuynumAction()
	{
		$num = $this->_request->getParam('num');
		
		$endTime = strtotime('2011-11-15 23:59:59');
		$key = 'ev:BlackDay:buyNum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->set($key, $num, $endTime);
		
		echo 'OK';
		exit;
	}
	
	//清除赠送好友婚纱店的记录
	public function clearfidsAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
		
		foreach ($fids as $fid) {
			$key = 'ev:BlackDay:to:' . $fid;
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$cache->delete($key);
		}
		
		echo 'OK';
		exit;
	}

	public function addplantAction()
	{
		$uid = $this->_request->getParam('uid');
		$itemId = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
		$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
		$bllCompensation->setItem($itemId, $num);
		$bllCompensation->sendOne($uid, $itemId);	
		echo 'OK';
		exit;	
	}

	public function clearchmasdayAction()
	{
		$uid = $this->_request->getParam('uid');
		$taskId = $this->_request->getParam('cid');
		
		$key = 'ev:chrismas:getgift:flag:' . $taskId . '1225123' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	public function addgiftAction()
	{
		$uid = $this->_request->getParam('uid');
		$itemId = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
		
		$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $itemId, $num);
		
		echo $ok;
		exit;
	}
	
}