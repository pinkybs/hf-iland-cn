<?php

class Hapyfish2_Island_HFC_Card
{
	public static function getUserCard($uid)
    {
        $key = 'i:u:card:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalCard = Hapyfish2_Island_Dal_Card::getDefaultInstance();
	            $result = $dalCard->get($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $cid => $count) {
	            		$data[$cid] = array($count, 0);
	            	}
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		
        	}
        }
        
        $cards = array();
        if ( is_array($data) ) {
	        foreach ($data as $cid => $item) {
	        	$cards[$cid] = array('count' => $item[0], 'update' => $item[1]);
	        }
        }
        
        return $cards;
    }
    
    public static function updateUserCard($uid, $cards, $savedb = false)
    {
        $key = 'i:u:card:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
        	foreach ($cards as $cid => $item) {
        		$data[$cid] = array($item['count'], 0);
        	}
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
	        		$dalCard = Hapyfish2_Island_Dal_Card::getDefaultInstance();
	        		foreach ($cards as $cid => $item) {
	        			if ($item['update']) {
	        				$dalCard->update($uid, $cid, $item['count']);
	        			}
	        		}
        		} catch (Exception $e) {
        			
        		}
        	}
        	
        	return $ok;
        } else {
            $data = array();
        	foreach ($cards as $cid => $item) {
        		$data[$cid] = array($item['count'], $item['update']);
        	}
        	return $cache->update($key, $data);
        }
    }
    
    public static function addUserCard($uid, $cid, $count = 1, $cards = null)
    {
    	if (!$cards) {
	    	$cards = self::getUserCard($uid);
	    	if (!$cards) {
	    		return false;
	    	}
    	}
    	
    	if (isset($cards[$cid])) {
    		$cards[$cid]['count'] += $count;
    		$cards[$cid]['update'] = 1;
    	} else {
    		$cards[$cid] = array('count' => $count, 'update' => 1);
    	}
		if($cid == 183741){
			//1为获得
			$report = array(0,$count,1);
			$log = Hapyfish2_Util_Log::getInstance();
			$log->report('ziZhi', $report);
		}
    	return self::updateUserCard($uid, $cards);
    }
    
    public static function useUserCard($uid, $cid, $count = 1, $cards = null)
    {
        if (!$cards) {
	    	$cards = self::getUserCard($uid);
	    	if (!$cards) {
	    		return false;
	    	}
    	}

        if (!isset($cards[$cid]) || $cards[$cid]['count'] < $count) {
    		return false;
    	} else {
    		$cards[$cid]['count'] -= $count;
    		$cards[$cid]['update'] = 1;
    		//船只加速卡I,金币道具卡
    		if ( $cid == 26241 ) {
    			$savedb = false;
    		}
    		else {
    			$savedb = true;
    		}
    		if($cid == 183741){
    			//2为使用
    			$report = array(0,$count,2);
				$log = Hapyfish2_Util_Log::getInstance();
				$log->report('ziZhi', $report);
    		}
    		return self::updateUserCard($uid, $cards, $savedb);
    	}
    }
    
}