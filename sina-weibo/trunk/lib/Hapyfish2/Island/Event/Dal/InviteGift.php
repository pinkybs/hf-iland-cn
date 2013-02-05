<?php


class Hapyfish2_Island_Event_Dal_InviteGift
{
    protected static $_instance;
	protected $table_invite_gift = 'island_invite_gift';
    
    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_InviteFlow
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTableName($uid)
    {
    	$id = $uid % 10;
    	return 'island_user_invite_gift_' . $id;
    }
    
    public function getAllInviteGift($uid, $id)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT str_" . $id . " FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function getInviteGift($id)
    {
    	$sql = "SELECT * FROM $this->table_invite_gift WHERE id=:id";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql, array('id' => $id));
    }
    
    public function update($uid, $str, $id)
    {
    	$tbname = $this->getTableName($uid);
    	
    	$sql = "UPDATE $tbname SET str_" . $id . " =:str WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid, 'str' => $str));
    }
    
    public function insert($uid, $str, $id)
    {
    	$tbname = $this->getTableName($uid);
 	
    	$sql = "INSERT INTO $tbname (uid, str_" . $id . ") VALUES (:uid, :str)";
    	
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid, 'str' => $str));
    }
    
}