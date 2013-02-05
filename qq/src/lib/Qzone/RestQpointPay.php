<?php

require_once 'Qzone/Rest/QpointPay.php';

class Qzone_RestQpointPay
{
    public $api_key;
    public $app_id;
    public $app_name;
    public $user_id;

    /**
     * Qpoint pay api call object
     *
     * @var Qzone_RestQpointPay
     */
    public $rest;

    protected $err;

    protected $code;

    protected static $_instance;

    public function __construct($app_id, $app_key, $app_name)
    {
        $this->app_id = $app_id;
    	$this->api_key = $app_key;
    	$this->app_name = $app_name;
        $this->rest = new Qzone_Rest_QpointPay($app_id, $app_key, $app_name);
        $this->err = false;
        $this->code = 0;
    }

    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->rest->set_User($user_id, $session_key);
    }

    /**
     * single instance of Qzone_RestQzone
     *
     * @return Qzone_RestQzone
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_ID, APP_KEY, APP_NAME);
        }

        return self::$_instance;
    }

    protected function clearErr()
    {
    	$this->err = false;
    	$this->code = 0;
    }

    public function isErr()
    {
    	return $this->err;
    }

    public function getCode()
    {
    	return $this->code;
    }

    public function getQpointPayToken($id, $price, $num, $img, $des)
    {
        $this->clearErr();
    	try {
    	    $payitem = $id.'*'.$price.'*'.$num;
    	    $des = empty($des) ? '' : $des;
            $data = $this->rest->qpoint_buy($payitem, 1, $des, $img);
            if ($data['ret']==0 && isset($data['token']) && isset($data['url_params'])) {
            	return $data;
            }
            $this->code = $data['ret'];
            info_log('getQpointPayToken:'.$data['msg'].'|'.$this->code, 'qpointPay_err');
        }
        catch (Exception $e) {
            info_log('getQpointPayToken:'.$e->getMessage(), 'qpointPay_err');
        }

        $this->err = true;
        return null;
    }

}