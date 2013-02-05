<?php

class ApitestController extends Zend_Controller_Action
{
    protected $uid;
    
    protected $info;
    
    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
        	$result = array('status' => '-1', 'content' => 'serverWord_101');
        	echo json_encode($result);
        	exit;
        }
        
        $this->info = $info;
        $this->uid = $info['uid'];
    	
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	if (!$skey) {
    		return false;
    	}
    	
    	$tmp = split('_', $skey);
    	if (empty($tmp) || count($tmp) != 5) {
    		return false;
    	}
    	
        $uid = $tmp[0];
        $openid = $tmp[1];
        $openkey = $tmp[2];
        $t = $tmp[3];
        $sig = $tmp[4];
        
        $vsig = md5($uid . $openid . $openkey . $t . APP_KEY);
        if ($sig != $vsig) {
        	return false;
        }
        
        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }
        
        return array('uid' => $uid, 'openid' => $openid, 'openkey' => $openkey,  't' => $t);
    }
    
    protected function echoResult($data)
    {
    	echo json_encode($data);
    	exit;
    }

    /**
     * init swf
     *
     */
    public function initswfAction()
    {
        require (CONFIG_DIR . '/swfconfig.php');
        $this->echoResult($swfResult);
    }
    
    /**
     * add remind Action
     *
     */
    public function addremindAction()
    {
        $fid = $this->_request->getParam('fid');
        $type = $this->_request->getParam('type');
        $content = $this->_request->getParam('content');
        
        $result = Hapyfish2_Island_Bll_Remind::addRemind($this->uid, $fid, $content, $type);
        $this->echoResult($result);
    }
    
    public function readremindAction()
    {
        $pageIndex = $this->_request->getParam('pageIndex', 1);
        $pageSize = $this->_request->getParam('pageSize', 50);
        
        $remindList = Hapyfish2_Island_Bll_Remind::getRemind($this->uid, $pageIndex, $pageSize);
        $this->echoResult($remindList);
    }

    /**
     * mooch visitor Action
     *
     */
    public function moochvisitorAction()
    {
        $ownerUid = $this->_request->getParam('ownerUid');
        $positionId = $this->_request->getParam('positionId');

        $result = Hapyfish2_Island_Bll_Dock::mooch($this->uid, $ownerUid, $positionId);
		$this->echoResult($result);
    }

    /**
     * load island Action
     *
     */
    function initislandAction()
    {
        $ownerUid = $this->_request->getParam('ownerUid', $this->uid);

        if ($ownerUid == '134' && $this->uid != $ownerUid) {
            echo Hapyfish2_Island_Bll_Island::restoreInitUserIsland($ownerUid);
        }
        else {
            $result = Hapyfish2_Island_Bll_Island::initIsland($ownerUid, $this->uid);
            $this->echoResult($result);
        }
    }

    /**
     * load island Action
     *
     */
    function diyislandAction()
    {
        $changesAry = $this->_request->getParam('changes');
        $removesAry = $this->_request->getParam('removes');

        $changesAry = Zend_Json::decode($changesAry);
        $removesAry = Zend_Json::decode($removesAry);

        $result = Hapyfish2_Island_Bll_Island::diyIsland($this->uid, $changesAry, $removesAry);
        
        $this->echoResult($result);
    }

    /**
     * load shop Action
     *
     */
    function loadshopAction()
    {
    	$result = Hapyfish2_Island_Bll_Shop::loadShop();
    	
		$this->echoResult($result);
    }

    /**
     * buy item Action
     *
     */
    function buyitemAction()
    {
        $itemBoxAry = $this->_request->getParam('toItemBox');
        $islandAry = $this->_request->getParam('toIsland');

        $itemBoxAry = json_decode($itemBoxAry, true);
        $islandAry = json_decode($islandAry, true);
        $uid = $this->uid;
        
        $result = array();
        if (!empty($itemBoxAry)) {
            //buy item
            $result = Hapyfish2_Island_Bll_Shop::buyItemArray($uid, $itemBoxAry);
        }
        if (!empty($islandAry)) {
            //buy Building
            $result = Hapyfish2_Island_Bll_Shop::buyIslandArray($uid, $islandAry);
        }       

        $itemBox = Hapyfish2_Island_Bll_Warehouse::loadItems($uid);

        $result = array('resultVo' => $result, 'items' => $itemBox);
        $this->echoResult($result);
    }

    /**
     * sale item Action
     *
     */
    function saleitemAction()
    {
        $itemArray = $this->_request->getParam('items');
		$itemArray = json_decode($itemArray, true);
		$uid = $this->uid;
		
		$result = Hapyfish2_Island_Bll_Shop::saleItemArray($uid, $itemArray);
        $itemBox = Hapyfish2_Island_Bll_Warehouse::loadItems($uid);
        
        $result = array('resultVo' => $result, 'items' => $itemBox);
        $this->echoResult($result);
    }

    /**
     * change help
     *
     */
    function changehelpAction()
    {
        $help = $this->_request->getParam('step');

        $result = Hapyfish2_Island_Bll_User::changeHelp($this->uid, $help);
        
        $this->echoResult($result);
    }

    /**
     * harvest plant
     *
     */
    function harvestplantAction()
    {
        $itemId = $this->_request->getParam('itemId');

        $result = Hapyfish2_Island_Bll_Plant::harvestPlant($this->uid, $itemId);
        
        $this->echoResult($result);
    }

    /**
     * mooch plant
     *
     */
    function moochplantAction()
    {
        $fid = $this->_request->getParam('fid');
        $itemId = $this->_request->getParam('itemId');

        $result = Hapyfish2_Island_Bll_Plant::moochPlant($this->uid, $fid, $itemId);
        
        $this->echoResult($result);
    }

    /**
     * manage plant
     *
     */
    function manageplantAction()
    {
        $itemId = $this->_request->getParam('itemId');
        $eventType = $this->_request->getParam('eventType');
        $ownerUid = $this->_request->getParam('ownerUid');

        $result = Hapyfish2_Island_Bll_Plant::managePlant($this->uid, $itemId, $eventType, $ownerUid);
        
        $this->echoResult($result);
    }

    /**
     * upgrade plant
     *
     */
    function upgradeplantAction()
    {
        $itemId = $this->_request->getParam('itemId');

        $result = Hapyfish2_Island_Bll_Plant::upgradePlant($this->uid, $itemId);
        
        $this->echoResult($result);
    }

    /**
     * finish task
     *
     */
    function finishtaskAction()
    {
        $taskId = $this->_request->getParam('taskId');

        $result = Hapyfish2_Island_Bll_Task::finishTask($this->uid, $taskId);

        $this->echoResult($result);
    }

    /**
     * read task
     *
     */
    function readtaskAction()
    {
    	$result = Hapyfish2_Island_Bll_Task::readTask($this->uid);
        
        $this->echoResult($result);
    }

    /**
     * read task
     *
     */
    function readtitleAction()
    {
        $uid = $this->uid;
    	$ownerUid = $this->_request->getParam('uid', $uid);

        $result = Hapyfish2_Island_Bll_User::readTitle($uid, $ownerUid);
        
        $this->echoResult($result);
    }

    /**
     * read task
     *
     */
    function changetitleAction()
    {
        $titleId = (int)$this->_request->getParam('titleId', 0);
        
        $result = Hapyfish2_Island_Bll_User::changeTitle($this->uid, $titleId);
        
        $this->echoResult($result);
    }

    /**
     * read ship
     *
     */
    function readshipAction()
    {
        $pid = $this->_request->getParam('positionId');
        
        $result = Hapyfish2_Island_Bll_Dock::readShip($this->uid, $pid);
        
        $this->echoResult($result);
    }

    /**
     * unlock ship
     *
     */
    function unlockshipAction()
    {
        $shipId = $this->_request->getParam('boatId');
        $priceType = $this->_request->getParam('priceType');
        $pid = $this->_request->getParam('positionId');
        
        $result = Hapyfish2_Island_Bll_Dock::unlockShip($this->uid, $shipId, $pid, $priceType);
        
        $this->echoResult($result);
    }

    /**
     * change ship
     *
     */
    function changeshipAction()
    {
        $shipId = $this->_request->getParam('boatId');
        $positionId = $this->_request->getParam('positionId');
        
        $result = Hapyfish2_Island_Bll_Dock::changeShip($this->uid, $shipId, $positionId);
        
        $this->echoResult($result);
    }


    /**
     * init dock Action
     *
     */
    function initdockAction()
    {
        $ownerUid = $this->_request->getParam('ownerUid', $this->uid);
        
        $result = Hapyfish2_Island_Bll_Dock::initDock($ownerUid, $this->uid);
        
        $this->echoResult($result);
    }

    /**
     * init user Action
     *
     */
    function inituserAction()
    {
		echo Hapyfish2_Island_Bll_BasicInfo::getInitVoData();
		exit;
    }

    /**
     * init user Action
     *
     */
    function inituserinfoAction()
    {
        $uid = $this->uid;

        //get user info
        $userVo = Hapyfish2_Island_Bll_User::getUserInit($uid);
        
		$first = $this->_request->getParam('first', '0');
		$first = '1';
        if ($first == '1') {
        	$todayInfoResult = Hapyfish2_Island_Bll_User::updateUserTodayInfo($uid);
        	$userVo['signAward'] = $todayInfoResult['activeCount'];
        	$userVo['news'] = $todayInfoResult['showViewNews'];
        } else {
        	$userVo['signAward'] = -1;
        	$userVo['news'] = false;
        }
        	
        //get user item box info
        $itemBox = Hapyfish2_Island_Bll_Warehouse::loadItems($uid);
        
        //title info
        $title = Hapyfish2_Island_Bll_User::readTitle($uid, $uid);
        //system time
        $systemTime = time();
        
        $result = array('user' => $userVo, 'items' => $itemBox, 'title' => $title, 'systemTime' => $systemTime);
        
        $this->echoResult($result);
    }

    /**
     * add boat Action
     *
     */
	function addboatAction()
	{
		$result = Hapyfish2_Island_Bll_Dock::expandPosition($this->uid);

		$this->echoResult($result);
	}

	/**
	 * receive boat Action
	 *
	 */
	function receiveboatAction()
	{
		$pid = $this->_request->getParam('positionId');

        $result = Hapyfish2_Island_Bll_Dock::receiveBoat($this->uid, $pid);
        
		$this->echoResult($result);
	}

	/**
	 * load items Action
	 *
	 */
	function loaditemsAction()
	{
		$result = Hapyfish2_Island_Bll_Warehouse::loadItems($this->uid);

		$this->echoResult($result);
	}

	function usecardAction()
	{
		$cid = $this->_request->getParam('cid');
		$itemId = $this->_request->getParam('itemId');
		$onwerUid = $this->_request->getParam('ownerUid');

		$uid = $this->uid;
		$pid = $this->_request->getParam('positionId');

        $result = array();
        
		if ($pid) {
			$result = Hapyfish2_Island_Bll_Card::speedCard($uid, $pid, $cid);
		} else {
			$result = Hapyfish2_Island_Bll_Card::useCard($uid, $onwerUid, $cid, $itemId);
		}

		$this->echoResult($result);
	}

	/**
	 * read feed Action
	 *
	 */
	function readfeedAction()
	{
		$pageIndex = $this->_request->getParam('pageIndex', 1);
		$pageSize = $this->_request->getParam('pageSize', 50);

		$feedList = Hapyfish2_Island_Bll_Feed::getFeed($this->uid, $pageIndex, $pageSize);
		
		$this->echoResult($feedList);
	}

	function getfriendsAction()
	{
		$pageIndex = $this->_request->getParam('pageIndex', 1);
        $pageSize = $this->_request->getParam('pageSize', 20);
        
        $rankResult = Hapyfish2_Island_Bll_Friend::getRankList($this->uid, $pageIndex, $pageSize);

        $this->echoResult($rankResult);
	}
	
	public function getgoldAction()
	{
		$result = array('gemNum' => 0);
		$this->echoResult($result);
	}

 }