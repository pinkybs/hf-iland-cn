<?php

require_once 'SinaWeibo/Weiyouxi.php';

class SinaWeibo_Client
{
    protected $appKey;
    protected $appSecret;
    protected $appId;
    protected $appName;
    protected $userId;
    protected $rest;

    protected static $_instance;

    /**
     * get client object
     *
     * @return SinaWeibo_Client
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_KEY, APP_SECRET);
        }

        return self::$_instance;
    }

    public function __construct($appKey, $appSecret)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->rest = new SinaWeibo_Weiyouxi($appKey, $appSecret);
    }

    public function isCurlBegun()
    {
        return $this->rest->getCurlBegunStatus();
    }

    public function setUser($sessionKey)
    {
        $this->rest->setAndCheckSessionKey($sessionKey);
    }

    public function getUserId()
    {
        try {
            $userId = $this->rest->getUserId();
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getUserId]: ' . $e->getMessage(), 'wb_api_call_err');
            return null;
        }

        $this->userId = $userId;
        return $userId;
    }

    //获取某用户的个人信息
    public function getUser($userId)
    {
        try {
        	$data = $this->rest->get('user/show', array('uid' => $userId));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getUser]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
//info_log(Zend_Json::encode($data), 'sina_user');
                $user = array();
                $user['uid'] = $data['id'];
                $user['name'] = $data['name'];
                $gender = -1;
                if ($data['gender'] == 'f') {
                    $gender = 0;
                }
                if ($data['gender'] == 'm') {
                    $gender = 1;
                }
                $user['gender'] = $gender;
                $user['verified'] = $data['verified'] ? 1 : 0;
                $user['headurl'] = $data['profile_image_url'];

                return $user;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getUser]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取某用户的粉丝数
    public function getFollowerCount($userId)
    {
        try {
        	$data = $this->rest->get('user/show', array('uid' => $userId));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getFollowerCount]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
				$FollowerCount = $data['followers_count'];
                return $FollowerCount;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getFollowerCount]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取当前用户的互粉好友ID
    public function getFriendIds()
    {
        try {
        	$data = $this->rest->get('user/friend_ids');
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getFriendIds]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }

                return $data['ids'];
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getFriendIds]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取当前用户安装了当前应用的互粉好友ID 返回所有结果(不分页)
    public function getAppFriendIds()
    {
        try {
        	$data = $this->rest->get('user/app_friend_ids');
//info_log(Zend_Json::encode($data), 'sina_app_friend');
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getAppFriendIds]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                //return $data['ids'];
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getAppFriendIds]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //判断是否是本应用的用户(是否安装了本应用)
    public function isAppUser($userId)
    {
        try {
        	$data = $this->rest->get('application/is_user', array('uid' => $userId));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::isAppUser]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data['flag'] == 1;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::isAppUser]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //
    public function getFriendsCnt()
    {
        try {
            $params = array('page'=>1, 'count'=>10, 'trim'=>1);
        	$data = $this->rest->get('user/friends', $params);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getFriends]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data['total_number'];
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getFriendsCnt]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取当前用户的互粉好友信息
    public function getFriends($page=1, $size=20)
    {
        try {
            $params = array('page'=>$page, 'count'=>$size, 'trim'=>1);
        	$data = $this->rest->get('user/friends', $params);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getFriends]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getFriends]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //粉丝列表
    public function getFollower($page=1, $size=50)
    {
        try {
            $params = array('page'=>$page, 'count'=>$size);
        	$data = $this->rest->get('user/followers', $params);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getFollower]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getFollower]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //关注者列表
    public function getFollowing($page=1, $size=50)
    {
        try {
            $params = array('page'=>$page, 'count'=>$size);
        	$data = $this->rest->get('user/following', $params);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getFollowing]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getFollowing]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //判断当前用户是否是本应用微博的粉丝
    public function isFans()
    {
        try {
        	$data = $this->rest->get('application/is_fan');
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::isFans]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data['flag'] == 1;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::isFans]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //设置成就
    public function setAchieve($aid)
    {
        try {
        	$data = $this->rest->post('achievements/set', array('achv_id' => $aid));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::setAchieve]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::setAchieve]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获得成就
    public function listAchieve()
    {
        try {
        	$data = $this->rest->post('achievements/get');
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::listAchieve]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::listAchieve]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //设置排行榜(数值)
    public function setRank($rankId, $value)
    {
        try {
        	$data = $this->rest->post('leaderboards/set', array('rank_id' => $rankId, 'value' => $value));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::setRank]: '.$rankId.'|'.$value.'-'.$data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::setRank]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //排行计数累加
    public function increaseRank($rankId, $value)
    {
        try {
        	$data = $this->rest->post('leaderboards/increment', array('rank_id' => $rankId, 'value' => $value));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::increaseRank]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::increaseRank]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取好友排行榜
    public function getRank($rankId)
    {
        try {
        	$data = $this->rest->get('leaderboards/get_friends', array('rank_id' => $rankId));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getRank]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getRank]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取总排行
    public function rankAll($rankId)
    {
        try {
        	$data = $this->rest->get('leaderboards/get_total', array('rank_id' => $rankId));
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::rankAll]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::rankAll]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //发送单条通知
    public function sendNotice($uids, $title, $content)
    {
        try {
            if (mb_strlen($title, 'UTF-8') > 30) {
                $title = mb_substr($title, 0, 30, 'UTF-8');
            }
            if (mb_strlen($content, 'UTF-8') > 300) {
                $content = mb_substr($content, 0, 300, 'UTF-8');
            }
            $aryParam = array('uids'=>$uids, 'title'=>$title, 'content'=>$content);
        	$data = $this->rest->post('notice/send', $aryParam);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::sendNotice]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::sendNotice]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //用户忽略该应用的所有邀请
    public function ignoreAllInvite()
    {
        try {
        	$data = $this->rest->get('invite/ignore_game_all.json');
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::ignoreAllInvite]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::ignoreAllInvite]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //判断用户是否已经为本应用打分
    public function hasScored()
    {
        try {
        	$data = $this->rest->get('application/scored');
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::hasScored]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data['flag'];
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::hasScored]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //获取用户参与活动的信息。活动期间 ， 用户进入游戏时需调用。
    public function engageStatus($eid)
    {
        try {
            $aryParam = array('eid' => $eid);
        	$data = $this->rest->get('engage/get_user_status', $aryParam);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::engageStatus]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::engageStatus]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    /* 支付API */
    //获得支付token
    public function getPayToken($orderId, $amount, $desc, $sign)
    {
        try {
            $aryParam = array('order_id'=>$orderId, 'amount'=>$amount, 'desc'=>$desc, 'sign'=>$sign);
        	$data = $this->rest->post('pay/get_token', $aryParam);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getPayToken]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                //"token":"***", "order_uid":"***"
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getPayToken]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

    //查询订单状态
    public function getPayStatus($orderId, $puid, $appId, $sign)
    {
        try {
            $aryParam = array('order_id'=>$orderId, 'user_id'=>$puid, 'app_id'=>$appId, 'sign'=>$sign);
        	$data = $this->rest->get('pay/order_status', $aryParam);
            if(isset($data)) {
                if (isset($data['error_code'])) {
                    info_log('[SinaWeibo_Client::getPayStatus]: ' . $data['error_code'].$data['error'], 'wb_api_call_err');
                    return null;
                }
                //内容要求：订单状态信息0进行中，1已经成功支付 (订单不存在将抛出异常)
                //"order_id":"***","amount":"100","order_uid":"1234455555","order_status":"0/1",
                return $data;
            }
        }
        catch (Exception $e) {
            info_log('[SinaWeibo_Client::getPayStatus]: ' . $e->getMessage(), 'wb_api_call_err');
        }

        return null;
    }

}