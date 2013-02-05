<?php
/**
 * lei.wu,
 * lei.wu@hapyfish.com
 * */
class Hapyfish2_Island_Event_Bll_Timegift
{
	
	public static function setup($uid) 
	{


		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key 	= 'event_timegift_' . $uid;
		$val 	= array();
		$val['state'] = 0;
		
		$val['time_at'] = time();
		
		$cache->set($key, $val, Hapyfish2_Cache_Memcached::LIFE_TIME_ONE_DAY*10);
		
		$tg = Hapyfish2_Island_Event_Dal_Timegift::getDefaultInstance();
		
		$tg->setup($uid);
		
		
	}
	
	public static function gettime($uid) 
	{
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		if( $val['state'] >= 6 || empty($val) ) {
			// 任务已经领取完
			$result['state'] = '-1';
			$result['content'] = 'serverWord_201'; // #todo 错误代码未改
			return $result;
		}
		
		$result['state'] = $val['state'];
		$result['time_at'] = $val['time_at'];
		
		return $result;
		
	}
	
	public static function receive( $uid ) 
	{
		
		/*
		 memcache 数据结构
		 hash 	time_at		上次时间
		 		state		第几次领取物品 
		 */
		
		//$h = array('1'=>1, '2'=>1,'3'=>1,'4'=>1,'5'=>1,'6'=>1);			// 测试时间,1分钟
		$h = array('0' => 0,'1'=>5, '2'=>10,'3'=>20,'4'=>30,'5'=>40,'6'=>60);	// 正确时间
		$now = time();
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		
		
		if( $val['state'] >= 6 || empty($val) ) {
			// 任务已经领取完
			$result['state'] = '-1';
			$result['content'] = 'serverWord_201'; // #todo 错误代码未改
			return $result;
		}
	
		if( $now < ($val['time_at'] + ($h[$val['state']] * 60)) ) {
			// 时间未到
			$result['state'] = '-1';
			$result['content'] = 'serverWord_200'; // #todo 错误代码未改
			return $result;
		}
	
		try {
			
			switch( $val['state'] ) {
				case 0 :
						/*
						$dalBuilding = Dal_Island_Building::getDefaultInstance();
                		$bd = array('uid' => $uid, 'bid' => 7221, 'item_type' => 21, 'status' => 0, 'buy_time' => $now);
                		$dalBuilding->addUserBuilding($bd);
                		*/
						$info = array('uid'=>$uid, 'item_data'=>'7221*1', 'type'=>'5');
                		
						Hapyfish2_Island_Bll_GiftPackage::getNewUserGift($info);
                		
                		$result['state'] = '1';
                		$result['cids'] = array('7221');
                		$result['time_at'] = $now;
						break;
				case 1 :
						/*
						$dalCard = Dal_Island_Card::getDefaultInstance();
						$newCard = array( 'uid' => $uid, 'cid' => 26341, 'count' => 1, 'buy_time' => $now, 'item_type' => 41);
			            $dalCard->addUserCard($newCard); 
			            */
						$info = array('uid'=>$uid, 'item_data'=>'26341*1', 'type'=>'10');
                		Hapyfish2_Island_Bll_GiftPackage::getNewUserGift($info);
			            $result['state'] = '2';
                		$result['cids'] = array('26341');
                		$result['time_at'] = $now;
						break;
				case 2 :
						/*
						$dalPlant = Dal_Island_Plant::getDefaultInstance();
		                $p1 = array('uid' => $uid, 'bid' => 1132, 'status' => 0, 'item_id' => 11, 'buy_time' => $now, 'item_type' => 32);
		                $dalPlant->insertUserPlant($p1);
		                */
						$info = array('uid'=>$uid, 'item_data'=>'1132*1', 'type'=>'20');
                		Hapyfish2_Island_Bll_GiftPackage::getNewUserGift($info);
		                $result['state'] = '3';
                		$result['cids'] = array('1132'); 
                		$result['time_at'] = $now;
						break;
				case 3 :
						/*
						$dalCard = Dal_Island_Card::getDefaultInstance();
						$newCard = array( 'uid' => $uid, 'cid' => 26441, 'count' => 1, 'buy_time' => $now, 'item_type' => 41);
			            $dalCard->addUserCard($newCard);
			            */
						$info = array('uid'=>$uid, 'item_data'=>'26441*1,74841*1', 'type'=>'30');
                		Hapyfish2_Island_Bll_GiftPackage::getNewUserGift($info);
			            $result['state'] = '4';
                		$result['cids'] = array('26441','74841'); 
                		$result['time_at'] = $now;
						break;
				case 4 :
						/*
						$dalPlant = Dal_Island_Plant::getDefaultInstance();
		                $p1 = array('uid' => $uid, 'bid' => 5232, 'status' => 0, 'item_id' => 52, 'buy_time' => $now, 'item_type' => 32);
		                $dalPlant->insertUserPlant($p1); 
		                
						$dalCard = Dal_Island_Card::getDefaultInstance();
						$newCard = array( 'uid' => $uid, 'cid' => 56641, 'count' => 1, 'buy_time' => $now, 'item_type' => 41);
			            $dalCard->addUserCard($newCard);
			            */
						$info = array('uid'=>$uid, 'item_data'=>'5232*1,56641*1', 'type'=>'40');
                		Hapyfish2_Island_Bll_GiftPackage::getNewUserGift($info);
                		
			            $result['state'] = '5';
                		$result['cids'] = array('5232','56641');
                		$result['time_at'] = $now;
						break;
				case 5 :
						/*
						$dalCard = Dal_Island_Card::getDefaultInstance();
						$newCard = array( 'uid' => $uid, 'cid' => 56741, 'count' => 1, 'buy_time' => $now, 'item_type' => 41);
			            $dalCard->addUserCard($newCard);
			            */
						$info = array('uid'=>$uid, 'item_data'=>'56741*1,74841*1', 'type'=>'60');
                		Hapyfish2_Island_Bll_GiftPackage::getNewUserGift($info);
                		 
			            $result['state'] = '6';
                		$result['cids'] = array('56741','74841');
                		$result['time_at'] = $now;
						break;
				default :
			}
			
		}
		catch( Exception $e ) {
			$result['state'] = '-1';
			$result['content'] = 'serverWord_110';
			info_log($e->getMessage(), 'Event_Timegift_Err');
			info_log($e->getTraceAsString(), 'Event_Timegift_Err');
		}
		 
		if( $val['state'] >= 5 ) {
			$cache->delete( $key );
		} else {
			$val['state']++;
			$val['time_at'] = time();
			$cache->set($key, $val, Hapyfish2_Cache_Memcached::LIFE_TIME_ONE_DAY*10);
			$tg = new Hapyfish2_Island_Event_Dal_Timegift();

			$tg->nextsteptask($uid);
			
		}
		
		return $result;
		
	}
	
}