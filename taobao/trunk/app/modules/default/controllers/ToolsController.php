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
        if ( STATIC_HOST == 'http://tbstatic.hapyfish.com' ) {
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
        //$this->checkistest();
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
		$uid = $this->_request->getParam('uid');
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

        Hapyfish2_Island_Cache_Task::updateUserOpenTask(uid, array());
        $keyOpen = 'i:u:openTask2:' . $uid;
        $cache->delete($keyOpen);

		echo 'OK';
		exit;
	}

    public function updatetasknumAction()
    {
        $uid = $this->_request->getParam('uid');
        //get user achievement info
        $userAchievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
        echo json_encode($userAchievement);
        $taskType = $this->_request->getParam('taskType');
        $num = $this->_request->getParam('num');
        $taskType = 'num_' . $taskType;
        echo '<br/>';
        echo $taskType.':'.$num;
        echo '<br/>';
        Hapyfish2_Island_HFC_Achievement::updateUserAchievementByFieldData($uid, $taskType, $num);
        
        $achievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
        if ($achievement) {
        	
        echo '1';
        echo '<br/>';
            //if (isset($achievement[$taskType])) {
                $achievement[$taskType] = $num;
            echo json_encode($achievement);
            echo '<br/>';
            
                Hapyfish2_Island_HFC_Achievement::saveUserAchievement($uid, $achievement);
            //}
        }
        
        
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

	//补发宝箱大转盘积分
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

		$feed = '补发积分' . $point;

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

	//手动插入称号
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

    public function loadshiplistAction()
    {
    	$list = Hapyfish2_Island_Cache_BasicInfo::loadShipList();
		$key = 'island:shiplist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false);

		echo 'OK';
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

    function savediyAction()
    {
    	$dbId = $this->_request->getParam('dbid');
    	Hapyfish2_Island_Tool_Savediy::savedbAllUser($dbId);

		echo 'OK';
		exit;
    }

    //修复建筑信息(解决建设任务不能完成)
    function repairplantAction()
    {
    	$cid = $this->_request->getParam('cid');
    	$uid = $this->_request->getParam('uid', 1);
    	Hapyfish2_Island_Tool_Repair::repairUserPlant($cid, $uid);

		echo 'OK';
		exit;
    }

    //修复玩家数据
    function repairuserAction()
    {
    	$uid = $this->_request->getParam('uid');
    	Hapyfish2_Island_Tool_Repair::repairUserInfo($uid);

		echo 'OK';
		exit;
    }

    //手动触发玩家成就任务
    function checktaskAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$taskId = $this->_request->getParam('taskId');

		Hapyfish2_Island_Bll_Task::checkTask($uid, $taskId);

		echo 'OK';
    	exit;
    }

    //清理玩家成就缓存
    function cleartitlecacheAction()
    {
    	$uid = $this->_request->getParam('uid');

    	$key = 'i:u:ach:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->delete($key);

        echo 'OK';
        exit;
    }

    function clearuserxmasAction()
    {
    	$uid = $this->_request->getParam('uid');

    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$mkeyUid = 'event_xmas_fair_daily_' . $uid;
		$cache->set($mkeyUid, false);

		echo 'OK';
		exit;
    }

    function clearxmasinfoAction()
    {
    	$mkey = 'event_xmas_fair';
		$cacheInfo = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cacheInfo->delete($mkey);

		echo 'OK';
		exit;
    }
    
    public function testcatchAction()
    {
        $uid = $this->_request->getParam('uid');
        $productid = (int)$this->_request->getParam('id');
        $type = (int)$this->_request->getParam('type');
        
        $result = Hapyfish2_Island_Bll_Test::catchFish($uid, $productid, $type);
        echo json_encode($result);
        
        exit;
    }
    
    
	public function addboatAction()
	{
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
	
	//扣除用户卡片
	public function clearusercardAction()
	{
		$uid = $this->_request->getParam('uid');
		$cid = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
		Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, $num);
		echo "OK";
		exit;
	}

	public function repairpraiseAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$keys = array(
			'i:u:exp:' . $uid,
			'i:u:coin:' . $uid,
			'i:u:gold:' . $uid,
			'i:u:level:' . $uid,
			'i:u:island:' . $uid,
			'i:u:title:' . $uid,
			'i:u:cardstatus:' . $uid
		);

		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		
		$dalUserIsland = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
		$userIsland = $dalUserIsland->get($uid);
		
		$cache->save($keys[4], $userIsland);
		
		echo 'OK';
		exit;
	}
	
	public function updatebiggiftlevelAction()
	{
		$uid = $this->_request->getParam('uid');
		
		Hapyfish2_Island_Cache_User::updateUserNextBigGiftLevel($uid, 5);
		echo "OK";
		exit;
	}
	
	//获得用户连续登录翻拍缓存
	public function getusercacheAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$mckey = Hapyfish2_Island_Bll_DailyAward::$_mcKeyPrex . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $dailyReward = $cache->get($mckey);
		
		print_r('<pre>');print_r($dailyReward);print_r('</pre>');
		
		echo 'OK';
		exit;
	}
	
	public function getuserplantinfoAction()
	{
		$ownerUid = $this->_request->getParam('uid');
		$itemId = $this->_request->getParam('itemId');
		$islandId = $this->_request->getParam('islandId');
		
		$userPlant = Hapyfish2_Island_HFC_Plant::getOne($ownerUid, $itemId, 1, $islandId);
		
		print_r('<pre>');print_r($userPlant);print_r('</pre>');
		exit;
	}
	
	public function loadlotterylistAction()
	{
		Hapyfish2_Island_Cache_LotteryItemOdds::loadLotteryItemOddsList(1);
        echo 'ok';
        exit;
	}

	public function loadlocallotterylistAction()
	{
		$key = 'island:lotteryitemodds:1';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$cache = Hapyfish2_Island_Cache_LotteryItemOdds::getBasicMC();
		$list = $cache->get($key);
		$localcache->set($key, $list);
        echo SERVER_ID . 'ok';
        exit;
	}

	public function getlocallotterylistAction()
	{
		$key = 'island:lotteryitemodds:1';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		print_r($list);
		exit;
	}
	
	//充值送测试
	public function testpayAction()
	{
		$uid = $this->_request->getParam('uid');
		$amount = $this->_request->getParam('amount');
		
		$ok = Hapyfish2_Island_Bll_Payment::chargeGift($uid, $amount, 0);
		Hapyfish2_Island_Cache_Fish::updateUnlock5($uid);
		
		echo $ok ? 'OK' : 'NOT';
		exit;
	}

	//一元店充值信息
	public function testpayoneAction()
	{
		$uid = $this->_request->getParam('uid');
		
		Hapyfish2_Island_Event_Bll_OneGoldShop::setPayInfo($uid);
		
		echo 'OK';
		exit;
	}
	
	//一元店清理用户本期领取奖励记录
	public function clearuseroneAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$key = 'i:u:oneshop:gift:get_status:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}

	//一元店清理所以物品缓存
	public function clearallAction()
	{
		$key = 'i:e:onegold:all';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo "ok";
		exit;
	}
	
	//一元店清理本期礼物缓存
	public function clearonegoldAction()
	{
		$key = 'i:e:oneshop:gift';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	//一元店清理时间
	public function clearonegolduserAction()
	{
		$key = 'i:e:oneshop:gift:newtime';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->set($key, null);
		
		echo 'OK';
		exit;
	}
	
	//一元店清理数量
	public function clearnumAction()
	{
		$keyCid = 'i:e:oneshop:gift:hasnum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($keyCid);
		
		echo 'OK';
		exit;
	}
/**一元店物品显示不出时需清理的缓存*/	
	
	//团购玩家不显示icon
	public function clearoneteambuyAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$dalTeamBuy = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
		$dalTeamBuy->clearOneUser($uid);
		
    	$keys = 'i:e:teambuy:buygood:' . $uid;
		$caches = Hapyfish2_Cache_Factory::getMC($uid);
		$caches->delete($keys);
		
		echo $uid;
		exit;
	}
	
	public function checknewpaylevelAction()
	{
		$itd = $this->_request->getParam('tid');
		
		$uids[1] = array();
		$uids[2] = array();
		$uids[3] = array();
		$uids[4] = array();
		
		$new = array();
		foreach ($uids[$itd] as $uid) {
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$new[$uid] = $userLevelInfo['level'];
		}
		
		print_r('<pre>');print_r($new);print_r('</pre>');
		exit;
	}
	//捕鱼 清除鱼信息列表
	public function clearfishAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$key = 'i:e:l:p:flist:' . 1 ;
		$cache->delete($key);
		$key = 'i:e:l:p:flist:' . 2 ;
		$cache->delete($key);
		$key = 'i:e:l:p:flist:' . 3 ;
		$cache->delete($key);
		$key = 'i:e:l:p:flist:' . 4 ;
		$cache->delete($key);
		$key = 'i:e:l:p:flist:' . 5 ;
		$cache->delete($key);							
		echo 'ok';
		exit;		
	}
	
	public function clearfishallAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$key = 'i:e:flistall';
		$cache->delete($key);							
		echo 'ok';
		exit;		
	}	
	
	//捕鱼 清除鱼信息详细
	public function clearfishinfoAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		for($i=1;$i<=100;$i++) {
		$key = 'i:e:l:p:finfo:' . $i ;
		$cache->delete($key);	
		}					
		echo 'ok';
		exit;		
	}
	
	//捕鱼 清除商品信息
	public function clearproductAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$key = 'i:e:tb:pd';
		$cache->delete($key);						
		echo 'ok';
		exit;		
	}
	
	//捕鱼 清除领域信息
	public function cleardomainAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$key = 'i:e:l:p:fdomain';
		$cache->delete($key);						
		echo 'ok';
		exit;		
	}
	
	public function clearproductproAction()
	{
		$productid = $this->_request->getParam('pid');
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		for($i=1;$i<=5;$i++) {
			$key = 'i:e:tb:pd:prob:l:pid:' . $i . ':' . $productid;
			$cache->delete($key);
		}
		echo 'ok';
		exit;	
	}
	
	//捕鱼达人称号测试
	public function updateusertitleAction()
	{
		$flag = $this->_request->getParam('flag');
		$uids = array(6497758);
		$titleId = 100;
		
		$nowTime = time();
		if($flag==1) {
			$feed = '恭喜你获得称号<font color="#FF0000"> 捕鱼达人</font>';
		}elseif($flag==2) {
			$feed = '取消称号<font color="#FF0000"> 捕鱼达人</font>';
		}
		foreach ($uids as $uid) {
			if($flag==1) {
				Hapyfish2_Island_HFC_User::gainTitle($uid, $titleId, true);
			}elseif($flag==2) {
				Hapyfish2_Island_HFC_User::delTitle($uid, $titleId, true);
			}
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
	
	//扣除宝石
	public function decusergoldAction()
	{
		$uid = $this->_request->getParam('uid');
		$decGold = $this->_request->getParam('gold');
		
		$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		
		if ($decGold > $userGold) {
			$decGold = $userGold;
		}
		
		//获取用户等级
		$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		$userLevel = $userLevelInfo['level'];
		
		//扣除宝石
		$goldInfo = array('uid' => $uid,
						'cost' => $decGold,
						'summary' => '',
						'user_level' => $userLevel,
						'create_time' => time(),
						'cid' => '',
						'num' => 0);

        $ok = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		
		info_log($uid . ' -> ' . $decGold . ' | ' . $userGold, 'DecGold');
		
		$result = $ok ? 'OK' : 'not';
		echo $result;
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
	
	//清除接待游客数
    public function clearvisitnumAction()
    {
    	$uid = $this->_request->getParam('uid');
    	
    	$key = 'i:u:visitor:num:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo "ok";
		exit;
    }
	
	public function resetoneboxinfoAction()
	{
		$uid = $this->_request->getParam('uid');

		$newkey = 'i:e:oneshop:box:has:' . $uid;
		$keyBox = 'i:e:oneshop:gift:bigbox:' . $uid;
		$cacheBox = Hapyfish2_Cache_Factory::getMC($uid);
		$cacheBox->delete($keyBox);
		$cacheBox->delete($newkey);
		
		echo 'OK';
		exit;
	}
	
	public function getjointeambuyflagAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$mkey = 'i:e:teambuy:buygood:' . $uid;
		$mcache = Hapyfish2_Cache_Factory::getMC($uid);
		$state = $mcache->get($mkey);
		
		echo $state;
		exit;
	}
	
	public function clearchrismasAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		
		$key = 'ev:chrismas:first:' . $uid;
		$key1 = 'ev:chrismas:collect:' . $uid;
		$key2 = 'ev:chrismas:fawn:flag:' . $uid;
		$key2 = 'ev:chrismas:ice:flag:' . $uid;
		$cache->delete($key);
		$cache->delete($key1);
		$cache->delete($key2);
		$cache->delete($key2);
		
		$taskIds = array(4, 5, 6);
		foreach ($taskIds as $taskId) {
			$key4 = 'ev:chrismas:getgift:flag:' . $taskId . '1225123' . $uid;
			$cache->delete($key4);
		}
		
		$db = Hapyfish2_Island_Event_Dal_Christmas::getDefaultInstance();
		$db->clearGetFlag($uid);
		
		echo 'OK';
		exit;
	}
	
    public function clearresolveAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$key = 'i:b:c:bm:r'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$userR = $cache->get($key);
    	$userR['num'] = 0;
    	$cache->set($key, $userR);
    	echo"OK";
    	exit;
    }
    public function addmaterialAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$cid = $this->_request->getParam('cid');
    	$num = $this->_request->getParam('num');
    	Hapyfish2_Island_Bll_Compound::addMaterial($uid, $cid, $num);
    	echo"OK";
    	exit;
    }
    
    public function clearusermaterialAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$key = 'i:u:c:bm:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo "ok";
		exit;
    }
    public function clearusernumAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$key = 'i:b:c:bm:r'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$userR = $cache->get($key);
    	$userR['num'] = 0;
    	$cache->set($key, $userR);
    	$userR = $cache->get($key);
    	print_r($userR);
    	echo "ok";
    	exit;
    	
    }
	public function sendgiftAction()
	{
		$uid = $this->_request->getParam('uid');
		$cid = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
	
		$ok = Hapyfish2_Island_Bll_GiftPackage::addGift($uid, $cid, $num);
		
		echo $ok;
		exit;
	}
	/*
	public function addusermammonAction()
	{
		$nowTime = time();
		$uid = $this->_request->getParam('uid');
		//持续双倍经验2小时
		$sumkey = 'i:u:mammon:ev:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$sumint = $cache->get($sumkey);

		//if ($sumint === false) {
			$userCardStatus = Hapyfish2_Island_HFC_User::getCardStatus($uid);

			$douExpTime = $userCardStatus['mammon'] - $nowTime;
			
        	if ($douExpTime < 0) {
        		$userCardStatus['mammon'] = $nowTime + 3600 * 2;
        	} else {
        		$userCardStatus['mammon'] += 3600 * 2;
        	}

			Hapyfish2_Island_HFC_User::updateCardStatus($uid, $userCardStatus);

			$cache->set($sumkey, 1);
		//}
		echo 'OK'; 
		exit;		
	}
	public function clearusermammonAction()
	{
		$nowTime = time();
		$uid = $this->_request->getParam('uid');
		//持续双倍经验2小时
		$sumkey = 'i:u:mammon:ev:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$sumint = $cache->get($sumkey);

		if ($sumint === false) {
			$userCardStatus = Hapyfish2_Island_HFC_User::getCardStatus($uid);
			$userCardStatus['mammon'] = 0;
			Hapyfish2_Island_HFC_User::updateCardStatus($uid, $userCardStatus);

			$cache->set($sumkey, 1);
		}
		echo 'OK'; 
		exit;		
	}	
	public function clearblackcardAction()
	{
		$nowTime = time();
		$uid = $this->_request->getParam('uid');
    	$key = 'i:u:blackcntdly:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo 'OK'; 
		exit;		
	}
	public function clearusedblackcardAction()
	{
		$nowTime = time();
		$uid = $this->_request->getParam('uid');
    	$key = 'i:u:usedblackcntdly:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo 'OK'; 
		exit;		
	}
	*/	
	public function deccoinAction()
	{
        $this->checkistest();
		$uid = $this->check();
		$coin = $this->_request->getParam('coin');
		if (empty($coin) || $coin <= 0) {
			echo 'add coin error, must > 1';
			exit;
		}

		Hapyfish2_Island_HFC_User::decUserCoin($uid, $coin);

		echo 'OK';
		exit;
	}	
	

	//大转盘缓存清理
	public function clearcasinoAction()
	{
		$key = 'island:caisnoawardtype';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	public function getuserstarAction()
	{
		$userStarsArr = array();
		$uid = $this->_request->getParam('uid');
	    $key = 'i:u:star:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $userStars = $cache->get($key);
		if($userStars['star_list']) {
			$userStarsArr = @explode(",", $userStars['star_list']);
		}        
        print_r($userStarsArr);
        exit;		
	}
	public function loadplantAction()
	{
		$uid = $this->_request->getParam('uid');
		$pid = $this->_request->getParam('pid');
		$dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
		$plant = $dalPlant->getOneNum($uid, $pid);        
        print_r($plant);
        echo 'OK';
        exit;		
	}
	
	//清除元旦概率表缓存
	public function clearnewdaysitemsAction()
	{
        $uid = $this->check();
		$key = 'ev:newdays:items';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	//清除元旦玩家使用石头锤子次数
	public function clearnewdayshummerAction()
	{
        $uid = $this->check();
		$uid = $this->_request->getParam('uid');
		
		$key = 'ev:newdays:woodenhammer:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
				
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
    
    public function getsvgiftrandAction()
    {
        $uid = $this->check();
        $list = Hapyfish2_Island_Cache_SuperVisitor::getSvGiftRandArray();
        $rand = array_rand($list);
        echo json_encode($list);
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
	public function updateairtimeAction()
	{
		$uid = $this->_request->getParam('uid');
		$level = $this->_request->getParam('level');
		$cidArr = array(1=>135132, 2=>135232, 3=>135332, 4=>135432, 5=>135532);
		$peopleArr = array(1=>20, 2=>30, 3=>40, 4=>50, 5=>70);
		$key = 'i:u:c:p:airvisitors:'.$cidArr[$level].':'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$time = time()-3600;
		$data = array('receive_time'=>$time, 'remain_visitor_num'=>$peopleArr[$level]);
		$cache->set($key, $data);
		echo 'OK';
		exit;
		
	}  

	//清除勋章的数据缓存
	public function clearatlasbookAction()
	{
		$key = 'ev:atlasbook';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	//清除勋章人数缓存
	public function clearatlasbooknumAction()
	{
		$key = 'ev:atlasbook:num';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	public function clearsfbasicAction()
	{
		$key = 'ev:newYears:data';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		 	
		echo 'OK';
		exit; 
	}
	
	public function clearfsallAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$db = Hapyfish2_Island_Event_Dal_SpringFestival::getDefaultInstance();
		$db->deleteOne($uid);
		
		$key1 = 'ev:newYears:fragmentnum:' . $uid;
		$key2 = 'ev:newYears:curcrystal:' . $uid;
		$key3 = 'ev:newYears:luckybag:' . $uid;
		$key4 = 'ev:newYears:dumpling:' . $uid;
		$key5 = 'ev:newYears:first:' . $uid;
		$key6 = 'ev:newYears:dumpling:num:' . $uid;
		$key7 = 'ev:newYears:dumpling:time:' . $uid;
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key1);
		$cache->delete($key2);
		$cache->delete($key3);
		$cache->delete($key4);
		$cache->delete($key5);
		$cache->delete($key6);
		$cache->delete($key7);
		
		echo 'OK';
		exit;
	}
	public function getfishranknumtAction()
	{
		$key = 'i:e:fishnumrank';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$data = $cache->get($key);
		var_dump($data); 	
		echo 'OK';
		exit; 
	}
	public function clearfishranknumtAction()
	{
		$key = 'i:e:fishnumrank';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		 	
		echo 'OK';
		exit; 
	}	

	public function clearlfuserdataAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$key = 'lantern:lf:userdata:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo 'OK';
		exit; 
	}
	
	public function addrosenumAction()
	{
		$uid = $this->_request->getParam('uid');
		$rose_1 = $this->_request->getParam('rose_1', 0);
		$rose_2 = $this->_request->getParam('rose_2', 0);
		$rose_3 = $this->_request->getParam('rose_3', 0);
		$rose_4 = $this->_request->getParam('rose_4', 0);
		$rose_5 = $this->_request->getParam('rose_5', 0);
		$rose_6 = $this->_request->getParam('rose_6', 0);
		
		$roseList = Hapyfish2_Island_Event_Cache_ValentineDay::getRoseList($uid);
		
		foreach ($roseList as $key => $rose) {
			if ($key == 'rose_1') {
				if ($rose_1 > 0) {
					$roseList[$key] += (int)$rose_1;
				}
			}
			
			if ($key == 'rose_2') {
				if ($rose_2 > 0) {
					$roseList[$key] += (int)$rose_2;
				}
			}
			
			if ($key == 'rose_3') {
				if ($rose_3 > 0) {
					$roseList[$key] += (int)$rose_3;
				}
			}
			
			if ($key == 'rose_4') {
				if ($rose_4 > 0) {
					$roseList[$key] += (int)$rose_4;
				}
			}
			
			if ($key == 'rose_5') {
				if ($rose_5 > 0) {
					$roseList[$key] += (int)$rose_5;
				}
			}
			
			if ($key == 'rose_6') {
				if ($rose_6 > 0) {
					$roseList[$key] += (int)$rose_6;
				}
			}
		}
		
		Hapyfish2_Island_Event_Cache_ValentineDay::renewRoseList($uid, $roseList);
		
		echo 'OK';
		exit;
	}

	// 捕鱼2 工具
	public function clearuserfishAction()
	{
		$uid = $this->_request->getParam('uid');
		$del = $this->_request->getParam('isdel', 0);
		
		$key = 'i:u:fish:getufish:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		if ($del == 1) {
			Hapyfish2_Island_Dal_Fish::delUserFish($uid);
		}
		
		echo 'OK';
		exit;
	}

	public function clearfinfoAction()
	{	
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		for($i=1;$i<=139;$i++) {
			$key = 'i:u:fish:finfo:'.$i;
			$cache->delete($key);
		}
		echo 'OK';
		exit;
	}
	
	public function saveusercacheAction()
	{
		$uid = $this->_request->getParam('uid');
		$ok = Hapyfish2_Island_Tool_SaveOldUserCache::saveOne($uid);
		if ($ok) {
			echo 'OK';
			exit;
		}
		echo 'False';
		exit;
	}
	
	public function getfinfoAction()
	{	
		$id = $this->_request->getParam('id');			
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$key = 'i:u:fish:finfo:'.$id;
		$data = $cache->get($key);
		print_r($data);
		echo 'OK';
		exit;
	}

	public function getfishuserAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:fish:uinfo' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		print_r($data);
		echo 'OK';
		exit;
	}
	
	public function clearfishislandAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		for($i=1;$i<=20;$i++) {
			$key = 'i:fish:idinfo:'.$i;
			$cache->delete($key);
		}
		echo 'OK';
		exit;
	}

	public function clearfishuserAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:fish:uinfo' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo 'OK';
		exit;
	}

	public function clearfishmapAction()
	{
		$key = 'i:fish:map';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		echo 'OK';
		exit;
	}	
	
	public function clearislandsAction()
	{
		$key = 'i:fish:island'; 
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		echo 'OK';
		exit;
	}	
	
	public function clearislandfishAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		for($i=1;$i<=25;$i++) {
			$key = 'i:fish:island:fishs:'.$i;
			$cache->delete($key);			 
		}
		echo 'OK';
		exit;
	}	

	public function clearallfishAction()
	{
		$key = 'i:fish:fsall';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);			 
		echo 'OK';
		exit;
	}

	public function clearfishpropAction()
	{
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		for($i=1;$i<=25;$i++) {
			for($j=1;$j<=3;$j++) {
				$key = 'i:u:fish:ctfhs:'.$i.':'.$j;
				$cache->delete($key);
			}				
		}
		echo 'OK';
		exit;
	}

	public function clearuserdramaAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:fish:udrama:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo 'OK';
		exit;
	}
	
	public function resetonegoldboxAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$keyBox = 'i:e:oneshop:gift:bigbox:' . $uid;
		$key = 'i:e:oneshop:box:has:' . $uid;
		$cacheBox = Hapyfish2_Cache_Factory::getMC($uid);
		$cacheBox->delete($keyBox);
		$cacheBox->delete($key);
		
		echo 'OK';
		exit;
	}
	
	public function clearfishtaskstaticAction()
	{
		$key = 'ev:fish:task:static';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	public function testfishtaskAction()
	{
		$uid = $this->_request->getParam('uid');
		$id = $this->_request->getParam('id');
		$num = $this->_request->getParam('num');
		
		
		//每日任务
		$catchFishTaskInitVo = Hapyfish2_Island_Cache_Fish::getCatchFishTaskInitVo($uid);
		
		foreach ($catchFishTaskInitVo as $taskKey => $catchFishTask) {
			if ($id == $catchFishTask['id']) {
				$catchFishTaskInitVo[$taskKey]['yetCatchNum'] += $num;
				break;
			}
		}
		
		Hapyfish2_Island_Cache_Fish::renewCatchFishTaskInitVo($uid, $catchFishTaskInitVo);
		
		echo 'OK';
		exit;
	}

	public function clearfishtaskdataAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$key = 'i:u:fish:task:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	public function clearfishstaticdataAction()
	{
		$key = 'i:fish:map';
		$key1 = 'i:fish:island';
		$key2 = 'i:fish:fsall';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		$cache->delete($key1);
		$cache->delete($key2);
		
		echo 'OK';
		exit;
	}
	
	public function clearfishislandlocksAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$key = 'i:u:fish:uinfo' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		
		echo 'OK';
		exit;
	}
	
	public static function loadmatchfishAction()
	{
		Hapyfish2_Island_Cache_FishCompound::loadBasic();

		Hapyfish2_Island_Cache_FishCompound::loadSkill();

		Hapyfish2_Island_Cache_FishCompound::loadTrack();

		Hapyfish2_Island_Cache_FishCompound::loadObstacle();
		$list = Hapyfish2_Island_Cache_FishCompound::loadAward();
		Hapyfish2_Island_Cache_FishCompound::loadGuide();
		Hapyfish2_Island_Cache_Vip::loadvip();
		$data = array('result' => 'OK');
		print_r($list);
		echo "ok";
		exit;
	}

	public function loadlocalmatchfishAction()
	{
		$cache = Hapyfish2_Island_Cache_FishCompound::getBasicMC();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$key = 'i:fish:comp';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'i:f:skill';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'i:f:track';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'i:f:obstacle';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		$key = 'i:f:m:guide';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		$key = 'i:f:m:award';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		$key = 'i:fish:vip';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		$data = array('result' => SERVER_ID.' OK');
		echo "ok";
		exit;
	}
	public function getachievementAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$userAchievement = Hapyfish2_Island_HFC_Achievement::getUserAchievement($uid);
		
		print_r('<pre>');print_r($userAchievement);print_r('</pre>');
		exit;
	}
	
	public function adduserfishAction()
	{
		$uid = $this->_request->getParam('uid');
		$fid = $this->_request->getParam('fid');
		$num = $this->_request->getParam('num');
		for($i=1; $i<=$num;$i++){
			Hapyfish2_Island_Cache_Fish::setUserFish($uid, $fid);
		}
		echo "ok";
		exit;
	}
	
	public function adduserskillAction()
	{
		$uid = $this->_request->getParam('uid');
		$id = $this->_request->getParam('id');
		$num = $this->_request->getParam('num');
		Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid,$id,$num);
		echo "ok";
		exit;
	}
	public function clearusergameAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:f:m:game:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo "ok";
		exit;
	}
	
	public function clearfishguideAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:f:m:guide:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key,3);
