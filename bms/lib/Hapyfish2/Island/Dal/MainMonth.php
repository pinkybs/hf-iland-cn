<?php

class Hapyfish2_Island_Dal_MainMonth
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_month_main = 'month_main';
    private $_tb_day_main = 'day_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_MainMonth
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_month_main;
    }
    
    public function getTableMain()
    {
        return 'island_' . $this->_prefix . '_' . $this->_tb_day_main;
    }
    
    public function getMonth($month)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT log_time,total_user,add_user,active_user,pay_amount FROM $tbname WHERE log_time=:month";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('month' => $month));
    }
    
    public function getRange($begin, $end, $sort = 'DESC')
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT log_time,total_user,add_user,active_user,pay_amount FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time $sort";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getMonthInfo($begin, $end)
    {
        $tbname = $this->getTableMain();
        $sql = "SELECT SUM(add_user) AS all_add_user,SUM(pay_total_amount) AS all_pay FROM $tbname WHERE log_time>=:begin AND log_time<:end";
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getMonthTotalUser($begin, $end)
    {
        $tbname = $this->getTableMain();
        $sql = "SELECT MAX(total_count) as all_total_count FROM $tbname WHERE log_time>=:begin AND log_time<:end";
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
    
    public function update($month, $info)
    {
        $tbname = $this->getTable();
        
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('log_time = ?', $month);
    	
        $wdb->update($tbname, $info, $where);
    }
}