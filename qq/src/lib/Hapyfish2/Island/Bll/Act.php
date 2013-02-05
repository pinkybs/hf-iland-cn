<?php

class Hapyfish2_Island_Bll_Act
{
	public static function get($uid = 0)
	{
		$now = time();
		$actState = array();

		if ($uid > 0) {
			//check user level info
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			if ( $userLevelInfo['level'] >= 5 ) {
//				$inviteFlowStep = Hapyfish2_Island_Event_Bll_InviteFlow::getInviteStep($uid);
//				if ($inviteFlowStep >= 0 && $inviteFlowStep < 4) {
//					$yaoQingHaoYou = array(
//						'actName' => 'yaoQingHaoYou',
//						'btn' => 'yaoQingHaoYouActBtn',
//						'index' => 2,
//						'module' => 'swf/v2.0.1/yaoQingHaoYou.swf?v=2011072601',
//						'state' => 0
//					);
//					$actState['yaoQingHaoYou'] = $yaoQingHaoYou;
//				}

				//结束时间 2011-02-24 00:00:00
				if ($now < 1298476800) {
					$cdKeyTuZi = array(
						'actName' => 'cdKeyTuZi',
						'btn' => 'cdKeyTuZiActBtn',
						'index' => 5,
						'module' => 'swf/v2.0.1/cdKeyTuZi.swf?v=2011012801',
						'state' => 0
					);
					$actState['cdKeyTuZi'] = $cdKeyTuZi;
				}

				//结束时间 1300118400;//2011-03-15
				if ($now < 1300118400) {
					$valentine = array(
						'actName' => 'valentineRose',
						'btn' => 'valentineActBtn',
						'index' => 6,
						'module' => 'swf/v2.0.1/valentineRose.swf?v=2011021202',
						'state' => 0
					);
					$actState['valentineRose'] = $valentine;
				}
				$consumeEvent = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeEvent();
			    $data = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeStep($uid,$consumeEvent['start'],$consumeEvent['end']);
				if($consumeEvent){
			        if($now > $consumeEvent['start'] && $now < $consumeEvent['end'] ){
					    $consumerAndgiver=array(
					       'actName' => 'consumerAndgiver',
					       'btn' => 'consumerAndgiverActBtn',
					       'index' => 7,
					       'module' => 'swf/v2.0.1/consumerAndgiver.swf?v=2011111002',
					       'state' => 0
					);
					$actState['consumerAndgiver'] = $consumerAndgiver;
				    }
			    }

				//star info ,累计登录送星座
				$starList = array(
					'actName' => 'dailyGetConstellation',
					'btn' => 'dailyGetConstellationActBtn',
					'module' => 'swf/v2.0.1/dailyGetConstellation.swf?v=20110421',
				);
				$actState['dailyGetConstellation'] = $starList;

				//升级建筑抽奖
	//			$dalLucky = Hapyfish2_Island_Event_Dal_LuckyDraw::getDefaultInstance();
	//			$luckyDrawCollent = $dalLucky->getLuckyDrawInfo();
	//
	//			$isLucky = array ('actName' => 'happyTurnOverSky',
	//								'btn' => 'happyTurnOverSkyActBtn',
	//								'module' => 'swf/v2.0.1/happyTurnOverSky.swf?=2011051801',
	//								'state' => 0 );
	//			$actState['happyTurnOverSky'] = $isLucky;
				
				$userHelp = Hapyfish2_Island_Cache_UserHelp::getHelpInfo($uid);

				if ( $userHelp['completeCount'] == 8 ) {
					//团购活动
					$icon = Hapyfish2_Island_Event_Bll_TeamBuy::checkIcon($uid);
	
					$teamBuy = array('actName' => 'teamBuy',
									'module' => 'swf/v2.0.2/teamBuy.swf?v=2012013101',
									'btn' => 'teamBuyActBtn',
									'index' => 1,
									'state' => $icon);
					$actState['teamBuy'] = $teamBuy;
				}
				/*
				//团购活动
				$key = 'TeamBuyInfo';
				$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
				$cacheTeam = $cache->get($key);
				if(!$cacheTeam) {
					$dalTeamBuy = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
					$cacheTeam = $dalTeamBuy->getTeamBuyInfo();
					if($cacheTeam) {
						$cache->set($key, $cacheTeam);
					}
				}

				if($cacheTeam == false) {
					$icon = 1;
				} else {
					if($cacheTeam['start_time'] + ($cacheTeam['ok_time'] + $cacheTeam['buy_time']) * 3600 - time() < 0) {
						$icon = 1;
					} else {
						$icon = 0;

						$newkey = 'BuyGoods_' . $uid;
						$newcache = Hapyfish2_Cache_Factory::getMC($uid);
						$state = $newcache->get($newkey);
						if(!$state) {
							$dalTeamBuynew = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
							$state = $dalTeamBuynew->getJoinTeamBuyInfo($uid);

							if($state) {
								$newcache->set($newkey, $state);
							}
						}
						if($state == 1) {
							$icon = $state;
						} else {
							$icon = 0;
						}
					}
				}

				if($icon == 0) {
					$openUIDs = Hapyfish2_Island_Event_Bll_TeamBuy::getOpenUID();

					if($openUIDs) {
						if(in_array($uid, $openUIDs)) {
							$icon = 0;
						} else {
							$icon = 1;
						}
					}
				}

				$teamBuy = array('actName' => 'teamBuy',
								'module' => 'swf/v2.0.1/teamBuy.swf?v=2011051301',
								'btn' => 'teamBuyActBtn',
								'state' => $icon);
				$actState['teamBuy'] = $teamBuy;
*/
				
				//天气feed
				$flashStrom = array('actName' => 'feedflashstorm',
									'module2' => 'swf/v2.0.1/feedflashstorm.swf?v=2011072701',
									'state' => 0);
				$actState['feedflashstorm'] = $flashStrom;

				//收集任务
				$timekey = 'time';
			    $time =  Hapyfish2_Island_Event_Bll_Hash::getval ($timekey);
				$time = unserialize ($time);
				$switch = Hapyfish2_Island_Event_Bll_Hash::getswitch($uid);

				if($switch) {
					if( time() < $time['end'] && time() >$time['start']) {
						$collectkey = 'collectgift_haveget_' . $uid;
						$collectval = Hapyfish2_Island_Event_Bll_Hash::getval($collectkey);

						if(empty($collectval) ) {
							$state = 0;
						} else {
							$state = 1;
						}

						$collectionTask = array ('actName' => "collectionTask",
										    	'btn' => "collectionTaskActBtn",
										    	'module' => "swf/v2.0.1/collectionTask.swf?v=2011111401",
										    	'state' => $state);
					    $actState['collectionTask'] = $collectionTask;
					}
				}
			}
		}

		//海星商城
		$starfishAndExternalMall = array(
			'actName' => 'starfishAndExternalMall',
			'btn' => '',
			'index' => 2,
			'module2' => 'swf/v2.0.1/starfishAndExternalMall.swf?v=2011110902',
			'state' => 0,
		);
		$actState['starfishAndExternalMall'] = $starfishAndExternalMall;

		//DM
		$actState['newsIcon'] = array(
				'actName' => 'newsIcon',
	        	'module2' => 'swf/v2.0.1/newsIcon.swf',
	        	'state' => 0);

		//岛屿扩建图标
		$islandGuide = array(
						'actName' => 'upgradeIslandGuide',
						'btn' => '',
						'module2' => 'swf/v2.0.1/upgradeIslandGuide.swf?v=2011072601',
						'state' => 0);
		$actState['upgradeIslandGuide'] = $islandGuide;

		// 时间性礼物
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		if( $val && $val['state'] < 6 ) {
			$sixTimesGift = array(
							    'actName' => 'sixTimesGift',
								'module2' => 'swf/v2.0.1/SixTimesGiftMain.swf?v=2011072601',
								'state' => (int)$val['state'] );

			$actState['sixTimesGift'] = $sixTimesGift;
		}

		//限时抢购
		$actCheck = Hapyfish2_Island_Event_Bll_PanicBuy::getIconAct();
		$panicBuy = array('actName' => 'rushBuy',
							'module' => 'swf/v2.0.1/rushBuy.swf?v=2011110901',
							'module2' => 'swf/v2.0.1/rushBuy.swf?v=2011110901',
							'btn' => 'RushBuyActButton',
							'state' => $actCheck);
		$actState['rushBuy'] = $panicBuy;

		//等级阶段性礼物
		$levelBigGift = array(	'actName' => 'levelBigGift',
								'btn' => 'LevelBigGiftActBtn',
								'module' => 'swf/v2.0.1/levelBigGift.swf?v=2011070102',
								'module2' => 'swf/v2.0.1/levelBigGift.swf?v=2011071802');
		$actState['levelBigGift'] = $levelBigGift;

        if($now < 1300636799){
            $haveget = Hapyfish2_Island_Event_Bll_CollectStuff::haveGetgift($uid);
            if($haveget!=1){
                 $whiteValentineDay = array(
				       'actName' => 'whiteValentineDay',
				       'btn' => 'whiteValentineDayActBtn',
				       'index' => 8,
				       'module' => 'swf/v2.0.1/whiteValentineDay.swf?v=20110804',
				       'state' => 0
				);
				$actState['whiteValentineDay'] = $whiteValentineDay;
            }
		}
		//好友搜索
		  if (defined('PLATFORM_SOURCE') && '1' != PLATFORM_SOURCE) {
			$actState['friendSearch'] = array(
				'actName' => 'friendSearch',
	        	'module2' => 'swf/v2.0.1/friendSearch.swf?v=2011072801',
	        	'state' => 0);
		}

		//万圣节,10月25-11月08日
		$startTime = strtotime('2011-10-25 10:30:00');
		$endTime = strtotime('2011-11-08 14:00:00');
		//if (($now >= $startTime) && ($now <= $endTime)) {
		if ($now <= $endTime) {
			$halloween = array('actName' => 'halloween',
							'btn' => 'com.hapyfish.hw.ui.HalloweenIconButton',
							'module' => 'swf/v2.0.1/halloween.swf?v=2011110901',
        		   			'module2' => 'swf/v2.0.1/halloween.swf?v=2011110901',
        		   			'index' => 1,
        		   			'state' => 0);
			$actState['halloween'] = $halloween;
		}

		//单身节活动
		$endTime = strtotime('2011-11-18 23:59:59');
		if ($now <= $endTime) {
			$blackDay = array('actName' => 'SingleDay',
							'module' => 'swf/v2.0.1/SingleDay.swf?v=2011111101',
							'module2' => 'swf/v2.0.1/SingleDay.swf?v=2011111101',
							'state' => 0);
			$actState['blackDay'] = $blackDay;
		}
		
		//排行榜
//		$rankList = array(
//					'actName' => 'rankList',
//					'btn' => '',
//					'index' => 2,
//					'module2' => 'swf/v2.0.1/rankingList.swf?v=2011050902',
//					'state' => 0,
//				);
//		$actState['rankList'] = $rankList;
//		if($now <= 1313683199){
//				$valentine = array(
//					'actName' => 'tanabata',
//					'btn' => 'tanabataActBtn',
//					'index' => 6,
//					'module' => 'swf/v2.0.1/tanabata.swf?v=2011080201',
//					'state' => 0
//					);
//				$actState['valentineRose'] = $valentine;
//				$ret = Hapyfish2_Island_Event_Bll_DreamGardenUserAward::check($uid);
//				$dreamgarden = array('actName' => 'dreamGardenUserAward',
//							'btn' => 'dreamGardenUserAwardActBtn',
//							'index' => 2,
//							'module' => 'swf/v2.0.1/dreamGardenUserAward.swf?v=2011060701',
//							'state' => $ret ? 1 : 0,);
//				$actState['dreamGardenUserAward'] = $dreamgarden;
//				$status = Hapyfish2_Island_Event_Bll_Qixi::getswitch($uid);
//				if($status){
//					$statue = 1;
//				}else{
//					$statue = 0;
//				}
//				$valentine2 = array(
//					'actName' => 'tanabata2',
//					'btn' => 'tanabata2ActBtn',
//					'index' => 7,
//					'module' => 'swf/v2.0.1/tanabata2.swf?v=2011081202',
//					'state' => $statue
//					);
//				$actState['tanabata2'] = $valentine2;
//			}
//			if($now <= 1318435199){
//				$guoqing = array(
//					'actName' => 'guoqing',
//					'btn' => 'ChronoCrossActBtn',
//					'module2' => 'swf/v2.0.1/ChronoCross.swf?v=2011092801',
//					'module' => 'swf/v2.0.1/ChronoCross.swf?v=2011092801',
//					'state' => 0
//
//					);
//				$actState['guoqing'] = $guoqing;
//			}
//
//			if(time() <= 1314806399){
//				$checktoday = Hapyfish2_Island_Event_Bll_Qixi::checkToday($uid);
//				if($checktoday){
//					$statue = 0;
//				}else{
//					$statue = 1;
//				}
//				$qixiGetgift = array(	'actName' => 'tanabataGift',
//									'module' => 'swf/v2.0.1/tanabataGift.swf?v=2011081202',
//									'index' => 8,
//									'state' => $statue
//								);
//				$actState['tanabataGift'] = $qixiGetgift;
//			}

		return $actState;
	}

}
