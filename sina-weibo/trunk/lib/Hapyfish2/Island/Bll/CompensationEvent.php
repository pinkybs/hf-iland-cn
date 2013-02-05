<?php

class Hapyfish2_Island_Bll_CompensationEvent
{
	public static function gain($uid, $id)
	{
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		//金币10000
		$compensation->setCoin(50000);
		$compensation->setGold(50);
		//$compensation->setFeedTitle('');
		$ok = $compensation->sendOne($uid, '8.7日服务器异常补偿：');

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