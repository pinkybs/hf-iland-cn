<?php

class Hapyfish2_Db_Factory
{
	protected static $_db = null;
    
    public static function getDB()
    {
    	if (self::$_db === null) {
    		include CONFIG_DIR . '/database.php';
    		$dbAdapter = self::buildAdapter($DATABASE);
    		self::$_db = array('r' => $dbAdapter, 'w' => $dbAdapter);
    	}
    	
        return self::$_db;
    }
    
    public static function buildAdapter($params)
	{
	    $params['driver_options'] = array(
	        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
	        PDO::ATTR_TIMEOUT => 4
	    );
	    
	    $dbAdapter = Zend_Db::factory('PDO_MYSQL', $params);
	    $dbAdapter->query("SET NAMES utf8");
	
	    return $dbAdapter;
	}


}