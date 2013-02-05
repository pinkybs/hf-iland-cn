<?php
class Hapyfish2_Island_Event_Dal_LuckyDraw
{
	protected static $_instance;

	protected $table_luckydraw = 'island_luckydraw';
	protected $table_luckydraw_user = 'island_luckydraw_user';
	protected $table_lucky_cdkey = 'island_lucky_cdkey';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Card
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getLuckyDrawInfo()
    {
		$sql = " SELECT * FROM $this->table_luckydraw ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

		return $rdb->fetchRow($sql);
    }

    public function luckyDrawGift($uid)
    {
    	$sql = " SELECT uid FROM $this->table_luckydraw_user where uid=:uid ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

		return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function getCDKey($count)
    {
    	$sql = " SELECT cd_key FROM $this->table_lucky_cdkey WHERE count<>:count ORDER BY RAND() LIMIT 1 ";

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

    	$CDKey = $rdb->fetchOne($sql, array('count' => $count));

    	if($CDKey) {
	    	$sqlnews = " UPDATE $this->table_lucky_cdkey SET count=:count WHERE cd_key=:cd_key ";

        	$wdb = $db['w'];

	    	$wdb->query($sqlnews, array('count' => $count, 'cd_key' => $CDKey));
    	}

    	return $CDKey;
    }

    public function insert($uid)
    {
    	$sql = " INSERT INTO $this->table_luckydraw_user (uid) VALUES ($uid) ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$wdb = $db['w'];

		$wdb->query($sql, array('uid' => $uid));
    }

    public function update($uid, $CDKey)
    {
		$sql = " UPDATE $this->table_luckydraw_user SET cd_key=:cd_key WHERE uid=:uid ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$wdb = $db['w'];

		$wdb->query($sql, array('uid' => $uid, 'cd_key' => $CDKey));
    }

    public function checkCDKey($uid)
    {
    	$sql = " SELECT cd_key FROM $this->table_luckydraw_user WHERE uid=:uid ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$rdb = $db['r'];

    	$result = $rdb->fetchOne($sql, array('uid' => $uid));

    	if($result) {
    		return $result;
    	} else {
    		return -1;
    	}
    }

    public function getJoinNum()
    {
    	$sql = " SELECT count(cd_key) FROM $this->table_luckydraw_user WHERE cd_key > -1 ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$rdb = $db['r'];

    	return $rdb->fetchOne($sql);
    }

    public function deleteDW()
    {
    	$sql = " DELETE FROM $this->table_luckydraw ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql);
    }

    public function delete($uid)
    {
    	$sql = " DELETE FROM $this->table_luckydraw_user WHERE uid=:uid ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid));
    }

    public function deleteCDK($uid)
    {
    	$sql = " UPDATE $this->table_luckydraw_user SET cd_key=-1 WHERE uid=:uid ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid));
    }

    public function insertCDK($cdk)
    {
    	$sql = " INSERT INTO $this->table_lucky_cdkey (cd_key) VALUES ($cdk) ";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('cd_key' => $cdk));
    }

}