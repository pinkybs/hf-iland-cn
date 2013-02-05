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
        $data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
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
	public function setupgifttimeAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_Timegift::setup($this->uid);
    	$this->echoResult($result);

    }
    /**
     * 	梦想花园用户登录奖励
     *
     *
     */
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
	protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	if (!$skey) {
    		return false;
    	}

    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $puid = $tmp[1];
        $session_key = base64_decode($tmp[2]);
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $puid . $session_key . $t . APP_KEY);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_KEY);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
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

    public function getactivedayAction()
    {
    	$uid = $this->uid;
    	$result = array('status' => -1, 'content' => 'serverWord_110');
    	$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
    	if (!$loginInfo) {
			$this->echoResult($result);
		}

		$this->echoResult(array('day' => $loginInfo['active_login_count'], 'result' => array('status' => 1)));
    }

    public function active5dayAction()
    {
    	$uid = $this->uid;
    	$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
    	if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult($result);
		}

    	$result = array('status' => -1, 'content' => 'serverWord_110');
    	$loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
        if (!$loginInfo) {
			$this->echoResult($result);
		}

		if ($loginInfo['active_login_count'] < 5) {
			$this->echoResult($result);
		}

    	$isGained = Hapyfish2_Island_Event_Bll_Active5Day::isGained($uid);
		if (!$isGained) {
			$result = Hapyfish2_Island_Event_Bll_Active5Day::gain($uid);
		} else {
			$result['content'] = 'serverWord_151';
		}

        //release lock
        $lock->unlock($key);

		$this->echoResult($result);
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
	 public function getstarfishexternalmallAction(){
        $uid = $this->uid;
//        if($uid != 11111111){
//        	 $result = array('status' => -1, 'content' => '活动即将开始，请耐心等待');
//             $this->echoResult($result);
//        }
        $result['result'] = array('status' => 1,'content' => '');
        $start = 1306406399;
        $result['data'] = Hapyfish2_Island_Event_Bll_StarfishSale::getSaleList();
        $result['haveInvitedFriendNum'] = Hapyfish2_Island_Event_Bll_StarfishSale::getInviteCount($uid,$start);
        $result['userStarFish'] = Hapyfish2_Island_HFC_User::getUserStarFish($uid);
        $this->echoResult($result);
     }
     public function starfishexchangeAction(){
         $cid = $this->_request->getParam('cid');
         $uid = $this->uid;
//        if($uid != 11111111){
//        	 $result = array('status' => -1, 'content' => '活动即将开始，请耐心等待');
//             $this->echoResult($result);
//        }
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

	public function sendteambuyfeedAction()
	{
		$uid = $this->uid;
		$type = 'TEAMBUY_FEED';

		$result = Hapyfish2_Island_Bll_Activity::send($type, $uid);

        $this->echoResult($result);
	}

    /**
     * add user strom coin
     */
    public function addstromcoinAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_Strom::addCoin($uid);

    	$this->echoResult($result);
    }

    /**
     * get user strom status
     */
    public function getflashstomstatusAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Bll_Strom::getStrom($uid);

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
		$cache->set($key, 1);

		exit;
	}

	public function lupawardboxopenedAction()
	{
		$result['result'] = array('status' => 1,'content' => '');

		$uid = $this->uid;

		$gettf = Hapyfish2_Island_Event_Bll_UpgradeGift::getTF($uid);

		$ok = false;
		if (!$gettf) {
			$ok = Hapyfish2_Island_Event_Bll_UpgradeGift::gifttouser($uid);
			if ($ok)
			{
				Hapyfish2_Island_Event_Bll_UpgradeGift::setTF($uid);
				$result['result']['goldChange'] = 10;
				$result['result']['coinChange'] = 20000;
			}
		}

		if (! $ok) {
			$result['result']['status'] = -1;
		}

		$this->echoResult($result);
	}

	public function getsystimeAction()
	{
//		$result['result'] = array('status' => 1,'content' => '');
//		$result['systime'] = time();

		$result = array ('systime' => time());
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
			$arySaleInfo['name'] = '大礼包';
			$arySaleInfo['price'] = 50;
			$items[] = array('item_id' => 3431, 'item_num' => 1);
			$items[] = array('item_id' => 18031, 'item_num' => 1);
			$items[] = array('item_id' => 18331, 'item_num' => 1);
			$items[] = array('item_id' => 20931, 'item_num' => 1);
		}
		//保安卡礼包
		//码头保安卡
		else if (2 == $packId) {
			$arySaleInfo['id'] = 1709;
			$arySaleInfo['name'] = '鲸鱼馆';
			$arySaleInfo['price_type'] = 2;
			$arySaleInfo['price'] = 9;
			$items[] = array('item_id' => 15231, 'item_num' => 1);
		}
		//加速卡包
		//加速III
		else if (3 == $packId) {
			$arySaleInfo['id'] = 1409;
			$arySaleInfo['name'] = '水晶球';
			$arySaleInfo['price_type'] = 1;
			$arySaleInfo['price'] = 10000;
			$items[] = array('item_id' => 42821, 'item_num' => 1);
		}
    	//加速卡包3
		//加速II
		else if (4 == $packId) {
			$arySaleInfo['id'] = 1509;
			$arySaleInfo['name'] = '童话南瓜车';
			$arySaleInfo['price_type'] = 1;
			$arySaleInfo['price'] = 10000;
			$items[] = array('item_id' => 43021, 'item_num' => 1);
		}
    	//加时卡包
		//加时卡
		else if (5 == $packId) {
			$arySaleInfo['id'] = 1609;
			$arySaleInfo['name'] = '富士山';
			$arySaleInfo['price_type'] = 1;
			$arySaleInfo['price'] = 12500000;
			$items[] = array('item_id' => 79232, 'item_num' => 1);
		}
    	else if (6 == $packId) {
			$arySaleInfo['id'] = 1809;
			$arySaleInfo['name'] = '富士山';
			$arySaleInfo['price_type'] = 2;
			$arySaleInfo['price'] = 850;
			$items[] = array('item_id' => 82532, 'item_num' => 1);
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

 }