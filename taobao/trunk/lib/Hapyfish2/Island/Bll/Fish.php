<?php

class Hapyfish2_Island_Bll_Fish
{
	//开船花费宝石	
	public static $GsailGold = array(
			1=>array(2=>1, 3=>2, 4=>3, 5=>4),
			2=>array(1=>1, 3=>1, 4=>2, 5=>3),
			3=>array(1=>2, 2=>1, 4=>1, 5=>2),
			4=>array(1=>3, 2=>2, 3=>1, 5=>1),
			5=>array(1=>4, 2=>3, 3=>2, 4=>1),
			6=>array(7=>1, 8=>2, 9=>3, 10=>4),
			7=>array(6=>1, 8=>1, 9=>2, 10=>3),
			8=>array(6=>2, 7=>1, 9=>1, 10=>2),
			9=>array(6=>3, 7=>2, 8=>1, 10=>1),
			10=>array(6=>4, 7=>3, 8=>2, 9=>1),
			11=>array(12=>1, 13=>2, 14=>3, 15=>4),
			12=>array(11=>1, 13=>1, 14=>2, 15=>3),
			13=>array(11=>2, 12=>1, 14=>1, 15=>2),
			14=>array(11=>3, 12=>2, 13=>1, 15=>1),
			15=>array(11=>4, 12=>3, 13=>2, 14=>1),
			16=>array(17=>1, 18=>2, 19=>3, 20=>4),
			17=>array(16=>1, 18=>1, 19=>2, 20=>3),
			18=>array(16=>2, 17=>1, 19=>1, 20=>2),
			19=>array(16=>3, 17=>2, 18=>1, 20=>1),
			20=>array(16=>4, 17=>3, 18=>2, 18=>1),
			21=>array(22=>1, 23=>2, 24=>3, 25=>4),
			22=>array(21=>1, 23=>1, 24=>2, 25=>3),
			23=>array(21=>2, 22=>1, 24=>1, 25=>2),
			24=>array(21=>3, 22=>2, 23=>1, 25=>1),
			25=>array(21=>4, 22=>3, 23=>2, 24=>1)
	);
	
	//开船花费时间
	public static $GsailTime = array(
			1=>array(2=>1, 3=>2, 4=>3, 5=>4),
			2=>array(1=>1, 3=>1, 4=>2, 5=>3),
			3=>array(1=>2, 2=>1, 4=>1, 5=>2),
			4=>array(1=>3, 2=>2, 3=>1, 5=>1),
			5=>array(1=>4, 2=>3, 3=>2, 4=>1),
			6=>array(7=>1, 8=>2, 9=>3, 10=>4),
			7=>array(6=>1, 8=>1, 9=>2, 10=>3),
			8=>array(6=>2, 7=>1, 9=>1, 10=>2),
			9=>array(6=>3, 7=>2, 8=>1, 10=>1),
			10=>array(6=>4, 7=>3, 8=>2, 9=>1),
			11=>array(12=>1, 13=>2, 14=>3, 15=>4),
			12=>array(11=>1, 13=>1, 14=>2, 15=>3),
			13=>array(11=>2, 12=>1, 14=>1, 15=>2),
			14=>array(11=>3, 12=>2, 13=>1, 15=>1),
			15=>array(11=>4, 12=>3, 13=>2, 14=>1),
			16=>array(17=>1, 18=>2, 19=>3, 20=>4),
			17=>array(16=>1, 18=>1, 19=>2, 20=>3),
			18=>array(16=>2, 17=>1, 19=>1, 20=>2),
			19=>array(16=>3, 17=>2, 18=>1, 20=>1),
			20=>array(16=>4, 17=>3, 18=>2, 18=>1),
			21=>array(22=>1, 23=>2, 24=>3, 25=>4),
			22=>array(21=>1, 23=>1, 24=>2, 25=>3),
			23=>array(21=>2, 22=>1, 24=>1, 25=>2),
			24=>array(21=>3, 22=>2, 23=>1, 25=>1),
			25=>array(21=>4, 22=>3, 23=>2, 24=>1)
		);
						
	//灾难
	public static $Gstorm = array(
		array('id'=>1,'name'=>'大雾', 'stepBackwardNum'=>1, 'price'=>1, 'content'=>'捕鱼过程中突然碰到了大雾，已经无法分辨方向了'),
		array('id'=>2,'name'=>'暴风雨', 'stepBackwardNum'=>1, 'price'=>1,'content'=>'不好了，暴风雨来了，船只快支持不住了。'),
		array('id'=>3,'name'=>'鲨鱼', 'stepBackwardNum'=>2, 'price'=>2, 'content'=>'前方有几条鲨鱼慢慢靠近过来了，没有办法的话只能返航了。'),
		array('id'=>4,'name'=>'海上漩涡', 'stepBackwardNum'=>3, 'price'=>3, 'content'=>'前方碰到大量漩涡，大家小心了，照这局势看只能返航了。'),
		array('id'=>5,'name'=>'撞到冰山', 'stepBackwardNum'=>4, 'price'=>4, 'content'=>'泰坦尼克号的悲剧要再次上演了吗，我可不想追随他们而去啊！')
	);
	
	//剧情
	public static $Gdrama = array(
		array('id'=>1, 'titleString'=>'亚特兰蒂斯的宝藏', 'content'=>"岛主你好！\n我是乐乐，最近听说了吗？亚特兰蒂斯的遗迹出现在遗忘海域。据说捕获海鱼可以获得遗迹的封印石，收集封印石就有意向不到的收获哦！我们现在所在的快乐海域就有不少海鱼。就从我们的快乐岛开始出发吧！"),
		array('id'=>2, 'titleString'=>'开始捕鱼', 'content'=>'捕鱼的感觉如何？提示，不同的海岛，捕获的海鱼种类都不同，获得的封印石数量可能也不同哦！千万注意不要遇上灾难事件，否则只能返航哦！'),
		array('id'=>3, 'titleString'=>'神秘的遗迹封印石', 'content'=>'哇！这条海鱼是亚特兰蒂斯的海族化身的，拥有遗迹的封印石，继续努力，获得更多封印石吧！记住，在遗迹建筑那里可以兑换封印石哦！'),
		array('id'=>4, 'titleString'=>'开启新航路', 'content'=>'恭喜！你解锁了通往彩虹岛的航路。只要捕获一定种类的海鱼就能解锁新的航路。在海图这里可以查看解锁条件哦！'),
		array('id'=>5, 'titleString'=>'开启新海图', 'content'=>'大消息哦！乐乐已经研究出来通往阳光海域的海图了，只要收集齐珍宝岛上的特定鱼类就可以直接前往了！祝大家一路顺风哦！但是要提醒大家的是：如果选择前往其他海域的话，船只都是从该海域的第一个岛开始航行哦！'),
	);
	
	//小鱼炮
	public static $Gsmall = array('time'=>1800, 'addexp'=>3);
	
	//大鱼炮
	public static $Gbig = array('addexp'=>15, 'price'=>2, 'cid'=>134141);
	
	//封印之石CID
	const CARDID = 142441;
	const AddCard = 173041;
	const ZIZHICard = 183741;
	const TIAOZHANCard = 185441;
	//文字定义
	const TXT001 = '购买';
	const TXT002 = '小鱼炮冷却时间未到';
	const TXT003 = '捕鱼获得奖励:';
	const TXT004 = '该岛屿未开启';
	const TXT005 = '你已经在该岛屿了';
	const TXT006 = '立即开船到岛';
	const TXT007 = '遇到风暴停留岛';
	const TXT008 = '碎片数不够';
	const TXT009 = '升级建筑失败';
	const TXT010 = '兑换建筑失败';
	const TXT011 = '成功兑换建筑';
	const TXT012 = '已经有该类型的建筑了,无需兑换';
	const TXT013 = '金币数不够';
	const TXT014 = '成功兑换';
	const TXT015 = '不能重复领取';
	const TXT016 = '数量不足，不能领取';
	const TXT017 = '每日捕鱼任务奖励：';
	const TXT018 = '您已经使用了刷鱼卡，不能重复使用';
	const TXT019 = '刷鱼';
	const TXT020 = '大鱼炮数量不足';
	const FIRSTSKILL = 3;
	const SECONDSKILL = 5;
	/*report log 
		606-购买大鱼炮数量
		607-使用大鱼炮或小鱼炮次数，1-大鱼炮 2-小鱼炮
		608-每个海岛的解锁人数
		609-各种鱼的捕获次数
		610-金币消耗数量
	*/

	
	/**
	 * 获取静态数据
	 */
	public static function getFishStatic()
	{
		$rule = self::getFishRule();
		$mapStatic = self::getMaps();
		$islandStatic = self::getIslands();
		$fishStatic = self::getFishAll();
		$stormVo = self::$Gstorm;
		$fishFragment = self::fragmentInfo();
		$relicPlant = self::getFishPlant();
		$coinRule = self::getCoinRule();
		$drama = self::$Gdrama;
		$fishCompound = Hapyfish2_Island_Bll_FishCompound::initFishCompound();
		$fishSkill = Hapyfish2_Island_Bll_FishCompound::initFishSkill();
		$taskInit = Hapyfish2_Island_Cache_Fish::getTask();
		$pve = Hapyfish2_Island_Bll_FishCompound::getPve();
		$chart = Hapyfish2_Island_Bll_FishCompound::initChart();
		$compound = Hapyfish2_Island_Bll_FishCompound::initCompound();
		$ob = Hapyfish2_Island_Bll_FishCompound::initOb();
		$guide = Hapyfish2_Island_Bll_FishCompound::initGuide();
		$zizhi = Hapyfish2_Island_Bll_FishCompound::getZiZhi();
		$vip = Hapyfish2_Island_Bll_Vip::initVip();	
		$resultVo = array(
			'catchFishIllustratedHandbookStaticVo'	=>	$fishStatic,
			'catchFishGameRuleVo'					=>	$rule,
			'catchFishChartStaticVo'				=>	$mapStatic,
			'catchFishIslandStaticVo'				=>	$islandStatic,
			'catchFishDisasterVo'					=>	$stormVo,
			'catchFishFragmentVo'					=>	$fishFragment,
			'catchFishRelicBuildingVo'				=>	$relicPlant,
			'catchFishCoinRuleVo'					=>	$coinRule,
			'catchFishFisrtGoStaticVo'				=>	$drama,
			'catchFishFosterFishStaticVo'			=>	$fishCompound,
			'catchFishSkillStaticVo'				=> $fishSkill,
			'catchFishTaskStaticVo'					=>	$taskInit,
			'catchFishPVEChartStaticVo'				=> $chart,
			'catchFishNewFosterFishStaticVo'		=> $compound,
			'catchFishPVEIslandStaticVo'			=> $pve,
			'catchFishPVEObstacleStaticVo'			=> $ob,
			'catchFishGuideStaticVo'				=>$guide,
			'catchFishQualificationStoneVo'			=>$zizhi,
			'catchFishVipVo'                        =>$vip,
		);
		return $resultVo;
	}
		
