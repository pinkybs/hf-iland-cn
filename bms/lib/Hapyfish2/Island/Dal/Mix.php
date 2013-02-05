<?php

class Hapyfish2_Island_Dal_Mix
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_mix = 'day_mix';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Mix
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
    
    public function getMixMainTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_mix;
    }
    
    
    public function getDay($day)
    {
    	$tbname = $this->getMixMainTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRange($begin, $end)
    {
    	$tbname = $this->getMixMainTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insertMixMain($info)
    {
		$tbname = $this->getMixMainTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }

}