<?php
/**
 * lei.wu,
 * lei.wu@hapyfish.com
 * */
class Hapyfish2_Island_Event_Dal_Peidui
{
	protected static $_instance;
	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
 	public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 50;
    	return 'island_user_plant_' . $id;
    }
    
	public function getUid($id, $cid)
	{
		$tb = $this->getTableName($id);
		$sql = "select count(distinct(uid)) from $tb where cid=:cid ";
		$db = Hapyfish2_Db_Factory::getDB($id);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array('cid' => $cid));
	}
	
	public function getId($id, $cid)
	{
		$tb = $this->getTableName($id);
		
		$sql = "SELECT COUNT(id) FROM $tb WHERE cid=:cid";
		
		$db = Hapyfish2_Db_Factory::getDB($id);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('cid' => $cid));
	}

	public function getNewSendUid($id, $cid)
	{
		$tb = $this->getTableName($id);
		
		$sql = "select uid,count(uid) as num from $tb where cid=:cid and buy_time between 1315908000 and 1315929599 group by uid";
		
		$db = Hapyfish2_Db_Factory::getDB($id);
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql, array('cid' => $cid));
	}
	
}