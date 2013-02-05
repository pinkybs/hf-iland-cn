<?php
/**
 * bujisky.li
 * bujisky.li@hapyfish.com
 * */
class Hapyfish2_Island_Event_Bll_fansGift
{
	public static function receive($uid) 
	{
		
		$data = self::check($uid);
		$result['status'] = 1;
		$result['content'] = '恭喜您成功领取每天登陆加关注礼包'; 
		if( !$data ) {
			// 已经领取完
			$result['status'] = '-1';
			$result['content'] = '您今天已领取过了'; // #todo 错误代码未改
			return array('result' => $result);
		}
		//获取官方粉丝数
		$context = Hapyfish2_Util_Context::getDefaultInstance();
		$session_key = $context->get('session_key');			
		$rest = SinaWeibo_Client::getInstance();
		$fankey = 'i:u:e:fansnum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$followersInfo = $cache->get($fankey);
		$followersCount = $followersInfo[0];
		$date = date('Ymd');
		if(!$followersInfo) {
	        $rest->setUser($session_key);
	        $followersCount = $rest->getFollowerCount('2155826421');
	        $cache->add($fankey,array($followersCount, $date));
		}else {
			if($followersInfo[1]!=$date) {
		        $rest->setUser($session_key);
	        	$followersCount = $rest->getFollowerCount('2155826421');
	        	if($followersCount && $followersCount>$followersInfo[0]) {
	        		$cache->set($fankey,array($followersCount, $date));
				}else {
					$cache->set($fankey,array($followersInfo[0], $date));
				}
			}
		}
		$fType = self::getType($followersCount);
        //		
		
		$send = new Hapyfish2_Island_Bll_Compensation();	
		$list = self::getGiftlist($fType);
		if($list){
			foreach($list as $k => $v){
				switch($v['type']){
					case 'coin':
						$send->setCoin($v['num']);
					break;
					case 'gold':
						$send->setGold($v['num']);	
					break;
					case 'starfish':
						$send->setStarFish($v['num']);	
					break;
					default:
						$send->setItem($v['cid'], $v['num']);
					break;	
				}
			}
		}
		$ok = $send->sendOne($uid, '恭喜您获得粉丝礼包:');
		if($ok){
			$time = time();
			$dreamgardenuser = Hapyfish2_Island_Event_Dal_fansGift::getDefaultInstance();
			$dreamgardenuser->insert($uid, $time);
			$data = $time;
			$key = 'i:u:e:f:g:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        $cache->set($key, $data);
		}
 		$ret['result'] = $result;
		return $ret;
		
	}

	public static function check( $uid ) 
	{
		$key = 'i:u:e:f:g:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ymd = date('Ymd');
        $data = $cache->get($key);
		if ($data === false) {
			try {
    			$dreamgardenuser = Hapyfish2_Island_Event_Dal_fansGift::getDefaultInstance();
    			$data = $dreamgardenuser->get($uid);
    			if($data){
    				$cache->set($key, $data);
    			}
			} catch (Exception $e) {
				return false;
			}
		}
		$symd = date('Ymd', $data);
		if($symd == $ymd){
			return false;
		} else {
		 return true;
		}
	}
	
	public static function getGiftlist($fType)
	{
		$key = 'i:u:e:f:g:l' . $fType;
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$info = $cache->get($key);
		if($info === false){
			try {
    			$dal = Hapyfish2_Island_Event_Dal_fansGift::getDefaultInstance();
    			$data = $dal->getList($fType);
    			if($data){
    				$cache->set($key, $data);
    			}
			} catch (Exception $e) {
				return false;
			}
		}
		return $info;
	}
	
	public static function getUserFans($uid)
	{
		$result['status'] = 1;
		$rest = SinaWeibo_Client::getInstance();
		$context = Hapyfish2_Util_Context::getDefaultInstance();
		$puid = $context->get('puid');
		$session_key = $context->get('session_key');	
					
		$data = self::check($uid);
		if(!$data){
			$isFan = 1;
		} else {
			
			$ukey = 'i:u:e:isfan:'.$uid;
			$ucache = Hapyfish2_Cache_Factory::getMC($uid);
			$rest->setUser($session_key);
			$fansData = $rest->isFans();
			if($fansData==0 || $fansData==1) {
				$ucache->set($ukey, $fansData);
			}
			$isFan = $ucache->get($ukey);
			
		}
		//获取官方粉丝数
		$fankey = 'i:u:e:fansnum';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$followersInfo = $cache->get($fankey);
		$followersCount = $followersInfo[0];
		$date = date('Ymd');
		if(!$followersInfo) {
	        $rest->setUser($session_key);
	        $followersCount = $rest->getFollowerCount('2155826421');
	        $cache->add($fankey,array($followersCount, $date));
		}else {
			if($followersInfo[1]!=$date) {
		        $rest->setUser($session_key);
	        	$followersCount = $rest->getFollowerCount('2155826421');
	        	if($followersCount && $followersCount>$followersInfo[0]) {
	        		$cache->set($fankey,array($followersCount, $date));
				}else {
					$cache->set($fankey,array($followersInfo[0], $date));
				}
			}
		}
		
		$fType = self::getType($followersCount);
		
		$list = self::getGiftlist($fType);
		$fansGift['coin'] = ''; 
		$fansGift['gold'] = '';
		$fansGift['starfish'] = '';
		$fansGift['cid'] = array();
		$rlist['fans_flag'] = 0;
		if($isFan){
			$rlist['fans_flag'] = 1;
		}
		if($list){
			foreach($list as $k => $v){
				if($v['type'] == 'coin'){
					$fansGift['coin'] = $v['num'];
				} elseif ($v['type'] == 'gold'){
					$fansGift['gold'] = $v['num'];
				}elseif ($v['type'] == 'starfish'){
					$fansGift['starfish'] = $v['num'];
				} else{
					$fansGift['cid'][] = $v['cid'].'*'.$v['num'];
				}
			}
		}
		$rlist['followers_count'] = $followersCount;
		$rlist['level'] = $fType;
		$rlist['result'] = $result;
		$rlist['awardarr'] = $fansGift;
		$rlist['url'] = 'http://weibo.com/2155826421';
		return $rlist;
	}
	private function getType($followersCount) {
		$fType = 1;
	    if($followersCount < 40000) {
        	$fType = 1;
        }elseif($followersCount < 60000) {
        	$fType = 2;
        }elseif($followersCount < 80000) {
        	$fType = 3;
        }elseif($followersCount < 100000) {
        	$fType = 4;
        }elseif($followersCount >= 100000) {
        	$fType = 5;
        }
        return $fType;		
	}
}