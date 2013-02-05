<?php

class Hapyfish2_Island_Dal_LossUser
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_lossuser = 'day_lossuser';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_ActiveUserLevel
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
    
    public function getLossUserTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_lossuser;
    }
    
    public function getDay($day)
    {
    	$tbname = $this->getLossUserTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRange($begin, $end)
    {
    	$tbname = $this->getLossUserTable();
    	$sql = "SELECT log_time,user_count,avg_wood,avg_stone FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insertLossUser($info)
    {
		$tbname = $this->getLossUserTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }

    
}