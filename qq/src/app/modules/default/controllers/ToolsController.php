<?php

class ToolsController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	function check()
	{
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
        if ( STATIC_HOST == 'http://imgcache.qzoneapp.com/island' ) {
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
		$platformUser = Hapyfish2_Platform_Bll_Factory::getUser($uid);
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
		$uid = $this->check();
		$mckey = Hapyfish2_Island_Bll_DailyAward::$_mcKeyPrex . $uid;
	    $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($mckey);
		echo 'OK';
		exit;
	}

	public function clearvipgiftAction()
	{
		$uid = $this->_request->getParam('uid');
		$hasGetVipGift = Hapyfish2_Island_Cache_User::setVipGift($uid, 'N');
		echo 'OK';
		exit;
	}

	/***********************/
    function clearluckycacheAction()
    {
    	$tid = $this->_request->getParam('tid');

    	if($tid == 1) {
			$dal = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
    		$dal->deleteDW();
    	} else {
			$key = 'IpadCollect';
	    	$cache = Hapyfish2_Cache_LocalCache::getInstance();
	    	$cache->delete($key);
    	}

    	echo 'OK';
    	exit;
    }

    function clearhasktvAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$tid = $this->_request->getParam('tid');

    	if($tid == 1) {
			$dal = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
    		$dal->delete($uid);
    	} else if($tid == 2){
			$dal = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
    		$dal->delete($uid);

			$key = 'hasGetLuckyGift_' . $uid;
	    	$cache = Hapyfish2_Cache_LocalCache::getInstance();
	    	$cache->delete($key);
    	} else {
			$key = 'hasGetLuckyGift_' . $uid;
	    	$cache = Hapyfish2_Cache_LocalCache::getInstance();
	    	$cache->delete($key);
    	}

    	echo 'OK';
    	exit;
    }

    function clearcdkeyAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$tid = $this->_request->getParam('tid');

    	if($tid == 1) {
	    	$key = 'hasCDKey_' . $uid;
	    	$cache = Hapyfish2_Cache_LocalCache::getInstance();
	    	$cache->delete($key);
    	} else if($tid == 2) {
			$dal = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
    		$dal->deleteCDK($uid);

			$key = 'hasCDKey_' . $uid;
	    	$cache = Hapyfish2_Cache_LocalCache::getInstance();
	    	$cache->delete($key);
    	} else {
			$dal = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
    		$dal->deleteCDK($uid);
    	}

    	echo 'OK';
    	exit;
    }

	function clearteambuyAction()
    {
    	$key = 'TeamBuyInfo';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);

		echo 'OK';
		exit;
    }

    function clearteambuygoodAction()
    {
    	$tid = $this->_request->getParam('tid');

    	$dalTeamBuy = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
    	$users = $dalTeamBuy->getHasJoinTeamBuyUser();

    	if($users) {
    		if($tid == 1) {
		    	foreach ($users as $user) {
		    		foreach ($user as $uid) {
				    	$key = 'BuyGoods_' . $uid;
						$cache = Hapyfish2_Cache_Factory::getMC($uid);
						$cache->delete($key);
		    		}
		    	}
		    	echo 'OK';
    		} else if($tid == 2){
				$dalTeamBuyUser = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
				$dalTeamBuyUser->clearTeamBuyUser();
    		} else if($tid == 3) {
		    	foreach ($users as $user) {
		    		foreach ($user as $uid) {
				    	$key = 'BuyGoods_' . $uid;
						$cache = Hapyfish2_Cache_Factory::getMC($uid);
						$cache->delete($key);
		    		}
		    	}

				$dalTeamBuyUser = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
				$dalTeamBuyUser->clearTeamBuyUser();
		    	echo 'OK';
    		}
    	}
		else {
			echo 'NULL';
		}

		exit;
    }

	function clearnewislandAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$islandInfo = Hapyfish2_Island_HFC_User::getUserIsland($uid);
    	$islandInfo['unlock_island'] = '1';
    	$islandInfo['current_island'] = 1;
    	Hapyfish2_Island_HFC_User::updateFieldUserIsland($uid, $islandInfo);

    	$userVo = Hapyfish2_Island_HFC_User::getUserVO($uid);
        $userLevelInfo = array('level' => $userVo['level'],
							   'island_level' => $userVo['island_level'],
							   'island_level_2' => 0,
							   'island_level_3' => 0,
							   'island_level_4' => 0);
        Hapyfish2_Island_HFC_User::updateUserLevel($uid, $userLevelInfo);

        //$dalBackground = Hapyfish2_Island_Dal_Background::getDefaultInstance();
        //$dalBackground->clear($uid);
        $dalBuilding = Hapyfish2_Island_Dal_Building::getDefaultInstance();
        $dalBuilding->clearNewIsland($uid);
        $dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
        $dalPlant->clearNewIsland($uid);

		$cache = Hapyfish2_Cache_Factory::getMC($uid);

        $key1 = 'island:allplantonisland:' . $uid . ':' . '2';
		$cache->delete($key1);
        $key2 = 'island:allplantonisland:' . $uid . ':' . '3';
		$cache->delete($key2);
        $key3 = 'island:allplantonisland:' . $uid . ':' . '4';
		$cache->delete($key3);

		$key4 = 'i:u:bldids:onisl:' . $uid . ':' . '2';
		$cache->delete($key4);
		$key5 = 'i:u:bldids:onisl:' . $uid . ':' . '3';
		$cache->delete($key5);
		$key6 = 'i:u:bldids:onisl:' . $uid . ':' . '4';
		$cache->delete($key6);

		$key7 = 'i:u:pltids:onisla:' . $uid . ':' . '2';
		$cache->delete($key7);
		$key8 = 'i:u:pltids:onisla:' . $uid . ':' . '3';
		$cache->delete($key8);
		$key9 = 'i:u:pltids:onisla:' . $uid . ':' . '4';
		$cache->delete($key9);

		$key10 = 'i:u:isfstin:' . $uid . ':' . '2';
		$cache->delete($key10);
		$key11 = 'i:u:isfstin:' . $uid . ':' . '3';
		$cache->delete($key11);
		$key12 = 'i:u:isfstin:' . $uid . ':' . '4';
		$cache->delete($key12);

		echo 'OK';
		exit;
    }

	public function updatetaskAction()
	{
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

	public function updatetimegiftAction()
	{
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

    function deletestarfishAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$num = $this->_request->getParam('num');
    	Hapyfish2_Island_Bll_StarFish::consume($uid, $num, '');
    	echo $uid.'---'.$num.'----OK';
    }

    function deleteusercardAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$num = $this->_request->getParam('num');
    	$cid = $this->_request->getParam('cid');
    	Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, $num);
    	echo "ok";
    	exit;
    }

