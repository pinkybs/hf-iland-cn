<?php

/**
 * Event Casino
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2008 Happyfish Inc.
 * @create     2011/05/10    Nick
*/
class Hapyfish2_Island_Event_Bll_Casino
{
    /**
     * get user point
     *
     */
	public static function getUserPoint($uid)
	{
		$key = 'i:u:casinop:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		
		$data = $cache->get($key);
		if ($data === false) {
			try {
	        	$dalCasino = Hapyfish2_Island_Event_Dal_Casino::getDefaultInstance();
				$data = $dalCasino->getUserPoint($uid);
			
				if ( empty($data) ) {
					$data = 0;
				}
				
	            if ( $data != null ) {
	                $cache->add($key, $data);
	            }
			} catch (Exception $e) {
				return 0;
			}
		}
		return $data;
	}

	public static function updateUserPoint($uid, $point, $nowPoint)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_Casino::getDefaultInstance();
			$db->updateUserPoint($uid, $point);
			
			$key = 'i:u:casinop:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        $cache->set($key, $nowPoint);
		} catch(Exception $e) {
			
		}
	}
	
    /**
     * get point change list
     *
     */
	public static function getPointChangeList()
	{
        $key = 'event:pointchalist';
		$EventFeed = Hapyfish2_Cache_Factory::getEventFeed();
		$pointChangeList = $EventFeed->get($key);
		return $pointChangeList;
	}
	
	public static function addUserPointChangeInfo($changeInfo)
	{
        $key = 'event:pointchalist';
		$EventFeed = Hapyfish2_Cache_Factory::getEventFeed();
		$info = array($changeInfo['name'], $changeInfo['create_time'], $changeInfo['giftName']);
    	$EventFeed->insert($key, $info, 0, 10);
	}
	
	/**
	 * change casino
	 *
	 * @param $uid
	 * @return array
	 */
	public static function changeCasino($uid, $point)
	{
		$result = array('status' => -1);

		$myPoint = self::getUserPoint($uid);
		if ( $myPoint < $point ) {
			$result['content'] = '您的积分不够兑换此物品哦~';
			return $result;
		}

		$lstPointGift = self::getPointChagceGiftList();
		if ( !isset($lstPointGift[$point]) ) {
			$result['content'] = '您选择的兑换物品有误，请重新选择。';
			return $result;
		}

		$now = time();

		if ( $point == 50 ) {
			$newCard1 = array('uid' => $uid,'cid' => 67441,'count' => 6,'buy_time' => $now,'item_type' => 41);
			$newCard2 = array('uid' => $uid,'cid' => 67541,'count' => 1,'buy_time' => $now,'item_type' => 41);
            //add user card
            Hapyfish2_Island_HFC_Card::addUserCard($uid, $newCard1['cid'], $newCard1['count']);
            Hapyfish2_Island_HFC_Card::addUserCard($uid, $newCard2['cid'], $newCard2['count']);
            
			$feed = '恭喜您用积分兑换了<font color="#FF0000">一键收取卡6张、超级防御卡1张</font>！';
			$name = '初级道具卡包';
		}
		else if ( $point == 100 ) {
			$newCard1 = array('uid' => $uid,'cid' => 67441,'count' => 12,'buy_time' => $now,'item_type' => 41);
			$newCard2 = array('uid' => $uid,'cid' => 67541,'count' => 3,'buy_time' => $now,'item_type' => 41);
            //add user card
            Hapyfish2_Island_HFC_Card::addUserCard($uid, $newCard1['cid'], $newCard1['count']);
            Hapyfish2_Island_HFC_Card::addUserCard($uid, $newCard2['cid'], $newCard2['count']);
            
			$feed = '恭喜您用积分兑换了<font color="#FF0000">一键收取卡12张、超级防御卡3张</font>！';
			$name = '高级道具卡包';
		}
		else if ( $point == 1000 ) {
			$newBuilding = array('uid' => $uid,
                                         'bgid' => 68911,
                                         'buy_time'=> $now,
                                         'item_type' => 11);
			
            $newBuilding2 = array('uid' => $uid,
                                         'bgid' => 68813,
                                         'buy_time'=> $now,
                                         'item_type' => 13);
            //add user background
            Hapyfish2_Island_Cache_Background::addNewBackground($uid, $newBuilding);
            Hapyfish2_Island_Cache_Background::addNewBackground($uid, $newBuilding2);
            
			$feed = '恭喜您用积分兑换了<font color="#FF0000">咖啡岛、咖啡海组合</font>！';
			$name = '咖啡岛、咖啡海';
		}
		else {
			$pointGift = $lstPointGift[$point]['bid'];
			$itemType = substr($pointGift, -2, 2);
			if ( $itemType == 21 ) {
	            //$dalBuilding = Dal_Island_Building::getDefaultInstance();
	            $b1 = array('uid' => $uid, 'cid' => $pointGift, 'item_type' => $itemType, 'status' => 0, 'buy_time' => $now);
	            //$dalBuilding->addUserBuilding($b1);
            	Hapyfish2_Island_HFC_Building::addOne($uid, $b1);
            	
	            //$buildingInfo = Hapyfish_Island_Cache_Shop::getBuildingById($pointGift);
            	$buildingInfo = Hapyfish2_Island_Cache_BasicInfo::getBuildingInfo($pointGift);
	            $feed = '恭喜您用积分兑换了<font color="#FF0000">'.$buildingInfo['name'].'</font>！';
	            $name = $buildingInfo['name'];
			}
			else if ( $itemType == 31 ||  $itemType == 32 ) {
				$itemId = substr($pointGift, 0, -2);
            	$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($pointGift);
            	
	            //$dalPlant = Dal_Island_Plant::getDefaultInstance();
	            $p1 = array('uid' => $uid, 'cid' => $pointGift, 'status' => 0, 'item_id' => $itemId, 'level' => $plantInfo['level'], 'buy_time' => $now, 'item_type' => $itemType);
	            
	            //$dalPlant->insertUserPlant($p1);
	            Hapyfish2_Island_HFC_Plant::addOne($uid, $p1);
	            
	            //$plantInfo = Hapyfish_Island_Cache_Shop::getPlantById($pointGift);
	            $feed = '恭喜您用积分兑换了<font color="#FF0000">'.$plantInfo['name'].'</font>！';
	            $name = $plantInfo['name'];
			}
			else if ( $itemType == 41 ) {
				$newCard = array(
	        		'uid' => $uid,
					'cid' => $pointGift,
	        		'count' => $lstPointGift[$point]['count'],
					'buy_time' => $now,
					'item_type' => $itemType);
				//add user card
				//$dalCard = Dal_Island_Card::getDefaultInstance();
	            //$dalCard->addUserCard($newCard);
            	Hapyfish2_Island_HFC_Card::addUserCard($uid, $newCard['cid'], $newCard['count']);
	            
	            //card info
	        	//$cardInfo = Hapyfish_Island_Cache_Shop::getCardById($pointGift);
	        	$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($pointGift);
	        	$feed = '恭喜您用积分兑换了<font color="#FF0000">'.$cardInfo['name']. $lstPointGift[$point]['count'] .'张</font>！';
	        	$name = $cardInfo['name']. $lstPointGift[$point]['count'] . '张';
			}
		}

		$result['userPoint'] = $myPoint - $point;
        self::updateUserPoint($uid, -$point, $result['userPoint']);
        
		$info = Hapyfish2_Platform_Bll_User::getUser($uid);
		
        $userPointInfo = array('uid' => $uid,
        					   'name' => $info['name'],
        					   'giftName' => $name,
        					   'create_time' => $now);
        
        /*$dalCasino = Dal_Casino_Casino::getDefaultInstance();
        $dalCasino->addUserPointChangeInfo($userPointInfo);*/
        self::addUserPointChangeInfo($userPointInfo);
        
        
        $minifeed = array('uid' => $uid,
                          'template_id' => 0,
                          'actor' => $uid,
                          'target' => $uid,
                          'title' => array('title' => $feed),
                          'type' => 6,
                          'create_time' => $now);
        Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);

        $result['status'] = 1;
        return $result;
	}

	public static function getPointChagceGiftList()
	{
		/*
		10	67441	1	一键收取卡1张
		30	67441	3	一键收取卡3张
		50	"67441/67541"	5	一键收取卡6张+超级防御卡1张
		100	"67441/67541 "	10	一键收取卡12张+超级防御卡3张
		200	60021	1	DJ鸭子
		300	55721	1	踏板摩托
		600	55821	1	巴士
		1000	"68911/68813"	1	咖啡岛、咖啡海组合
		1500	63031	1	花园
		2000	68132	1	狮子舞
		2500	67631	1	大圣诞树
		3000	71731	1	圣诞老人
		*/
		$aryGift = array();
		$aryGift[10] = array('point'=> 10,  'bid'=>'67441', 'count'=>1);
		$aryGift[30] = array('point'=> 30,  'bid'=>'67441', 'count'=>3);
		$aryGift[50] = array('point'=> 50,  'bid'=>'67441/67541', 'count'=>7);//***
		$aryGift[100] = array('point'=> 100, 'bid'=>'67441/67541', 'count'=>15);//***
		$aryGift[200] = array('point'=> 200, 'bid'=>'60021', 'count'=>1);
		$aryGift[300] = array('point'=> 300, 'bid'=>'55721', 'count'=>1);
		$aryGift[600] = array('point'=> 600, 'bid'=>'55821', 'count'=>1);
		$aryGift[1000] = array('point'=> 1000, 'bid'=>'68911/68813', 'count'=>2);//***
		$aryGift[1500] = array('point'=> 1500, 'bid'=>'63031', 'count'=>1);
		$aryGift[2000] = array('point'=> 2000, 'bid'=>'68132', 'count'=>1);
		$aryGift[2500] = array('point'=> 2500, 'bid'=>'67631', 'count'=>1);
		$aryGift[3000] = array('point'=> 3000, 'bid'=>'71731', 'count'=>1);

		return $aryGift;
	}
	
}