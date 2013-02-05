<?php

/**
 * Event ValentineDay
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/02/06    zhangli
*/
class Hapyfish2_Island_Event_Cache_ValentineDay
{
	const LIFE_TIME_ONE_MONTH = 2592000;
	
	/**
	 * @获得花园数据
	 * @param int $uid
	 * @return Array
	 */
	public static function getGardenList($uid)
	{
		 $key = 'ev:valday:garden:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);	 
		 $dataVo = $cache->get($key);
		 
		 if ($dataVo === false) {
			$dataVo = array(array('buildingId' => 0, 'completeTime' => 0, 'yield' => 0, 'flowerId' => 0, 'maxYield' => 0, 'gainTime' => 0),
							array('buildingId' => 0, 'completeTime' => 0, 'yield' => 0, 'flowerId' => 0, 'maxYield' => 0, 'gainTime' => 0),
	 						array('buildingId' => 0, 'completeTime' => 0, 'yield' => 0, 'flowerId' => 0, 'maxYield' => 0, 'gainTime' => 0),
	 						array('buildingId' => 0, 'completeTime' => 0, 'yield' => 0, 'flowerId' => 0, 'maxYield' => 0, 'gainTime' => 0),
	 						array('buildingId' => 0, 'completeTime' => 0, 'yield' => 0, 'flowerId' => 0, 'maxYield' => 0, 'gainTime' => 0));
		 					
		 	$cache->set($key, $dataVo, self::LIFE_TIME_ONE_MONTH);
		 }
		 
		 //检查是否是5个数据
		 $result = self::checkData($uid, $dataVo);
	 
		 if ($result['ok'] == true) {
		 	$dataVo = $result['dataVo'];
		 	$cache->set($key, $dataVo, self::LIFE_TIME_ONE_MONTH);
		 }
		 
		 return $dataVo;
	}
	
	/**
	 * 
	 * @更新花园信息
	 * @param int $uid
	 * @param array $gardenList
	 */
	public static function renewGardenList($uid, $gardenList)
	{
		 $key = 'ev:valday:garden:' . $uid;
		 $cache = Hapyfish2_Cache_Factory::getMC($uid);
		 $cache->set($key, $gardenList, self::LIFE_TIME_ONE_MONTH);
	}
	
	/**
	 * @用户拥有的玫瑰
	 * @param int $uid
	 * @return array
	 */
	public static function getRoseList($uid)
	{
		$key = 'ev:valday:roselist:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		if ($data == false) {
 			try {
				$db = Hapyfish2_Island_Event_Dal_ValentineDay::getDefaultInstance();
				$data = $db->getRoseList($uid);
			} catch (Exception $e) {}

			if ($data) {
				$cache->set($key, $data);
			}
		}
		
		return $data;
	}
	
	/**
	 * 
	 * @更新玫瑰数量
	 * @param int $uid
	 * @param array $roseList
	 */
	public static function renewRoseList($uid, $roseList)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_ValentineDay::getDefaultInstance();
			$db->renewRoseList($uid, $roseList);
		} catch (Exception $e) {}
		
		$key = 'ev:valday:roselist:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $roseList, self::LIFE_TIME_ONE_MONTH);
	}
	
	/**
	 * @检查建筑
	 * @param int $uid
	 * @param int $dataVo
	 * @return array
	 */
	public static function checkData($uid, $dataVo)
	{
		$ok = false;
	
		foreach ($dataVo as $key => $data) {
			if ($data['buildingId'] == 0) {			
				$ok = true;
				break;
			}
		}
	
		if ($ok == true) {
			try {			
				$db = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
				$itemVo = $db->getItemVo($uid, 1409);
			} catch (Exception $e) {}

			$itemIdArr = array();
			if ($itemVo) {
				foreach ($itemVo as $item) {
					$itemIdArr[] = $item['id'] . $item['item_type'];
				}
			}
			
		 	if ($itemIdArr) {
		 		$buildingIds = array();
		 		foreach ($dataVo as $data) {
		 			if ($data['buildingId'] > 0) {
		 				$buildingIds[] = $data['buildingId'];
		 			}
		 		}
		 		
		 		foreach ($itemIdArr as $itemId) {		
		 			if (!in_array($itemId, $buildingIds)) {
						foreach ($dataVo as $dataNewKey => $list) {					
			 				if ($list['buildingId'] == 0) {
			 					$dataVo[$dataNewKey]['buildingId'] = $itemId;
			 					break;
			 				}
			 			}	
		 			}
		 		}
		 	}
		}
		
		$result = array('ok' => $ok, 'dataVo' => $dataVo);
		return $result;
	}

    public static function getRoseGroupById($groupId)
    {
        $list = self::getRoseGroups();
        if (isset($list[$groupId])) {
            return $list[$groupId];
        }
        return null;
    }
	
    public static function getRoseGroups()
    {
        $key = 'island:rosegroups';
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();
        
        $list = $localcache->get($key);
        if (!$list) {
            $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadRoseGroups();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        
        return $list;
    }
    
    public static function loadRoseGroups()
    {
        $db = Hapyfish2_Island_Event_Dal_ValentineDay::getDefaultInstance();
        $list = $db->getRoseGroups();
        if ($list) {
            $key = 'island:rosegroups';
            $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
            $cache->set($key, $list);
        }
        return $list;
    }

    public static function firstQuest($uid)
    {
    	$key = 'ev:valday:first:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	
    	if ($data === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_ValentineDay::getDefaultInstance();
				$firstQuest = $db->getFirstQuest($uid);
			} catch (Exception $e) {}
			
			if (!$firstQuest) {
				try {
					$db->incFirstQuest($uid);
				} catch (Exception $e) {}
			}
			
			$cache->set($key, 1, self::LIFE_TIME_ONE_MONTH);
    	}
    	
    }
    
}