//		$key = 'i:u:m:f:a'.$uid;
//		$cache = Hapyfish2_Cache_Factory::getMC($uid);
//		$cache->set($key,array());
//		$key = 'i:u:f:m:game:'.$uid;
//		$cache = Hapyfish2_Cache_Factory::getMC($uid);
//		$cache->delete($key);
		echo "ok";
		exit;
	}
	
	public function cleartracklimitAction()
	{
		$uid = $this->_request->getParam('uid');
		$id = $this->_request->getParam('id');
		$key = 'i:u:f:m:limit:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$data['list'][$id] = 0;
		$cache->set($key,$data);
		echo "ok";
		exit;
	}
	
	public function insertusercomfishAction()
	{
		$uid = $this->_request->getParam('uid');
		$cid = $this->_request->getParam('cid');
		Hapyfish2_Island_Bll_FishCompound::insertUserFish($uid,$cid);
		echo "ok";
		exit;
	}
	
	public function clearfmexchangeAction()
	{
		$key = 'i:f:m:t:p:ex';
		$cache = Hapyfish2_Island_Cache_FishCompound::getBasicMC();
		$cache->delete($key);
		echo "ok";
		exit;
	}
	
	public function cleardekaronAction()
	{
		$key = 'i:f:m:t:rank:1';
		$cache = Hapyfish2_Island_Cache_FishCompound::getBasicMC();
		$data = $cache->get($key);
		foreach($data as $uid){
			$key1 ='i:u:f:m:arena:'.$uid;
			$cache1 = Hapyfish2_Cache_Factory::getMC($uid);
			$cache1->delete($key1);
		}
		$cache->delete($key);
		echo "ok";
		exit;
	}
	
	public function adduserreAction()
	{
		$uid = $this->_request->getParam('uid');
		$userNum = $this->_request->getParam('num');
		Hapyfish2_Island_Cache_FishCompound::updateUserPrestige($uid, $userNum);
		echo "ok";
		exit;
	}
	
	public function cleargetreawardAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:f:m:re:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo "ok";
		exit;
	}
	
	//修复奇迹飞机场没有气球
	public function repiarairdataAction()
	{
		$uid = $this->_request->getParam('uid');
		
		$now = time();
		$cid = 135132;
		$airInfo = array('receive_time' => $now, 'remain_visitor_num' => 20);
		
		$key = 'i:u:c:p:airvisitors:' . $cid . ':' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $airInfo);
		
		echo 'OK';
		exit;
	}
	
	public function addvipAction()
	{
		$uid = $this->_request->getParam('uid');
		$num = $this->_request->getParam('num');
		Hapyfish2_Island_Bll_Vip::insertGem($uid, $num);
		echo 'OK';
		exit;
	}
	//替换机器人头像图片地址
	public function replacerobturlAction()
	{
		for ($i = 1; $i <= 500; $i++) {
			$sid = 's' . $i;
			$file = TEMP_DIR . '/robot/' . $sid . '.cache';
			if (is_file($file)) {
				$data = file_get_contents($file);
				$news = str_replace('tbstatic.hapyfish', 'tbcdn.playwhale', $data);
				
				file_put_contents($file, $news);
			}

			$data1 = file_get_contents($file);
		}
		
		echo 'OK';
		exit;
	}
	
	public function clearvipAction()
	{
		$uid = $this->_request->getParam('uid');
		Hapyfish2_Island_Cache_Vip::updateGem($uid, 0);
		echo 'OK';
		exit;
	}
	
	public function clearprestigeAction()
	{
		$key = 'i:f:m:t:p:ex';
		$cache = Hapyfish2_Island_Cache_FishCompound::getBasicMC();
		$cache->delete($key);
		echo "ok";
		exit;
	}
	
	public function senduserskillAction()
	{
		$data = array(1346420,2548088,2784743,6647262,3789849,3775668,7643197,5940882,3289284,2498821,2943608,5594158,2740598,3566897,5455343,7600316,3316813,5237326,5960315,1420053,4798558,4421212,5221639,3055390,3470817,7676610,5135087,5043648,5601204,1099713,3755156,2771817,2413842,4151992,7628188,7601459,4510324,4442541,2577715,5054049,516252,7031555,7297213,4334745,2440161,702880,5136772,1933161,7645225,5240394,4379224,7633920,7691042,2291099,4451988,3507732,5423755,5994200,80228,1347208,1002615,105987,5600255,2361608,5560812,5794518,856907,7287771,5179214,816563,3093867,2255019,1721030,2199743,3054994,3021664,1091308,7665415,5950253,77576,4727260,72444,2258315,7626466,3038668,5805271,3174498,1278616,5104271,3208342,40930,4283766,319338,119108,7639271,11166,2666055,6501340,721937,2669203,5217298,5209783,5205256,4284092,6451473);
		foreach($data as $uid){
			Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, 1, 3);
		}
		echo "ok";
		exit;
	}
	
	public function gettoprankAction()
	{
		$cache = Hapyfish2_Island_Cache_FishCompound::getBasicMC();
		for($i=1;$i<=100;$i++){
			$key = 'i:f:m:t:rank:'.$i;
			$cache->delete($key);
		}
		echo "ok";
		exit;
	}
	
	public function getplantnumAction()
	{
		$num = 0;
		$start = $this->_request->getParam('start');
		$end = $this->_request->getParam('end');
		$cid = $this->_request->getParam('cid');
		for($i=$start;$i<$end;$i++){
			for($j=0;$j<=49;$j++){
				$db[$i][]= DATABASE_NODE_NUM*$j + $i;
			}
		}
		$dal = Hapyfish2_Island_Event_Dal_Peidui::getDefaultInstance();
		foreach($db as $k => $v){
			foreach($v as $k1 => $v1){
				$count = $dal->getUid($v1, $cid);
				if($count) {
					$num += $count;
				}
			}
		}
		
		echo $num;
		exit;
	}
	
	public function unlockfishAction()
	{
		$uid = $this->_request->getParam('uid');
		$id = $this->_request->getParam('id');
		Hapyfish2_Island_Cache_FishCompound::updateUserLock($uid, $id);
		echo "ok";
		exit;
	}
	
	public function clearskillAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:f:m:skill:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo "ok";
		exit;
	}
}