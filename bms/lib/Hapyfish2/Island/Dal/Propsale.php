<?php


class Hapyfish2_Island_Dal_Propsale
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_propsale = 'day_prop_sale';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Propsale
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
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_propsale;
    }
    
    public function getPropsaleCoinList($start, $end, $sortType)
    {
    	$tbname = $this->getTable();
    	if ( $sortType == 1 ) {
    	   $sql = "SELECT cid,SUM(num) AS s_num,SUM(coin) AS s_coin,SUM(gold) AS s_gold FROM $tbname WHERE date>=:start AND date<=:end AND coin>0 GROUP BY cid ORDER BY s_num DESC  LIMIT 0,20;";
    	}
    	else {
    	   $sql = "SELECT cid,SUM(num) AS s_num,SUM(coin) AS s_coin,SUM(gold) AS s_gold FROM $tbname WHERE date>=:start AND date<=:end AND coin>0 GROUP BY cid ORDER BY s_coin DESC  LIMIT 0,20;";
        }
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('start' => $start, 'end' => $end));
    }
    
    public function getPropsaleCoinCount($start, $end, $sortType)
    {
        $tbname = $this->getTable();
        if ( $sortType == 1 ) {
           $sql = "SELECT SUM(num) FROM $tbname WHERE date>=:start AND date<=:end AND coin>0;";
        }
        else {
           $sql = "SELECT SUM(coin) FROM $tbname WHERE date>=:start AND date<=:end AND coin>0;";
        }
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('start' => $start, 'end' => $end));
    }
    
    public function getPropsaleGoldList($start, $end, $sortType)
    {
        $tbname = $this->getTable();
        if ( $sortType == 1 ) {
           $sql = "SELECT cid,SUM(num) AS s_num,SUM(coin) AS s_coin,SUM(gold) AS a_gold FROM $tbname WHERE date>=:start AND date<=:end AND gold>0 GROUP BY cid ORDER BY s_num DESC  LIMIT 0,20;";
        }
        else {
           $sql = "SELECT cid,SUM(num) AS s_num,SUM(coin) AS s_coin,SUM(gold) AS a_gold FROM $tbname WHERE date>=:start AND date<=:end AND gold>0 GROUP BY cid ORDER BY s_coin DESC  LIMIT 0,20;";
        }
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql, array('start' => $start, 'end' => $end));
    }
    
    public function getPropsaleGoldCount($start, $end, $sortType)
    {
        $tbname = $this->getTable();
        if ( $sortType == 1 ) {
           $sql = "SELECT SUM(num) FROM $tbname WHERE date>=:start AND date<=:end AND gold>0;";
        }
        else {
           $sql = "SELECT SUM(gold) FROM $tbname WHERE date>=:start AND date<=:end AND gold>0;";
        }
        
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('start' => $start, 'end' => $end));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
}