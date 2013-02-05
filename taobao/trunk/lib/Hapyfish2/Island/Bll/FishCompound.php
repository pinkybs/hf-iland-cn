<?php

class Hapyfish2_Island_Bll_FishCompound
{
	const TXT001 = '条件不足，不能合成';
	const TXT002 = '参赛鱼不能放生';
	const TXT003 = '符石不足';
	const TXT004 = '挑战次数已达上限';
	const TXT005 = '只剩一条鱼不能放生';
	const TXT006 = '冷却时间未到，请换条鱼挑战';
	const TXT007 = '只能挑战排名在自己前面5名内的玩家';
	const TXT008 = '冷却时间未到';
	const TXT009 = '已领取奖励请明天再来';
	const TXT010 = '声望不足';
	const TXT011 = '资质已经最高 ，无法提升';
	const TXT012 = '资质石不足';
	const TXT013 = '没有该卡不能解锁';
	const TXT014 = 'vip等级不够不能解锁';
	const TXT015 = '恭喜你获得通关建筑：';
	const AddCid = 173041;
	const ZIZHICard = 183741;
	const TIAOZHANCard = 185441;
	const PREFIX1 = '[普通]';
	const PREFIX2 = '[精英]';
	const PREFIX3 = '[神奇]';
	const PREFIX4 = '挑战次数已达上限';
	
	
	public static function getId($uid)
	{
		 try {
    		$dalUserSequence = Hapyfish2_Island_Dal_UserSequence::getDefaultInstance();
    		return $dalUserSequence->get($uid, 'e', 1);
    	} catch (Exception $e) {
    	}
    	return 0;
	}
	
	public static function initFishCompound()
	{
		
		 $data = array();
		 $fish = Hapyfish2_Island_Cache_FishCompound::getBasic();
		 if(!$fish){
		 	return $data;
		 }
		 foreach($fish as $id=>$info){
		 	$list = array();
			$list['name'] = $info['name'];
		 	$list['id'] = $info['fid'];
		 	if($info['prefix'] == 1 && $info['type'] == 1 && $info['level'] >0){
		 		$list['name'] = self::PREFIX1.$info['name'];
		 	}
		 	if($info['prefix'] == 2 && $info['type'] == 1 && $info['level'] >0){
		 		$list['name'] = self::PREFIX2.$info['name'];
		 	}
		 	if($info['prefix'] == 3 && $info['type'] == 1 && $info['level'] >0){
		 		$list['name'] = self::PREFIX3.$info['name'];
		 	}
		 	$list['className']	= $info['class_name'];
		 	$list['speed'] = $info['speed'];
		 	$list['skill'] = $info['skill'];
		 	$list['level'] = $info['level'];
		 	$list['needFish'] = $info['condition'];
		 	$list['baseSuccessRate'] = $info['rate'];
		 	$list['maxlevel'] = self::getMaxlevel($info['fid'], $fish);
		 	$list['gemAddition'] = 5;
		 	$data[] = $list;
		 }
		return $data;
	}
	
	public static function getPve()
	{
		$list = array();
//		$data = array(
//			array(
//				'id'=>1,
//				'fishId'=>3,
//				'level'=>2,
//				'length'=>400,
//				'info'=>'[{"distance":100,"obstacle_id":1},{"distance":100,"obstacle_id":1},{"distance":120,"obstacle_id":2},{"distance":150,"obstacle_id":3}]',
//			),
//			array(
//				'id'=>1,
//				'fishId'=>2,
//				'level'=>3,
//				'length'=>400,
//				'info'=>'[{"distance":100,"obstacle_id":1},{"distance":100,"obstacle_id":1},{"distance":120,"obstacle_id":2},{"distance":150,"obstacle_id":3}]',
//			)
//		);
		$track = Hapyfish2_Island_Cache_FishCompound::getFishTrack();
		foreach($track as $id=>$v){
			$data = array();
			$info = array();
			$data['id'] = $id;
			$finfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($v['npc']);
			
			$data['fishId'] = $finfo['fid'];
			$data['level'] = $finfo['level'];
			$data['length']=$v['schedule'];
			$data['title'] = $v['description'];
			$data['content'] = $v['content'];
			$data['recommendlevel'] = $v['recommendLevel'];
			$data['awardString'] = $v['award'];
			$data['btncontent'] = $v['btncontent'];
			$data['challengeNum'] = $v['limit'];
			$ob = json_decode($v['obstacle']);
			$n = count($ob);
			$long = $v['schedule']/($n+1);
			for($i=0;$i<=$n;$i++){
				if($ob[$i]>0){
					$item['distance'] = ($i+1)*$long;
					$item['obstacle_id']= $ob[$i]?$ob[$i]:0;
					$info[] = $item;
				}
			}
			$data['info'] = json_encode($info);
			$list[] = $data;
		}
		return $list;
	}
	
	public static function initFishSkill()
	{
		$data = array();
		$skill = Hapyfish2_Island_Cache_FishCompound::getFishSkill();
		if($skill){
			foreach($skill as $info){
				$list = array();
				$list['id'] = $info['id'];
				$list['name'] = $info['name'];
				$list['level'] = $info['level'];
				$list['className'] = $info['className'];
				$list['effect'] = $info['continue_time'];
				$list['value'] = $info['value'];
				$list['type'] = $info['type'];
				$list['skillclassName'] = $info['skillclassName'];
				$list['description'] = $info['description'];
				$data[] = $list;
			}
		}
		return $data;
	}
	
	
	public static function myFish($uid)
	{
		
		$userFIsh = Hapyfish2_Island_Cache_FishCompound::getUserFishAll($uid);
		$list = array();
		$userGameFish = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		foreach($userFIsh as $fid=>$info){
			$myfish = array();
			$detail = Hapyfish2_Island_Cache_FishCompound::getFishInfo($info['cid']);
			$myfish['id'] = $info['id'];
			$myfish['fishId'] = $detail['fid'];
			$myfish['skill'] = $info['skill'];
			$myfish['isGame'] = 0;
			$myfish['prefix'] = $detail['prefix'];
			$myfish['gameNum'] = $info['gameNum'];
			$myfish['winNum'] = $info['winNum'];
			$myfish['level'] = $detail['level'];
			$fishProficiency = Hapyfish2_Island_Cache_FishCompound::getProficiency($uid,$info['id']);
			$myfish['exp'] = $fishProficiency;
			if($userGameFish == $info['id']){
				$myfish['isGame'] = 1;
			}
			$list[] = $myfish;
		}
		return $list;
	}
	
	public static function joinMatch($uid, $id)
	{
		
	}
	
