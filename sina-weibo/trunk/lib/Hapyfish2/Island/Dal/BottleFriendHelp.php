<?php
class Hapyfish2_Island_Dal_BottleFriendHelp
{
	protected static $_instance;
	
	protected static $_tbname = 'island_bottle_friendhelp_';
	
	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return self::$_tbname . $id;
    }
    
    public function getByUid($uid)
    {
    	$tbname = $this->getTableName($uid);
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        $sql = "SELECT `uid`,`fid`,`goldTF`,`lasttime` FROM {$tbname} WHERE uid=:uid";
        return $rdb->fetchRow($sql, array('uid'=>$uid));        
    }
    
    public function insert($uid,$info)
    {
    	$tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->insert($tbname, $info); 
    }
    
    public function update($uid, $info)
    {
    	$tbname = $this->getTableName($uid);
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $uid = $wdb->quote($uid);
        $where = " uid={$uid} ";
        
        return $wdb->update($tbname, $info, $where);
    }
    
    public function deleteByUid($uid)
    {
    	$tbname = $this->getTableName($uid);
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $sql = "DELETE FROM {$tbname} WHERE uid=:uid ";
        return $wdb->query($sql, array('uid' => $uid));
    }
    
    
    
}