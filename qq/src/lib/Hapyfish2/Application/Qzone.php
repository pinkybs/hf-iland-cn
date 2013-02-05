<?php

require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_Qzone extends Hapyfish2_Application_Abstract
{
    const QZONE_CODE = 1;
    const PENGYOU_CODE = 2;

    protected $_qzone;

    protected $_openid;

    protected $_openkey;

    protected $_hfskey;

    protected $_isQzone;

    public $newuser;

    public $params;

    public $invite;

    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Bll_Application
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_Qzone($actionController);
        }

        return self::$_instance;
    }

    public function get_params()
    {
		$this->params = array(
			'openid' => $_GET['openid'],
			'openkey' => $_GET['openkey']
		);

		if (isset($_GET['invkey']) && isset($_GET['itime']) && isset($_GET['iopenid'])) {
			$myopenid = $_GET['openid'];
			$iopenid = $_GET['iopenid'];
			$itime = $_GET['itime'];
			$appid = APP_ID;
			$appkey = APP_KEY;
			$validKey = md5($myopenid . '_' . $iopenid . '_' . $appid . '_' . $appkey . '_' . $itime);
			if ($_GET['invkey'] == $validKey) {
				$this->invite = true;
				$this->params['iopenid'] = $iopenid;
			}
		}

		return $this->params;
    }

    public function isNewUser()
    {
    	return $this->newuser;
    }

    public function isFromQzone()
    {
    	return $this->_isQzone;
    }

    protected function _getUser($data)
    {
        $user = array();
        $user['uid'] = '' . $this->_userId;
        $user['openid'] = $this->_openid;
        $user['nickname'] = $data['nickname'];
        $faceUrl = $data['figureurl'];
        if (strpos($data['figureurl'], 'http://') === false) {
            $faceUrl = 'http://' . $faceUrl;
        }
        $user['figureurl'] = $faceUrl;
        $sex = isset($data['gender']) ? $data['gender'] : '';
        if ($sex == '男') {
            $gender = 1;
        } else if ($sex == '女') {
            $gender = 0;
        } else {
            $gender = -1;
        }
        $user['gender'] = $gender;
        $user['is_vip'] = 0;
        if (isset($data['is_vip']) && $data['is_vip']) {
        	$user['is_vip'] = 1;
        }
		$user['is_year_vip'] = 0;
        if (isset($data['is_year_vip']) && $data['is_year_vip']) {
        	$user['is_year_vip'] = 1;
        }
		$user['vip_level'] = 0;
        if (isset($data['vip_level']) && $data['vip_level']) {
        	$user['vip_level'] = $data['vip_level'];
        }

        return $user;
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        //if is from qzone
        $this->_isQzone = (defined('PLATFORM_SOURCE') && '1' == PLATFORM_SOURCE);

        $this->invite = false;

    	//$this->_qzone = Qzone_Rest::getInstance();
    	$this->_qzone = Qzone_Factory::getRest();
        $this->get_params();
        $openid = $this->params['openid'];
        $openkey = $this->params['openkey'];

        $this->_qzone->setUser($openid, $openkey);
        $this->_openid = $openid;
        $this->_openkey = $openkey;
        $this->_appId = $this->_qzone->app_id;
        $this->_appName = $this->_qzone->app_name;
        $this->newuser = false;
    }

    protected function _updateInfo()
    {
    	$userData = $this->_qzone->getUser();
//info_log(json_encode($userData), 'qzone_user');
//exit;
    	if (!$userData) {
    		throw new Hapyfish2_Application_Exception('get user info error');
    	}

    	$puid = $this->_openid;
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
    		//info_log($e, 'getUserIDErr');
    		throw new Hapyfish2_Application_Exception('get user id error');
    	}

        $uid = $uidInfo['uid'];
        if (!$uid) {
        	throw new Hapyfish2_Application_Exception('user id error');
        }

        $this->_userId = $uid;

        $platformType = $this->_isQzone ? self::QZONE_CODE : self::PENGYOU_CODE;

        $user = $this->_getUser($userData);
        if ($this->newuser) {
        	Hapyfish2_Platform_Bll_Factory::addUser($user);
        	//add log
        	$logger = Hapyfish2_Util_Log::getInstance();
        	$logger->report('100', array($uid, $puid, $user['gender'], $platformType));
            //isource =2 xiaoyou  =1 qzone
        	$logInfo = array('openid' => $puid, 'iSource' => $platformType, 'iCmd' => 100, 'iState' => 0, 'ownerUid' => $uid);
        	$logger = Qzone_Log::getInstance();
        	//$logger->setLogFile(LOG_DIR . '/report.log');
        	$logger->report($uid, $logInfo);
        } else {
            $pUser = Hapyfish2_Platform_Bll_Factory::getUser($uid);
            if (empty($pUser) || empty($pUser['openid'])) {
                Hapyfish2_Platform_Bll_Factory::addUser($user);
            }
            else {
                Hapyfish2_Platform_Bll_Factory::updateUser($uid, $user, true);
            }
        }

        $fids = $this->_qzone->getAppFriendIds();

        if ($fids !== null) {
        	//这块可能会出现效率问题，fids很多的时候，memcacehd get次数会很多
        	//优化方案，先根据fid切分到相应的memcached组，用getMulti方法，减少次数
        	$fids = Hapyfish2_Platform_Bll_Factory::getUids($fids);
			if ($this->newuser) {
        		Hapyfish2_Platform_Bll_Factory::addFriend($uid, $fids);
        	} else {
        	    $pFriend = Hapyfish2_Platform_Bll_Factory::getFriend($uid);
        	    if (empty($pFriend)) {
        	        Hapyfish2_Platform_Bll_Factory::addFriend($uid, $fids);
        	    }
        	    else {
        		    Hapyfish2_Platform_Bll_Factory::updateFriend($uid, $fids);
        	    }
        	}
        }
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
        $openid = $this->_openid;
        $openkey = $this->_openkey;
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);

        $sig = md5($uid . $openid . $openkey . $t . $rnd . APP_KEY);

        $skey = $uid . '_' . $openid . '_' . $openkey . '_' . $t . '_' . $rnd . '_' . $sig;
        $this->_hfskey = $skey;

        setcookie('hf_skey', $skey , 0, '/', str_replace('http://', '.', HOST));
    }

}