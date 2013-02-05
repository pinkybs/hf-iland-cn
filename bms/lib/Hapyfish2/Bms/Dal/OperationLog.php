<?php

class Hapyfish2_Bms_Dal_OperationLog
{
    protected static $_instance;
    
    private $_bms_log_operation = 'bms_log_operation';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Bms_Dal_OperationLog
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
    	return $this->_bms_log_operation;
    }
    
    public function get($uid, $limit = 10)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT do_time,platform,content FROM $tbname WHERE uid=:uid ORDER BY do_time DESC LIMIT $limit";
    	
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