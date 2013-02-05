<?php

class Hapyfish2_Island_Dal_Mercenary
{
    protected static $_instance;
    
    private $_prefix = '';
    private $_tb_day_mercenary = 'day_mercenary_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Mercenary
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
    
    public function getMercenaryMainTable()
    {
    	return 'island_' . $this->_prefix . '_' . $this->_tb_day_mercenary;
    }
    
    
    public function getDay($day)
    {
    	$tbname = $this->getMercenaryMainTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
    
    public function getRange($begin, $end)
    {
    	$tbname = $this->getMercenaryMainTable();
    	$sql = "SELECT * FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insertMercenaryMain($info)
    {
		$tbname = $this->getMercenaryMainTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }

}