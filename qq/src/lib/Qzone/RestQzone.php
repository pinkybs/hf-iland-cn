<?php

require_once 'Qzone/Rest/Abstract.php';
require_once 'Qzone/Rest/Qzone.php';

class Qzone_RestQzone
{
    public $api_key;
    public $app_id;
    public $app_name;
    public $user_id;

    /**
     * Qzone rest api call object
     *
     * @var Qzone_Rest_Qzone
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
        $this->rest = new Qzone_Rest_Qzone($app_id, $app_key, $app_name);
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

    /**
     * check user whether installed app
     *
     * @return boolean true|false
     */
    public function isAppUser()
    {
        $this->clearErr();
    	try {
            $result = $this->rest->user_isAppUser();
            if(isset($result['setuped'])) {
                return $result['setuped'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::isAppUser]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function getUser()
    {
        $this->clearErr();
    	try {
            return $this->rest->user_getProfile();
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::getUser]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function getAppFriendIds()
    {
        $this->clearErr();
    	try {
            $data = $this->rest->user_getAppFriendIds();

			$fids = array();
			if (!empty($data['items'])) {
				foreach ($data['items'] as $item) {
					//filter
					if ($item['openid'] != $this->user_id) {
						$fids[] = $item['openid'];
					}
				}
			}

			return $fids;
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::getAppFriendIds]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function isVip()
    {
        $this->clearErr();
    	try {
            $result = $this->rest->pay_isvip();
            if(isset($result['is_vip'])) {
                return $result['is_vip'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::isVip]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function getPayBalance($needVip = false)
    {
        $this->clearErr();
    	try {
            $data = $this->rest->pay_getBalance();
            if ($data['ret']==0 && isset($data['balance'])) {
                if (!$needVip) {
					return $data['balance'];
            	} else {
            		return array('balance' => $data['balance'], 'is_vip' => $this->isVip());
            	}
            }
            $this->code = $data['ret'];
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::getPayBalance]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

	public function pay($items, $amt)
    {
        $this->clearErr();
    	try {
            $result = $this->rest->pay_pay($items, $amt);
            if($result['ret']==0 && isset($result['billno'])) {
                return $result['billno'];
            }
            $this->code = $result['ret'];
            info_log('Qzone_RestQzone.pay:'.$result['msg'].'|'.$result['ret'], 'qzone_pay_api_err');
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::pay]' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }

    public function payConfirm($billno, $amt)
    {
        $this->clearErr();
    	try {
            $result = $this->rest->pay_confirm($billno, $amt);
    	    if($result['ret']==0) {
                return true;
            }
            $this->code = $result['ret'];
            info_log('Qzone_RestQzone.payConfirm:'.$result['msg'].'|'.$result['ret'], 'qzone_pay_api_err');
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::payConfirm]' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }

    public function payCancel($billno, $amt)
    {
        $this->clearErr();
    	try {
            $result = $this->rest->pay_cancel($billno, $amt);
    	    if($result['ret']==0) {
                return true;
            }

            $this->code = $result['ret'];
            info_log('Qzone_RestQzone.payCancel:'.$result['msg'].'|'.$result['ret'], 'qzone_pay_api_err');
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_RestQzone::payCancel]' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }

}