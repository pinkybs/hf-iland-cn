<?php

/**
 * platform qzone user info
 *
 *
 * @package    Dal
 * @create      2010/09/25    Hulj
 */
class Hapyfish2_Platform_Dal_UserQzone
{

    protected static $_instance;

    /**
     *
     *
     * @return Hapyfish2_Platform_Dal_UserQzone
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
    	return 'platform_qzone_user_info_' . $id;
    }

    public function getInfo($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT uid,openid,nickname,figureurl,gender,is_vip,is_year_vip,vip_level,create_time FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
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

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $openid = $wdb->quote($user['openid']);
        $nickname = $wdb->quote($user['nickname']);
        $gender = $user['gender'];
        $figureurl = $wdb->quote($user['figureurl']);
        $is_vip = isset($user['is_vip']) ? $user['is_vip'] : 0;
        $is_year_vip = isset($user['is_year_vip']) ? $user['is_year_vip'] : 0;
        $vip_level = isset($user['vip_level']) ? $user['vip_level'] : 0;
        $create_time = time();

        $tbname = $this->getTableName($uid);

        $sql = "INSERT INTO $tbname (uid, openid, nickname, gender, figureurl, is_vip, is_year_vip, vip_level, create_time) VALUES"
              . '(' . $uid . ',' . $openid . ',' . $nickname . ',' . $gender . ',' . $figureurl . ',' . $is_vip . ','
              . $is_year_vip . ',' . $vip_level . ',' . $create_time .')'
              . ' ON DUPLICATE KEY UPDATE '
              . 'openid=' . $openid
              . ',nickname=' . $nickname
              . ',gender=' . $gender
              . ',figureurl=' . $figureurl
              . ',is_vip=' . $is_vip
              . ',is_year_vip=' . $is_year_vip
              . ',vip_level=' . $vip_level;

        return $wdb->query($sql);
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

    public function updateStatus($uid, $status, $time = null)
    {
    	if (!$time) {
    		$time = time();
    	}
    	$tbname = $this->getTableName($uid);
    	$sql = "UPDATE $tbname SET status=:status,status_update_time=$time WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'status' => $status));
    }

    public function getStatus($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT status FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function getStatus2($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT status,status_update_time FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getStatusUpdateTime($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT status_update_time FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

}