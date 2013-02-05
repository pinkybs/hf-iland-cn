<?php

class Hapyfish2_Island_Cache_SuperVisitor
{
	public static function get($uid)
	{
        $key = 'i:u:ship:sv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ( $data === false ) {
        	return array();
        }
        return $data;
	}
	
    public static function add($uid, $newData)
    {
        $key = 'i:u:ship:sv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        
        if ( $data === false || empty($data) ) {
	        $new = $newData;
        }
        else {
        	//清除超出有效时间的小人
        	$nowTime = time();
        	for ( $i=0,$iCount=count($data); $i<$iCount; $i++ ) {
        		if ( isset($data[$i][0]) ) {
		        	$id = $data[$i][0];
		        	$time = substr($id, 0, 10);
		        	if ( ($nowTime - $time) >= 2*60 ) {
		        		unset($data[$i]);
		        	}
                }
                else {
                	unset($data[$i]);
                }
	        }
	        $new = array_merge($data, $newData);
        }
        
		$cache->set($key, $new);
		return $new;
    }

    public static function update($uid, $data)
    {
        $key = 'i:u:ship:sv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        
		$cache->set($key, $data);
		return $data;
    }
    
    public static function hasShipSuperVisitor($uid, $postionId)
    {
        $key = 'i:u:ship:svs:' . $uid . ':' . $postionId;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ( $data != 'N') {
			return true;
		}
		else {
			return false;
		}
    }
    
    public static function updateShipSuperVisitor($uid, $postionId, $data)
    {
        $key = 'i:u:ship:svs:' . $uid . ':' . $postionId;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
    }

    public static function getTodayRemainSvNum($uid)
    {
        $key = 'i:u:todayrenum:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ( $data === false ) {
			return 1;
		}
		else {
			return $data;
		}
    }
    
    public static function updateTodayRemainSvNum($uid, $data)
    {
        $key = 'i:u:todayrenum:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
    }
    
    //mooch
	public static function getMoochSvInfo($uid)
	{
        $key = 'i:u:ship:moochsv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ( $data === false ) {
        	return array();
        }
        return $data;
	}
	
    public static function addMoochSvInfo($uid, $newData)
    {
        $key = 'i:u:ship:moochsv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        
        if ( $data === false || empty($data) ) {
	        $new = $newData;
        }
        else {
        	//清除超出有效时间的小人
        	$nowTime = time();
        	for ( $i=0,$iCount=count($data); $i<$iCount; $i++ ) {
        		if ( isset($data[$i][0]) ) {
	                $id = $data[$i][0];
	                $time = substr($id, 0, 10);
	                if ( ($nowTime - $time) >= 2*60 ) {
	                    unset($data[$i]);
	                }
        		}
        		else {
        			unset($data[$i]);
        		}
	        }
	        $new = array_merge($data, $newData);
        }
        
		$cache->set($key, $new);
		return $new;
    }

    public static function updateMoochSvInfo($uid, $data)
    {
        $key = 'i:u:ship:moochsv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        
		$cache->set($key, $data);
		return $data;
    }
    
    public static function canMoochSuperVisitor($uid, $postionId)
    {
        $key = 'i:u:ship:moochsvs:' . $uid . ':' . $postionId;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ( $data != 'N') {
			return true;
		}
		else {
			return false;
		}
    }
    
    public static function updateMoochShipSuperVisitor($uid, $postionId, $data)
    {
        $key = 'i:u:ship:moochsvs:' . $uid . ':' . $postionId;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
    }
    
    //collection
	public static function getUserCollection($uid)
    {
        $key = 'i:u:coltn:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalSuperVisitor = Hapyfish2_Island_Dal_SuperVisitor::getDefaultInstance();
	            $result = $dalSuperVisitor->get($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $cid => $count) {
	            		$data[$cid] = array($count, 0);
	            	}
	            	$cache->add($key, $data);
	            } else {
	            	$data = array();
	            	$cache->add($key, $data);
	            }
        	} catch (Exception $e) {
        	}
        }
        
        $collections = array();
        if ( is_array($data) ) {
	        foreach ($data as $cid => $item) {
	        	$collections[$cid] = array('count' => $item[0], 'update' => $item[1]);
	        }
        }
        return $collections;
    }
    
    public static function updateUserCollection($uid, $collections, $savedb = false)
    {
        $key = 'i:u:coltn:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $data = array();
        	foreach ($collections as $cid => $item) {
        		$data[$cid] = array($item['count'], 0);
        	}
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
	        		$dalSuperVisitor = Hapyfish2_Island_Dal_SuperVisitor::getDefaultInstance();
	        		foreach ($collections as $cid => $item) {
	        			if ($item['update']) {
	        				$dalSuperVisitor->update($uid, $cid, $item['count']);
	        			}
	        		}
        		} catch (Exception $e) {
        		}
        	}
        	return $ok;
        } else {
            $data = array();
        	foreach ($collections as $cid => $item) {
        		$data[$cid] = array($item['count'], $item['update']);
        	}
        	return $cache->update($key, $data);
        }
    }
    
    public static function addUserCollection($uid, $cid, $count = 1, $collections = null)
    {
    	if (!$collections) {
	    	$collections = self::getUsercollection($uid);
	    	if (!$collections) {
	    		$collections = array();
	    	}
    	}
    	
    	if (isset($collections[$cid])) {
    		$collections[$cid]['count'] += $count;
    		$collections[$cid]['update'] = 1;
    	} else {
    		$collections[$cid] = array('count' => $count, 'update' => 1);
    	}

    	return self::updateUsercollection($uid, $collections, true);
    }
    
    //today collection
	public static function getUserTodayCollection($uid)
    {
        $key = 'i:u:todaycoltn:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);  
        if ($data === false) {
            $data = array();
            $cache->add($key, $data);
        }
        
        $collections = array();
        if ( is_array($data) ) {
	        foreach ($data as $cid => $item) {
	        	$collections[$cid] = array('count' => $item[0]);
	        }
        }
        return $collections;
    }
    
    public static function updateUserTodayCollection($uid, $collections)
    {
        $key = 'i:u:todaycoltn:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array();
        foreach ($collections as $cid => $item) {
        	$data[$cid] = array($item['count']);
        }
        return $cache->update($key, $data);
    }
    
    public static function getTodayAllUserSvInfo()
    {
        $key = 'i:todayallsv';
        $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
        $data = $cache->get($key);
        
        $now = time();
        if ( $data == false ) {
        	$data = array('time' => $now, 
        	              'gid_13' => 0,
        	              'gid_20' => 0);
        }
        else {
        	$todayUnixTime = strtotime(date('Y-m-d', $now));
        	if ( $data['time'] < $todayUnixTime ) {
	            $data = array('time' => $now, 
	                          'gid_13' => 0,
	                          'gid_20' => 0);
        	}
        }
        
        return $data;
    }
    
    public static function updateTodayAllUserSvInfo($data)
    {
        $key = 'i:todayallsv';
        $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
        $cache->set($key, $data);
    }
    
}