<?php

class Hapyfish2_Island_Event_Dal_CdKey
{
    protected static $_instance;
    
    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
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
    
    public function getCdKeyCount()
    {
    	$sql = "SELECT COUNT(cdkey) FROM cdkey";
        $db = self::getDB();
        $rdb = $db['r'];
        return $rdb->fetchOne($sql);
    }
    
    public function getCdKey($cdKey)
    {
    	$sql = "SELECT cdkey,status FROM cdkey WHERE cdkey=:cdkey";
        $db = self::getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('cdkey' => $cdKey));
    }
    
	public function insertCdKey($cdKey)
    {
        $db = self::getDB();
        $wdb = $db['w'];
        return $wdb->insert('cdkey', array('cdkey' => $cdKey));
    }
    
	public function updateCdKey($cdKey, $info)
    {
        $db = self::getDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('cdkey=?', $cdKey);
        return $wdb->update('cdkey', $info, $where);
    }
    
	public function lstUserCdKey($uid)
    {
    	$sql = "SELECT uid,cdkey,create_time FROM cdkey_user WHERE uid=:uid";
        $db = self::getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
	public function insertUserCdKey($info)
    {
        $db = self::getDB();
        $wdb = $db['w'];
        return $wdb->insert('cdkey_user', $info);
    }
    
}