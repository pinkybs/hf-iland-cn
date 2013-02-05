<?php


class Hapyfish2_Island_Dal_ApiInfo
{
    protected static $_instance;
    
    private $_tb_island_api_info = 'island_api_info';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_ApiInfo
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTable()
    {
    	return $this->_tb_island_api_info;
    }
    
    public function getInfo($name)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE `name`=:name";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('name' => $name));
    }
    
    public function getStatPlatform()
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT `name`,stat FROM $tbname WHERE stat>0";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
}