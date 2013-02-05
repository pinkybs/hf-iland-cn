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
    
	public function getUid($id, $cid, $time)
	{
		$tb = $this->getTableName($id);
		$sql = "select uid from $tb where cid=:cid and buy_time<=:time";
		$db = Hapyfish2_Db_Factory::getDB($id);
        $rdb = $db['r'];
        return $rdb->fetchCol($sql, array('cid' => $cid, 'time'=> $time));
	}

}