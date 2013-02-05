<?php

class MovedataController extends Zend_Controller_Action
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

	public function movedataAction()
	{
		$params = $this->_request->getParams();

		//确认是否可以进行数据迁移
		$ok = 0;
		$ok = Hapyfish2_Island_Bll_MoveData::getMoveData($params);
		if ($ok) {
			$this->echoError('-1', 'repeat data');
		}
		
		$oldRest = Hapyfish2_Rest_Factory::getRest($params['selectApi']);
		if (!$oldRest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$oldRest->setUser($this->cuid);

		try {
			$oldUserInfo = $oldRest->getUserInfoAllMD($params['old_uid']);
			$oldUserAchievement = $oldRest->getUserAchievementMD($params['old_uid']);
			$oldUserBackground = $oldRest->getUserBackgroundMD($params['old_uid']);
			$oldUserBuilding = $oldRest->getUserBuildingMD($params['old_uid']);
			$oldUserPlant = $oldRest->getUserPlantMD($params['old_uid']);
			$oldUserCard = $oldRest->getUserCardMD($params['old_uid']);
			$oldUserIsland = $oldRest->getUserIslandMD($params['old_uid']);
			$oldUserDock = $oldRest->getUserDockMD($params['old_uid']);
		} catch (Exception $e) {
			info_log($e, 'infoError');
			$this->echoError($e->getCode(), $e->getMessage());
		}
		
		$newRest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$newRest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$newRest->setUser($this->cuid);

		$result = array();

		try {	
			//user info
			$newRest->updateUserInfoMD($params['uid'], $oldUserInfo);
			
			//user Achievement
			$newRest->updateUserAchievementMD($params['uid'], $oldUserAchievement);
			
			//user island
			$island = array('uid' => $params['uid'], 'unlock_island' => $oldUserIsland);
			$newRest->updateUserIslandMD($island);
				
			//user background			
			$newRest->updateUserBackgroundMD($params['uid'], $oldUserBackground);
			
			//user building
			$newRest->updateUserBuildingMD($params['uid'], $oldUserBuilding);
	
			//user plant
			$newRest->updateUserPlantMD($params['uid'], $oldUserPlant);
	
			//user card
			$cid = array();
			$count = array();
			foreach ($oldUserCard as $cardData) {
				$cid[] = $cardData['cid'];
				$count[] = $cardData['count'];
			}
			
			if ((count($cid) > 0) && (count($count) > 0) && (count($cid) == count($count))) {
				$newRest->updateUserCardMD($params['uid'], $cid, $count);
			}

			//user dock
			$position_id = array();
			$unlock_ship_ids = array();
			foreach ($oldUserDock as $dock) {
				$position_id[] = $dock['position_id'];
				$unlock_ship_ids[] = $dock['unlock_ship_ids'];
			}
			
			$unlock_ship_idsStr = join('*', $unlock_ship_ids);
			
			$unlock_ship_idsArr = array('unlock_ship' => $unlock_ship_idsStr);	
			$newRest->updateUserDockMD($params['uid'], $position_id, $unlock_ship_idsArr);
		} catch (Exception $e) {
			info_log($e, 'infoErrorEx');
			$this->echoError($e->getCode(), $e->getMessage());
		}
		
		//记录用户迁移
		Hapyfish2_Island_Bll_MoveData::incMoveData($params);
		
		$result['data'] = 1;
		
		$this->echoResult($result);
	}

}