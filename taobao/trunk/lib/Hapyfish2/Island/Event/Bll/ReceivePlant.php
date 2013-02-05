<?php

/**
 * Event ReceivePlant
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/11    zhangli
*/
class Hapyfish2_Island_Event_Bll_ReceivePlant
{
	const TXT001 = '不能重复领取';
	const TXT002 = '星级不够，不能领取';
	const TXT003 = '恭喜你获得：';
	const TXT004 = '所需建筑不足,不能领取';
	
	/**
	 * @兑换初始化
	 * @param int $uid
	 * @return Array
	 */
	public static function ReceivePlantInit($uid)
	{
		$result = array('status' => 1);
		
		//获取图鉴数据
		$atlasBookDataVo = Hapyfish2_Island_Event_Bll_AtlasBook::atlasBookInit($uid);
		
		$index = 5;
		
		//自己的星级
		$userStar = 0;
		foreach ($atlasBookDataVo['medalList'] as $key => $values) {
			if ($key == $index) {
				$userStar = $values['currentStar'];
			}
		}
		
		//建筑领取状态
		$exchangeAble = Hapyfish2_Island_Event_Cache_ReceivePlant::getExchangeAble($uid);
		
		$result['status'] = 1;
		$resultVo = array('result' => $result, 'myStar' => $userStar, 'exchangeable' => $exchangeAble);
		return $resultVo;
	}
	
	/**
	 * @兑换建筑
	 * @param int $uid
	 * @param int $index
	 * @return Array
	 */
	public static function toReceivePlant($uid, $index)
	{
		$result = array('status' => 1);
		
		if (!in_array($index, array(0, 1, 2))) {
			$result['content'] = 'serverWord_101';
			$resultVo = array('result' => $result);
			return $resultVo;
		}
		
		//建筑领取状态
		$exchangeAble = Hapyfish2_Island_Event_Cache_ReceivePlant::getExchangeAble($uid);
		
		//不能重复领取
		if ($exchangeAble[$index] == 1) {
			$result['content'] = self::TXT001;
			$resultVo = array('result' => $result, 'index' => -1);
			return $resultVo;
		}
		
			//获取图鉴数据
		$atlasBookDataVo = Hapyfish2_Island_Event_Bll_AtlasBook::atlasBookInit($uid);
		
		$id = 5;
		
		//自己的星级
		$userStar = 0;
		foreach ($atlasBookDataVo['medalList'] as $key => $values) {
			if ($key == $id) {
				$userStar = $values['currentStar'];
			}
		}
		
		$userPlantList = Hapyfish2_Island_Event_Bll_AtlasBook::getUserPlantList($uid);
		
		//第一个建筑
		if ($index == 0) {
			$needStar = 10;
			$cid = 134232;
		} else if ($index == 1) {
			$needStar = 25;
			$cid = 134332;
		} else if ($index == 2) {
			$needStar = 45;
			$cid = 132032;
		}
		
		//星级不够
		if ($userStar < $needStar) {
			$result['content'] = self::TXT002;
			$resultVo = array('result' => $result, 'index' => -1);
			return $resultVo;
		}
		
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();
	
		$compensation->setItem($cid, 1);
		$ok = $compensation->sendOne($uid, self::TXT003);
		
		if ($ok) {
			//统计兑换人数
			info_log($uid . ':' . $index, 'receivePlant');
			
			foreach ($exchangeAble as $ekey => $evalue) {
				if ($index == $ekey) {
					$exchangeAble[$ekey] = 1;
					break;
				}
			}
			
			//更新领取状态
			Hapyfish2_Island_Event_Cache_ReceivePlant::renewExchangeAble($uid, $exchangeAble);
		}
		
		$result['status'] = 1;
		$result['itemBoxChange'] = true;
		$resultVo = array('result' => $result, 'index' => $index);
		return $resultVo;
	}
}