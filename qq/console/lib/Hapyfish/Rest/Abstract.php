<?

require_once 'Hapyfish/Rest/Exception.php';

class Hapyfish_Rest_Abstract
{
    public $app_id;
	public $app_key;
    public $v;
    public $server_addr;
    public $api_uid;
    
    const CONNECT_TIMEOUT = 2;
    const TIMEOUT = 3;
    const DNS_CACHE_TIMEOUT = 600;
    
    const DEFAULT_SERVICE_VERSION = '1.0';
    const USERAGENT = 'PHP-cURL/HapyFish-Rest-1.0';
    
    public function __construct($app_id, $app_key) 
    {   
        $this->server_addr = 'http://main.island.qzoneapp.com';
    	$this->app_id = $app_id;
    	$this->app_key = $app_key;
        $this->v = self::DEFAULT_SERVICE_VERSION;
    }
    
    public function setUser($api_uid)
    {
    	$this->api_uid = $api_uid;
    }
    
    //===========================================================================================================
        
    public function call_method($method, $params)
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data, $method, $params);
        if (!$result || !is_array($result)) {
        	throw new Hapyfish_Rest_Exception('response error', -1);
        }
        if ($result['errno'] != 0) {
            throw new Hapyfish_Rest_Exception($result['errmsg'], $result['errno']);
        }
        
        unset($result['errno']);
        return $result;
    }
        
    protected function convert_result($data, $method, $params)
    {
    	return json_decode($data, true);
    }
    
    public function create_request_url($method, $params)
    {
        $this->add_standard_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        return $this->server_addr . '?' . $post_string;        
    }
    
    public function post_request($method, $params)
    {
        $this->add_standard_params($method, $params);
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
        $result = @curl_exec($ch);
        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);
        
        if ($errno != CURLE_OK) {
            throw new Hapyfish_Rest_Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //print_r($result);
        return $result;
    }
    
    private function get_api_address($method)
    {
    	return $this->server_addr . '/' . $method;
    }
    
    private function add_standard_params($method, &$params)
    {
        $params['hf_appid'] = $this->app_id;
        $params['hf_appkey'] = $this->app_key;
        $params['hf_apiuid'] = $this->api_uid;
        $params['hf_ver'] = $this->v;
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