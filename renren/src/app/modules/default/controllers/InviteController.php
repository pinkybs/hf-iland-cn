<?php

class InviteController extends Zend_Controller_Action
{
    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
            echo '<html><body><script type="text/javascript">window.top.location="http://apps.renren.com/'.APP_NAME.'/";</script></body></html>';
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

    public function topAction()
    {
    	$this->render();
    }

	public function friendsAction()
    {
        $st = time();
        
        $onwerUid = Hapyfish2_Platform_Cache_User::getUser($this->uid);
        
        $invite_param= 'hf_invite=true&hf_inviter=' . $onwerUid['puid'] . '&hf_st=' . $st;
        $sg = md5($invite_param . APP_KEY . APP_SECRET);

        $this->view->params = $invite_param . '&hf_sg=' . $sg;
        $this->view->st = $st;
        $this->view->sg = $sg;
        $this->render();
    }

    public function sendAction()
    {
		$st = $this->_request->getParam('hf_st');
        $sg = $this->_request->getParam('hf_sg');
        $ids = $this->_request->getParam('ids');

        $onwerUid = Hapyfish2_Platform_Cache_User::getUser($this->uid);

        if ($st && $sg && $ids) {
            foreach($ids as $id) {
                Hapyfish2_Island_Bll_InviteLog::addInvite($onwerUid['puid'], $id, $st, $sg);
            }
        }

		$this->_redirect('/invite/top');
        exit;
    }

 }
