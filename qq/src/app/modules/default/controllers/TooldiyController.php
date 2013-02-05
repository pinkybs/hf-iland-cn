<?php

class TooldiyController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo 'uid can not empty';
			exit;
		}

		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

	function clearbuildingAction()
    {
    	$uid = $this->_request->getParam('uid');
    	
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		
		$buildings = Hapyfish2_Island_Bll_Building::getAllOnIsland($uid, 1);
		foreach ( $buildings as $building ) {
			$id = $building['id'];
	        $key = 'i:u:bld:' . $uid . ':' . $id;
			$cache->delete($key);
		}
		
        $dalBuilding = Hapyfish2_Island_Dal_Building::getDefaultInstance();
        $dalBuilding->clearDiy($uid, 1);
        
		$key = 'i:u:bldids:onisl:' . $uid . ':' . '1';
		$cache->delete($key);
		$key1 = 'i:u:bldids:all:' . $uid;
		$cache->delete($key1);
		
		Hapyfish2_Island_Bll_Island::reload($uid, true, 1);
    }

	function clearplantAction()
    {
    	$uid = $this->_request->getParam('uid');
    	
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		
    	$ids = Hapyfish2_Island_Cache_Plant::getOnIslandIds($uid, 1); 
        foreach ($ids as $id) {
        	$key = 'i:u:plt:' . $uid . ':' . $id;
			$cache->delete($key);
        }
        
        $dalPlant = Hapyfish2_Island_Dal_Plant::getDefaultInstance();
        $dalPlant->clearDiy($uid, 1);
        
        $key = 'i:u:pltids:onisla:' . $uid . ':' . '1';
		$cache->delete($key);
        $key1 = 'i:u:pltids:all:' . $uid;
		$cache->delete($key1);
    	$key2 = 'island:allplantonisland:' . $uid . ':' . '1';
		$cache->delete($key2);
		
		Hapyfish2_Island_Bll_Island::reload($uid, true, 1);
    }
}