<?

class Qzone_Rest_QpointPay
{
    public $app_id;
	public $app_key;
	public $app_name;
    public $open_id;
    public $open_key;
    public $v;
    public $server_addr;

    const CONNECT_TIMEOUT = 2;
    const TIMEOUT = 3;
    const DNS_CACHE_TIMEOUT = 600;
    const RETRIES = 3;
    const DEFAULT_SERVICE_VERSION = '1.0';
    const USERAGENT = 'PHP-cURL/HapyFish-QzoneRest-1.0';

    public function __construct($app_id, $app_key, $app_name)
    {
        $this->app_id = $app_id;
    	$this->app_key = $app_key;
    	$this->app_name = $app_name;
        $this->_init();
    }

    protected function _init()
    {
        //$this->server_addr = 'https://119.147.75.204/v2/r/qzone';
        $this->server_addr = QPOINT_PAY_HOST_BUY.'/v2/r/qzone';
        $this->v = self::DEFAULT_SERVICE_VERSION;
    }

    public function set_User($open_id, $open_key)
    {
        $this->open_id = $open_id;
    	$this->open_key = $open_key;
    }

    protected function set_api_address($addr)
    {
         $this->server_addr = $addr;
    }

    protected function get_api_address($method)
    {
    	return $this->server_addr . '/' . $method;
    }

    /**
     * Q币/Q点直接购买  用户点击按钮选择“通过Q币/Q点购买道具”后，应用发送请求，以获取本次交易的token，以及购买物品的url参数。
     *
     * @param  string $payitem  请使用ID*price*num的格式，长度必须<=255字符。
     * @param  string $appmode  1表示用户不可以修改物品数量，2表示用户可以选择购买物品的数量。默认为2。
     * @param  string $goodsmeta 物品信息，格式必须是“name*des”。name表示物品的名称，des表示物品的描述信息。
     * @param  string $goodsurl  物品的图片url，用户购买物品的确认支付页面将显示该物品图片。长度<=512字符,注意图片规格为：116 * 116 px。
     * @return array
     */
    public function qpoint_buy($payitem, $appmode=2, $goodsmeta, $goodsurl)
    {
        $params = array();
        $params['appid'] = $this->app_id;
        $params['appkey'] = $this->app_key;
        $params['appname'] = $this->app_name;
        $params['openid'] = $this->open_id;
        $params['openkey'] = $this->open_key;
        $params['ts'] = time();

        $params['payitem'] = $payitem;
        $params['appmode'] = $appmode;

        ksort($params);
        //generate sig
        $strParam = '';
        foreach ($params as $key=>$val) {
            $strParam .= $key . $val;
        }
        $sig = md5($strParam.$this->app_key);

        $params['goodsmeta'] = base64_encode($goodsmeta);
        $params['goodsurl'] = $goodsurl;
        $params['device'] = 0;
        $params['userip'] = $this->getClientIP();
        $params['sig'] = $sig;

        return $this->call_method('qz_buy_goods', $params);
    }

    public function call_method($method, $params)
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data);
        if (!$result || !is_array($result)) {
        	throw new Exception('response error, not array');
        }
        if ($result['ret'] != 0) {
            throw new Exception($result['ret'].':'.$result['msg']);
        }

        //unset($result['ret']);
        return $result;
    }

    public function post_request($method, $params)
    {
        $post_string = $this->create_post_string($method, $params);
        $url = $this->get_api_address($method);

//info_log($url . '?' . $post_string, 'qpoint_test');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max connect time
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        //cache dns 1 hour
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, self::DNS_CACHE_TIMEOUT);

        curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $retries = self::RETRIES;
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
//info_log($result, 'qpoint_test');
        //echo $result;
        //print_r($result);
        return $result;
    }

    protected function convert_result($data)
    {
    	return json_decode($data, true);
    }

    protected function create_post_string($method, $params)
    {
        $post_params = array();
        foreach ($params as $key => &$val) {
            $post_params[] = $key.'='.urlencode($val);
        }
        return implode('&', $post_params);
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