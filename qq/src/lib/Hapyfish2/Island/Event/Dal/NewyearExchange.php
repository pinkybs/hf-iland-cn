<?php


class Hapyfish2_Island_Event_Dal_NewyearExchange
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_NewyearExchange
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableName($uid)
    {
    	$id = floor($uid/24) % 10;
    	return 'island_user_event_newyear_exchange_' . $id;
    }

    public function getList()
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,method,create_time FROM $tbname ORDER BY create_time DESC LIMIT 0,100 ";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql);
    }

    public function insert($uid, $method)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->insert($tbname, array('uid'=>$uid, 'method'=>$method, 'create_time'=>time()));
    }

}