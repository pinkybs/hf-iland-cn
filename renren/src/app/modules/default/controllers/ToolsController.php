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

	/*public function addcoinAction()
	{
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

		echo 'OK';
		exit;
	}

	public function addstarfishAction()
	{
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
	}*/

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
		$levelInfo['island_level'] = Hapyfish2_Island_Cache_BasicInfo::getIslandLevelInfoByUserLevel($level);

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
		var_dump(1);
		$uid = $this->check();
		$mckey = Hapyfish2_Island_Bll_DailyAward::$_mcKeyPrex . $uid;
	    $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($mckey);
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

		echo 'OK';
		exit;
	}

	public function testcardAction()
	{

		/*$result = Hapyfish2_Island_Bll_Card::useCard(1016, 1016, 26841, 1);

		try {
			Hapyfish2_Island_HFC_AchievementDaily::updateUserAchievementDailyByField(1016, 'num_2', 1);

			Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField(1016, 'num_2', 1);
		} catch (Exception $e) {

		}
		//task id 3004,task type 2
		$checkTask = Hapyfish2_Island_Bll_Task::checkTask(1016, 3004);
		if ( $checkTask['status'] == 1 ) {
			$result['finishTaskId'] = $checkTask['finishTaskId'];
		}
		echo json_encode($result);*/
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

	public function clearchangelistAction()
	{
		$key = 'event:pointchalist';
		$EventFeed = Hapyfish2_Cache_Factory::getEventFeed();
		$EventFeed->delete($key);
		echo 'OK';
		exit;
	}

	public function addpointAction()
	{
		$uid = $this->_request->getParam('uid');
		$point = $this->_request->getParam('point');

		$dalCasino = Hapyfish2_Island_Event_Dal_Casino::getDefaultInstance();
		$dalCasino->updateUserPoint($uid, $point);

		$data = $dalCasino->getUserPoint($uid);

		$key = 'i:u:casinop:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);

		$feed = '因系统漏洞，您兑换的物品没有到账，现已补发您消耗掉的积分' . $point;

        $minifeed = array('uid' => $uid,
                          'template_id' => 0,
                          'actor' => $uid,
                          'target' => $uid,
                          'title' => array('title' => $feed),
                          'type' => 6,
                          'create_time' => time());
        Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);

		$total = $data;

		echo $uid . '  '. $total;
		exit;
	}

    public function loadshiplistAction()
    {
    	$list = Hapyfish2_Island_Cache_BasicInfo::loadShipList();
		$key = 'island:shiplist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		echo 'OK';
		exit;
    }

	public function gaintitleAction()
	{
		$uid = $this->_request->getParam('uid');
		$titleId = $this->_request->getParam('tid');

		try {
        	Hapyfish2_Island_HFC_User::gainTitle($uid, $titleId, true);
		} catch (Exception $e) {
			echo 'false';
			exit;
		}
		echo 'ok';
		exit;
	}

	public function addunlockshipAction()
	{
		$tid = $this->_request->getParam('tid');
		$month = $this->_request->getParam('month');

		$result = Hapyfish2_Island_Bll_UnlockshipGold::addunlockship($tid, $month);

		print ($result);
		exit;
	}
	
    function repairplantAction()
    {
    	$cid = $this->_request->getParam('cid');
    	$uid = $this->_request->getParam('uid', 1);
    	Hapyfish2_Island_Tool_Repair::repairUserPlant($cid, $uid);

		echo 'OK';
		exit;
    }

	public function clearleveltaskAction()
	{
		$uid = $this->_request->getParam('uid');
		$level = $this->_request->getParam('level');
		
		//update achievement task,22
        try {
        	Hapyfish2_Island_HFC_Achievement::updateUserAchievementByFieldData($uid, 'num_22', $level);

			//task id 3050,task type 22
			Hapyfish2_Island_Bll_Task::checkTask($uid, 3050);
        } catch (Exception $e) {
        }
        
        echo 'OK';
        exit;
	}
	
	//登陆翻牌查看
	public function getuserdailyawardAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$mckey = Hapyfish2_Island_Bll_DailyAward::$_mcKeyPrex . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $dailyReward = $cache->get($mckey);
		
		print_r('<pre>');print_r($dailyReward);print_r('</pre>');
		
		echo 'OK';
		exit;
	}
    
	//测试充值送
	public function testpaysendAction()
	{
		$uid = $this->_request->getParam('uid');
		$amount = $this->_request->getParam('amount');
		
		if(!in_array($amount, array(10, 20, 50, 100))) {
			echo '输入的数值有误！';
			exit;
		}
		
		$ok = Hapyfish2_Island_Bll_Payment::chargeGift($uid, $amount);
		
		if($ok) {
			echo 'OK';
		} else {
			echo 'NOT';
		}
		
		exit;
	}
	
	//发论坛管理员工资
	public function tosendadminAction()
	{
		$tid = $this->_request->getParam('tid');
		
		$puids[60] = array(252771475,227804193,321140683);
		$puids[30] = array(268686485,293531341,249425183,282717471);
		
		$nowTime = time();
		$feed = '恭喜你获得论坛管理员工资<font color="#379636">' . $tid . '</font>宝石';
	
		foreach ($puids[$tid] as $puid) {
			$puidMS = Hapyfish2_Platform_Bll_UidMap::getUser($puid);
		
			$goldInfo = array('gold' => $tid, 'type' => 0, 'time' => $nowTime);
			
			$ok = Hapyfish2_Island_Bll_Gold::add($puidMS['uid'], $goldInfo);
			
			if($ok) {
	        	$minifeed = array(
							'uid' => $puidMS['uid'],
							'template_id' => 0,
							'actor' => $puidMS['uid'],
							'target' => $puidMS['uid'],
							'title' => array('title' => $feed),
							'type' => 3,
							'create_time' => $nowTime
						);

				Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
				
				echo 'puid : ' . $puid . ' ***  uid : ' . $puidMS['uid'];
			} else {
				echo 'not puid : ' . $puid;
			}
		}
		
		exit;
	}
	
	public function clearcollectAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'normal_collectgift_haveget_' . $uid;
		
		$db = Hapyfish2_Island_Event_Dal_Hash::getDefaultInstance();
		$ok = $db->delete($key);
		
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
    function cleartitlecacheAction()
    {
    	$uid = $this->_request->getParam('uid');

    	$key = 'i:u:ach:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->delete($key);

        echo 'OK';
        exit;
    }
	
    public function saveallusercacheAction()
    {
        Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUser();

        echo 'OK';
        exit;
    }
    
    public function saveallusercachetestAction()
    {
        Hapyfish2_Island_Tool_SaveOldUserCache::savedbAllUserTest();

        echo 'OK';
        exit;
    }
    function sendboxgiftAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$taskId = $this->_request->getParam('taskId');

		Hapyfish2_Island_Bll_Task::checkTask($uid, $taskId);

		echo 'OK';
    	exit;
    }
    function updateusertitleAction()
    {
    	$uid = $this->_request->getParam('uid');
        $key = 'i:u:title:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
 	    $dalUserTitle = Hapyfish2_Island_Dal_UserTitle::getDefaultInstance();
	    $data = $dalUserTitle->get($uid);
	    print_r($data);
	    $cache->save($key, $data); 
	    echo 'OK';    
    	exit;
    }  

    public function savecacheAction()
    {
    	$dbId = $this->_request->getParam('db');
    	$tableId = $this->_request->getParam('table');
    	
		Hapyfish2_Island_Tool_Savecache::saveAllUserCacheByDB($dbId, $tableId);
		echo "OK ";
		exit;
    }
    
	public function cleardiyAction()
	{
		$uid = $this->_request->getParam('uid');
		for($i=1;$i<=2;$i++){
			Hapyfish2_Island_Bll_Card::clearDiy($uid, $i);
		}
		echo "ok";
		exit;
	}
}