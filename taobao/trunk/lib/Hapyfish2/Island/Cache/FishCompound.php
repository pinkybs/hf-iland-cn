<?php

class Hapyfish2_Island_Cache_FishCompound
{
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function getBasic()
	{
		$key = 'i:fish:comp';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadBasic();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function loadBasic()
	{
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getBasicInfo();
		if ($data) {
			$key = 'i:fish:comp';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}

/*	public static function getGrowing()
	{
		$key = 'i:fish:comp:grow';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadGrowing();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function getGrowInfo($fid)
	{
		$grow = self::getGrowing();
		if(isset($grow[$fid])){
			return $grow[$fid];
		}
		return null;
	}
	
	public static function loadGrowing()
	{
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getGrowing();
		if ($data) {
			$key = 'i:fish:comp:grow';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	*/
	public static function getFishInfo($fid)
	{
		$data = self::getBasic();
		if(isset($data[$fid])){
			return $data[$fid];
		}
		return null;
	}
	
	public static function getUserFish($uid, $id)
	{
		$list = self::getUserFishAll($uid);
		if(isset($list[$id])){
			return $list[$id];
		}
		return null;
		
	}
	
	public static function getUserFishAll($uid)
	{
		$key = 'i:u:m:f:a'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$data = $db->getUserFishCompound($uid);
	    	} catch (Exception $e) {
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return null;
	    	}
		}
		return $data;
	}
	
