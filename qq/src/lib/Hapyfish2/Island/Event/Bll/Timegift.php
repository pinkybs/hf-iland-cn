<?php

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
		$h = array('0' => 0,'1'=>0.1, '2'=>0.2,'3'=>0.3,'4'=>0.4,'5'=>0.5);		// 测试时间,1分钟
		//$h = array('0'=>0, '1'=>5, '2'=>20, '3'=>40, '4'=>60, '5'=>120);	// 正确时间	// 测试时间,1分钟
		
		$now = time();
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		
		if( $val['state'] >= 6 || empty($val) ) {
			//任务已经领取完
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
			$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
			switch( $val['state'] ) {
				case 0 :
						$bllCompensation->setItem(56641, 2);
						$bllCompensation->setItem(26341, 2);
						$bllCompensation->sendOne($uid, '');
						
                		$result['state'] = '0';
                		$result['cids'] = array('56641*2', '26341*2');
                		$result['time_at'] = $now;
                		$result['itemBoxChange'] = true;
						break;
				case 1 :
						$bllCompensation->setItem(56741, 1);
						$bllCompensation->setItem(26341, 2);
						$bllCompensation->sendOne($uid, '');
						
			            $result['state'] = '1';
                		$result['cids'] = array('56741*1', '26341*2');
                		$result['time_at'] = $now;
                		$result['itemBoxChange'] = true;
						break;
				case 2 :
						$bllCompensation->setItem(56741, 2);
						$bllCompensation->setItem(74841, 1);
						$bllCompensation->sendOne($uid, '');
						
		                $result['state'] = '2';
                		$result['cids'] = array('56741*2', '74841*1'); 
                		$result['time_at'] = $now;
                		$result['itemBoxChange'] = true;
						break;
				case 3 :
						$bllCompensation->setItem(74841, 1);
						$bllCompensation->setItem(26441, 2);
						$bllCompensation->setItem(14431, 1);
						$bllCompensation->sendOne($uid, '');
						
			            $result['state'] = '3';
                		$result['cids'] = array('74841*1', '26441*2', '14431*1');
                		$result['time_at'] = $now;
                		$result['itemBoxChange'] = true;
						break;
				case 4 :
						$bllCompensation->setCoin(10000);
						$bllCompensation->setItem(74841, 2);
						$bllCompensation->setItem(79331, 1);
						$bllCompensation->sendOne($uid, '');
						
			            $result['state'] = '4';
                		$result['cids'] = array('74841*2', '79331*1');
                		$result['coin'] = 10000;
                		$result['time_at'] = $now;
                		$result['coinChange'] = 10000;
                		$result['itemBoxChange'] = true;
						break;
				case 5 :
						$bllCompensation->setItem(54321, 1);
						$bllCompensation->setItem(15231, 1);
						$bllCompensation->setItem(76621, 1);
						$bllCompensation->sendOne($uid, '');
						
			            $result['state'] = '5';
                		$result['cids'] = array('54321*1', '15231*1', '76621*1');
                		$result['time_at'] = $now;
                		$result['itemBoxChange'] = true;
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
		
		$result['state']++;
		
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
	
	public static function getNextTimeGift($uid)
	{
		$now = time();
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		
		if( $val['state'] >= 6 || empty($val) ) {
			//任务已经领取完
			$result['state'] = '-1';
			$result['content'] = 'serverWord_201'; // #todo 错误代码未改
			return $result;
		}
		
		if ( $val['state'] == 6 ) {
			$result['state'] = '-1';
			$result['content'] = '您这是最后一次奖励了哦';
			return $result;
		}
		
		try {
			switch( $val['state'] ) {
				case 0 :
                		$result['state'] = '1';
                		$result['cidarr'] = array('56641*2', '26341*2');
						break;
				case 1 :
			            $result['state'] = '2';
                		$result['cidarr'] = array('56741*1', '26341*2');
						break;
				case 2 :
		                $result['state'] = '3';
                		$result['cidarr'] = array('56741*2', '74841*1');
						break;
				case 3 :
			            $result['state'] = '4';
                		$result['cidarr'] = array('74841*1', '26441*2', '14431*1');
						break;
				case 4 :
			            $result['state'] = '5';
                		$result['cidarr'] = array('74841*2', '79331*1');
                		$result['coin'] = 10000;
						break;
				case 5 :
			            $result['state'] = '6';
                		$result['cidarr'] = array('54321*1', '15231*1', '76621*1');
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
		
		return $result;
		
	}
	
}