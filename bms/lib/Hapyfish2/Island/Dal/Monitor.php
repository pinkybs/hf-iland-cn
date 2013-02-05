<?php

class Hapyfish2_Island_Dal_Monitor
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_monitor_server = 'monitor_server';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Monitor
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function setDbPrefix($prefix)
    {
    	$this->_prefix = $prefix;
    }
    
    public function getTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_monitor_server;
    }
    
    public function getServerList()
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT sid,name,type,area_name,create_time FROM $tbname";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
    public function getServerById($sid)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT sid,name,type,area_name,create_time FROM $tbname WHERE sid=:sid";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('sid' => $sid));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
    
    public function update($sid, $info)
    {
        $tbname = $this->getTable();
        
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('sid = ?', $sid);
    	
        $wdb->update($tbname, $info, $where);
    }
}