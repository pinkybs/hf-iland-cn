<?php

class ZxforstresstestController extends Zend_Controller_Action
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

    function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

    function echoResultAndLog($data, $logInfo)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);

    	/*
    	if ($logInfo != null) {
			//report log
			$logInfo['openid'] = $this->info['openid'];
			$logger = Qzone_Log::getInstance();
			//$logger->setLogFile(LOG_DIR . '/report.log');
			$logger->report($uid, $logInfo);
    	}
		*/
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

    public function harvestplantAction()
    {
		$uid = $this->check();
        $itemId = $this->_request->getParam('itemId');

		$key = 'harvestplant:' . $uid . ':' . $itemId;
		$lock = Hapyfish2_Cache_Factory::getLock($uid);

		//get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

        $result = Hapyfish2_Island_Bll_Plant::harvestPlant($uid, $itemId);

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
        $uid = $this->check();
    	$fid = $this->_request->getParam('fid');
        $itemId = $this->_request->getParam('itemId');


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


    function receiveboatAction()
	{
		$pid = $this->_request->getParam('positionId');
		$uid = $this->check();

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


    function upgradeplantAction()
    {
        $itemId = $this->_request->getParam('itemId');
        $uid = $this->check();

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

    function diyislandAction()
    {
        $uid = $this->check();
        $changesAry = $this->_request->getParam('changes');
        $removesAry = $this->_request->getParam('removes');

        $changesAry = Zend_Json::decode($changesAry);
        $removesAry = Zend_Json::decode($removesAry);

        $result = Hapyfish2_Island_Bll_Island::diyIsland($uid, $changesAry, $removesAry);

        if ($result['resultVo']['status'] > 0) {
        	$logInfo = array('iSource' => 2, 'iCmd' => 205, 'iState' => 0, 'ownerUid' => $uid);
        	$this->echoResultAndLog($result, $logInfo);
        } else {
        	$this->echoResult($result);
        }
    }


    function usecardAction()
	{
	    $uid = $this->check();
		$cid = $this->_request->getParam('cid');
		$itemId = $this->_request->getParam('itemId');
		$onwerUid = $this->_request->getParam('ownerUid');

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

    function readtaskAction()
    {
        $uid = $this->check();
    	$result = Hapyfish2_Island_Bll_Task::readTask($uid);

        $this->echoResult($result);
    }
}