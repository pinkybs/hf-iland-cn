<?php

class Hapyfish2_Island_Bll_Task_Build_T2087 implements Hapyfish2_Island_Bll_Task_Interface
{
    /**
     * check user task
     *
     * @param int $uid
     * @param int $taskId
     */
    public function check($uid, $taskId)
    {
        $result = array('status' => -1);
        
        //get task info
        $taskInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildTaskInfo($taskId);
        if (!$taskInfo) {
            return $result;
        }

        $isCompleted = Hapyfish2_Island_Cache_Task::isCompletedTask($uid, $taskId);
        if ($isCompleted) {
            $result['content'] = 'serverWord_151';
            return $result;
        }

        $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
        if ($userLevelInfo['level'] < $taskInfo['need_level']) {
            $result['content'] = 'serverWord_153';
            return $result;
        }
        
        $cnt = 0;
        $userUnlockShipCount = Hapyfish2_Island_Cache_Dock::getUnlockShipCount($uid);

    	if (isset($userUnlockShipCount[$taskInfo['need_cid']])) {
			$cnt = $userUnlockShipCount[$taskInfo['need_cid']];
		}

        if ($cnt < $taskInfo['need_num']) {
            $result['content'] = 'serverWord_150';
            return $result;
        }

        $nowTime = time();
        $finishComplete = Hapyfish2_Island_Cache_Task::completeTask($uid, $taskId, $nowTime);
    
        if (!$finishComplete) {
            $result['status'] = -1;
            $result['content'] = 'serverWord_152';
            return $result;
        }

        try {
        	$coinChange = $taskInfo['coin'];
        	$expChange = $taskInfo['exp'];
        	$cardId = $taskInfo['cid'];
        	
            if ($coinChange > 0) {
				Hapyfish2_Island_HFC_User::incUserCoin($uid, $coinChange);
            }
            if ($expChange > 0) {
				Hapyfish2_Island_HFC_User::incUserExp($uid, $expChange);
            }

            if ($cardId) {
				Hapyfish2_Island_HFC_Card::addUserCard($uid, $cardId, 1);
            }

            $result['status'] = 1;
            $result['expChange'] = $expChange;
            $result['coinChange'] = $coinChange;
            $result['taskChange'] = true;
        }
        catch (Exception $e) {
            $result['status'] = -1;
            $result['content'] = 'serverWord_110';
            return $result;
        }
        
        try {
        	$levelUp = Hapyfish2_Island_Bll_User::checkLevelUp($uid);
            $result['levelUp'] = $levelUp['levelUp'];
            $result['islandLevelUp'] = $levelUp['islandLevelUp'];
        	if ($levelUp['feed']) {
            	$result['feed'] = $levelUp['feed'];
            }
        } catch (Exception $e) {
        }

        return $result;
    }

}
