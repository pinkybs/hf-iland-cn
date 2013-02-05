<?php

class Hapyfish2_Island_Dal_FullScreen
{
    protected static $_instance;

    protected $table_user_fullscreen= 'island_user_fullscreen';
    
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
    
    public function addStatus($info)
    {
    	$sql = "INSERT INTO $this->table_user_fullscreen(uid, status) VALUES(:uid, :status)";
    	
		$db = Hapyfish2_Db_Factory::getDB($info['uid']);
        $wdb = $db['w'];
    	
        $wdb->query($sql, array('uid' => $info['uid'], 'status' => $info['status']));
    }
    
    public function getStatus($uid)
    {
    	$sql = "SELECT status FROM $this->table_user_fullscreen WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
}