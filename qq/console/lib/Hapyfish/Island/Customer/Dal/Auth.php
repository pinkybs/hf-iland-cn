<?php


class Hapyfish_Island_Customer_Dal_Auth
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish_Island_Customer_Dal_Auth
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function get($name, $password)
    {
    	$sql = "SELECT uid,status FROM `user` WHERE name=:name && pwd=:pwd";
    	
        $db = Hapyfish_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('name' => $name, 'pwd' => $password));
    }
}