<?php

class Hapyfish2_Island_Bll_Bot
{

	public static function crawlStatMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_main($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Main::add($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.statmain');
		}
	}

	public static function crawlStatActiveuserlevel($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_activeuserlevel($day);
			$data = $result['data'];
			$levelData = $data['level'];
			$level = array();
			if (!empty($levelData)) {
				$tmp = explode(',', $levelData);
				foreach ($tmp as $d) {
					if ($d) {
						$t = explode(':', $d);
						if ($t[1] != 0) {
							$level[$t[0]] = $t[1];
						}
					}
				}
			}
			$data['level'] = json_encode($level);
			Hapyfish2_Island_Bll_ActiveUserLevel::addActiveUserLevel($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.activeuserlevel');
		}
	}

	public static function crawlStatRetention($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_retention($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Retention::add($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.statretention');
		}
	}

	public static function crawlStatPayment($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_paymentofcal($day);
			$data = $result['data'];

			if ( $platform == 'qq' ) {
				$amount = round($data['amount']/10);
			}
			else {
				$amount = $data['amount'];
			}

			if ( !isset($data['costGold']) ) {
				$data['costGold'] = 0;
			}
			$info = array('pay_total_amount' => $amount, 'pay_gold_count' => $data['gold'], 'pay_user_count' => $data['count'], 'pay_count' => $data['userCount'], 'cost_gold' => $data['costGold']);
			Hapyfish2_Island_Bll_Main::updateInfo($platform, $day, $info);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.statpayment');
		}
	}

