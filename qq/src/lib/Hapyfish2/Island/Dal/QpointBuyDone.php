<?php


class Hapyfish2_Island_Dal_QpointBuyDone
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_QpointBuyDone
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableName($uid, $yearmonth)
    {
    	$id = floor($uid/24) % 10;
    	return 'island_user_qpoint_buy_done_' . $yearmonth . '_' . $id;
    }

    public function listQpointBuy($uid, $yearmonth, $limit = 50)
    {
    	$tbname = $this->getTableName($uid, $yearmonth);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getQpointBuyDone($uid, $bill_no, $yearmonth)
    {
        $tbname = $this->getTableName($uid, $yearmonth);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND bill_no=:bill_no";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid, 'bill_no' => $bill_no));
    }

    public function insert($uid, $info)
    {
        $yearmonth = date('Ym', $info['create_time']);
    	$tbname = $this->getTableName($uid, $yearmonth);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function clear($uid, $yearmonth)
    {
        $tbname = $this->getTableName($uid, $yearmonth);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }
}