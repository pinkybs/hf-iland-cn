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

    public function getsigAction()
    {
    	$t = time();
    	echo '1001_' . $t . '_' . md5('1001' . $t . APP_KEY);
    	echo '<br/>';
    	echo '1002_' . $t . '_' . md5('1002' . $t . APP_KEY);
    	exit;
    }

    protected function vailid($name)
    {
    	$skey = $_COOKIE[$name];
		if (!$skey) {
    		return false;
    	}

        $tmp = split('_', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 3) {
    		return false;
    	}

        $id= $tmp[0];
        $t = $tmp[1];
        $sig = $tmp[2];

        $vsig = md5($id . $t . APP_KEY);
		if ($sig != $vsig) {
			return false;
		}

        //max long time one day
        /*
        if (time() > $t + 31104000) {
        	return false;
        }*/

		return array('id' => $id, 't' => $t);
    }

	public function payflowAction()
	{
		$date = $this->_request->getParam('date');
		$logDir = '/data/log/happyfish/payflow/';
		if (!empty($date)) {
			$info = $this->vailid('hf_payflow_date');
	        if (!$info) {
				exit;
	        }

			if ($info['id'] == 1001) {
				$listname = $logDir . $date . '/file_list.txt';
				if (is_file($listname)) {
					echo file_get_contents($listname);
				}
			}
			exit;
		}

		$file = $this->_request->getParam('file');
		if (!empty($file)) {
			$info = $this->vailid('hf_payflow_file');
	        if (!$info) {
				exit;
	        }

			if ($info['id'] == 1002) {
				$tmp = explode('_', $file);
				if (count($tmp) == 0) {
					$filename = $logDir . $file . '/' . $file;
				} else if(count($tmp) == 2) {
					$filename = $logDir . $tmp[0] . '/' . $file;
				} else {
					exit;
				}


				if (is_file($filename)) {
					echo file_get_contents($filename);
				}
			}
			exit;
		}

		exit;
	}

    protected function vailidSkey()
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

    public function reportAction()
	{
	    $info = $this->vailidSkey();
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