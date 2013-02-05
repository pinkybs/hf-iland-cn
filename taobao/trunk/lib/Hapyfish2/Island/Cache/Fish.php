<?php

class Hapyfish2_Island_Cache_Fish
{
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function getMaps()
	{
		$key = 'i:fish:map';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getMaps();
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;
	}
	
	public static function getIslands()
	{
		$key = 'i:fish:island';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getIslands();
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;
	}	

	public static function getIslandInfo($islandId)
	{
		
		$key = 'i:fish:idinfo:'.$islandId;
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getIslandInfo($islandId);
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;
	}	
	
	public static function getFishAll()
	{
		$key = 'i:fish:fsall';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getFishAll();
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;
	}
		
	public static function getFishByIslandid($islandid)
	{		
		$key = 'i:fish:island:fishs:'.$islandid;
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getFishByIslandid($islandid);
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;
	}
		
	public static function getUserFish($uid)
	{
		$key = 'i:u:fish:getufish:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
	        	$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getUserFish($uid);		
	            $cache->add($key, $data);				
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;
	}

	public static function setUserFish($uid, $fishId, $saveDb=true)
	{
		$flag = 0;
		$key = 'i:u:fish:getufish:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data) {
			$count = count($data);
			foreach($data as $k=>$v) {
				if($v['id'] == $fishId) {
					$flag = 1;
					$data[$k]['num']+=1; 
				}
			}
			if($flag == 0) {
				$data[$count]['id'] = $fishId;
				$data[$count]['num'] = 1;
			}
		}else {
			$data = array(
				0=>array('id'=>$fishId, 'num'=>1)
			);
		}
		if($saveDb) {
	    	$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
			$dalFish->setUserFish($uid, $fishId);			
		}
		
