<?php

/**
 * Event LanternFestival
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/29    zhangli
*/
class Hapyfish2_Island_Event_Dal_LanternFestival
{
    protected static $_instance;

    protected $table_lf_user_data = 'island_lf_user_data';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_SpringFestival
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function incUserData($uid)
    {
    	$sql = "INSERT INTO $this->table_lf_user_data (uid) VALUES (:uid)";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
    public function getUserData($uid)
    {
		$sql = "SELECT plant FROM $this->table_lf_user_data WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function renewUserData($uid, $plant)
    {
    	$sql = "UPDATE $this->table_lf_user_data SET plant=:plant WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'plant' => $plant));
    }
    
    public function getCookTimes($uid)
    {
		$sql = "SELECT cook_times FROM $this->table_lf_user_data WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function renewCookTimes($uid, $num)
    {
    	$sql = "UPDATE $this->table_lf_user_data SET cook_times=:cook_times WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'cook_times' => $num));
    }
    
}