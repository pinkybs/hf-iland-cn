<?php


class Hapyfish2_Island_Dal_Background
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Background
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
    	$id = floor($uid/24) % 40;
    	return 'island_user_background_' . $id;
    }
    
    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,bgid,item_type FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function insert($uid, $id, $bgId, $itemType, $buyTime = null)
    {
        if (!$buyTime) {
        	$buyTime = time();
        }
        
    	$tbname = $this->getTableName($uid);
        
        $sql = "INSERT INTO $tbname(uid, id, bgid, item_type, buy_time) VALUES(:uid, $id, :bgid, :item_type, $buyTime)";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	
        return $wdb->query($sql, array('uid' => $uid, 'bgid' => $bgId, 'item_type' => $itemType));
    }
    
    public function delete($uid, $id)
    {
        $tbname = $this->getTableName($uid);
        
        $sql = "DELETE FROM $tbname WHERE uid=:uid AND id=:id";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'id' => $id));
    }
    
    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        
        $sql = "INSERT INTO $tbname(uid, id, bgid, item_type) VALUES(:uid, 1, 27211, 11),(:uid, 2, 24112, 12),(:uid, 3, 22913, 13),(:uid, 4, 99714, 14)";
        
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
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
    public function initNewIsland($uid, $islandId)
    {
        $tbname = $this->getTableName($uid);
        
        if ( $islandId == 2 ) {
        	$sql = "INSERT INTO $tbname(uid, id, bgid, item_type) VALUES(:uid, 11, 85911, 11),(:uid, 12, 23212, 12),(:uid, 13, 22213, 13),(:uid, 14, 25914, 14)";
        }
        else if ( $islandId == 3 ) {
        	$sql = "INSERT INTO $tbname(uid, id, bgid, item_type) VALUES(:uid, 1, 25411, 11),(:uid, 2, 23212, 12),(:uid, 3, 22213, 13),(:uid, 4, 25914, 14)";
        }
        else if ( $islandId == 4 ) {
        	$sql = "INSERT INTO $tbname(uid, id, bgid, item_type) VALUES(:uid, 1, 25411, 11),(:uid, 2, 23212, 12),(:uid, 3, 22213, 13),(:uid, 4, 25914, 14)";
        }
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
    public function getAllData($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT bgid FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }
}