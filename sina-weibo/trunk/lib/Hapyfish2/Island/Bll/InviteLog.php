<?php

class Hapyfish2_Island_Bll_InviteLog
{
	public static function add($uid, $fid, $t = null)
	{
		$ok = false;
		if (!$t) {
			$t = time();
		}
		$info = array(
			'uid' => $uid,
			'fid' => $fid,
			'time' => $t
		);

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
		//2011-02-18  开始1297958400 2011 03 09
		$time = 1299654498;
		try {
			$dalLog = Hapyfish2_Island_Dal_InviteLog::getDefaultInstance();
			return $dalLog->getAllByTime($uid, $time);
		} catch (Exception $e) {
		}

		return null;
	}
}