	public static function compound($uid, $fid = 0, $id = 0, $add = 0)
	{
		$result['status'] = -1;
		$s = 0;
		if($id > 0){
			$type = 2;
			$detail = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
			$bcid = $detail['cid'];
			$info = Hapyfish2_Island_Cache_FishCompound::getFishInfo($bcid); 
			$cid = $info['next_id'];
		}
		
		if($add > 0){
			$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
			if (!isset($userCard[self::AddCid]) || $userCard[self::AddCid]['count'] < $add) {
				$result['status'] = -2;
				$result['content'] = 'serverWord_105';
				return array('result'=>$result);
			}
			$useCard = true;
		}
		
		if($fid > 0){
			$type = 1;
			$comFish = array();
			$basicFish= self::initCompound();
			foreach($basicFish as $finfo){
				if($fid == $finfo['id']){
					$comFish = $finfo;
				}
			}
			if(empty($comFish)){
				return $result;
			}else{
				$sum = array(array_sum($comFish['weight']));
				$tot = 0;
				foreach($comFish['weight'] as $k => $w){
						$tot += $w;
						$aryTmp[$k] = $tot;
				}
				$rnd = mt_rand(1,$tot);

				foreach ($aryTmp as $key=>$value) {
					if ($rnd <= $value) {
						$fkey = $key;
						break;
					}
				}
				$cidList = $comFish['cid'];
				$cid = $cidList[$key];
			}
		}
		
		$info = Hapyfish2_Island_Cache_FishCompound::getFishInfo($cid);
		$fid = $info['fid'];
		if(!$info){
			$result['content'] = 'serverWord_110';
			return array('result'=>$result);
		}
		$unlock = $info['unlock'];
		$isUnlock = self::checkUnlock($uid, $cid);
		if(!$isUnlock){
			$result['content'] = 'serverWord_110';
			return array('result'=>$result);
		}
		$condition = json_decode($info['condition'], true);
		$userFish = Hapyfish2_Island_Cache_Fish::getUserFish($uid);
		
		if(!$userFish){
			$result['content'] = self::TXT001;
			return array('result'=>$result);
		}
		//check鱼是否够 并扣鱼
		$c = count($condition);
		$cn = 0;
		foreach($condition as $k => $v){
			foreach($userFish as $k1 => &$v1){
				if($v[0] == $v1['id']){
					$v1['num'] -= $v[1];
					if($v1['num'] < 0){
						$result['status'] = -2;
						$result['content'] = self::TXT001;
						return array('result'=>$result);
					}else{
						$cn += 1;
					}
				}
			}
		}
		if($cn != $c){
			$result['status'] = -2;
			$result['content'] = self::TXT001;
			return array('result'=>$result);
		}
		Hapyfish2_Island_Cache_Fish::updateUserFish($uid, $userFish);
		$rate = $info['rate'];
		$rate += $add*5;
		$rate = $rate >= 100?100:$rate;
		$r = rand(1, 100);
		if(isset($useCard)){
			Hapyfish2_Island_HFC_Card::useUserCard($uid, self::AddCid, $add, $userCard);
		}
		if($r <= $rate){
			$s = 1;
			if($type == 1){
				self::insertUserFish($uid,$cid);
			}
			if($type == 2){
				$detail['cid'] = $cid;
				Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $detail);
			}
			$result['status'] = 1;
		}
		//uid,fid,前缀,等级，1为合成、2为升级，成功，合成石
		$report = array($uid,$fid,$info['prefix'],$info['level'],$type,$s,$add);
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('comFish', $report);
		return array('result'=>$result,'id'=>$fid);
	}
	
	//检查是否解锁
	public static function checkUnlock($uid, $cid)
	{
		$finfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($cid);
		$userunlock = Hapyfish2_Island_Cache_FishCompound::getUserUnlockFish($uid);
		if($finfo['fish_type'] == 1){
			return true;
		}
		if(in_array($finfo['fish_type'], $userunlock)){
			return true;
		}
		return false;
	}
	
	public static function pVE($uid, $id, $tid)
	{
		$win = 0;
		$isNewFish = 0;
		$isNewAward= 0;
		$useSkillNum = 0;
		$award = array();
		$result['status'] = -1;
		$tInfo = Hapyfish2_Island_Cache_FishCompound::getTrackInfo($tid);
		$limit = Hapyfish2_Island_Cache_FishCompound::getUserTrackLimit($uid);
		$vip = Hapyfish2_Island_Bll_Vip::getVipStep($uid);
		$vipInfo = Hapyfish2_Island_Cache_Vip::getVipInfo($vip);
		$add = 0;
		if($vipInfo){
			$add += $vipInfo['pvegameNum'];
		}
		if(!empty($limit['list'])){
			if(isset($limit['list'][$tid])){
				if($limit['list'][$tid] >= $tInfo['limit'] + $add){
					$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
					if (isset($userCard[self::TIAOZHANCard])){
						$tiaozhan = $userCard[self::TIAOZHANCard]['count'];
					}else {
						$tiaozhan = 0;
					}
					if($tiaozhan < 1){
						$result['content'] = self::TXT004;
						return array('result'=>$result);
					}
					$useCard = 1;
				}
				$limit['list'][$tid] += 1;
			}else{
				$limit['list'][$tid] = 1;
			}
		}else{
			$limit['list'][$tid] = 1;
		}
		if(!$tInfo){
			return array('result'=>$result);
		} 
		
		if($id <= 0){
			$id = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		}
		$info = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		if(!$info){
			return array('result'=>$result);
		}
		$log = Hapyfish2_Util_Log::getInstance();
		$cid = $info['cid'];
		$fInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($cid);
		$npcInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($tInfo['npc']);
		if(!$fInfo){
			return array('result'=>$result);
		}
		$npcspeed = $npcInfo['speed'];
		$speeds = $fInfo['speed'];
		$obstacle = json_decode($tInfo['obstacle'], true);
		$n = count($obstacle);
		$skill = json_decode($info['skill'], true);
		$npcskill = json_decode($npcInfo['skill'], true);
		$npcskillz = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($npcskill[0][0]);
		$npcskillb = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($npcskill[1][0]);
		if($npcskillz){
			$npcvaluez = json_decode($npcskillz['value']);
			$npcctz = $npcskillz['continue_time'];
		}
		if($npcskillb){
			$npcvalueb = $npcskillb['value'];
		}
		
		$skillz = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($skill[0][0]);
		$skillznum = $skill[0][1];
		if($skillz){
			$valuez = json_decode($skillz['value']);
			$ctz = $skillz['continue_time'];
		}
		$skillb = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($skill[1][0]);
		if($skillb){
			$skillbnum = $skill[1][1];
			$valueb = $skillb['value'];
		}
		$myattack = array();
		$mydefence = array();
		$npcattack = array();
		$npcdefence = array();
		if($skill[0][0]>0){
			$myattack['skill_id'] = $skill[0][0];
			$myattack['num'] = 0;
		}
		
		if($skill[1][0]>0){
			$mydefence['skill_id'] = $skill[1][0];
			$mydefence['num'] = 0;
		}
		
		if($npcskill[0][0]>0){
			$npcattack['skill_id'] = $npcskill[0][0];
			$npcattack['num'] = 999;
		}
		
		if($npcskill[1][0]>0){
			$npcdefence['skill_id'] = $npcskill[1][0];
			$npcdefence['num'] = 999;
		}
		$totalLong = $tInfo['schedule'];
		$long = $totalLong/($n+1);
		//第一个技能为主动加速  第二个为被动过障碍
		if($skill[0][0]>0 ){
			$myattack['num'] = $skill[0][1];
		}
		if($skill[1][0]>0 ){
			$mydefence['num'] = $skill[1][1] ;
		}
		$num = 0;
		$adds = 0;
		$npcadds = 0;
		$ctt = 0;
		$npcCtt = 0;
		$addent = 0;
		$npcaddend = 0;
		$slowend = 0;
		$npcslowend = 0;
		$addt = 0;
		$npcaddt = 0;
		$speed = 0;
		$npcstartspeed = 0;
		$t = 0;
		$npct = 0;
		$myMatch = array();
		$npcMatch = array();
		for($i=1;$i<=$n+1;$i++){
			$marr = array();
			$npcarr = array();
			$slowt = 0;
			$npcslowt = 0;
			$oid = $obstacle[$i-1]?$obstacle[$i-1]:0;
			$olong = $long*$i;
			if($t == 0 ){
				$speed = $speeds;
				$t = round($long/$speed, 2);
			}else{
				if($speed*$addt >= $long){
					$t += round($long/$speed, 2);
					$addt -= round($long/$speed, 2);
				}else{
					$t += $addt;
					$t += round(($long - $speed*$addt)/$speeds,2);
					$speed = $speeds;
					$adds = 0;
					$addt = 0;
				}
			}
			if($npct == 0 ){
				$npcstartspeed = $npcspeed;
				$npct = round($long/$npcspeed, 2);
			}else{
				if($npcstartspeed*$npcaddt >= $long){
					$npct += round($long/$npcstartspeed, 2);
					$npcaddt -= round($long/$npcstartspeed, 2);
				}else{
					$npct += $npcaddt;
					$npct += round(($long - $npcstartspeed*$npcaddt)/$npcspeed,2);
					$npcstartspeed = $npcspeed;
					$npcadds = 0;
					$npcaddt = 0;
				}
			}
			if($obstacle[$i-1] && $obstacle[$i-1]>0){
				$marr['sort'] = $i;
				$npcarr['sort'] = $i;
				$oinfo = Hapyfish2_Island_Cache_FishCompound::getObstacleInfo($oid);
				if($oinfo['type'] == 2){
					$slowt = $oinfo['continue_time'];
					$npcslowt = $oinfo['continue_time'];
				}
				$marr['skill_id'] = 0;
				$marr['effect'] = 0;
				$marr['time'] = 0;
				$npcarr['skill_id'] = 0;
				$npcarr['effect'] = 0;
				$npcarr['time'] = 0;
				//1为加速板， 没固定数值
				if($oinfo['type'] == 1){
					if($skill[0][0]> 0 && $skill[0][1] > 0){
						$adds = rand($valuez[0],$valuez[1]);
						$addt = $ctz;
						$skill[0][1] -= 1;
						$log->report('Skill', array($uid,$skill[0][0],1,2));
						$useSkillNum += 1;
						$marr['skill_id'] = $skill[0][0];
						$marr['effect'] = $adds;
						$marr['time'] = $addt;
					}
					
					if($npcskill[0][0]> 0){
						$npcadds = rand($npcvaluez[0],$npcvaluez[1]);
						$npcaddt = $npcctz;
						$npcarr['skill_id'] = $npcskill[0][0];
						$npcarr['effect'] = $npcadds;
						$npcarr['time'] = $npcaddt;
					}
				}
				if($oinfo['type'] == 2){
					if($skill[1][0]> 0 && $skill[1][1]> 0){
						$slowt = $slowt - $valueb>0?$slowt - $valueb:0;
						$addt = $addent - $slowend > 0?$addent - $slowend:0;
						$skill[1][1] -= 1;
						$log->report('Skill', array($uid,$skill[1][0],1,2));
						$useSkillNum += 1;
						$marr['skill_id'] = $skill[1][0];
					}
					if($npcskill[1][0]> 0 && $npcskill[1][1]> 0){
						$npcslowt = $npcslowt - $npcvalueb>0?$npcslowt - $npcvalueb:0;
						$npcaddt = $npcaddend - $npcslowend > 0?$npcaddend - $npcslowend:0;
						$npcarr['skill_id'] = $npcskill[1][0];
					}
					$marr['time'] = $slowt;
					$npcarr['time'] = $npcslowt;
				}
				$speed = $speeds + $adds;
				$t += $slowt;
				$addent = $t + $addt;
				$slowend = $t + $slowt;
				
				$npcstartspeed = $npcspeed + $npcadds;
				$npct += $npcslowt;
				$npcaddend = $npct + $npcaddt;
				$npcslowend = $npct + $npcslowt;
				$myMatch[] = $marr;
				$npcMatch[] = $npcarr;
			}
			
		}
		$result['status'] = 1;
		if(isset($useCard)){
			Hapyfish2_Island_HFC_Card::useUserCard($uid, self::TIAOZHANCard, 1, $userCard);
		}
		if($t < $npct){
			$award = self::produceAward($tid);
			Hapyfish2_Island_Cache_FishCompound::updateAward($uid,$award);
			$win = 1;
			$info['winNum'] += 1;
			$unlock = Hapyfish2_Island_Cache_FishCompound::getUserUnlock($uid);
			if(!$unlock || $tid > $unlock){
				Hapyfish2_Island_Cache_FishCompound::updateUserLock($uid, $tid);
				if($tid == 14){
					$isNewFish = 1;
				}
				if($tid == 21){
					$isNewAward = 192132;
					$com = new Hapyfish2_Island_Bll_Compensation();
					$com->setItem(192132, 1);
					$com->sendOne($uid, self::TXT015);
				}
				
			}
		}
		$info['gameNum'] += 1;
		if($skill[0][1] <= 0){
			$skill[0][0] = 0;
		}
		if($skill[1][1] <= 0){
			$skill[1][0] = 0;
		}
		$info['skill'] = json_encode($skill);
		Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $info);
		//uid,fid,前缀,等级，1为合成、2为升级，成功，合成石
		$report = array($uid,$tid,$win,$useSkillNum);
		
		$log->report('pveFish', $report);
		Hapyfish2_Island_Cache_FishCompound::updateUserLimit($uid, $limit);
