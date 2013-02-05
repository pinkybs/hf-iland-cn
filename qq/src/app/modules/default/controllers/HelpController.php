<?php

/**
 * island help controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/10/12    Liz
 */
class HelpController extends Zend_Controller_Action
{
    protected $uid;
	
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
	
	public function init()
    {
        $info = $this->vailid();
        if (!$info) {
        	echo '<html><body>出错了，请刷新重新进入应用。</body></html>';
        	exit;
        }
        
        $this->info = $info;
        $this->uid = $info['uid'];
        $this->view->openid = $info['openid'];
        $this->view->openkey = $info['openkey'];
    	
    	$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        
        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }
    }

    public function topAction()
    {
        $this->render();
    }

    public function help2Action()
    {
        $this->render();
    }
    public function help3Action()
    {
        $this->render();
    }
    public function help4Action()
    {
        $this->render();
    }
    public function help5Action()
    {
        $this->render();
    }
    public function help6Action()
    {
        $this->render();
    }
    public function help7Action()
    {
        $this->render();
    }
    public function help8Action()
    {
        $this->render();
    }
    public function help9Action()
    {
        $this->render();
    }
 }
