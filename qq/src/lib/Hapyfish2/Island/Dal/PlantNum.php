<?php

class Hapyfish2_Island_Dal_PlantNum
{
    protected static $_instance;

    
    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Dock
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getnum($id,$db,$cid)
    {
    	$tbname = 'island_user_plant_'.$db;
    	$sql = "select uid from $tbname where cid=:cid";
		$db = Hapyfish2_Db_Factory::getDB($id);
        $rdb = $db['r'];
    	 return $rdb->fetchCol($sql, array('cid' => $cid));
    }
}