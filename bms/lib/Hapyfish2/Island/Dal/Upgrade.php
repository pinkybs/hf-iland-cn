<?php

class Hapyfish2_Island_Dal_Upgrade
{
    protected static $_instance;
    
    private $_prefix = '';
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
    
    public function getUpgradeTable()
    {
    	return 'island_' . $this->_prefix . '_day_upgrade';
    }
    
   
    
    public function insertUpgrade($data)
    {
    	$tbname = $this->getUpgradeTable();
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
    	return $wdb->insert($tbname, $data);
    }
    
    public function getUpgrade($start,$end)
    {
    	$tbname = $this->getUpgradeTable();
    	$db = Hapyfish2_Db_Factory::getDB();
    	$sql = "SELECT * FROM $tbname WHERE `date`>=:begin AND `date`<=:end ORDER BY `date` DESC";
    	$rdb = $db['r']; 
    	return $rdb->fetchAll($sql, array('begin' => $start, 'end' => $end));
    }

}