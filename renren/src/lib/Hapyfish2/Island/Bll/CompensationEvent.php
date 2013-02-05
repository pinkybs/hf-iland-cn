<?php

class Hapyfish2_Island_Bll_CompensationEvent
{
	public static function gain($uid, $id)
	{
		$compensation = new Hapyfish2_Island_Bll_Compensation();

		//双倍经验卡10张
		$compensation->setItem(74841, 10);
		//20宝石
		Hapyfish2_Island_Bll_Gold::add($uid, array('gold' => 20));

		$ok = $compensation->sendOne($uid, '10月22日停服补偿礼包：宝石*20 ');

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