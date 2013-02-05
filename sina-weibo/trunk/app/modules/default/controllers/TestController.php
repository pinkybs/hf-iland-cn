<?php

/**
 * api controller
 *
 * @copyright  Copyright (c) 2010 HapyFish Inc. (http://www.hapyfish.com)
 * @create      2010/01/19    Liz
 */
class TestController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
            echo 'valid error';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->uid = $info['uid'];
        $this->view->platformUid = $info['puid'];
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

    public function achieveAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->setAchieve(22);
        echo json_encode($aa);
        echo '<br/>';
        $bb = $rest->listAchieve();
        echo json_encode($bb);
        exit;
    }

    public function rankAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $cnt = Hapyfish2_Platform_Bll_Friend::getFriendCount($this->uid);
        $aa = $rest->setRank(1, $cnt);
        echo json_encode($aa);
        echo '<br/>';
        $bb = $rest->getRank(1);
        echo json_encode($bb);
        echo '<br/>';
        $cc = $rest->rankAll(1);
        echo json_encode($cc);
        exit;
    }


    public function followAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->getFollower(1,10);
        echo '<br/>粉丝<br/>';
        echo json_encode($aa);
        echo '<br/>关注<br/>';
        $bb = $rest->getFollowing(1,10);
        echo json_encode($bb);
        exit;
    }

    public function sendnAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->sendNotice('2137689143,2099009544', 'title<a href="c">bb</a>', 'content<a href="www.google.com">content</a></br>'."\n".'&nbsp; ok');
        echo json_encode($aa);
        exit;
    }

    public function isfanAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->isFans();
        echo json_encode($aa);
        exit;
    }

    public function ignoreallinviteAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->ignoreAllInvite();
        echo json_encode($aa);
        exit;
    }

    public function hasscoreAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->hasScored();
        echo json_encode($aa);
        exit;
    }

    public function engagestatusAction()
    {
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->engageStatus('10049');
        echo json_encode($aa);
        exit;
    }

    public function uinfoAction()
    {
    	$mKey = 'island:tool:wb:admfan';
        $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
        $info = $cache->get($mKey);
        print_r($info);
    	echo 'aa';
    	$followersCount = Hapyfish2_Island_Tool_Fans::getOfficialFan();//官方粉丝数
    	/*
        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);
        $aa = $rest->getFollowerCount('2155826421');
        */
    	echo 'followersCount:';
        print_r($followersCount);
        //echo json_encode($aa);
        exit;
    }
}