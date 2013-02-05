<?php

class Hapyfish2_Island_Event_Bll_Newyear
{

	const TXT001 = '获得新年礼物：';
	const TXT002 = '金币';
	const TXT003 = '成功领取礼品：';
	const TXT004 = '成功兑换礼品：';
	const TXT005 = '恭喜你获得了新年礼物 ';
	const TXT006 = '对不起，今天的礼物你已经领取过了哦';
	const TXT007 = '对不起，你还没有购买财神庙哦';
	const TXT008 = '爆竹';
	const TXT009 = '红包';
	const TXT011 = '成功购买特卖商品';
	const TXT012 = '七彩阁';
	const TXT013 = '福星馆';
	const TXT014 = '禄星馆';
	const TXT015 = '寿星馆';

	public static function getUserNewyear($uid)
	{
		$resultVo = array('status' => 1);
		try {
			$dalNewyear = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
			$rowNewyear = $dalNewyear->get($uid);
		}
		catch (Exception $e) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			info_log($e->getMessage(), 'Event_Newyear_Err');
			return array('result' => $resultVo);
		}

		$result = array('result' => $resultVo);
		if (empty($rowNewyear)) {
			try {
				$dalNewyear->addUserNewyear($uid, 'red_paper', 1);
				$result['brideNum'] = 1;
			}
			catch (Exception $e) {
				info_log($e->getMessage(), 'Event_Newyear_Err');
				$result['brideNum'] = 0;
			}
			$result['fireworksNum'] = 0;
		}
		else {
			$result['brideNum'] = $rowNewyear['red_paper'];
			$result['fireworksNum'] = $rowNewyear['red_cracker'];
		}
		$aryStatus = self::getExchangeTreasureStatus($uid);
		$result['JiaoZi'] = $aryStatus['71521'];
		$result['Fu'] = $aryStatus['72431'];
		$result['Lu'] = $aryStatus['72531'];
		$result['Shou'] = $aryStatus['72631'];
		return $result;
	}

	/**
	 * open red package
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function openRedPaper($uid)
	{
		$resultVo = array('status' => 1);
		$now = time();

		try {
			//check red paper enough
			$dalNewyear = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
			$rowNewyear = $dalNewyear->get($uid);
			if (empty($rowNewyear) || $rowNewyear['red_paper'] < 1) {
				$resultVo['content'] = 'serverWord_302';
				$resultVo['status'] = '-1';
				return array('result' => $resultVo);
			}
		}
		catch (Exception $e) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			info_log($e->getMessage(), 'Event_Newyear_Err');
			return array('result' => $resultVo);
		}

		//get rand item basic
		$lstItem = Hapyfish2_Island_Cache_LotteryItemOdds::getLotteryItemOddsList(1);
		$aryItem = self::getItemArray($lstItem);

		//get random item key
		$aryRandOdds = array();
		foreach ($aryItem as $data) {
			$key = $data['order'];
			$aryRandOdds[$key] = $data['item_odds'];
		}
		$gainKey = self::randomKeyForOdds($aryRandOdds);
        $gainItem = $aryItem[$gainKey];

        $compensation = new Hapyfish2_Island_Bll_Compensation();
        //金币item_type=1 | 宝石 item_type=2 | 红包 item_type=3
        if (1 == $gainItem['item_type']) {
        	//金币3000
			$compensation->setCoin($gainItem['item_num']);
			$gainVal = $gainItem['item_num'];
			$gain = 'coin';
        }
		else if (3 == $gainItem['item_type']) {
        	$gainVal = $gainItem['item_id'];
			$gain = 'specialId';
        }
		else {
			$compensation->setItem($gainItem['item_id'], $gainItem['item_num']);
        	$gainVal = $gainItem['item_id'];
			$gain = 'itemId';
        }

        //send gain item
		if (3 == $gainItem['item_type']) {
			try {
				$dalNewyear->updateByField($uid, 'red_cracker', $gainItem['item_num']);
				$ok = true;
			}
			catch (Exception $e){
				info_log($e->getMessage(), 'Event_Newyear_Err');
				$ok = false;
			}
	        if ($ok) {
	        	$title = self::TXT001 . self::TXT008;
		        $minifeed = array('uid' => $uid,
		                          'template_id' => 0,
		                          'actor' => $uid,
		                          'target' => $uid,
		                          'title' => array('title' => $title),
		                          'type' => 3,
		                          'create_time' => $now);
		        Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
	        }
		}
		else {
			$ok = $compensation->sendOne($uid, self::TXT001);
		}

		if ($ok) {
			try {
				//update card count -1
				$dalNewyear->updateByField($uid, 'red_paper', -1);
			}
			catch (Exception $e){
				info_log($e->getMessage(), 'Event_Newyear_Err');
			}
		}
		else {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			info_log('$compensation->sendOne Error:', 'Event_Newyear_Err');
			info_log(Zend_Json::encode($gainItem), 'Event_Newyear_Err');
			return array('result' => $resultVo);
		}

		return array('result' => $resultVo, $gain => $gainVal, 'num' =>$gainItem['item_num']);
	}

	/**
	 * get item array
	 *
	 * @param array $aryItem
	 * @return array
	 */
	private static function getItemArray($aryItem)
	{
		$aryRet = array();
		foreach ($aryItem as $data) {
			$itemKey = $data['order'];
			$aryRet[$itemKey] = $data;
		}
        return $aryRet;
	}

	//$aryKeys = array(5=>5,6=>45,7=>10,8=>40);
	/**
	 * generate random by key=>odds
	 *
	 * @param array $aryKeys
	 * @return integer
	 */
	private static function randomKeyForOdds($aryKeys)
	{
		$tot = 0;
		$aryTmp = array();
		foreach ($aryKeys as $key => $odd) {
			$tot += $odd;
			$aryTmp[$key] = $tot;
		}
		$rnd = mt_rand(1,$tot);

		foreach ($aryTmp as $key=>$value) {
			if ($rnd <= $value) {
				return $key;
			}
		}
	}

	public static function isGainTreasure($uid)
	{
		try {
			$dalNewyear = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
			$rowNewyear = $dalNewyear->get($uid);
			$gained = !empty($rowNewyear['gain_treasure']);
		}
		catch (Exception $e) {
			info_log($e->getMessage(), 'Event_Newyear_Err');
			$gained = false;
		}
		return $gained;
	}

	/**
	 * exchange bombs
	 *
	 * @param integer $uid
	 * @param integer $changeType [1,2,3]
	 * @return array
	 */
	public static function exchangeCracker($uid, $changeType=1)
	{
		$resultVo = array();
		$resultVo = array('status' => 1);

		if ( !(1 == $changeType || 2 == $changeType || 3 == $changeType) ) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			return array('result' => $resultVo);
		}

		try {
			$dalNewyear = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
			$rowNewyear = $dalNewyear->get($uid);
			if (empty($rowNewyear)) {
				$resultVo['status'] = '-1';
				$resultVo['content'] = 'serverWord_150';
				return array('result' => $resultVo);
			}
		}
		catch (Exception $e) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			info_log($e->getMessage(), 'Event_Newyear_Err');
			return array('result' => $resultVo);
		}

		//1、5个兑换禄星    2、10兑换寿星   3、15兑换福星   4、福星+禄星+寿星+饺子（充值送）兑换  七彩阁
		//724	福星馆	725	禄星馆	726	寿星馆
		if (1 == $changeType) {
			$needCnt = 5;
			$gainItemId = '72531';
			$gainName = self::TXT014;
		}
		else if (2 == $changeType) {
			$needCnt = 10;
			$gainItemId = '72631';
			$gainName = self::TXT015;
		}
		else {
			$needCnt = 15;
			$gainItemId = '72431';
			$gainName = self::TXT013;
		}

		if ($rowNewyear['red_cracker'] < $needCnt) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_150';
			return array('result' => $resultVo);
		}

		//send item to user
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		$compensation->setItem($gainItemId, 1);
		$ok = $compensation->sendOne($uid, self::TXT004);
		$now = time();
		if ($ok) {
			try {
				//update cracker count
	        	$dalNewyear->updateByField($uid, 'red_cracker', -$needCnt);
	        	//update exchange log
		        $dalExg = Hapyfish2_Island_Event_Dal_NewyearExchange::getDefaultInstance();
		        $dalExg->insert($uid, $changeType);
				//cache feed
		        $info = Hapyfish2_Platform_Bll_Factory::getUser($uid);
				$mkey = 'event_newyear_exchange_list';
				$eventFeed = Hapyfish2_Cache_Factory::getEventFeed();
				$eventFeed->insert($mkey, array($info['nickname'], $gainName, $now));
			}
			catch (Exception $e) {
				info_log($e->getMessage(), 'Event_Newyear_Err');
			}
		}
		else {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			return array('result' => $resultVo);
		}

		//create result vo
		return array('result' => $resultVo, 'itemId' => $gainItemId);
	}

	/**
	 * gain Treasure
	 *
	 * @param integer $uid
	 * @param integer $changeType [1,2,3,4]
	 * @return array
	 */
	public static function gainTreasure($uid)
	{
		$resultVo = array();
		$resultVo = array('status' => 1);

		try {
			$dalNewyear = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
			$rowNewyear = $dalNewyear->get($uid);
			if (empty($rowNewyear)) {
				$resultVo['status'] = '-1';
				$resultVo['content'] = 'serverWord_150';
				return array('result' => $resultVo);
			}
		}
		catch (Exception $e) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			info_log($e->getMessage(), 'Event_Newyear_Err');
			return array('result' => $resultVo);
		}

		//can get
		if (!empty($rowNewyear['gain_treasure']) || !self::canExchangeTreasure($uid)) {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_150';
			return array('result' => $resultVo);
		}
		$gainItemId = '72731';
		$now = time();
		//send item to user
		$compensation = new Hapyfish2_Island_Bll_Compensation();
		$compensation->setItem($gainItemId, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);

		if ($ok) {
			try {
				$dalNewyear->update($uid, array('gain_treasure'=>$now));
			}
			catch (Exception $e) {
				info_log($e->getMessage(), 'Event_Newyear_Err');
			}
			//cache feed
			$info = Hapyfish2_Platform_Bll_Factory::getUser($uid);
			$userName = $info['nickname'];
			$itemName = self::TXT012;
			$mkey = 'event_newyear_exchange_list';
			$eventFeed = Hapyfish2_Cache_Factory::getEventFeed();
			$eventFeed->insert($mkey, array($userName, $itemName, $now));
		}
		else {
			$resultVo['status'] = '-1';
			$resultVo['content'] = 'serverWord_110';
			return array('result' => $resultVo);
		}

		//create result vo
		return array('result' => $resultVo, 'itemId' => $gainItemId);
	}

    public static function canExchangeTreasure($uid)
    {
    	$result = self::getExchangeTreasureStatus($uid);

    	$rtn = false;
    	if ($result['71521'] > 0
    		&& ($result['72431']>0 || $result['75231']>0)
    		&& ($result['72531']>0 || $result['75331']>0)
    		&& ($result['72631']>0 || $result['75131']>0) ) {
			$rtn = true;
    	}

    	return $rtn;
    }

	public static function getExchangeTreasureStatus($uid)
    {
    	//福星+禄星+寿星+饺子（充值送）兑换  72731七彩阁
		//724	福星馆	725	禄星馆	726	寿星馆	715 饺子
		//752	金福星	753	金禄星	751	金寿星
    	$result = array('72431' => 0, '72531' => 0, '72631' => 0,
    					'75231' => 0, '75331' => 0, '75131' => 0, '71521' => 0);

    	$buildings = Hapyfish2_Island_HFC_Building::getAll($uid);
    	if ($buildings) {
	    	foreach($buildings as $item) {
	    		if ($item['cid'] == 71521) {
	    			$result['71521'] += 1;
	    			break;
	    		}
	    	}
    	}

    	$plants = Hapyfish2_Island_HFC_Plant::getAll($uid);
    	if ($plants) {
    		foreach($plants as $item) {
	    		if ($item['cid'] == 72431) {
	    			$result['72431'] += 1;
	    		}
    			if ($item['cid'] == 72531) {
	    			$result['72531'] += 1;
	    		}
    			if ($item['cid'] == 72631) {
	    			$result['72631'] += 1;
	    		}
    			if ($item['cid'] == 75231) {
	    			$result['75231'] += 1;
	    		}
    			if ($item['cid'] == 75331) {
	    			$result['75331'] += 1;
	    		}
    			if ($item['cid'] == 75131) {
	    			$result['75131'] += 1;
	    		}
	    	}
    	}

    	return $result;
    }

    public static function addRedPaper($uid, $num)
    {
    	if ($num <= 0) {
    		return false;
    	}
    	try {
    		$dal = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
			$dal->addUserNewyear($uid, 'red_paper', $num);
    	}
		catch (Exception $e) {
			info_log('addRedPaper error', 'Event_Newyear_Err');
			info_log($e->getMessage(), 'Event_Newyear_Err');
			return false;
		}
		return true;
    }


    /**
	 * get wealth god gift every day
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function openWealthGod($uid)
	{
		$resultVo = array('status' => 1);
		$now = time();

		$mkey = 'i:u:wealthgoddly:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $gainDate = $cache->get($mkey);
		$nowDate = date('Ymd');
		//has gained today's gift
		if ($gainDate && $gainDate == $nowDate) {
			$resultVo['status'] = -1;
	        $resultVo['content'] = self::TXT006;
	        return array('result' => $resultVo);
		}

		//has this plant
		$maxBid = 0;
		$plants = Hapyfish2_Island_HFC_Plant::getAll($uid);
    	if ($plants) {
    		foreach($plants as $item) {
	    		if ($item['cid'] == 70431 || $item['cid'] == 70531 || $item['cid'] == 70631) {
	    			$maxBid = $item['cid'] > $maxBid ? $item['cid'] : $maxBid;
	    		}
	    	}
    	}

		if (empty($maxBid)) {
			$resultVo['status'] = -1;
	        $resultVo['content'] = 'serverWord_303';
	        return array('result' => $resultVo);
		}

		//bomb count gain
		$gainRedPaperCnt = 1;
		if (70531 == $maxBid) {
			$gainRedPaperCnt = 2;
		}
		else if (70631 == $maxBid) {
			$gainRedPaperCnt = 3;
		}
		//gain red paper
		//update red paper count
       	try {
       		$dalNewyear = Hapyfish2_Island_Event_Dal_Newyear::getDefaultInstance();
       		$dalNewyear->updateByField($uid, 'red_paper', $gainRedPaperCnt);
       	}
		catch (Exception $e) {
			info_log($e->getMessage(), 'Event_Newyear_Err');
			info_log('openWealthGod-update user red_paper cnt error', 'Event_Newyear_Err');
			$resultVo['status'] = -1;
	        $resultVo['content'] = 'serverWord_110';
	        return array('result' => $resultVo);
		}

       	//send feed
       	$title = self::TXT001 . self::TXT009 .' X' .$gainRedPaperCnt;
	    $minifeed = array('uid' => $uid,
	                          'template_id' => 0,
	                          'actor' => $uid,
	                          'target' => $uid,
	                          'title' => array('title' => $title),
	                          'type' => 3,
	                          'create_time' => $now);
	    Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);

        //save cache
        $cache->set($mkey, $nowDate);

		$aryResult = array();
		$aryResult['result'] = $resultVo;
		$aryResult['num'] = $gainRedPaperCnt;
		$aryResult['millionDollar'] = 0;
		return $aryResult;
	}

}