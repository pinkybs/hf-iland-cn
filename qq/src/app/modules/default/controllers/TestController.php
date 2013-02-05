<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/01/20    Hulj
 */
class TestController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'hello qq test!! <br />';
    	$cdkey = $this->_request->getParam('key');
		echo substr($cdkey, -3, 1);
    	exit;//$this->render();
    }

    public function createkeyAction()
    {
        exit;
    	$count = Hapyfish2_Island_Event_Bll_CdKeyII::createCdkey(4, 100);
    	echo $count . 'Done!';
    	exit;
    }

    public function docdkeyAction()
    {
        exit;
    	$uid = $this->_request->getParam('uid');
    	$cdKey = $this->_request->getParam('key');
    	//check
		if (strlen($cdKey) != 17 || !Hapyfish2_Island_Event_Bll_CdKeyII::isRegularCdkey($cdKey)) {
			$result = array('status' => -1, 'content' => '');
			echo 'ng';
			exit;
		}
echo 'ok';
exit;

    	//$rst = Hapyfish2_Island_Event_Bll_CdKey::validCdKey($uid, $cdKey);
    	echo $rst;
    	exit;
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
		} else if (!empty($_SERVER['HTTP_QVIA'])) {
			$strData = substr($_SERVER['HTTP_QVIA'], 0, 8);
			$data = array(hexdec(substr($strData, 0, 2)),
			              hexdec(substr($strData, 2, 2)),
			              hexdec(substr($strData, 4, 2)),
			              hexdec(substr($strData, 6, 2)));
			$ip = implode('.', $data);
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }

    public function testgetpayAction()
    {
        $info = $this->vailid();
        $data = array('uid' => $info['uid'], 'openid' => $info['openid'], 'openkey' => $info['openkey']);

        $rest = Qzone_RestQzone::getInstance();
        $rest->setUser($info['openid'], $info['openkey']);

        echo $this->getClientIP();
        echo '<br/>';
        echo $rest->getPayBalance($this->getClientIP());
        exit;
    }

    public function testclientipAction()
    {
        echo print_r($_SERVER);
        echo '<br />';
    	echo $this->getClientIP();
    	echo '<br />';
    	echo $_SERVER['SERVER_PORT'];
    	exit;//$this->render();
    }

    public function getqpointpaytokenAction()
	{
		$info = $this->vailid();
		$qzone = Qzone_RestQpointPay::getInstance();
        $openid = $info['openid'];
        $openkey = $info['openkey'];
        $qzone->setUser($openid, $openkey);
        $id = 114732;
        $rowInfo = Hapyfish2_Island_Cache_BasicInfo::getPlantInfo($id);
        $token = $qzone->getQpointPayToken($rowInfo['cid'], $rowInfo['price'], 1, 'http://imgcache.qzoneapp.com/sango/goods/taoFaLing_3.png', $rowInfo['price'].'*1个');
        echo 'token:' . json_encode($token);
        exit;
	}
}
