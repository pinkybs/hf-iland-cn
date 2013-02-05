<?php


class Hapyfish2_Island_Dal_Tutorial
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_tutorial = 'day_tutorial';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Tutorial
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_tutorial;
    }
    
    public function getTutorial($day)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRangeTutorial($begin, $end)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
    
    public function update($day, $info)
    {
        $tbname = $this->getTable();
        
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('log_time = ?', $day);
    	
        $wdb->update($tbname, $info, $where);
    }
}