/***************************** 分割线 ************************************/
	public function addqixititleAction()
	{
		$uids = array(3785899,167389,3272359,3568982,148827,2452642,3194331,231074,2694755,5869379,31955,4417329,18905,1796987,4593,876569,236689,1087939,281985,3998897,541111,536464,6191,920504,4749702,154254,2150130,194887,53335,2660184,4721810,133294,1382092,1460879,503479,2178,131300,2090366,3224131,2585442,19656,3753,1524397,965556,257723,2497610,3512376,4790931,5557231,4391847,202186,343554,4344522,1276698,2230948,2226342,759340,18232,81266,4437796,4304240,4273219,1394272,3408,2836508,9925,3834677,90505,10433,92915,1463916,1331266,5626268,2439,116026,3953140,287557,52365,5248622,392512,502255,4336354,4300161,42234,1176167,4117950,32081,1052393,3877825,1784145,3066481,3822307,3774124,2596968,10855,1651521,1193939,2040234,3798297,2425944);
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

    public function clear1Action()
    {
    	$uid = $this->_request->getParam('uid');

    	$keyOB = 'i:e:panicbuy:qishu:' . $uid;
    	$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
    	$cacheUser->delete($keyOB);

    	echo 'OK';
    	exit;
    }

    public function clear2Action()
    {
    	$uid = $this->_request->getParam('uid');

		$keybuyCount = 'i:e:panicbuy:count:' . $uid;
		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
    	$cacheUser->delete($keybuyCount);

    	echo 'OK';
    	exit;
    }

    public function clear3Action()
    {
    	$uid = $this->_request->getParam('uid');

		$keyBox = 'i:e:panicbuy:box:' . $uid;
		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
    	$cacheUser->delete($keyBox);

    	echo 'OK';
    	exit;
    }

    public function clear4Action()
    {
		$uid = $this->_request->getParam('uid');

		$keyUser = 'i:e:panicbuy:sale:' . $uid;
		$cacheUser = Hapyfish2_Cache_Factory::getMC($uid);
    	$cacheUser->delete($keyUser);

    	echo 'OK';
    	exit;
    }

    public function clear5Action()
    {
		$key = 'i:e:panicbuy:alldata';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
    	$cache->delete($key);

    	echo 'OK';
    	exit;
    }

    public function clear6Action()
    {
		$key = 'i:e:panicbuy:now';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
    	$cache->delete($key);

    	echo 'OK';
    	exit;
    }

    public function clear7Action()
    {
    	$uid = $this->_request->getParam('uid');
    	$num = $this->_request->getParam('num');

    	if ($num < 0) {
    		echo 'Error!';
    		exit;
    	}

		$db = Hapyfish2_Island_Event_Dal_PanicBuy::getDefaultInstance();
		$db->updateUserBuyCount($uid, $num);

		$key = 'i:e:panicbuy:count:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->set($key, $num, 3600 * 24 * 15);

    	echo 'OK';
    	exit;
    }

    public function clear8Action()
    {
		$key = 'i:e:panicbuy:num';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
    	$cache->delete($key);

    	echo 'OK';
    	exit;
    }

    public function saveallusercachetestAction()
    {
    	$uid = $this->_request->getParam('uid');
        Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUserTest($uid);

        echo 'OK';
        exit;
    }
    
    public function clearuertitleAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$key = 'i:u:title:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->delete($key);
		echo $uid.'-------OK';
		exit;
    }

	//设置婚纱店购买人数
	public function setbuynumAction()
	{
		$num = $this->_request->getParam('num');
		
		$endTime = strtotime('2011-11-17 23:59:59');
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
    
	//清除连续登陆缓存
	public function clearconfigAction()
	{
		$key = 'i:award:config';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo "OK";
		exit;
	}
    
}