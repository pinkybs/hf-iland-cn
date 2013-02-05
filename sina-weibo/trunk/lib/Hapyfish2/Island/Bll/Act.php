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

				$inviteFlowStep = Hapyfish2_Island_Event_Bll_InviteFlow::getInviteStep($uid);
				if ($inviteFlowStep >= 0 && $inviteFlowStep < 4) {
					$yaoQingHaoYou = array(
						'actName' => 'yaoQingHaoYou',
						'btn' => 'yaoQingHaoYouActBtn',
						'index' => 2,
						'module' => 'swf/v2011122301/yaoQingHaoYou.swf?v=201121802',
						'state' => 0
					);
					$actState['yaoQingHaoYou'] = $yaoQingHaoYou;
				}

				//天气feed
				$flashStrom = array('actName' => 'feedflashstorm',
								'module2' => 'swf/feedflashstorm.swf?v=2011072001',
								'state' => 0);
				$actState['feedflashstorm'] = $flashStrom;

				//梦想花园
//		    	$ret = Hapyfish2_Island_Event_Bll_DreamGardenUserAward::check($uid);
//				$dreamgarden = array('actName' => 'dreamGardenUserAward',
//									'btn' => 'dreamGardenUserAwardActBtn',
//									'index' => 2,
//									'module' => 'swf/dreamGardenUserAward.swf?v=2011071801',
//									'state' => $ret ? 1 : 0,);
//				$actState['dreamGardenUserAward'] = $dreamgarden;

				//star info ,累计登录送星座
				$starList = array(
					'actName' => 'dailyGetConstellation',
					'btn' => 'dailyGetConstellationActBtn',
					'module' => 'swf/dailyGetConstellation.swf',
					'index' => 4,
				);
				$actState['dailyGetConstellation'] = $starList;

				//收集任务
				/*
				$timekey = 'time';
			    $time =  Hapyfish2_Island_Event_Bll_Hash::getval ($timekey);
				$time = unserialize ($time);
				$switch = Hapyfish2_Island_Event_Bll_Hash::getswitch($uid);

				if($switch) {
					if( $now < $time['end'] && $now >$time['start']) {
						$collectkey = 'collectgift_haveget_' . $uid;
						$collectval = Hapyfish2_Island_Event_Bll_Hash::getval($collectkey);

						if(empty($collectval) ) {
							$state = 0;
						} else {
							$state = 1;
						}

						$collectionTask = array ('actName' => "collectionTask",
										    	'btn' => "collectionTaskActBtn",
										    	'module' => "swf/v2011092901/collectionTask.swf?v=2011061001",
										    	'state' => $state);
					    $actState['collectionTask'] = $collectionTask;
					}
				}
				*/
				//感恩节收集任务
				$timekey = 'time';
			    $time =  Hapyfish2_Island_Event_Bll_Tkhash::getval ($timekey);
				$time = unserialize ($time);
				$switch = Hapyfish2_Island_Event_Bll_Tkhash::getswitch($uid);

				if($switch) {
					if( $now < $time['end'] && $now >$time['start']) {
						$collectkey = 'collectgift_haveget_' . $uid;
						$collectval = Hapyfish2_Island_Event_Bll_Tkhash::getval($collectkey);

						if(empty($collectval) ) {
							$state = 0;
						} else {
							$state = 1;
						}

						$collectionTask = array ('actName' => "Thanksgiving",
										    	'btn' => "com.hapyfish.hw.ui.ThanksgivingActBtn",
										    	'module' => "swf/v2011112301/Thanksgiving.swf?v=2011112301",
										    	'state' => $state);
					    $actState['collectionTask'] = $collectionTask;
					}
				}				
			    //团购活动
				$icon = Hapyfish2_Island_Event_Bll_TeamBuy::checkIcon($uid);
				$teamBuy = array('actName' => 'teamBuy',
								'module' => 'swf/teamBuy.swf?v=2011021405',
								'btn' => 'teamBuyActBtn',
								'index' => 3,
								'state' => $icon);
				$actState['teamBuy'] = $teamBuy;
			}
		}

		//海星商城
		$starfishAndExternalMall = array(
				'actName' => 'starfishAndExternalMall',
				'btn' => '',
				'index' => 2,
				'module2' => 'swf/starfishAndExternalMall.swf?v=2011042001',
				'state' => 0,
			);
		$actState['starfishAndExternalMall'] = $starfishAndExternalMall;

		//DM
		$newsIcon = array('actName' => 'newsIcon',
        		   		'module2' => 'swf/newsIcon.swf',
        		   		'state' => 0);
		$actState['newsIcon'] = $newsIcon;

		//岛屿扩建图标
		$islandGuide = array(
						'actName' => 'upgradeIslandGuide',
						'btn' => '',
						'module2' => 'swf/upgradeIslandGuide.swf',
						'state' => 0);
		$actState['upgradeIslandGuide'] = $islandGuide;

		//排行榜
		$rankList = array(
				'actName' => 'rankList',
				'btn' => '',
				'index' => 2,
				'module2' => 'swf/rankingList.swf?v=2011072602',
				'state' => 0,
			);
	    $actState['rankList'] = $rankList;

		// 时间性礼物
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'event_timegift_' . $uid;
		$val = $cache->get($key);
		if( $val && $val['state'] < 6 ) {
			$sixTimesGift = array('actName' => 'sixTimesGift',
								'module2' => 'swf/SixTimesGiftMain.swf?v=2011042001',
								'state' => (int)$val['state']);

			$actState['sixTimesGift'] = $sixTimesGift;
		}

		//等级阶段性礼物
		$levelBigGift = array(	'actName' => 'levelBigGift',
								'btn' => 'LevelBigGiftActBtn',
								'module' => 'swf/levelBigGift.swf?v=2011070102',
								'module2' => 'swf/levelBigGift.swf?v=2011071802');
		$actState['levelBigGift'] = $levelBigGift;

		$friendserach = array('actName' => 'friendserach',
	        			   		'module2' => 'swf/v2011072801/friendSearch.swf?v=2011072801',
	        			   		'state' => 0);
	    $actState['friendserach'] = $friendserach;

		//圣诞节活动
		$chrismasEndTime = strtotime('2012-01-03 23:59:59');
		if ($now <= $chrismasEndTime) {
			$christmas = array('actName' => 'MerryChristmas',
								'module' => 'swf/v2011121901/MerryChristmasDM.swf?v=2011121601',
								'module2' => 'swf/v2011121901/MerryChristmas.swf?v=2011121901',
								'btn' => 'MerryChristmasbtn',
								'index' => 0,
								'state' => 0);
			$actState['christmas'] = $christmas;
		}
	    
