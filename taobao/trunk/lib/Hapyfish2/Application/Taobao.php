<?php

require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_Taobao extends Hapyfish2_Application_Abstract
{
    protected $_rest;

    protected $_puid;

    protected $_session_key;

    protected $_hfskey;

    protected $newuser;

    protected $top_params;

    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Hapyfish2_Application_Taobao
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_Taobao($actionController);
        }

        return self::$_instance;
    }

    public function get_valid_top_params($params, $namespace = 'top')
    {
        if (empty($params)) {
            return array();
        }

        $prefix = $namespace . '_';
        $prefix_len = strlen($prefix);
        $top_params = array();

        $sign_key = $prefix . 'sign';

        foreach ($params as $name => $val) {
            if ($name != $sign_key) {
                $top_params[substr($name, $prefix_len)] = $val;
            }
        }

        // validate that the params match the signature
        $signature = isset($params[$sign_key]) ? $params[$sign_key] : null;

        $sig_params = array(
        	'appkey' => $top_params['appkey'],
        	'session' => $top_params['session'],
        	'parameters' => $top_params['parameters']
        );

        //20120330 修复get请求方式 +号变成空格的bug by zx  ---start
        $signature = str_replace(' ', '+', $signature);
        $sig_params['parameters'] = str_replace(' ', '+', $sig_params['parameters']);
        //20120330 修复get请求方式 +号变成空格的bug by zx  ---end

        if (!$signature || (!$this->_rest->verifySignature($sig_params, $signature))) {
            err_log('sig validate failed:sig:'.$signature.' str:'.$top_params['parameters']);
            return array();
        }

        parse_str(base64_decode($top_params['parameters']), $parameters);
        $top_params['parameters'] = $parameters;

        if (!$this->validate_ts($parameters['ts'])) {
            err_log('top_ts validate failed:'.date('Ymd H:i:s', (int)($parameters['ts']/1000)));
        	return array();
        }

        return $top_params;
    }

    public function validate_top_params()
    {
        $this->top_params = $this->get_valid_top_params($_GET);

        if (!$this->top_params) {
            $this->top_params = $this->get_valid_top_params($_POST);
        }

        return !empty($this->top_params);
    }

    public function validate_ts($ts)
    {
    	if(empty($ts)) {
    		return false;
    	}

    	$now = floor(microtime(true)*1000);
    	$span = $now - $ts;
    	//ts span allow range [-15min, 15min]
    	if (abs($span) > 3600000) {
    		return false;
    	}

    	return true;
    }

    public function getPlatformUid()
    {
    	return $this->_puid;
    }

    public function getRest()
    {
    	return $this->_rest;
    }

    public function isNewUser()
    {
    	return $this->newuser;
    }

    protected function _getUser($data)
    {
        $user = array();
        $user['uid'] = $this->_userId;
        $user['puid'] = $data['uid'];
        $user['name'] = $data['name'];
        $user['nick'] = $data['nick'];
        $user['figureurl'] = $data['headurl'];
        $sex = isset($data['sex']) ? $data['sex'] : '';
        if ($sex == '1') {
            $gender = 1;
        } else if ($sex == '0') {
            $gender = 0;
        } else {
            $gender = -1;
        }
        $user['gender'] = $gender;

        return $user;
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $request = $this->getRequest();
        $appkey = $this->top_params['appkey'];
        $this->_rest = Taobao_Rest::getInstance();

        if (!$this->_rest) {
            debug_log('taobao rest error');
            throw new Exception('system error');
        }

        if (!$this->validate_top_params()) {
            throw new Exception('signature error:');
        }

        $uid = $this->top_params['parameters']['visitor_id'];

        //OK
        $this->_rest->setUser($uid, $this->top_params['session']);
        $this->_session_key = $this->top_params['session'];
        $this->_puid = $uid;
        $this->_appId = $appkey;
        $this->_appName = APP_NAME;
        $this->newuser = false;
    }

    protected function _updateInfo()
    {
    	$userData = $this->_rest->jianghu_getUser();

    	if (!$userData) {
    		throw new Hapyfish2_Application_Exception('get user info error');
    	}

    	$puid = $this->_puid;
    	if ($puid != $userData['uid']) {
    		throw new Hapyfish2_Application_Exception('platform uid error');
    	}

    	try {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
    		//first coming
    		if (!$uidInfo) {
    			$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    			if (!$uidInfo) {
    				throw new Hapyfish2_Application_Exception('generate user id error');
    			}
    			$this->newuser = true;
    		}
    	} catch (Exception $e) {
    		throw new Hapyfish2_Application_Exception('get user id error');
    	}

        $uid = $uidInfo['uid'];
        if (!$uid) {
        	throw new Hapyfish2_Application_Exception('user id error');
        }

        $this->_userId = $uid;

        $user = $this->_getUser($userData);
        if ($this->newuser) {
        	Hapyfish2_Platform_Bll_User::addUser($user);
        	//add log
        	$logger = Hapyfish2_Util_Log::getInstance();
        	$logger->report('100', array($uid, $puid, $user['gender']));
        } else {
        	Hapyfish2_Platform_Bll_User::updateUser($uid, $user, true);
        }

        $fids = $this->_rest->jianghu_getFriendIdsByPage();

        if ($fids !== null) {
        	//这块可能会出现效率问题，fids很多的时候，memcacehd get次数会很多
        	//优化方案，先根据fid切分到相应的memcached组，用getMulti方法，减少次数
        	$fids = Hapyfish2_Platform_Bll_User::getUids($fids);
			if ($this->newuser) {
        		Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
        	} else {
        		Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        		//Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
        	}
        }

        //get top more user info
        $userFields = array('user_id', 'nick', 'type', 'has_shop', 'vip_info',
        					'created', 'birthday', 'email', 'alipay_account', 'alipay_no');

        $moreInfo = $this->_rest->top_getUser(implode(',', $userFields));
        if ($moreInfo) {
            $info = array();
            $info['uid'] = $uid;
            $detail = array();
            $detail['user_id'] = $puid;
            foreach ($moreInfo as $key=>$val) {
                if ($val) {
                    //注册时间
                    if ($key == 'created' || $key== 'birthday') {
                        $val = strtotime($val).'';
                    }
                    $detail[$key] = (string)$val;
                }
            }

            $info['info'] = $detail;
            Hapyfish2_Platform_Bll_UserMore::updateInfo($uid, $info, true);
        }

        $combinedSession = $this->_session_key . '_' . $this->top_params['parameters']['ts'];
        Hapyfish2_Platform_Bll_UserMore::updateUserSessionKey($uid, $combinedSession);

        //贴图
       /* $album_id = Hapyfish2_Platform_Cache_User::getVUID($uid);
        if (empty($album_id)) {
            $album_id = $this->_rest->jianghu_getAlubmId();
            Hapyfish2_Platform_Cache_User::updateVUID($uid, $album_id);
        }*/
    }

    public function getSKey()
    {
    	return $this->_hfskey;
    }

    /**
     * run() - main mothed
     *
     * @return void
     */
    public function run()
    {
		$this->_updateInfo();

        //P3P privacy policy to use for the iframe document
        //for IE
        header('P3P: CP=CAO PSA OUR');

        $uid = $this->_userId;
        $puid = $this->_puid;
        $session_key = $this->_session_key;
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);

        $sig = md5($uid . $puid . $session_key . $t . $rnd . APP_KEY);

        $skey = $uid . '.' . $puid . '.' . base64_encode($session_key) . '.' . $t . '.' . $rnd . '.' . $sig;
        $this->_hfskey = $skey;
        setcookie('hf_skey', $skey , 0, '/', str_replace('http://', '.', HOST));
    }
}