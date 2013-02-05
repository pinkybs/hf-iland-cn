<?php

class UnittestController extends Zend_Controller_Action
{
	public function userAction()
	{
		$auth = Zend_Auth::getInstance();
	    if (!$auth->hasIdentity()) {
            $result = array('status' => '-1', 'content' => 'serverWord_101');
            echo Zend_Json::encode($result);
            exit;
        }
        $uid = $auth->getIdentity();
		$qzone = Qzone_Rest2::getInstance();
		$openid = $_SESSION['openid'];
		$openkey = $_SESSION['openkey'];
		$qzone->setUser($openid, $openkey);

		$user_data = $qzone->getUser();

		print_r($user_data);

		exit;
	}

	public function paybalanceAction()
	{
		$auth = Zend_Auth::getInstance();
	    if (!$auth->hasIdentity()) {
            $result = array('status' => '-1', 'content' => 'serverWord_101');
            echo Zend_Json::encode($result);
            exit;
        }
        $uid = $auth->getIdentity();
		$qzone = Qzone_Rest2::getInstance();
		$openid = $_SESSION['openid'];
		$openkey = $_SESSION['openkey'];
		$qzone->setUser($openid, $openkey);

		$balance = $qzone->getPayBalance();

		echo 'balance: ' . $balance;

		exit;
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

    public function testclientipAction()
    {
        echo print_r($_SERVER);
        echo '<br />';
    	echo $this->getClientIP();
    	exit;//$this->render();
    }

}