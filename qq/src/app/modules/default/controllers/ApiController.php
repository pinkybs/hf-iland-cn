<?php

class ApiController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
        if (APP_STATUS == 0) {
        	$ip = $this->getClientIP();
        	//info_log($ip,"checkip");
        	if ($ip != '27.115.48.202' && $ip != '116.247.76.102' && $ip != '122.147.63.223') {
        		$result = array('status' => '-1', 'content' => '停机维护中');
    			$this->echoResult($result);
        	}
    	}

    	$info = $this->vailid();
        if (!$info) {
        	$result = array('status' => '-1', 'content' => 'serverWord_101');
			$this->echoResult($result);
        }

        $this->info = $info;
        $this->uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'openid' => $info['openid'], 'openkey' => $info['openkey']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
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
		} else if (!empty($_SERVER['HTTP_QVIA'])) {
			$strData = substr($_SERVER['HTTP_QVIA'], 0, 8);
			$data = array(hexdec(substr($strData, 0, 2)),
			              hexdec(substr($strData, 2, 2)),
			              hexdec(substr($strData, 4, 2)),
			              hexdec(substr($strData, 6, 2)));
			$ip = implode('.', $data);
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }

    protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	if (!$skey) {
    		return false;
    	}

    	$tmp = split('_', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $openid = $tmp[1];
        $openkey = $tmp[2];
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $openid . $openkey . $t . APP_KEY);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $openid . $openkey . $t . $rnd . APP_KEY);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'openid' => $openid, 'openkey' => $openkey,  't' => $t, 'rnd' => $rnd);
    }

    protected function checkEcode($params = array())
    {
    	if ($this->info['rnd'] > 0) {
    		$rnd = $this->info['rnd'];
    		$uid = $this->uid;
    		$ts = $this->_request->getParam('tss');
    		$authid = $this->_request->getParam('authid');
    		$ok = true;
    		if (empty($authid) || empty($ts)) {
    			$ok = false;
    		}
    		if ($ok) {
    			$ok = Hapyfish2_Island_Bll_Ecode::check($rnd, $uid, $ts, $authid, $params);
    		}
    		if (!$ok) {
    			//Hapyfish2_Island_Bll_Block::add($uid, 1, 2);
    			info_log($uid, 'ecode-err');
	        	$result = array('status' => '-1', 'content' => 'serverWord_101');
	        	setcookie('hf_skey', '' , 0, '/', '.island.qzoneapp.com');
	        	$this->echoResult($result);
    		}
    	}
    }

    protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

    protected function echoResultAndLog($data, $logInfo)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);

    	if ($logInfo != null) {
			//report log
			$logInfo['openid'] = $this->info['openid'];
			$logger = Qzone_Log::getInstance();
			//$logger->setLogFile(LOG_DIR . '/report.log');
			$logger->report($this->uid, $logInfo);
    	}
    	exit;
    }

    /**
     * init swf
     *
     */
    public function initswfAction()
    {
        $uid = $this->uid;
    	$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		require (CONFIG_DIR . '/swfconfig.php');

    	if ($userLevelInfo['level'] <= 5) {
			if ($this->info['rnd'] > 0) {
				$swfResult_0['edata'] = HOST . '/api/edata?t=' . time();
			}
			$this->echoResult($swfResult_0);
    	} else {
			if ($this->info['rnd'] > 0) {
				$swfResult_1['edata'] = HOST . '/api/edata?t=' . time();
			}
			$this->echoResult($swfResult_1);
		}
    }

	/**
     * compute first touch
     */
    public function firsttouchAction()
    {
    	try {
	    	$uid = $this->uid;
	    	//add log
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('102', array($uid));
			$this->echoResult(array('status'=>1));
    	} catch (Exception $e) {
        }
    }

	/**
     * compute second touch
     */
    public function secondtouchAction()
    {
    	try{
	    	$uid = $this->uid;
	    	//add log
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('103', array($uid));
			$this->echoResult(array('status'=>1));
    	} catch (Exception $e) {
        }
    }

	/**
     * get user next big gift level
     */
    public function getnextgiftlevelAction()
    {
    	try {
	    	$uid = $this->uid;
	    	$result = Hapyfish2_Island_Bll_User::getUserNextBigGiftLevel($uid);
	    	$this->echoResult($result);
    	} catch (Exception $e) {
        }
    }

	/**
     * get level big gift
     */
    public function getlevelbiggiftAction()
    {
    	try {
	    	$uid = $this->uid;
	    	$result = Hapyfish2_Island_Bll_User::getLevelBigGift($uid);
	    	$this->echoResult($result);

    	} catch (Exception $e) {
        }
    }

	/**
     * upgrade island
     */
    public function upgradeislandAction()
    {
    	$uid = $this->uid;
    	$islandId = $this->_request->getParam('islandId', 1);
    	$islandLevel = $this->_request->getParam('level', 1);

    	$result = Hapyfish2_Island_Bll_User::upgradeIsland($uid, $islandId, $islandLevel);
    	$this->echoResult($result);
    }

	/**
     * clear diy
     */
    public function cleardiyAction()
    {
    	$uid = $this->uid;
    	$type = $this->_request->getParam('type', 1);

    	$result = Hapyfish2_Island_Bll_Card::clearDiy($uid, $type);
    	$this->echoResult($result);
    }

	/**
     * get vip gift
     *
     */
    public function getvipgiftAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_User::getVipGift($uid);
    	$this->echoResult($result);
    }

	/**
     * get star gift
     *
     */
    public function getstargiftAction()
    {
    	$sid = $this->_request->getParam('idx');
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_User::getStarGift($uid, $sid);
    	$this->echoResult($result);
    }

	/**
     * read star gift
     *
     */
    public function readstargiftAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_User::readStarGift($uid);

    	$this->echoResult($result);
    }

	/**
     * get gift package list
     *
     */
    public function getgiftpackagelistAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_GiftPackage::getList($uid);
    	$this->echoResult($result);
    }

    /**
     * open one gift package
     *
     */
    public function opengiftpackageAction()
    {
    	$uid = $this->uid;
		$pid = $this->_request->getParam('id');
    	$result = Hapyfish2_Island_Bll_GiftPackage::openOne($uid, $pid);
		$this->echoResult($result);
    }

    public function openallgiftAction()
    {
    	$uid = $this->uid;
    	$result['result'] = array('status' => 1);
    	$giftdal = Hapyfish2_Island_Dal_GiftPackage::getDefaultInstance();
    	$giftlist = $giftdal->getList($uid);
    	if($giftlist){
    		foreach($giftlist as $k=>$v ){
    			Hapyfish2_Island_Bll_GiftPackage::openOne($uid, $v['pid']);
    		}
    	}
    	$this->echoResult($result);

    }

    public function getgiftpackagenumAction()
    {
    	$uid = $this->uid;

    	$num = Hapyfish2_Island_Bll_GiftPackage::getNum($uid);
		$result = array('result' => array('status' => 1), 'giftNum' => $num);
		$this->echoResult($result);
    }

	/**
     * get level gift
     */
    public function getlevelgiftAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_GiftPackage::getLevelGift($uid);
		echo Zend_Json::encode($result);
    }

    public function edataAction()
    {
    	if ($this->info['rnd'] > 0) {
    		$rnd = $this->info['rnd'];
			$file = CONFIG_DIR . '/ecode/ES' . $rnd . '.swf';
			if (is_file($file)) {
		        ob_end_clean();
		        ob_start();
		        $file_size = filesize($file);
		        header("Accept-Ranges: bytes");
		        header("Content-Length: " . $file_size);
		        header("Cache-Control: no-store, no-cache, must-revalidate");
		        header("Content-Type: application/x-shockwave-flash");
				echo file_get_contents($file);
			}
    	}
    	exit;
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
    	$uid = $this->uid;
    	$ownerUid = $this->_request->getParam('ownerUid');
        $positionId = $this->_request->getParam('positionId');

        //$this->checkEcode(array('ownerUid' => $ownerUid, 'positionId' => $positionId));

        $key = 'moochvisitor:' . $ownerUid . ':' . $positionId;
        $fid = (int)$ownerUid;
        $lock = Hapyfish2_Cache_Factory::getLock($fid);

        //get lock
        $ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_102');
			$this->echoResult(array('result' => $result));
        }
		$robot = strpos($ownerUid, 's');
        if($robot === 0){
			$result = Hapyfish2_Island_Bll_Robot::moochboat($uid, $ownerUid, $positionId);
		} else {
        	$result = Hapyfish2_Island_Bll_Dock::mooch($uid, $ownerUid, $positionId);
		}

        //release lock
        $lock->unlock($key);

        if ($result['result']['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 303, 'iState' => 0, 'ownerUid' => $ownerUid, 'expChange' => $result['result']['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
    }

    /**
     * load island Action
     *
     */
    function initislandAction()
    {
        $uid = $this->uid;
    	$ownerUid = $this->_request->getParam('ownerUid', $uid);
    	$islandId = $this->_request->getParam('islandId');

        $robot = strpos($ownerUid, 's');
    	if ($ownerUid == '134' && $uid != $ownerUid) {
            echo Hapyfish2_Island_Bll_Island::restoreInitUserIsland($ownerUid);
        }else if ($robot === 0){
        	echo Hapyfish2_Island_Bll_Robot::getRobotInfo($ownerUid);
        }
        else {
        	if ($uid == $ownerUid) {
        		$status = Hapyfish2_Platform_Bll_Factory::getStatus($uid);
        		if ($status > 0) {
        			$result = array('status' => '-1', 'content' => 'serverWord_101');
        			setcookie('hf_skey', '' , 0, '/', '.island.qzoneapp.com');
        			$this->echoResult($result);
        		}
        		$logInfo = array('iSource' => 2, 'iCmd' => 201, 'iState' => 0, 'ownerUid' => $ownerUid);
        	} else {
        		$logInfo = array('iSource' => 2, 'iCmd' => 301, 'iState' => 0, 'ownerUid' => $ownerUid);
        	}

            $result = Hapyfish2_Island_Bll_Island::initIsland($ownerUid, $uid, false, $islandId);

            $this->echoResultAndLog($result, $logInfo);
        }
    }

    /**
     * open new island
     *
     */
    function openislandAction()
    {
    	$islandId = $this->_request->getParam('islandId');
    	//pay type 0：金币,1：宝石
    	$priceType = $this->_request->getParam('payType');

    	$result = Hapyfish2_Island_Bll_Island::openIsland($this->uid, $islandId, $priceType);

        $this->echoResult($result);
    }

    /**
     * change island tip
     *
     */
    function changeislandtipAction()
    {
    	$mapIconState = $this->_request->getParam('mapIconState');

    	$result = Hapyfish2_Island_Bll_Island::changeIslandTip($this->uid, $mapIconState);

        $this->echoResult($result);
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

        if ($result['resultVo']['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 205, 'iState' => 0, 'ownerUid' => $this->uid);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }

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
            $result = Hapyfish2_Island_Bll_Shop::buyIslandArrayForQpoint($uid, $islandAry);
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

		$key = 'saleitem:' . $uid;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

        //get lock
        $ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
        }

		$result = Hapyfish2_Island_Bll_Shop::saleItemArray($uid, $itemArray);
        $itemBox = Hapyfish2_Island_Bll_Warehouse::loadItems($uid);

        //release lock
        $lock->unlock($key);

        $resultData = array('resultVo' => $result, 'items' => $itemBox);

        if ($result['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 206, 'iState' => 0, 'ownerUid' => $uid, 'coinChange' => $result['coinChange']);
        	$this->echoResultAndLog($resultData, $logInfo);
        } else {
        	$this->echoResult($resultData);
        }
    }

    /**
     * harvest plant
     *
     */
    function harvestallplantAction()
    {
        $uid = $this->uid;

		$key = 'harvestallplant:' . $uid;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Plant::harvestAllPlant($uid);

        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    /**
     * change help
     *
     */
    function changehelpAction()
    {
        $help = $this->_request->getParam('step');

        $uid = $this->uid;
        $key = 'changehelp:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

    	//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}
    	if(8 == $help){
			Hapyfish2_Island_Bll_Robot::addFriend($uid);
		}
        $result = Hapyfish2_Island_Bll_User::changeHelp($uid, $help);

        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    /**
     * get help gift
     *
     */
    function gethelpgiftAction()
    {
        $help = $this->_request->getParam('step');

        $uid = $this->uid;
        $key = 'gethelpgift:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

    	//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_User::getHelpGift($uid, $help);

        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    /**
     * harvest plant
     *
     */
    function harvestplantAction()
    {
        $itemId = $this->_request->getParam('itemId');
        $uid = $this->uid;

		$key = 'harvestplant:' . $uid . ':' . $itemId;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Plant::harvestPlant($this->uid, $itemId);

        //release lock
        $lock->unlock($key);

        if ($result['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 203, 'iState' => 0, 'ownerUid' => $uid, 'coinChange' => $result['coinChange'], 'expChange' => $result['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
    }

    /**
     * mooch plant
     *
     */
    function moochplantAction()
    {
    	$fid = $this->_request->getParam('fid');
        $itemId = $this->_request->getParam('itemId');
        $uid = $this->uid;

        //$this->checkEcode(array('fid' => $fid, 'itemId' => $itemId));

		$key = 'moochplant:' . $fid . ':' . $itemId;
		$ownerId = (int)$fid;
		$lock = Hapyfish2_Cache_Factory::getLock($ownerId);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}
		$robot = strpos($fid, 's');
		if($robot === 0){
			$result = Hapyfish2_Island_Bll_Robot::moochPlant($uid, $fid, $itemId);
		} else {
        	$result = Hapyfish2_Island_Bll_Plant::moochPlant($uid, $fid, $itemId);
		}

        //release lock
        $lock->unlock($key);

        if ($result['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 302, 'iState' => 0, 'ownerUid' => $fid, 'coinChange' => $result['coinChange'], 'expChange' => $result['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
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

		$key = 'manageplant:' . $ownerUid . ':' . $itemId;
		$fid = (int)$ownerUid;
		$uid = $this->uid;
		$lock = Hapyfish2_Cache_Factory::getLock($fid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('resultVo' => $result));
		}

        $result = Hapyfish2_Island_Bll_Plant::managePlant($uid, $itemId, $eventType, $ownerUid);

        //release lock
        $lock->unlock($key);

        if ($result['resultVo']['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 219, 'iState' => 0, 'ownerUid' => $ownerUid, 'expChange' => $result['resultVo']['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
    }

    /**
     * upgrade plant
     *
     */
    function upgradeplantAction()
    {
        $itemId = $this->_request->getParam('itemId');
        $uid = $this->uid;

		$key = 'upgradeplant:' . $uid . ':' . $itemId;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('resultVo' => $result));
		}

		$result = Hapyfish2_Island_Bll_Plant::upgradePlant($uid, $itemId);

        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    /**
     * save photo
     *
     */
    public function savephotoAction()
    {
        //Hapyfish2_Island_Bll_User::savePhoto($this->uid);
    	$uid = $this->uid;
    	$result = array('status' => -1);
		try {
			Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_27', 1);

			$resultVo = array();
			//task id 3071,task type 27
			$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3071);
			if ( $checkTask['status'] == 1 ) {
				$result['finishTaskId'] = $checkTask['finishTaskId'];
			}
			$result['status'] = 1;
			$this->echoResult($result);
		} catch (Exception $e) {
		}
    }

    /**
     * finish task
     *
     */
    function finishtaskAction()
    {
        $taskId = $this->_request->getParam('taskId');
        $uid = $this->uid;

        $key = 'finishtask:' . $uid . ':' . $taskId;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

    	//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Task::finishTask($uid, $taskId);

        //release lock
        $lock->unlock($key);

        if ($result['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 210, 'iState' => 0, 'ownerUid' => $uid, 'coinChange' => $result['coinChange'], 'expChange' => $result['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
    }

    /**
     * refresh task
     *
     */
    function refreshtaskAction()
    {
        $taskId = $this->_request->getParam('taskId');
        $uid = $this->uid;

        $key = 'refreshtask:' . $uid . ':' . $taskId;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

    	//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Task::refreshTask($uid, $taskId);

        //release lock
        $lock->unlock($key);

        if ($result['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 210, 'iState' => 0, 'ownerUid' => $uid, 'coinChange' => $result['coinChange'], 'expChange' => $result['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
    }

    /**
     * open lock task
     *
     */
    public function opentaskAction()
    {
    	$openTask = $this->_request->getParam('openTask');

        $openTask = Zend_Json::decode($openTask);
        $result = Hapyfish2_Island_Bll_Task::openTask($this->uid, $openTask);

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
        $isAuto = $this->_request->getParam('isAuto', 0);

        $result = Hapyfish2_Island_Bll_Dock::changeShip($this->uid, $shipId, $positionId, $isAuto);

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
		header("Cache-Control: max-age=2592000");
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

 		$userVo['medalArray'] = Hapyfish2_Island_Bll_Rank::isTopTen($uid);
		$first = $this->_request->getParam('first', '0');
        if ($first == '1') {
			$key = 'inituserinfo:' . $uid;
        	$lock = Hapyfish2_Cache_Factory::getLock($uid);
	    	//get lock
			$ok = $lock->lock($key);
        	if ($ok) {
        		$todayInfoResult = Hapyfish2_Island_Bll_User::updateUserTodayInfo($uid, $userVo['medalArray']);
        		$userVo['signAward'] = $todayInfoResult['activeCount'];
        		$userVo['news'] = $todayInfoResult['showViewNews'];
        		//每日登陆翻牌
        		$userVo['SignAwardarray'] = Hapyfish2_Island_Bll_Fragments::getFragmentsInfo($uid);
        		//release lock
        		$lock->unlock($key);
			}
        } else {
        	$userVo['signAward'] = -1;
        	$userVo['news'] = false;
        }

		//全屏状态
		$keyFulScreen = 'i:u:fullScreen:' . $uid;
		$fullScreenCache = Hapyfish2_Cache_Factory::getMC($uid);
		$fullScreenStatus = $fullScreenCache->get($keyFulScreen);

		if(!$fullScreenStatus) {
	        $dalFullScreen = Hapyfish2_Island_Dal_FullScreen::getDefaultInstance();
	        $fullScreenStatus = $dalFullScreen->getStatus($uid);

	        if($fullScreenStatus) {
	        	$fullScreenCache->set($keyFulScreen, $fullScreenStatus);
	        	$userVo['isFullScreen'] = $fullScreenStatus;
	        } else {
	        	$userVo['isFullScreen'] = 0;
	        }
		} else {
        	$userVo['isFullScreen'] = $fullScreenStatus;
        }

        //礼包数量
		$userVo['giftNum'] = Hapyfish2_Island_Bll_GiftPackage::getNum($uid);
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
     * gain daily awards Action
     *
     */
    public function gaindailyawardsAction()
    {
    	$uid = $this->uid;
    	$key = 'gaindlyawardlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Bll_DailyAward::gainAwards($uid);
    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    /**
     * add boat Action
     *
     */
	function addboatAction()
	{
		$uid = $this->uid;
		$key = 'expandposition:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

		$result = Hapyfish2_Island_Bll_Dock::expandPosition($uid);

        //release lock
        $lock->unlock($key);

		$this->echoResult($result);
	}

	/**
	 * receive boat Action
	 *
	 */
	function receiveboatAction()
	{
		$pid = $this->_request->getParam('positionId');
		$uid = $this->uid;

		$key = 'receiveboat:' . $uid . ':' . $pid;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Dock::receiveBoat($uid, $pid);

        //release lock
        $lock->unlock($key);

        if ($result['result']['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 202, 'iState' => 0, 'ownerUid' => $uid, 'expChange' => $result['result']['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
	}

	/**
	 * stop boat Action
	 *
	 */
	function stopboatAction()
	{
		$pid = $this->_request->getParam('positionId');
		$uid = $this->uid;

		$key = 'stopboat:' . $uid . ':' . $pid;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Dock::stopBoat($uid, $pid);

        //release lock
        $lock->unlock($key);

        if ($result['result']['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 202, 'iState' => 0, 'ownerUid' => $uid, 'expChange' => $result['result']['expChange']);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
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

		$key = 'usecard:' . $uid . ':' . $cid;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = array();

		if ($pid) {
			$result = Hapyfish2_Island_Bll_Card::speedCard($uid, $pid, $cid);

			//release lock
        	$lock->unlock($key);

			if ($result['resultVo']['status'] > 0) {
				$logInfo = array('iSource' => 2, 'iCmd' => 215, 'iState' => 0, 'ownerUid' => $uid, 'expChange' => $result['resultVo']['expChange']);
				$this->echoResultAndLog($result, $logInfo);
			}
		} else {
			$result = Hapyfish2_Island_Bll_Card::useCard($uid, $onwerUid, $cid, $itemId);

			//release lock
        	$lock->unlock($key);
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

        $logInfo = array('iSource' => 2, 'iCmd' => 305, 'iState' => 0, 'ownerUid' => $this->uid);
        $this->echoResultAndLog($rankResult, $logInfo);
	}

	public function getgoldAction()
	{
		$uid = $this->uid;
		$gold = Hapyfish2_Island_Bll_Gold::get($uid);
		$result = array('result' => array('status' => 1), 'gemNum' => $gold);

		$this->echoResult($result);
	}

    public function uploadpictureAction()
	{
        $prefix = 'HappyIsland';
        $picture_name = $prefix . '-' . date('Ymdhis');

        $uid = $this->uid;
        $puid = $this->info['puid'];
        $taobao = Taobao_Rest::getInstance();
        $taobao->setUser($puid, $this->info['session_key']);

        try {
            $album_id = Hapyfish2_Platform_Cache_User::getVUID($uid);

            $url = $taobao->jianghu->get_picture_uploadPicture($album_id, $picture_name);

            $result = array(
                'picture_name' => $picture_name,
                'url' => $url
            );

            $this->echoResult($result);
        }catch (Exception $e) {

        }

        exit;
	}

    public function sendfeedAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		$result = Hapyfish2_Island_Bll_Activity::send($type, $uid);
        $this->echoResult($result);
	}


	//qq qpoint pay upgrade
	public function qpointpayAction()
	{
	    $cid = $this->_request->getParam('cid');

	    $uid = $this->uid;
    	$key = 'qpointpaylock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => '点击过快');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Bll_QpointBuy::getToken($uid, $cid);
    	//release lock
        $lock->unlock($key);

        $this->echoResult($result);
	}

 }