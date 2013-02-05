<?php


class Hapyfish2_Island_Dal_ConsumeLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_ConsumeLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getCoinTableName($uid, $yearmonth)
    {
    	$id = floor($uid/4) % 10;
    	return 'island_user_coinlog_' . $yearmonth . '_' . $id;
    }

    public function getGoldTableName($uid, $yearmonth)
    {
    	$id = floor($uid/4) % 10;
    	return 'island_user_goldlog_' . $yearmonth . '_' . $id;
    }

    public function getPayOrderFlowTableName($uid, $yearmonth)
    {
    	$id = floor($uid/4) % 10;
    	return 'island_user_payorder_flow_' . $yearmonth . '_' . $id;
    }

    public function getCoin($uid, $yearmonth, $limit = 50)
    {
    	$tbname = $this->getCoinTableName($uid, $yearmonth);
    	$sql = "SELECT cost,summary,create_time FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
        $yearmonth = date('Ym', $info['create_time']);
    	$tbname = $this->getCoinTableName($uid, $yearmonth);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function clear($uid, $yearmonth)
    {
        $tbname = $this->getCoinTableName($uid, $yearmonth);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

    public function getGold($uid, $yearmonth, $limit = 50)
    {
    	$tbname = $this->getGoldTableName($uid, $yearmonth);
    	$sql = "SELECT cost,summary,create_time FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function insertGold($uid, $info)
    {
        $yearmonth = date('Ym', $info['create_time']);
    	$tbname = $this->getGoldTableName($uid, $yearmonth);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function clearGold($uid, $yearmonth)
    {
        $tbname = $this->getGoldTableName($uid, $yearmonth);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

    public function getPayOrderFlow($uid, $yearmonth, $limit = 100)
    {
    	$tbname = $this->getPayOrderFlowTableName($uid, $yearmonth);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid ORDER BY time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function insertPayOrderFlow($uid, $info)
    {
        $yearmonth = date('Ym', $info['time']);
    	$tbname = $this->getPayOrderFlowTableName($uid, $yearmonth);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function clearPayOrderFlow($uid, $yearmonth)
    {
        $tbname = $this->getPayOrderFlowTableName($uid, $yearmonth);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

    public function getFXUnlockshipGold($tid, $id, $yearmonth)
    {
    	$tbname = 'island_user_goldlog_' . $yearmonth . '_' . ($id - 1);

    	$sql = " SELECT uid,cost,cid,create_time FROM $tbname WHERE cid LIKE '%03' AND create_time < 1307080800 ";

		$db = Hapyfish2_Db_Factory::getDB($tid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql);
    }

    public function updateFXUnlockshipGold($tid, $id, $cost, $info, $yearmonth)
    {
		$tbname = 'island_user_goldlog_' . $yearmonth . '_' . ($id - 1);

    	$sql = "UPDATE $tbname SET cost=:cost WHERE uid=:uid AND cid=:cid AND create_time=:create_time";

    	$db = Hapyfish2_Db_Factory::getDB($tid);
    	$wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $info['uid'], 'cost' => $cost, 'cid' => $info['cid'], 'create_time' => $info['create_time']));
    }

}