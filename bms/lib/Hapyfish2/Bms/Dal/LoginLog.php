<?php

class Hapyfish2_Bms_Dal_LoginLog
{
    protected static $_instance;
    
    private $_bms_log_login = 'bms_log_login';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Bms_Dal_LoginLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTableName()
    {
    	return $this->_bms_log_login;
    }
    
    public function get($uid, $limit = 10)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT login_time,ip FROM $tbname WHERE uid=:uid ORDER BY login_time DESC LIMIT $limit";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insert($info)
    {
        $tbname = $this->getTableName();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$wdb->insert($tbname, $info);
    }
    
}