<?php

/**
 * platform user info more
 *
 *
 * @package    Dal
 * @create      2010/09/25    Hulj
 */
class Hapyfish2_Platform_Dal_UserMore
{

    protected static $_instance;

    /**
     *
     *
     * @return Hapyfish2_Platform_Dal_UserMore
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
    	return 'platform_user_info_more_' . $id;
    }

    public function getUser($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT * FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    /**
     * insert new platform uid
     *
     * @param string $puid
     * @return integer
     */
    public function add($user)
    {
    	$uid = $user['uid'];
        $info = $user['info'];
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        //$puid = $wdb->quote($user['puid']);
        $tbname = $this->getTableName($uid);

        $sql = "INSERT INTO $tbname (uid, info) VALUES (:uid, :info)"
              . " ON DUPLICATE KEY UPDATE info=:info ";

        return $wdb->query($sql, array('uid'=>$uid, 'info'=>$info));
    }

    /**
     * get inner uid
     *
     * @param string $puid
     * @return integer
     */
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $where = $wdb->quoteinto('uid = ?', $uid);

        return $wdb->update($tbname, $info, $where);
    }

}