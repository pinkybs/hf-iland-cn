<?php

class Hapyfish2_Island_Dal_Main
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_main = 'day_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Main
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_main;
    }
    
    public function getDay($day)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT log_time,total_count,add_user,add_user_male,add_user_female,active,active_male,active_female,pay_total_amount,pay_user_count,pay_count,cost_gold,memo FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRange($begin, $end, $sort = 'DESC')
    {
    	$tbname = $this->getTable();
    	$platform = $this->_prefix;
    	if ( in_array($platform, array('weibo','taobao','fb_taiwan','fb_thailand')) ) {
    		$sql = "SELECT log_time,total_count,add_user,add_user_male,add_user_female,active,active_male,active_female,active_twoday,active_secondday,pay_total_amount,pay_gold_count,pay_user_count,pay_count,cost_gold,memo,send_gold FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time $sort";
    	}else {
    		$sql = "SELECT log_time,total_count,add_user,add_user_male,add_user_female,active,active_male,active_female,active_twoday,active_secondday,pay_total_amount,pay_gold_count,pay_user_count,pay_count,cost_gold,memo FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time $sort";
    	}
    	
    	
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