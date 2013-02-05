<?php

class InviteController extends Zend_Controller_Action
{
    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
            echo '<html><body><script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script></body></html>';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->uid = $info['uid'];
        $this->view->platformUid = $info['puid'];
    }

    protected function vailid()
    {
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
    	if (!$skey) {
    		return false;
    	}

    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}

        $uid = $tmp[0];
        $puid = $tmp[1];
        $session_key = base64_decode($tmp[2]);
        $t = $tmp[3];

        $rnd = -1;
        if ($count == 5) {
        	$sig = $tmp[4];
	        $vsig = md5($uid . $puid . $session_key . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
        } else if ($count == 6) {
        	$rnd = $tmp[4];
        	$sig = $tmp[5];
        	$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
        	if ($sig != $vsig) {
	        	return false;
	        }
        }

        //max long time one day
        if (time() > $t + 86400) {
        	return false;
        }

        return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
    }

    public function topAction()
    {
        $now = time();
        //截至时间 2011-01-24 00:00:00
        if ($now < 1295798400) {
        	$this->view->epath = 'e20110123/';
        } else {
        	$this->view->epath = '';
        }

    	$this->render();
    }

    public function friendsAction()
    {
		$uid = $this->uid;
		$page = $this->_request->getParam('page', 1);
		$size = 15;

        $rest = SinaWeibo_Client::getInstance();
        $rest->setUser($this->info['session_key']);

        /*//load friends cnt from cache
        $mkey = 'i:u:wb:friendcnt' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cntFriend = $cache->get($key);
		if ($cntFriend === false) {
			$cntFriend = $rest->getFriendIds();
    		if (null == $cntFriend) {
    		    echo '<a target="_top" href="http://game.weibo.com/'.APP_NAME.'/">Session out.Try reload.</a>';
    		    //echo '<html><body><script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script></body></html>';
                exit;
    		}
    		$cntFriend = count($cntFriend);
			//info_log($uid.':'.$cntFriend, 'friendscnt');
    		if (0 == $cntFriend) {
    		    echo '您还没有任何新浪互粉好友。<a target="_top" href="http://game.weibo.com/'.APP_NAME.'/">click</a>';
                exit;
    		}
			$cache->set($key, $cntFriend, 60);
		}*/

        //get friends info by group through api
        $lstFriend = $rest->getFriends($page, $size);
        if (null == $lstFriend) {
		    echo '<a target="_top" href="http://game.weibo.com/'.APP_NAME.'/">Time out.Try reload.</a>';
		    //echo '<html><body><script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script></body></html>';
            exit;
		}

		$cntFriend = $lstFriend['total_number'];
		$pageCnt = ceil($cntFriend/$size);

		$friendList = false;
        foreach ($lstFriend['users'] as $fdata) {
            $pfid = $fdata['id'];
            $inGame = Hapyfish2_Platform_Cache_UidMap::getUser($pfid);
            //if is in game friends
            if (empty($inGame)) {
                $friendList[] = array('uid'=>$pfid, 'name'=>$fdata['name'], 'face'=>$fdata['profile_image_url'], 'joint'=>'0');
            }
            else {
                $friendList[] = array('uid'=>$pfid, 'name'=>$fdata['name'], 'face'=>$fdata['profile_image_url'], 'joint'=>'1');
            }
        }

        if ($friendList) {
            //$friendList = array_merge($friendList,$friendList,$friendList);
            //$friendNum = count($friendList);
        }
        else {
            $friendList = '[]';
		    //$friendNum = 0;
        }

		$this->view->friendList = json_encode($friendList);
		$this->view->friendNum = $cntFriend;
		$this->view->pageSize = $size;
		$this->view->pageNum = $pageCnt;
		$this->view->curPage = $page;

		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$this->view->user = $user;

    	$this->render();
    }

    public function sendAction()
    {

        echo '<script type="text/javascript">parent.initInvite("1");</script>邀请发送成功！';
        exit;

        $uid = $this->uid;
        $puids = $this->_request->getParam('ids');
        $aryPuid = explode(',', $puids);
        if (empty($aryPuid)) {
            echo 'Failed';
            exit();
        }
//info_log($puids,'inviteSends');
//info_log(json_encode($_REQUEST), 'inviteSends');
echo '<html><body>邀请已发送。3秒后自动跳转。<script type="text/javascript">window.setTimeout(function(){window.top.location="http://game.weibo.com/'.APP_NAME.'/";},3000);</script></body></html>';
//echo $puids.' invitation sended.<a href="http://game.weibo.com/'.APP_NAME.'/">back</a>';
exit;

        try {
            //invite send logs
            $dalInvite = Hapyfish2_Island_Event_Dal_InviteSend::getDefaultInstance();
            $now = time();
            foreach ($aryPuid as $puid) {
                $rowInvite = $dalInvite->getInviteSend($puid, $uid);
                if (empty($rowInvite)) {
                    $dalInvite->insert($puid, array('invite_puid' => $puid, 'uid' => $uid, 'create_time' => $now));
                }
            }
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'send-invite-err');
        }

        echo '<a href="javascript:void(0);" onclick="HFApp.invite();" target="_top">Back&gt;&gt;</a>';
        exit();
    }
 }