		$cache->set($key, $data);
		return $data;
	}	
	
	public static function getCatchFishes($islandId, $cannonId)
	{
				/*
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getCatchFishes($islandId, $cannonId);
				return $data;	
				*/	
		$key = 'i:u:fish:ctfhs:'.$islandId.':'.$cannonId;
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getCatchFishes($islandId, $cannonId);
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;		
	}
	
	public static function getFishInfo($fishId)
	{
		$key = 'i:u:fish:finfo:'.$fishId;
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getFishInfo($fishId);
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;		
	}

	public static function getFishPlant()
	{
		$key = 'i:u:fish:plants';
		$cache = self::getBasicMC();			
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getFishPlant();
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;		
	}

	public static function getFishPlantByItemId($itemId)
	{		
		$key = 'i:u:fish:plt:itm:'.$itemId;
		$cache = self::getBasicMC();	
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getFishPlantByItemId($itemId);
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;		
	}
	
	public static function getPlantsByItemId($itemId)
	{		
		$key = 'i:u:fish:itemplt:'.$itemId;
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false) {
			try {
				$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $dalFish->getPlantsByItemId($itemId);
				$cache->add($key, $data);
			} catch (Exception $e) {
				return array();
			}
		}
		return $data;		
	}
	
	//获取疲劳时间
	public static function getTiredTime($uid)
	{
		$key = 'i:u:fish:tdtime:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ($data === false) {
			$data = 0;
			$cache->add($key, $data);
		}
		return $data;
	}
	
	//设置疲劳时间
	public static function setTiredTime($uid, $time)
	{
		$key = 'i:u:fish:tdtime:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $time);
	}
			
	//获取最后一次捕捞时间
	public static function getLastCatchTime($uid)
	{
		$key = 'i:u:fish:lttime:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ($data === false) {
			$data = 0;
			$cache->add($key, $data);
		}
		return $data;
	}
	
	//更新最后一次捕捞时间
	public static function setLastCatchTime($uid)
	{
		$time = time();
		$key = 'i:u:fish:lttime:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $time);
	}
		
	/**
	 * 用户基本数据
	 */
	public static function getFishUser($uid, $id)
	{
		$key = 'i:u:fish:uinfo' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		if ($data) {
			if (($data['map'] != $id) && ($id > 0)) {
				$cache->delete($key);
				$data = $cache->get($key);
			}
		}	

		if ($data === false) {
			$data = array();
			$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
			$locks = $dalFish->getUserLocks($uid);
			if(!$locks) {
				$lockArr = array(1);
			}else {
				$lockArr = @explode(",", $locks);
			}
			$data['lock'] = $lockArr;	//已经开启的海岛ID
			$data['map'] = $id;			//当前所在海图
			
			if($id == 5) {
				$island = 21;
			}else if ($id == 4) {
				$island = 16;
			}else if($id == 3) {
				$island = 11;
			} else if ($id == 2) {
				$island = 6;
			} else {
				$island = 1;
			}
			
			$data['island'] = $island;		//当前所在海岛
			$data['nextIsland'] = 0;	//开船即将到的下一个海岛
			$data['skillExp'] = 0;		//熟练度

			$data['sailTime'] = 0;		//开船需要花费的时间
			$data['sailLastTime'] = 0;	//开船起步时间
			$data['stormType'] = 0;		//灾难类型
			$data['step'] = 0;			//后退步数
			$data['time'] = date('Ymd');
			$data['dayNum'] = 0;		//当天捕鱼次数
			$data['dayCoin'] = 0;		//当天取消疲劳时间累加到的金币数
			$data['reduces'] = 0;		//当天金币取消疲劳时间次数
			$cache->set($key, $data);
		}
		return $data;
	}
		
	public static function setFishUser($uid, $data)
	{
		$key = 'i:u:fish:uinfo' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);

	}
	
	public static function getUserDrama($uid)
	{
		$key = 'i:u:fish:udrama:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ($data === false) {	
			$data = array(
				'firstGo'	=>	0,	//第一次进捕鱼游戏
				'firstCatch'=>	0,	//第一次捕鱼
				'firstCard'	=>	0,	//第一次获得封印石
				'firstNew'	=>	0,	//第一次开辟新岛
				'firstSea'	=>	0	//开启新海域
			);
		}

		//增加开启海域剧情
		if (!isset($data['firstSea'])) {
			$data[] = array('firstSea' => 0);
			$cache->set($key, $data);
		}
		
		return $data;
	}
	
	public static function setUserDrama($uid, $data)
	{
		$key = 'i:u:fish:udrama:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
	}

	public static function updateUserFish($uid, $userFish)
	{
		$key = 'i:u:fish:getufish:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userFish);
		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		if(is_array($userFish)){
			foreach($userFish as $k=> $v){
				$dalFish->updateUserFish($uid, $v);
			}
		}
	}

	public static function getTask()
	{
		$key = 'ev:fish:task:static';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		
		if ($data === false) {
			try {
				$db = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$data = $db->getTask();
			} catch (Exception $e) {}
	
			if ($data) {
				$cache->set($key, $data);
			}
		}
		
		return $data;
	}
	
	public static function getCatchFishTaskInitVo($uid)
	{
		$key = 'i:u:fish:task:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		if ($data === false) {
			try {
				$db = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
				$idsArr = $db->getTaskId();
			} catch (Exception $e) {}
			
			if ($idsArr) {
				foreach ($idsArr as $idKey => $arr) {
					$data[$idKey]['id'] = $arr['id'];
					$data[$idKey]['isget'] = 0;
					$data[$idKey]['fishId'] = $arr['fish_id'];
					$data[$idKey]['yetCatchNum'] = 0;
				}
				
				$date = date('Y-m-d');
				$dateFormart = $date . ' 23:59:59';
				$falseTime = strtotime($dateFormart);
		
				$cache->set($key, $data, $falseTime);
			}
		}
		
		return $data;
	}
	
	public static function renewCatchFishTaskInitVo($uid, $data)
	{
		$date = date('Y-m-d');
		$dateFormart = $date . ' 23:59:59';
		$falseTime = strtotime($dateFormart);
		
		$key = 'i:u:fish:task:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data, $falseTime);
	}
	
	public static function getIsPoseidon($uid)
	{
		$isPoseidon = array('0' => 0, '1' => 0, '2' => 0, '3' => 0, '4'=>0);
		
		$userVO = Hapyfish2_Island_HFC_User::getUserVO($uid);

		$islandIds = explode(',', $userVO['unlockIsland']);

		foreach ($islandIds as $islandId) {
			$plantVo[] = Hapyfish2_Island_Bll_Plant::getAllOnIsland($uid, $uid, $islandId);
		}

		$lstArr = array();
		if (count($plantVo) > 0) {
			foreach ($plantVo as $plants) {
				foreach ($plants['plants'] as $plant) {
					if ($plant['cid'] == 154632) {
						$isPoseidon[0] = 1;
					}
					
					if ($plant['cid'] == 154732) {
						$isPoseidon[1] = 1;
					}
					
					if ($plant['cid'] == 154832) {
						$isPoseidon[2] = 1;
					}
					
					if ($plant['cid'] == 154932) {
						$isPoseidon[3] = 1;
					}
					
					if ($plant['cid'] == 192432) {
						$isPoseidon[4] = 1;
					}
					
				}
			}
		}
		
		$lstPlants = Hapyfish2_Island_HFC_Plant::getInWareHouse($uid);
		if (count($lstPlants) > 0) {
			foreach ($lstPlants as $lstPlant) {
				if ($lstPlant['cid'] == 154632) {
					$isPoseidon[0] = 1;
				}
				
				if ($lstPlant['cid'] == 154732) {
					$isPoseidon[1] = 1;
				}
				
				if ($lstPlant['cid'] == 154832) {
					$isPoseidon[2] = 1;
				}
				
				if ($lstPlant['cid'] == 154932) {
					$isPoseidon[3] = 1;
				}
				
				if ($lstPlant['cid'] == 192432) {
					$isPoseidon[4] = 1;
				}
			}
		}
		
		return $isPoseidon;
	}
	
	public static function getBrushFishCardTime($uid)
	{
		$key = 'i:u:fish:brush:card:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		return $data;
	}
	
	public static function addBrushFishCardTime($uid, $time)
	{
		$key = 'i:u:fish:brush:card:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $time);
	}
	
	public static function getUnlock5($uid)
	{
		$key = 'i:u:f:hy5:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			return 0;
		}
		return $data;
	}
	
	public static function updateUnlock5($uid)
	{
		$key = 'i:u:f:hy5:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, 1);
	}
	
}