	public static function updateUserFish($uid, $info, $userFish = array())
	{
		if(empty($userFish)){
			$userFish = self::getUserFishAll($uid);
		}
		$userFish[$info['id']] = $info;
		$key = 'i:u:m:f:a'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userFish);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$data = $db->updateUserFish($uid,$info);
    	} catch (Exception $e) {
    		return false;
    	}
	    return true;
	}
	
	public static function getFishSkill()
	{
		$key = 'i:f:skill';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadSkill();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function loadSkill()
	{
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getSkill();
		if ($data) {
			$key = 'i:f:skill';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function getGuide()
	{
		$key = 'i:f:m:guide';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadGuide();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function loadGuide()
	{
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getGuide();
		if ($data) {
			$key = 'i:f:m:guide';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function getSkillInfo($sid)
	{
		$data = self::getFishSkill();
		if(isset($data[$sid])){
			return $data[$sid];
		}
		return null;
	}
	
	public static function getFishTrack()
	{
		$key = 'i:f:track';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadTrack();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function getTrackInfo($tid)
	{
		$data = self::getFishTrack();
		if(isset($data[$tid])){
			return $data[$tid];
		}
		return null;
	}
	
	public static function loadTrack()
	{
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getTrack();
		if ($data) {
			$key = 'i:f:track';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function getFishObstacle()
	{
		$key = 'i:f:obstacle';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadObstacle();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function getObstacleInfo($oid)
	{
		$data = self::getFishObstacle();
		if(isset($data[$oid])){
			return $data[$oid];
		}
		return null;
	}
	
	public static function loadObstacle()
	{
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getObstacle();
		if ($data) {
			$key = 'i:f:obstacle';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function getUserSkill($uid)
	{
		$key = 'i:u:f:m:skill:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$data = $db->getUserSkill($uid);
	    	} catch (Exception $e) {
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return null;
	    	}
		}
		return $data;
	}
	
	public static function getUserGameFish($uid)
	{
		$key = 'i:u:f:m:game:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		return $data;
	}
	
	public static function setUserGameFish($uid, $id)
	{
		$key = 'i:u:f:m:game:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $id);
	}
	

	public static function getUserUnlock($uid)
	{
		$key = 'i:u:f:m:unlock:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$data = $db->getUnlock($uid);
	    	} catch (Exception $e) {
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return 0;
	    	}
		}
		return $data;
	}
	
	public static function updateUserLock($uid,$id)
	{
		$key = 'i:u:f:m:unlock:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $id);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$data = $db->updateUnlock($uid,$id);
    	} catch (Exception $e) {
    	}
	}
	
	public static function getTypetrack($type)
	{
		$data = array();
		$track = self::getFishTrack();
		if($track){
			foreach($track as $id => $v){
				if($v['type'] == $type){
					$data[$id] = $v;
				}
			}
		}
		return $data;
	}
	
	public static function getUserTrackLimit($uid)
	{
		$key = 'i:u:f:m:limit:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data['date'] != $date || $data === false){
			$data['date'] = $date;
			$data['list'] = array();
		}
		return $data;
	}
	
	public static function updateUserLimit($uid,$data)
	{
		$key = 'i:u:f:m:limit:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
	}
	
	public static function removeFish($uid, $id, $userfish)
	{
		if(empty($userfish)){
			$userfish = self::getUserFishAll($uid);
		}
		unset($userfish[$id]);
		$key = 'i:u:m:f:a'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userfish);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$data = $db->remove($uid,$id);
    	} catch (Exception $e) {
    		return false;
    	}
	    return true;
	}
	
	public static function updateUserSkill($uid, $info, $userSkill = array())
	{
		if(empty($userSkill)){
			$userSkill = self::getUserSkill($uid);
		}
		$userSkill[$info['cid']] = $info;
		self::setUserSkill($uid, $userSkill);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$data = $db->updateUserSkill($uid,$info);
    	} catch (Exception $e) {
    		return false;
    	}
	    return true;
	}
	
	public static function addUserSkill($uid,$cid,$num)
	{
		$userSkill = self::getUserSkill($uid);
		if(isset($userSkill[$cid])){
			$userSkill[$cid]['count'] += $num;
			$userSkill[$cid]['uid'] = $uid;
			$userSkill[$cid]['cid'] = $cid;
		}else{
			$userSkill[$cid]['uid'] = $uid;
			$userSkill[$cid]['cid'] = $cid;
			$userSkill[$cid]['count'] = $num;
		}
		self::updateUserSkillAll($uid, $userSkill);
	}
	
	public static function setUserSkill($uid, $userSkill)
	{
		$key = 'i:u:f:m:skill:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userSkill);
	}
	
	public static function updateUserSkillAll($uid, $userSkill)
	{
		self::setUserSkill($uid, $userSkill);
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		if(is_array($userSkill)){
			foreach($userSkill as $info){
				try{
					$db->updateUserSkill($uid,$info);
		    	} catch (Exception $e) {
		    		return false;
		    	}
			}
		}
	}
	
	public static function getUserGuide($uid)
	{
		$key = 'i:u:f:m:guide:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$data = $db->getUserGuide($uid);
	    	} catch (Exception $e) {
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return 0;
	    	}
		}
		return $data;
	}
	
	public static function updateUserGuide($uid,$step)
	{
		$key = 'i:u:f:m:guide:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key,$step);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$db->updateUserGuide($uid, $step);
    	} catch (Exception $e) {
    		return false;
    	}
    	return true;
	}
	
	public static function getAward()
	{
		$key = 'i:f:m:award';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadAward();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}
	
	public static function loadAward()
	{
		
		$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$data = $db->getAward();
		if ($data) {
			$key = 'i:f:m:award';
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function updateAward($uid,$award)
	{
		$key = 'i:f:m:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $award);
	}
	
	public static function getUserAward($uid)
	{
		$key = 'i:f:m:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		return $data;
	}
	
	public static function deleteAward($uid)
	{
		$key = 'i:f:m:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
	}
	
	public static function getMtachTime($uid,$id)
	{
		$key = 'i:u:m:pvp:'.$uid.':'.$id;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			return 0;
		}
		$time = time();
		$result = $data - $time > 0?$data - $time:0;
		return $result;
	}
	
	public static function updatePvpLimit($uid,$limit)
	{
		$key = 'i:u:m:f:pvp:l:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $limit);
	}
	
	public static function getPvpLimit($uid)
	{
		$key = 'i:u:m:f:pvp:l:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			$data['date'] = $date;
			$data['limit'] = 0;
		}
		return $data;
	}
	
	public static function updateMatchTime($fuid,$fid)
	{
		$key = 'i:u:m:pvp:'.$fuid.':'.$fid;
		$cache = Hapyfish2_Cache_Factory::getMC($fuid);
		$time = time();
		$time += 600;
		$cache->set($key, $time);
	}
	
	public static function addUserPrestige($uid, $prestige)
	{
		//1为增加
		$report = array(0,$prestige,1);
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('Shengwang', $report);
		$num = self::getUserPrestige($uid);
		$num += $prestige;
		self::updateUserPrestige($uid, $num);
	}
	
	public static function getUserPrestige($uid)
	{
		$key ='i:u:f:m:pvp:pr:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$data = $db->getUserPrestige($uid);
    	} catch (Exception $e) {
    		return 0;
    	}
			if($data){
				$cache->set($key,$data);
			}else{
				$data = 0;
			}
		}
		return $data;
	}
	
	public static function updateUserPrestige($uid, $num)
	{
		$key ='i:u:f:m:pvp:pr:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $num);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$db->updateUserPrestige($uid, $num);
    	} catch (Exception $e) {
    		return false;
    	}
	}
	
	public static function getUserRank($uid)
	{
		$key ='i:u:f:m:arena:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$rank = $cache->get($key);
		if($rank === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$rank = $db->getUserRank($uid);
	    	} catch (Exception $e) {
	    	}
	    	if($rank){
	    		$cache->set($key, $rank);
	    	}else{
	    		return null;
	    	}
		}
		return $rank;
	}
	
	public static function getTotalRank($n)
	{
		$key = 'i:f:m:t:rank:'.$n;
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$data = $db->getLimitRank($n);
	    	} catch (Exception $e) {
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return null;
	    	}
		}
		return $data;
	}

	public static function prestigeExchange()
	{
		$key = 'i:f:m:t:p:ex';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false){
			try{
				$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
				$data = $db->getPrestigeExchange();
	    	} catch (Exception $e) {
	    	}
	    	if($data){
	    		$cache->set($key, $data);
	    	}else{
	    		return null;
	    	}
		}
		return $data;
	}
	
	public static function getMaxRank()
	{
		$key = 'i:f:m:a:max:r';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false){
			$data = 0;
		}
		return $data;
	}
	
	public static function getArenaLimit($uid)
	{
		$key = 'i:u:f:m:ar:l:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			$data['date'] = $date;
			$data['limit'] = 0;
		}
		return $data;
	}
	
	public static function getArenaTime($uid)
	{
		$key = 'i:i:f:m:ar:t:l:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			return 0;
		}
		$time = time();
		$result = $data - $time > 0?$data - $time:0;
		return $result;
	}
	
	public static function updateArenaLimit($uid, $limit)
	{
		$key = 'i:u:f:m:ar:l:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->set($key, $limit);
	}
	
	public static function updateArenaTime($uid, $end)
	{
		$key = 'i:i:f:m:ar:t:l:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$time = time();
		$time += $end;
		$cache->set($key, $time);
	}
	
	public static function updateUserRank($uid, $data)
	{
		$key ='i:u:f:m:arena:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
		try{
			$db = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
			$db->updateUserRank($uid,$data);
	    } catch (Exception $e) {
	    }
	}
	
	public static function getUserReport($uid)
	{
		$key = 'i:u:f:m:a:report'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		return $data;
	}
	
	public static function updateReport($uid, $report)
	{
		$key = 'i:u:f:m:a:report'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$data[] = $report;
		}else{
			if(count($data) >= 3){
				array_shift($data);
			}
			$data[] = $report;
		}
		$cache->set($key,$data);
	}
	
	public static function getReputationAward($uid)
	{
		$key = 'i:u:f:m:re:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			$data['date'] = $date;
			$data['get'] = 0;
		}
		return $data;
	}
	
	public static function updateReputationAward($uid, $award)
	{
		$key = 'i:u:f:m:re:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->set($key, $award);
	}
	
	public static function getHorn()
	{
		$key = 'i:u:f:getHorn';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		return $data;
	}
	
	public static function updateHorn($word)
	{
		$key = 'i:u:f:getHorn';
		$cache = self::getBasicMC();
		$cache->set($key, $word, 3600);
	}
	
	public static function getProficiency($uid,$id)
	{
		$key = 'i:u:f:m:cp:p:'.$uid.':'.$id;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$data = 0;
		}
		return $data;
	}
	
	public static function updateProficiency($uid,$id, $num)
	{
		$key = 'i:u:f:m:cp:p:'.$uid.':'.$id;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $num);
	}
	
	public static function getUserUnlockFish($uid)
	{
		$key = 'i:u:f:m:unlock:f:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			return array();
		}
		return $data;
	}
	
	public static function updateUserUnlockFish($uid, $userunlock)
	{
		$key = 'i:u:f:m:unlock:f:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userunlock);
	}
}