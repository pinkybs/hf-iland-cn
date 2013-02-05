<?php


class Hapyfish2_Bms_Dal_Auth
{
    protected static $_instance;
    
    private $_tb_user = 'bms_user';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Bms_Dal_Auth
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
    	return $this->_tb_user;
    }
    
    public function get($name, $password)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT uid,status,super FROM $tbname WHERE name=:name AND pwd=:pwd";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('name' => $name, 'pwd' => $password));
    }
    
    public function getBuUid($uid)
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT uid,name,real_name,status,super,create_time FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    
    public function getList()
    {
    	$tbname = $this->getTable();
    	$sql = "SELECT uid,name,real_name,status,super,create_time FROM $tbname ORDER BY uid ASC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$wdb->insert($tbname, $info);
    	
    	return $wdb->lastInsertId();
    }
    
    public function update($uid, $info)
    {
        $tbname = $this->getTable();
        
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('uid = ?', $uid);
    	
        $wdb->update($tbname, $info, $where);
    }
}