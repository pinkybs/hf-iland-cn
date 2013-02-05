<?php

class Hapyfish2_Island_Event_Dal_CdKeyII
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

    public function getCdKeyCount($no)
    {
    	$tbname = 'cdkey_' . $no;
    	$sql = "SELECT COUNT(cdkey) FROM $tbname";
        $db = self::getDB();
        $rdb = $db['r'];
        return $rdb->fetchOne($sql);
    }

    public function getCdKey($no, $cdKey)
    {
    	$tbname = 'cdkey_' . $no;
    	$sql = "SELECT cdkey,status FROM $tbname WHERE cdkey=:cdkey";
        $db = self::getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('cdkey' => $cdKey));
    }

	public function insertCdKey($no, $cdKey)
    {
    	$tbname = 'cdkey_' . $no;
        $db = self::getDB();
        $wdb = $db['w'];
        return $wdb->insert($tbname, array('cdkey' => $cdKey));
    }

	public function updateCdKey($no, $cdKey, $info)
    {
    	$tbname = 'cdkey_' . $no;
        $db = self::getDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('cdkey=?', $cdKey);
        return $wdb->update($tbname, $info, $where);
    }

	public function getUserCdKey($no, $uid)
    {
    	$tbname = 'cdkey_user_' . $no;
    	$sql = "SELECT uid,cdkey,create_time FROM $tbname WHERE uid=:uid";
        $db = self::getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

	public function insertUserCdKey($no, $info)
    {
    	$tbname = 'cdkey_user_' . $no;
        $db = self::getDB();
        $wdb = $db['w'];
        return $wdb->insert($tbname, $info);
    }

}