<?

class Hapyfish2_Rest_Island extends Hapyfish2_Rest_Abstract
{
    public function noop()
    {
        return $this->call_method('openapi/noop', array());
    }

    public function getUserInfo($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/userinfo', $p);
    }

    public function getUserInfoByPUID($puid)
    {
        return $this->call_method('openapi/userinfobypuid', array('puid' => $puid));
    }

    public function getUserCardInfo($uid)
    {
        return $this->call_method('openapi/usercardinfo', array('uid' => $uid));
    }

    public function getWatchUser($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
    	return $this->call_method('openapi/watchuser', $p);
    }

    public function getCoinLog($uid, $params = array())
    {
        $params['uid'] = $uid;
    	return $this->call_method('openapi/coinlog', $params);
    }

    public function getGoldLog($uid, $params = array())
    {
        $params['uid'] = $uid;
    	return $this->call_method('openapi/goldlog', $params);
    }

    public function getInviteLog($uid)
    {
    	return $this->call_method('openapi/invitelog', array('uid' => $uid));
    }

    public function getLevelUpLog($uid)
    {
    	return $this->call_method('openapi/leveluplog', array('uid' => $uid));
    }

    public function getItemList($type = 0)
    {
    	return $this->call_method('openapi/itemlist', array('type' => $type));
    }

    public function getNotice($type)
    {
    	return $this->call_method('manageapi/getnotice', array('type' => $type));
    }

    public function updateNotice($info)
    {
    	return $this->call_method('manageapi/updatenotice', $info);
    }

    public function sendItem($info)
    {
    	return $this->call_method('manageapi/senditem', $info);
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    public function blockUser($uid)
    {
    	return $this->call_method('manageapi/blockuser', array('uid' => $uid));
    }

    public function unblockUser($uid)
    {
    	return $this->call_method('manageapi/unblockuser', array('uid' => $uid));
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //

    public function getPraise($uid)
    {
    	return $this->call_method('fixapi/praise', array('uid' => $uid, 'view' => '0'));
    }

    public function fixPraise($uid)
    {
    	return $this->call_method('fixapi/praise', array('uid' => $uid, 'view' => '1'));
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //

    public function payhourofday($day, $type = 0)
    {
    	return $this->call_method('statapi/paymentofhour', array('day' => $day, 'type' => $type));
    }

    public function mainofday($startday, $endday)
    {
    	return $this->call_method('statapi/mainofday', array('startday' => $startday, 'endday' => $endday));
    }


    /* cLoadTm */
    public function listcLoadTmData($startday, $endday)
    {
    	return $this->call_method('staticsapi/cloadtm', array('dtBegin' => $startday, 'dtEnd' => $endday));
    }

    public function listPromoteData($startday, $endday)
    {
    	return $this->call_method('staticsapi/promote', array('dtBegin' => $startday, 'dtEnd' => $endday));
    }

    public function listEasyportionData($startday, $endday)
    {
    	return $this->call_method('staticsapi/easyportion', array('dtBegin' => $startday, 'dtEnd' => $endday));
    }

    public function loadpropData($begin, $end, $cid)
    {
    	return $this->call_method('staticsapi/propsale', array('dtBegin' => $begin, 'dtEnd' => $end, 'dtCid'=>$cid));
    }

    /////
    public function getPaysetting()
    {
    	return $this->call_method('paysettingapi/info');
    }

    public function updatePaysetting($info)
    {
    	return $this->call_method('paysettingapi/update', $info);
    }

    /////
    public function refresh($name)
    {
    	return $this->call_method('manageapi/refresh', array('name' => $name));
    }

    public function getLoginInfo($uid)
    {
    	return $this->call_method('openapi/logininfo', array('uid' => $uid));
    }

    public function getPayLog($uid, $params = array())
    {
        $params['uid'] = $uid;
    	return $this->call_method('openapi/paylog', $params);
    }

    public function getDonateLog($uid, $params = array())
    {
        $params['uid'] = $uid;
    	return $this->call_method('openapi/donatelog', $params);
    }

    public function sendFeed($info)
    {
    	return $this->call_method('manageapi/sendfeed', $info);
    }

    public function getAppInfo()
    {
    	return $this->call_method('openapi/appinfo', array());
    }

    public function updateAppInfo($info)
    {
    	return $this->call_method('manageapi/updateappinfo', $info);
    }

    public function getUserPlatformInfo($uid)
    {
    	return $this->call_method('openapi/userplatforminfo', array('uid' => $uid));
    }

    //move data get
    public function getUserInfoAllMD($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/getuserinfoallmd', $p);
    }
    
    public function getUserAchievementMD($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/getuserachievementmd', $p);
    }
    
    public function getUserBackgroundMD($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/getuserbackgroundmd', $p);
    }
    
    public function getUserBuildingMD($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/getuserbuildingmd', $p);
    }
    
    public function getUserCardMD($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/getusercardmd', $p);
    }
    
    public function getUserDockMD($uid, $params = null)
    {
		return $this->call_method('openapi/getuserdockmd', array('uid' => $uid));
    }
    
    public function getUserIslandMD($uid)
    {
		return $this->call_method('openapi/getuserislandmd', array('uid' => $uid));
    }
    
    public function getUserPlantMD($uid, $params = null)
    {
        $p = array('uid' => $uid);
		if (!empty($params)) {
			foreach ($params as $k => $v) {
				$p[$k] = $v;
			}
		}
		return $this->call_method('openapi/getuserplantmd', $p);
    }
    
    //move data update 
    public function updateUserInfoMD($uid, $params = null)
    {	
		return $this->call_method('openapi/updateuserinfomd', array('uid' => $uid, 'params' => $params));
    }
        
    public function updateUserAchievementMD($uid, $params = null)
    {
		return $this->call_method('openapi/updateuserachievementmd', array('uid' => $uid, 'params' => $params));
    }
    
    public function updateUserBackgroundMD($uid, $params = null)
    {    	
        return $this->call_method('openapi/updateuserbackgroundmd', array('uid' => $uid, 'params' => $params));
    }
    
    public function updateUserBuildingMD($uid, $params = null)
    {
        return $this->call_method('openapi/updateuserbuildingmd', array('uid' => $uid, 'params' => $params));
    }
    
    public function updateUserPlantMD($uid, $params = null)
    {
        return $this->call_method('openapi/updateuserplantmd', array('uid' => $uid, 'params' => $params));
    }
    
    public function updateUserCardMD($uid, $cid, $count)
    {
        return $this->call_method('openapi/updateusercardmd', array('uid' => $uid, 'cid' => $cid, 'count' => $count));
    }
    
    public function updateUserDockMD($uid, $position_id, $unlock_ship_ids)
    {
        return $this->call_method('openapi/updateuserdockmd', array('uid' => $uid, 'position_id' => $position_id, 'unlock_ship_ids' => $unlock_ship_ids));
    }
    
    public function updateUserIslandMD($info)
    {
        return $this->call_method('openapi/updateuserislandmd', $info);
    }
}