<?php

define('ADMIN_USERNAME','admin'); 					// Admin Username
define('ADMIN_PASSWORD','yewushuang_920');  	// Admin Password

class MycatchfishController extends Zend_Controller_Action
{


	public function init()
	{
		// http 401 验证
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
           $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
			Header("WWW-Authenticate: Basic realm=\"Who is god of wealth, Login\"");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
		}

		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
	}
	
	//捕鱼2	Admin Tools
	
	public function statAction()
	{
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		$date = $this->_request->getParam('date');
		if($act == 'search') {
			$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
			$info = $dalFish->getStat($date);
			$this->view->info = $info;
		}
		$this->view->date = $date;
	}
	
	public function fishinfoAction()
	{
		$fishId = $this->_request->getParam('fishid');
		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		$fishInfo = $dalFish->getFishInfo($fishId);
		$this->view->fishinfo = $fishInfo;
		$this->view->fishid = $fishId;
		$act = $this->_request->getParam('act');
		if($act == 'update') {
			$islandids = $this->_request->getParam('islandids');
			$name = $this->_request->getParam('name');
			$istask = $this->_request->getParam('istask');
			$type = $this->_request->getParam('type');
			$cid = $this->_request->getParam('cid');
			$itemid = $this->_request->getParam('itemid');
			$num = $this->_request->getParam('num');
			$coin = $this->_request->getParam('coin');
			$gold = $this->_request->getParam('gold');
			$difficulty = $this->_request->getParam('difficulty');
			$probability = $this->_request->getParam('probability');
			$isfish = $this->_request->getParam('isfish');
			
			$fields = array(
				'islandids'	=> 	$islandids,
				'name'		=>	trim($name),
				'istask'	=>	(int)$istask,
				'type'		=>	(int)$type,
				'cid'		=>	(int)$cid,
				'itemid'	=>	(int)$itemid,
				'num'		=>	(int)$num,
				'coin'		=>	(int)$coin,
				'gold'		=>	(int)$gold,
				'difficulty'=>	(int)$difficulty,
				'probability'=>	trim($probability),
				'isfish'	=>	(int)$isfish	
				
			);
			$dalFish->updateFishById($fishId, $fields);
			
			$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
			$key = 'i:fish:fsall';
			$cache->delete($key);

			for($i=1;$i<=5;$i++) {
				$key = 'i:fish:island:fishs:'.$i;
				$cache->delete($key);			 
			}
			
			$key = 'i:u:fish:finfo:'.$fishId;
			$cache->delete($key);
			
			$url = HOST.'/mycatchfish/fishinfo/fishid/'.$fishId;
			$this->showMessage("操作成功!", $url);
		}
	}
	
	public function probabilityAction()
	{
		$islandId = $this->_request->getParam('islandid');
		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		$fishes = $dalFish->getCatchFishesByIslandId($islandId);
		if($fishes) {
			foreach($fishes as $k=>$v) {
				$fishes[$k]['name'] = $dalFish->getFishNameById($v['fishid']);
			}
		}
		$this->view->islandid = $islandId;
		$this->view->fishes = $fishes;
		
		$act = $this->_request->getParam('act');
		if($act == 'update') {
			$typeids = $this->_request->getParam('typeids');
			$fishids = $this->_request->getParam('fishids');
			$islandids = $this->_request->getParam('islandids');
			$probability1 = $this->_request->getParam('probability1');
			$probability2 = $this->_request->getParam('probability2');
			$probability3 = $this->_request->getParam('probability3');
			$probability4 = $this->_request->getParam('probability4');
			$count = count($fishids);
			for($i=0;$i<$count;$i++) {
				$dalFish->updateCatchFish($typeids[$i], $islandids[$i], $fishids[$i], $probability1[$i], $probability2[$i], $probability3[$i], $probability4[$i]);
			}			
			$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
			for($m=1;$m<=2;$m++) {
				$key = 'i:u:fish:ctfhs:'.$islandId.':'.$m;
				$cache->delete($key);
			}
			
			$url = HOST.'/mycatchfish/probability/islandid/'.$islandId;
			$this->showMessage("操作成功!", $url);			
		}
	}

	public function relicplantAction()
	{
		$dalFish = Hapyfish2_Island_Dal_Fish::getDefaultInstance();
		$data = $dalFish->getFishPlant();
		$this->view->data = $data;
		
		$act = $this->_request->getParam('act');
		if($act == 'update') {
			$id = $this->_request->getParam('id');
			$cid = $this->_request->getParam('cid');
			$item_id = $this->_request->getParam('item_id');
			$isGem = $this->_request->getParam('isGem');
			$material = $this->_request->getParam('material');
			$fileds = array(
				'cid'		=>	trim($cid),
				'item_id'	=>	trim($item_id),
				'isGem'		=>	$isGem,
				'material'	=>	trim($material)
			);
			$dalFish->updateFishPlant($id, $fileds);
			
			$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
			$key = 'i:u:fish:plants';
			$cache->delete($key);
			
			$key = 'i:u:fish:plt:itm:'.$item_id;
			$cache->delete($key);		

			$key = 'i:u:fish:itemplt:'.$item_id;
			$cache->delete($key);			
			
			$url = HOST.'/mycatchfish/probability/relicplant/';
			$this->showMessage("操作成功!", $url);				
		}elseif($act == 'add') {
			$cid = $this->_request->getParam('cid');
			$item_id = $this->_request->getParam('item_id');
			$isGem = $this->_request->getParam('isGem');
			$material = $this->_request->getParam('material');
			$fileds = array(
				'cid'		=>	trim($cid),
				'item_id'	=>	trim($item_id),
				'isGem'		=>	$isGem,
				'material'	=>	trim($material)
			);
			$dalFish->addFishPlant($fileds);

			$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
			$key = 'i:u:fish:plants';
			$cache->delete($key);	

			$url = HOST.'/mycatchfish/relicplant/';
			$this->showMessage("操作成功!", $url);			
		}
		
	}
	
	public function showMessage($content, $url)
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
		echo '<script>alert("'.$content.'");window.location.href="'.$url.'";</script>';
		exit;
	}	
	
	public function fishmatchAction()
	{
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		$date = $this->_request->getParam('date');
		if(!$date){
			$dtYesterday = strtotime("-1 day");
			$date = date('Ymd', $dtYesterday);
		}
		$dalFish = Hapyfish2_Island_Stat_Dal_Catchfish::getDefaultInstance();
		$compound = $dalFish->getCompoundFish($date);
		$sea = $dalFish->getFishSea($date);
		$pve = $dalFish->getFishPve($date);
		$comCard = $dalFish->getComCard($date);
		$levelUp = $dalFish->getFishLevelUp($date);
		$step = $dalFish->getFishStep($date);
		$detail = $dalFish->getCompoundFishDetail($date);
		$skill = $dalFish->getSkill($date);
		$zizhi = $dalFish->getZizhi($date);
		$pvp = $dalFish->getpvp($date);
		$this->view->compound = $compound;
		$this->view->sea = $sea;
		$this->view->pve = $pve;
		$this->view->comCard = $comCard;
		$this->view->levelUp = $levelUp;
		$this->view->step = $step;
		$this->view->detail = $detail;
		$this->view->skill = $skill;
		$this->view->pvp = $pvp;
		$this->view->zizhi = $zizhi;
		$this->view->date = $date;
	}
	
	public function paystatAction()
	{
		$list = array();
		$dal = Hapyfish2_Island_Dal_Vip::getDefaultInstance();
		for($id=1;$id<=DATABASE_NODE_NUM;$id++){
			$data = $dal->getvipStat($id);
			$list = array_merge($list,$data);
		}
		foreach($list as $k => $v){
    		$volume[$k] = $v['money'];
		}
		array_multisort($volume, SORT_DESC, $list);
		$this->view->list = $list;
	}
	
}