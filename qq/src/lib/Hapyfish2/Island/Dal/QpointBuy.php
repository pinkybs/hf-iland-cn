<?php


class Hapyfish2_Island_Dal_QpointBuy
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_QpointBuy
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
    	return 'island_user_qpoint_buy_' . $id;
    }

    public function getQpointBuy($uid, $token)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND token=:token";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid, 'token' => $token));
    }

    public function insert($uid, $info)
    {
    	$tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function update($uid, $token, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $where = array(
            $wdb->quoteInto('uid=?', $uid),
            $wdb->quoteInto('token=?', $token)
        );

        return $wdb->update($tbname, $info, $where);
    }

    public function clear($uid, $time)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE create_time<:create_time";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('create_time' => $time));
    }
}