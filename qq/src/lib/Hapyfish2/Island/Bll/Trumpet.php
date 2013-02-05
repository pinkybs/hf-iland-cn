<?php

class Hapyfish2_Island_Bll_Trumpet
{ 
	public static function getTrumpetMsg($uid)
	{
		$time = time();
		$msg = array();
		$key = 'trumpet:msg';
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if(!empty($data)){
        	foreach($data as $k => $v){
        		if($v['time'] >= $time){
        			return $v;
        		}
        	}
        }
		return $msg;
	}
	
	public static function setTrumpetMsg($uid, $msg, $time = 60)
	{
		$resultVo['status'] = 1;
		$endtime = time();
		$key = 'trumpet:msg';
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$name = '系统公告';
		$msg = Hapyfish2_Island_Bll_Remind::filterContent($msg);
		if($uid !=''){
			$user = Hapyfish2_Platform_Bll_User::getUser($uid);
			$name = $user['nickname'];
		}
		$arr = array('name' => $name, 'content' => $msg, 'time' => time()+$time);
		$data[] = $arr;
		
		foreach($data as $k =>$v){
			if($endtime >= $v['time']){
				unset($data[$k]);
			}
		}
		$data = array_values($data);
		for($i=0; $i<MEMCACHED_SECTION_NUM; $i++){
			$cache = Hapyfish2_Cache_Factory::getMC($i);
			$cache->set($key, $data);
		}
		
		return $resultVo;
	}
	
}
