<?php

class CacheclearzlController extends Zend_Controller_Action
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

    function clearteambuyAction()
    {
    	$key = 'TeamBuyInfo';
		$cache = Hapyfish2_Cache_LocalCache::getInstance();
		$cache->delete($key);
		
		echo 'OK';
		exit;
    }
    
    function clearteambuygoodAction()
    {
    	$dalTeamBuy = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
    	$mids = array(0, 1, 2, 3);
    	foreach ($mids as $mid) {
    		$users[] = $dalTeamBuy->getHasJoinTeamBuyUser($mid);
    	}
		
    	if($users) {
	    	foreach ($users as $user) {
	    		foreach ($user as $uids) {
		    		foreach ($uids as $uid) {
				    	$key = 'BuyGoods_' . $uid;
						$cache = Hapyfish2_Cache_Factory::getMC($uid);
						$cache->delete($key);
		    		}
	    		}
	    	}
	    	echo 'OK';
    	}
		else {
			echo 'NOT';
		}

//    	$uid = 1024;
//    	
//    	$key = 'BuyGoods_' . $uid;
//		$cache = Hapyfish2_Cache_Factory::getMC($uid);
//		$cache->delete($key);
    	
		exit;
    }
    
    function cleartableAction()
    {
    	$dalTeamBuyUser = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
        
    	$mids = array(0, 1, 2, 3);
    	foreach ($mids as $mid) {
    		$dalTeamBuyUser->clearTeamBuyUser($mid);
    	}
		
		echo 'OK';
		exit;
    }
    
 }

