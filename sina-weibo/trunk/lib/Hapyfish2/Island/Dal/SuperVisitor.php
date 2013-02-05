<?php

class Hapyfish2_Island_Dal_SuperVisitor
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_SuperVisitor
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
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_collection_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT cid,count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchPairs($sql, array('uid' => $uid));
    }

    public function update($uid, $cid, $count)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname (uid, cid, count) VALUES($uid, $cid, $count) ON DUPLICATE KEY UPDATE count=$count";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql);
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }
    
}