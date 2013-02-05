<?php

class BotController extends Zend_Controller_Action
{
	function init()
    {
    	$this->cuid = '1';
    	$this->platform = $this->_request->getParam('platform');
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }
    
    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }
	
	public function getstatmainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_main($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Main::add($this->platform, $data);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getstatactiveuserlevelAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
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
			Hapyfish2_Island_Bll_ActiveUserLevel::addActiveUserLevel($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getstatretentionAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_retention($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Retention::add($this->platform, $data);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getpaymentAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_payment($day);
			$data = $result['data'];
		
			if ( $this->platform == 'qq' ) {
				$amount = round($data['amount']/10);
			}
			else {
				$amount = $data['amount'];
			}
			$info = array('pay_total_amount' => $amount, 'pay_gold_count' => $data['gold'], 'pay_user_count' => $data['trans_count']);
			Hapyfish2_Island_Bll_Main::updateInfo($this->platform, $day, $info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getpaymentofcalAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_paymentofcal($day);
			$data = $result['data'];
			
			if ( $this->platform == 'qq' ) {
				$amount = round($data['amount']/10);
			}
			else {
				$amount = $data['amount'];
			}
			
			if ( !isset($data['costGold']) ) {
				$data['costGold'] = 0;
			}
			$info = array('pay_total_amount' => $amount, 'pay_gold_count' => $data['gold'], 'pay_user_count' => $data['count'], 'pay_count' => $data['userCount'], 'cost_gold' => $data['costGold']);
			Hapyfish2_Island_Bll_Main::updateInfo($this->platform, $day, $info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	public function getstatmainhourAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_mainhour($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_MainHour::add($this->platform, $data);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
    public function getstattutorialAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_tutorial($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Tutorial::add($this->platform, $data);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    public function getstatsendgoldAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_sendgold($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Sendgold::add($this->platform, $data);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    public function getstatpayclickAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_payclick($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Payclick::add($this->platform, $data);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }        
    public function getstatmainmonthAction()
    {
        $month = $this->_request->getParam('month');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_mainmonth($month);
            $data = $result['data'];
            Hapyfish2_Island_Bll_MainMonth::addMainMonth($this->platform, $data);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
         
    //商城道具排行榜
    public function getpropsaledataAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_propsale($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Propsale::add($this->platform, $data);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
    //所有用户等级分布
    public function getstatalluserlevelAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_userlevel($day);
            $data = $result['data'];
            
            Hapyfish2_Island_Bll_ActiveUserLevel::addUserlevel($this->platform, $data);
            
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
    //每日升级人数
    public function getstatlevelupAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_levelup($day);
            $data = $result['data'];
            
            Hapyfish2_Island_Bll_ActiveUserLevel::addLevelup($this->platform, $data);
            
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
    //充值相关（额度分布，等级分布）
    public function getstatpaylistAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_paylist($day);
            $data = $result['data'];
            
            Hapyfish2_Island_Bll_Payment::addPaylist($this->platform, $data);
            
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
    //所有用户等级分布
    public function getstatlossuserAction()
    {
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
            $result = $bot->stat_lossuser($day);
            $data = $result['data'];
            
            Hapyfish2_Island_Bll_LossUser::addLossUser($this->platform, $data);
            
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
	public function getstatallAction()
	{
        $day = $this->_request->getParam('day');
        $bot = Hapyfish2_Rest_Factory::getBot($this->platform);
        if (!$bot) {
            $this->echoError('-1', 'apiinfo error');
        }
        $bot->setUser($this->cuid);
        try {
        	//getstatmain
            $result = $bot->stat_main($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Main::add($this->platform, $data);
            
            //getactiveuserlevel
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
            Hapyfish2_Island_Bll_ActiveUserLevel::addActiveUserLevel($this->platform, $data);
            
            //getretention
            $result = $bot->stat_retention($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_Retention::add($this->platform, $data);
            
            //getpaymentofcal
            /*$result = $bot->stat_paymentofcal($day);
            $data = $result['data'];
            if ( $this->platform == 'qq' ) {
                $amount = round($data['amount']/10);
            }
            else {
                $amount = $data['amount'];
            }
            
            if ( !isset($data['costGold']) ) {
                $data['costGold'] = 0;
            }
            $info = array('pay_total_amount' => $amount, 'pay_gold_count' => $data['gold'], 'pay_user_count' => $data['count'], 'pay_count' => $data['userCount'], 'cost_gold' => $data['costGold']);
            Hapyfish2_Island_Bll_Main::updateInfo($this->platform, $day, $info);*/
            
            //getmainhour
            $result = $bot->stat_mainhour($day);
            $data = $result['data'];
            Hapyfish2_Island_Bll_MainHour::add($this->platform, $data);
            
            $this->echoResult('OK');
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
	    
	}
	
	public function md5Action()
	{
		$pwd = $this->_request->getParam('pwd');
		$password = md5($pwd. '&' . SECRET);
		echo $password;
		exit;
	}
	
}