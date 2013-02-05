<?php


class Hapyfish2_Island_Event_Dal_CollectKitten
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_CollectKitten
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function get($uid)
    {
    	$sql = "SELECT create_time FROM island_user_event_ckitten WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function insert($uid, $info)
    {
    	$tbname = 'island_user_event_ckitten';

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function delete($uid)
    {
    	$sql = "DELETE FROM island_user_event_ckitten WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid));
    }
    
}