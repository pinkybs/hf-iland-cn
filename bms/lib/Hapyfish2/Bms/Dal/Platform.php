<?php


class Hapyfish2_Bms_Dal_Platform
{
    protected static $_instance;
    
    private $_tb_platform = 'bms_platform';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Bms_Dal_Platform
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
    	return $this->_tb_platform;
    }
    
    public function getList()
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname ORDER BY `index` ASC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
    public function getInfoByName($name)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE `name`=:name";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('name' => $name));
    }
    
    public function getInfoById($id)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT * FROM $tbname WHERE `pid`=:id";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('id' => $id));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
    
    public function update($pid, $info)
    {
        $tbname = $this->getTable();
        
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('pid = ?', $pid);
    	
        $wdb->update($tbname, $info, $where);
    }
}