//	    //粉丝礼包
//	    $sinafans = array('actName' => 'Sinafans',
//	    	'btn' => 'sinaFansBtn',
//        	'module' => 'swf/v2011081501/Sinafans.swf?v=2011081504',
//        	'state' => 0);
//	   $actState['sinafans'] = $sinafans;

//		//邀请好友--第一期结束时间2011-09-21 12:00:00
//	    if ($now < 1316577600) {
//		    $inviteGift = array('actName' => 'inviteFriendsExchangeAward',
//		    					'module2' => 'swf/v2011091303/inviteFriendsExchangeAward.swf',
//		    					'state'	 => 0);
//		    $actState['inviteFriendsExchangeAward'] = $inviteGift;
//	    }
//
//		//万圣节,10月24-11月07日
//		$startTime = strtotime('2011-10-24 14:00:00');
//		$endTime = strtotime('2011-11-07 14:00:00');
//		if (($now >= $startTime) && ($now <= $endTime)) {
//			$halloween = array('actName' => 'halloween',
//							'btn' => 'com.hapyfish.hw.ui.HalloweenIconButton',
//							'module' => 'swf/v2011102401/halloween.swf?v=2011102405',
//        		   			'module2' => 'swf/v2011102401/halloween.swf?v=2011102405',
//        		   			'index' => 1,
//        		   			'state' => 0);
//			$actState['halloween'] = $halloween;
//		}
    	//捕鱼
    	
		$catchFish = array('actName' => 'CatchFish',
							'module2' => 'swf/v2011111601/CatchFish.swf?v=2011111101',
							'module' => 'swf/v2011111601/CatchFishDM.swf?v=2011101905',
							'btn' => 'Moudle1CatchFishBtn',
							'index' => 12,
							'state' => 0);
		$actState['CatchFish'] = $catchFish;

