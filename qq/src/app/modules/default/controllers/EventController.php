<?php

class EventController extends Zend_Controller_Action
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
        $data = array('uid' => $info['uid'], 'openid' => $info['openid'], 'openkey' => $info['openkey']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

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

    /*
     * get next time gift,获取下一次时间性礼物的奖品信息
     *
     */
    public function getnexttimegiftAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_Timegift::getNextTimeGift($this->uid);
    	$this->echoResult($result);
    }

	public function setupgifttimeAction()
    {
    	try {
	    	Hapyfish2_Island_Event_Bll_Timegift::setup($this->uid);

			echo 'Ok';
			exit;
    	} catch (Exception $e){
			echo 'false';
			exit;
    	}
    }

	//梦想花园用户登录奖励
    public function recivedreamgardenawardAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_DreamGardenUserAward::receive($this->uid);
    	$this->echoResult($result);
    }

	public function resetreamgardenawardAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_DreamGardenUserAward::reset($this->uid);
    	$this->echoResult($result);
    }

    public function getactivedayAction()
    {
    	$uid = $this->uid;
    	$result = array('status' => -1, 'content' => 'serverWord_110');
    	$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
    	if (!$loginInfo) {
			$this->echoResult($result);
		}

		$activeLoginDays = ($loginInfo['active_login_count'] - 1) < 0 ? 0 : ($loginInfo['active_login_count'] - 1);
		$this->echoResult(array('day' => $activeLoginDays, 'result' => array('status' => 1)));
    }

    public function active5dayAction()
    {
    	//$result = array('status' => -1, 'content' => '新内容即将上线，敬请期待...');//
		//$this->echoResult(array('result' => $result));

    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
    	if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}

    	$result = array('status' => -1, 'content' => 'serverWord_110');
    	$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
        if (!$loginInfo) {
			$this->echoResult(array('result' => $result));
		}

		$activeLoginDays = ($loginInfo['active_login_count'] - 1) < 0 ? 0 : ($loginInfo['active_login_count'] - 1);
		if ($activeLoginDays < 5) {
			$this->echoResult(array('result' => $result));
		}

    	$isGained = Hapyfish2_Island_Event_Bll_Active5DayVer2::isGained($uid);
		if (!$isGained) {
			$result = Hapyfish2_Island_Event_Bll_Active5DayVer2::gain($uid);
		} else {
			$result['content'] = 'serverWord_151';
		}

        //release lock
        $lock->unlock($key);

		$this->echoResult(array('result' => $result));
    }

    public function getinviteflowstateAction()
    {
    	$uid = $this->uid;
    	$data = Hapyfish2_Island_Event_Bll_InviteFlow::getState($uid);
    	$this->echoResult($data);
    }

    public function inviteawardAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = array('result' => array('status' => -1, 'content' => 'serverWord_110'));

		$data = Hapyfish2_Island_Event_Bll_InviteFlow::getState($uid);
		$step = $data['step'];
		$friendCount = count($data['friendsList']);

		if ($step == 1) {
			if ($friendCount < 4) {
				 $this->echoResult($result);
			}
		} else if ($step == 2) {
			if ($friendCount < 3) {
				 $this->echoResult($result);
			}
		} else  if ($step == 3) {
			if ($friendCount < 2) {
				 $this->echoResult($result);
			}
		} else  if ($step == 4) {
			if ($friendCount < 1) {
				 $this->echoResult($result);
			}
		} else {
			$this->echoResult($result);
		}

		$result = Hapyfish2_Island_Event_Bll_InviteFlow::gain($uid, $step);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    //rabbit cdkey
	public function cdkeyrabbitAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

    	$cdkey = $this->_request->getParam('cdkey');
    	$isValid = Hapyfish2_Island_Event_Bll_CdKeyII::isRegularCdkey($cdkey);
    	if (!$isValid) {
    		$resultVo = array('status' => -3);
			$this->echoResult(array('result' => $resultVo));
    	}

    	//cdkey exchange go
		$rst = Hapyfish2_Island_Event_Bll_CdKeyII::validCdKey($uid, $cdkey);
		if ($rst > 0) {
			$resultVo = array('status' => 1);
			if (1 == $rst) {
				$resultVo['coinChange'] = 5000;
			}
			else if (2 == $rst) {
				$resultVo['coinChange'] = 8000;
			}
			else if (3 == $rst) {
				$resultVo['coinChange'] = 10000;
			}
			else {
				$resultVo['coinChange'] = 20000;
			}
			$result = array('result' => $resultVo, 'level' => $rst);
		}
		else {
			$resultVo = array('status' => $rst);
			if (-1 == $rst) {
				$resultVo['content'] = 'serverWord_110';
			}
			$result = array('result' => $resultVo);
		}
		//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//农历新年：请求鞭炮数量和红包数量
	public function loadnewyearitemAction()
    {
    	$uid = $this->uid;
    	//get item count
		$result = Hapyfish2_Island_Event_Bll_Newyear::getUserNewyear($uid);
		$this->echoResult($result);
    }

	//农历新年：新年兑换礼品列表
    public function loadnewyearawardlistAction()
    {
    	$mkey = 'event_newyear_exchange_list';
		$eventFeed = Hapyfish2_Cache_Factory::getEventFeed();
    	$aryList = $eventFeed->get($mkey);
    	$listVo = array();
    	if ($aryList) {
	    	foreach ($aryList as $data) {
				$listVo[] = array('userName'=>$data[0], 'itemName'=>$data[1], 'time'=>$data[2]);
	    	}
    	}
    	$resultVo = array('status' => 1);
		$this->echoResult(array('result' => $resultVo, 'OpenBrideAwardsUserVOList' => $listVo));
    }

	//农历新年：打开红包
    public function opennewyearredpaperAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

    	$result = Hapyfish2_Island_Event_Bll_Newyear::openRedPaper($uid);
    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//农历新年：新年兑换七彩阁是否足够
    public function checknewyeargainedAction()
    {
	    $uid = $this->uid;
	    if (Hapyfish2_Island_Event_Bll_Newyear::isGainTreasure($uid)) {
	    	$resultVo = array('status' => -1, 'content' => 'serverWord_151');
			$this->echoResult(array('result' => $resultVo));
	    }
		$aryStatus = Hapyfish2_Island_Event_Bll_Newyear::getExchangeTreasureStatus($uid);
		$canGet = 0;
		if ($aryStatus['71521'] > 0
    		&& ($aryStatus['72431']>0 || $aryStatus['75231']>0)
    		&& ($aryStatus['72531']>0 || $aryStatus['75331']>0)
    		&& ($aryStatus['72631']>0 || $aryStatus['75131']>0) ) {
			$canGet = 1;
    	}

		$result = array('result'=>array('status'=>1), 'enough'=>$canGet, 'JiaoZi'=>$aryStatus['71521'],
						'Shou'=>$aryStatus['72631'],'Lu'=>$aryStatus['72531'],'Fu'=>$aryStatus['72431'],
						'GoldFu' => $aryStatus['75231'], 'GoldLu' => $aryStatus['75331'], 'GoldShou' => $aryStatus['75131']);
		$this->echoResult($result);
    }

    //农历新年：新年兑换礼品
    public function exchangenewyearawardAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$exchangeType = (int)$this->_request->getParam('requestType');//1,2,3,4
		if ($exchangeType>=1 && $exchangeType <=3) {
			$result = Hapyfish2_Island_Event_Bll_Newyear::exchangeCracker($uid, $exchangeType);
		}
		else {
			$result = Hapyfish2_Island_Event_Bll_Newyear::gainTreasure($uid);
		}

    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//取得当天财神信息
	public function getwealthgodAction()
    {
    	$uid = $this->uid;
    	$mkey = 'i:u:wealthgoddly:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $gainDate = $cache->get($mkey);
		$nowDate = date('Ymd');
    	//has gained today's gift
		$todayHasGet = 0;
		if ($gainDate && $gainDate == $nowDate) {
			$todayHasGet = 1;
		}

		$maxBid = 0;
    	$plants = Hapyfish2_Island_HFC_Plant::getAll($uid);
    	if ($plants) {
    		foreach($plants as $item) {
	    		if ($item['cid'] == 70431 || $item['cid'] == 70531 || $item['cid'] == 70631) {
	    			$maxBid = $item['cid'] > $maxBid ? $item['cid'] : $maxBid;
	    		}
	    	}
    	}

    	//max level
		$maxLevel = 0;
    	if (70431 == $maxBid) {
			$maxLevel = 3;
		}
		else if (70531 == $maxBid) {
			$maxLevel = 4;
		}
		else if (70631 == $maxBid) {
			$maxLevel = 5;
		}

		$resultVo = array('status' =>1);
		$result['result'] = $resultVo;
		$result['todayHasGet'] = $todayHasGet;
		$result['maxLevel'] = $maxLevel;
		$this->echoResult($result);
    }

	//农历新年：财神送礼
    public function wealthgodAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Event_Bll_Newyear::openWealthGod($uid);
    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//农历新年：商城特卖
	public function salemallAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
