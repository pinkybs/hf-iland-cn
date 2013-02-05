<?php

class ConsumelogsController extends Zend_Controller_Action
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
		$uid = $this->uid;
		$time = time();
		$year = date('Y', $time);
		$month = (int)date('n', $time);
		
    	$logs = Hapyfish2_Island_Bll_ConsumeLog::getCoin($uid, $year, $month, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->date = $year . '年' . $month . '月';
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }

    public function coinAction()
    {
		$uid = $this->uid;
		$time = time();
		$year = date('Y', $time);
		$month = (int)date('n', $time);
		
    	$logs = Hapyfish2_Island_Bll_ConsumeLog::getCoin($uid, $year, $month, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->date = $year . '年' . $month . '月';
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }
    
    public function goldAction()
    {
		$uid = $this->uid;
		$time = time();
		$year = date('Y', $time);
		$month = (int)date('n', $time);
		
    	$logs = Hapyfish2_Island_Bll_ConsumeLog::getGold($uid, $year, $month, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->date = $year . '年' . $month . '月';
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }
    
    function __call($methodName, $args)
    {
        echo '400';
        exit;
    }

}