<?php


class Hapyfish2_Island_Dal_Building
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Building
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
    	$id = floor($uid/DATABASE_NODE_NUM) % 50;
    	return 'island_user_building_' . $id;
    }

    public function getAllIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }

    public function getOnIslandIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id FROM $tbname WHERE uid=:uid AND status=1";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }

    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,cid,x,y,z,mirro,item_type,status FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql, array('uid' => $uid));
    }

    public function getOne($uid, $id)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,cid,x,y,z,mirro,item_type,status FROM $tbname WHERE uid=:uid AND id=:id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid, 'id' => $id), Zend_Db::FETCH_NUM);
    }

    public function getOnIsland($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,cid,x,y,z,mirro,item_type,status FROM $tbname WHERE uid=:uid AND status=1";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getInWareHouse($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,cid,x,y,z,mirro,item_type,status FROM $tbname WHERE uid=:uid AND status=0";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function upgradeCoordinate($uid, $step = 1)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "UPDATE $tbname SET x=x+$step,y=y+$step WHERE uid=:uid AND status=1";

    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('uid' => $uid));
    }

    public function insert($uid, $building)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $building);
    }

    public function update($uid, $id, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
        $id = $wdb->quote($id);
    	$where = "uid=$uid AND id=$id";

        return $wdb->update($tbname, $info, $where);
    }

    public function delete($uid, $id)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid AND id=:id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'id' => $id));
    }

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "INSERT INTO $tbname(uid, id, cid, x, y, z, mirro, status, item_type)
            VALUES
            (:uid, 1, 7521,  3, 3, 0, 0, 1, 21),
            (:uid, 2, 7221,  7, 0, 0, 0, 1, 21),
            (:uid, 3, 7221,  6, 0, 0, 0, 1, 21),
            (:uid, 4, 7221,  6, 2, 0, 0, 1, 21),
            (:uid, 5, 7221,  6, 3, 0, 0, 1, 21),
            (:uid, 6, 7221,  7, 1, 0, 0, 1, 21),
            (:uid, 7, 7221,  8, 1, 0, 0, 1, 21),
            (:uid, 8, 7221,  8, 0, 0, 0, 1, 21),
            (:uid, 9, 7221,  9, 0, 0, 0, 1, 21),
            (:uid, 10, 7221,  9, 1, 0, 0, 1, 21),
            (:uid, 11, 7221,  10, 0, 0, 0, 1, 21),
            (:uid, 12, 7221,  11, 1, 0, 0, 1, 21),
            (:uid, 13, 7221,  10, 1, 0, 0, 1, 21),
            (:uid, 14, 7221,  12, 1, 0, 0, 1, 21),
            (:uid, 15, 7221,  13, 2, 0, 0, 1, 21),
            (:uid, 16, 7221,  14, 3, 0, 0, 1, 21),
            (:uid, 17, 7221,  9, 2, 0, 0, 1, 21),
            (:uid, 18, 7221,  10, 2, 0, 0, 1, 21),
            (:uid, 19, 7221,  10, 3, 0, 0, 1, 21),
            (:uid, 20, 7221,  14, 4, 0, 0, 1, 21),
            (:uid, 21, 7221,  6, 4, 0, 0, 1, 21),
            (:uid, 22, 7221,  10, 4, 0, 0, 1, 21),
            (:uid, 23, 22121,  5, 15, 0, 0, 1, 21),
            (:uid, 24, 22121,  6, 15, 0, 0, 1, 21),
            (:uid, 25, 22121,  7, 15, 0, 0, 1, 21),
            (:uid, 26, 33021,  10, 5, 0, 0, 1, 21),
            (:uid, 27, 32921,  6, 5, 0, 0, 1, 21),
            (:uid, 28, 46221,  13, 12, 0, 0, 1, 21),
            (:uid, 29, 46321,  13, 13, 0, 0, 1, 21),
            (:uid, 30, 50421,  1, 10, 0, 0, 1, 21),
            (:uid, 31, 50421,  4, 6, 0, 0, 1, 21),
            (:uid, 32, 50521,  13, 9, 0, 0, 1, 21),
            (:uid, 33, 7821,  8, 10, 0, 0, 1, 21),
            (:uid, 34, 7821,  13, 6, 0, 0, 1, 21),
            (:uid, 35, 36221,  0, 6, 0, 0, 1, 21),
            (:uid, 36, 46121,  1, 5, 0, 0, 1, 21)";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid));
    }

    public function clearDiy($uid, $islandId)
    {
        $tbname = $this->getTableName($uid);

        $sql = "UPDATE $tbname SET status=0 WHERE uid=:uid AND status=:islandId";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'islandId' => $islandId));
    }
    
	public function getAllCid($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT cid FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getOneNum($uid, $cid)
    {
		$tbname = $this->getTableName($uid);
    	$sql = "SELECT COUNT(id) FROM $tbname WHERE uid=:uid AND cid=:cid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid, 'cid' => $cid));
    }
    
	public function getAllData($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT cid FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }
    
}