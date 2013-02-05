<?php

class Hapyfish2_Island_Dal_MoveData
{
    protected static $_instance;

    protected $_tb_log = 'island_movedata_log';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Main
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function get($info)
    {
    	$sql = "SELECT from_uid,to_uid,from_api,to_api FROM $this->_tb_log WHERE from_uid=:from_uid AND to_uid=:to_uid AND from_api=:from_api AND to_api=:to_api";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('from_uid' => $info['old_uid'], 'to_uid' => $info['uid'], 'from_api' => $info['selectApi'], 'to_api' => $info['platform']));
    }
    
    public function insert($info)
    {
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($this->_tb_log, $info);
    }
}