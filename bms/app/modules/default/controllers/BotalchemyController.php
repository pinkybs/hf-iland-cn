<?php

class BotalchemyController extends Zend_Controller_Action
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
	
    //佣兵
	public function getmercenarymainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_mercenarymain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Mercenary::addMercenaryMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
    //订单
	public function getordermainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_ordermain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Order::addOrderMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
    //道具
	public function getitemmainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_itemmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Item::addItemMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
    //商店
	public function getshopmainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_shopmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Shop::addShopMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
    //合成术
	public function getmixmainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_mixmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Mix::addMixMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
    //小时数据统计
	public function getstatmainhourAction()
	{
		$day = $this->_request->getParam('day');
		$allday = $this->_request->getParam('allday', 0);
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			if ( $allday > 0 ) {
				$time = $allday . '00';
				$time = (int)$time;
				for ( $i=0;$i<24;$i++ ) {
					$result = $bot->stat_statmainhour($time);
					$data = $result['data'];
					Hapyfish2_Island_Bll_StatMainHour::add($this->platform, $data);
					$time++;
				}
			}
			else {
				$result = $bot->stat_statmainhour($day);
				$data = $result['data'];
				Hapyfish2_Island_Bll_StatMainHour::add($this->platform, $data);
			}
			
			$this->echoResult($result);
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
	//战斗
	public function getfightmainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_fight($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addFightMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	//交互
	public function getmutualmainAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_mutual($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addMutualMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	//修理
	public function getrepairAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_repair($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addRepairMain($this->platform, $data);
			
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
	}
	
	//建筑升级
	public function getupgradeAction()
	{
		$day = $this->_request->getParam('day');
		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
		if (!$bot) {
			$this->echoError('-1', 'apiinfo error');
		}
		$bot->setUser($this->cuid);
		try {
			$result = $bot->stat_upgrade($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Upgrade::addUpgradeMain($this->platform, $data);
			
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
			$result = $bot->stat_mercenarymain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Mercenary::addMercenaryMain($this->platform, $data);
			
			$result = $bot->stat_ordermain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Order::addOrderMain($this->platform, $data);
			
			$result = $bot->stat_itemmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Item::addItemMain($this->platform, $data);
			
			$result = $bot->stat_shopmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Shop::addShopMain($this->platform, $data);
			
			$result = $bot->stat_mixmain($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Mix::addMixMain($this->platform, $data);
			
			$result = $bot->stat_statmainhour($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_StatMainHour::add($this->platform, $data);
			
			$result = $bot->stat_fight($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addFightMain($this->platform, $data);
			
			$result = $bot->stat_mutual($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addMutualMain($this->platform, $data);
			
			$result = $bot->stat_repair($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Fight::addRepairMain($this->platform, $data);
			
			$result = $bot->stat_upgrade($day);
			$data = $result['data'];
			Hapyfish2_Island_Bll_Upgrade::addUpgradeMain($this->platform, $data);
			
            
            $this->echoResult('OK');
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
	    
	}
	
}