//		//单身节活动
//		$endTime = strtotime('2011-11-15 23:59:59');
//		if ($now <= $endTime) {
//			$blackDay = array('actName' => 'SingleDay',
//							'module' => 'swf/v2011110702/SingleDay.swf?v=2011110701',
//							'module2' => 'swf/v2011110702/SingleDay.swf?v=2011110701',
//							'state' => 0);
//			$actState['blackDay'] = $blackDay;
//		}
		
//		//限时抢购
//		$actCheck = Hapyfish2_Island_Event_Bll_PanicBuy::getIconAct();
//		$panicBuy = array('actName' => 'rushBuy',
//							'module' => 'swf/v2011110101/rushBuy.swf?v=v2011110101',
//							'module2' => 'swf/v2011110101/rushBuy.swf?v=v2011110101',
//							'btn' => 'RushBuyActButton',
//							'state' => $actCheck);
//		$actState['rushBuy'] = $panicBuy;
		
		//特殊游客功能
		$spVisitor = array(
						'actName' => 'spVisitor',
						'module2' => 'swf/v2011083002/SVisitor.swf?v=2011082301',
						'state' => 0);
		$actState['spVisitor'] = $spVisitor;
		
		//特殊游客功能，收集
		$SVCollection = array(
						'actName' => 'SVCollection',
						'module2' => 'swf/v2011091302/SVCollection.swf?v=2011091302',
						'index' => 9,
						'state' => 0);
		$actState['SVCollection'] = $SVCollection;

		$avoidWallow = array(
						'actName' => 'avoidWallow',
						'module2' => 'swf/v2011082902/avoidWallow.swf?v=2011082301',
						'index' => 10,
						'state' => 0);
		$actState['avoidWallow'] = $avoidWallow;

//		$consumeEvent = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeEvent();
//		    $data = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeStep($uid,$consumeEvent['start'],$consumeEvent['end']);
//			if($consumeEvent){
//		        if($now > $consumeEvent['start'] && $now < $consumeEvent['end'] ){
//				    $consumerAndgiver=array(
//				       'actName' => 'consumerAndgiver',
//				       'btn' => 'consumerAndgiverActBtn',
//				       'index' => 7,
//				       'module' => 'swf/consumerAndgiver.swf?v=2011050602',
//				       'state' => 0
//				);
//				$actState['consumerAndgiver'] = $consumerAndgiver;
//			    }
//		    }

/*		if($now <= 1313768399){
				$valentine = array(
					'actName' => 'tanabata',
					'btn' => 'tanabataActBtn',
					'index' => 6,
					'module' => 'swf/tanabata.swf?v=2011080202',
					'state' => 0
					);
				$actState['valentineRose'] = $valentine;
				$status = Hapyfish2_Island_Event_Bll_Qixi::getswitch($uid);
				if($status){
					$statue = 1;
				}else{
					$statue = 0;
				}
				$valentine2 = array(
					'actName' => 'tanabata2',
					'btn' => 'tanabata2ActBtn',
					'index' => 7,
					'module' => 'swf/tanabata2.swf?v=2011080202',
					'state' => $statue
					);
				$actState['tanabata2'] = $valentine2;
			}
			if($now <= 1318237200){
				$guoqing = array(
					'actName' => 'guoqing',
					'btn' => 'ChronoCrossActBtn',
					'module2' => 'swf/v2011092606/ChronoCross.swf?v=2011080202',
					'module' => 'swf/v2011092606/ChronoCross.swf?v=2011080202',
					'state' => 0

					);
				$actState['guoqing'] = $guoqing;
			}

			if($now <= 1314806399){
				$checktoday = Hapyfish2_Island_Event_Bll_Qixi::checkToday($uid);
				if($checktoday){
					$statue = 0;
				}else{
					$statue = 1;
				}
				$qixiGetgift = array(	'actName' => 'tanabataGift',
									'module' => 'swf/tanabataGift.swf?v=2011080202',
									'index' => 8,
									'state' => $statue
								);
				$actState['tanabataGift'] = $qixiGetgift;
			}*/
			//评分
//			$state = Hapyfish2_Island_Event_Bll_Grade::getStatue($uid);
//			if($state !=2){
//				$score = array(	'actName' => 'sinaPingfeng',
//									'module2' => 'swf/sinaPingfeng.swf?v=2011021405',
//									'state' => $state
//								);
//				$actState['sinaPingfeng'] = $score;
//			}

		return $actState;
	}

}