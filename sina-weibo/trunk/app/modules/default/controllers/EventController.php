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
    	$status = Hapyfish2_Platform_Cache_User::getStatus($info['uid']);
        if($status > 0){
        	exit;
        }
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

	protected function vailid()
    {
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
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
	        $vsig = md5($uid . $puid . $session_key . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
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
        $start = 1301663820;
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
	public function getrankAction()
	{
		$uid = $this->uid;
		$result = Hapyfish2_Island_Bll_Rank::getRank($uid);
		 $this->echoResult($result);
	}

	public function getfriendlistAction()
	{
		$uid = $this->uid;
		$list = Hapyfish2_Island_Bll_SearchFriend::getSearchFriend();
		$this->echoResult($list);
	}
	public function sendremindAction()
	{
		$uid = $this->uid;
		$fid = $this->_request->getParam('uid');
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$content = $user['name'].'加你为好友了，快去你的主页看看吧!';
		Hapyfish2_Island_Bll_Remind::addRemind($this->uid, $fid, $content, 0);
		exit;
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

	//收集任务--收集面板
	public function collectiontaskAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_Hash::collectionTask($uid);

		$this->echoResult($result);
	}
	//收集任务--领取礼物
	public function getgiftAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_Hash::getGift($uid);

		$this->echoResult($result);
	}
	//感恩节收集任务--收集面板
	public function tkcollectiontaskAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_Tkhash::collectionTask($uid);

		$this->echoResult($result);
	}
	//感恩节收集任务--领取礼物
	public function tkgetgiftAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_Tkhash::getGift($uid);

		$this->echoResult($result);
	}
	//团购活动--团购信息
	public function teambuyAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_TeamBuy::teamBuy($uid);

		$this->echoResult($result);
	}

	//团购活动--加入团购
	public function jointeambuyAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_TeamBuy::joinTeamBuy($uid);

		$this->echoResult($result);
	}

	//团购活动--购买团购商品
	public function buygoodsAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Event_Bll_TeamBuy::buyGoods($uid);

		$this->echoResult($result);
	}

	//团购活动--发feed
	public function sendteambuyfeedAction()
	{
		$uid = $this->uid;
		$type = 'TEAMBUY_FEED';

		$result = Hapyfish2_Island_Bll_Activity::send($type, $uid);
        header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo $result;
    	exit;
	}

    /**
     * send flash strom feed
     */
	public function sendstromfeedAction()
	{
		$uid = $this->uid;

		$result = Hapyfish2_Island_Bll_Activity::send('STROM_FEED', $uid);
		header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo $result;
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

		$topcids = array('98332','103332','102532','91632');


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
				$result['keyNum'] = (isset($userCard['86241']) ? $userCard['86241']['count'] : 0);	// 剩余钥匙个数

//				$point = Hapyfish2_Island_Event_Bll_Casino::getUserPoint($uid);
//				$result['leftNum'] = ($point ? $point : 0);	// 玩家积分
				$result['price1'] = 5;	// 开一次用的岛钻石
				$result['price10'] = 50;	// 开10次用的岛钻石
				$result['cheapPrice1'] = 5;	// 开一次用的岛钻石
				$result['cheapPrice10']= 40;	// 开10次用的岛钻石
				$result['canUseGold'] = 1;	// 是否可以使用岛钻石 1,或0



				// 宝箱优化
				$list = Hapyfish2_Island_Cache_BottleFriendHelp::getByUid($uid);
				$result['helpTime'] = $list['lasttime'] ? ((time()-$list['lasttime'])>(3600*8) ? 0 : (3600*8) - (time()-$list['lasttime'])) : 0;// 距离好友结束时间
				$result['helpCount'] = $list['fid'] || $list['fid']=='0' ? count(explode(',',$list['fid'])): 0;// 已给予帮助个数
				$result['helpTotal'] = 5;// 需要好友帮助总数

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

		$this->echoResult($result);
	}

	// 掉宝箱系统，获取玩家奖励列表
	public function bottleuserlistAction()
	{
		$uid = $this->uid;
		$result = array('result'=>array('status'=>-1, 'goldChange'=>0), 'list'=>array());

		$list = Hapyfish2_Island_Cache_BottleQueue::getall();
		if ($list) {
			$result['result']['status'] = 1;
			$result['list'] = $list;
			$this->echoResult($result);
		} else {
			// test
			$result['result']['status'] = 1;
			$result['list'] = array(array('name'=>'lei.wu', 'time'=>time(), 'list'=>array('coin'=>123, 'tips'=>'321')));
		}

		$this->echoResult($result);
	}

	// 获取给予帮助玩家列表
	public function bottlefriendhelpAction()
	{
		$uid = $this->uid;

		$result = array('result'=>array('status'=>-1, 'goldChange'=>0), 'list'=>array());

		$list = Hapyfish2_Island_Cache_BottleFriendHelp::getByUid($uid);

		$names = array();
		$faces = array();
		$price = array('price'=>1, 'priceType'=>2, 'cheapPrice'=>1, 'cheapPriceType'=>2);


		if ($list) {
			$fids = $list['fid'] || $list['fid']=='0' ? explode(',', $list['fid']) : array();
			$goldTF = $list['goldTF'] || $list['goldTF']=='0' ? explode(',', $list['goldTF']) : array();

			foreach ($goldTF as $key => $val) {

				if ($goldTF[$key]) {
					$names[] = '';
					$faces[] = '';
				} else {
					$userinit = Hapyfish2_Island_Bll_User::getUserInit($fids[$key]);
					$names[] = $userinit['name'] ? $userinit['name'] : '';
					$faces[] = $userinit['smallFace'] ? $userinit['smallFace'] : '';
				}
			}
		}

		$result['result']['status']=1;				//
		$result['total'] = 5;						//需要好友总数
		$result['price'] = $price['price'];			//邀请一个好友的价格
		$result['priceType'] = $price['priceType'];	//邀请好友价格类型
		$result['cheapPrice'] = $price['cheapPrice'];//邀请一个好友打折价格
		$result['cheapPriceType'] = $price['cheapPriceType'];	//邀请一个好友打折价格
		$result['nameList'] = $names;	//头像列表
		$result['faceList'] = $faces;	//名称列表

		$this->echoResult($result);
	}

	// 走捷径
	public function bottlefriendhelpshortcutAction()
	{
		$uid = $this->uid;

		$result = array('result'=>array('status'=>-1, 'goldChange'=>0), 'list'=>array());

		$price = array('price'=>1, 'priceType'=>2, 'cheapPrice'=>1, 'cheapPriceType'=>2);

		$tf = true;

		// 获得玩家钻石
		$balanceInfo = Hapyfish2_Island_Bll_Gold::get($uid, true);
		$userGold = $balanceInfo['balance'];

		// 获得朋友帮助列表
		$list = Hapyfish2_Island_Cache_BottleFriendHelp::getByUid($uid);
		$fid = $list['fid'] || $list['fid']=='0'  ? explode(',', $list['fid']) : array();
		$goldTF = $list['goldTF'] || $list['goldTF']=='0' ? explode(',', $list['goldTF']) : array();


		if ( $userGold < $price['cheapPrice'] ) {
			$result['result']['content'] = 'serverWord_140';
			$tf = false;
		}
		if ( count($list['goldTF']) >= 5) {
			$tf = false;
		}

		if ($tf) {

			try {
				$fid[] = '0';
				$goldTF[] = '1';
				if ($list) {
					$info = array('fid'=>join(',', $fid), 'goldTF'=>join(',', $goldTF));
					Hapyfish2_Island_Cache_BottleFriendHelp::update($uid, $info);
				} else {
					$info = array('uid'=>$uid, 'fid'=>join(',', $fid), 'goldTF'=>join(',', $goldTF));
					Hapyfish2_Island_Cache_BottleFriendHelp::insert($uid, $info);
				}
				$result['result']['status'] = 1;
				$result['result']['goldChange'] = -$price['cheapPrice'];
				$ok = true;
			} catch (Exception $e) {
			}

			if ($ok) {
				$userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
				$userLevel = $userLevelInfo['level'];

				$goldCost = $price['cheapPrice'];
				$goldInfo = array(	'uid'=>$uid,
									'cost'=>$goldCost,
									'summary'=>'求助开启海盗宝箱',
									'user_level'=>$userLevel,
									'cid'=>'10002',
									'num'=>'1');
				$rst = Hapyfish2_Island_Bll_Gold::consume($uid, $goldInfo);
                if ($rst) {
				    Hapyfish2_Island_HFC_Achievement::updateUserAchievementByField($uid, 'num_19', $goldCost);
                }
			}
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
				//$com->setGold(10);
				$com->setCoin(1000);
				$skey = $gkey;
				//$result['goldChange'] = 10;
				$result['coinChange'] = 1000;
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

    public function getfansinfoAction()
    {
    	$uid = $this->uid;
    	$result = Hapyfish2_Island_Event_Bll_fansGift::getUserFans($uid);
    	$this->echoResult($result);
    }

    public function getfansgiftAction()
    {
    	$uid = $this->uid;
   		$key = 'evlock:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
	    //get lock
		$ok = $lock->lock($key);
    	if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result'=>$result));
		}
    	$result = Hapyfish2_Island_Event_Bll_fansGift::receive($uid);
    	$lock->unlock($key);
    	$this->echoResult($result);
    }

	 public function loadvalentineAction()
    {
    	$uid = $this->uid;
    	$eventEndTime = 1313768399;//2011-03-15
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

	//碎片换建筑
	public function exchangefragmentAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		$result = Hapyfish2_Island_Bll_Fragments::exchangeFragment($uid, $type);
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


    public function getgradegiftAction()
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
		$result = Hapyfish2_Island_Event_Bll_Grade::getGradeGift($uid);
		$lock->unlock($key);
        $this->echoResult($result);
    }

    //邀请好友获得奖励
    public function initinviteforawardAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Event_Bll_InviteGift::initInviteForAward($uid);

    	$this->echoResult($result);
    }

    public function getawardforinviteAction()
    {
    	$uid = $this->uid;

    	$key = 'ev:invite:lock:' . $uid;
    	$lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
		$ok = $lock->lock($key);
        if (!$ok) {
			$result = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $result));
		}

    	$result = Hapyfish2_Island_Event_Bll_InviteGift::getAwardForInvite($uid);
    	$lock->unlock($key);

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
		$lock->unlock($key);

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
//
//	//限时抢购-初始化
//	public function panicbuyinitAction()
//	{
//		$uid = $this->uid;
//
//		$result = Hapyfish2_Island_Event_Bll_PanicBuy::panicBuyInit($uid);
//		$this->echoResult($result);
//	}
//
//	//限时抢购-领取
//	public function getpanicbuygiftAction()
//	{
//		$uid = $this->uid;
//
//        $key = 'evlock:panic:' . $uid;
//        $lock = Hapyfish2_Cache_Factory::getLock($uid);
//
//	    //get lock
//		$ok = $lock->lock($key);
//		if (!$ok) {
//			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
//			$this->echoResult(array('result' => $resultVo));
//		}
//
//		$result = Hapyfish2_Island_Event_Bll_PanicBuy::getPanicBuyGift($uid);
//		$lock->unlock($key);
//
//		$this->echoResult($result);
//	}
//
//	//限时抢购宝箱-init
//	public function panicbuyboxAction()
//	{
//		$uid = $this->uid;
//
//		$result = Hapyfish2_Island_Event_Bll_PanicBuy::panicBuyBox($uid);
//		$this->echoResult($result);
//	}
//
//	//限时抢购宝箱-领取
//	public function getpanicbuyboxAction()
//	{
//		$uid = $this->uid;
//		$idx = $this->_request->getParam('id');
//
//        $key = 'evlock:panic:box:' . $uid;
//        $lock = Hapyfish2_Cache_Factory::getLock($uid);
//
//	    //get lock
//		$ok = $lock->lock($key);
//		if (!$ok) {
//			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
//			$this->echoResult(array('result' => $resultVo));
//		}
//
//		$result = Hapyfish2_Island_Event_Bll_PanicBuy::getPanicBuyBox($uid, $idx);
//		$lock->unlock($key);
//
//		$this->echoResult($result);
//	}
	
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
   //读取捕鱼面板
    public function initfishAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Event_Bll_CatchFish::initFish($uid);
    	$this->echoResult($result);
    }

    //读取捕鱼动态数据
    public function fishuserAction()
    {
    	$uid = $this->uid;

    	$result = Hapyfish2_Island_Event_Bll_CatchFish::fishUser($uid);
    	$this->echoResult($result);
    }

    //捕鱼动作
    public function catchfishAction()
    {
    	$uid = $this->uid;
    	$productid = (int)$this->_request->getParam('id');
    	$helpFlag = (int)$this->_request->getParam('isHideHelpView');
    	$result = Hapyfish2_Island_Event_Bll_CatchFish::catchFish($uid, $productid, $helpFlag);
    	$this->echoResult($result);
    }
    
    //捕鱼获取折扣券购买建筑
    public function catchfishbuyplantAction()
    {
    	$uid = $this->uid;
    	$cid = (int)$this->_request->getParam('cid');
    	$result = Hapyfish2_Island_Event_Bll_CatchFish::buyPlant($uid, $cid);
    	$this->echoResult($result);
    }
    
    //获取捕鱼排行榜
    public function fishrankAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_CatchFish::catchFishRank();
    	$this->echoResult($result);
    }
    
    public function getrollrankAction()
    {
    	$result = Hapyfish2_Island_Event_Bll_CatchFish::catchFishRollRank();
    	$this->echoResult($result);
    }
    
	//圣诞节——初始化
	public function christmasinitAction()
	{
		$uid = $this->uid;
		
		$result = Hapyfish2_Island_Event_Bll_Christmas::christmasinit($uid);
		
		$this->echoResult($result);
	}
    
	//圣诞节——获取需要的建筑
	public function chrismasgetplantAction()
	{
		$uid = $this->uid;
		
		$result = Hapyfish2_Island_Event_Bll_Christmas::christmasGetPlant($uid);
		
		$this->echoResult($result);
	}
	
	//圣诞节——首次请求面板
	public function christmasoncerequestAction()
	{
		$uid = $this->uid;
		$taskId = $this->_request->getParam('taskId');
		
		$result = Hapyfish2_Island_Event_Cache_Christmas::christmasOnceRequest($uid, $taskId, 1);

		$this->echoResult($result);
	}
    
	//圣诞节——领取奖励
	public function christmastaskAction()
	{
		$uid = $this->uid;
		$taskId = $this->_request->getParam('taskId');
		
		$key = 'evlock:chrismas:gettask:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		
		$result = Hapyfish2_Island_Event_Bll_Christmas::christmastask($uid, $taskId);
		$lock->unlock($key);
		
		$this->echoResult($result);
	}
	
	 //圣诞节——购买建筑
	public function chrismascompleteAction()
	{
		$uid = $this->uid;
		$cid = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
		
		$key = 'evlock:chrismas:complete:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		
		$result = Hapyfish2_Island_Event_Bll_Christmas::christmasComplete($uid, $cid, $num);
		$lock->unlock($key);
		
		$this->echoResult($result);
	}
    
	//圣诞节--兑换公主
	public function toexchangeprincessAction()
	{
		$uid = $this->uid;
		$id = $this->_request->getParam('id');

		$key = 'evlock:chrismas:colorball:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		
		$result = Hapyfish2_Island_Event_Bll_Christmas::toExchangePrincess($uid, $id);
		$lock->unlock($key);
		
		$this->echoResult($result);
	}
	
	//圣诞节--赛鹿
	public function chrismasmatchfawnAction()
	{
		$uid = $this->uid;
		$deerList = $this->_request->getParam('deerlist');

		$deerListArr = explode('-', $deerList);

		$key = 'evlock:chrismas:matchfawn:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

	    //get lock
		$ok = $lock->lock($key);
		if (!$ok) {
			$resultVo = array('status' => -1, 'content' => 'serverWord_103');
			$this->echoResult(array('result' => $resultVo));
		}
		
		$result = Hapyfish2_Island_Event_Bll_Christmas::chrismasMatchFawn($uid, $deerListArr);
		$lock->unlock($key);
		
		$this->echoResult($result);
	}
    
 }