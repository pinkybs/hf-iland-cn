<?

require_once 'Hapyfish2/Rest/Exception.php';

class Hapyfish2_Rest_Abstract
{
    public $app_key;
	public $app_secret;
    public $v;
    public $server_addr;
    public $sign_method;
    public $api_uid;
    
    const CONNECT_TIMEOUT = 2;
    const TIMEOUT = 10;
    const DNS_CACHE_TIMEOUT = 600;
    const RETRIES = 3;
    
    const DEFAULT_SERVICE_VERSION = '1.0';
    const USERAGENT = 'PHP-cURL/HapyFish-Rest-1.0';
    
    const SIGN_METHOD_MD5 = 'md5';
    const SING_METHOD_HMAC = 'hmac';
    
    public function __construct($app_key, $app_secret, $addr = null) 
    {
    	$this->app_key = $app_key;
    	$this->app_secret = $app_secret;
    	$this->server_addr = $addr;
        $this->v = self::DEFAULT_SERVICE_VERSION;
        $this->sign_method = self::SIGN_METHOD_MD5;
    }
    
    public function setServerAddr($addr)
    {
    	$this->server_addr = $addr;
    }
    
    public function setUser($api_uid)
    {
    	$this->api_uid = $api_uid;
    }
    
    //===========================================================================================================
        
    public function call_method($method, $params = array())
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data, $method, $params);
        if (!$result || !is_array($result)) {
        	throw new Hapyfish2_Rest_Exception('response error', -1);
        }
        if ($result['errno'] != 0) {
            throw new Hapyfish2_Rest_Exception($result['errmsg'], $result['errno']);
        }
        
        unset($result['errno']);
        return $result;
    }
        
    protected function convert_result($data, $method, $params)
    {
    	return json_decode($data, true);
    }
    
    public function generate_sign($params_array)
    {
        ksort($params_array);

        $sign = '';
        foreach ($params_array as $k => $v) {
            $sign .= "$k$v";
        }

        if ($this->sign_method == self::SING_METHOD_HMAC) {
            $sign = strtoupper(bin2hex(mhash(MHASH_MD5, $sign, $this->app_secret)));
        } else {
            $sign = strtoupper(md5($this->app_secret . $sign . $this->app_secret));
        }

        return $sign;
    }
    
    public function create_request_url($method, $params)
    {
        $this->add_standard_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        return $this->server_addr . '?' . $post_string;        
    }
    
    public function post_request($method, $params)
    {
        $this->finalize_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        
        $url = $this->get_api_address($method);
        //echo $post_string.'<br /><br />';
        //echo $url . '?' . $post_string;

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
            throw new Hapyfish2_Rest_Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //print_r($result);
        return $result;
    }
    
    private function get_api_address($method)
    {
    	return $this->server_addr . '/' . $method;
    }
    
    private function convert_array_values_to_csv(&$params)
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
        }
    }
    
    private function add_standard_params($method, &$params)
    {
        $params['hf_app_key'] = $this->app_key;
        $params['hf_api_uid'] = $this->api_uid;
        $params['hf_ts'] = time();
        $params['hf_v'] = $this->v;
        $params['hf_sign_method'] = $this->sign_method;
    }
    
    private function finalize_params($method, &$params)
    {
        $this->add_standard_params($method, $params);
        //we need to do this before signing the params
        $this->convert_array_values_to_csv($params);
        $params['hf_sign'] = $this->generate_sign($params);
    }
            
    private function create_post_string($method, $params)
    {
        $post_params = array();
        foreach ($params as $key => &$val) {
            $post_params[] = $key.'='.urlencode($val);
        }
        return implode('&', $post_params);
    }
    
}