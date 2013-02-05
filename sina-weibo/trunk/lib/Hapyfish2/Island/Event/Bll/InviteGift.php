<?php

class Hapyfish2_Island_Event_Bll_InviteGift
{
	public static function initInviteForAward($uid)
	{
		$result = array('status' => -1);
		
		//测服地址
		//$adURL = 'http://game.weibo.com/home/activity/?actId=10049';
		//正服地址
		$adURL = 'http://game.weibo.com/home/activity/?actId=10048';

		//$id = 10049;//测服
		$id = 10048;//正服
		//奖励物品(第一期)
		$list = self::getInviteGift($id);
		//邀请列表
		$availableList = self::getAllInvite($uid, $id);
		
		if (isset($availableList['status'])) {
			$resultVo['result'] = $availableList;
			return $resultVo;
		}
		
		$result = array('status' => 1);
		$resultVo = array(
					'result' => $result,
					'list'	=> $list,
					'availableList'	=> $availableList,
					'adURL' => $adURL
				);
			
		return $resultVo;		
	}
	
	public static function getAllInvite($uid, $id)
	{
		$key = 'i:e:invite:status:' . $id . ':' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
	
		$db = Hapyfish2_Island_Event_Dal_InviteGift::getDefaultInstance();

		if ($data === false) {
			$dat = $db->getAllInviteGift($uid, $id);

			if ($dat === false) {
				$pkey = 'i:e:session:key:' . $uid;
				$mcache = Hapyfish2_Cache_Factory::getMC($uid);		
				$resultData = $mcache->get($pkey);
				
				if ($resultData === false) {
					$resultData = self::getApiData($id);
					
					if ($resultData) {
						$mcache->set($pkey, $resultData, 3600 * 2);
					} else {
						$result = array('status' => -1, 'content' => 'serverWord_101');
						return $result;
					}
				}
		
				$dataVo = $resultData['prize'];
			
				$data = array('0' => 0, '1' => 0, '2' => 0);
				
				foreach ($dataVo as $dakey => $val) {
					if ($val['current_value'] >= $val['target_value']) {
						$data[$dakey] = 1;
					} else {
						$data[$dakey] = 0;
					}
				}

				$cache->set($key, $data, 3600 * 2);
			
				foreach ($data as $dk => $dv) {
					$inc[] = $dk . '*' . $dv;
				}
				
				$str = join(',', $inc);

				try {
					$db->insert($uid, $str, $id);
				} catch (Exception $e) {
				}

				return $data;
			} else {
				$arrDat = explode(',', $dat);
						
				foreach ($arrDat as $arrDatVa) {
					$dataEO = array();
					$dataEO = explode('*', $arrDatVa);
					$data[$dataEO[0]] = $dataEO[1];
				}

				if (($data[0] + $data[1] + $data[2]) == 6) {
					$cache->set($key, $data, 3600 * 24 * 7);
					
					return $data;
				}
			}
		}
		
		$pkey = 'i:e:session:key:' . $uid;
		$mcache = Hapyfish2_Cache_Factory::getMC($uid);
		$resultData = $mcache->get($pkey);
		
		if ($resultData === false) {
			$resultData = self::getApiData($id);
			
			if ($resultData) {
				$mcache->set($pkey, $resultData, 3600 * 2);
			} else {
				$result = array('status' => -1, 'content' => 'serverWord_101');
				return $result;
			}
		}
		
		$dataVo = $resultData['prize'];
		
		foreach ($dataVo as $dakey => $val) {
			if ($val['current_value'] >= $val['target_value']) {
				if ($data[$dakey] != 2) {
					$data[$dakey] = 1;
				}
			}
		}

		$cache->set($key, $data, 3600 * 2);
		
		foreach ($data as $dk => $dv) {
			$inc[] = $dk . '*' . $dv;
		}
		
		$str = join(',', $inc);

		try {
			$db->update($uid, $str, $id);
		} catch (Exception $e) {
		}
		
		return $data;
	}	

	public static function getInviteGift($id)
	{
		$key = 'i:e:invite:gift:' . $id;
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$data = $cache->get($key);
		
		if ($data === false) {
			$db = Hapyfish2_Island_Event_Dal_InviteGift::getDefaultInstance();
			
			try {
				$dataVo = $db->getInviteGift($id);
			} catch (Exception $e) {
			}
			
			if ($dataVo) {
				foreach ($dataVo as $value) {
					$value['needNum'] = (int)$value['need_num'];
					if (count($value['card_list']) > 0) {
						$value['cidList'] = explode(',', $value['card_list']);
					} else {
						$value['cidList'] = array();
					}

					if (count($value['num_list']) > 0) {
						$value['numList'] = explode(',', $value['num_list']);
					} else {
						$value['numList'] = array();
					}
					
					unset($value['id']);
					unset($value['need_num']);
					unset($value['card_list']);
					unset($value['num_list']);
					$data[] = $value;
				}

				$cache->set($key, $data);
			}
		}
		
		return $data;
	}
	
