<?php

class Hapyfish2_Island_Dal_Payment
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_user_payment = 'user_payment';
    private $_tb_user_payment_log = 'user_payment_log';
    private $_tb_paylist = 'day_paylist';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Payment
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
    
    public function getPaymentTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_user_payment;
    }
    
    public function getPaymentLogTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_user_payment_log;
    }
    
    public function getPaylistTable()
    {
        return 'island_' . $this->_prefix . '_' . $this->_tb_paylist;
    }
    
    public function getTop100User($day)
    {
    	$tbname = $this->getPaymentTable();
    	$sql = "SELECT uid,amount,create_time FROM $tbname ORDER BY amount DESC limit 100";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getNewPayUserCount($begin, $end)
    {
    	$tbname = $this->getPaymentTable();
    	$sql = "SELECT COUNT(uid) FROM $tbname WHERE create_time>=:begin AND create_time<:end";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getTotalPayUserCount($begin, $end)
    {
    	$tbname = $this->getPaymentLogTable();
    	$sql = "SELECT COUNT(DISTINCT uid) FROM $tbname WHERE create_time>=:begin AND create_time<:end";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getTotalPayAmount($begin, $end)
    {
    	$tbname = $this->getPaymentLogTable();
    	$sql = "SELECT SUM(amount) FROM $tbname WHERE create_time>=:begin AND create_time<:end";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getNewUserPayAmount($begin, $end)
    {
    	$tbname = $this->getPaymentLogTable();
    	$tbname2 = $this->getPaymentTable();
    	$sql = "SELECT SUM(amount) FROM $tbname WHERE create_time>=:begin AND create_time<:end AND uid IN (SELECT uid FROM $tbname2 WHERE create_time>=:begin AND create_time<:end)";
    	
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

    public function insertPaylist($info)
    {
        $tbname = $this->getPaylistTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
        return $wdb->insert($tbname, $info);
    }
    
    public function getPaylist($day)
    {
        $tbname = $this->getPaylistTable();
        $sql = "SELECT * FROM $tbname WHERE log_time=:day ";
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
}