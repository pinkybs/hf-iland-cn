<?php

class Hapyfish2_Island_Bll_Act
{
	public static function get($uid = 0)
	{
		$now = time();
		$actState = array();

		if ($uid > 0) {
			//判断新手引导是否完成
			$userHelp = Hapyfish2_Island_Cache_UserHelp::getHelpInfo($uid);
	        if ( $userHelp['completeCount'] == 8 ) {
                //团购活动
                $icon = Hapyfish2_Island_Event_Bll_TeamBuy::checkIcon($uid);

                $teamBuy = array('actName' => 'teamBuy',
                                'module' => 'swf/teamBuy.swf?v=2011061301',
                                'btn' => 'teamBuyActBtn',
                				'index' => 1,
                                'state' => $icon);
                $actState['teamBuy'] = $teamBuy;
	        }

			//check user level info
			$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
			if ( $userLevelInfo['level'] >= 7 ) {
				// 大转盘
				/**$casino = array ('actName' => 'zhuanpan',
								'btn' => 'zhuanpanActBtn',
								'module' => 'swf/turntable.swf?v=2011053001',
								'mClassName' => 'TurntableMain',
								'module2' => '',
								'mClassName2' => '',
								'index' => 7,
								'state' => 0 );
                $actState['zhuanpan'] = $casino;*/

				//天气feed
				$flashStrom = array('actName' => 'feedflashstorm',
								'module2' => 'swf/feedflashstorm.swf?v=2011062201',
								'state' => 0);
				$actState['feedflashstorm'] = $flashStrom;

				//积分兑换
				/**$jifen = array ('actName' => 'jifen',
								'index' => 6,
								'btn' => 'jifenActBtn',
								'link' => HOST . '/casinochange/index',
								'state' => '0' );
				$actState ['jifen'] = $jifen;*/

		        //特殊游客功能
		        $spVisitor = array(
		                        'actName' => 'spVisitor',
		                        'module2' => 'swf/SVisitor.swf?v=2012010601',
		                        'state' => 0);
		        $actState['spVisitor'] = $spVisitor;
		        
		        //特殊游客功能，收集
		        /*$SVCollection = array(
		                        'actName' => 'SVCollection',
		                        'module2' => 'swf/SVCollection.swf?v=2011091302',
		                        'index' => 9,
		                        'state' => 0);
		        $actState['SVCollection'] = $SVCollection;*/

				//防沉迷
				/*$avoidWallow = array('actName' => 'avoidWallow',
		        			   		'module2' => 'swf/avoidWallow.swf',
		        			   		'state' => 0);
		    	$actState['avoidWallow'] = $avoidWallow;*/

//		    	//停机补偿,结束时间5-20
//				$LUpendtime	 = mktime( 23,59,0,5,31,2011 );
//				if( $now < $LUpendtime ) {
//					$luptf = Hapyfish2_Island_Event_Bll_UpgradeGift::getTF($uid);
//					$luptf = $luptf ? 1 : 0;
//					$LUpAward = array('actName'=>'LUpAward',
//									'module'=>'swf/versionLevelUpAward.swf',
//									'btn'=>'LUpAwardBoxBtn',
//									'state'=>$luptf);
//					$actState['LUpAward'] = $LUpAward;
//				}

				//好友搜索
				$friendserach = array('actName' => 'friendserach',
		        			   		'module2' => 'swf/friendSearch.swf?v=2011072801',
		        			   		'state' => 0);
		    	$actState['friendserach'] = $friendserach;

//				//梦想花园
//		    	$ret = Hapyfish2_Island_Event_Bll_DreamGardenUserAward::check($uid);
//				$dreamgarden = array('actName' => 'dreamGardenUserAward',
//									'btn' => 'dreamGardenUserAwardActBtn',
//									'index' => 5,
//									'module' => 'swf/dreamGardenUserAward.swf?v=2011060701',
//									'state' => $ret ? 1 : 0,);
//				$actState['dreamGardenUserAward'] = $dreamgarden;

				//star info ,累计登录送星座
				if($now <= strtotime('2012-02-16 00:00:01')) {
					$starList = array(
						'actName' => 'dailyGetConstellation',
						'btn' => 'dailyGetConstellationActBtn',
						'module' => 'swf/dailyGetConstellation.swf?v=2011122204',
						'index' => 5,
					);
					$actState['dailyGetConstellation'] = $starList;
				}

				//收集任务
				$timekey = 'time';
			    $time =  Hapyfish2_Island_Event_Bll_Hash::getval ($timekey);
				$time = unserialize ($time);
				$switch = Hapyfish2_Island_Event_Bll_Hash::getswitch($uid);

				$state = 1;
				if($switch) {
					if( $now < $time['end'] && $now >$time['start']) {
						$collectkey = 'collectgift_haveget_' . $uid;
						$collectval = Hapyfish2_Island_Event_Bll_Hash::getval($collectkey);

						if(empty($collectval) ) {
							$state = 0;
						} else {
							$state = 1;
						}
					}
				}
				$collectionTask = array ('actName' => "collectionTask",
								    	'btn' => "collectionTaskActBtn",
								    	'module' => "swf/collectionTask.swf?v=2011110302",
										'index' => 2,
								    	'state' => $state);
			    $actState['collectionTask'] = $collectionTask;
			    
				//大圣诞树--淘集市
				/*$xmasTree = array('actName' => 'christTree',
			        			  'module' => 'swf/christTree.swf?v=2011061001',
			        			  'state' => 0);
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$mkeyUid = 'event_xmas_fair_daily_' . $uid;
				$gainDate = $cache->get($mkeyUid);

				$nowDate = date('Ymd');
				//has gained today's gift
				if ($gainDate && $gainDate == $nowDate) {
					$xmasTree['state'] = 1;
				}
				$actState['christTree'] = $xmasTree;*/
			}
//
//		    //元旦活动
//		    $newDaysTime = mktime(23, 59, 59, 01, 03, 2012);
//		    if ($now <= $newDaysTime) {
//				$newDays = array ('actName' => "HappyNewYear",
//								'module' => "swf/HappyNewYear.swf?v=2011123002",
//								'btn' => 'com.hapyfish.hny.HnyActBtn',
//								'index' => 0,
//								'state' => 0);
//		    	$actState['newDays'] = $newDays;
//		    }
//			
//		    //春节活动
//		    $SFTime = strtotime('2012-02-01 23:59:59');
//		    if ($now <= $SFTime) {
//				$springFestival = array ('actName' => "newYear",
//									'module' => "swf/newYearJiaozi.swf?v=2012011701",
//									'btn' => 'newYearJiaoziBtn',
//									'state' => 0);
//		    	$actState['newYear'] = $springFestival;
//		    }
//		    
//		    //元宵节活动
//		    $LFEndTime = strtotime('2012-02-07 23:59:59');
//		    if ($now <= $LFEndTime) {
//				$lanternFestival = array ('actName' => "YuanXiaoMeiShi",
//									'module' => "swf/YuanXiaoMeiShi.swf?v=2012020203",
//									'btn' => 'YuanXiaoMeiShiActBtn',
//									'index' => 2,
//									'state' => 0);
//		    	$actState['YuanXiaoMeiShi'] = $lanternFestival;
//		    }
//
//		    //情人节活动
//		    $valEndTime = strtotime('2012-02-19 23:59:59');
//		    if ($now <= $valEndTime) {
//		    	Hapyfish2_Island_Event_Cache_ValentineDay::firstQuest($uid);
//				$valentineDay = array ('actName' => "ValentineExchange",
//									'module' => "swf/ValentineExchange.swf?v=2012021001",
//									'btn' => 'ValentineExchangeBtn',
//									'index' => 2,
//									'state' => 0);
//		    	$actState['ValentineExchange'] = $valentineDay;
//
//				$valentineDayPlant = array ('actName' => "ValentinePlant",
//									'module' => "swf/ValentinePlant.swf?v=2012021303",
//									'index' => 2,
//									'state' => 0);
//		    	$actState['ValentinePlant'] = $valentineDayPlant;
//		    }
//		    
//		    //兑换建筑
//		    $receiveTime = strtotime('2012-01-17 23:59:59');
//		    if ($now <= $receiveTime) {
//		    	$exchangeAble = Hapyfish2_Island_Event_Cache_ReceivePlant::getExchangeAble($uid);
//		    	$exchangeAbleNum = $exchangeAble[0] + $exchangeAble[1] + $exchangeAble[2];
//		    	
//		    	if ($exchangeAbleNum < 3) {
//					$receivePlant = array ('actName' => "MingXingExchange",
//									'module' => "swf/MingXingExchange.swf?v=2012011003",
//									'btn' => 'MingXingExchangeActBtn',
//									'state' => 0);
//			    	$actState['MingXingExchange'] = $receivePlant;
//		    	}
//		    }
			
		    //图鉴
			$atlasBook = array ('actName' => "medalBook",
							'module' => "swf/MedalBook.swf?v=2012052501",
							'btn' => 'medalBookActBtn',
							'index' => 1,
							'state' => 0);
	    	$actState['medalBook'] = $atlasBook;
		    
			//特卖海星
			$starfishAndExternalMall = array(
						'actName' => 'starfishAndExternalMall',
						'btn' => '',
						'index' => 2,
						'module2' => 'swf/starfishAndExternalMall.swf?v=2011111104',
						'state' => 0);
			$actState['starfishAndExternalMall'] = $starfishAndExternalMall;

			//news，海岛新闻
			$newsIcon = array(
						'actName' => 'newsIcon',
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
			
			//活动DM
			$newsIconTw = array(
						'actName' => 'platformDM',
        		   		'module2' => 'swf/platformDM.swf?v=2012042001',
        		   		'state' => 0);
			$actState['newsIconTW'] = $newsIconTw;

//			//排行榜
//			$rankList = array(
//						'actName' => 'rankList',
//						'btn' => '',
//						'index' => 2,
//						'module2' => 'swf/rankingList.swf?v=2011062301',
//						'state' => 0,
//					);
//			$actState['rankList'] = $rankList;
//			//七夕
//			if($now <= 1313596799){
//				$valentine = array(
//					'actName' => 'tanabata',
//					'btn' => 'tanabataActBtn',
//					'index' => 6,
//					'module' => 'swf/tanabata.swf?v=2011080202',
//					'state' => 0
//					);
//				$actState['valentineRose'] = $valentine;
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
//					'module' => 'swf/tanabata2.swf?v=2011080202',
//					'state' => $statue
//					);
//				$actState['tanabata2'] = $valentine2;
//			}

	        //淘金币兑换活动
	        //$AmoyTime = strtotime('2011-10-16 17:00:00');
	        //if ($now < $AmoyTime) {
        		$AmoyAward = array('actName' => 'exchangeTCoin',
									'module2' => 'swf/exchangeTCoin.swf?v=2011120801',
									'state' => 0);
				$actState['exchangeTCoin'] = $AmoyAward;
	        //}

    		//一元店
			$onegold = array('actName' => 'oneyuanshop',
							'module2' => 'swf/Oneyuanshop.swf?v=2012071702',
							'btn'	=>	'oneyuanBtn',
							'state' => 0);
			$actState['Oneyuanshop'] = $onegold;

			//圣诞节活动
//			$chrismasEndTime = strtotime('2011-12-27 23:59:59');
//			$christmasState = 1;
//			if ($now <= $chrismasEndTime) {
//				$christmasState = 0;
//			}
//			
//			$christmas = array('actName' => 'MerryChristmas',
//									'module' => 'swf/MerryChristmasDM.swf?v=2011120701',
//									'module2' => 'swf/MerryChristmas.swf?v=2011122001',
//									'btn' => 'MerryChristmasbtn',
//									'index' => 0,
//									'state' => $christmasState);
//			$actState['christmas'] = $christmas;
//
//			
//			//感恩节活动
//			$endTime = strtotime('2011-11-27 23:59:59');
//			if ($now <= $endTime) {
//				$Thanksgiving = array('actName' => 'Thanksgiving',
//								'module' => 'swf/Thanksgiving.swf?v=2011112203',
//								'module2' => 'swf/Thanksgiving.swf?v=2011112203',
//								'btn' => 'ThanksgivingActBtn',
//								'index' => 0,
//								'state' => 0);
//				$actState['Thanksgiving'] = $Thanksgiving;
//			}
//			
//			//万圣节,10月24-11月07日
//			//$startTime = strtotime('2011-10-24 14:00:00');
//			$endTime = strtotime('2011-11-08 23:59:59');
//			//if (($now >= $startTime) && ($now <= $endTime)) {
//			if ($now <= $endTime) {
//				$halloween = array('actName' => 'halloween',
//								'btn' => 'com.hapyfish.hw.ui.HalloweenIconButton',
//								'module' => 'swf/halloween.swf?v=2011102406',
//	        		   			'module2' => 'swf/halloween.swf?v=2011102406',
//	        		   			'index' => 1,
//	        		   			'state' => 0);
//				$actState['halloween'] = $halloween;
//			}
//
//			//单身节活动
//			$endTime = strtotime('2011-11-16 23:59:59');
//			if ($now <= $endTime) {
//				$blackDay = array('actName' => 'SingleDay',
//								'module' => 'swf/SingleDay.swf?v=2011110901',
//								'module2' => 'swf/SingleDay.swf?v=2011110901',
//								'state' => 0);
//				$actState['blackDay'] = $blackDay;
//			}
			
    		//捕鱼
			$catchFish = array('actName' => 'CatchFish',
								'module2' => 'swf/CatchFish.swf?v=2012052201',
								'module' => 'swf/CatchFishDM.swf?v=2012041801',
								'btn' => 'Moudle1CatchFishBtn',
								'index' => 12,
								'state' => 0);
			$actState['CatchFish'] = $catchFish;
			
			$catchFish1 = array('actName' => 'CatchFishGameFish',
								'module' => 'swf/CatchFishGameFish.swf?v=2012042301',
								'btn' => 'CatchFishGameFishBtn',
								'index' => 13,
								'state' => 0);
			$actState['CatchFish1'] = $catchFish1;

			
			$cardChange = array('actName' => 'CatchFishCardEX',
								'module' => 'swf/CatchFishCardEx.swf?v=20120223',
								'btn' => 'CatchFishCardEXBtn',
								'state' => 0);
			$actState['cardChange'] = $cardChange;
			
			$endTime = strtotime('2012-03-01 23:59:59');
			if($now <= $endTime) {
				$fishAward = array('actName' => 'CatchFishAward',
									'module' => 'swf/CatchFishAward.swf?v=20120227',
									'btn' => 'CatchFishAwardBtn',
									'state' => 0);
				$actState['fishAward'] = $fishAward;	
			}		
						
			// 时间性礼物
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$key = 'event_timegift_' . $uid;
			$val = $cache->get($key);

			if( $val && $val['state'] < 6 ) {
				$sixTimesGift = array(	'actName' => 'sixTimesGift',
										'module2' => 'swf/SixTimesGiftMain.swf?v=2011070701',
										'state' => (int)$val['state'] );
				$actState['sixTimesGift'] = $sixTimesGift;
			}

			$levelBigGift = array(	'actName' => 'levelBigGift',
									'btn' => 'LevelBigGiftActBtn',
									'module' => 'swf/levelBigGift.swf?v=2011070101',
									'module2' => 'swf/levelBigGift.swf?v=2011070101');
			$actState['levelBigGift'] = $levelBigGift;
			$vipLook = array(	'actName' => 'CatchFishVipLook',
									'btn' => 'CatchFishVipLookbtn',
									'module' => 'swf/CatchFishVipLook.swf?v=2012042701',
									);
			$actState['CatchFishVipLook'] = $vipLook;

//			if($now <= 1314806399){
//				$checktoday = Hapyfish2_Island_Event_Bll_Qixi::checkToday($uid);
//				if($checktoday){
//					$statue = 0;
//				}else{
//					$statue = 1;
//				}
//				$qixiGetgift = array(	'actName' => 'tanabataGift',
//									'module' => 'swf/tanabataGift.swf?v=2011080202',
//									'index' => 8,
//									'state' => $statue
//								);
//				$actState['tanabataGift'] = $qixiGetgift;
//			}

		}

		$zhuanbao = array(
			'actName' => 'IslandNew',
			'btn' => 'IslandNewBtn',
			'module' => 'swf/IslandNew.swf?v=2012051701',
			'state' => 0

			);
		$actState['zhuanbao'] = $zhuanbao;

		return $actState;
	}

}