<?php

class Hapyfish2_Island_Bll_Vip
{
	const TXT001 = '条件不足，不能合成';
	public static function initVip()
	{
		$list = array();
		$data = Hapyfish2_Island_Cache_Vip::getBasic();
		if($data){
			foreach($data as $k=>$v){
				$list[] = $v;
			}
		}
		return $list;
	}
	
	public static function getVipStep($uid, $gem = 0)
	{
		$vip = 0;
		if($gem == 0){
			$gem = Hapyfish2_Island_Cache_Vip::getGem($uid);
		}
		$info = Hapyfish2_Island_Cache_Vip::getBasic();
		foreach($info as $k=> $v){
			if($gem >= $v['needGem']){
				$step = $k;
				$vip = $step >= $vip ? $step : $vip;
			}
		}
		
		return $vip;
	}
	
	public static function getVipPrestige($uid)
	{
		$vip = self::getVipStep($uid);
		$num = 15;
		if($vip == 1){
			$num = 17;
		}else if($vip == 2){
			$num = 20;
		}else if($vip == 3){
			$num = 24;
		}else if($vip == 4){
			$num = 30;
		}else if($vip == 5){
			$num = 40;
		}
		return $num;
	}
	
	public static function getVipTime($uid)
	{
		$vip = self::getVipStep($uid);
		$time = 600;
		if($vip == 4 || $vip == 5){
			$time = 0;
		}
		return $time;
	}
	
	public static function skip($uid)
	{
		$result['status'] = -1;
		$vip = self::getVipStep($uid);
		if($vip < 1){
			return $result;
		}
		if($vip == 4 || $vip == 5){
			$result['status'] = 1;
			return $result;
		}
		$info = Hapyfish2_Island_Cache_Vip::getVipInfo($vip);
		$userLimit = Hapyfish2_Island_Cache_Vip::getskipLimit($uid);
		if($userLimit['limit'] >= $info['skipNum']){
			return $result;
		}
		$userLimit['limit'] += 1;
		Hapyfish2_Island_Cache_Vip::updateskipLimit($uid, $userLimit);
		$result['status'] = 1;
		return $result;
	}
	
	public static function insertGem($uid, $gem)
	{
		$usergem = Hapyfish2_Island_Cache_Vip::getGem($uid);
		$vip = self::getVipStep($uid,$usergem);
		$usergem += $gem;
		Hapyfish2_Island_Cache_Vip::updateGem($uid, $usergem);
		$vip1 = self::getVipStep($uid,$usergem);
		if($vip1 > $vip ){
			Hapyfish2_Island_Cache_Vip::insertVipMessage($uid, $vip1);
			$arr = range($vip + 1,$vip1);
			self::getVipStepAward($uid, $arr);
		}
	}
	
	public static function event($uid,$win)
	{
		$time = 1335974399;
		if(time() > $time){
			return;
		}
		$Award = Hapyfish2_Island_Cache_Vip::getEventAward($uid);
		if($Award['one'] == -1){
			$Award['one'] = 0;
		}
		if($win == 1 && $Award['two'] == -1){
			$Award['two'] = 0;
		}
		Hapyfish2_Island_Cache_Vip::updateEventAward($uid, $Award);
	}
	
	
	public static function getEventAward($uid,$type)
	{
		$result['status'] = -1;
		$time = 1335974399;
		if(time() > $time){
			return $result;
		}
		$Award = Hapyfish2_Island_Cache_Vip::getEventAward($uid);
		if($type == 1){
			if($Award['one'] != 0){
				return $result;
			}
			Hapyfish2_Island_Cache_Fish::setUserFish($uid, 1);
			Hapyfish2_Island_Cache_Fish::setUserFish($uid, 1);
			Hapyfish2_Island_Cache_Fish::setUserFish($uid, 2);
			Hapyfish2_Island_Cache_Fish::setUserFish($uid, 24);
			$name = '恭喜你成功领取:亲吻鱼*2，斑马鱼*1，小水母*1';
			$Award['one'] = 1;
		}
		
		if($type == 2){
			if($Award['two'] != 0){
				return $result;
			}
			Hapyfish2_Island_HFC_Card::addUserCard($uid, 183741, 1);
			$name = '恭喜你成功领取:资质石*1';
			$Award['two'] = 1;
		}
		$feed = array(
				'uid' => $uid,
				'template_id' => 0,
				'actor' => 134,
				'target' => $uid,
				'type' => 3,
				'title' => array('title' => $name),
				'create_time' => time()
			);
		Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
		Hapyfish2_Island_Cache_Vip::updateEventAward($uid, $Award);
		$result['status'] = 1;
		return $result;
	}
	
	public static function getVipStepAward($uid,$vipArr)
	{
		$award = array(
			1=>array(array('cid'=>183841,'num'=>3),array('cid'=>183741,'num'=>5)),
			2=>array(array('cid'=>183841,'num'=>5),array('cid'=>183741,'num'=>10)),
			3=>array(array('cid'=>183841,'num'=>10),array('cid'=>183741,'num'=>20)),
			4=>array(array('cid'=>183841,'num'=>15),array('cid'=>183741,'num'=>30)),
			5=>array(array('cid'=>183841,'num'=>30),array('cid'=>183741,'num'=>50))
		);
		foreach($vipArr as $k => $v){
			$com = new Hapyfish2_Island_Bll_Compensation();
			$reward = $award[$v];
			foreach($reward as $k1 => $v1){
				$com->setItem($v1['cid'], $v1['num']);
			}
			$title = '恭喜你获得VIP'.$v.'奖励:';
			$com->sendOne($uid, $title);
		}
	}
	
	public static function getskipNum($uid)
	{
		$userLimit = Hapyfish2_Island_Cache_Vip::getskipLimit($uid);
		$vip = self::getVipStep($uid);
		$info = Hapyfish2_Island_Cache_Vip::getVipInfo($vip);
		$num = 0;
		if($vip == 4 || $vip == 5){
			return -1;
		}
		if($vip == 0){
			return  0;
		}
		$num = $info['skipNum'] - $userLimit['limit'] > 0?$info['skipNum'] - $userLimit['limit']:0;
		return $num;
	}
}