<?php

class Hapyfish_Island_Customer_Dal_Log
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish_Island_Customer_Dal_Log
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function get($uid)
    {
    	$sql = "SELECT uid,ip,time FROM user_log_login WHERE uid=:uid";
    	
        $db = Hapyfish_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insert($info)
    {
        $tbname = 'user_log_login';

        $db = Hapyfish_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$wdb->insert($tbname, $info);
    }
    
}