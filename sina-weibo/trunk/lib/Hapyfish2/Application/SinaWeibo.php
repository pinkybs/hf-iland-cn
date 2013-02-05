<?php

require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_SinaWeibo extends Hapyfish2_Application_Abstract
{
    protected $_rest;

    protected $_puid;

    protected $_session_key;

    protected $_hfskey;

    protected $newuser;

    public $_cntFidBefore;


    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Hapyfish2_Application_SinaWeibo
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_SinaWeibo($actionController);
        }

        return self::$_instance;
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

    public function getSKey()
    {
    	return $this->_hfskey;
    }

    public function getSessionKey()
    {
    	return $this->_session_key;
    }

    protected function _getUser($data)
    {
        $user = array();
        $user['uid'] = $this->_userId;
        $user['puid'] = $data['uid'];
        $user['name'] = $data['name'];
        $user['gender'] = $data['gender'];
        $user['verified'] = $data['verified'];
        $user['figureurl'] = $data['headurl'];
        return $user;
    }

    protected function _updateInfo()
    {
    	$userData = $this->_rest->getUser($this->_puid);
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

        $this->_cntFidBefore = Hapyfish2_Platform_Bll_Friend::getFriendCount($uid);
        $fids = $this->_rest->getAppFriendIds();

        if ($fids !== null) {
        	//这块可能会出现效率问题，fids很多的时候，memcacehd get次数会很多
        	//优化方案，先根据fid切分到相应的memcached组，用getMulti方法，减少次数
        	$cntAppFids = count($fids);
        	$fids = Hapyfish2_Platform_Bll_User::getUids($fids);
			if ($this->newuser) {
        		Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
        	} else {
        		Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        		//Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
        	}
        }

        //是否新浪V用户
        $verified = Hapyfish2_Platform_Cache_User::getVerified($uid);
        if ($verified != $user['verified']) {
            Hapyfish2_Platform_Cache_User::updateVerified($uid, $user['verified']);
            Hapyfish2_Platform_Cache_User::updateHFCUser($uid);
        }
    }

	/**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $sessionKey = $_REQUEST[SinaWeibo_Weiyouxi::PREFIX_PARAM.'session_key'];
        $puid = $_REQUEST[SinaWeibo_Weiyouxi::PREFIX_PARAM.'user_id'];
        $signature = $_REQUEST[SinaWeibo_Weiyouxi::PREFIX_PARAM.'signature'];
        if (empty($sessionKey)) {
            throw new Exception('empty session_key from sina');
        }
        if (empty($puid)) {
            throw new Exception('empty user_id from sina');
        }
        if (empty($signature)) {
            throw new Exception('empty signature from sina');
        }

        try {
            $this->_rest = SinaWeibo_Client::getInstance();
            $this->_rest->setUser($sessionKey);

            $this->_puid = $puid;
            $this->_session_key = $sessionKey;
            $this->_appId = APP_ID;
            $this->_appName = APP_NAME;
            $this->newuser = false;
        }
        catch (Exception $e) {
            throw new Exception('Hapyfish2_Application_SinaWeibo--_init Err:'. $e->getMessage());
        }

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

        $sig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);

        $skey = $uid . '.' . $puid . '.' . base64_encode($session_key) . '.' . $t . '.' . $rnd . '.' . $sig;
        $this->_hfskey = $skey;
        setcookie('hf_skey', $skey , 0, '/', str_replace('http://', '.', HOST));
    }

}