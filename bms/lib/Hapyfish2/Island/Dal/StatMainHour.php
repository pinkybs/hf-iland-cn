<?php

class Hapyfish2_Island_Dal_StatMainHour
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_main_hour = 'day_stat_main_hour';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_StatMainHour
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_main_hour;
    }
    
    public function getDay($day)
    {
    	$tbname = $this->getTable();
    	$stime = $day . '00';
    	$etime = $day . '23';
    	$sql = "SELECT * FROM $tbname WHERE log_time>=:stime AND log_time<=:etime";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('stime' => $stime, 'etime' => $etime));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }

}