<?php

class Hapyfish2_Island_Dal_Server
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
     * @return Hapyfish2_Island_Dal_Server
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getServerList()
    {
    	$sql = "SELECT id,name,host,pub_ip,local_ip,type,status,add_time FROM island_server";
    	
        $db = self::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchPairs($sql);
    }    
}