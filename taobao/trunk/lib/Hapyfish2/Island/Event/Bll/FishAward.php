<?php

class Hapyfish2_Island_Event_Bll_FishAward
{
	
	const TXT001 = '成功领取';
	
	public static function fishAwardInit($uid)
	{	
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid);
		$userLocks = $fishUser['lock'];
		for($i=1;$i<=2;$i++) {
			$lockId = $i + 1;
			$isLock = 0;
			if(in_array($lockId, $userLocks)) {
				$isLock = 1;
			}
			$isGet = Hapyfish2_Island_Event_Cache_FishAward::getUserFishAward($uid, $lockId);
			
			$resultVo[] = array(
				'id'	=>	$i,
				'lock'	=>	$isLock,
				'isget'	=>	$isGet
			);
		}
		
		return array('catchFishAwardExVo'=>$resultVo);
	}

	public static function fishAwardEx($uid, $id)
	{
		$award = array(1=>141632, 2=>141832);
		$resultVo = array('status'=>-1);
		$id = intval($id);
		if( $id < 1 || $id > 2) {
			return array('result'=>$resultVo);
		}
		
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid);
		$userLocks = $fishUser['lock'];
		$lockId = $id + 1;
		if( !in_array($lockId, $userLocks) ) {
			return array('result'=>$resultVo);
		}
		
		$isGet = Hapyfish2_Island_Event_Cache_FishAward::getUserFishAward($uid, $lockId);
		if($isGet) {
			return array('result'=>$resultVo);
		}
		
		$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
		$bllCompensation->setItem($award[$id], 1);
		$bllCompensation->sendOne($uid, self::TXT001);
		
		Hapyfish2_Island_Event_Cache_FishAward::setUserFishAward($uid, $lockId);
		
		$resultVo['status'] = 1;
		
		$catchFishAwardExVo = self::fishAwardInit($uid);
		return array('result'=>$resultVo, 'catchFishAwardExVo'=>$catchFishAwardExVo['catchFishAwardExVo']);
	}
		
}