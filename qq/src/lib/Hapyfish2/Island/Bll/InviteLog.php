<?php

class Hapyfish2_Island_Bll_InviteLog
{
	public static function add($uid, $fid)
	{
		$ok = false;
		$t = time();
		$info = array(
			'uid' => $uid,
			'fid' => $fid,
			'time' => $t
		);
		
		//成就任务完成判断
        try {
			Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_18', 1);
			
			//task id 3034,task type 18
			Hapyfish2_Island_Bll_Task::checkTask($uid, 3034);
        } catch (Exception $e) {
        }
        
		try {
			$dalLog = Hapyfish2_Island_Dal_InviteLog::getDefaultInstance();
			$dalLog->insert($uid, $info);
			
			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
			$dalUser->update($fid, array('inviter' => $uid));
			
			$ok = true;
			
			//2011-01-24 00:00:00 结束
			if ($t >= 1295020800 && $t < 1295798400) {
				//2011-01-15 00:00:00 开始统计
				$time1 = 1295020800;
				$count = $dalLog->getCountByTime($uid, $time1);
				if ($count == 5) {
					//送3颗海星
					Hapyfish2_Island_Bll_StarFish::add($uid, 3, '成功邀请5名好友获得3个海星', $t);
				}
			}
			
		} catch (Exception $e) {
			
		}
		
		return $ok;
	}
	
	public static function getAll($uid)
	{
		try {
			$dalLog = Hapyfish2_Island_Dal_InviteLog::getDefaultInstance();
			return $dalLog->getAll($uid);
		} catch (Exception $e) {
		}
		
		return null;
	}
	
	public static function getAllOfFlow($uid)
	{
		//2011-01-18 16:00:00 开始
		$time = 1300204800;
		try {
			$dalLog = Hapyfish2_Island_Dal_InviteLog::getDefaultInstance();
			return $dalLog->getAllByTime($uid, $time);
		} catch (Exception $e) {
		}
		
		return null;
	}
}