<?php


class Hapyfish2_Island_Dal_UserExt
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_UserTitle
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
        $tbname = 'island_user_ext_info';
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

	public function insert($uid, $info)
    {
        $tbname = 'island_user_ext_info';
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function update($uid, $info)
    {
        $tbname = 'island_user_ext_info';
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$where = $wdb->quoteinto('uid = ?', $uid);
        $wdb->update($tbname, $info, $where);
    }

}