//        if($uid!=4030638){
//    		$resultVo = array('status' => -1, 'content' => '商品更新中');
//			$this->echoResult(array('result' => $resultVo));
//        }
	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$packId = (int)$this->_request->getParam('packId');

		/*//2011-01-21 1295539200 2011-02-10 1297267200
		//金色福星馆 109  金色寿星馆 209  金禄星礼包309  财神庙礼包 409*/

		//加速卡包1 509,加速卡包2 609,加速卡包3 709,加速卡包4 809 ,加速卡包5 909,加速卡包6 1009,加时卡包3 1109,大卡包1   1209
		//2011-02-11 1297353600  2011-03-15  1300118400

		//2011-03-08 1299513600  2012-03-08  1331136000
		//兔兔大礼包  1309  船只加速3 1409  设施加速卡2 1509  码头保安卡 1609  丘比特天+爱之海  1709
		$arySaleInfo = array('start'=>'1299513600','end'=>'1331136000', 'price_type'=>2, 'price'=>100);
		$items = array();

		//足球风暴
		//阿根廷足球馆 	38432	50	1 巴西足球馆 38732 50 1 德国足球馆 39032 50 1 世界杯-岛 43311 0 1
		if (1 == $packId) {
			$arySaleInfo['id'] = 1309;
			$arySaleInfo['name'] = '兔兔大礼包';
			$arySaleInfo['price'] = 188;
			$items[] = array('item_id' => 78931, 'item_num' => 1);
			$items[] = array('item_id' => 79031, 'item_num' => 1);
			$items[] = array('item_id' => 79131, 'item_num' => 1);
			$items[] = array('item_id' => 54321, 'item_num' => 1);
		}
		//加速卡包
		//加速III
		else if (3 == $packId) {
			$arySaleInfo['id'] = 1409;
			$arySaleInfo['name'] = '船只加速卡3';
			$arySaleInfo['price'] = 80;
			$items[] = array('item_id' => 26441, 'item_num' => 20);
		}
    	//加速卡包3
		//加速II
		else if (4 == $packId) {
			$arySaleInfo['id'] = 1509;
			$arySaleInfo['name'] = '设施加时卡2';
			$arySaleInfo['price'] = 80;
			$items[] = array('item_id' => 26641, 'item_num' => 20);
		}
    	//加时卡包
		//加时卡
		else if (5 == $packId) {
			$arySaleInfo['id'] = 1609;
			$arySaleInfo['name'] = '码头保安卡';
			$arySaleInfo['price'] = 40;
			$items[] = array('item_id' => 27141, 'item_num' => 20);
		}
    	//保安卡礼包
		//码头保安卡
		else if (2 == $packId) {
			$arySaleInfo['id'] = 1709;
			$arySaleInfo['name'] = '丘比特天+爱之海';
			$arySaleInfo['price_type'] = 1;
			$arySaleInfo['price'] = 1000;
			$items[] = array('item_id' => 23312, 'item_num' => 1);
			$items[] = array('item_id' => 22413, 'item_num' => 1);
		}
    	//加速卡包6
		//加速III	26441	100	200
	   /*
		else if (6 == $packId) {
			$arySaleInfo['id'] = 1009;
			$arySaleInfo['name'] = '加速卡包6';
			$arySaleInfo['price'] = 200;
			$items[] = array('item_id' => 26441, 'item_num' => 100);
		}
    	//加时卡包3
		//加时卡I	26541	10	加时卡II	26641	10	60
		else if (7 == $packId) {
			$arySaleInfo['id'] = 1109;
			$arySaleInfo['name'] = '加时卡包3';
			$arySaleInfo['price'] = 60;
			$items[] = array('item_id' => 26541, 'item_num' => 10);
			$items[] = array('item_id' => 26641, 'item_num' => 10);
		}
		//大卡包1
		//加速II		26341	10	加速III	26441	10	保安卡	27141	10	加时卡I	26541	10	加时卡II	26641	10	100
		else {
			$arySaleInfo['id'] = 1209;
			$arySaleInfo['name'] = '大卡包1';
			$arySaleInfo['price'] = 100;
			$items[] = array('item_id' => 26341, 'item_num' => 10);
			$items[] = array('item_id' => 26441, 'item_num' => 10);
			$items[] = array('item_id' => 27141, 'item_num' => 10);
			$items[] = array('item_id' => 26541, 'item_num' => 10);
			$items[] = array('item_id' => 26641, 'item_num' => 10);
		}
        */
		$arySaleInfo['item'] = $items;
		$bllMall = new Hapyfish2_Island_Event_Bll_SaleMall($arySaleInfo);
		if (1 == $arySaleInfo['price_type']) {
			$result = $bllMall->coinSale($uid);
		}
		else {
			$result = $bllMall->goldSale($uid);
		}
		if ($result['result']['status'] < 0) {
			$result['result']['status'] = -1;
		}

    	//release lock
        $lock->unlock($key);

    	//pack sell logs
		if (1 == $result['result']['status']) {
			info_log($arySaleInfo['name'].','.$uid, 'packsale_'.date('Ymd'));
		}

        $this->echoResult($result);
    }

    //情人节：取数据
    public function loadvalentineAction()
    {
    	$uid = $this->uid;
    	$eventEndTime = 1313683199;//2011-03-15
    	$now = time();
    	//get item count
		$result = Hapyfish2_Island_Event_Bll_Valentine::getUserValentine($uid);
		$result['daysLeftNum'] = ($eventEndTime - $now) > 0 ? ($eventEndTime - $now) : 0;
		$this->echoResult($result);
    }


	//情人节：兑换
    public function exchangevalentineAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$exchangeType = (int)$this->_request->getParam('requestType');//1,2,3,4,5
		$result = Hapyfish2_Island_Event_Bll_Valentine::exchangeRose($uid, $exchangeType);

    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    //情人节：读取花
    public function loadvalentineroseAction()
    {
    	$resultVo = array('status' => 1);
    	$uid = $this->uid;
    	$today = date('Ymd');
		$mkey = 'i:u:eventsendrose:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $arySendInfo = $cache->get($mkey);
        $remainCnt = 10;
        if ( $arySendInfo && $arySendInfo['dt'] && $arySendInfo['dt']==$today && $arySendInfo['ids'] ) {
			$remainCnt = (10 - count($arySendInfo['ids'])) > 0 ? (10 - count($arySendInfo['ids'])) : 0;
        }
        $this->echoResult(array('result' => $resultVo, 'todayLeftSendTimes' => $remainCnt));
    }

	//情人节：送花
    public function sendvalentineroseAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$fids = $this->_request->getParam('requestType');
		$result = Hapyfish2_Island_Event_Bll_Valentine::sendRose($uid, $fids);

    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//情人节：索花
    public function askvalentineroseAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$fids = $this->_request->getParam('requestType');
		$result = Hapyfish2_Island_Event_Bll_Valentine::begRose($uid, $fids);

    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//情人节：买花
    public function buyvalentineroseAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$num = (int)$this->_request->getParam('requestType');
		$result = Hapyfish2_Island_Event_Bll_Valentine::buyRose($uid, $num);

    	//release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	//情人节：排名列表  /情人节：兑换列表
    public function loadvalentinerankAction()
    {
    	$mkey1 = Hapyfish2_Island_Event_Bll_Valentine::CACHE_KEY_EXCHANGE;
		$eventFeed = Hapyfish2_Cache_Factory::getEventFeed();
    	$aryList1 = $eventFeed->get($mkey1);
    	$listVo1 = array();
    	if ($aryList1) {
	    	foreach ($aryList1 as $data) {
				$listVo1[] = array('userName'=>$data[0], 'itemName'=>$data[1], 'time'=>$data[2]);
	    	}
    	}

    	$mkey2 = Hapyfish2_Island_Event_Bll_Valentine::CACHE_KEY_RANK;
		$eventRank = Hapyfish2_Cache_Factory::getEventRank();
    	$aryList2 = $eventRank->get($mkey2);
    	$listVo2 = array();
    	if ($aryList2) {
	    	foreach ($aryList2 as $data) {
				$listVo2[] = array('userName'=>$data[0], 'roseNum'=>$data[1]);
	    	}
    	}
    	$resultVo = array('status' => 1);
		$this->echoResult(array('result' => $resultVo, 'getGiftRankListVo' => $listVo1, 'getRoseRankListVo' => $listVo2));
    }
	    public function getconsumeinfoAction(){
        $uid = $this->uid;
        $data = array();
        $consumeEvent = array();
        $time = time();
		$consumeEvent = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeEvent();
		$data = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeStep($uid,$consumeEvent['start'],$consumeEvent['end']);
		$consumeGold = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeGold($uid,$consumeEvent['start'],$consumeEvent['end']);
		$result['result']['status'] = 1;
		if($consumeEvent){
		    $result['startTime'] = date('Y-m-d-H',$consumeEvent['start']);
		    $result['endTime'] = date('Y-m-d-H',$consumeEvent['end']);
		    $livtime = $consumeEvent['end'] - $time;
		    $day = floor($livtime/86400);
		    $hour = floor(($livtime - $day*86400)/3600);
		    $min = floor(($livtime - $day*86400 - $hour*3600)/60);
		    $second = $livtime-$day*86400 - $hour*3600-$min*60;
		    $result['leavingTime'] = '0-0-'.$day.'-'.$hour.'-'.$min.'-'.$second;
		    if($consumeEvent['data']){
		        if(in_array(1,$data['data'])){
		            $consumeEvent['data'][0]['haveGet'] = true;
		        } else {
		            $consumeEvent['data'][0]['haveGet'] = false;
		        }
		        if(in_array(2,$data['data'])){
		            $consumeEvent['data'][1]['haveGet'] = true;
		        } else {
		            $consumeEvent['data'][1]['haveGet'] = false;
		        }
		         if(in_array(3,$data['data'])){
		            $consumeEvent['data'][2]['haveGet'] = true;
		        } else {
		            $consumeEvent['data'][2]['haveGet'] = false;
		        }
		       $result['data'] = $consumeEvent['data'];
		      $cid1 = explode('*', $result['data'][0]['cid']);
		      $result['data'][0]['cid'] = $cid1[0];
		      $cid2 = explode('*', $result['data'][1]['cid']);
		      $result['data'][1]['cid'] = $cid2[0];
		      $cid3 = explode('*', $result['data'][2]['cid']);
		      $result['data'][2]['cid'] = $cid3[0];
		    }
		}
		if(!$consumeGold){
		    $consumeGold =0;
		}
		$result['totalGold']=$consumeGold;
        $this->echoResult($result);
    }
    public function consumeexchangeAction(){
        $step = $this->_request->getParam('id');
        $uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		$result = array('result' => array('status' => -1, 'content' => 'serverWord_110'));
        $data = array();
        $consumeEvent = array();
        $time = time();
		$consumeEvent = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeEvent();
		$data = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeStep($uid,$consumeEvent['start'],$consumeEvent['end']);
		$consumeGold = Hapyfish2_Island_Event_Bll_ConsumeExchange::getConsumeGold($uid,$consumeEvent['start'],$consumeEvent['end']);
		if ($step == 1) {
			if ($time < $consumeEvent['start'] || $time>$consumeEvent['end'] || in_array($step,$data['data']) || $consumeGold < $consumeEvent['data'][0]['gold']) {
				 $this->echoResult($result);
			}
		} else if ($step == 2) {
			if ($time < $consumeEvent['start'] || $time>$consumeEvent['end'] || in_array($step,$data['data']) || $consumeGold < $consumeEvent['data'][1]['gold']) {
				 $this->echoResult($result);
			}
		} else  if ($step == 3) {
			if ($time < $consumeEvent['start'] || $time>$consumeEvent['end'] || in_array($step,$data['data']) || $consumeGold < $consumeEvent['data'][2]['gold']) {
				 $this->echoResult($result);
			}
		} else {
			$this->echoResult($result);
		}

		$result = Hapyfish2_Island_Event_Bll_ConsumeExchange::Exchange($uid, $step);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

	/**
	 *
	 * 时间性礼物
	 */
	public function receivetimegiftAction()
    {
    	//$result = array("100","200");
    	$result = Hapyfish2_Island_Event_Bll_Timegift::receive($this->uid);

    	$this->echoResult($result);
    }

    public function getgifttimeAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_Timegift::gettime($this->uid);
    	$this->echoResult($result);
    }

    public function getcollectstuffAction(){
        $uid = $this->uid;
        $collec = Hapyfish2_Island_Event_Bll_CollectStuff::getCollectStuff($uid);
        $haveget = Hapyfish2_Island_Event_Bll_CollectStuff::haveGetgift($uid);
        if($haveget == 1){
            $collec['haveGet'] = true;
        }else{
            $collec['haveGet'] = false;
        }
        $this->echoResult($collec);
    }
    public function buycollectstuffAction(){
        $uid = $this->uid;
        $id = $this->_request->getParam('id');
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		$goods = array();
        $time['start'] = intval(1299686400);
        $time['end'] = intval(1300636799);
		if (1 == $id) {
			$goods = array('id' => 76131,'name'=>'爱情树','price'=>82);
		}
		else if (3 == $id) {
		   $goods = array('id' => 73131,'name'=>'亲嘴小女孩','price'=>82);
		}
		else if (4 == $id) {
			 $goods = array('id' => 73231,'name'=>'亲嘴小男孩','price'=>82);
		}
		$result = Hapyfish2_Island_Event_Bll_CollectStuff::goldSale($uid,$goods,$time);
		if ($result['result']['status'] < 0) {
			$result['result']['status'] = -1;
		}
		$result['id'] = $id;
    	//release lock
        $lock->unlock($key);
    if (1 == $result['result']['status']) {
			info_log($goods['name'].','.$uid, 'packsale_'.date('Ymd'));
		}
        $this->echoResult($result);
    }
     public function getstuffAction(){
        $uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		$haveget = Hapyfish2_Island_Event_Bll_CollectStuff::haveGetgift($uid);
		if($haveget == 1){
		    $result = array('result' => array('status' => -1,'content' => '对不起您已兑换过礼物'));
		}else{
			$time['start']=1299686400;
            $time['end']=1300636799;
		    $result = Hapyfish2_Island_Event_Bll_CollectStuff::Exchange($uid, $time);
		}
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);

     }
     public function getstarfishexternalmallAction(){
        $uid = $this->uid;
        $result['result'] = array('status' => 1,'content' => '');
        $start = 1300903200;
        $result['data'] = Hapyfish2_Island_Event_Bll_StarfishSale::getSaleList();
        $result['haveInvitedFriendNum'] = Hapyfish2_Island_Event_Bll_StarfishSale::getInviteCount($uid, $start);
        $result['userStarFish'] = Hapyfish2_Island_HFC_User::getUserStarFish($uid);
        $this->echoResult($result);
     }
     public function starfishexchangeAction(){
         $cid = $this->_request->getParam('cid');
         $uid = $this->uid;
        $key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		$result = Hapyfish2_Island_Event_Bll_StarfishSale::Exchange($uid,$cid);
        $lock->unlock($key);
        $this->echoResult($result);
     }

	/**
	 * join userfullsereen status
	 */
	public function fullscreenAction()
	{
		$uid = $this->uid;

		$key = 'i:u:fullScreen:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$status = $cache->get($key);

		if(!$status) {
			$dalFullScreen = Hapyfish2_Island_Dal_FullScreen::getDefaultInstance();
			$status = $dalFullScreen->getStatus($uid);

			if(!$status) {
				$info = array('uid' => $uid, 'status' => 1);
				$dalFullScreen->addStatus($info);
				$cache->set($key, 1);
			}
		}
		exit;
	}

	// 掉宝箱系统，获取季度列表
	public function getbottlelistAction()
	{
		$uid = $this->uid;
		$result = array('result'=>array('status'=>-1, 'goldChange'=>0), 'list'=>array());
		$key = 'bottle:list';

		$list = Hapyfish2_Island_Cache_Hash::get($key);
		$list = unserialize($list);

		$topcids = array('86732','92232','92232','98632','102332','99631', '115232');




		if ($list) {
			$temp = array();
			foreach ($list as $key => $val) {
				if ($val['online']) {
					unset($val['online']);
					unset($val['tips']);
					$val['qid'] = (int)$val['qid'];
					$temp[] = array_merge($val, array('id'=>(string)$key, 'cid'=>(int)$topcids[$key % count($topcids)]));

				}
			}
			$result['result']['status']=1;
			$result['list'] = $temp;
		}

		$this->echoResult($result);
	}

	// 掉宝箱系统，获取单一季度奖励列表
	public function getbottleinfoAction()
	{
		$uid = $this->uid;
		$result = array('result'=>array('status'=>-1, 'goldChange'=>0), 'list'=>array());
		$idx = $this->_request->getParam('idx');

		if ($idx || $idx == "0") {

			$tf = false;
			$key = 'bottle:list';
			$hash = Hapyfish2_Island_Cache_Hash::get($key);
			$hash = unserialize($hash);

			if ($hash[$idx] && $hash[$idx]['online']) {
				$tf = true;
			}

			if ($tf) {

				$temp = array();
				$list = Hapyfish2_Island_Cache_Bottle::getAllByBottleId($idx);
				foreach ($list['list'] as $key => $val) {
					$tips = ($val['btl_tips'] ? $val['btl_tips'] : '');
					switch ($val['type']) {
						case 'COIN' :
							$temp[] = array('name'=>$val['btl_name'], 'coin'=>(int)$val['coin'], 'tips'=>$tips);
							break;
						case 'GOLD' :
							$temp[] = array('name'=>$val['btl_name'], 'gem'=>(int)$val['gold'], 'tips'=>$tips);
							break;
						case 'STARFISH' :
							$temp[] = array('name'=>$val['btl_name'], 'starfish'=>(int)$val['starfish'], 'tips'=>$tips);
							break;
						case 'PLANT' :
						case 'BUILDING' :
						case 'CARD' :
							$temp[] = array('name'=>$val['btl_name'], 'itemId'=>$val['item_id'], 'itemNum'=>$val['num'], 'tips'=>$tips);
							break;
					}
				}

				$result['result']['status']=1;
				$result['list'] = $temp;

				// 今天免费抽奖了没
				$freeNum = Hapyfish2_Island_Cache_Counter::getBottleTodayTF($uid);
				// 获得玩家卡牌
				$userCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);

				$result['freeNum']= ($freeNum ? 0 : 1);	// 还有免费的次数
				$result['keyNum'] = ($userCard['86241'] ? $userCard['86241']['count'] : 0);	// 剩余钥匙个数

				$result['price1'] = 4;	// 开一次用的岛钻石
				$result['price10'] = 35;	// 开10次用的岛钻石
				$result['cheapPrice1'] = 4;	// 开一次用的岛钻石
				$result['cheapPrice10']= 35;	// 开10次用的岛钻石
				$result['canUseGold'] = 1;	// 是否可以使用岛钻石 1,或0

			}

		}

		$this->echoResult($result);
	}

	// 掉宝箱系统，领取宝箱
	public function bottlereceiveAction()
	{
		$idx = $this->_request->getParam('idx');
		$type = $this->_request->getParam('type');
		$num = abs($this->_request->getParam('count',1));
		$uid = $this->uid;

		$result = Hapyfish2_Island_Bll_Bottle::click($idx, $type, $uid, $num);

//		$resultVo = array('status' => -1, 'content' => 'serverWord_110');
//		$result = array('result' => $resultVo);

		$this->echoResult($result);
	}

	// 掉宝箱系统，获取玩家奖励列表
	public function bottleuserlistAction()
	{
		$uid = $this->uid;
		$result = array('result'=>array('status'=>-1, 'goldChange'=>0), 'list'=>array());
		$list = Hapyfish2_Island_Cache_BottleQueue::getall($uid);
		if ($list) {
			$result['result']['status'] = 1;
			$result['list'] = $list;
		}

		$this->echoResult($result);
	}
	public function getfriendlistAction()
	{
		$uid = $this->uid;
		if (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE) {
			$list = Hapyfish2_Island_Bll_SearchFriend::getQzoneSearchFriend();
		} else {
			$list = Hapyfish2_Island_Bll_SearchFriend::getSearchFriend();
		}
		$logger = Hapyfish2_Util_Log::getInstance();
        $logger->report('10001', array($uid));
		$this->echoResult($list);
	}
	public function sendremindAction()
	{
		$uid = $this->uid;
		$fid = $this->_request->getParam('uid');
		$user = Hapyfish2_Platform_Bll_Factory::getUser($uid);
		$content = $user['nickname'].'加你为好友了，快去你的主页看看吧!';
		Hapyfish2_Island_Bll_Remind::addRemind($this->uid, $fid, $content, 0);
		exit;
	}

    /**
	 *  get IPAD collect
	 */
	public function luckydrawAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_LuckyDraw::luckyDraw($uid);

		$this->echoResult($result);
	}

	/**
	 * get Ipad collent gift
	 */
	public function luckydrawgiftAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_LuckyDraw::getLuckyDrawGift($uid);

		$this->echoResult($result);
	}

	/**
	 * get ipad CD key
	 */
	public function getcdkeyAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_LuckyDraw::getCDKey($uid);

		$this->echoResult($result);
	}

	public function teambuyAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_TeamBuy::teamBuy($uid);

		$this->echoResult($result);
	}

	public function jointeambuyAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_TeamBuy::joinTeamBuy($uid);

		$this->echoResult($result);
	}

	public function buygoodsAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_TeamBuy::buyGoods($uid);

		$this->echoResult($result);
	}
	public function getrankAction()
	{
		$uid = $this->uid;
		$result = Hapyfish2_Island_Bll_Rank::getRank($uid);
		 $this->echoResult($result);
	}

 	/**
     * add user strom coin
     */
    public function addstromcoinAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Event_Bll_Strom::addCoin($uid);
    	$type = 'STROM_FEED';
    	$result['result']['feed'] = Hapyfish2_Island_Bll_Activity::send($type, $uid);
    	$this->echoResult($result);
    }

    /**
     * get user strom status
     */
    public function getflashstomstatusAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Event_Bll_Strom::getStrom($uid);

    	$this->echoResult($result);
    }

    /**
     * send strom feedn
     */

	/**
	 * 收集任务
	 */
	public function getgiftAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_Hash::getGift($uid);

		$this->echoResult($result);
	}

	public function collectiontaskAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_Hash::collectionTask($uid);

		$this->echoResult($result);
	}

	public function gettrumpetAction()
	{
		$uid = $this->uid;

		$msg = Hapyfish2_Island_Bll_Trumpet::getTrumpetMsg($uid);
		if(!empty($msg)){
			$result['resultVo']['status'] = 1;
			$result['broadcastWords'] [] =  $msg;
		} else {
			$result['resultVo']['status'] = -1;
		}
		$this->echoResult($result);
	}

	public function sendfeedgiftAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		$sfstause = '';
		$result['status'] = 1;
		$gkey = 'i:u:sf:g:'.$uid;
		$ukey = 'i:u:sf:u:'.$uid;
		$scache = Hapyfish2_Cache_Factory::getMC($uid);
		$gfstause = $scache->get($gkey);
		$ustatue = $scache->get($ukey);
		if($gfstause != $type && $ustatue!= $type){
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}
		$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
	    //get lock
		$ok = $lock->lock($key);
    	if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result'=>$result));
		}
		$com = new Hapyfish2_Island_Bll_Compensation();
		$com->setUid($uid);
		switch ($type) {
			case 'changeHelp' :
				$com->setStarFish(20);
				$skey = $gkey;
				break;
			case 'unlockShip' :
				$com->setCoin(10000);
				$skey = $ukey;
				$result['coinChange'] = 10000;
				break;
		}
		$ok = $com->send('获得分享奖励：');
		if($ok){
			$scache->delete($skey);
		}
		$lock->unlock($key);
		$resultVo = array('result'=>$result);
		$this->echoResult($resultVo);
	}

	public function sendtaskfeedAction()
	{
		$uid = $this->uid;
		$taskId = $this->_request->getParam('taskId');
		$taskInfo = Hapyfish2_Island_Cache_BasicInfo::getAchievementTaskInfo($taskId);
		$titleList = Hapyfish2_Island_Cache_BasicInfo::getTitleList();
		$data['title'] = $titleList[$taskInfo['title']];
		$feed = Hapyfish2_Island_Bll_Activity::send('USER_OBTAIN_TITLE', $uid, $data);
		$result['result']['feed'] = $feed;
		$result['result']['status'] = 1;
		$this->echoResult($result);
	}

	public function sendqueyuAction()
	{
		$uid = $this->uid;
		$result['result']['status'] = 1;
		$key1 = 'i:u:e:q:y:'.$uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key1);
        if($data == 1){
			$ok = Hapyfish2_Island_Event_Bll_Valentine::incRose($uid, 7, 1);
			if($ok){
				  $minifeed = array('uid' => $uid,
		                          'template_id' => 0,
		                          'actor' => $uid,
		                          'target' => $uid,
		                          'title' => array('title' => '恭喜你获得分享奖励：7根鹊羽'),
		                          'type' => 3,
		                          'create_time' => time());
		         Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
			}
			$data = $cache->delete($key1);
        }else{
        	$result['result']['status'] = -1;
        }
        $this->echoResult($result);
	}

   public function exchangeqixiAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		$result = Hapyfish2_Island_Event_Bll_Qixi::getGift($uid);
		$lock->unlock($key);
        $this->echoResult($result);
    }
    public function getqixigiftAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		$result = Hapyfish2_Island_Event_Bll_Qixi::xmasFair($uid);
		$lock->unlock($key);
        $this->echoResult($result);
    }
	public function exchangefragmentAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		$result = Hapyfish2_Island_Bll_Fragments::exchangeFragment($uid, $type);
		$this->echoResult($result);
	}

    public function initguoqingAction()
    {
    	$uid = $this->uid;
    	$result = Hapyfish2_Island_Event_Bll_Midautumn::getUserMidautumn($uid);
    	$this->echoResult($result);
    }

    public function passexchangeAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$key = 'ev:invite:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}
		$result = Hapyfish2_Island_Event_Bll_Midautumn::Exchange($uid, $id);
    	$lock->unlock($key);

    	$this->echoResult($result);
    }

    public function buypassAction()
    {
    	$uid = $this->uid;
    	$num = $this->_request->getParam('num');
    	$key = 'ev:invite:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}
		$result = Hapyfish2_Island_Event_Bll_Midautumn::buyPass($uid, $num);
    	$lock->unlock($key);

    	$this->echoResult($result);
    }

    public function demandpassAction()
    {
    	$uid = $this->uid;
    	$uids = $this->_request->getParam('list');
    	$result = Hapyfish2_Island_Event_Bll_Midautumn::begPass($uid, $uids);
    	$this->echoResult($result);
    }

    public function donatepassAction()
    {
    	$uid = $this->uid;
    	$type = $this->_request->getParam('type');
    	$num = $this->_request->getParam('num');
    	$uids = $this->_request->getParam('list');

    	$key = 'ev:invite:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}
		if($type == 1){
			$result = Hapyfish2_Island_Event_Bll_Midautumn::sendPass($uid, $uids);
		}else{
			$result = Hapyfish2_Island_Event_Bll_Midautumn::buyFriendPass($uid, $num, $uids);
		}
    	$lock->unlock($key);

    	$this->echoResult($result);
    }

	//限时抢购-init
	public function panicbuyinitAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_PanicBuy::panicBuyInit($uid);
		$this->echoResult($result);
	}

	//限时抢购-领取
	public function getpanicbuygiftAction()
	{
		$uid = $this->uid;

        $key = 'evlock:panic:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Event_Bll_PanicBuy::getPanicBuyGift($uid);
		$lock->unlock($key);

		$this->echoResult($result);
	}

	//限时抢购宝箱-init
	public function panicbuyboxAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_PanicBuy::panicBuyBox($uid);
		$this->echoResult($result);
	}

	//限时抢购宝箱-领取
	public function getpanicbuyboxAction()
	{
		$uid = $this->uid;
		$idx = $this->_request->getParam('id');

        $key = 'evlock:panic:box:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Event_Bll_PanicBuy::getPanicBuyBox($uid, $idx);
		$lock->unlock($key);

		$this->echoResult($result);
	}

	//万圣节--初始化
    public function halloweeninitAction()
    {
    	$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_HallWitches::halloWeenInit($uid);
		$this->echoResult($result);
    }

    //万圣节--刷新选牌状态
    public function refrushcardchanceAction()
    {
    	$uid = $this->uid;

    	$key = 'ev:hallRef:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key, 2);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}

		$result = Hapyfish2_Island_Event_Bll_HallWitches::refrushCardChance($uid);
		$lock->unlock($key);

		$this->echoResult($result);
    }

	//万圣节--选卡片
    public function hallchoosecardAction()
    {
    	$uid = $this->uid;

    	$key = 'ev:hall:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key, 2);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}

		$result = Hapyfish2_Island_Event_Bll_HallWitches::hallChooseCard($uid);

		$this->echoResult($result);
    }

    //万圣节--兑换列表
	public function exchangelistAction()
	{
    	$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_HallWitches::exchangeList($uid);
		$this->echoResult($result);
	}

	//万圣节--补齐兑换
	public function replenishcardAction()
	{
    	$uid = $this->uid;
    	$groupId = $this->_request->getParam('groupId');

		if ($groupId === false) {
			$result = array('status' => -1, 'content' => 'serverWord_101');
			$this->echoResult(array('result' => $result));
		}

    	$key = 'ev:hallEx:lock:replenish:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key, 2);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}

		$result = Hapyfish2_Island_Event_Bll_HallWitches::replenishCard($uid, $groupId);
		$lock->unlock($key);

		$this->echoResult($result);
	}

	//万圣节--兑换物品
	public function toexchangeAction()
	{
    	$uid = $this->uid;
    	$groupId = $this->_request->getParam('groupId');

		if ($groupId === false) {
			$result = array('status' => -1, 'content' => 'serverWord_101');
			$this->echoResult(array('result' => $result));
		}

    	$key = 'ev:hallEx:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
    	$ok = $lock->lock($key, 2);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}

		$result = Hapyfish2_Island_Event_Bll_HallWitches::toExchange($uid, $groupId);
		$lock->unlock($key);

		$this->echoResult($result);
	}
	
	//单身节--购买人数
	public function getbuynumAction()
	{
		$uid = $this->uid;
		
		$result = Hapyfish2_Island_Event_Bll_BlackDay::getBuyNum();
		$this->echoResult($result);
	}
	
	//单身节--升级建筑
	public function gradeupbridalAction()
	{
		$uid = $this->uid;
		$itemId = $this->_request->getParam('itemId');
		
        $key = 'evlock:blcakday:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Event_Bll_BlackDay::gradeUpBridal($uid, $itemId);
		$lock->unlock($key);

		$this->echoResult($result);
	}
	
	//单身节--获得好友列表(赠送三次以上的好友不返回)
	public function getfriendlistbridalAction()
	{
		$uid = $this->uid;
		
		$result = Hapyfish2_Island_Event_Bll_BlackDay::getFriendListBridal($uid);
		$this->echoResult($result);
	}
	
	//单身节--赠送婚纱
	public function tosendbridalAction()
	{
		$uid = $this->uid;
		$friends = $this->_request->getParam('uids');
		
        $key = 'evlock:blcakday:to:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}

		$result = Hapyfish2_Island_Event_Bll_BlackDay::toSendBridal($uid, $friends);
		$lock->unlock($key);

		$this->echoResult($result);
	}

 }