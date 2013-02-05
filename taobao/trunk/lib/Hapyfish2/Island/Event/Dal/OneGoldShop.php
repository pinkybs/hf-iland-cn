<?php

/**
 * Event OneGoldShop
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/07/26    zhangli
*/
class Hapyfish2_Island_Event_Dal_OneGoldShop
{
	protected static $_instance;

	protected $table_onegold_shop_gift = 'island_onegold_shop_gift';
	protected $table_onegold_shop_box = 'island_onegold_shop_box';
	
    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_Casino
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTBName($uid)
    {
    	$id = floor($uid / DATABASE_NODE_NUM) % 10;
    	return 'island_user_onegold_shop_' . $id;
    }
	
    /**
     * 获取一元店所有信息
     */
    public function getAllOneGoldGift()
    {
    	$sql = "SELECT id,cid,num,gold,coin,starfish,start_time,end_time FROM $this->table_onegold_shop_gift WHERE get_status=0 ORDER BY id ASC";
    	
    	$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
    }
    
    //获取用户领取状态
    public function getBuyStatus($uid)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "SELECT has_get FROM $TBname WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    //用户参加活动次数
    public function getBuyNum($uid)
    {
        $TBname = $this->getTBName($uid);
    	
    	$sql = "SELECT buy_num FROM $TBname WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    //查询用户是否参加过活动
    public function getAct($uid)
    {
    	$TBname = $this->getTBName($uid);
		
		$sql = "SELECT uid FROM $TBname WHERE uid=:uid";
		
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	
    	return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    //增加用户充值信息
    public function incBuyStatus($uid)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "INSERT INTO $TBname (uid, `count`, has_get, all_pay_num) VALUES (:uid, 1, 1, 1)";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('uid' => $uid));
    }
    
