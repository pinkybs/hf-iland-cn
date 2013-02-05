<?php

class Hapyfish2_Island_Event_Bll_Grade
{
	
	public static function getStatue($uid)
	{
		$key = 'i:u:e:g:s'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$status = $cache->get($key);
		if($status){
			return 2;
		}
		try {
			$context = Hapyfish2_Util_Context::getDefaultInstance();
			$puid = $context->get('puid');
			$session_key = $context->get('session_key');
			$rest = SinaWeibo_Client::getInstance();
			$rest->setUser($session_key);
			$info = $rest->hasScored();
		} catch (Exception $e) {
			return 0;
		}
		if($info == 1){
			return 1;
		}
		return 0;
		
	}
	
	public static function getGradeGift($uid)
	{
		$result ['result'] ['status'] = 1;
		$statue = self::getStatue($uid);
		if($statue !=1 ){
			$result ['result'] ['status'] = - 1;
			$result ['result'] ['content'] = '您已领取过，或您没有未岛主评5分！';
			return $result;
		}
		$com = new Hapyfish2_Island_Bll_Compensation();
		$com->setCoin(100000);
		$ok = $com->sendOne($uid, '恭喜你获得岛主评分奖励：');
		if($ok){
			$key = 'i:u:e:g:s'.$uid;
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$cache->set($key, 1);
			$dal = Hapyfish2_Island_Event_Dal_Grade::getDefaultInstance();
			$dal->updateUserStatus($uid);
		}
		$result ['result']['coinChange'] = 100000;
		return $result;
	}

}