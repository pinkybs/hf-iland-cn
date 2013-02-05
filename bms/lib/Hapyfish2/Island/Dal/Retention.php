<?php


class Hapyfish2_Island_Dal_Retention
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_user_retention_rate = 'day_user_retention';
    
    private $_tb_day_main = 'day_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Retention
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_user_retention_rate;
    }
    
    public function getMainTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_main;
    }
    
    public function getRetention($day)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRangeRetention($begin, $end)
    {
    	$tb1 = $this->getTable();
    	$tb2 = $this->getMainTable();
    	$sql = "SELECT a.*,b.memo FROM $tb1 AS a LEFT JOIN $tb2 AS b ON a.log_time=b.log_time WHERE a.log_time>=:begin AND a.log_time<=:end ORDER BY a.log_time DESC";
    	
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