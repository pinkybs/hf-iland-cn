<?php


class Hapyfish2_Island_Event_Dal_Newyear
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_Newyear
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function get($uid)
    {
    	$sql = "SELECT uid,red_paper,red_cracker,gain_treasure FROM island_user_event_newyear WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
    	$tbname = 'island_user_event_newyear';

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function delete($uid)
    {
    	$sql = "DELETE FROM island_user_event_newyear WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid));
    }

    /**
     * update
     *
     * @param integer $uid
     * @param array $info
     * @return void
     */
    public function update($uid, $info)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $where = $wdb->quoteinto('uid = ?', $uid);
        return $wdb->update('island_user_event_newyear', $info, $where);
    }

	/**
     * update user Newyear by field name
     *
     * @param integer $uid
     * @param string $field
     * @param integer $change
     * @return void
     */
    public function updateByField($uid, $field, $change)
    {
        $sql = "UPDATE island_user_event_newyear SET $field = $field + :change WHERE uid=:uid ";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql,array('uid'=>$uid, 'change'=>$change));
    }

	/**
     * update user Newyear by multiple field name
     *
     * @param integer $uid
     * @param array $param
     * @return void
     */
    public function updateByMultipleField($uid, $param)
    {
        $change = array();
        foreach ( $param as $k => $v ) {
            $change[] = $k . '=' . $k . '+' . $v;
        }
        $s1 = join(',', $change);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "UPDATE island_user_event_newyear SET $s1 WHERE uid=:uid ";
        return $wdb->query($sql,array('uid'=>$uid));
    }

	/**
     * add user Newyear
     *
     * @param integer $uid
     * @param string $field
     * @param integer $num
     * @return integer
     */
    public function addUserNewyear($uid, $field, $num)
    {
    	$row = $this->get($uid);
        if (empty($row)) {
        	return $this->insert($uid, array('uid' => $uid, $field => $num));
        }
        else {
            return $this->updateByField($uid, $field, $num);
        }
    }
}