	public static function getApiData($id)
	{		
		try {
			$context = Hapyfish2_Util_Context::getDefaultInstance();
			$puid = $context->get('puid');
			$session_key = $context->get('session_key');
			$rest = SinaWeibo_Client::getInstance();
			$rest->setUser($session_key);
			$presult = $rest->engageStatus($id);
		} catch (Exception $e) {
			return 0;
		}
	
//		$new = array('id' => 10049, 
//				'name' => '乐鱼活动测试一', 
//				'prize' => array(
//						array('id' => 1, 
//							'name' => '金币', 
//							'target_value' => 5, 
//							'current_value' => 0),
//						 array('id' => 2, 
//					 		'name' => '卡', 
//					 		'target_value' => 15,
//						 	'current_value' =>0), 
//						 array('id' => 3, 
//						 	'name' => '岛皮', 
//						 	'target_value' => 30, 
//						 	'current_value' =>0)));
		
		return $presult;
	}
	
	public static function getAwardForInvite($uid)
	{
		$result = array('status' => -1);
		
		//$id = 10049;//测服
		$id = 10048;//正服
		
		//奖励物品(第一期)
		$list = self::getInviteGift($id);

		//邀请列表
		$availableList = self::getAllInvite($uid, $id);
		
		if (($availableList[0] + $availableList[1] + $availableList[2]) == 6) {
			$result['content'] = '对不起，您已经领取了全部奖励！';
			$resultVo = array('result' => $result);
			return $resultVo;
		}

		if (($availableList[0] == 0) && ($availableList[1] == 0) && ($availableList[2] == 0)) {
			$result['content'] = '对不起，您没有邀请足够的好友，不能领取奖励！！';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		$coin = 0;
		$gold = 0;
		$starfish = 0;
		$cidList = array();
		$numList = array();

		if ($availableList[0] == 1) {
			$coin = $list[0]['coin'];
			$gold = $list[0]['gem'];
			$starfish = $list[0]['starfish'];
			
			if ($list[0]['cidList'] != false) {
				$cidList = $list[0]['cidList'];
			}

			if ($list[0]['numList'] != false) {
				$numList = $list[0]['numList'];
			}
		}

		if ($availableList[1] == 1) {
			$coin += $list[1]['coin'];
			$gold += $list[1]['gem'];
			$starfish += $list[1]['starfish'];
			
			if ($cidList != false) {
				$cidList = array_merge($cidList, $list[1]['cidList']);
			} else {
				$cidList = $list[1]['cidList'];
			}
			
			if ($numList != false) {
				$numList = array_merge($numList, $list[1]['numList']);
			} else {
				$numList = $list[1]['numList'];
			}
		}
		
		if ($availableList[2] == 1) {
			$coin += $list[2]['coin'];
			$gold += $list[2]['gem'];
			$starfish += $list[2]['starfish'];
			
			if ($cidList != false) {
				$cidList = array_merge($cidList, $list[2]['cidList']);
			} else {
				$cidList = $list[2]['cidList'];
			}
			
			if ($numList != false) {
				$numList = array_merge($numList, $list[2]['numList']);
			} else {
				$numList = $list[2]['numList'];
			}
		}
		
		foreach ($cidList as $cidkey => $cid) {
			if ($cid != false) {
				$newCidList[] = $cid;
			}
		}

		foreach ($numList as $numkey => $num) {
			if ($num != false) {
				$newNumList[] = $num;
			}
		}
		
		if (count($newCidList) < 1) {
			$newCidList = array();
		}
		
		if (count($newNumList) < 1) {
			$newNumList = array();	
		}
		
		$result = array(
					'status' => 1,
					'coinChange' => $coin,
					'goldChange' => $gold,
					'starFishchange' => $starfish
				);
		
		$resultVo = array(
					'result' => $result,
					'cidList' => $newCidList,
					'numList' => $newNumList					
				);
		
		$compensation = new Hapyfish2_Island_Bll_Compensation();		
		
		if ($coin > 0) {
			$compensation->setCoin($coin);
		}
		if($gold > 0){
			$compensation->setGold($gold);
			
			//update by hudanfeng add send gold log start
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('801', array($uid, $gold, 9));
			//end			
		}
		if($starfish > 0){
			$compensation->setStarfish($starfish);
		}
		
		if (count($newCidList) > 0 && count($newNumList) > 0) {
			foreach ($newCidList as $itemkey => $item) {
				$compensation->setItem($item, $newNumList[$itemkey]);
			}
		}
		
		$compensation->sendOne($uid, '恭喜你获得邀请好友奖励：');

		if ($availableList[0] == 1) {
			$availableList[0] = 2;
		}
		
		if ($availableList[1] == 1) {
			$availableList[1] = 2;
		}
		
		if ($availableList[2] == 1) {
			$availableList[2] = 2;
		}

		$key = 'i:e:invite:status:' . $id . ':' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);

		if (($availableList[0] + $availableList[1] + $availableList[2]) == 6) {
			$cache->set($key, $availableList, 3600 * 24 * 7);
		} else {
			$cache->set($key, $availableList, 3600 * 2);
		}
		
		foreach ($availableList as $dk => $dv) {
			$inc[] = $dk . '*' . $dv;
		}
	
		$str = join(',', $inc);

		try {
			$db = Hapyfish2_Island_Event_Dal_InviteGift::getDefaultInstance();		
			$db->update($uid, $str, $id);
		} catch (Exception $e) {
		}

		return $resultVo;		
	}
	
}