<?php

class LogController extends Zend_Controller_Action
{

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
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
        /*if (time() > $t + 86400) {
        	return false;
        }*/

        return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
    }

	public function reportAction()
	{
	    $info = $this->vailid();
        $uid = $info['uid'];
		$type = $this->_request->getParam('type');
		$aryLog = null;
		$log = Hapyfish2_Util_Log::getInstance();
		if ('cLoadTm' == $type) {
            $tm1 = $this->_request->getParam('tm1', 0);
    		$tm2 = $this->_request->getParam('tm2', 0);
    		$tm3 = $this->_request->getParam('tm3', 0);
    		$tm4 = $this->_request->getParam('tm4', 0);
    		$isNew = $this->_request->getParam('isNew', 0);
            $aryLog = array($uid, $tm1, $tm2, $tm3, $tm4, $isNew);

            /*//噪点数据
            if ($tm2 != 0) {
                if ($tm2<$tm1 || $tm2-$tm1 > 600000) {
                    $aryLog = false;
                }
            }
            if ($tm2 != 0 && $tm3 != 0) {
                if ($tm3 != 0 && ($tm3<$tm2 || $tm3-$tm2 > 600000)) {
                    $aryLog = false;
                }
            }
            if ($tm4<$tm3 || $tm4-$tm3 > 86400000) {
                $aryLog = false;
            }*/
		}
		else if ('noflash' == $type) {
		    $isNew = $this->_request->getParam('isNew', 0);
		    $ver = MyLib_Browser::getBrowser();
            $aryLog = array($uid, $ver, $isNew);
		}
		else if ('nocookie' == $type) {
		    $isNew = $this->_request->getParam('isNew', 0);
            $ver = MyLib_Browser::getBrowser();
            $aryLog = array($uid, $ver, $isNew);
		}

		if ($aryLog) {
		    $log->report($type, $aryLog);
		}
		header("HTTP/1.0 204 No Content");
		exit;
	}

 }