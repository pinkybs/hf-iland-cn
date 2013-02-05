<?php

class Hapyfish2_Island_Cache_Compound
{
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}

	public static function getBasicInfo()
	{
		$key = 'i:b:c:bm';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$Info = $localcache->get($key);
		if (!$Info) {
			$cache = self::getBasicMC();
			$Info = $cache->get($key);
			if (!$Info) {
				try {
					$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
					$data = $dal->getBasicInfo();
					if($data){
						foreach($data as $k => $v){
							$Info[$v['type']][$v['cid']] =  $v;
						}
						$cache->set($key, $Info);
					} else {
						return null;
					}
			 	} catch (Exception $e) {
            		return null;
        		 }
			}
			$localcache->set($key, $Info);
		}
		return $Info;
	}
	
	public static function loadBasicInfo()
	{
		$key = 'i:b:c:bm';
		$cache = self::getBasicMC();
		try {
			$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
			$data = $dal->getBasicInfo();
			if($data){
				foreach($data as $k => $v){
					$Info[$v['type']][$v['cid']] =  $v;
				}
				$cache->set($key, $Info);
			} else {
				return null;
			}
		} catch (Exception $e) {
            return null;
        }
        return $Info;
	}
	
	public static function getUpdateConfig()
	{
		$key = 'i:b:c:bm:c';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$Info = $localcache->get($key);
		if (!$Info) {
			$cache = self::getBasicMC();
			$Info = $cache->get($key);
			if (!$Info) {
				 try {
					$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
					$data = $dal->getUpdateConfig();
					if($data){
						foreach($data as $k => $v){
							$Info[$v['cid']] =  $v;
						}
						$cache->set($key, $Info);
					} else {
						return null;
					}
				 } catch (Exception $e) {
            		return null;
        		 }
			}
			$localcache->set($key, $Info);
		}
		return $Info;
	}
	
	public static function loadUpdateconfig()
	{
		$key = 'i:b:c:bm:c';
		$cache = self::getBasicMC();
		try {
			$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
			$data = $dal->getUpdateConfig();
			if($data){
				foreach($data as $k => $v){
					$Info[$v['cid']] =  $v;
				}
				$cache->set($key, $Info);
			} else {
				return null;
			}
		} catch (Exception $e) {
            return null;
        }
        return $data;
	}
	//个人图纸和材料
	public static function getUserbAm($uid)
	{
		$data = array();
		$key = 'i:u:c:bm:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
			$userAm = $dal->getUserAm($uid);
			if($userAm){
				foreach($userAm as $k=>$value){
					$data[$value['type']][$value['cid']] =  $value;
				}
				$cache->set($key, $data);
			}
		}
		return $data;
	}
	 
	public static function getResolveConfig()
	{
		$key = 'i:b:c:bm:r';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$Info = $localcache->get($key);
		if (!$Info) {
			$cache = self::getBasicMC();
			$Info = $cache->get($key);
			if (!$Info) {
				 try {
					$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
					$data = $dal->getResolveConfig();
					if($data){
						foreach($data as $k => $v){
							$Info[$v['id']] =  $v;
						}
						$cache->set($key, $Info);
					} else {
						return null;
					}
				 } catch (Exception $e) {
            		return null;
        		 }
			}
			$localcache->set($key, $Info);
		}
		return $Info;
	}
	
	
	public static function loadResolveConfig()
	{
		$key = 'i:b:c:bm:r';
		$cache = self::getBasicMC();
		try {
			$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
			$data = $dal->getResolveConfig();
			if($data){
				foreach($data as $k => $v){
					$Info[$v['id']] =  $v;
				}
				$cache->set($key, $Info);
			} else {
				return null;
			}
		} catch (Exception $e) {
            return null;
        }
        return $data;
	}
	public static function getMarket()
	{
		$key = 'i:b:c:bm:m';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$Info = $localcache->get($key);
		if (!$Info) {
			$cache = self::getBasicMC();
			$Info = $cache->get($key);
			if (!$Info) {
				 try {
					$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
					$data = $dal->getMarket();
					if($data){
						foreach($data as $k => $v){
							$Info[$v['cid']] =  $v;
						}
						$cache->set($key, $Info);
					} else {
						return null;
					}
				 } catch (Exception $e) {
            		return null;
        		 }
			}
			$localcache->set($key, $Info);
		}
		return $Info;
	}
	
	public static function loadMarket()
	{
		$key = 'i:b:c:bm:m';
		$cache = self::getBasicMC();
		try {
			$dal = Hapyfish2_Island_Dal_Compound::getDefaultInstance();
			$data = $dal->getMarket();
			if($data){
				foreach($data as $k => $v){
					$Info[$v['cid']] =  $v;
				}
			$cache->set($key, $Info);
			} else {
				return null;
			}
		} catch (Exception $e) {
            return null;
        }
        return $data;
	}
	
	public static function getNoticeAll($time)
	{
		$key = 'compound:notice';
		$cache = Hapyfish2_Island_Cache_Compound::getBasicMC();
		$notice = $cache->get($key);
		if($notice === false){
			return null;
		} 
		$lastDay = date('Ymd', $time-3*86400);
		foreach($notice as $date => $v){
			if($date <= $lastDay){
				unset($notice[$date]);
				$cache->set($key, $notice);
			}
		}
		return $notice;
	}
	
	public static function setNotice($uid, $cid, $time)
	{
		$notice = array();
		$key = 'compound:notice';
		$cache = Hapyfish2_Island_Cache_Compound::getBasicMC();
		$notice = $cache->get($key);
		$newData = array('uid'=>$uid, 'cid'=>$cid);
		$day = date('Ymd', $time);
		$notice[$day][] = $newData;
		$cache->set($key, $notice);
	}
	
	public static function getUserRate($uid)
	{
		$key = 'i:u:c:p:r:i'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		return 	$data;
	}
	
	public static function clearUserRate($uid)
	{
		$key = 'i:u:c:p:r:i'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
	}
	
	public static function UpdateUserRate($uid, $num)
	{
		$key = 'i:u:c:p:r:i'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data){
			$data += $num;
		}else{
			$data = $num;
		}
		$cache->set($key, $data);
	}
	public static function getAirRemainVisitor($uid, $cid)
	{
		$key = 'i:u:c:p:airvisitors:'.$cid.':'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		return 	$data;
	}
	public static function updateAirRemainVisitor($uid, $cid, $data)
	{
		$key = 'i:u:c:p:airvisitors:'.$cid.':'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
	}
	
	public static function upgradeAirRemainVisitor($uid, $cid, $nextCid, $data)
	{
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'i:u:c:p:airvisitors:'.$cid.':'.$uid;
		$cache->delete($key);
		
		$nextKey = 'i:u:c:p:airvisitors:'.$nextCid.':'.$uid;
		$cache->set($key, $data);
		
	}
	
		
}