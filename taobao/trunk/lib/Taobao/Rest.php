<?php

require_once 'Taobao/Rest/Abstract.php';
require_once 'Taobao/Rest/Jianghu.php';
require_once 'Taobao/Rest/Shop.php';
require_once 'Taobao/Rest/Top.php';

class Taobao_Rest
{
    public $api_key;
    public $secret;
    public $app_id;
    public $app_name;
    public $user_id;


    public $jianghu;
    public $shop;
    public $top;

    protected static $_instance;

    public function __construct($api_key, $secret, $app_id, $app_name)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->app_id = $app_id;
        $this->app_name = $app_name;

        $this->jianghu = new Taobao_Rest_Jianghu($api_key, $secret);
        $this->shop = new Taobao_Rest_Shop($api_key, $secret);
        $this->top = new Taobao_Rest_Top($api_key, $secret);
    }

    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->jianghu->session_key = $session_key;
        $this->top->session_key = $session_key;
    }

    /**
     * single instance of Taobao_Rest
     *
     * @return Taobao_Rest
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_KEY, APP_SECRET, APP_ID, APP_NAME);
        }

        return self::$_instance;
    }

    public function verifySignature($top_params, $signature)
    {
        $str = '';
        ksort($top_params);
        foreach ($top_params as $k => $v) {
            $str .= "$v";
        }
        $str .= $this->secret;

        return base64_encode(md5($str,true)) == $signature;
    }

    public function jianghu_getUser()
    {
        try {
            $data = $this->jianghu->user_getProfile();
            if(isset($data['user'])) {
                $user = array();
                $t = $data['user'];
                $user['uid'] = $t['uid'];
                $user['name'] = empty($t['real_name']) ? $t['nick'] : str_replace('{HFCHAR}', '&', $t['real_name']);
                $user['nick'] = $t['nick'];
                $user['sex'] = $t['sex'] == 0 ? 1 : 0;
                $user['tinyurl'] = $t['icons']['icon_40'];
                $user['headurl'] = $t['icons']['icon_120'];
                /*
                try {
                    $shop = $this->shop->shop_get(array('sid'), $t['nick']);
                    if ($shop && isset($shop['shop'])) {
                        $user['shop_id'] = $shop['shop']['sid'];
                    }
                }catch (Exception $e1) {
                    err_log($e1->getMessage());
                }*/

                //$user['shop_id'] = '0';

                //debug_log(json_encode($data['user']));
                return $user;
            }
        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }

        return null;
    }

    public function jianghu_getFriendIds()
    {
        try {
            $data = $this->jianghu->friends_getFriendList();
            if($data && is_array($data)) {
                $fids = array();
                foreach ($data as $v) {
                   $fids[] = $v['uid'];
                }
                return $fids;
            }
        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }

        return null;
    }

    public function jianghu_getPageFriendIds($pageNo, $pageSize = 200)
    {
        $data = $this->jianghu->friends_getFriendList($pageNo, $pageSize);
        if ($data && is_array($data)) {
            $fids = array();
            foreach ($data as $v) {
                $fids[] = $v['uid'];
            }
            return $fids;
        }

        return null;
    }

    public function jianghu_getFriendIdsByPage()
    {
        try {
            $fids = array();
            for($i = 1; $i <= 10; $i++) {
                $tmpIds = $this->jianghu_getPageFriendIds($i);
                if ($tmpIds) {
                    $fids = array_merge($fids, $tmpIds);
                    if (count($tmpIds) < 200) {
                        break;
                    }
                } else {
                	break;
                }
            }

            return $fids;
        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }

        return null;
    }

    public function jianghu_getFriends()
    {
        try {
            $data = $this->jianghu->friends_getFriendList();
            if($data && is_array($data)) {
                $friends = array();
                foreach ($data as $v) {
                   $name = empty($v['real_name']) ? $v['nick'] : str_replace('{HFCHAR}', '&', $v['real_name']);
                   $friends[$v['uid']] = array('uid' => $v['uid'], 'name' => $name, 'thumbnail' => $v['icons']['icon_60']);
                }
                return $friends;
            }
        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }

        return null;
    }

    public function jianghu_getPageFriends($pageNo, $pageSize = 200)
    {
        $data = $this->jianghu->friends_getFriendList($pageNo, $pageSize);
        if($data && is_array($data)) {
            $friends = array();
            foreach ($data as $v) {
               $name = empty($v['real_name']) ? $v['nick'] : str_replace('{HFCHAR}', '&', $v['real_name']);
               $friends[$v['uid']] = array('uid' => $v['uid'], 'name' => $name, 'thumbnail' => $v['icons']['icon_60']);
            }
            return $friends;
        }

        return null;
    }

    public function jianghu_getFriendsByPage()
    {
        try {
            $friends = array();
            for($i = 1; $i <= 10; $i++) {
                $tmpFriends = $this->jianghu_getPageFriends($i);
                if ($tmpFriends) {
                    $friends = array_merge($friends, $tmpFriends);
                    if (count($tmpFriends) < 200) {
                        break;
                    }
                } else {
                	break;
                }
            }

            return $friends;
        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }

        return null;
    }

    public function jianghu_getPageAlbumList($pageNo, $pageSize = 200)
    {
        $data = $this->jianghu->albums_getAlbumList($pageNo, $pageSize);
        if($data && is_array($data)) {
            return $data;
        }
        return null;
    }

    public function jianghu_getAlubmId()
    {
         try {
            $list = array();
            for($i = 1; $i <= 10; $i++) {
                $tmpList = $this->jianghu_getPageAlbumList($i);
                if ($tmpList) {
                    $list = $tmpList;
                    if (count($tmpFriends) < 200) {
                        break;
                    }
                } else {
                	break;
                }
            }

            if (!empty($list)) {
                for($i = 0, $len = count($list); $i < $len; $i++) {
                    if ($list[$i]['type'] == '2'  && $list[$i]['name'] = '贴图相册') {
                        return $list[$i]['album_id'];
                    }
                }
            }

        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }

        return null;
    }


	public function jianghu_getVasIsvUrl($params)
    {
        try {
	        $data = $this->jianghu->get_vas_isv_url($params);
	        if($data && is_array($data)) {
	            return $data;
	        }
        }
        catch (Exception $e) {
            info_log('jianghu_getVasIsvUrl:'.$e->getMessage(), 'api_call_failed'.date('Ymd'));
            //err_log($e->getMessage());
        }

        return null;
    }

	public function jianghu_getVasIsvInfo($outer_order_id, $proxy_code, $buyer_time)
    {
        try {
	        $data = $this->jianghu->get_vas_isv_info($outer_order_id, $proxy_code, $buyer_time);
	        if($data && is_array($data)) {
	            return $data;
	        }
        }
        catch (Exception $e) {
            info_log('jianghu_getVasIsvInfo:'.$e->getMessage(), 'api_call_failed'.date('Ymd'));
            //err_log($e->getMessage());
        }

        return null;
    }

    public function jianghu_getCoinsSum()
    {
        try {
	        $data = $this->jianghu->coins_sum();
	        if ($data && is_array($data) && isset($data['sum'])) {
	            return $data['sum'];
	        }
        }
        catch (Exception $e) {
            info_log('jianghu_getCoinsSum:'.$e->getMessage(), 'api_call_failed'.date('Ymd'));
            //err_log($e->getMessage());
        }

        return null;
    }

    //参数$count-兑换淘金币数（每天最多200个）
    public function jianghu_coinsConsume($count)
    {
        try {
	        $data = $this->jianghu->coins_consume($count);
	        return $data;
        }
        catch (Exception $e) {
            info_log('jianghu_coinsConsume:'.$e->getMessage(), 'api_call_failed'.date('Ymd'));
            //err_log($e->getMessage());
        }

        return null;
    }

    public function top_getUser($fields = null, $nick = null)
    {
        try {
	        $data = $this->top->user_get($fields, $nick);
	        if ($data && $data['user']) {
                return $data['user'];
	        }
	        else {
	            if ($data) {
	                info_log('top_getUser:'.json_encode($data), 'api_call_failed'.date('Ymd'));
	            }
	        }
        }
        catch (Exception $e) {
            info_log('top_getUser:'.$e->getMessage(), 'api_call_failed'.date('Ymd'));
            //err_log($e->getMessage());
        }

        return null;
    }

}