<?php
require_once 'SinaWeibo/saet2.ex.class.php';

class SinaWeibo_Weiboapp
{
    protected $appKey;
    protected $appSecret;
    protected $appName;
    protected $userId;
    protected static $rest;

    public function __construct($appKey, $appSecret)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->rest = null;
    }

    /**
     * get rest
     *
     * @return SinaWeibo_Client
     */
    public function getRest()
    {
        return $this->rest;
    }

    public function setRest($access_token, $refresh_token)
    {
        $this->rest = new SaeTClient($this->appKey, $this->appSecret, $access_token, $refresh_token);
    }

    public function getUser($userId)
    {
        if ($this->rest == null) {
            throw new Exception('rest is null,please set it first');
        }
        $data = $this->rest->show_user($userId);
        if ($data === false || $data === null) {
            throw new Exception('get data failed');
        }
        if (isset($data['error_code']) && isset($data['error'])) {
            throw new Exception($data['error_code'].':'.$data['error']);
        }
        return $data;
    }

    public function create_post_string($params)
    {
        $post_params = array();
        foreach ($params as $key => &$val) {
            $post_params[] = $key . '=' . urlencode($val);
        }
        return implode('&', $post_params);
    }

    //{"access_token":"6161345e62e1d11763443ff586974fba","expires_in":7200,"refresh_token":"e1dd956936cd3e0f618ef63c2c1f11b9"}
    public function getAccessTokenByPass($username, $password)
    {
        $ch = curl_init();
        $url = 'https://api.t.sina.com.cn/oauth2/access_token';
        $postParam = array('client_id' => $this->appKey, 'client_secret' => $this->appSecret,
        'grant_type' => 'password', 'username' => $username, 'password' => $password);
        $postBody = $this->create_post_string($postParam);
        $method = 'POST';
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($postBody) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
        }
        // We need to set method even when we don't have a $postBody 'DELETE'
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $cURLVersion = curl_version();
        $ua = 'PHP-cURL/' . $cURLVersion['version'] . ' ';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $retries = 3;
        $response = false;
        while (($response === false) && (-- $retries > 0)) {
            $response = @curl_exec($ch);
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = @curl_errno($ch);
        //$error = @curl_error($ch);
        @curl_close($ch);
        $result = null;
        if ($response) {
            $result = json_decode($response, true);
        }
        return $result;
    }
}