//		$myattack = array('skill_id'=>1,'num'=>2);
//		$mydefence = array('skill_id'=>3,'num'=>3);
//		$npcattack = array('skill_id'=>1,'num'=>999);
//		$npcdefence = array('skill_id'=>3,'num'=>999);
//		$myMatch = array(array('skill_id'=>1,'effect'=>4,'time'=>3),array('skill_id'=>3,'effect'=>0,'time'=>1),array('skill_id'=>1,'effect'=>4,'time'=>2),array('skill_id'=>3,'effect'=>0,'time'=>3));
//		$npcMatch = array(array('skill_id'=>1,'effect'=>4,'time'=>4),array('skill_id'=>0,'effect'=>0,'time'=>2),array('skill_id'=>1,'effect'=>4,'time'=>4),array('skill_id'=>0,'effect'=>0,'time'=>3));
		return array('result'=>$result,'myFish'=>array('race'=>$myMatch,'attack'=>$myattack,'defence'=>$mydefence),'npcFish'=>array('race'=>$npcMatch,'attack'=>$npcattack,'defence'=>$npcdefence),'award'=>$award,'win'=>$win,'isNewFish'=>$isNewFish,'isNewAward'=>$isNewAward);
	}
	
	public static function getUserUnlokList($uid)
	{
		$unlock = Hapyfish2_Island_Cache_FishCompound::getUserUnlock($uid);
		$limit = Hapyfish2_Island_Cache_FishCompound::getUserTrackLimit($uid);
		$checkpoint = Hapyfish2_Island_Cache_FishCompound::getTypetrack(1);
		$data = array();
		foreach($checkpoint as $id=>$v){
			$list = array();
			$tag = 0;
			$list['id'] = $id;
			if(isset($limit[$id])){
					$list['surplusNum'] = $v['limit'] - $limit[$id] >= 0?$v['limit'] - $limit[$id]:0;
			} else {
				$list['surplusNum'] = $v['limit'];
			}
			if($unlock && $id <= $unlock){
				$list['lock'] = 1;
			}else{
				if($tag == 0){
					$list['lock'] = 1;
					$tag = 1;
				}
			}
			$data[] = $list;
		}
		return $list;
	}
	
	public static function matchFishInit($uid)
	{
		$unlock = self::getUserFishUnlock($uid);
		$pveIsland = self::getUserCheckpointChartUnlock($uid);
		$myFish = self::myFish($uid);
		$data['catchFishNewFosterFishInitVo'] = $unlock;
		$data['catchFishPVEIslandInitVo'] = $pveIsland['checkPoint'];
		$data['catchFishPVEChartInitVo'] = $pveIsland['chart'];
		$data['catchFishMyFishInitVo'] = $myFish;
		$data['catchFishSkillInitVo'] = self::getUserSkill($uid);
		$data['catchFishGuideInitVo'] = self::getUserGuide($uid);
		return $data;
	}
	
	public static function getUserCheckpointChartUnlock($uid)
	{
		$unlock = Hapyfish2_Island_Cache_FishCompound::getUserUnlock($uid);
		$limit = Hapyfish2_Island_Cache_FishCompound::getUserTrackLimit($uid);
		$vip = Hapyfish2_Island_Bll_Vip::getVipStep($uid);
		$vipInfo = Hapyfish2_Island_Cache_Vip::getVipInfo($vip);
		$add = 0;
		if($vipInfo){
			$add += $vipInfo['pvegameNum'];
		}
		$checkpoint = Hapyfish2_Island_Cache_FishCompound::getTypetrack(1);
		$unlockCheckpoint = array();
		$unlockChart = array();
		$listc = array();
		$tag = 0;
		foreach($checkpoint as $id=>$v){
			$list = array();
			$list['id'] = $id;
			if(!isset($listc[$v['sea']])){
				$listc[$v['sea']] = 0;
			}
			if(isset($limit['list'][$id])){
					$list['surplusNum'] = $v['limit'] + $add - $limit['list'][$id] >= 0?$v['limit'] + $add - $limit['list'][$id]:0;
			} else {
				$list['surplusNum'] = $v['limit'] + $add;
			}
			$list['lock']= 0;
			if($unlock && $id <= $unlock){
				$list['lock'] = 1;
				$listc[$v['sea']] = 1;
			}else{
				if($tag == 0){
					$list['lock'] = 1;
					$listc[$v['sea']] = 1;
					$tag = 1;
				}
			}
			$unlockCheckpoint[] = $list;
		}
		if($listc){
			foreach($listc as $seaId => $unlock){
				$cunlock = array();
				$cunlock['id'] = $seaId;
				$cunlock['lock'] = $unlock;
				$unlockChart[] = $cunlock;
			}
			
		}
		
		return array('checkPoint'=>$unlockCheckpoint, 'chart'=>$unlockChart);
	}
	
	public static function getUserFishUnlock($uid)
	{
		$fish = Hapyfish2_Island_Cache_FishCompound::getBasic();
		$unlock = Hapyfish2_Island_Cache_FishCompound::getUserUnlock($uid);
		$userunlock = Hapyfish2_Island_Cache_FishCompound::getUserUnlockFish($uid);
		if($unlock >= 3){
			if(!in_array(2,$userunlock)){
				$userunlock[] = 2;
				Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userunlock);
			}
		}
		if($unlock >= 8){
			if(!in_array(2,$userunlock)){
				$userunlock[] = 2;
				Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userunlock);
			}
			if(!in_array(3,$userunlock)){
				$userunlock[] = 3;
				Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userunlock);
			}
		}
		
		if($unlock >= 14){
			if(!in_array(2,$userunlock)){
				$userunlock[] = 2;
				Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userunlock);
			}
			if(!in_array(3,$userunlock)){
				$userunlock[] = 3;
				Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userunlock);
			}
			if(!in_array(8,$userunlock)){
				$userunlock[] = 8;
				Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userunlock);
			}
		}
		$data = array();
		$basicFish = array();
		if($fish){
			foreach($fish as $info){
				if($info['level'] == 0){
					$basicFish[] = $info;
				}
			}
		}
		if(!empty($basicFish)){
			foreach($basicFish as $finfo){
				$list = array();
				$list['id'] = $finfo['fid'];
				$list['lock'] = 0;
				if(in_array($finfo['fish_type'],$userunlock) || $finfo['unlock'] == 0){
					$list['lock'] = 1;
				}
				$data[] = $list;
			}	
		}
		return $data;
	}
	
	public static function initChart()
	{
		$list = array();
		$data = array();
		$checkpoint = Hapyfish2_Island_Cache_FishCompound::getTypetrack(1);
		if($checkpoint){
			foreach($checkpoint as $info){
				$list[$info['sea']][] = $info['id'];
			}
		}
		if(!empty($list)){
			foreach($list as $sea=>$t){
				$detail = array();
				$detail['id'] = $sea;
				$detail['islandArray'] = json_encode($t);
				$data[] = $detail;
			}
		}
		return $data;
	}
	
	public static function initCompound()
	{
		$data = array();
		$list = array();
		$fish = Hapyfish2_Island_Cache_FishCompound::getBasic();
		if($fish){
			foreach($fish as $info){
				if($info['level'] == 1 && $info['type'] == 1){
					$list[$info['fish_type']]['fisharray'][] = $info['fid'];
					$list[$info['fish_type']]['weight'][] = $info['weight'];
					$list[$info['fish_type']]['cid'][] = $info['id'];
				}
				if($info['level'] == 0 && $info['type'] == 1){
					$list[$info['fish_type']]['id'] = $info['fid'];
					$list[$info['fish_type']]['unlock'] = $info['unlock'];
					$list[$info['fish_type']]['content'] = $info['content'];
				}
			}
		}
		if(!empty($list)){
			foreach($list as $d){
				$detail = array();
				$detail['id'] = $d['id'];
				$detail['fisharray'] = json_encode($d['fisharray']);
				$detail['weight'] = $d['weight'];
				$detail['cid'] = $d['cid'];
				$detail['lockCustomsPassId'] = $d['unlock'];
				$detail['content'] = $d['content'];
				$data[] = $detail;
			}
		}
		return $data;
	}
	