	public static function getFishRule()
	{
		$small = self::$Gsmall;
		$big = self::$Gbig;
		
		$resultVo = array(
			'coolTime'		=>	$small['time'],
			'maxXuliTime'	=>	20,
			'bigCannonPrice'=>	$big['price']
		);	
		return $resultVo;	
	}
	
	public static function getCoinRule()
	{
		$resultVo = array();
		for($i=1;$i<=20;$i++) {
			if($i == 1) {
				$coin = 500;
			}elseif($i <= 10) {
				$coin += 1000; 
			}else {
				$coin += 5000; 
			}
			$resultVo[] = array('id'=>$i, 'coin'=>$coin);
		}
		
		return $resultVo;
	}
	
	/**
	 * 获取海图静态数据
	 */
	public static function getMaps()
	{
		$resultVo = array();
		$maps = Hapyfish2_Island_Cache_Fish::getMaps();
		
		if($maps) {
			foreach($maps as $k=>$v) {
				$resultVo[$k]['id'] = $v['id'];
				$resultVo[$k]['name'] = $v['name'];
				$v['islandids'] = str_replace("，", ",", $v['islandids']);
				$resultVo[$k]['islandArray'] = @explode(",", $v['islandids']);
			}
		}
		
		return $resultVo;
	}
	
	/**
	 * 获取海岛静态数据
	 */
	public static function getIslands()
	{
		$sailTime = self::$GsailTime;
		$resultVo = Hapyfish2_Island_Cache_Fish::getIslands();
		
		if($resultVo) {
			foreach($resultVo as $k=>$v) {
				$v['fishids'] = str_replace("，", ",", $v['fishids']);
				$resultVo[$k]['lockConditionArray'] =$v['fishids'] == 0?array(): @explode(",", $v['fishids']);
				$resultVo[$k]['nextIslandTime'] = $sailTime[$v['id']][$v['id']+1] * 3600;
				
				$fishes = array();
				$fishes = Hapyfish2_Island_Cache_Fish::getFishByIslandid($v['id']);
				if($fishes) {
					foreach($fishes as $v2) {
						$resultVo[$k]['canCaptureArray'][] = $v2['fishid'];
					}
				}
			}
		}
		
		return $resultVo;
	}
	
	/**
	 * 获取用户的捕鱼图鉴
	 */
	public static function getUserFish($uid)
	{
		$userFIsh = Hapyfish2_Island_Cache_FishCompound::getUserFishAll($uid);
		if(!$userFIsh){
			Hapyfish2_Island_Bll_FishCompound::insertUserFish($uid,3);
			for($i=1;$i<=14;$i++){
				Hapyfish2_Island_Cache_Fish::setUserFish($uid, 1);
			}
			for($i=1;$i<=6;$i++){
				Hapyfish2_Island_Cache_Fish::setUserFish($uid, 2);
			}
			for($i=1;$i<=2;$i++){
				Hapyfish2_Island_Cache_Fish::setUserFish($uid, 24);
			}
			$info = array('uid'=>$uid,'cid'=>1,'count'=>1);
			Hapyfish2_Island_Cache_FishCompound::updateUserSkill($uid, $info);
			
		}
		$resultVo = Hapyfish2_Island_Cache_Fish::getUserFish($uid);
		return array('catchFishIllustratedHandbookInitVo'=>$resultVo);
		
	}
		
	/**
	 * 获取鱼图鉴的静态数据
	 */
	public static function getFishAll()
	{
		$resultVo = array();
		$data = Hapyfish2_Island_Cache_Fish::getFishAll();
		if($data) {
			foreach($data as $k=>$v) {
				$resultVo[$k]['id'] = $v['fishid'];
				$resultVo[$k]['name'] = $v['name'];
				$resultVo[$k]['isfish'] = $v['isfish'];
				$resultVo[$k]['type'] = $v['map'];
				$resultVo[$k]['className'] = $v['classname'];
				$resultVo[$k]['captureDifficulty'] = $v['difficulty'];
				$v['islandids'] = str_replace("，", ",", $v['islandids']);
				$resultVo[$k]['outputIsland'] = @explode(",", substr($v['islandids'], 0, -1));
				$awardType = $v['type'];
				$resultVo[$k]['awardType'] = $awardType;
				if($awardType == 0) {
					$resultVo[$k]['awardCid'] = $v['cid'];
				}elseif($awardType == 1) {
					$resultVo[$k]['awardCid'] = $v['cid'];
					$resultVo[$k]['awardNum'] = $v['num'];
				}elseif($awardType == 2) {
					$resultVo[$k]['awardNum'] = $v['coin'];
				}elseif($awardType == 3) {
					$resultVo[$k]['awardNum'] = $v['gold'];
				}elseif($awardType == 4) {
					$resultVo[$k]['awardCid'] = $v['itemid'];
					$resultVo[$k]['awardNum'] = $v['num'];
				}
			}
		}
		return $resultVo;
		
	}
	
	/**
	 * 用户当前小鱼炮冷却时间
	 * @param $uid
	 */
	public static function getTiredTime($uid)
	{
		$now = time();
		//初始化疲劳时间
		$oldTiredTime = Hapyfish2_Island_Cache_Fish::getTiredTime($uid);
		$lastCacheTime = Hapyfish2_Island_Cache_Fish::getLastCatchTime($uid);
		$timeDiff = $now - $lastCacheTime;

		if(($oldTiredTime-$timeDiff) <= 0) {
			$tiredTime = 0;
		}else {
			$tiredTime = $oldTiredTime-$timeDiff;
		}
		return 	$tiredTime;	
	}

