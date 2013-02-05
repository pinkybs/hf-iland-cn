<?php


class Hapyfish2_Island_Dal_Payclick
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_payclick = 'day_payclick';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Payclick
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_payclick;
    }
    
    public function getPayclick($day)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE create_time=:create_time";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('create_time' => $day));
    }
    
    public function getRangePayclick($begin, $end)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE create_time>=:begin AND create_time<=:end ORDER BY create_time DESC";
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
}