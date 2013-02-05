<?php

class Hapyfish2_Island_Dal_MainHour
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_main_hour = 'day_main_hour';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_MainHour
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
    	$sql = "SELECT log_time,add_user,active_user FROM $tbname WHERE log_time>=:stime AND log_time<=:etime";
    	
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