    //更新充值信息
    public function repBuyStatus($uid, $step)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "UPDATE $TBname SET all_pay_num=all_pay_num+1,has_get=:has_get WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('uid' => $uid, 'has_get' => $step));
    }
    
    //更新用户本期抢购状态
    public function refurbishHasGet($uid)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "UPDATE $TBname SET has_get=0,buy_num=buy_num+1 WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('uid' => $uid));
    }
    
    //查询用户领取到哪一期礼包了
    public function hasCountBox($uid)
    {
        $TBname = $this->getTBName($uid);
    	
    	$sql = "SELECT `count` FROM $TBname WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function getTime()
    {
    	$sql = "SELECT start_time,end_time FROM $this->table_onegold_shop_gift WHERE get_status=0 ORDER BY id ASC LIMIT 1";
    	
    	$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	
    	return $rdb->fetchRow($sql);
    }
    
    //获取本期物品
    public function oneGoldShop($falseTime)
    {
    	//$sql = "SELECT * FROM $this->table_onegold_shop_gift WHERE get_status=0 ORDER BY id ASC LIMIT 1";
    	$sql = "SELECT * FROM $this->table_onegold_shop_gift WHERE end_time=:end_time";
    	
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
        $wdb = $db['w'];
        
        $data = $rdb->fetchRow($sql, array('end_time' => $falseTime));

    	$UPSQL = "UPDATE $this->table_onegold_shop_gift SET get_status=1 WHERE id=:id";
    	
    	$wdb->query($UPSQL, array('id' => $data['id']));
    	
    	return $data;
    }

    //一元充值记录
    public function addOneGoldPay($uid, $itety)
    {
    	$TBname = $this->getTBName($uid);

    	$newSQL = "SELECT `count`,all_pay_num,has_get FROM $TBname WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$wdb = $db['w'];
    	
    	$data = $rdb->fetchRow($newSQL, array('uid' => $uid));

    	$payCount = 0;
    	if ($data === false) {
    		$INsql = "INSERT INTO $TBname (uid, `count`, has_get, all_pay_num) VALUES (:uid, 1, 1, 1)";

    		$wdb->query($INsql, array('uid' => $uid));
    		
    		return 1;
    	} else {
    		$UPsql = "UPDATE $TBname SET all_pay_num=all_pay_num+1,has_get=:has_get WHERE uid=:uid";
 		
    		$payCount = $data['all_pay_num'] + 1;
	    	$wdb->query($UPsql, array('uid' => $uid, 'has_get' => $itety));
	
	    	return $payCount;
    	}	
    }
    
    //获取用户1元充值次数
    public function getOneGoldHasGet($uid)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "SELECT `count`,has_get,buy_num FROM $TBname WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    

    
    //更新用户领取礼包的步
    public function updateCountBox($uid, $stay)
    {
    	$TBname = $this->getTBName($uid);
    	
        $sql = "UPDATE $TBname SET `count`=:count WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid, 'count' => $stay));
    }
    
    //获取用户礼包领取状态
    public function getOneGoldBox($uid)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "SELECT `status` FROM $TBname WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    //更新用户领取礼包状态
    public function updateOneGoldBox($uid, $enData)
    {
    	$TBname = $this->getTBName($uid);
    	
    	$sql = "UPDATE $TBname SET `status`=:status WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('uid' => $uid, 'status' => $enData));
    	return true;
    }
    
    //获取礼包信息
    public function getBoxInfo($boxID)
    {
    	$sql = "SELECT box_id,idx,`data`,coin,gold,starfish FROM $this->table_onegold_shop_box WHERE box_id=:box_id";
    	
 		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql, array('box_id' => $boxID));
    }
    
    //获取本期物品结束时间
    public function getStartTime($id)
    {
    	$sql = "SELECT start_time,end_time FROM $this->table_onegold_shop_gift WHERE id=:id";
    	
		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('id' => $id));
    }
    
    //更新礼包领取次数
    public function  refrushBoxAct($idx)
    {
		$sql = "UPDATE $this->table_onegold_shop_box SET get_num=get_num+1 WHERE idx=:idx";
    	
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];

        $wdb->query($sql, array('idx' => $idx));
    }
    
    public function AllData()
    {
    	$sql = "SELECT id,cid,num,gold,coin,starfish,get_status,start_time,end_time FROM $this->table_onegold_shop_gift WHERE get_status=0 ORDER BY id ASC";
    	
    	$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	
    	return $rdb->fetchAll($sql);
    }
    
    public function updateStatus($id)
    {
		$sql = "UPDATE $this->table_onegold_shop_gift SET get_status=1 WHERE id<=:id";
    	
		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('id' => $id));
    }
    
    public function addNewOne()
    {
    	$sqlget = "SELECT id,end_time FROM $this->table_onegold_shop_gift ORDER BY id DESC LIMIT 1";
    	
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	$wdb = $db['w'];
    	
    	$lastData = $rdb->fetchRow($sqlget);
    	$newID = $lastData['id'] + 1;
  	
    	$hour = date('H', $lastData['end_time']);
    	if ($hour == 14) {
    		$year = date('Y-m-d', $lastData['end_time']);
    		$newTime = $year . 22 . ':00:00';
    		$newEndTime = strtotime($newTime);
    	} else {
    		$year = date('Y-m', $lastData['end_time']);
    		$mouth = date('d', $lastData['end_time']);
    		$mouthNew = $mouth + 1;
    		if ($mouthNew > 9) {
    			$newTime = $year . '-' . $mouthNew . 14 . ':00:00';
    		} else {
    			$newTime = $year . '-0' . $mouthNew . 14 . ':00:00';
    		}
        	
    		$newEndTime = strtotime($newTime);
    	}

    	$sql = "INSERT INTO $this->table_onegold_shop_gift (id, start_time, end_time) VALUES (:id, :start_time, :end_time)";
    	
    	$wdb->query($sql, array('id' => $newID, 'start_time' => $lastData['end_time'], 'end_time' => $newEndTime));
    }
    
    public function update($data, $start_time, $end_time)
    {
    	$sql = "UPDATE $this->table_onegold_shop_gift SET cid=:cid,num=:num,gold=:gold,coin=:coin,starfish=:starfish,start_time=:start_time,end_time=:end_time WHERE id=:id";
    	
		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('id' => $data['id'], 'cid' => $data['cid'], 
    							'num' => $data['num'], 'gold' => $data['gold'],
    							'coin' => $data['coin'], 'starfish' => $data['starfish'], 
    							'start_time' => $start_time, 'end_time' => $end_time));
    }
    
    public function boxInfo()
    {
    	$sql = "SELECT * FROM $this->table_onegold_shop_box ORDER BY idx ASC";
    	
		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$rdb = $db['r'];
    	
    	return $rdb->fetchAll($sql);
    }
    
    public function boxUpdate($dataVo)
    {
    	$sql = "UPDATE $this->table_onegold_shop_box SET box_id=:box_id,`data`=:data,coin=:coin,gold=:gold,starfish=:starfish WHERE idx=:idx";

		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('box_id' => $dataVo['box_id'], 'idx' => $dataVo['idx'], 'data' => $dataVo['data'], 
    							'coin' => $dataVo['coin'], 'gold' => $dataVo['gold'], 'starfish' => $dataVo['starfish']));
    }
    
    public function incNewBox()
    {
    	$sqlGet = "SELECT idx,box_id FROM $this->table_onegold_shop_box ORDER BY idx DESC LIMIT 1";
    	
		$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
    	$wdb = $db['w'];
    	$rdb = $db['r'];
    	
    	$lastData = $rdb->fetchRow($sqlGet);
    	$newIdx = $lastData['idx'] + 10;
    	
    	if (($lastData['idx'] % 3) == 0) {
    		$newBoxID = $lastData['box_id'] + 1;
    	} else {
    		$newBoxID = $lastData['box_id'];
    	}
	
    	$sql = "INSERT INTO $this->table_onegold_shop_box (box_id, idx) VALUES (:box_id, :idx)";
    	
    	$wdb->query($sql, array('box_id' => $newBoxID, 'idx' => $newIdx));
    }
    
}