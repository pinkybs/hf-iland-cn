<?php

class Hapyfish2_Island_Dal_InviteLog
{
    protected static $_instance;
    
    protected $eventTBname = 'island_event_invitelog';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_InviteLog
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
    	$id = floor($uid/4) % 10;
    	return 'island_user_invitelog_' . $id;
    }
    
    public function addInvite($info)
    {
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->insert($this->eventTBname, $info);
    }
    
    public function insert($uid, $info)
    {
    	$tbname = $this->getTableName($uid);
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function getInvite($key)
    {
    	$sql = " SELECT * FROM $this->eventTBname WHERE sig=:sig AND status=:status ";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('sig' => $key, 'status' => 1));
    }
    
    public function deleteInvite($key)
    {
		$sql = "DELETE FROM $this->eventTBname WHERE sig=:sig";
        
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('sig' => $key));
    }
    
    public function insertInviteLog()
    {
		$tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 
    }
    
    public function getAll($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,fid,`time` FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function getAllByTime($uid, $time)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT fid FROM $tbname WHERE uid=:uid AND `time`>$time ORDER BY `time`";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function getCount($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT COUNT(uid) AS c FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function getCountByTime($uid, $time)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT COUNT(uid) AS c FROM $tbname WHERE uid=:uid AND `time`>$time";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);
        
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
}