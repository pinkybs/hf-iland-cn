<?php

class FishmatchController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
		if (APP_STATUS == 0) {
        	$stop = true;
            if (APP_STATUS_DEV == 1) {
    			$ip = $this->getClientIP();
	    		if ($ip == '27.115.48.202' || $ip == '122.147.63.223' || $ip == '116.247.76.102') {
	    			$stop = false;
	    		}
    		}
    		if ($stop) {
        		$result = array('status' => '-1', 'content' => '停机维护中');
    			$this->echoResult($result);
    		}
    	}

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

    protected function getClientIP()
    {
    	$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
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

    /**
	 *
	 * 时间性礼物
	 */
	public function initfishAction()
    {
		$uid = $this->uid;
    	$data = Hapyfish2_Island_Bll_FishCompound::matchFishInit($uid);
    	$result = $data;

    	$this->echoResult($result);
    }
    
    public function compoundfishAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$add = $this->_request->getParam('mixStoneNum');
    	$result = Hapyfish2_Island_Bll_FishCompound::compound($uid,$id,0,$add);
    	$resultVo = array('result'=>$result['result'],'id'=>$result['id']);
    	$this->echoResult($resultVo);
    }
    
    public function levelupfishAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$add = $this->_request->getParam('mixStoneNum');
    	$result = Hapyfish2_Island_Bll_FishCompound::compound($uid,0,$id,$add);
    	$resultVo = array('result'=>$result['result']);
    	$this->echoResult($resultVo);
    }
    
    public function switchfishAction()
    {
    	$uid = $this->uid;
    	$id  = $this->_request->getParam('id');
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::switchfish($uid, $id);
    	$result = array('result'=>$resultVo);
    	$this->echoResult($result);
    }
    
    public function pveAction()
    {
    	$uid = $this->uid;
    	$id  = $this->_request->getParam('id');
    	$fid  = $this->_request->getParam('fishId');
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::pVE($uid, $fid, $id);
    	$this->echoResult($resultVo);
    }
    
    public function changeskillAction()
    {
    	$uid = $this->uid;
    	$id  = $this->_request->getParam('id');
    	$skillId = $this->_request->getParam('skillarray');
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::changeSkill($uid, $id, $skillId);
    	$result = array('result'=>$resultVo);
    	$this->echoResult($result);
    }
    
    public function deletefishAction()
    {
    	$uid = $this->uid;
    	$id  = $this->_request->getParam('id');
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::removeFish($uid, $id);
    	$result = array('result'=>$resultVo);
    	$this->echoResult($result);
    }
    
    public function exchangeAction()
    {
    	$uid = $this->uid;
    	$num  = $this->_request->getParam('num');
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::exchange($uid, $num);
    	$result = array('result'=>$resultVo);
    	$this->echoResult($result);
    }
    
    public function updateguideAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$result['status'] = -1;
    	$resultVo = Hapyfish2_Island_Cache_FishCompound::updateUserGuide($uid, $id);
    	if($resultVo){
    		$result['status'] = 1;
    	}
    	$results = array('result'=>$result);
    	$this->echoResult($results);
    }
    
    public function getawardAction()
    {
		$uid = $this->uid;
    	$id = $this->_request->getParam('revoke');
    	$result['status'] = 1;
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::getAward($uid, $id);
    	$this->echoResult($resultVo);
    }
    
    public function getfriendfishAction()
    {
    	$uid = $this->uid;
    	$fuid = $this->_request->getParam('fuid');
    	$result['status'] = 1;
    	$result['list'] = Hapyfish2_Island_Bll_FishCompound::getFriendFIsh($fuid);
    	$result['mapId']=11;
    	$tesultVo = array('result'=>$result);
    	$this->echoResult($tesultVo);
    }
    
	public function pvpAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$fuid = $this->_request->getParam('fuid');
    	$resultVo = Hapyfish2_Island_Bll_FishCompound::pvp($uid,$fuid,$id);
    	$this->echoResult($resultVo);
    }
    
    public function initarenaAction()
    {
    	$uid = $this->uid;
    	$result = Hapyfish2_Island_Bll_FishCompound::initarena($uid);
    	$this->echoResult($result);
    }
    
    public function toprankAction()
    {
    	$list = Hapyfish2_Island_Bll_FishCompound::topRank();
    	$result['status'] = 1;
    	$resultVo['result'] = $result;
    	$resultVo['catchFishArmoryVo'] = $list;
    	$this->echoResult($resultVo);
    }
    
    public function dekaronAction()
    {
    	$uid = $this->uid;
    	$ranking = $this->_request->getParam('ranking');
    	$result = Hapyfish2_Island_Bll_FishCompound::dekaron($uid,$ranking);
    	$this->echoResult($result);
    }
    
    public function getreawardAction()
    {
    	$uid = $this->uid;
    	$result = Hapyfish2_Island_Bll_FishCompound::getReAward($uid);
    	$this->echoResult($result);
    }
    
    public function reexchangeAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$result = Hapyfish2_Island_Bll_FishCompound::getReExchange($uid,$id);
    	$this->echoResult($result);
    }
    
    public function changeprefixAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$result = Hapyfish2_Island_Bll_FishCompound::changePrefix($uid,$id);
    	$this->echoResult($result);
    }
    
    public function skipAction()
    {
    	$uid = $this->uid;
    	$result = Hapyfish2_Island_Bll_Vip::skip($uid);
    	$resultVo = array('result'=>$result);
    	$this->echoResult($resultVo);
    }
    
    public function geteventawardAction()
    {
    	$uid = $this->uid;
    	$type = $this->_request->getParam('type');
    	$result = Hapyfish2_Island_Bll_Vip::getEventAward($uid, $type);
    	$resultVo = array('result'=>$result);
    	$this->echoResult($resultVo);
    }
    
    public function unlockfishAction()
    {
    	$uid = $this->uid;
    	$fid = $this->_request->getParam('cid');
    	$result = Hapyfish2_Island_Bll_FishCompound::unlockFish($uid, $fid);
    	$resultVo = array('result'=>$result);
    	$this->echoResult($resultVo);
    }
 }	