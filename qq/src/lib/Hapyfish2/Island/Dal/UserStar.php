<?php


class Hapyfish2_Island_Dal_UserStar
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_UserStar
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
    	return 'island_user_island_info_' . $id;
    }
    
    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT star_list FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('uid = ?', $uid);
    	
        return $wdb->update($tbname, $info, $where);
    }
    
}