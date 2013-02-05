<?

require_once 'Qzone/Rest/Abstract.php';

class Qzone_Rest_Qzone extends Qzone_Rest_Abstract
{
    protected function _init()
    {
        //$this->server_addr = API_HOST . '/cgi-bin';
        $this->server_addr = 'http://113.108.86.20/qzone';
        $this->v = self::DEFAULT_SERVICE_VERSION;
    }

    protected function set_api_address($addr)
    {
         $this->server_addr = $addr;
    }

    protected function get_api_address($method)
    {
    	return $this->server_addr . '/' . $method;
    }

    public function user_getProfile()
    {
        return $this->call_method('user/info', array());
    }

    public function user_isAppUser()
    {
        return $this->call_method('user/is_setuped', array());
    }

    public function user_getAppFriendIds()
    {
        $params = array(
        	'page'   => '0'
        );

    	return $this->call_method('relation/friends', $params);
    }

    public function friend_isFriend($fopenid)
    {
        $params = array(
            'fopenid' => $fopenid
        );

    	return $this->call_method('relation/is_friend', $params);
    }

    public function pay_isvip()
    {
    	return $this->call_method('pay/is_vip', array());
    }

    public function pay_getBalance()
    {
        $params = array();
        $params['appid'] = $this->app_id;
        $params['appkey'] = $this->app_key;
        $params['appname'] = $this->app_name;
        $params['openid'] = $this->open_id;
        $params['openkey'] = $this->open_key;
        $params['ts'] = time();

        //generate sig
        $strParam = '';
        foreach ($params as $key=>$val) {
            $strParam .= $key . $val;
        }
        $sig = md5($strParam.$this->app_key);
        $params['device'] = 0;
        $params['userip'] = $this->getClientIP();
        $params['sig'] = $sig;

        $data = $this->post_request_pay('v2/r/qzone/qz_get_balance', $params);
        if ($data) {
            $data = json_decode($data, true);
        }
    	return $data;
    }

	/**
     * pay, user can confirm or cancel
     *
     * @param array $items (array('xxx' => 3, 'yyy' => 5))
     * @param int $amt
     */
    public function pay_pay($items, $amt)
    {
    	$payitem = array();
    	foreach ($items as $key => $val) {
            $payitem[] = $key.'*'.$val;
        }

        $params = array();
        $params['amt'] = $amt;
        $params['appid'] = $this->app_id;
        $params['appkey'] = $this->app_key;
        $params['appname'] = $this->app_name;
        $params['openid'] = $this->open_id;
        $params['openkey'] = $this->open_key;
        $params['ts'] = time();

        //generate sig
        $strParam = '';
        foreach ($params as $key=>$val) {
            $strParam .= $key . $val;
        }
        $sig = md5($strParam.$this->app_key);
        $params['payitem'] = implode('&amp;', $payitem);
        $params['device'] = 0;
        $params['userip'] = $this->getClientIP();
        $params['sig'] = $sig;

        $data = $this->post_request_pay('v2/r/qzone/qz_pre_pay', $params);
        if ($data) {
            $data = json_decode($data, true);
        }
    	return $data;
    }

    public function pay_confirm($billno, $amt)
    {
    	$params = array();
        $params['action'] = 'confirm';
        $params['amt'] = $amt;
        $params['appid'] = $this->app_id;
        $params['appkey'] = $this->app_key;
        $params['appname'] = $this->app_name;
        $params['billno'] = $billno;
        $params['openid'] = $this->open_id;
        $params['openkey'] = $this->open_key;
        $params['ts'] = time();

        //generate sig
        $strParam = '';
        foreach ($params as $key=>$val) {
            $strParam .= $key . $val;
        }
        $sig = md5($strParam.$this->app_key);
        $params['device'] = 0;
        $params['userip'] = $this->getClientIP();
        $params['sig'] = $sig;

        $data = $this->post_request_pay('v2/r/qzone/qz_pay_confirm', $params);
        if ($data) {
            $data = json_decode($data, true);
        }
    	return $data;
    }

    public function pay_cancel($billno, $amt)
    {
    	$params = array();
        $params['action'] = 'cancel';
        $params['amt'] = $amt;
        $params['appid'] = $this->app_id;
        $params['appkey'] = $this->app_key;
        $params['appname'] = $this->app_name;
        $params['billno'] = $billno;
        $params['openid'] = $this->open_id;
        $params['openkey'] = $this->open_key;
        $params['ts'] = time();

        //generate sig
        $strParam = '';
        foreach ($params as $key=>$val) {
            $strParam .= $key . $val;
        }
        $sig = md5($strParam.$this->app_key);
        $params['device'] = 0;
        $params['userip'] = $this->getClientIP();
        $params['sig'] = $sig;

        $data = $this->post_request_pay('v2/r/qzone/qz_pay_confirm', $params);
        if ($data) {
            $data = json_decode($data, true);
        }
    	return $data;
    }

    public function post_request_pay($method, $params)
    {
        $post_string = $this->create_post_string($method, $params);
        if (MEMCACHED_SECTION_NUM == 48) {
            //qzone支付的正式IP为：113.108.86.20；
            $this->set_api_address('http://113.108.86.20');
        }
        else {
            //qzone支付的测试IP为：119.147.19.43->119.147.75.204。
            $this->set_api_address('http://119.147.75.204');
        }
        $url = $this->get_api_address($method);

//info_log($url . '?' . $post_string, 'debugqzonepay');


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max connect time
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Qzone_Rest_Abstract::CONNECT_TIMEOUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, Qzone_Rest_Abstract::TIMEOUT);
        //cache dns 1 hour
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, Qzone_Rest_Abstract::DNS_CACHE_TIMEOUT);

        curl_setopt($ch, CURLOPT_USERAGENT, Qzone_Rest_Abstract::USERAGENT);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $retries = Qzone_Rest_Abstract::RETRIES;
        $result = false;
        while (($result === false) && (--$retries > 0)) {
			$result = @curl_exec($ch);
		}

        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);

        if ($errno != CURLE_OK) {
            throw new Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //print_r($result);
        return $result;
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

}