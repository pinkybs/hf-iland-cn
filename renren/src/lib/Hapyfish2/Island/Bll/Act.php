<?php

class Hapyfish2_Island_Bll_Act
{
	public static function get($uid = 0)
	{
		$now = time();
		$actState = array();

		if ($uid > 0) {
			//时间性礼物
			$cache = Hapyfish2_Cache_Factory::getMC($uid);
			$key = 'event_timegift_' . $uid;
			$val = $cache->get($key);

			if( $val && $val['state'] < 6 ) {
				$sixTimesGift = array(	'actName' => 'sixTimesGift',
									'module2' => 'swf/SixTimesGiftMain.swf?v=2011042001',
									'state' => (int)$val['state'] );

				$actState['sixTimesGift'] = $sixTimesGift;
			}

			//特卖海星
			$starfishAndExternalMall = array(
					'actName' => 'starfishAndExternalMall',
					'btn' => '',
					'index' => 2,
					'module2' => 'swf/starfishAndExternalMall.swf?v=2011052502',
					'state' => 0,
				);
			$actState['starfishAndExternalMall'] = $starfishAndExternalMall;

			//newsIcon
			$newsIcon = array(	'actName' => 'newsIcon',
        			   		'module2' => 'swf/newsIcon.swf',
        			   		'state' => 0);
			$actState['newsIcon'] = $newsIcon;

			//天气feed
			$flashStrom = array('actName' => 'feedflashstorm',
							'module2' => 'swf/feedflashstorm.swf?v=2011041901',
							'state' => 0);
			$actState['feedflashstorm'] = $flashStrom;

			//积分兑换
	        $ganen = array('actName' => 'ganen',
	        				'btn' => 'ganenActBtn',
	        				'link' => HOST . '/casinochange/index',
	        				'index' => 2,
	        				'state' => 0);
	        $actState['ganen'] = $ganen;

			//防沉迷
			$avoidWallow = array('actName' => 'avoidWallow',
	        			   		'module2' => 'swf/20120315/avoidWallow20120315.swf?v=2012030501',
	        			   		'state' => 0);
	    	$actState['avoidWallow'] = $avoidWallow;

			//梦想花园
	    	$ret = Hapyfish2_Island_Event_Bll_DreamGardenUserAward::check($uid);
			$dreamgarden = array(
				'actName' => 'dreamGardenUserAward',
				'btn' => 'dreamGardenUserAwardActBtn',
				'index' => 2,
				'module' => 'swf/dreamGardenUserAward.swf?v=2011061601',
				'state' => $ret ? 1 : 0,
			);
			$actState['dreamGardenUserAward'] = $dreamgarden;

	    	//停机补偿,结束时间5-20
			$LUpendtime	  = 1305907200;
			if( time() < $LUpendtime ) {
				$luptf = Hapyfish2_Island_Event_Bll_UpgradeGift::getTF($uid);
				$luptf = $luptf ? 1 : 0;
				$LUpAward = array('actName'=>'LUpAward',
								'module'=>'swf/versionLevelUpAward.swf',
								'btn'=>'LUpAwardBoxBtn',
								'state'=>$luptf);
				$actState['LUpAward'] = $LUpAward;
			}

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
									    	'module' => "swf/collectionTask.swf?v=2011040702",
									    	'state' => $state);
				    $actState['collectionTask'] = $collectionTask;
				}
			}
			
			//邀请好友送宝石
			$inviteFlowStep = Hapyfish2_Island_Event_Bll_InviteFlow::getInviteStep($uid);
			if ($inviteFlowStep >= 0 && $inviteFlowStep < 4) {
				$yaoQingHaoYou = array(
					'actName' => 'yaoQingHaoYou',
					'btn' => 'yaoQingHaoYouActBtn',
					'index' => 2,
					'module' => 'swf/yaoQingHaoYou.swf?v=2011062104',
					'state' => 0
				);
				$actState['yaoQingHaoYou'] = $yaoQingHaoYou;
			}
			
			//团购活动
			$icon = Hapyfish2_Island_Event_Bll_TeamBuy::checkIcon($uid);
			$teamBuy = array('actName' => 'teamBuy',
							'module' => 'swf/teamBuy.swf?v=2011021404',
							'btn' => 'teamBuyActBtn',
							'state' => $icon);
			$actState['teamBuy'] = $teamBuy;
		}

		return $actState;
	}

}