	/**
	 * 捕鱼的动态数据
	 * @param $uid
	 */
	public static function fishInit($uid)
	{
		$sailGold = self::$GsailGold;
		//$chartInitVo = array();
		$islandInitVo = array();
		$now = time();
		$sailTime = 0;
		$isLock = 0;
		$id = 0;  //默认海域参数为0
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		if($fishUser['time'] != date('Ymd')) {
			$fishUser['time'] = date('Ymd');
			$fishUser['dayNum'] = 0;
			$fishUser['dayCoin'] = 0;
			$fishUser['reduces'] = 0;
		}		
		
		$lock = $fishUser['lock'];
		$islands = Hapyfish2_Island_Cache_Fish::getIslands();
		foreach($islands as $k=>$v) {
			$islandInitVo[$k]['id'] = $v['id'];
			if(in_array($v['id'], $lock)) {
				$isLock = 1;
			}else {
				$isLock = 0;
			}
			$islandInitVo[$k]['lock'] = $isLock;
		}
		
		//查询是否有海神柱
		$isPoseidonArr = Hapyfish2_Island_Cache_Fish::getIsPoseidon($uid);
		$unLockIsland = count($fishUser['lock']);
		
		//海图解锁
		$unlock5 = Hapyfish2_Island_Cache_Fish::getUnlock5($uid);
		if ($unLockIsland >= 21 && $unlock5 == 1) {
			$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
								array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
								array('id' => 3, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[2]),
								array('id' => 4, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[3]),
								array('id' => 5, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[4]));
		}else if ($unLockIsland >= 16) {
			$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
								array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
								array('id' => 3, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[2]),
								array('id' => 4, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[3]),
								array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
		} else if (($unLockIsland >= 11) && ($unLockIsland < 16)) {			
			$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
								array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
								array('id' => 3, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[2]),
								array('id' => 4, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[3]),
								array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
		} else if (($unLockIsland > 5) && ($unLockIsland < 11)) {			
			$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
								array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
								array('id' => 3, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[2]),
								array('id' => 4, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[3]),
								array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
		} else if($unLockIsland <= 5 ){
			$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
								array('id' => 2, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[1]),
								array('id' => 3, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[2]),
								array('id' => 4, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[3]),
								array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
		}
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[self::AddCard])) {
			$maxStone = 0;
		}else{
			$maxStone = $userCard[self::AddCard]['count'];
		}
		if (isset($userCard[self::ZIZHICard])){
			$ziZhi = $userCard[self::ZIZHICard]['count'];
		}else{
			$ziZhi = 0;
		}
		if (isset($userCard[self::TIAOZHANCard])){
			$tiaozhan = $userCard[self::TIAOZHANCard]['count'];
		}else{
			$tiaozhan = 0;
		}
		$skipNum = Hapyfish2_Island_Bll_Vip::getskipNum($uid);
		$isCardAward = Hapyfish2_Island_Cache_FishCompound::getUserAward($uid);
		if(!$isCardAward){
			$isCardAward = array();
		}
		$eventAward = Hapyfish2_Island_Cache_Vip::getEventAward($uid);
		$oneAward = 0;
		$twoAward = 0;
		if($eventAward['one'] == 0){
			$oneAward = 1;
		}
		if($eventAward['two'] == 0){
			$twoAward = 1;
		}
		$vipLevel = Hapyfish2_Island_Bll_Vip::getVipStep($uid);
		$vipgem = Hapyfish2_Island_Cache_Vip::getGem($uid);
		$isnewVip = Hapyfish2_Island_Cache_Vip::getVipMessage($uid);
		$commonUseVo = array();
		$commonUseVo['id'] = $fishUser['map'];
		$commonUseVo['coinNum'] = $fishUser['reduces'];
		$commonUseVo['islandId'] = $fishUser['island'];
		$commonUseVo['goalIslandId'] = $fishUser['nextIsland'];
		$commonUseVo['maxtime'] = $fishUser['sailTime'];
		$commonUseVo['coolTime'] = self::getTiredTime($uid);
		$commonUseVo['bigCannonNum'] = self::getCannon($uid);
		$commonUseVo['skillExp'] = $fishUser['skillExp'];
		$commonUseVo['mixStoneNum'] = $maxStone;
		$commonUseVo['qualificationStoneNum'] = $ziZhi;
		$commonUseVo['isCardAward'] = $isCardAward;
		$commonUseVo['viplevel'] = $vipLevel;
		$commonUseVo['vipgem'] = $vipgem;
		$commonUseVo['skipNum'] = $skipNum;
		$commonUseVo['fishGameCardNum'] = $tiaozhan;
		$commonUseVo['isnewVip'] = $isnewVip;
		$commonUseVo['fisrtaward'] = $oneAward;
		$commonUseVo['twoaward'] = $twoAward;
		if($isnewVip > 0){
			Hapyfish2_Island_Cache_Vip::deleteMessage($uid);
		}
		$lastLimit = Hapyfish2_Island_Cache_FishCompound::getPvplimit($uid);
		$commonUseVo['friendChallengeNum'] = 10 - $lastLimit['limit'] >0? 10 - $lastLimit['limit']:0;
		if($fishUser['nextIsland']) {
			$timeDiff = $fishUser['sailTime'] + $fishUser['sailLastTime'] - $now;
			if($timeDiff > 0) {
				$sailTime = $timeDiff;
				$commonUseVo['quickGoGem'] = $sailGold[$fishUser['island']][$fishUser['nextIsland']];
			}else {
				$commonUseVo['islandId'] = $fishUser['nextIsland'];
				$commonUseVo['goalIslandId'] = 0;
				$fishUser['island'] = $fishUser['nextIsland'];
				$fishUser['nextIsland'] = 0;
				$fishUser['sailTime'] = 0;
				$fishUser['sailLastTime'] = 0;
				Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
			}
		}
		$commonUseVo['time'] = $sailTime;
		
		$commonUseVo['drama'] = 0;
		$userDrama = Hapyfish2_Island_Cache_Fish::getUserDrama($uid);
		if(!$userDrama['firstGo']) {
			$commonUseVo['drama'] = 1;
			$userDrama['firstGo'] = 1;
			Hapyfish2_Island_Cache_Fish::setUserDrama($uid, $userDrama);
		}
		
		if ($userDrama['firstSea'] == 0) {
			$commonUseVo['drama'] = 5;
			$userDrama['firstSea'] = 1;
			Hapyfish2_Island_Cache_Fish::setUserDrama($uid, $userDrama);
		}
		
		//刷鱼卡状态
		$brushCardTime = Hapyfish2_Island_Cache_Fish::getBrushFishCardTime($uid);
		$hasTime = $brushCardTime - $now;
		
		if ($hasTime > 0) {
			$commonUseVo['isCatchFishCard'] = $hasTime;
		} else {
			$commonUseVo['isCatchFishCard'] = 0;
		}
		
		$cidFawn = 183841;
		$commonUseVo['catchFishCardNum'] = 0;
		
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);

		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ($cid == $cidFawn) {
					$commonUseVo['catchFishCardNum'] = $item['count'];
					break;
				}
			}
		}

		return 	array('catchFishIslandInitVo'=>$islandInitVo, 'catchFishChartInitVo'=>$chartInitVo, 'catchFishCommonUseVo'=>$commonUseVo);	
	}	
	
	/**
	 * 捕鱼
	 * 
	 * $cannonId	鱼炮		1-大 鱼炮	2-小鱼炮
	 * $domainId	发炮位置(1,2,3)
	 */
	public static function CatchFish($uid, $cannonId, $domainId, $islandId)
	{
		$logger = Hapyfish2_Util_Log::getInstance();
		$resultVo = array('status'=>-1);
		$small = self::$Gsmall;
		$big = self::$Gbig;			
		$now = time();
		
		//判断鱼炮发射位置
		$domainId = (int)$domainId;
		if($domainId < 0 || $domainId >3) {
			$result['content'] = 'serverWord_110';
			return  array('result'=>$result);	
		}
		
		$tiredTime = self::getTiredTime($uid);
		
		//判断鱼炮
		$userBigCannon = self::getCannon($uid);
		
		$cannonId = (int)$cannonId;
		if($cannonId > 2 || $cannonId < 0) {
			$result['content'] = 'serverWord_110';
			return  array('result'=>$result);
		}
		if($cannonId == 1 && $userBigCannon < 1) {
			$result['content'] = 'serverWord_110';
			return  array('result'=>$result);
		}
		//判断小鱼炮冷却时间
		if($cannonId == 2) {
			if($tiredTime > 0) {
				$result['content'] = self::TXT002;
				return  array('result'=>$result);
			}
		}
		
		$id = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		
		$fishUser['dayNum'] += 1;
		if($fishUser['time'] != date('Ymd')) {
			$fishUser['time'] = date('Ymd');
			$fishUser['dayNum'] = 1;
			$fishUser['dayCoin'] = 0;
			$fishUser['reduces'] = 0;
		}
		
		if($cannonId == 1) {
			if($fishUser['skillExp'] >= 100) {
				$fishUser['skillExp'] = $big['addexp'];
			}else {
				$fishUser['skillExp'] = $fishUser['skillExp'] + $big['addexp'];
				if($fishUser['skillExp'] >= 100) {
					$fishUser['skillExp'] = 100;
				}
			}
			
			Hapyfish2_Island_HFC_Card::useUserCard($uid, $big['cid'], 1);
		}else {
			if($fishUser['skillExp'] >= 100) {
				$fishUser['skillExp'] = $small['addexp'];
			}else {
				$fishUser['skillExp'] = $fishUser['skillExp'] + $small['addexp'];
				if($fishUser['skillExp'] >= 100) {
					$fishUser['skillExp'] = 100;
				}				
			}
			$tiredTime = $small['time'];
			Hapyfish2_Island_Cache_Fish::setTiredTime($uid, $small['time']);
			Hapyfish2_Island_Cache_Fish::setLastCatchTime($uid);
		}
		
		//随机灾难
		$isStorm = self::checkIsStorm($fishUser);
		if($isStorm['isMeetWindstorm'] >= 1) {
			$isStorm['bigCannonNum'] = $cannonId==1?($userBigCannon-1):$userBigCannon;
			$isStorm['coolTime'] = $tiredTime;
			$isStorm['coinNum'] = $fishUser['reduces'];
			$isStorm['skillExp'] = $fishUser['skillExp'];
			$resultVo['status'] = 1;
			$fishUser['step'] = $isStorm['stepBackwardNum'];
			$fishUser['island'] = $isStorm['backwardIsland'];
			$fishUser['stormType'] = $isStorm['isMeetWindstorm'];
			Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
			return array('catchFishCommonUseVo'=>$isStorm, 'result'=>$resultVo);
		}
		
		//$currentIslandId = $fishUser['island'];
		$currentIslandId = $islandId;
		
		//捕获鱼的信息
		$catchFishInfo = self::getCatchFishInfo($uid, $cannonId, $currentIslandId, $domainId, $fishUser['skillExp']);
		$catchFishAward = $catchFishInfo[0];
		$resultVo['status'] = 1;
		$resultVo['coinChange'] = $catchFishAward['awardType']==2?$catchFishAward['num']:0;
		$resultVo['goldChange'] = $catchFishAward['awardType']==3?$catchFishAward['num']:0;
		
		$commonUseVo = array();
		$commonUseVo['id'] = $fishUser['map'];
		//$commonUseVo['islandId'] = $fishUser['island'];
		$commonUseVo['islandId'] = $islandId;
		$commonUseVo['coolTime'] = $tiredTime;
		$commonUseVo['bigCannonNum'] = $cannonId==1?$userBigCannon-1:$userBigCannon;
		$commonUseVo['skillExp'] = $fishUser['skillExp'];
		$commonUseVo['isMeetWindstorm'] = 0;
		$commonUseVo['stepBackwardNum'] = 0;
		$commonUseVo['coinNum'] = $fishUser['reduces'];		
		
		$isNewIsland = 0;
		$userFish = $catchFishInfo[1];
		foreach($userFish as $k=>$v) {
			$tmp1[]=$v['id'];
		}
		$islandInfo = Hapyfish2_Island_Cache_Fish::getIslandInfo($currentIslandId);
		$needFish = $islandInfo['fishids'];
		$needFish = str_replace("，", ",", $needFish);
		$tmp2 = @explode(',', $needFish);	
		if(is_array($tmp1) && is_array($tmp2)) {
			$tmp = array_intersect($tmp2, $tmp1);
			if( $tmp == $tmp2 && !in_array($currentIslandId+1, $fishUser['lock'])  && $currentIslandId < 25 ) {
				$isNewIsland = $currentIslandId+1;
				array_push($fishUser['lock'], $isNewIsland);
				self::updateUserLocks($uid, $fishUser['lock']);
				
				$logger->report('608', array($uid, $currentIslandId+1));
			}
		}
		
		//判断剧情
		$commonUseVo['drama'] = 0;
		$userDrama = Hapyfish2_Island_Cache_Fish::getUserDrama($uid);
		if(!$userDrama['firstCatch'] && $fishUser['dayNum'] == 1) {	//判断是否第一次捕鱼
			$commonUseVo['drama'] = 2;
			$userDrama['firstCatch'] = 1;
		}elseif(!$userDrama['firstCard']) {	//判断是否第一次获得封印石
			if($catchFishAward['awardType'] == 4 && $catchFishAward['awardCid'] == self::CARDID) {  
				$commonUseVo['drama'] = 3;
				$userDrama['firstCard'] = 1;				
			}
		}elseif(!$userDrama['firstNew'] && $isNewIsland > 0) {	//判断第一开辟新岛
			$commonUseVo['drama'] = 4;
			$userDrama['firstNew'] = 1;
		}
		if($commonUseVo['drama'] > 0) {
			Hapyfish2_Island_Cache_Fish::setUserDrama($uid, $userDrama);
		}
		
		
		$commonUseVo['isNewIsland'] = $isNewIsland;
		$commonUseVo['isNewChart'] = 0;
		
		Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
		
		//report log			
		$logger->report('607', array($uid, $cannonId)); 
		
		return array('catchFishAwardVo'=>$catchFishAward, 'result'=>$resultVo, 'catchFishCommonUseVo'=>$commonUseVo);
	}
	
	public static function updateUserLocks($uid, $islandArr)
	{
		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		$locks = implode(',', $islandArr);
		$dalFish->updateUserLocks($uid, $locks);
	}
	
	public static function getCatchFishInfo($uid, $cannonId, $islandId, $domainId, $skillExp, $isBrushFish = 0)
	{
		if ($isBrushFish == 0) {
			$fishArr = array();
			$skillFlag = 0;
			if( intval($skillExp) >= 100 ) {
				$skillFlag = 1;
			}
			$fishes = Hapyfish2_Island_Cache_Fish::getCatchFishes($islandId, $cannonId);
			foreach($fishes as $k=>$v) {
				$fishArr[$k]['fishid'] = $v['fishid'];
				if($skillFlag == 1) {
					$fishArr[$k]['probability'] = $v['probability4'];
				}else {
					if($domainId == 1) {
						$fishArr[$k]['probability'] = $v['probability1'];				
					}elseif($domainId == 2) {
						$fishArr[$k]['probability'] = $v['probability2'];
					}elseif($domainId == 3) {
						$fishArr[$k]['probability'] = $v['probability3'];
					}
				}
			}
			//随机鱼信息
			$allnum = 0;
			$a = 1;
			$randArr = array();
			for($i=0;$i<count($fishArr);$i++) {
				$allnum+=$fishArr[$i]['probability'];
				$randArr[$fishArr[$i]['fishid']] = array($a,$a+$fishArr[$i]['probability']-1);
				$a+=$fishArr[$i]['probability'];
			}
			$num = rand(1,$allnum);
			foreach($randArr as $k=>$v) {
				if($num>=$v[0] && $num<=$v[1]) {
					$fishId = $k;
					break;
				}
			}
			$fishInfo = Hapyfish2_Island_Cache_Fish::getFishInfo($fishId);
			$fishInfo['probability'] = str_replace("，", ",", $fishInfo['probability']);
			$probability = @explode(",", $fishInfo['probability']);
			
			$award = array();
			$randNum = rand(1, 100);
			$awardFlag = 0;
			$type = $fishInfo['type'];
			$award['awardType'] = $type;
			
			if($randNum <= $probability[$cannonId-1]) {	//获得奖励
				$awardFlag = 1;
				$award['num'] = $fishInfo['num'];
				$award['isRubbish'] = 0;
				if($type == 0) {
					$award['isRubbish'] = 1;
				}elseif($type == 1) {
					$award['awardCid'] = $fishInfo['cid'];
				}elseif($type == 2) {
					$award['num'] = $fishInfo['coin'];
				}elseif($type == 3) {
					$award['num'] = $fishInfo['gold'];
				}elseif($type == 4) {
					$award['awardCid'] = $fishInfo['itemid'];
				}
			}else {
				$award['awardType'] = 0;
			}
			
			if($fishInfo['isfish'] == 0) {
				$award['awardType'] = -1;
			}
			
			$award['id'] = $fishId;
			
			$isnew = 1;
			if($fishInfo['istask'] == 0) {
				$isnew = 2;	
			}		
			$userFishes = Hapyfish2_Island_Cache_Fish::getUserFish($uid);
			if($userFishes) {
				foreach($userFishes as $k=>$v) {
					if($v['id'] == $fishId) {
						$isnew = 0;
						break;
					}
				}
			}
	
			$award['isnew'] = $isnew;
			
			//发奖励
			if($awardFlag == 1) {
				if($type >= 1) {
					$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
					if($type == 1) {
						$bllCompensation->setItem($fishInfo['cid'], $fishInfo['num']);
					}elseif($type == 2) {
						$bllCompensation->setCoin($fishInfo['coin']);
					}elseif($type == 3) {
						$bllCompensation->setGold($fishInfo['gold']);
					}elseif($type == 4) {
						$bllCompensation->setItem($fishInfo['itemid'], $fishInfo['num']);
					}
					$bllCompensation->sendOne($uid, self::TXT003);
				}
			}
			
			//更新用户捕鱼图鉴
			$userFish = Hapyfish2_Island_Cache_Fish::setUserFish($uid, $fishId);
			
			//report log	
			$logger = Hapyfish2_Util_Log::getInstance();		
			$logger->report('609', array($uid, $fishId)); 
					
			//每日任务
			$catchFishTaskInitVo = Hapyfish2_Island_Cache_Fish::getCatchFishTaskInitVo($uid);
			
			foreach ($catchFishTaskInitVo as $taskKey => $catchFishTask) {
				if ($fishId == $catchFishTask['fishId']) {
					$catchFishTaskInitVo[$taskKey]['yetCatchNum'] += 1;
					break;
				}
			}
			
			Hapyfish2_Island_Cache_Fish::renewCatchFishTaskInitVo($uid, $catchFishTaskInitVo);
			return array($award, $userFish);
		} else {
			$fishArr = array();
			$fishes = Hapyfish2_Island_Cache_Fish::getCatchFishes($islandId, $cannonId);
			foreach ($fishes as $k => $v) {
				$fishArr[$k]['fishid'] = $v['fishid'];
				if ($domainId == 1) {
					$fishArr[$k]['probability'] = $v['probability1'];				
				} else if ($domainId == 2) {
					$fishArr[$k]['probability'] = $v['probability2'];
				} else if($domainId == 3) {
					$fishArr[$k]['probability'] = $v['probability3'];
				}
			}
			//随机鱼信息
			$allnum = 0;
			$a = 1;
			$randArr = array();
			for ($i = 0; $i < count($fishArr); $i++) {
				$allnum += $fishArr[$i]['probability'];
				$randArr[$fishArr[$i]['fishid']] = array($a, $a + $fishArr[$i]['probability'] - 1);
				$a += $fishArr[$i]['probability'];
			}
			$awardVo = array();
			//循环10次,每次刷10条鱼
			for ($list = 1; $list <= 10; $list++) {
				$award = array();
				
				$num = rand(1, $allnum);
				foreach ($randArr as $k => $v) {
					if ($num >= $v[0] && $num <= $v[1]) {
						$fishId = $k;
						break;
					}
				}
				$fishInfo = Hapyfish2_Island_Cache_Fish::getFishInfo($fishId);
				$fishInfo['probability'] = str_replace("，", ",", $fishInfo['probability']);
				$probability = @explode(",", $fishInfo['probability']);
			
				$award = array();
				$randNum = rand(1, 100);
				$awardFlag = 0;
				$type = $fishInfo['type'];
				$award['awardType'] = $type;
				
				if ($randNum <= $probability[$cannonId - 1]) {	//获得奖励
					$awardFlag = 1;
					$award['num'] = $fishInfo['num'];
					$award['isRubbish'] = 0;
					if ($type == 0) {
						$award['isRubbish'] = 1;
					} else if ($type == 1) {
						$award['awardCid'] = $fishInfo['cid'];
					} else if($type == 2) {
						$award['num'] = $fishInfo['coin'];
					} else if($type == 3) {
						$award['num'] = $fishInfo['gold'];
					} else if($type == 4) {
						$award['awardCid'] = $fishInfo['itemid'];
					}
				} else {
					$award['awardType'] = 0;
				}
				
				if ($fishInfo['isfish'] == 0) {
					$award['awardType'] = -1;
				}
				$award['id'] = $fishId;
		
				$isnew = 1;
				if ($fishInfo['istask'] == 0) {
					$isnew = 2;	
				}
					
				$userFishes = Hapyfish2_Island_Cache_Fish::getUserFish($uid);
				if ($userFishes) {
					foreach($userFishes as $k=>$v) {
						if($v['id'] == $fishId) {
							$isnew = 0;
							break;
						}
					}
				}
		
				$award['isnew'] = $isnew;
				
				$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
				
				//发奖励
				if ($awardFlag == 1) {
					if($type >= 1) {
						if($type == 1) {
							$bllCompensation->setItem($fishInfo['cid'], $fishInfo['num']);
						}elseif($type == 2) {
							$bllCompensation->setCoin($fishInfo['coin']);
						}elseif($type == 3) {
							$bllCompensation->setGold($fishInfo['gold']);
						}elseif($type == 4) {
							$bllCompensation->setItem($fishInfo['itemid'], $fishInfo['num']);
						}
						
						$bllCompensation->sendOne($uid, self::TXT003);
					}
				}
				//更新用户捕鱼图鉴
				$userFish = Hapyfish2_Island_Cache_Fish::setUserFish($uid, $fishId);
				
				//report log	
				$logger = Hapyfish2_Util_Log::getInstance();		
				$logger->report('609', array($uid, $fishId)); 
						
				//每日任务
				$catchFishTaskInitVo = Hapyfish2_Island_Cache_Fish::getCatchFishTaskInitVo($uid);
				
				foreach ($catchFishTaskInitVo as $taskKey => $catchFishTask) {
					if ($fishId == $catchFishTask['fishId']) {
						$catchFishTaskInitVo[$taskKey]['yetCatchNum'] += 1;
						break;
					}
				}
				
				Hapyfish2_Island_Cache_Fish::renewCatchFishTaskInitVo($uid, $catchFishTaskInitVo);
				$awardVo[] = $award;
			}
			return array($awardVo, $userFish);
		}
	}
	
	/**
	 * 随机灾难
	 * 
	 * @param $fishUser
	 */
	public static function checkIsStorm($fishUser)
	{
		$resultVo = array('isMeetWindstorm'=>0);
		$isMeetWindstorm = 0;
		$step = 0;
		$randNum = rand(1,100);
		//$randNum = 1;
		$islandId = $fishUser['island'];
		if($islandId > 1 && $randNum <= 15) {
			$randStormNum = rand(1,100);
			//$randStormNum = 40;
			if($islandId == 2) {
				if($randStormNum <= 30) {
					$step = 1;
					$isMeetWindstorm = 1;
				}elseif($randStormNum <= 55) {
					$step = 1;
					$isMeetWindstorm = 2;
				}
			}elseif($islandId == 3) {
				if($randStormNum <= 30) {
					$step = 1;
					$isMeetWindstorm = 1;
				}elseif($randStormNum <= 55) {
					$step = 1;
					$isMeetWindstorm = 2;
				}elseif($randStormNum <= 75) {
					$step = 2;
					$isMeetWindstorm = 3;
				}				
			}elseif($islandId == 4) {
				if($randStormNum <= 30) {
					$step = 1;
					$isMeetWindstorm = 1;
				}elseif($randStormNum <= 55) {
					$step = 1;
					$isMeetWindstorm = 2;
				}elseif($randStormNum <= 75) {
					$step = 2;
					$isMeetWindstorm = 3;
				}elseif($randStormNum <= 90) {
					$step = 3;
					$isMeetWindstorm = 4;
				}				
			}elseif($islandId == 5) {
				if($randStormNum <= 30) {
					$step = 1;
					$isMeetWindstorm = 1;
				}elseif($randStormNum <= 55) {
					$step = 1;
					$isMeetWindstorm = 2;
				}elseif($randStormNum <= 75) {
					$step = 2;
					$isMeetWindstorm = 3;
				}elseif($randStormNum <= 90) {
					$step = 3;
					$isMeetWindstorm = 4;
				}elseif($randStormNum <= 100) {
					$step = 4;
					$isMeetWindstorm = 5;
				}				
			}
			
			if($isMeetWindstorm > 0) {
				$newIsland = $fishUser['island'] - $step;
				if($newIsland <= 0) {
					$newIsland = 1;
				}
				$resultVo['status'] = 1;
				$resultVo['isMeetWindstorm'] = $isMeetWindstorm;
				$resultVo['id'] = $fishUser['map'];
				$resultVo['islandId'] = $fishUser['island'];
				$resultVo['backwardIsland'] = $newIsland;
				$resultVo['stepBackwardNum'] = $fishUser['island']-$newIsland;
			}
		}
		return 	$resultVo;	
	}
	
	/**
	 * 开船
	 * 
	 * @param $islandId	岛屿ID
	 */
	public static function setSail($uid, $islandId)
	{
		$sailGold = self::$GsailGold;
		$sailTime = self::$GsailTime;
		
		$resultVo = array('status'=>-1);
		$islandId = (int)$islandId;
		if(!$islandId) {
			$resultVo['content'] = 'serverWord_110';
			return array('result'=>$resultVo);
		}
		
		$id = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		
		//判断目标岛屿是否已经开启
		$lock = $fishUser['lock'];
		if(!in_array($islandId, $lock)) {
			$resultVo['content'] = self::TXT004;
			return array('result'=>$resultVo);			
		}
		
		$currentIslandId = $fishUser['island'];
		
		if($currentIslandId == $islandId) {
			$resultVo['content'] = self::TXT005;
			return array('result'=>$resultVo);			
		}		
		
		$needTime = $sailTime[$currentIslandId][$islandId] * 3600;
		$fishUser['sailTime'] = $needTime;
		$fishUser['sailLastTime'] = time();
		$fishUser['nextIsland'] = $islandId;
		Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
		
		$resultVo['status'] = 1;
		$quickGoGem = $sailGold[$currentIslandId][$islandId];
		return 	array('result'=>$resultVo, 'time'=>$needTime, 'quickGoGem'=>$quickGoGem, 'goalIslandId'=>$islandId);	
	}	

	/**
	 * 船只立刻到岛
	 * 
	 * @param 
	 */
	public static function quickGo($uid, $poseidonColumn)
	{
		$sailGold = self::$GsailGold;
		
		$resultVo = array('status'=>-1);

		$id = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		$nextIsland = (int)$fishUser['nextIsland'];
		if(!$nextIsland) {
			$resultVo['content'] = 'serverWord_110';
			return array('result'=>$resultVo);
		}		
		//判断目标岛屿是否已经开启
		$lock = $fishUser['lock'];
		if(!in_array($nextIsland, $lock)) {
			$resultVo['content'] = self::TXT004;
			return array('result'=>$resultVo);			
		}
		
		$currentIslandId = $fishUser['island'];

		if($currentIslandId == $nextIsland) {
			$resultVo['content'] = self::TXT005;
			return array('result'=>$resultVo);			
		}		

		if ($poseidonColumn == 1) {
			$islands = Hapyfish2_Island_Cache_Fish::getIslands();
			foreach($islands as $k=>$v) {
				$islandInitVo[$k]['id'] = $v['id'];
				if(in_array($v['id'], $lock)) {
					$isLock = 1;
				}else {
					$isLock = 0;
				}
				$islandInitVo[$k]['lock'] = $isLock;
			}
			
			//查询是否有海神柱
			$isPoseidonArr = Hapyfish2_Island_Cache_Fish::getIsPoseidon($uid);
			
			//海图解锁
			$unLockIsland = count($fishUser['lock']);
			$unlock5 = Hapyfish2_Island_Cache_Fish::getUnlock5($uid);
			if ($unLockIsland >= 21 && $unlock5 == 1) {
				$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
									array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
									array('id' => 3, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[2]),
									array('id' => 4, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[3]),
									array('id' => 5, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[4]));
			}else if ($unLockIsland >= 16 ) {
				$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
									array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
									array('id' => 3, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[2]),
									array('id' => 4, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[3]),
									array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
			} else if (($unLockIsland >= 11) && ($unLockIsland < 16)) {			
				$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
									array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
									array('id' => 3, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[2]),
									array('id' => 4, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[3]),
									array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
			} else if (($unLockIsland > 5) && ($unLockIsland < 11)) {			
				$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
									array('id' => 2, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[1]),
									array('id' => 3, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[2]),
									array('id' => 4, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[3]),
									array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
			} else if  ($unLockIsland<= 5){
				$chartInitVo = array(array('id' => 1, 'lock' => 1, 'isPoseidon' => $isPoseidonArr[0]),
									array('id' => 2, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[1]),
									array('id' => 3, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[2]),
									array('id' => 4, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[3]),
									array('id' => 5, 'lock' => 0, 'isPoseidon' => $isPoseidonArr[4]));
			}
			
			$isPoseidonCount = 0;
			if ($currentIslandId > 20) {
				$isPoseidonCount = 4;
			} else if ( $currentIslandId > 15) {
				$isPoseidonCount = 3;
			} else if($currentIslandId > 10) {
				$isPoseidonCount = 2;
			} else if (($currentIslandId > 5) && ($currentIslandId < 11)) {
				$isPoseidonCount = 1;
			}
			
			foreach ($chartInitVo as $charKey => $charData) {
				if ($charKey == $isPoseidonCount) {
					if ($charData['isPoseidon'] == 0) {
						$resultVo['content'] = 'serverWord_101';
						return array('result'=>$resultVo);
					}
				}
			}

			$needGold = 0;
			$ok = true;
		} else {
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			
			$needGold = $sailGold[$currentIslandId][$nextIsland];
			if($userGold < $needGold) {
				$resultVo['content'] = "serverWord_140";
				return array('result'=>$resultVo);	
			}
			$goldInfo = array(
	        	'uid' => $uid,
	        	'cost' => $needGold,
	        	'summary' => self::TXT006,
	        	'cid' => 0,
	        	'num' => 1
	        );
	        $ok = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		}
		
        if($ok) {
	 		$fishUser['island'] = $nextIsland;
			$fishUser['sailTime'] = 0;
			$fishUser['sailLastTime'] = 0;
			$fishUser['nextIsland'] = 0;
			Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);  
			$resultVo['status'] = 1;
			$resultVo['goldChange'] = -$needGold;			     	
        }		

		return 	array('result'=>$resultVo);	
	}	
	
	/**
	 * 停留或继续保留岛屿请求
	 * 
	 * $isStop	0-不停留	1-停留
	 */
	public static function stopIsland($uid, $isStop)
	{
		$storm = self::$Gstorm;
		$resultVo = array('status'=>-1);
		
		$isStop = (int)$isStop;
		if($isStop > 1 || $isStop < 0) {
			$resultVo['content'] = 'serverWord_110';
			return array('result'=>$resultVo);			
		}
		
		$id = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		$step = $fishUser['step'];
		$stormType = $fishUser['stormType'];
		if($isStop == 0) {
			/*
			$step = $fishUser['step'];
			$islandId = $fishUser['island'] - $step;
			if($islandId < 1) {
				$islandId = 1;
			}
			$fishUser['island'] = $islandId;
			$fishUser['step'] = 0;
			Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
			*/
			
		}else {
			$needGold = $storm[$stormType-1]['price'];
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			if($userGold < $needGold) {
				$resultVo['content'] = 'serverWord_140';
				return array('result'=>$resultVo);
			}
			$goldInfo = array(
	        	'uid' => $uid,
	        	'cost' => $needGold,
	        	'summary' => self::TXT007,
	        	'cid' => 0,
	        	'num' => 1
	        );		
	        $ok = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
	        if($ok) {
	        	$resultVo['goldChange'] = -$needGold;
	        	$fishUser['island'] = $fishUser['island']+$fishUser['step'];
	        	$fishUser['step'] = 0;
	        	$fishUser['stormType'] = 0;
				Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
	        }
		}
		
		$resultVo['status'] = 1;
		
		$commonUseVo = array();
		$commonUseVo['id'] = $fishUser['map'];
		$commonUseVo['islandId'] = $fishUser['island'];	
		$commonUseVo['time'] = 0;	
		$commonUseVo['coolTime'] = self::getTiredTime($uid);
		$commonUseVo['bigCannonNum'] = self::getCannon($uid);
		$commonUseVo['skillExp'] = $fishUser['skillExp'];
		$commonUseVo['isNewIsland'] = 0;
		$commonUseVo['isNewChart'] = 0;
		$commonUseVo['coinNum'] = $fishUser['reduces'];
		
		return 	array('result'=>$resultVo, 'catchFishCommonUseVo'=>$commonUseVo);
	}
	
	/**
	 * 购买大鱼炮
	 */
	public static function BuyCannon($uid, $cid, $num)
	{
		$resultVo = array('status'=>-1);
		$needCoin = 0;
		$needGold = 0;	
		$now = time();
		
		if( $num <= 0 ) {
			$resultVo['content'] = 'serverWord_110';
			return array('result'=>$resultVo);
		}
		$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
		if(!$cardInfo) {
			$resultVo['content'] = 'serverWord_110';
			return array('result'=>$resultVo);
		}
		if ($cardInfo['price_type'] == 1) {
            $needCoin = $cardInfo['price']*$num;
        }
        else if ($cardInfo['price_type'] == 2) {
        	$needGold = $cardInfo['price']*$num;
        }
        if($needCoin > 0) {
	        $userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
			if ($userCoin < $needCoin) {
		        $resultVo['content'] = 'serverWord_137';
		        return  array('result'=>$resultVo);   
		    }
		    Hapyfish2_Island_HFC_User::decUserCoin($uid, $needCoin);
		    $summary = LANG_PLATFORM_BASE_TXT_13 . $cardInfo['name'];
		    $ok = Hapyfish2_Island_Bll_ConsumeLog::coin($uid, $needCoin, $summary, $now);
        	if($ok) {
				$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
				$bllCompensation->setItem($cardInfo['cid'], $num);
				$bllCompensation->sendOne($uid, self::TXT001.':');		     	
		     }
		     $resultVo['coinChange'] = -$needCoin;
        }		    		    
        if($needGold > 0) {     
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
			if ($userGold < $needGold) {
				$resultVo['content'] = 'serverWord_140';
				return  array('result'=>$resultVo);   
			}
			$goldInfo = array(
		        		'uid' => $uid,
		        		'cost' => $needGold,
		        		'summary' => LANG_PLATFORM_BASE_TXT_13 . $cardInfo['name'],
		        		'user_level' => 1,
		        		'cid' => $cardInfo['cid'],
		        		'num' => $num
		        	);
		     $ok = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
		     if($ok) {
				$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
				$bllCompensation->setItem($cardInfo['cid'], $num);
				$bllCompensation->sendOne($uid, self::TXT001.':');		     	
		     }
			 $resultVo['goldChange'] = -$needGold;			
        } 	
        $resultVo['status'] = 1; 
        
		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('606', array($uid, $num));        
        
        return  array('result'=>$resultVo);			
	}
	
	/**
	 *获取大鱼炮数
	 */
	public static function getCannon($uid)
	{
		$big = self::$Gbig;
		$bigCannonCid = $big['cid'];
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[$bigCannonCid]) || $userCard[$bigCannonCid]['count'] < 1) {
			$num = 0;
		}else {
			$num = $userCard[$bigCannonCid]['count'];
		}	
		return $num;		
	}	
	
	public static function getFishPlant()
	{
		$resultVo = array();
		
		$relicPlants = Hapyfish2_Island_Cache_Fish::getFishPlant();
		if($relicPlants) {
			foreach($relicPlants as $k=>$v) {
				$itemId = $v['item_id'];
				$levelUpMaterial = substr($v['material'], 1, -1);
				$materials = @explode("*", $levelUpMaterial);
				$plants = Hapyfish2_Island_Cache_Fish::getPlantsByItemId($itemId);
				if($plants) {
					foreach($plants as $k1=>$v1) {
						$resultVo[] = array(
							'level'			=>	$v1['content'],
							'currentTime'	=>	$v1['pay_time'],
							'coin'			=>	$v1['ticket'],
							'isgem'			=>	$v['isGem'],
							'name'			=>	$v1['name'],
							'cid'			=>	$v1['cid'],
							'levelUpMaterial'=>	$materials[$v1['content']-1],
							'id'			=>	$v1['item_id']
						);
					}
				}
			}
		}
		return $resultVo;
	}	
	
	/**
	 * 获取用户已经有的遗迹建筑
	 * @param unknown_type $uid
	 */
	public static function getUserRelicPlant($uid)
	{
		$userRelicPlant = array();

		$itemId = "";
		$plants = Hapyfish2_Island_Cache_Fish::getFishPlant();
		//print_r($plants);
		if($plants) {
			$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
			foreach($plants as $k=>$v) {
				$info = array();
				$info = $dalFish->getUserPlantByItemId($uid, $v['item_id']);
				//print_r($info);
				if($info) {
					$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($info['cid']);
					$userRelicPlant[] = array(
						'cid'	=>	$info['cid'],
						'level'	=>	$plantInfo['content'],
						'id'	=>	$info['item_id']
					);	
				}
			}
		}
		$fishFragment = self::fragmentInfo();
		
		return array('catchFishRelicBuildingInitVo'=>$userRelicPlant, 'catchFishFragmentVo'=>$fishFragment);
	}
	
	/**
	 * 通过碎片兑换建筑
	 * 
	 * $cid	要兑换的建筑CID
	 */
	public static function changePlant($uid, $cid)
	{
		$cardId = self::CARDID;
		$resultVo = array('status'=>-1);
		
		$plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($cid);
		if(!$plantInfo) {
			$resultVo['content'] = 'serverWord_110';
			return array('result'=>$resultVo);
		}	
		$fishPlantInfo = Hapyfish2_Island_Cache_Fish::getFishPlantByItemId($plantInfo['item_id']);
		$needFragment = self::getMaterial($fishPlantInfo['material'], $plantInfo['content']);
		
		$fragment = self::getUserFragment($uid);
		$userFragment = $fragment[0]['num'];
		if($userFragment < $needFragment) {
			$resultVo['content'] = self::TXT008;
			return array('result'=>$resultVo);					
		}
		
		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		$info = $dalFish->getUserPlantByItemId($uid, $plantInfo['item_id']);
		if($info) {
			$resultVo['content'] = self::TXT012;
			return array('result'=>$resultVo);
		}
		
		$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
		$bllCompensation->setItem($cid, 1);
		$bllCompensation->sendOne($uid, self::TXT011.':');	
		
		Hapyfish2_Island_HFC_Card::useUserCard($uid, $cardId, $needFragment);
		
		$resultVo['status'] = 1;
		return array('result'=>$resultVo);
					
	}
	
	/**
	 * 通过碎片升级或强化建筑
	 * 
	 * $cid	要升级或强化的建筑CID
	 */
	public static function upgradePlant($uid, $cid)
	{
		$cardId = self::CARDID;
		$now = time();
		$resultVo = array('status'=>-1);

		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		$plant = $dalFish->getUserPlantInfo($uid, $cid);
		$id = $plant['id'];
		$ownerCurrentIsland = $plant['status'];
		$userPlant = Hapyfish2_Island_HFC_Plant::getOne($uid, $id, 1, $ownerCurrentIsland);
		if (!$userPlant) {
            $resultVo['content'] = 'serverWord_115';
            return array('result' => $resultVo);
        }	
        $plantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($userPlant['cid']);
	    $nextCid = $plantInfo['next_level_cid'];
        if (!$plantInfo || !$plantInfo['next_level_cid']) {
        	return array('result' => $resultVo);
        } 

	    $nextLevelPlantInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($nextCid);
        if (!$nextLevelPlantInfo) {
        	return array('result' => $resultVo);
        }

 		$fishPlantInfo = Hapyfish2_Island_Cache_Fish::getFishPlantByItemId($plantInfo['item_id']);
		$needFragment = self::getMaterial($fishPlantInfo['material'], $nextLevelPlantInfo['content']);   
		 
        $fragment = self::getUserFragment($uid);
        $userFragment = $fragment[0]['num'];
		if($userFragment < $needFragment) {
			$resultVo['content'] = self::TXT008;
			return array('result'=>$resultVo);					
		}       
		 
 		$userPlant['level'] += 1;
		$userPlant['cid'] = $nextLevelPlantInfo['cid'];
		$userPlant['pay_time'] = $nextLevelPlantInfo['pay_time'];
		$userPlant['ticket'] = $nextLevelPlantInfo['ticket'];       
		$res = Hapyfish2_Island_HFC_Plant::updateOne($uid, $id, $userPlant, true);
		
		if($res) {
			//扣除用户碎片
			Hapyfish2_Island_HFC_Card::useUserCard($uid, $cardId, $needFragment);
		}else {
            $resultVo['content'] = self::TXT009;
            return array('result' => $resultVo);		
		}
		
		$resultVo['status'] = 1;
		
		$buildingVo = Hapyfish2_Island_Bll_Plant::handlerPlant($userPlant, $now);
		
		return array('result' => $resultVo, 'buildingVo' => $buildingVo);
	}	
	
	public static function getMaterial($material, $level)
	{
		$levelMaterial = substr($material, 1, -1);
		$needMaterial = @explode("*", $levelMaterial);
		$fragmentInfo = $needMaterial[$level-1];
		$fragmentInfo = substr($fragmentInfo, 1, -1);
		$fragments = @explode(",", $fragmentInfo);
		$needFragment = $fragments[1];
		return $needFragment;
	}
		
	/**
	 * 获取用户碎片数
	 */
	public static function getUserFragment($uid)
	{
		$cid = self::CARDID;
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < 1) {
			$num = 0;
		}else {
			$num = $userCard[$cid]['count'];
		}
		$resultVo = array(
			0	=>	array('id'=>$cid, 'num'=>$num)
		);	
		return $resultVo;
	}

	/**
	 * 碎片基本信息
	 */
	public static function fragmentInfo() 
	{
		$cid = self::CARDID;
		$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
		$resultVo = array(
			0	=>	array('id'=>$cid, 'name'=>$cardInfo['name'], 'className'=>$cardInfo['class_name'])
		);
		return $resultVo;
	}
	
	/**
	 * 取消疲劳时间
	 */
	public static function reduceTime($uid)
	{
		$resultVo = array('status'=>-1);
		
		$id = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		$times = (int)$fishUser['dayNum'];
		$dayCoin = (int)$fishUser['dayCoin'];		
		if($fishUser['time'] != date('Ymd')) {
			$times = 0;
			$dayCoin = 0;
			$fishUser['reduces'] = 0;
		}
		$userCoin = Hapyfish2_Island_HFC_User::getUserCoin($uid);
		$needCoin = self::needCoin($fishUser['reduces'], $dayCoin);
		if($needCoin > $userCoin) {
			$resultVo['content'] = self::TXT013;
			return array('result'=>$resultVo);
		}
		Hapyfish2_Island_HFC_User::decUserCoin($uid, $needCoin, 1);
		
		$fishUser['time'] = date('Ymd');
		$fishUser['dayNum'] = $times;
		$fishUser['dayCoin'] = $needCoin;
		$fishUser['reduces'] = (int)$fishUser['reduces'] + 1;
		Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
		
		Hapyfish2_Island_Cache_Fish::setTiredTime($uid, 0);
		//report log
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('610', array($uid, $needCoin));   
		
		$resultVo['status'] = 1;
		$resultVo['coinChange'] = -$needCoin;
		return array('result'=>$resultVo, 'coinNum'=>$fishUser['reduces']);
				
	}
	/**
	 * 取消疲劳时间需要的金币规则
	 */
	public static function needCoin($times, $dayCoin)
	{
		$needCoin = 0;
		
		if($times == 0) {
			$needCoin = 500;
		}elseif($times < 10) {
			$needCoin = $dayCoin + 1000;
		}elseif($times >= 20) {
			$needCoin = 59500;
		}elseif($times >= 10) {
			$needCoin = $dayCoin + 5000;
		}	
		
		return $needCoin;
	}
	
	public static function cardInit($uid)
	{
		$big = self::$Gbig;
		$cid = 110441;
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < 1) {
			$num1 = 0;
		}else {
			$num1 = $userCard[$cid]['count'];
		}	
			
		if (!isset($userCard[$big['cid']]) || $userCard[$big['cid']]['count'] < 1) {
			$num2 = 0;
		}else {
			$num2 = $userCard[$big['cid']]['count'];
		}		
		$fishCardEx = array(
			'card1num'	=>	$num1,
			'card2num'	=>	$num2
		);
		return array('catchFishCardEx'=>$fishCardEx);	
	}	
	
	/**
	 * 兑换卡片
	 */
	public static function changeCard($uid, $num)
	{
		$num = (int)$num;
		$needNum = 2 * $num;
		$big = self::$Gbig;
		$resultVo = array('status'=>-1);
		$cid = 110441;
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < 1) {
			$num1 = 0;
		}else {
			$num1 = $userCard[$cid]['count'];
		}	
			
		if (!isset($userCard[$big['cid']]) || $userCard[$big['cid']]['count'] < 1) {
			$num2 = 0;
		}else {
			$num2 = $userCard[$big['cid']]['count'];
		}	
			
		if($num1 < $needNum) {
			return array('result'=>$resultVo);
		}
		
		Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, $needNum);
		$bllCompensation = new Hapyfish2_Island_Bll_Compensation();
		$bllCompensation->setItem($big['cid'], $num);
		$bllCompensation->sendOne($uid, self::TXT014.':');	
		
		$resultVo['status'] = 1;
		
		$fishCardEx = array(
			'card1num'	=>	$num1-$needNum,
			'card2num'	=>	$num2+$num
		);
		
		return array('result'=>$resultVo, 'catchFishCardEx'=>$fishCardEx);	
	}
	
	
	public static function catchFishChartChange($uid, $id)
	{
		$sailGold = self::$GsailGold;
		$chartInitVo = array();
		$islandInitVo = array();
		$now = time();
		$sailTime = 0;
		$isLock = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		if($fishUser['time'] != date('Ymd')) {
			$fishUser['time'] = date('Ymd');
			$fishUser['dayNum'] = 0;
			$fishUser['dayCoin'] = 0;
			$fishUser['reduces'] = 0;
		}		
		
		$lock = $fishUser['lock'];
		$islands = Hapyfish2_Island_Cache_Fish::getIslands();
		foreach($islands as $k=>$v) {
			$islandInitVo[$k]['id'] = $v['id'];
			if(in_array($v['id'], $lock)) {
				$isLock = 1;
			}else {
				$isLock = 0;
			}
			$islandInitVo[$k]['lock'] = $isLock;
		}
		
		$commonUseVo = array();
		$commonUseVo['id'] = $id;
		$commonUseVo['coinNum'] = $fishUser['reduces'];
		$commonUseVo['islandId'] = $fishUser['island'];
		$commonUseVo['goalIslandId'] = $fishUser['nextIsland'];
		$commonUseVo['maxtime'] = $fishUser['sailTime'];
		$commonUseVo['coolTime'] = self::getTiredTime($uid);
		$commonUseVo['bigCannonNum'] = self::getCannon($uid);
		$commonUseVo['skillExp'] = $fishUser['skillExp'];
		if($fishUser['nextIsland']) {
			$timeDiff = $fishUser['sailTime'] + $fishUser['sailLastTime'] - $now;
			if($timeDiff > 0) {
				$sailTime = $timeDiff;
				$commonUseVo['quickGoGem'] = $sailGold[$fishUser['island']][$fishUser['nextIsland']];
			}else {
				$commonUseVo['islandId'] = $fishUser['nextIsland'];
				$commonUseVo['goalIslandId'] = 0;
				$fishUser['island'] = $fishUser['nextIsland'];
				$fishUser['nextIsland'] = 0;
				$fishUser['sailTime'] = 0;
				$fishUser['sailLastTime'] = 0;
				Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
			}
		}
		$commonUseVo['time'] = $sailTime;
		
		$commonUseVo['drama'] = 0;
		$userDrama = Hapyfish2_Island_Cache_Fish::getUserDrama($uid);
		if(!$userDrama['firstGo']) {
			$commonUseVo['drama'] = 1;
			$userDrama['firstGo'] = 1;
			Hapyfish2_Island_Cache_Fish::setUserDrama($uid, $userDrama);
		}
		
		if ($userDrama['firstSea'] == 0) {
			$userDrama['firstSea'] = 1;
			Hapyfish2_Island_Cache_Fish::setUserDrama($uid, $userDrama);
		}
		
		$result = array('status' => 1);
		$resultVo = array('result' => $result, 'catchFishCommonUseVo' => $commonUseVo);
		
		return $resultVo;
	}
	
	public static function catchFishTaskInit($uid)
	{
		$catchFishTaskInitVo = Hapyfish2_Island_Cache_Fish::getCatchFishTaskInitVo($uid);
		
		foreach ($catchFishTaskInitVo as $key => $catchFishTask) {
			unset($catchFishTaskInitVo[$key]['fishId']);
		}
		
		$resultVo = array('catchFishTaskInitVo' => $catchFishTaskInitVo);
		
		return $resultVo;
	}
	
	public static function catchFishGetTaskAward($uid, $id)
	{
		$result = array('status' => -1);
		
		//任务id限制
		if (!in_array($id, array(1, 2, 3, 4, 5))) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            
            return $resultVo;
		}
		
		//任务数据
		$catchFishTaskInitVo = Hapyfish2_Island_Cache_Fish::getCatchFishTaskInitVo($uid);
		
		foreach ($catchFishTaskInitVo as $catchFishTask) {
			if ($id == $catchFishTask['id']) {
				$fishTask = $catchFishTask;
				break;
			}
		}
		
		if (!$fishTask) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            
            return $resultVo;
		}
		
		//已经领取
		if ($fishTask['isget'] == 1) {
            $result['content'] = self::TXT015;
            $resultVo = array('result' => $result);
            
            return $resultVo;
		}
		
		//任务静态数据
		$taskStaticVo = Hapyfish2_Island_Cache_Fish::getTask();
		
		foreach ($taskStaticVo as $taskStatic) {
			if ($id == $taskStatic['id']) {
				$taskStaticData = $taskStatic;
				break;
			}
		}
		
		if (!$taskStaticData) {
            $result['content'] = 'serverWord_101';
            $resultVo = array('result' => $result);
            
            return $resultVo;
		}
		
		//所需数量不足
		if ($fishTask['yetCatchNum'] < $taskStaticData['catchFishNum']) {
            $result['content'] = self::TXT016;
            $resultVo = array('result' => $result);
            
            return $resultVo;
		}
		
		//发放奖励
		$compensation = new Hapyfish2_Island_Bll_Compensation();

		$coin = 0;
		$gold = 0;
		if ($taskStaticData['type'] == 1) {
			$compensation->setCoin($taskStaticData['awardnum']);
			$coin = $taskStaticData['awardnum'];
			$result['coinChange'] = $taskStaticData['awardnum'];
		} else if ($taskStaticData['type'] == 2) {
			$compensation->setGold($taskStaticData['awardnum'], 1);
			$result['goldChange'] = $taskStaticData['awardnum'];
		} else {
			$compensation->setItem($taskStaticData['awardcid'], $taskStaticData['awardnum']);
			$result['itemBoxChange'] = true;
		}
		
		$ok = $compensation->sendOne($uid, self::TXT017);
		
		if ($ok) {
			foreach ($catchFishTaskInitVo as $key => $catchFishTask) {
				if ($id == $catchFishTask['id']) {
					$catchFishTaskInitVo[$key]['isget'] = 1;
					break;
				}
			}
			
			Hapyfish2_Island_Cache_Fish::renewCatchFishTaskInitVo($uid, $catchFishTaskInitVo);
		}
		
		$result['status'] = 1;
		$resultVo = array('result' => $result);
		
		return $resultVo;
	}
	
	public static function brushFish($uid, $islandId, $type)
	{
		$result = array('status' => -1);
		
		$needCard = 10;
		$cid = 134141;
		
		//$type: 1宝石,2大鱼炮
		if ($type == 2) {
			$cardNum = 0;
			
			//get cards
			$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
	
			$cardVo = array();
			if ($lstCard) {
				foreach ($lstCard as $cidKey => $item) {
					if ($cid == $cidKey) {
						$cardNum = $item['count'];
						break;
					}
				}
			}
			
			//卡片不足
			if ($needCard > $cardNum) {
	    		$result['content'] = self::TXT020;
	    		$resultVo = array('result' => $result);
	    		
	    		return $resultVo;
			}
		} else {
	    	//获得用户gold
			$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
	    	
			$needGold = 20;
			
			//宝石不足
			if ($needGold > $userGold) {
	    		$result['status'] = 'serverWord_140';
	    		$resultVo = array('result' => $result);
	    		
	    		return $resultVo;
			}
		}
		
		$now = time();
		$logger = Hapyfish2_Util_Log::getInstance();
		
		//判断鱼炮发射位置
		$domainId = rand(1, 3);
		
		//判断鱼炮
		$cannonId = 1;
		
		$isBrushFish = 1;
		
		$id = 0;
		$fishUser = Hapyfish2_Island_Cache_Fish::getFishUser($uid, $id);
		$fishUser['dayNum'] += 1;
		if($fishUser['time'] != date('Ymd')) {
			$fishUser['time'] = date('Ymd');
			$fishUser['dayNum'] = 1;
			$fishUser['dayCoin'] = 0;
			$fishUser['reduces'] = 0;
		}
		
		$currentIslandId = $islandId;
		//捕获鱼的信息
		$catchFishInfoVo = self::getCatchFishInfo($uid, $cannonId, $currentIslandId, $domainId, 0, $isBrushFish);
		$result['status'] = 1;
		$result['coinChange'] = 0;
		$result['goldChange'] = 0;
		$catchFishAwardVo = array();
	
		foreach ($catchFishInfoVo[0] as $catchFishAward) {
			$catchFishAwardVo[] = $catchFishAward;
			
			if ($catchFishAward['awardType'] == 2) {
				$result['coinChange'] += $catchFishAward['num'];
			}
			
			if ($catchFishAward['awardType'] == 3) {
				$result['goldChange'] += $catchFishAward['num'];
			}
		}	
		
		$isNewIsland = 0;
				
		foreach ($catchFishInfoVo[1] as $fishData) {
			$tmp1[] = $fishData['id'];
		}
		
		$islandInfo = Hapyfish2_Island_Cache_Fish::getIslandInfo($currentIslandId);
		$needFish = $islandInfo['fishids'];
		$needFish = str_replace("，", ",", $needFish);
		$tmp2 = @explode(',', $needFish);
		if (is_array($tmp1) && is_array($tmp2)) {
			$tmp = array_intersect($tmp2, $tmp1);
			if( $tmp == $tmp2 && !in_array($currentIslandId + 1, $fishUser['lock'])  && $currentIslandId < 25 ) {
				$isNewIsland = $currentIslandId + 1;
				array_push($fishUser['lock'], $isNewIsland);
				self::updateUserLocks($uid, $fishUser['lock']);
				
				$logger->report('608', array($uid, $currentIslandId + 1));
			}
		}
		
		Hapyfish2_Island_Cache_Fish::setFishUser($uid, $fishUser);
		
		//report log			
		$logger->report('612', array($uid)); 
		$logger->report('613', array($uid, $islandId));
		
		foreach ($catchFishAwardVo as $catchFishAwardKey => $catchFishAwardData) {
			$rankAwardType[$catchFishAwardKey] = $catchFishAwardData['awardType'];
			$rankId[$catchFishAwardKey] = $catchFishAwardData['id'];
			$rankIsNew[$catchFishAwardKey] = $catchFishAwardData['isnew'];
		}
		
		array_multisort($rankAwardType, SORT_ASC, $catchFishAwardVo);
		
		if ($type == 1) {
			//获取用户等级
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			
			//扣除宝石
			$goldInfo = array('uid' => $uid,
							'cost' => $needGold,
							'summary' => self::TXT019,
							'user_level' => $userLevel,
							'create_time' => time(),
							'cid' => 0,
							'num' => 0);
	
	        $ok2 = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
	
			if ($ok2) {
				try {
					Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldInfo['cost']);
	
					//task id 3068,task type 19
					$checkTask = Hapyfish2_Island_Bll_Task::checkTask($uid, 3068);
					if ( $checkTask['status'] == 1 ) {
						$result['finishTaskId'] = $checkTask['finishTaskId'];
					}
		        } catch (Exception $e) {}
			}
			
			$result['goldChange'] += -$needGold;
		} else {		
			$decCard = Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, $needCard);
			
			if (!$decCard) {
				return array('result' => array('status' => -1, 'content' => 'serverWord_110'));
			}
			
			$result['itemBoxChange'] = true;
		}
		
		return array('result' => $result, 'catchFishAwardVo' => $catchFishAwardVo);
	}
	
	/**
	 * 
	 * use brush fish card
	 * @param int $uid
	 * @param int $cid
	 * 
	 * @return array
	 */
	public static function brushFishCard($uid)
	{
		$resultVo = array('status' => -1);
	    
		$cid = 183841;
		
		$cardInfo = Hapyfish2_Island_Cache_BasicInfo::getCardInfo($cid);
		if (!$cardInfo) {
			return array('resultVo' => $resultVo);
		}
		
		$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < 1) {
			$resultVo['content'] = 'serverWord_105';
			return array('resultVo' => $resultVo);
		}

		//check user brush fish card time
		$brushFishCardTime = Hapyfish2_Island_Cache_Fish::getBrushFishCardTime($uid);

		$now = time();
		if ($brushFishCardTime - $now > 0) {
			$resultVo['content'] = self::TXT018;
			return array('resultVo' => $resultVo);
		}

		$result = Hapyfish2_Island_HFC_Card::useUserCard($uid, $cid, 1, $userCard);
		if (!$result) {
			$resultVo['content'] = 'serverWord_110';
			return array('resultVo' => $resultVo);
		}
		
		try {
			$brushFishCardTime = $now + 24 * 3600;

			Hapyfish2_Island_Cache_Fish::addBrushFishCardTime($uid, $brushFishCardTime);
			
			$resultVo['status'] = 1;
            $resultVo['itemBoxChange'] = true;
		}
		catch (Exception $e) {
			$resultVo['content'] = 'serverWord_110';
            return array('resultVo' => $resultVo);
		}
		
		try {
			Hapyfish2_Island_HFC_AchievementDaily::updateUserAchievementDailyByField($uid, 'num_2', 1);
			
			Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_2', 1);
		} catch (Exception $e) {}

		//report log
		$logger = Hapyfish2_Util_Log::getInstance();			
		$logger->report('611', array($uid)); 
		
		return array('result' => $resultVo);
	}
	
}