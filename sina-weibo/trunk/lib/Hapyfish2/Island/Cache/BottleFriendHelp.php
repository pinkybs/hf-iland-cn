<?php
class Hapyfish2_Island_Cache_BottleFriendHelp
{
	
	public static function getByUid($uid)
	{
		$key = 'i:u:bottlefh:' . $uid;
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $list = $cache->get($key);
        
        if ($list === false) {
        	try {
        		$db = Hapyfish2_Island_Dal_BottleFriendHelp::getDefaultInstance();
        		$list = $db->getByUid($uid);
        		
        		if ($list) {
        			$cache->add($key, $list);
        		} else {
        			return null;
        		}
        		
        	} catch (Exception $e) {
        		return null;
        	}
        }
        
        return $list;
	}
	
	// 删除 memcache缓存
	public static function reloadByUid($uid) 
	{
		$key = 'i:u:bottlefh:' . $uid;
		
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        
        return $cache->delete($key);
	}
	
	// 插入
	public static function insert($uid, $info)
	{
		$db = Hapyfish2_Island_Dal_BottleFriendHelp::getDefaultInstance();
		
		$db->insert($uid, $info);
		
		return self::reloadByUid($uid);
	}
	
	// 更新
	public static function update($uid, $info)
	{
		$db = Hapyfish2_Island_Dal_BottleFriendHelp::getDefaultInstance();
		
		$db->update($uid, $info);
		
		return self::reloadByUid($uid);
	}
	
	// 删除
	public static function delete($uid)
	{
		$db = Hapyfish2_Island_Dal_BottleFriendHelp::getDefaultInstance();
		
		$db->deleteByUid($uid);
		
		return self::reloadByUid($uid);
	}
	
	// 重置
	public static function reset($uid)
	{
		$db = Hapyfish2_Island_Dal_BottleFriendHelp::getDefaultInstance();
		
		$info = array('fid'=>'','goldTF'=>'','lasttime'=>time());
		
		$db->update($uid, $info);
		
		return self::reloadByUid($uid);
	}
	
}