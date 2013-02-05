<?php
/**
 * bujisky.li
 * bujisky.li@hapyfish.com
 * */
class Hapyfish2_Island_Event_Dal_fansGift  
{
	protected $tbname = 'island_user_fans_gift';
	protected static $_instance;
	
	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	public function insert($uid, $time) 
	{
    	$sql = "INSERT INTO $this->tbname VALUES($uid, $time) ON DUPLICATE KEY UPDATE create_time=:time";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
      	return $wdb->query($sql, array('time' => $time));
		
	}
	public function get($uid) 
	{
    	$sql = "SELECT create_time FROM ".$this->tbname." WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
	}
	
	public function getList($fType)
	{
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$sql = " SELECT * from fans_config where ftype=:ftype";
		$rdb = $db['r'];
		return  $rdb->fetchAll($sql, array('ftype' => $fType));
	}
	
	public function delete($uid)
	{
		$sql = "DELETE from $this->tbname where uid=:uid";	
		$db = Hapyfish2_Db_Factory::getDB($uid);
		$wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
	}
}