//	public static function getBasicFish()
//	{
//		$data = array();
//		$fish = Hapyfish2_Island_Cache_FishCompound::getBasic();
//		if($fish){
//			foreach($fish as $k=>$v){
//				if($v['level'] == 1 && $v['prefix'] == 1)
//				{
//					$list[$k] = $v;
//				}
//			}
//		}
//		return $list;
//	}


	public static function getMaxlevel($fid, $fish = array())
	{
		$level = 0;
		if(empty($fish))
		{
			$fish = Hapyfish2_Island_Cache_FishCompound::getBasic();
		}
		foreach($fish as $info){
			if($fid == $info['fid']){
				$level = $level > $info['level']?$level:$info['level'];
			}
		}
		return $level;
	}
	
	public static function changeSkill($uid, $id, $skill)
	{
		$result = array('status'=>-1);
		$detail = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		if(!$detail){
			return $result;
		}
		$oldSkill = json_decode($detail['skill'], true);
		$skillArray = json_decode($skill, true);
		$userSkill = Hapyfish2_Island_Cache_FishCompound::getUserSkill($uid);
		if(!$userSkill){
			$result['content'] = self::TXT003; 
			return $result;
		}
		if($oldSkill[0][0]>0){
			if(isset($userSkill[$oldSkill[0][0]])){
				$userSkill[$oldSkill[0][0]]['count'] += $oldSkill[0][1];
			}else{
				$userSkill[$oldSkill[0][0]]['count'] = $oldSkill[0][1];
				$userSkill[$oldSkill[0][0]]['cid'] = $oldSkill[0][0];
				$userSkill[$oldSkill[0][0]]['uid'] = $uid;
			}
		}
		
		if($oldSkill[1][0]>0){
			if(isset($userSkill[$oldSkill[1][0]])){
				$userSkill[$oldSkill[1][0]]['count'] += $oldSkill[1][1];
			}else{
				$userSkill[$oldSkill[1][0]]['count'] = $oldSkill[1][1];
				$userSkill[$oldSkill[1][0]]['cid'] = $oldSkill[1][0];
				$userSkill[$oldSkill[1][0]]['uid'] = $uid;
			}
		}
		if($userSkill[$skillArray[0][0]] > 0){
			if (!isset($userSkill[$skillArray[0][0]]) || $userSkill[$skillArray[0][0]]['count'] < $skillArray[0][1]) {
				$result['content'] = self::TXT003;
				return $result;
			}
		}
		if($userSkill[$skillArray[1][0]] > 0){
			if (!isset($userSkill[$skillArray[1][0]]) || $userSkill[$skillArray[1][0]]['count'] < $skillArray[1][1]) {
				$result['content'] = self::TXT003;
				return $result;
			}
		}
		$userSkill[$skillArray[0][0]]['count'] -= $skillArray[0][1];
		$userSkill[$skillArray[1][0]]['count'] -= $skillArray[1][1];
		Hapyfish2_Island_Cache_FishCompound::updateUserSkillAll($uid, $userSkill);
		$detail['skill'] = $skill;
		Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $detail);
		$result = array('status'=>1);
		return $result;
	}
	
	public static function insertUserFish($uid,$cid)
	{
		$id = self::getId($uid);
		$data['id'] = $id;
		$data['cid'] = $cid;
		$data['uid'] = $uid;
		$data['skill'] = '[[0,0],[0,0]]';
		$data['status'] = 0;
		$data['gameNum'] = 0;
		$data['winNum'] = 0;
		$userGameFish = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		if(!$userGameFish){
			$data['status'] = 1;
			Hapyfish2_Island_Cache_FishCompound::setUserGameFish($uid, $id);
		}
		
		Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $data);
	}
	
	public static function removeFish($uid, $id)
	{
		$result = array('status'=>-1);
		$userGameFish = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		if($id == $userGameFish){
			$result['content'] = self::TXT002;
			return $result; 
		}
		$detail = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		if(!$detail){
			return $result;
		}
		$userFIsh = Hapyfish2_Island_Cache_FishCompound::getUserFishAll($uid);
		$num = count($userFIsh);
		if($num <= 1){
			$result['content'] = self::TXT005;
			return $result; 
		}
		Hapyfish2_Island_Cache_FishCompound::removeFish($uid, $id);
		$result = array('status'=>1);
		return $result;
	}
	
	public static function initOb()
	{
		$data = array();
		$ob = Hapyfish2_Island_Cache_FishCompound::getFishObstacle();
		foreach($ob as $v){
			$data[] = $v;
		}
		return $data;
	}
	
	public static function getUserSkill($uid)
	{
		$data = array();
		$list = Hapyfish2_Island_Cache_FishCompound::getUserSkill($uid);
		if(!empty($list)){
			foreach($list as $skill){
				$info = array();
				$info['id'] = $skill['cid'];
				$info['num'] = $skill['count'];
				$data[] = $info;
			}
		}
		return $data;
		
	}
	
	public static function exchange($uid, $num)
	{
		$result = array('status'=>-1);
		$cid = 142441;
		$toCid = 173041;
		$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
		if(!$cardInfo){
			return $result;
		}
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < $num*20) {
			$result['content'] = 'serverWord_105';
			return $result;
		}
		
		$data = Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, $num*20, $userCard);
		if (!$data) {
			$result['content'] = 'serverWord_110';
			return $result;
		}
		Hapyfish2_Island_HFC_Card::addUserCard($uid, $toCid, $num);
		$result = array('status'=>1);
		return $result;
	}
	
	public static function switchfish($uid, $id)
	{
		$result = array('status'=>-1);
		$info = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		if(!$info){
			$result['content'] = 'serverWord_110';
			return $result;
		}
		Hapyfish2_Island_Cache_FishCompound::setUserGameFish($uid, $id);
		$result = array('status'=>1);
		return $result;
	}
	
	public static function initGuide()
	{
		$list = array();
		$data = Hapyfish2_Island_Cache_FishCompound::getGuide();
		if($data){
			foreach($data as $id=>$info){
				$list[] = $info;
			}
		}
		return $list;
	}
	
	public static function getUserGuide($uid)
	{
		$list = array();
		$guide = Hapyfish2_Island_Cache_FishCompound::getGuide();
		$userGuide = Hapyfish2_Island_Cache_FishCompound::getUserGuide($uid);
		foreach($guide as $id=>$v){
			$data['isMake'] = 1;
			$processArray = array();
			$data['id'] = $id;
			$guide = json_decode($v['processArray'], true);
			$num = count($guide);
			for($i=1;$i<=$num;$i++){
				if($id <= 3){
					$processArray[] = array($i,1);
				}else{
					$processArray[] = array($i,0);
				}
			}
			if($id <= $userGuide){
				$data['isMake'] = 1;
			}
			$data['processArray'] = json_encode($processArray);
			$list[] = $data;
		}
		return $list;
	}
	
	public static function produceAward($id)
	{
		$list = array();
		$awardList = Hapyfish2_Island_Cache_FishCompound::getAward();
		if(!isset($awardList[$id])){
			return null;
		}
		foreach($awardList[$id] as $key => $value){
			$keys = explode('award',$key);
			if(!$keys[1]){
				continue;
			}
			$award = json_decode($value,true);
			$tot = 0;
			foreach($award as $k=>$v){
				$tot += $v[3];
				$aryTmp[$k] = $tot;
			}
			$rnd = mt_rand(1,$tot);
			foreach ($aryTmp as $k1=>$v1) {
				if ($rnd <= $v1) {
					$akey = $k1;
					break;
				}
			}
			$info = $award[$akey];
			$data['awardType'] = $info[0];
			$data['awardnum'] = $info[2];
			$data['awardCid'] = $info[1];
			$list[] = $data;
		}
		return $list;
	}
	
	public static function getAward($uid,$revoke = -1)
	{
		$cost = 0;
		$goldChange = 0;
		$coinChange = 0;
		$result = array();
		$result['status'] = -1;
		$award = Hapyfish2_Island_Cache_FishCompound::getUserAward($uid);
		if(!$award){
			$result['content'] = 'serverWord_110';
			return array('result'=>$result);
		}
		$revokeArr = explode(',', $revoke);
		if($revoke && $revokeArr[0] >= 0){
			$num = count($revokeArr);
			if($num == 1){
				$cost = 2;
			}else if($num == 2)
			{
				$cost = 6;
			}else if($num == 3)
			{
				$cost = 14;
			}else {
				$cost = 30;
			}
			foreach($revokeArr as $k => $v){
				unset($award[$v]);
			}
		}
		shuffle($award);
		if($cost > 0){
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			if($userGold < $cost){
				$result['content'] = 'serverWord_140';
				return array('result'=>$result);
			}
			$goldInfo = array(
					'uid' => $uid,
					'cost' => $cost,
					'summary' => '赛鱼撤牌' ,
					'user_level' => 1,
					'cid' => 0,
					'num' => 1
				);
			$ok = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
			$goldChange = -$cost;
			if(!$ok){
				$result['content'] = 'serverWord_110';
				return array('result'=>$result);
			}
		}
		
		
		$toAward = $award[0];
		$com = new Hapyfish2_Island_Bll_Compensation();
		if($toAward['awardType'] == 1){
			$com->setCoin($toAward['awardnum']);
			$coinChange = $toAward['awardnum'];
		}else if($toAward['awardType'] == 2){
			$com->setGold($toAward['awardnum']);
			$goldChange = $toAward['awardnum'];
		}else if($toAward['awardType'] == 3 || $toAward['awardType'] == 4){
			$com->setItem($toAward['awardCid'], $toAward['awardnum']);
		}else if ($toAward['awardType'] == 5){
			$finfo = Hapyfish2_Island_Cache_Fish::getFishInfo($toAward['awardCid']);
			$name = $finfo['name'].'x'.$toAward['awardnum'];
			for($i=1;$i<=$toAward['awardnum'];$i++){
				Hapyfish2_Island_Cache_Fish::setUserFish($uid, $toAward['awardCid']);
			}
		}else if ($toAward['awardType'] == 6){
			$sinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($toAward['awardCid']);
			$name = $sinfo['name'];
			Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid,$toAward['awardCid'],$toAward['awardnum']);
			$report = array($uid,$toAward['awardCid'],$toAward['awardnum'],1);
			$log = Hapyfish2_Util_Log::getInstance();
			$log->report('Skill', $report);
		}
		$com->sendOne($uid,'');
		Hapyfish2_Island_Cache_FishCompound::deleteAward($uid);
		if($goldChange > 0){
			$result['goldChange'] = $goldChange;
		}
		if($coinChange > 0){
			$result['coinChange'] = $coinChange;
		}
		if($name){
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
		}
		$result['status'] = 1;
		return array('result'=>$result,'award'=>$toAward);
	}
	
	public static function getFriendFIsh($fuid)
	{
		$list = array();
		$fish = Hapyfish2_Island_Cache_FishCompound::getUserFishAll($fuid);
		if($fish){
			foreach($fish as $k => $v){
				$data = array();
				$status = 1;
				$time = Hapyfish2_Island_Cache_FishCompound::getMtachTime($fuid,$v['id']);
				$finfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($v['cid']);
				if($time > 0){
					$status = 2;
				}
				$data['id'] = $v['id'];
				$data['fishId'] = $finfo['fid'];
				$data['level'] = $finfo['level'];
				$data['status'] = $status;
				$data['endtime'] = $time;
				$data['skillInfo'] = $v['skill'];
				$list[] = $data;
			}
		}
		return $list;
	}
	
	public static function pvp($uid,$fuid,$frid)
	{
		$result['status'] = -1;
		$limit = Hapyfish2_Island_Cache_FishCompound::getPvplimit($uid);
		if($limit['limit'] >= 10){
			$result['content'] = self::TXT004;
			return array('result'=>$result);
		}
		$limit['limit'] += 1;
		$timeLimit = Hapyfish2_Island_Cache_FishCompound::getMtachTime($fuid,$frid);
		if($timeLimit > 0){
			$result['content'] = self::TXT006;
			return array('result'=>$result);
		}
		$id = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		$info = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		$ucid = $info['cid'];
		if(!$info){
			return array('result'=>$result);
		}
		$finfo = Hapyfish2_Island_Cache_FishCompound::getUserFish($fuid, $frid);
		if(!$finfo){
			return array('result'=>$result);
		}
		$fcid = $finfo['cid'];
		$tid = 11;
		$tInfo = Hapyfish2_Island_Cache_FishCompound::getTrackInfo($tid);
		$obstacle = json_decode($tInfo['obstacle'], true);
		$totalLong = $tInfo['schedule'];
		$n = count($obstacle);
		$long = $totalLong/($n+1);
		$myFishInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($ucid);
		$friendFishInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($fcid);
		$myskill = json_decode($info['skill'], true);
		$friendskill = json_decode($finfo['skill'], true);
		$mykillzinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($myskill[0][0]);
		if($mykillzinfo){
			$mykillzvalue = json_decode($mykillzinfo['value'],true);
			$myctz = $mykillzinfo['continue_time'];
			
		}
		$mykillbinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($myskill[1][0]);
		$friendskillzinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($friendskill[0][0]);
		$friendskillbinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($friendskill[1][0]);
		if($friendskillzinfo){
			$friendkillzvalue = json_decode($friendskillbinfo['value'],true);
			$friendctz = $friendskillzinfo['continue_time'];
		}
		$myMatch = array();
		$friendMatch = array();
		$myattack = array();
		$mydefence = array();
		$friendattack = array();
		$frienddefence = array();
		if($myskill[0][0]>0){
			$myattack['skill_id'] = $myskill[0][0];
			$myattack['num'] = $myskill[0][1];
		}
		
		if($myskill[1][0]>0){
			$mydefence['skill_id'] = $myskill[1][0];
			$mydefence['num'] = $myskill[1][1];
		}
		
		if($friendskill[0][0]>0){
			$friendattack['skill_id'] = $friendskill[0][0];
			$friendattack['num'] = $friendskill[0][1];
		}
		
		if($friendskill[1][0]>0){
			$frienddefence['skill_id'] = $friendskill[1][0];
			$frienddefence['num'] = $friendskill[1][1];
		}
		$myadds = 0;
		$friendadds = 0;
		$myctt = 0;
		$friendctt = 0;
		$myaddend = 0;
		$friendaddend = 0;
		$myslowend = 0;
		$friendslowend = 0;
		$myaddt = 0;
		$friendaddt = 0;
		$myspeed = 0;
		$friendspeed = 0;
		$myt = 0;
		$friendt = 0;
		for($i=1;$i<=$n+1;$i++){
			$myarr = array();
			$friendarr = array();
			$myslowt = 0;
			$friendslowt = 0;
			$oid = $obstacle[$i-1]?$obstacle[$i-1]:0;
			$olong = $long*$i;
			if($myt == 0 ){
				$myspeed = $myFishInfo['speed'];
				$myt = round($long/$myspeed, 2);
			}else{
				if($myspeed*$myaddt >= $long){
					$myt += round($long/$myspeed, 2);
					$myaddt -= round($long/$myspeed, 2);
				}else{
					$myt += $myaddt;
					$myt += round(($long - $myspeed*$myaddt)/$myFishInfo['speed'],2);
					$myspeed = $myFishInfo['speed'];
					$myadds = 0;
					$myaddt = 0;
				}
			}
			
			
			if($friendt == 0 ){
				$friendspeed = $friendFishInfo['speed'];
				$friendt = round($long/$friendspeed, 2);
			}else{
				if($friendspeed*$friendaddt >= $long){
					$friendt += round($long/$friendspeed, 2);
					$friendaddt -= round($long/$friendspeed, 2);
				}else{
					$friendt += $friendaddt;
					$friendt += round(($long - $friendspeed*$friendaddt)/$friendFishInfo['speed'],2);
					$friendspeed = $friendFishInfo['speed'];
					$friendadds = 0;
					$friendaddt = 0;
				}
			}
			
			if($obstacle[$i-1] && $obstacle[$i-1]>0){
				$myarr['sort'] = $i;
				$friendarr['sort'] = $i;
				$oinfo = Hapyfish2_Island_Cache_FishCompound::getObstacleInfo($oid);
				if($oinfo['type'] == 2){
					$myslowt = $oinfo['continue_time'];
					$friendslowt = $oinfo['continue_time'];
				}
				$myarr['skill_id'] = 0;
				$myarr['effect'] = 0;
				$myarr['time'] = 0;
				$friendarr['skill_id'] = 0;
				$friendarr['effect'] = 0;
				$friendarr['time'] = 0;
				//1为加速板， 没固定数值
				if($oinfo['type'] == 1){
					if($myskill[0][0]> 0 && $myskill[0][1] > 0){
						$myadds = rand($mykillzvalue[0],$mykillzvalue[1]);
						$myaddt = $myctz;
						$myskill[0][1] -= 1;
						$myarr['skill_id'] = $myskill[0][0];
						$myarr['effect'] = $myadds;
						$myarr['time'] = $myaddt;
					}
					if($friendskill[0][0]> 0 && $friendskill[0][1] > 0){
						$friendadds = rand($friendkillzvalue[0],$friendkillzvalue[1]);
						$friendaddt = $friendctz;
						$friendskill[0][1] -= 1;
						$friendarr['skill_id'] = $friendskill[0][0];
						$friendarr['effect'] = $friendadds;
						$friendarr['time'] = $friendaddt;
					}
				}
				//障碍
				if($oinfo['type'] == 2){
					if($myskill[1][0]> 0 && $myskill[1][1]> 0){
						$myslowt = $myslowt - $mykillbinfo['value']>0?$myslowt - $mykillbinfo['value']:0;
						$myaddt = $myaddend - $myslowend > 0?$myaddend - $myslowend:0;
						$myskill[1][1] -= 1;
						$myarr['skill_id'] = $myskill[1][0];
					}
					
					if($friendskill[1][0] > 0 && $friendskill[1][1]> 0){
						$friendslowt = $friendslowt - $friendskillbinfo['value']>0?$friendslowt - $friendskillbinfo['value']:0;
						$friendaddt = $friendaddend - $friendslowend > 0?$friendaddend - $friendslowend:0;
						$friendskill[1][1] -= 1;
						$friendarr['skill_id'] = $friendskill[1][0];
					}
					$myarr['time'] = $myslowt;
					$friendarr['time'] = $friendslowt;
				}
				
				$myspeed = $myFishInfo['speed'] + $myadds;
				$myt += $myslowt;
				$myaddent = $myt + $myaddt;
				$myslowend = $myt + $myslowt;
				$friendspeed = $friendFishInfo['speed'] + $friendadds;
				$friendt += $friendslowt;
				$friendaddend = $friendt + $friendaddt;
				$friendslowend = $friendt + $friendslowt;
				$myMatch[] = $myarr;
				$friendMatch[] = $friendarr;
			}
		}
		Hapyfish2_Island_Cache_FishCompound::updatePvpLimit($uid,$limit);
		Hapyfish2_Island_Cache_FishCompound::updateMatchTime($fuid,$frid);
		if($myskill[0][1] <= 0){
			$myskill[0][0] = 0;
		}
		if($myskill[1][1] <= 0){
			$myskill[1][0] = 0;
		}
		$info['skill'] = json_encode($myskill);
		Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $info);
		if($myt < $friendt){
			$win = 1;
		}else{
			$win = 0;
		}
		$award['prestige'] = self::getPvpAward($win, $myFishInfo['speed'], $friendFishInfo['speed']);
		Hapyfish2_Island_Cache_FishCompound::addUserPrestige($uid, $award['prestige']);
		$result['status'] = 1;
		//1为pvp
		$report = array($uid, 1);
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('pvp', $report);
		return array('result'=>$result,'myFish'=>array('race'=>$myMatch,'attack'=>$myattack,'defence'=>$mydefence),'friendFish'=>array('race'=>$friendMatch,'attack'=>$friendattack,'defence'=>$frienddefence),'award'=>$award,'win'=>$win);
	}
	
	public static function getPvPaward($win, $myspeed, $fspeed)
	{
		$num = 1;
		$speed = $myspeed - $fspeed;
		if($win == 1){
			if($speed <= -10){
				$num = 100;
			}else if($speed >-10 && $speed <=-5){
				$num = 50;
			}else if($speed >-5 && $speed <=-1){
				$num = 10;
			}else if($speed == 0){
				$num = 5;
			}else if($speed >=1 && $speed <=5){
				$num = 3;
			}else if($speed >5 && $speed <=10){
				$num = 2;
			}else if($speed >= 10){
				$num = 1;
			}
		}
		return $num;
	}
	
	
	public static function initarena($uid)
	{
		$rankArr = array();
		$time = time();
		$myReport = array();
		$userRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
		if(!$userRank){
			self::insertRank($uid);
			$userRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
		}
		$limit = Hapyfish2_Island_Cache_FishCompound::getArenaLimit($uid);
		$last = 15 - $limit['limit'] >0 ? 15 - $limit['limit']:0;
		$raward = Hapyfish2_Island_Cache_FishCompound::getReputationAward($uid);
		$myArena['ranking'] = $userRank['rank'];
		$myArena['reputation'] = Hapyfish2_Island_Cache_FishCompound::getUserPrestige($uid);
		$info = Hapyfish2_Platform_Bll_User::getUser($uid);
		$myArena['name'] = $info['name'];
		$myArena['faceString'] = $info['figureurl'];
		$myArena['coolTime'] = Hapyfish2_Island_Cache_FishCompound::getArenaTime($uid);
		$myArena['gameNum'] = $last;
		$myArena['title'] = '';
		$myArena['isgetaward'] = $raward['get'];
		$myArena['everydayReputationNum'] = self::getRankingPrestige($userRank['rank']);
		$myArena['winningStreak'] = $userRank['winningStreak'];
		$word = Hapyfish2_Island_Cache_FishCompound::getHorn();
		if($word){
			$myArena['announcement'] = $word;
		}else{
			if($userRank['winningStreak'] >= 2){
				$myArena['announcement'] = '你霸气外露，在竞技场连胜'.$userRank['winningStreak'].'场';
			}else{
				$myArena['announcement'] = '';
			}
		}
		$initrank = self::getRankVo($uid, $userRank);
		if($initrank){
			foreach($initrank as $k => $v){
				$rank['id'] = $v['rank'];
				$finfo = Hapyfish2_Platform_Bll_User::getUser($v['uid']);
				$rank['name'] = $finfo['name'];
				$rank['faceString'] = $finfo['figureurl'];
				$rank['ranking'] = $v['rank'];
				$rankArr[] = $rank;
			}
		}
		$exchange = self::getPrestigeExchange();
		$report = Hapyfish2_Island_Cache_FishCompound::getUserReport($uid);
		if($report){
			$report = array_reverse($report);
			foreach($report as $k => $v){
				$log['time'] = $time - $v['create_time'];
				$uinfo = Hapyfish2_Platform_Bll_User::getUser($v['uid']);
				$tuinfo = Hapyfish2_Platform_Bll_User::getUser($v['touid']);
				$log['firstname'] = $uinfo['name'];
				if($v['uid'] == $uid){
					$log['firstname'] = '';
				}
				$log['twoname'] = $tuinfo['name'];
				$log['iswin'] = $v['win'];
				$log['myFish'] = json_decode($v['myarr'],true);
				$log['friendFish'] = json_decode($v['farr'],true);
				$log['myFishId'] = $v['mfid'];
				$log['foeId'] = $v['ffid'];
				$log['myFishlevel'] = $v['mlevel'];
				$log['foelevel'] = $v['flevel'];
				$log['ranking'] = $v['lifting'];
				$log['PVPislandId'] = 11;
				$myReport[] = $log;
			}
		}
		$result['status'] = 1;
		return array('result'=>$result,'catchFishGameFishCommonVo'=>$myArena,'catchFishBattlefieldReportVo'=>$myReport,'catchFishReputationExVo'=>$exchange,'catchFishGameFishUserVo'=>$rankArr);		
	}
	
	public static function getPrestigeExchange()
	{
		$data = array();
		$list = Hapyfish2_Island_Cache_FishCompound::prestigeExchange();
		foreach($list as $k=>$v){
			$arr['id'] = $v['id'];
			$arr['reputationNum'] = $v['prestige'];
			$arr['ExCid'] = $v['cid'];
			$arr['type'] = $v['type'];
			$arr['ExNum'] = $v['num'];
			$arr['isvip'] = $v['vip'];
			$data[] = $arr;
		}
		return $data;
	}
	
	public static function iniRank($uid, $userRank)
	{
		$list = array();
		if(!$userRank){
			$userRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
		}
		$n = ceil($userRank['rank']/2000);
		$n1 = ceil((abs($userRank['rank']-5))/2000);
		$rankArr = Hapyfish2_Island_Cache_FishCompound::getTotalRank($n);
		if( $n1 > 0 && $n != $n1){
			$rankArr1 = Hapyfish2_Island_Cache_FishCompound::getTotalRank($n1);
			$rankArr = array_merge($rankArr1, $rankArr);
		}
		if($userRank['rank'] <= 5){
			$arr = array_slice($rankArr,0,6,false);
			$k = array_search($uid, $arr);
			unset($arr[$k]);
		}else{
			$k = array_search($uid, $rankArr);
			$arr = array_slice($rankArr,$k-5,5);	
		}
		$arr = array_reverse($arr);
		if($arr){
			foreach($arr as $userId){
				$list[] = Hapyfish2_Island_Cache_FishCompound::getUserRank($userId);
			}
		}
		return $list;
	}
	
	public static function topRank()
	{
		$list = array();
		$rankArr = Hapyfish2_Island_Cache_FishCompound::getTotalRank(1);
		$arr = array_slice($rankArr,0,10,false);
		if(!empty($arr)){
			foreach($arr as $uid){
				$data = array();
				$userRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
				$data['ranking'] = $userRank['rank'];
				$data['trend'] = $userRank['lifting'];
				$uinfo = Hapyfish2_Platform_Bll_User::getUser($uid);
				$data['name'] = $uinfo['name'];
				$userGameFish = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
				$finfo = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $userGameFish);
				$detail = Hapyfish2_Island_Cache_FishCompound::getFishInfo($finfo['cid']);
				$data['speed'] = $detail['speed'];
				$list[] = $data;
			}
		}
		return $list;
	}
	
	public static function insertRank($uid)
	{
		$dal = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		$max = $dal->getMaxRank();
		if(!$max){
			$max = 0;
		}
		$max += 1;
		$key = 'rank:dekaron:'.$max;
        $lock = Hapyfish2_Cache_Factory::getLock(0);
        $ok = $lock->lock($key);
        if (!$ok) {
        	return false;
        }
        $n =  ceil($max/2000);
        $k = $max - ($n-1)*2000 -1;
        $data[$n][$k] = $uid;
        $eventRank = Hapyfish2_Cache_Factory::getEventRank(0);
        $ok = $eventRank->updateArenaRank($data);
        $info['uid'] = $uid;
        $info['rank'] = $max;
        $info['winningStreak'] = 0;
        $info['lifting'] = 0;
        Hapyfish2_Island_Cache_FishCompound::updateUserRank($uid,$info);
		return $ok;
	}
	
	public static function getRankingPrestige($rank)
	{
		$num = 20;
		if($rank == 1){
			$num = 1500;
		}else if($rank == 2){
			$num = 1000;
		}else if($rank == 3){
			$num = 700;
		}else if($rank == 4){
			$num = 500;
		}else if($rank == 5){
			$num = 400;
		}else if($rank == 6){
			$num = 300;
		}else if($rank == 7){
			$num = 200;
		}else if($rank == 8){
			$num = 150;
		}else if($rank == 9){
			$num = 100;
		}else if($rank >= 10 && $rank <= 30){
			$num = 80;
		}else if($rank >= 31 && $rank <= 50){
			$num = 70;
		}else if($rank >= 51 && $rank <= 100){
			$num = 60;
		}else if($rank >= 101 && $rank <= 500){
			$num = 50;
		}else if($rank >= 501 && $rank <= 2000){
			$num = 30;
		}
		return $num;
	}
	
	public static function dekaron($uid,$rank)
	{
		$result['status'] = -1;
		$key = 'rank:dekaron:'.$rank;
        $lock = Hapyfish2_Cache_Factory::getLock(0);
        $ok = $lock->lock($key);
        if (!$ok) {
        	$result['content'] = 'serverWord_103';
        	return array('result'=>$result);
        }
		$myRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
		$key1 = 'rank:dekaron:'.$myRank['rank'];
        $ok = $lock->lock($key1);
        if (!$ok) {
        	$result['content'] = 'serverWord_103';
        	return array('result'=>$result);
        }
//		if($myRank['rank'] > $rank + 5){
//			$result['content'] = self::TXT007;
//			return array('result'=>$result);
//		}
		$fnr =  ceil($rank/2000);
		$rankArr = Hapyfish2_Island_Cache_FishCompound::getTotalRank($fnr);
		$fk = $rank - ($fnr-1)*2000 -1;
		$mnr = ceil($myRank['rank']/2000);
		if($fnr != $mnr){
			$rankArr1 = Hapyfish2_Island_Cache_FishCompound::getTotalRank($mnr);
		}else{
			$rankArr1 = $rankArr;
		}
		$mk = $myRank['rank'] - ($mnr-1)*2000 -1;
		$fuid = $rankArr[$fk];
		$FriendRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($fuid);
		$limit = Hapyfish2_Island_Cache_FishCompound::getArenaLimit($uid);
		
		if($limit['limit'] >= 15){
			$result['content'] = self::TXT004;
			return array('result'=>$result);
		}
		$limit['limit'] += 1;
		$timeLimit = Hapyfish2_Island_Cache_FishCompound::getArenaTime($uid);
		if($timeLimit > 0){
			$result['content'] = self::TXT008;
			return array('result'=>$result);
		}
		$id = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		$info = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		$ucid = $info['cid'];
		if(!$info){
			return array('result'=>$result);
		}
		$fGameFish = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($fuid);
		$finfo = Hapyfish2_Island_Cache_FishCompound::getUserFish($fuid, $fGameFish);
		if(!$finfo){
			return array('result'=>$result);
		}
		$fcid = $finfo['cid'];
		$tid = 11;
		$tInfo = Hapyfish2_Island_Cache_FishCompound::getTrackInfo($tid);
		$obstacle = json_decode($tInfo['obstacle'], true);
		$totalLong = $tInfo['schedule'];
		$n = count($obstacle);
		$long = $totalLong/($n+1);
		$myFishInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($ucid);
		$friendFishInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($fcid);
		$myskill = json_decode($info['skill'], true);
		$friendskill = json_decode($finfo['skill'], true);
		$mykillzinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($myskill[0][0]);
		if($mykillzinfo){
			$mykillzvalue = json_decode($mykillzinfo['value'],true);
			$myctz = $mykillzinfo['continue_time'];
			
		}
		$mykillbinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($myskill[1][0]);
		$friendskillzinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($friendskill[0][0]);
		$friendskillbinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($friendskill[1][0]);
		if($friendskillzinfo){
			$friendkillzvalue = json_decode($friendskillzinfo['value'],true);
			$friendctz = $friendskillzinfo['continue_time'];
		}
		$myMatch = array();
		$friendMatch = array();
		$myattack = array();
		$mydefence = array();
		$friendattack = array();
		$frienddefence = array();
		if($myskill[0][0]>0){
			$myattack['skill_id'] = $myskill[0][0];
			$myattack['num'] = $myskill[0][1];
		}
		
		if($myskill[1][0]>0){
			$mydefence['skill_id'] = $myskill[1][0];
			$mydefence['num'] = $myskill[1][1];
		}
		
		if($friendskill[0][0]>0){
			$friendattack['skill_id'] = $friendskill[0][0];
			$friendattack['num'] = $friendskill[0][1];
		}
		
		if($friendskill[1][0]>0){
			$frienddefence['skill_id'] = $friendskill[1][0];
			$frienddefence['num'] = $friendskill[1][1];
		}
		$myadds = 0;
		$friendadds = 0;
		$myctt = 0;
		$friendctt = 0;
		$myaddend = 0;
		$friendaddend = 0;
		$myslowend = 0;
		$friendslowend = 0;
		$myaddt = 0;
		$friendaddt = 0;
		$myspeed = 0;
		$friendspeed = 0;
		$myt = 0;
		$friendt = 0;
		for($i=1;$i<=$n+1;$i++){
			$myarr = array();
			$friendarr = array();
			$myslowt = 0;
			$friendslowt = 0;
			$oid = $obstacle[$i-1]?$obstacle[$i-1]:0;
			$olong = $long*$i;
			if($myt == 0 ){
				$myspeed = $myFishInfo['speed'];
				$myt = round($long/$myspeed, 2);
			}else{
				if($myspeed*$myaddt >= $long){
					$myt += round($long/$myspeed, 2);
					$myaddt -= round($long/$myspeed, 2);
				}else{
					$myt += $myaddt;
					$myt += round(($long - $myspeed*$myaddt)/$myFishInfo['speed'],2);
					$myspeed = $myFishInfo['speed'];
					$myadds = 0;
					$myaddt = 0;
				}
			}
			
			if($friendt == 0 ){
				$friendspeed = $friendFishInfo['speed'];
				$friendt = round($long/$friendspeed, 2);
			}else{
				if($friendspeed*$friendaddt >= $long){
					$friendt += round($long/$friendspeed, 2);
					$friendaddt -= round($long/$friendspeed, 2);
				}else{
					$friendt += $friendaddt;
					$friendt += round(($long - $friendspeed*$friendaddt)/$friendFishInfo['speed'],2);
					$friendspeed = $friendFishInfo['speed'];
					$friendadds = 0;
					$friendaddt = 0;
				}
			}
			if($obstacle[$i-1] && $obstacle[$i-1]>0){
				$myarr['sort'] = $i;
				$friendarr['sort'] = $i;
				$oinfo = Hapyfish2_Island_Cache_FishCompound::getObstacleInfo($oid);
				if($oinfo['type'] == 2){
					$myslowt = $oinfo['continue_time'];
					$friendslowt = $oinfo['continue_time'];
				}
				$myarr['skill_id'] = 0;
				$myarr['effect'] = 0;
				$myarr['time'] = 0;
				$friendarr['skill_id'] = 0;
				$friendarr['effect'] = 0;
				$friendarr['time'] = 0;
				//1为加速板， 没固定数值
				if($oinfo['type'] == 1){
					if($myskill[0][0]> 0 && $myskill[0][1] > 0){
						$myadds = rand($mykillzvalue[0],$mykillzvalue[1]);
						$myaddt = $myctz;
						$myskill[0][1] -= 1;
						$myarr['skill_id'] = $myskill[0][0];
						$myarr['effect'] = $myadds;
						$myarr['time'] = $myaddt;
					}
					if($friendskill[0][0]> 0 && $friendskill[0][1] > 0){
						$friendadds = rand($friendkillzvalue[0],$friendkillzvalue[1]);
						$friendaddt = $friendctz;
						$friendskill[0][1] -= 1;
						$friendarr['skill_id'] = $friendskill[0][0];
						$friendarr['effect'] = $friendadds;
						$friendarr['time'] = $friendaddt;
					}
				}
				//障碍
				if($oinfo['type'] == 2){
					if($myskill[1][0]> 0 && $myskill[1][1]> 0){
						$myslowt = $myslowt - $mykillbinfo['value']>0?$myslowt - $mykillbinfo['value']:0;
						$myaddt = $myaddend - $myslowend > 0?$myaddend - $myslowend:0;
						$myskill[1][1] -= 1;
						$myarr['skill_id'] = $myskill[1][0];
					}
					
					if($friendskill[1][0] > 0 && $friendskill[1][1]> 0){
						$friendslowt = $friendslowt - $friendskillbinfo['value']>0?$friendslowt - $friendskillbinfo['value']:0;
						$friendaddt = $friendaddend - $friendslowend > 0?$friendaddend - $friendslowend:0;
						$friendskill[1][1] -= 1;
						$friendarr['skill_id'] = $friendskill[1][0];
					}
					$myarr['time'] = $myslowt;
					$friendarr['time'] = $friendslowt;
				}
				$myspeed = $myFishInfo['speed'] + $myadds;
				$myt += $myslowt;
				$myaddent = $myt + $myaddt;
				$myslowend = $myt + $myslowt;
				$friendspeed = $friendFishInfo['speed'] + $friendadds;
				$friendt += $friendslowt;
				$friendaddend = $friendt + $friendaddt;
				$friendslowend = $friendt + $friendslowt;
				$myMatch[] = $myarr;
				$friendMatch[] = $friendarr;
			}
		}
		$end = Hapyfish2_Island_Bll_Vip::getVipTime($uid);
		Hapyfish2_Island_Cache_FishCompound::updateArenaLimit($uid,$limit);
		Hapyfish2_Island_Cache_FishCompound::updateArenaTime($uid,$end);
		
		if($myskill[0][1] <= 0){
			$myskill[0][0] = 0;
		}
		if($myskill[1][1] <= 0){
			$myskill[1][0] = 0;
		}
		$info['skill'] = json_encode($myskill);
		Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $info);
		if($myt < $friendt){
			$win = 1;
			$fwin = 0;
			$prestige = Hapyfish2_Island_Bll_Vip::getVipPrestige($uid);
		}else{
			$win = 0;
			$fwin = 1;
			$prestige = 5;
		}
		$myRank['lifting'] = 0;
		$FriendRank['lifting'] = 0;
		if($win == 1){
			if($rank < $myRank['rank']){
				$myRank['lifting'] = 1;
				if($rank <= 3){
					self::insertHorn($uid,$fuid,$rank);
				}
				$FriendRank['rank'] = $myRank['rank'];
				$myRank['rank'] = $rank;
				$FriendRank['lifting'] = -1;
				$data[$fnr][$fk] = $uid;
				$data[$mnr][$mk] = $fuid;
				$eventRank = Hapyfish2_Cache_Factory::getEventRank(0);
        		$ok = $eventRank->updateArenaRank($data);
			}
			$myRank['winningStreak'] += 1;
			$FriendRank['winningStreak'] = 0;
		}else{
			$FriendRank['winningStreak'] += 1;
			$myRank['winningStreak'] = 0;
		}
		Hapyfish2_Island_Cache_FishCompound::addUserPrestige($uid, $prestige);
		Hapyfish2_Island_Cache_FishCompound::updateUserRank($uid,$myRank);
		Hapyfish2_Island_Cache_FishCompound::updateUserRank($fuid,$FriendRank);
		$myfish = array('race'=>$myMatch,'attack'=>$myattack,'defence'=>$mydefence);
		$friendFish = array('race'=>$friendMatch,'attack'=>$friendattack,'defence'=>$frienddefence);
		$result['status'] = 1;
		$time = time();
		$mreport['uid'] = $uid;
		$mreport['touid'] = $fuid;
		$mreport['win'] = $win;
		$mreport['lifting'] = $myRank['lifting'];
		$mreport['mfid'] = $myFishInfo['fid'];
		$mreport['mlevel'] = $myFishInfo['level'];
		$mreport['ffid'] = $friendFishInfo['fid'];
		$mreport['flevel'] = $friendFishInfo['level'];
		$mreport['myarr'] = json_encode($myfish);
		$mreport['farr'] = json_encode($friendFish);
		$mreport['create_time'] = $time;
		$freport['uid'] = $uid;
		$freport['touid'] = $fuid;
		$freport['win'] = $fwin;
		$freport['lifting'] = $FriendRank['lifting'];
		$freport['ffid'] = $myFishInfo['fid'];
		$freport['flevel'] = $myFishInfo['level'];
		$freport['mfid'] = $friendFishInfo['fid'];
		$freport['mlevel'] = $friendFishInfo['level'];
		$freport['farr'] = json_encode($myfish);
		$freport['myarr'] = json_encode($friendFish);
		$freport['create_time'] = $time;
		Hapyfish2_Island_Cache_FishCompound::updateReport($uid,$mreport);
		Hapyfish2_Island_Cache_FishCompound::updateReport($fuid,$freport);
		Hapyfish2_Island_Bll_Vip::event($uid, $win);
		$award['prestige'] = $prestige;
		$lock->unlock($key);
		$lock->unlock($key1);
		//2为竞技场
		$report = array($uid, 2);
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('pvp', $report);
		return array('result'=>$result,'myFish'=>$myfish,'friendFish'=>$friendFish,'iswin'=>$win,'award'=>$award,'PVPislandId'=>11,'myFishId'=>$myFishInfo['fid'],'foeId'=>$friendFishInfo['fid'],'myFishlevel'=>$myFishInfo['level'],'foelevel'=>$friendFishInfo['level']);
	}
	
	public static function getReAward($uid)
	{
		$result['status'] = -1;
		$raward = Hapyfish2_Island_Cache_FishCompound::getReputationAward($uid);
		if($raward['get'] == 1){
			$result['content'] = self::TXT009;
			return array('result'=>$result);
		}
		$myRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
		$num = self::getRankingPrestige($myRank['rank']);
		Hapyfish2_Island_Cache_FishCompound::addUserPrestige($uid, $num);
		$raward['get'] = 1;
		Hapyfish2_Island_Cache_FishCompound::updateReputationAward($uid, $raward);
		$result['status'] = 1;
		return array('result'=>$result);
	}
	
	public static function getZiZhi()
	{
		$data = array(array('id'=>1,'name'=>'普通','num'=>1),array('id'=>2,'name'=>'精英','num'=>2));
		return $data;
	}
	
	public static function getReExchange($uid,$id)
	{
		$result['status'] = -1; 
		$list = Hapyfish2_Island_Cache_FishCompound::prestigeExchange();
		if(!isset($list[$id])){
			return array('result'=>$result);
		}
		$userVip = Hapyfish2_Island_Bll_Vip::getVipStep($uid);
		$info = $list[$id];
		if($userVip < $info['vip']){
			$result['content'] = self::TXT014;
			return array('result'=>$result);
		}
		$userNum = Hapyfish2_Island_Cache_FishCompound::getUserPrestige($uid);
		if($userNum < $info['prestige']){
			$result['content'] = self::TXT010;
			return array('result'=>$result);
		}
		$userNum -= $info['prestige'];
		$com = new Hapyfish2_Island_Bll_Compensation();
		if($info['type'] == 1){
			$com->setItem($info['cid'], $info['num']);
			$com->sendOne($uid, '成功兑换:');
		}else if($info['type'] == 2){
			$sinfo = Hapyfish2_Island_Cache_FishCompound::getSkillInfo($info['cid']);
			Hapyfish2_Island_Cache_FishCompound::addUserSkill($uid, $info['cid'], $info['num']);
			$name = $sinfo['name'];
		}else if($info['type'] == 3){
			$finfo = Hapyfish2_Island_Cache_Fish::getFishInfo($info['cid']);
			Hapyfish2_Island_Cache_Fish::setUserFish($uid, $info['cid']);
			$name = $finfo['name'];
		}else if($info['type'] == 4){
			$com->setItem($info['cid'], $info['num']);
			$com->sendOne($uid, '成功兑换:');
		}else if($info['type'] == 5){
			$com->setCoin($info['num']);
			$com->sendOne($uid, '成功兑换:');
		}else if($info['type'] == 6){
			$com->setGold($info['num']);
			$com->sendOne($uid, '成功兑换:');
		}
		if($name){
			$name = '成功兑换:'.$name;
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
		}
		//2为使用
		$report = array($id,$info['prestige'],2);
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('Shengwang', $report);
		Hapyfish2_Island_Cache_FishCompound::updateUserPrestige($uid, $userNum);
		$result['status'] = 1;
		return array('result'=>$result);
	}
	
	public static function insertHorn($uid,$fuid,$rank)
	{
		$info = Hapyfish2_Platform_Bll_User::getUser($uid);
		$finfo = Hapyfish2_Platform_Bll_User::getUser($fuid);
		$word = $info['name'].'战胜了'.$finfo['name'].'获得了第'.$rank.'名';
		Hapyfish2_Island_Cache_FishCompound::updateHorn($word);		
	}
	
	public static function changePrefix($uid, $id)
	{
		$s = 0;
		$result['status'] = -1;
		$fish = Hapyfish2_Island_Cache_FishCompound::getUserFish($uid, $id);
		$cid = $fish['cid'];
		$fishInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($cid);
		$fishType = $fishInfo['fish_type'];
		$prefix = $fishInfo['prefix'];
		$level = $fishInfo['level'];
		if($prefix == 3){
			$result['content'] = self::TXT011;
			return array('result'=>$result);
		}
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (isset($userCard[self::ZIZHICard])){
			$num = $userCard[self::ZIZHICard]['count'];
		}else{
			$num = 0;
		}
		if($prefix == 1){
			$need = 1;
		}
		if($prefix == 2){
			$need = 2;
		}
		if($num < $need){
			$result['content'] = self::TXT012;
			return array('result'=>$result);
		}
		$prefix += 1;
		$fishArr = Hapyfish2_Island_Cache_FishCompound::getBasic();
		if($fishArr){
			foreach($fishArr as $data){
				if($prefix == $data['prefix'] && $fishType == $data['fish_type'] && $level == $data['level']){
					$nextCid = $data['id'];
					break;
				}
			}
		}
		$fishProficiency = Hapyfish2_Island_Cache_FishCompound::getProficiency($uid,$id);
		if($fishProficiency >= 100){
			$s = 1;
		}
		$rand = 3;
		$rate = rand(1, 100);
		if($rate <= $rand){
			$s = 1;
		}
		if($s == 1){
			$fishProficiency = 0;
			$result['status'] = 1;
			$fish['cid'] = $nextCid;
			Hapyfish2_Island_Cache_FishCompound::updateUserFish($uid, $fish);
			//3为前缀
			$report = array($prefix, 0, 3);
			$log = Hapyfish2_Util_Log::getInstance();
			$log->report('ziZhi', $report);
			
		}else{
			$fishProficiency += 3;
		}
		$userGameFish = Hapyfish2_Island_Cache_FishCompound::getUserGameFish($uid);
		Hapyfish2_Island_HFC_Card::useUserCard($uid, self::ZIZHICard, $need, $userCard);
		Hapyfish2_Island_Cache_FishCompound::updateProficiency($uid, $id, $fishProficiency);
		$fishInfo = Hapyfish2_Island_Cache_FishCompound::getFishInfo($fish['cid']);
		$myfish['id'] = $fish['id'];
		$myfish['fishId'] = $fishInfo['fid'];
		$myfish['skill'] = $fish['skill'];
		$myfish['isGame'] = 0;
		$myfish['prefix'] = $fishInfo['prefix'];
		$myfish['gameNum'] = $fish['gameNum'];
		$myfish['winNum'] = $fish['winNum'];
		$myfish['level'] = $fishInfo['level'];
		$fishProficiency = Hapyfish2_Island_Cache_FishCompound::getProficiency($uid,$fish['id']);
		$myfish['exp'] = $fishProficiency;
		if($userGameFish == $fish['id']){
			$myfish['isGame'] = 1;
		}
		return array('result'=>$result, 'catchFishMyFishInitVo'=>$myfish);
	}
	
	public static function getRankVo($uid, $userRank)
	{
		if(!$userRank){
			$userRank = Hapyfish2_Island_Cache_FishCompound::getUserRank($uid);
		}
		$rank = $userRank['rank'];
		if($rank <= 100){
			$list = self::iniRank($uid, $userRank);
		}else{
			for($i=1;$i<=5;$i++){
				$data[] = self::getRankKey(&$rank);
			}
			unset($rank);
			foreach($data as $rankKey){
				$n =  ceil($rankKey/2000);
        		$k = $rankKey - ($n-1)*2000 - 1;
        		$rankArr1 = Hapyfish2_Island_Cache_FishCompound::getTotalRank($n);
        		$info = $rankArr1[$k];
        		$detail = Hapyfish2_Island_Cache_FishCompound::getUserRank($info);
        		$list[] = $detail;
			}
		}
		return $list;
	}
	
	public static function getRankKey($rank)
	{
		if($rank > 10000){
			$rank -= 2000;
			if($rank < 10000){
				$rank += 1000;
			}
		}else if($rank <= 10000 & $rank >1000 ){
			$rank -= 1000;
			if($rank < 1000){
				$rank += 900;
			}
		}else if($rank <= 1000 & $rank >100 ){
			$rank -= 100;
			if($rank < 100){
				$rank += 99;
			}
		}else{
			$rank -= 1;
		}
		return $rank;
	}
	
	public static function unlockFish($uid, $fid)
	{
		$result['status'] = -1;
		$userUnlockFIsh = Hapyfish2_Island_Cache_FishCompound::getUserUnlockFish($uid);
		$basic = Hapyfish2_Island_Cache_FishCompound::getBasic();
		foreach($basic as $k=>$v){
			if($fid == $v['fid']){
				$fishType = $v['fish_type'];
				$unlockCard = $v['unlock'];
				break;
			}
		}
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($unlockCard)) {
			$num = 0;
		}else{
			$num = $userCard[$unlockCard]['count'];
		}
		if($num <= 0){
			$result['content'] = self::TXT013;
			return $result;
		}
		$userUnlockFIsh[] = $fishType;
		Hapyfish2_Island_HFC_Card::useUserCard($uid, $unlockCard);
		Hapyfish2_Island_Cache_FishCompound::updateUserUnlockFish($uid, $userUnlockFIsh);
		$result['status'] = 1;
		return $result;
	}
}