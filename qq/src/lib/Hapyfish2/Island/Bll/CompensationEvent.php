<?php

class Hapyfish2_Island_Bll_CompensationEvent
{
	public static function gain($uid, $id)
	{
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		$compensation->setItem(89232, 1);
		$ok = $compensation->sendOne($uid, '[系统补偿]');
		//金币10000
		
		if ($ok) {
			$info = array(
				'id' => $id,
				'uid' => $uid,
				'create_time' => time()
			);
			try {
				$dalCompensationLog = Hapyfish2_Island_Dal_CompensationLog::getDefaultInstance();
				$dalCompensationLog->insert($uid, $info);
			} catch (Exception $e) {
				info_log($uid . ':' . $id, 'CompensationEvent_Gain');
			}
		}
	}
	
	public static function isGained($uid, $id)
	{
		$result = true;
		
		try {
			$dalCompensationLog = Hapyfish2_Island_Dal_CompensationLog::getDefaultInstance();
			$data = $dalCompensationLog->getOne($uid, $id);
			if ($data) {
				$result = true;
			} else {
				$result = false;
			}
		} catch (Exception $e) {
			
		}
		
		return $result;
	}
}