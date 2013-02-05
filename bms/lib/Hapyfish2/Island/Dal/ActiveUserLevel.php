<?php

class Hapyfish2_Island_Dal_ActiveUserLevel
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_active_user_level = 'day_active_user_level';
    private $_tb_day_user_level = 'day_all_user_level';
    private $_tb_day_levelup = 'day_levelup';

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
    
    public function getActiveUserLevelTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_active_user_level;
    }
    
    public function getUserLevelTable()
    {
        return 'island_' . $this->_prefix . '_' . $this->_tb_day_user_level;
    }
    
    public function getLevelupTable()
    {
        return 'island_' . $this->_prefix . '_' . $this->_tb_day_levelup;
    }
    
    public function getDay($day)
    {
    	$tbname = $this->getActiveUserLevelTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRange($begin, $end)
    {
    	$tbname = $this->getActiveUserLevelTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insertActiveUserLevel($info)
    {
		$tbname = $this->getActiveUserLevelTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }

    public function insertUserlevel($info)
    {
        $tbname = $this->getUserLevelTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
        return $wdb->insert($tbname, $info);
    }
    
    public function getDayAllUserLevel($day)
    {
        $tbname = $this->getUserLevelTable();
        $sql = "SELECT * FROM $tbname WHERE log_time=:day";
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function insertLevelup($info)
    {
        $tbname = $this->getLevelupTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
        return $wdb->insert($tbname, $info);
    }
    
    public function getDayLevelup($day)
    {
        $tbname = $this->getLevelupTable();
        $sql = "SELECT * FROM $tbname WHERE log_time=:day";
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
}