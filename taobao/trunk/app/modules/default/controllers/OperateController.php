<?php

define('ADMIN_USERNAME','admin'); 					// Admin Username
define('ADMIN_PASSWORD','hapyfish');  	// Admin Password

class OperateController extends Zend_Controller_Action
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
	
	public function indexAction(){}
	
	/**********团购任务后台************/
	public function teambuyAction()
	{
		$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
		$info = $db->getTeamBuyMessage();

		$shexTin = explode('*', $info['gid']);
		$shex['cid'] = $shexTin[0];
		$shex['num'] = $shexTin[1];

		if($info['start_time']) {
			$start_time = date('Y-m-d H:i:s', $info['start_time']);
		} else {
			$start_time = date('Y-m-d H:i:s', time());
		}

		$join_time = $info['ok_time'];
		$buy_time = $info['buy_time'];

		$min_price_info = explode('*', $info['min_price']);
		if($min_price_info[1] == 1) {
			$min_price = $min_price_info[0] . '*金币';
		} else if($min_price_info[1] == 2) {
			$min_price = $min_price_info[0] . '*宝石';
		}

		$max_price_info = explode('*', $info['max_price']);
		if($max_price_info[1] == 1) {
			$max_price = $max_price_info[0] . '*金币';
		} else if($max_price_info[1] == 2) {
			$max_price = $max_price_info[0] . '*宝石';
		}

		(int)$hasNum = $db->getNum();
		
		$this->view->shex = $shex;
		$this->view->start_time = $start_time;
		$this->view->join_time = $join_time;
		$this->view->buy_time = $buy_time;
		$this->view->min_price = $min_price;
		$this->view->max_price = $max_price;
		$this->view->info = $info;
		$this->view->hasnum = $hasNum;
	}
	
	public function teambuyupdateAction()
	{
		$teambuyData = $this->_request->getParams('operate');
		$teambuy = $teambuyData['operate'];

		$shex = $teambuy['gid'] . '*' . $teambuy['num'];
		$start_time = strtotime($teambuy['start_time']);

		$max_price_info = explode('*', $teambuy['max_price']);
		if($max_price_info[1] == '宝石') {
			$max_price_info[1] = 2;
		} else {
			$max_price_info[1] = 1;
		}
		$max_price = $max_price_info[0] . '*' . $max_price_info[1];

		$min_price_info = explode('*', $teambuy['min_price']);
		if($min_price_info[1] == '宝石') {
			$min_price_info[1] = 2;
		} else {
			$min_price_info[1] = 1;
		}
		$min_price = $min_price_info[0] . '*' . $min_price_info[1];

		$info = array('gid' => $shex,
						'name' => $teambuy['name'],
						'start_time' => $start_time,
						'ok_time' => $teambuy['ok_time'],
						'buy_time' => $teambuy['buy_time'],
						'max_price' => $max_price,
						'min_price' => $min_price,
						'min_num' => $teambuy['min_num'],
						'max_num' => $teambuy['max_num'],
						'start_num' => $teambuy['start_num'],
						'bec_num' => $teambuy['bec_num'],
						'bec_price' => $teambuy['bec_price'],
						'scale_gold' => $teambuy['scale_gold'],
						'scale_coin' => $teambuy['scale_coin']);

		$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
		$db->updateTeamBuyInfo($info);

		$key = 'ev:teambuy:data';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);

		$this->_redirect("operate/teambuy");
	}

	public function clearteambuycacheAction()
	{
		$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
    	$users = $db->getHasUser();

    	if($users) {
    		foreach ($users as $uid) {
		    	$keys = 'ev:teambuy:join:' . $uid;
				$caches = Hapyfish2_Cache_Factory::getMC($uid);
				$caches->delete($keys);
				
				$db->clearOneUser($uid);
	    	}
    	}
    	
		$key = 'ev:teambuy:data';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);

		$this->_redirect("operate/teambuy");
	}

	public function teambuyswitchAction()
	{
		$teambuy = $this->_request->getParams('teambuyswitch');

		$db = Hapyfish2_Island_Event_Dal_TeamBuy::getDefaultInstance();
		$db->switchTeamBuy($teambuy['teambuyswitch']);

		$key = 'ev:teambuy:data';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		$this->_redirect("operate/teambuy");
	}
	
	/**********收集任务后台************/
	public function collectlistAction()
	{

		$key = 'collectgift';
		$jianglikey = 'jiangliid';
		$timekey = 'time';
		$xiaoxikey = 'xiaoxi';

		$val = Hapyfish2_Island_Event_Bll_Hash::getval($key);
		$jianglival = Hapyfish2_Island_Event_Bll_Hash::getval($jianglikey);
		$time = Hapyfish2_Island_Event_Bll_Hash::getval($timekey);
		$message = Hapyfish2_Island_Event_Bll_Hash::getval($xiaoxikey);

		$keyswitch = "collectcontrolswitch";
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$switch = $cache->get($keyswitch);

		if($switch['uid']) {
			$this->view->switchuid = $switch['uid'];
		}

		if( $val ) {
			$list = unserialize( $val );
			$this->view->list = $list;
		}

		if( $jianglival ) {
			$this->view->jiangli_id = $jianglival;
		}

		if($time) {
			$time = unserialize( $time );
			$time['start'] = date('Y-m-d H:i:s',$time['start']);
			$time['end'] = date('Y-m-d H:i:s',$time['end']);
		    $this->view->time = $time;
		}

		if($message) {
		    $this->view->message = unserialize( $message );
		}
	}

	public function collectlistdoAction()
	{
		$key = 'collectgift';
		$jianglikey = 'jiangliid';
		$timekey = 'time';
		$xiaoxikey = 'xiaoxi';

		$names = $this->_request->getParam('names');
		$cids = $this->_request->getParam('cids');
		$tips = $this->_request->getParam('tips');
		$time['start'] = $this->_request->getParam('start');
		$time['end'] = $this->_request->getParam('end');
		$message['tishi'] = $this->_request->getParam('tishi');
		$message['zhu'] = $this->_request->getParam('zhu');
		$jiangli_id = $this->_request->getParam('jiangli_id');

		$time['start'] = $time['start'] ? strtotime($time['start']) : strtotime('now');
		$time['end'] = strtotime($time['end']);

		$arr = array();
		foreach( $names as $k => $v ) {
			$arr[] = array('name'=>$names[$k], 'cid'=>$cids[$k], 'tip'=>$tips[$k]);
		}
		if( $arr ) {
			Hapyfish2_Island_Event_Bll_Hash::setval($key, serialize( $arr ));
		}
		if( $jiangli_id ) {
			Hapyfish2_Island_Event_Bll_Hash::setval($jianglikey, $jiangli_id );
		}
		if($time){
		   Hapyfish2_Island_Event_Bll_Hash::setval($timekey, serialize($time) );
		}
		if($message){
		    Hapyfish2_Island_Event_Bll_Hash::setval($xiaoxikey, serialize($message) );
		}

		$this->_redirect("operate/collectlist");
	}

	public function controlswitchAction()
	{
		$uids = $this->_request->getParam('uids');
		$type = $this->_request->getParam('type');

		$result = array();
		$result['type'] = $type;
		$result['uid'] = $uids;

		$key = "collectcontrolswitch";
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->set($key,$result);

		$this->_redirect("operate/collectlist");
	}

	public function clearcollectAction()
	{
		Hapyfish2_Island_Event_Bll_Hash::clearall();
		$this->_redirect("operate/collectlist");
	}
	
	/**********一元店任务后台************/
	public function onegoldAction(){}
	
	public function onegoldindexAction()
	{
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$dataVo = $db->AllData();
		
		$this->view->datavo = $dataVo;
	}
	
	public function onegoldstatusAction()
	{
		$id = $this->_request->getParam('id', 1);
	
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$db->updateStatus($id);
		
		$this->_redirect("operate/onegoldindex");
	}
	
	public function onegoldupdateAction()
	{
		$dataVo = $this->_request->getParam('operate');
	
		$startTime = strtotime($dataVo['start_time']);
		$endTime = strtotime($dataVo['end_time']);
		
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$db->update($dataVo, $startTime, $endTime);
		
		$key = 'i:e:onegold:all';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		$key1 = 'i:e:oneshop:gift';
		$cache->delete($key1);
		
		$this->_redirect("operate/onegoldindex");
	}
	
	public function onegoldaddnewAction()
	{
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$db->addNewOne();
		
		$this->_redirect("operate/onegoldindex");
	}

	public function onegoldclearnumAction()
	{
		$key = 'i:e:oneshop:gift:hasnum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		$this->_redirect("operate/onegoldindex");
	}
	
	public function onegoldboxAction()
	{
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$dataVo = $db->boxInfo();
		
		$this->view->datavo = $dataVo;
	}
	
	public function onegoldboxupdateAction()
	{
		$dataVo = $this->_request->getParam('data');
		
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$db->boxUpdate($dataVo);
		
		$this->_redirect("operate/onegoldbox");
	}
	
	public function onegoldincnewboxAction()
	{
		$db = Hapyfish2_Island_Event_Dal_OneGoldShop::getDefaultInstance();
		$db->incNewBox();
		
		$this->_redirect("operate/onegoldbox");
	}
	
	/*******************/
	public function atlasbookAction()
	{
		$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
		$dataVo = $db->getData();
		
		$this->view->datavo = $dataVo;
	}
	
	public function atlasbookupdateAction()
	{
		$dataVo = $this->_request->getParam('data');

		$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
		$db->atlasbookupdate($dataVo);
		
		$key = 'ev:atlasbook';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		$this->_redirect("operate/atlasbook");
	}
	
	public function atlasbookaddnewAction()
	{
		$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
		$lastID = $db->getLastID();
		
		$lastID += 1;
		$db->atlasbookaddnew($lastID);
		
		$this->_redirect("operate/atlasbook");
	}
	
	public function atlasbookdelAction()
	{
		$id = $this->_request->getParam('id', 1);
	
		$db = Hapyfish2_Island_Event_Dal_AtlasBook::getDefaultInstance();
		$db->atlasbookdel($id);
		
		$key = 'ev:atlasbook';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		$this->_redirect("operate/atlasbook");
	}
	
	/************连续登陆****************/
	public function awardconfigAction()
	{
		$key = 'i:award:config';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$config = $cache->get($key);
		
		if($config === false){
    		try {
            	$db = Hapyfish2_Island_Dal_Fragments::getDefaultInstance();
            	$config = $db->getAwardConfig();
    		} catch (Exception $e) {} 
		}
		
		$time = date('Y-m-d H:i:s', $config['create_time']);
		
		$this->view->cid = $config['cid'];
		$this->view->time = $time;
	}
	
	public function awardconfigupdateAction()
	{
		$cid = $this->_request->getParam('cid');
		$time = $this->_request->getParam('time');
		
		$create_time = strtotime($time);
		
		try {
			$db = Hapyfish2_Island_Dal_Fragments::getDefaultInstance();
			$db->updateAwaraConfig($cid, $create_time);
		} catch (Exception $e) {}
		
		$key = 'i:award:config';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		
		$this->_redirect("operate/awardconfig");
	}
	
}