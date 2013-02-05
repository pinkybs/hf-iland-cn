<?php

class EventtestController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
        	$result = array('status' => '-1', 'content' => 'serverWord_101');
			$this->echoResult($result);
        }

        $this->info = $info;
        $this->uid = $info['uid'];

    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	if (!$skey) {
    		return false;
    	}

    	$tmp = split('_', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $openid = $tmp[1];
        $openkey = $tmp[2];
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $openid . $openkey . $t . APP_KEY);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $openid . $openkey . $t . $rnd . APP_KEY);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'openid' => $openid, 'openkey' => $openkey,  't' => $t, 'rnd' => $rnd);
    }

    protected function checkEcode($params = array())
    {
    	if ($this->info['rnd'] > 0) {
    		$rnd = $this->info['rnd'];
    		$uid = $this->uid;
    		$ts = $this->_request->getParam('tss');
    		$authid = $this->_request->getParam('authid');
    		$ok = true;
    		if (empty($authid) || empty($ts)) {
    			$ok = false;
    		}
    		if ($ok) {
    			$ok = Hapyfish2_Island_Bll_Ecode::check($rnd, $uid, $ts, $authid, $params);
    		}
    		if (!$ok) {
    			//Hapyfish2_Island_Bll_Block::add($uid, 1, 2);
    			//info_log($uid, 'fecode');
    			info_log($uid, 'ecode-err');
	        	$result = array('status' => '-1', 'content' => 'serverWord_101');
	        	setcookie('hf_skey', '' , 0, '/', '.island.qzoneapp.com');
	        	$this->echoResult($result);
    		}
    	}
    }

    protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

	public function goldeggAction()
    {
    	$uid = $this->uid;
    	$time = time();

    	$result = array('status' => '-1', 'content' => 'serverWord_110');

    	$isGained = Hapyfish2_Island_Event_Bll_NewYearEgg::isGained($uid);
		if (!$isGained) {
			$result = Hapyfish2_Island_Event_Bll_NewYearEgg::gain($uid, $time);
		} else {
			$result['content'] = 'serverWord_151';
		}
		$this->echoResult($result);
    }

	public function cleartestgiftAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:e:tstg:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		$dal = Hapyfish2_Island_Event_Dal_TestGift::getDefaultInstance();
		$dal->delete($uid);
        echo 'ok';
        exit;
	}

	public function clearactive5dayAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:e:atv5d:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		$dal = Hapyfish2_Island_Event_Dal_Active5Day::getDefaultInstance();
		$dal->delete($uid);
        echo 'ok';
        exit;
	}

	public function clearkittenAction()
	{
		$uid = $this->_request->getParam('uid');
		$key = 'i:u:e:ckitten:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		$dal = Hapyfish2_Island_Event_Dal_CollectKitten::getDefaultInstance();
		$dal->delete($uid);
        echo 'ok';
        exit;
	}

	public function addredpaperAction()
	{
		$uid = $this->_request->getParam('uid');
		$num = $this->_request->getParam('num');
		Hapyfish2_Island_Event_Bll_Newyear::addRedPaper($uid, $num);
        echo 'ok';
        exit;
	}

	public function loadlotterylistAction()
	{
		Hapyfish2_Island_Cache_LotteryItemOdds::loadLotteryItemOddsList(1);
		$key = 'island:lotteryitemodds:1';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$cache = Hapyfish2_Island_Cache_LotteryItemOdds::getBasicMC();
		$list = $cache->get($key);
		$localcache->set($key, $list);
        echo 'ok';
        exit;
	}

	public function cleareventfeedAction()
	{
		$mkey = 'event_newyear_exchange_list';
		$eventFeed = Hapyfish2_Cache_Factory::getEventFeed();
		$eventFeed->delete($mkey);
        echo 'ok';
        exit;
	}

	public function clearwealthgodAction()
	{
		$uid = $this->_request->getParam('uid');
		$mkey = 'i:u:wealthgoddly:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($mkey);
        echo 'ok';
        exit;
	}

	public function loadvalentinecacheAction()
	{
		$uid = $this->_request->getParam('uid');
		$mkey1 = Hapyfish2_Island_Event_Bll_Valentine::CACHE_KEY_EXCHANGE;
		$eventFeed = Hapyfish2_Cache_Factory::getEventFeed();
		$data1 = $eventFeed->get($mkey1);
		echo Zend_Json::encode($data1);
		echo '<br />';
		$mkey2 = Hapyfish2_Island_Event_Bll_Valentine::CACHE_KEY_RANK;
        $eventRank = Hapyfish2_Cache_Factory::getEventRank();
    	$data2 = $eventRank->get($mkey2);
		echo Zend_Json::encode($data2);
		echo '<br />';

		$locKey = 'island:event:roserank:1';
    	$loc = Hapyfish2_Cache_LocalCache::getInstance();
    	$minLine = $loc->get($locKey);
    	echo $minLine;
    	echo '<br />';
    	//$loc->delete($locKey);
		$eventFeed->delete($mkey1);
		$eventRank->delete($mkey2);

    	if ($uid) {
    		$mkey = 'i:u:eventsendrose:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        $arySendInfo = $cache->get($mkey);
	        $cache->delete($mkey);
	        echo Zend_Json::encode($arySendInfo);
			echo '<br />';
    	}

        exit;
	}

	public function loadsendroseAction()
	{
		$uid = $this->_request->getParam('uid');
		$mkey = 'i:u:eventsendrose:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $arySendInfo = $cache->get($mkey);
        echo Zend_Json::encode($arySendInfo);
        $cache->delete($mkey);
		echo 'del ok<br />';
		exit;
	}

	public function addroseAction()
	{
		$uid = $this->_request->getParam('uid');
		$num = $this->_request->getParam('num');
		Hapyfish2_Island_Event_Bll_Valentine::incRose($uid, $num, 1);
		echo $uid.'+'.$num.' roses ok<br />';
		exit;
	}
	public function clearconsumeexchangeAction(){
	    $key = 'consumeandgive:';
	    try{
		$cache = Hapyfish2_Island_Event_Bll_ConsumeExchange::getBasicMC();
		$cache ->delete($key);
		echo 'OK';
		exit;
	    } catch (Exception $e) {
				info_log($e->getMessage(), 'Event_ConsumeExchange');
			}
	}
    public function cleargetstuffAction(){
        $uid = $this->_request->getParam('uid');
    	$key = 'CollectStuff:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->delete($key);
    	$dal = Hapyfish2_Island_Event_Dal_CollectStuff::getDefaultInstance();
    	$dal->deletegetstuff($uid);


    }
    public function getroserankAction(){
//    	header("Content-type:application/vnd.ms-excel");
//        header("Content-Disposition:filename=rose.csv");
          header("Content-type:text/plain");
          header("Content-Disposition:filename=rose.txt");
        $mkey2 = Hapyfish2_Island_Event_Bll_Valentine::CACHE_KEY_RANK;
		$eventRank = Hapyfish2_Cache_Factory::getEventRank();
    	$aryList2 = $eventRank->get($mkey2);
    	$listVo2 = array();
    	echo "name,num,id\n";
    	if ($aryList2) {
	    	foreach ($aryList2 as $data) {
				$listVo2[] = array('userName'=>$data[0], 'roseNum'=>$data[1]);
				echo $data[0].",".$data[1].",".$data[2]."\n";
	    	}
    	}

    }
}