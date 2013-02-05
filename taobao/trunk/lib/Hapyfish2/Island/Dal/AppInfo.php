<?php

class Hapyfish2_Island_Dal_AppInfo
{
    protected static $_instance;
    
    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_AppInfo
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getInfo()
    {
    	$sql = "SELECT * FROM app_info";
    	
        $db = $this->getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql);
    }
    
    public function update($appId, $info)
    {
        $tbname = 'app_info';
        
        $db = $this->getDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('app_id = ?', $appId);
    	
        $wdb->update($tbname, $info, $where);
    }
}