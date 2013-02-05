<?php

class Hapyfish2_Island_Event_Dal_TeamBuy
{
	protected static $_instance;

	protected $table_teambuy_info = 'island_teambuy_info';
	protected $table_user_teambuy = 'island_user_teambuy';

	public static function getDefaultInstance()
    {

        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @获取团购信息
     * @return Array
     */
    public function getData()
    {
		$sql = "SELECT * FROM $this->table_teambuy_info WHERE `status`=1";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

    	return $rdb->fetchRow($sql);
    }
    
	/**
	 * @用户参加团购状态
	 * @param int $uid
	 * @return int
	 */
    public function getStatus($uid)
    {
		$sql = "SELECT `status` FROM $this->table_user_teambuy WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

    	return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
	/**
	 * @增加用户的团购状态
	 * @param int $uid
	 * @return int
	 */
    public function addStatus($uid)
    {
		$sql = "INSERT INTO $this->table_user_teambuy (uid, `status`) VALUES (:uid, -1)";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid));
    }
    
	/**
	 * @更新用户的团购状态
	 * @param int $uid
	 * @return int
	 */
    public function updateStatus($uid)
    {
		$sql = "UPDATE $this->table_user_teambuy SET `status`=1 WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid));
    }
    
    /**
     * @更新团购信息
     * @param int $num
     */
    public function renewData($num)
    {
		$sql = "UPDATE $this->table_teambuy_info SET `status`=:`status`";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('status' => $num));
    }
    
    /**
     * @参加团购的人数
     * @return int
     */
    public function getNum()
    {
		$sql = "SELECT COUNT(uid) FROM $this->table_user_teambuy";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

	 	return $rdb->fetchOne($sql);
    }
    
    /**
     * @购买的人数
     * @return int
     */
    public function getBuyNum()
    {
		$sql = "SELECT COUNT(uid) FROM $this->table_user_teambuy WHERE `status`=1";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

	 	return $rdb->fetchOne($sql);
    }
    
    /**********后台工具***********/
	public function getTeamBuyMessage()
	{
		$sql = " SELECT * FROM $this->table_teambuy_info ";

        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

    	return $rdb->fetchRow($sql);
	}
    
	public function updateTeamBuyInfo($action)
	{
		$sql = "UPDATE $this->table_teambuy_info SET gid=:gid,`name`=:name,start_time=:start_time,ok_time=:ok_time,
				buy_time=:buy_time,max_price=:max_price,min_price=:min_price,min_num=:min_num,max_num=:max_num,
				start_num=:start_num,bec_num=:bec_num,bec_price=:bec_price,scale_gold=:scale_gold,scale_coin=:scale_coin";

        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

		$wdb->query($sql, array('gid' => $action['gid'], 'name' => $action['name'],
								'start_time' => $action['start_time'], 'ok_time' => $action['ok_time'],'buy_time' => $action['buy_time'],
								'max_price' => $action['max_price'], 'min_price' => $action['min_price'],
								'min_num' => $action['min_num'], 'max_num' => $action['max_num'],
								'start_num' => $action['start_num'],
								'bec_num' => $action['bec_num'], 'bec_price' => $action['bec_price'],
								'scale_gold' => $action['scale_gold'], 'scale_coin' => $action['scale_coin']));
	}
    
	public function getHasUser()
	{
		$sql = "SELECT uid FROM $this->table_user_teambuy LIMIT 5000";

 	    $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

	 	return $rdb->fetchCol($sql);
	}
    
	public function clearOneUser($uid)
	{
		$sql = "DELETE FROM $this->table_user_teambuy WHERE uid=:uid";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$wdb = $db['w'];
		
		$wdb->query($sql, array('uid' => $uid));
	}
    
	public function switchTeamBuy($act)
	{
		$sql = "UPDATE $this->table_teambuy_info SET `status`=:status";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

		$wdb->query($sql, array('status' => $act));
	}

}