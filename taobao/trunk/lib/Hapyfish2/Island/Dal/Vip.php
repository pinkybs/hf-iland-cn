<?php
class Hapyfish2_Island_Dal_Vip
{
	protected static $_instance;
	
	
	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getBasicDb()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }
    
   public function getVipInfo()
   {
		$db = $this->getBasicDb();
		$sql = "select `level`,needGem,skipNum,pvegameNum from island_vip_config";
		$rdb= $db['r'];
		return $rdb->fetchAssoc($sql);
   }
   
   public function getUserGem($uid)
   {
	   	$db = Hapyfish2_Db_Factory::getDB($uid);
	   	$rdb = $db['r'];
	   	$sql = "select money from island_user_vip where uid=:uid";
	   	return $rdb->fetchOne($sql,array('uid'=>$uid));
   }
   
   public function updateUserGem($uid, $usergem)
   {
		$db = Hapyfish2_Db_Factory::getDB($uid);
		$wdb = $db['w'];
		$sql = "INSERT INTO island_user_vip (uid, money) VALUES (:uid,:money) ON DUPLICATE KEY UPDATE money=:money";
		return $wdb->query($sql,array('uid'=>$uid,'money'=>$usergem));
   }
   
	public function getvipStat($id)
   {
	   	$db = Hapyfish2_Db_Factory::getDB($id);
	   	$rdb = $db['r'];
	   	$sql = "select uid,money from island_user_vip";
	   	return $rdb->fetchAll($sql);
   }
}