<?php

class Hapyfish2_Island_Event_Dal_PanicBuy
{
	protected static $_instance;

	protected $table_panic_gift = 'island_panic_gift';
	protected $table_panic_box = 'island_panic_box';

	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getTableName($uid)
    {
    	$id = $uid % 10;
    	return 'island_user_panic_' . $id;
    }

    public function getAllData()
    {
    	$sql = "SELECT * FROM $this->table_panic_gift WHERE sale_status=0 ORDER BY sale_id ASC";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        return $rdb->fetchAll($sql);
    }

	public function getBoxVo($boxID)
	{
    	$sql = "SELECT * FROM $this->table_panic_box WHERE box_id=:box_id ORDER BY idx ASC";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('box_id' => $boxID));
	}

    //查询用户领取到哪一期礼包了
    public function hasCountBox($uid)
    {
        $tbname = $this->getTableName($uid);

    	$sql = "SELECT box_id FROM $tbname WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    //获取用户礼包领取状态
    public function getPanicBox($uid)
    {
    	$tbname = $this->getTableName($uid);;

    	$sql = "SELECT box_status FROM $tbname WHERE uid=:uid";

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

	public function updatePanicBox($uid, $boxStr)
	{
    	$tbname = $this->getTableName($uid);;

    	$sql = "UPDATE $tbname SET box_status=:box_status WHERE uid=:uid";

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid, 'box_status' => $boxStr));
	}

    public function getUserBuyCount($uid)
    {
    	$tbname = $this->getTableName($uid);

    	$sql = "SELECT buy_count FROM $tbname WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

		$buyCount = $rdb->fetchOne($sql, array('uid' => $uid));

		if ($buyCount === false) {
			$wdb = $db['w'];

			$sqlAdd = "INSERT INTO $tbname (uid) VALUES (:uid)";

			$wdb->query($sqlAdd, array('uid' => $uid));

			$buyCount = 0;
		}

        return $buyCount;
    }

    public function updateUserBuyCount($uid, $nowhasCount)
    {
    	$tbname = $this->getTableName($uid);

    	$sql = "UPDATE $tbname SET buy_count=:buy_count WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid, 'buy_count' => $nowhasCount));
    }

	//更新用户领取礼包的步
    public function updateCountBox($uid, $stay)
    {
    	$tbname = $this->getTableName($uid);

        $sql = "UPDATE $tbname SET box_id=:box_id WHERE uid=:uid";

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid, 'box_id' => $stay));
    }

	public function addNewData()
	{
		$sqlSELECT = "SELECT sale_id,sale_price,sale_type,cid,num,sale_num,end_time FROM $this->table_panic_gift ORDER BY sale_id DESC LIMIT 1";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$wdb = $db['w'];
        $rdb = $db['r'];

        $lastData = $rdb->fetchRow($sqlSELECT);
       
        $id = $lastData['sale_id'] + 1;
		$startTime = $lastData['end_time'];
		$endTime = $lastData['end_time'] + 3600 * 2;
		
        $sql = "INSERT INTO $this->table_panic_gift (sale_id, sale_price, sale_type, cid, num, sale_num, start_time, end_time) VALUES (:sale_id, :sale_price, :sale_type, :cid, :num, :sale_num, :start_time, :end_time)";

        $wdb->query($sql, array('sale_id' => $id,
        						'sale_price' => $lastData['sale_price'],
						        'sale_type' => $lastData['sale_type'],
						        'cid' => $lastData['cid'],
						        'num' => $lastData['num'],
        						'sale_num' => $lastData['sale_num'],
        						'start_time' => $startTime,
        						'end_time' => $endTime));
	}

	public function panicupdate($data)
	{
		$sql = "UPDATE $this->table_panic_gift SET sale_price=:sale_price,sale_type=:sale_type,cid=:cid,num=:num,sale_num=:sale_num,start_time=:start_time,end_time=:end_time WHERE sale_id=:sale_id";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$wdb = $db['w'];

		$wdb->query($sql, array('sale_id' => $data['sale_id'],
								'sale_price' => $data['sale_price'],
								'sale_type' => $data['sale_type'],
								'cid' => $data['cid'],
								'num' => $data['num'],
								'sale_num' => $data['sale_num'],
								'start_time' => $data['start_time'],
								'end_time' => $data['end_time']));
	}

	public function getAllBox()
	{
		$sql = "SELECT * FROM $this->table_panic_box ORDER BY idx ASC";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
		$rdb = $db['r'];

		return $rdb->fetchAll($sql);
	}

    public function boxUpdate($dataVo)
    {
    	$sql = "UPDATE $this->table_panic_box SET box_id=:box_id,sale_data=:sale_data,coin=:coin,starfish=:starfish WHERE idx=:idx";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];

    	$wdb->query($sql, array('box_id' => $dataVo['box_id'], 'idx' => $dataVo['idx'], 'sale_data' => $dataVo['sale_data'], 'coin' => $dataVo['coin'], 'starfish' => $dataVo['starfish']));
    }

    public function incNewBox()
    {
    	$sqlGet = "SELECT idx FROM $this->table_panic_box ORDER BY idx DESC LIMIT 1";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];
    	$rdb = $db['r'];

    	$lastIdx = $rdb->fetchOne($sqlGet);
    	$newIdx = $lastIdx + 20;

    	$sql = "INSERT INTO $this->table_panic_box (idx) VALUES (:idx)";

    	$wdb->query($sql, array('idx' => $newIdx));
    }

    public function updateStatus($id)
    {
		$sql = "UPDATE $this->table_panic_gift SET sale_status=1 WHERE sale_id<=:sale_id";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('sale_id' => $id));
    }
    
}