<?php
class Hapyfish2_Island_Dal_Compound
{
	protected static $_instance;
	
	
	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getBasicInfo()
    {
    	$sql = "SELECT * from island_blueprint";
    	$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	
    	return $rdb->fetchAll($sql);
    }
    
 	public function getEventDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
    }
    
    public function getMTbName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_mab_' . $id;
    }
    
    public function updateUserMa($uid, $cid, $num, $type)
    {
    	$tbname = $this->getMTbName($uid);
    	$sql = "INSERT INTO $tbname(uid, cid, num, type) VALUES(:uid, :cid, :num, :type) ON DUPLICATE KEY UPDATE num=:num";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('uid'=>$uid, 'cid' => $cid, 'num' => $num, 'type' => $type));
    }
    
   public function getResolveConfig()
   {
   		$sql = "SELECT id,material from island_resolve";
   		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	return $rdb->fetchAll($sql);
   }
	
   public function getMarket()
   {
   		$sql = "select * from island_supermarket";
   		$db = self::getEventDB();
   		$rdb = $db["r"];
   		return $rdb->fetchAll($sql);
   }
   public function getUpdateConfig()
   {
   		$sql = "SELECT * from island_miracle_building";
   		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	return $rdb->fetchAll($sql);
   }
   
   public function getUserAm($uid)
   {
   		$tbname = $this->getMTbName($uid);
    	$sql = "select * from  $tbname where uid=:uid";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	return $rdb->fetchAll($sql, array('uid' => $uid));
   }
}