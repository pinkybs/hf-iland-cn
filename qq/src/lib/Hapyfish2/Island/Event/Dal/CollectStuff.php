<?php


class Hapyfish2_Island_Event_Dal_CollectStuff
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_CollectStuff
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getStuffTableName($uid){
    	$id = floor($uid/24) % 10;
    	return 'island_user_event_stuff_exchange_' . $id;
    }
    public function insert($uid, $info)
    {
        $tbname = $this->getStuffTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->insert($tbname, $info);
    }
    public function haveGetgift($uid){
        $tbname = $this->getStuffTableName($uid);
        $sql = "SELECT step FROM $tbname WHERE uid=:uid ";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    public function deletegetstuff($uid){
        $tbname = $this->getStuffTableName($uid);
        $sql = "DELETE from $tbname where uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
}