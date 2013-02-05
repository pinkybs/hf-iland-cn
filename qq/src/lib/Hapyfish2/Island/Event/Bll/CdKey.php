<?php

class Hapyfish2_Island_Event_Bll_CdKey
{
	/**
	 * validate cd key
	 * @param : integer uid
	 * @param : string $cdKey
	 * @param : integer id
	 * @param : integer $itemType
	 * @return: integer 1 - OK / -1 NG:cdkey not exist / -2 NG:cdkey used  / -3 NG:use count limit / -4 NG:insert error
	 */
	public static function validCdKey($uid, $cdKey)
	{
		$dalCdKey= Hapyfish2_Island_Event_Dal_CdKey::getDefaultInstance();
		$rowCdKey = $dalCdKey->getCdKey($cdKey);
		
		//check
		if (empty($rowCdKey)) {
			return -1;
		}
		if ($rowCdKey['status']) {
			return -2;
		}
		
		$lstCdKey = $dalCdKey->lstUserCdKey($uid);
		if ($lstCdKey && count($lstCdKey)>=5) {
			return -3;
		}
		
		$time = time();
		//update cdkey
		try {
			$info = array();
			$info['uid'] = $uid;
			$info['cdkey'] = $cdKey;
			$info['create_time'] = $time;
	        $dalCdKey->insertUserCdKey($info);
	        
	        $info1 = array('status' => 1);
	        $dalCdKey->updateCdKey($cdKey, $info1);
		} catch (Exception $e) {
			return -4;
		}
		
		//send event present
		//礼包内容：金币10000；道具卡（加速I，加速II，加速III，2星建设，3星建设，防御卡，保安卡）x5
		$robot = new Hapyfish2_Island_Bll_Compensation();
		$robot->setUid($uid);
		$robot->setFeedTitle('兑换豪礼活动礼包1个');
		$robot->setCoin(10000);
		$robot->setItem(26241, 5);
		$robot->setItem(26341, 5);
		$robot->setItem(26441, 5);
		$robot->setItem(56641, 5);
		$robot->setItem(56741, 2);
		$robot->setItem(26841, 5);
		$robot->setItem(27141, 5);
		$numSend = $robot->send();

		return 1;
	}

}