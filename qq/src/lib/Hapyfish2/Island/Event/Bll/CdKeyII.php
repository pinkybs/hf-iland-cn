<?php

class Hapyfish2_Island_Event_Bll_CdKeyII
{
	/**
	 * validate cd key
	 * @param : integer uid
	 * @param : string $cdKey
	 * @param : integer id
	 * @param : integer $itemType
	 * @return: integer 1 - OK / -1 NG:other error / -2 NG:cdkey used  / -3 NG:Invalid cdkey -4 NG:cdkey level has gained
	 */
	public static function validCdKey($uid, $cdKey)
	{
		$cdkeyLv = self::getCdKeyLevel($cdKey);

		try {
			$dalCdKey= Hapyfish2_Island_Event_Dal_CdKeyII::getDefaultInstance();
			$rowCdKey = $dalCdKey->getCdKey($cdkeyLv, $cdKey);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'CdkeyII_Err');
			return -1;
		}

		//check
		if (empty($rowCdKey) || $cdkeyLv > 4 || $cdkeyLv < 1) {
			return -3;
		}
		if ($rowCdKey['status']) {
			return -2;
		}

		//this level has gained
		try {
			$userCdKey = $dalCdKey->getUserCdKey($cdkeyLv, $uid);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'CdkeyII_Err');
			return -1;
		}
		if ($userCdKey) {
			return -4;
		}

		$time = time();
		//update cdkey
		try {
			$info = array();
			$info['uid'] = $uid;
			$info['cdkey'] = $cdKey;
			$info['create_time'] = $time;
	        $dalCdKey->insertUserCdKey($cdkeyLv, $info);

	        $info1 = array('status' => 1);
	        $dalCdKey->updateCdKey($cdkeyLv, $cdKey, $info1);
		} catch (Exception $e) {
			return -1;
		}

		//send event present
		$robot = new Hapyfish2_Island_Bll_Compensation();
		/*初级礼包
			金币		5000
			加速卡II	26341	3
			加速卡III	26441	3
			字母happy new year 一套
			字母A	27721	2
			字母E	28121	2
			字母H	28421	1
			字母N	29021	1
			字母P	29221	2
			字母R	29421	1
			字母W	29921	1
			字母Y	30121	2*/
		if (1 == $cdkeyLv) {
			$robot->setCoin(5000);
			$robot->setItem(26341, 3);
			$robot->setItem(26441, 3);
			$robot->setItem(27721, 2);
			$robot->setItem(28121, 2);
			$robot->setItem(28421, 1);
			$robot->setItem(29021, 1);
			$robot->setItem(29221, 2);
			$robot->setItem(29421, 1);
			$robot->setItem(29921, 1);
			$robot->setItem(30121, 2);
			$robot->setFeedTitle('一级大礼包');
		}
		/*二级礼包
			金币		8000
			加速卡II	26341	5
			加速卡III	26441	5
			3星蛋糕店	1332	1
			字母happy new year 一套*/
		else if (2 == $cdkeyLv) {
			$robot->setCoin(8000);
			$robot->setItem(26341, 5);
			$robot->setItem(26441, 5);
			$robot->setItem(1332, 1);
			$robot->setItem(27721, 2);
			$robot->setItem(28121, 2);
			$robot->setItem(28421, 1);
			$robot->setItem(29021, 1);
			$robot->setItem(29221, 2);
			$robot->setItem(29421, 1);
			$robot->setItem(29921, 1);
			$robot->setItem(30121, 2);
			$robot->setFeedTitle('二级大礼包');
		}
		/*三级礼包
			金币		10000
			加速卡II	26341	5
			加速卡III	26441	5
			3星快餐店	2332	1
			饺子	71521	1
			字母happy new year 一套*/
		else if (3 == $cdkeyLv) {
			$robot->setCoin(10000);
			$robot->setItem(26341, 5);
			$robot->setItem(26441, 5);
			$robot->setItem(2332, 1);
			$robot->setItem(71521, 1);
			$robot->setItem(27721, 2);
			$robot->setItem(28121, 2);
			$robot->setItem(28421, 1);
			$robot->setItem(29021, 1);
			$robot->setItem(29221, 2);
			$robot->setItem(29421, 1);
			$robot->setItem(29921, 1);
			$robot->setItem(30121, 2);
			$robot->setFeedTitle('三级大礼包');
		}
		/*四级礼包
			金币		20000
			加速卡II	26341	8
			加速卡III	26441	8
			新年天空	67712	1
			白雪岛	27411	1
			3星建设卡	56741	1
			字母happy new year 一套*/
		else {
			$robot->setCoin(20000);
			$robot->setItem(26341, 8);
			$robot->setItem(26441, 8);
			$robot->setItem(67712, 1);
			$robot->setItem(27411, 1);
			$robot->setItem(56741, 1);
			$robot->setItem(27721, 2);
			$robot->setItem(28121, 2);
			$robot->setItem(28421, 1);
			$robot->setItem(29021, 1);
			$robot->setItem(29221, 2);
			$robot->setItem(29421, 1);
			$robot->setItem(29921, 1);
			$robot->setItem(30121, 2);
			$robot->setFeedTitle('四级大礼包');
		}
		$ok = $robot->sendOne($uid, '恭喜获得兔年上上签：');
		if ($ok) {
			return $cdkeyLv;
		}
		else {
			return -1;
		}
	}

	public static function getCdKeyLevel($cdkey)
	{
		return substr($cdkey, -3, 1);
	}

	public static function isRegularCdkey($cdkey)
    {
    	if (strlen($cdkey) != 17) {
    		return false;
    	}

    	$aryCdkey = str_split($cdkey);
    	$tail = 0;
    	$aryChar = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
    	foreach ($aryCdkey as $idx=>$char) {
    		if (!in_array($char, $aryChar)) {
    			return false;
    		}
    		if ($idx<15) {
    			$tail += hexdec($char);
    		}
    	}
    	return dechex($tail % 256) == substr($cdkey, -2);
    }


	public static function createCdkey($no, $num)
	{
		try {
			$dalCdKey= Hapyfish2_Island_Event_Dal_CdKeyII::getDefaultInstance();
			for ($i=1; $i<=$num; $i++) {
				$tm = microtime(true);
				$cdkey = md5($i.$tm.APP_KEY);
				$cdkey = substr($cdkey,10,16);
				$cdkeyNew = self::_rebuildCdkey($no, $cdkey);
				$dalCdKey->insertCdKey($no, $cdkeyNew);
			}
		} catch (Exception $e) {
			info_log($e->getMessage(), 'CdkeyII_Err');
		}
		return $num;
	}



	private static function _rebuildCdkey($no, $cdkey)
    {
    	$aryCdkey = str_split($cdkey);
    	$tail = 0;
    	foreach ($aryCdkey as $idx=>$char) {
    		if ($idx<14) {
    			$tail += hexdec($char);
    		}
    	}
    	$tail += hexdec($no);
    	$newCdkey = substr($cdkey,0,14) . $no . dechex($tail % 256);
    	return $newCdkey;
    }
}