    public static function crawlStatTutorial($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_tutorial($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Tutorial::add($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.stattutorial');
        }
    }
    public static function crawlStatSendgold($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_sendgold($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Sendgold::add($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statsendgold');
        }
    }
    public static function crawlStatPayclick($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_payclick($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Payclick::add($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statpayclick');
        }
    }
    public static function crawlStatPropsale($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_propsale($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Propsale::add($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statpropsale');
        }
    }
    //所有用户等级分布
    public static function crawlStatUserlevel($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_userlevel($day);
            $data = $result['data'];

            Hapyfish2_Island_Bll_ActiveUserLevel::addUserlevel($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statuserlevel');
        }
    }

    //所有用户等级分布
    public static function crawlStatLevelup($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_levelup($day);
            $data = $result['data'];

            Hapyfish2_Island_Bll_ActiveUserLevel::addLevelup($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statlevelup');
        }
    }

    //七天未登录用户信息
    public static function crawlStatLossuser($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_lossuser($day);
            $data = $result['data'];

            Hapyfish2_Island_Bll_LossUser::addLossUser($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statlossuser');
        }
    }
    
    //充值相关
    public static function crawlStatPaylist($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_paylist($day);
            $data = $result['data'];

            Hapyfish2_Island_Bll_Payment::addPaylist($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statpaylist');
        }
    }

    public static function crawlStatDonate($bot, $day, $platform)
    {
        try {
            $result = $bot->stat_donate($day);
            $data = $result['data'];

            Hapyfish2_Island_Bll_Donate::addDayDonate($platform, $data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.crawl.statdonate');
        }
    }

    
	public static function crawl()
	{
		$list = Hapyfish2_Island_Bll_ApiInfo::getStatPlatform();
		
		if (!empty($list)) {
			$day1 = date("Ymd", strtotime("-1 day"));
            $day2 = date("Ymd", strtotime("-2 day"));
			$cuid = '1';
			
			foreach ($list as $platform => $stat) {
				//
				$bot = Hapyfish2_Rest_Factory::getBot($platform);
				if (!$bot) {
					continue;
				}
				$bot->setUser($cuid);

				if ( $platform == 'fb_esp') {
					$day = $day2;
				}
				else {
					$day = $day1;
				}

				self::crawlStatMain($bot, $day, $platform);

				self::crawlStatActiveuserlevel($bot, $day, $platform);

				self::crawlStatRetention($bot, $day, $platform);

				if ($stat == 1) {
					self::crawlStatPayment($bot, $day, $platform);
				}

				self::crawlStatMainHour($bot, $day, $platform);

				if ( in_array($platform, array('fb_thailand', 'taobao', 'weibo', 'fb_taiwan', 'qq')) ) {
                    self::crawlStatTutorial($bot, $day, $platform);
				}

                if ( in_array($platform, array('weibo','taobao','fb_taiwan','fb_thailand')) ) {
                	self::crawlStatSendgold($bot, $day, $platform);
                }
				if ( in_array($platform, array('fb_thailand')) ) {
                	self::crawlStatPayclick($bot, $day, $platform);
                }
                if ( in_array($platform, array('taobao')) ) {
                    self::crawlStatPropsale($bot, $day, $platform);
                }
                if ( in_array($platform, array('ipanda_taobao')) ) {
                	//所有用户等级分布
                    self::crawlStatUserlevel($bot, $day, $platform);
                    //每日升级人数
                    self::crawlStatLevelup($bot, $day, $platform);
                    //充值相关
                    self::crawlStatPaylist($bot, $day, $platform);

                    self::crawlStatDonate($bot, $day, $platform);
                    
                    //七天未登录用户信息
                    self::crawlStatLossuser($bot, $day, $platform);
                }
                
                //炼金
                if ( in_array($platform, array('alchemy_kaixin')) ) {
                	//佣兵信息
					self::crawlStatMercenaryMain($bot, $day, $platform);
					//订单
					self::crawlStatOrderMain($bot, $day, $platform);
					//道具
					self::crawlStatItemMain($bot, $day, $platform);
					//战斗
					self::crawlFightMain($bot, $day, $platform);
					//交互
					self::crawlMutualMain($bot, $day, $platform);
					//修理
					self::crawlRepair($bot, $day, $platform);
					//升级
					self::crawlUpgrade($bot, $day, $platform);
                }
                
				info_log('crawl[' . $platform . ']:finished.', 'bot.crawl');
			}
		}
	}

	public static function crawlHour()
	{
		$list = Hapyfish2_Island_Bll_ApiInfo::getStatPlatform();
		if (!empty($list)) {
			$hour1 = date("YmdH", strtotime("-1 hours"));
			$cuid = '1';
			foreach ($list as $platform => $stat) {
				//
				$bot = Hapyfish2_Rest_Factory::getBot($platform);
				if (!$bot) {
					continue;
				}
				$bot->setUser($cuid);
                //炼金
                if ( in_array($platform, array('alchemy_kaixin')) ) {
                	//佣兵信息
					self::crawlStatMainHour($bot, $hour1, $platform);
                }
                
				info_log('crawl[' . $platform . ']:finished.', 'bot.crawlhour');
			}
		}
	}
	
    public static function crawlByPlatform($platform)
    {
            $day1 = date("Ymd", strtotime("-1 day"));
            $day2 = date("Ymd", strtotime("-2 day"));
            $cuid = '1';
            
                //
                $bot = Hapyfish2_Rest_Factory::getBot($platform);
                if (!$bot) {
                    continue;
                }
                $bot->setUser($cuid);

                $day = $day1;
                
                self::crawlStatMain($bot, $day, $platform);

                self::crawlStatActiveuserlevel($bot, $day, $platform);

                self::crawlStatRetention($bot, $day, $platform);

                self::crawlStatPayment($bot, $day, $platform);
                
                self::crawlStatMainHour($bot, $day, $platform);

                if ( in_array($platform, array('fb_thailand', 'taobao', 'weibo', 'fb_taiwan', 'qq', 'nk_poland')) ) {
                    self::crawlStatTutorial($bot, $day, $platform);
                }

                info_log('crawl[' . $platform . ']:finished.', 'bot.crawl');
    }
    
    /********************************* 炼金 ***************************************/
    //炼金-佣兵信息
	public static function crawlStatMercenaryMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_mercenarymain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Mercenary::addMercenaryMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.mercenarymain');
		}
	}
    //炼金-订单
	public static function crawlStatOrderMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_ordermain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Order::addOrderMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.ordermain');
		}
	}
    //炼金-道具
	public static function crawlStatItemMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_itemmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Item::addItemMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.itemmain');
		}
	}
    //炼金-商店
	public static function crawlStatShopMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_shopmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Shop::addShopMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.shopmain');
		}
	}
    //炼金-合成术
	public static function crawlStatMixMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_mixmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Mix::addMixMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.mixmain');
		}
	}

    //炼金-小时数据统计
	public static function crawlStatMainHour($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_statmainhour($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_StatMainHour::add($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.statmainhour');
		}
	}
	
    //战斗
	public static function crawlFightMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_fight($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addFightMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.fightmain');
		}
	}
    //交互
	public static function crawlMutualMain($bot, $day, $platform)
	{
	try {
			$result = $bot->stat_mutual($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addMutualMain($platform, $data);
			
			
		} catch (Exception $e) {
			$this->echoError($e->getCode(), 'bot.crawl.mutual');
		}
	}
	//修理
	public static function crawlRepair($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_repair($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addRepairMain($platform, $data);
			
			
		} catch (Exception $e) {
			$this->echoError($e->getCode(), 'bot.crawl.repair');
		}
	}
	//建筑升级
	public static function crawlUpgrade($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_upgrade($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Upgrade::addUpgradeMain($platform, $data);
			
			
		} catch (Exception $e) {
			$this->echoError($e->getCode(), 'bot.crawl.upgrade');
		}
	}
	
}