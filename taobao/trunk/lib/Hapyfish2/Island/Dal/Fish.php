<?php

class Hapyfish2_Island_Dal_Fish
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Fish
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    protected function getBasicDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    } 
      
    protected function getTableName($uid)
    {
    	$id = floor($uid/8) % 10;
    	return 'island_user_fish_' . $id;
    }
    
    protected function getUlTableName($uid)
    {
    	$id = floor($uid/8) % 10;
    	return 'island_user_fishlocks_' . $id;
    }
       
    protected function getFgTableName($uid)
    {
    	$id = floor($uid/8) % 10;
    	return 'island_user_fishfragment_' . $id;
    }
    
    public function getPtTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 50;
    	return 'island_user_plant_' . $id;
    }
         
    public function getUserFish($uid) 
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$tableName = $this->getTableName($uid);
    	$sql = "SELECT fishid as id,num FROM ".$tableName." WHERE uid=:uid";
    	$data = $rdb->fetchAll($sql, array('uid'=>$uid));
    	return $data;
    }
    
    public function delUserFish($uid) 
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$tableName = self::getTableName($uid);
    	$sql = "DELETE FROM ".$tableName." WHERE uid=:uid";
    	$wdb->query($sql, array('uid'=>$uid));
    }  
      
    public function setUserFish($uid, $fishId) 
    {
    	$num = $this->checkUserFish($uid, $fishId);
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$tableName = $this->getTableName($uid);
    	if($num >= 1) {
    		//update
    		$sql = "UPDATE ".$tableName." SET num=num+1 WHERE uid=:uid AND fishid=:fishid";
    		$wdb->query($sql, array('uid'=>$uid, 'fishid'=>$fishId));
    	}else {
    		//insert
		    $rows = array(
		    	'uid'		=>	$uid, 
		    	'num'		=>	1, 
		    	'fishid'	=>	$fishId
		    );
		    $wdb->insert($tableName, $rows);
    	}
    	
    }

    public function updateUserLocks($uid, $locks)
    {
    	$isLocks = $this->getUserLocks($uid);
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$tableName = $this->getUlTableName($uid);
    	if($isLocks) {
    		$sql = "UPDATE ".$tableName." SET locks=:locks WHERE uid=:uid";
    		$wdb->query($sql, array('uid'=>$uid, 'locks'=>$locks));
    	}else {
 			$rows = array(
		    	'uid'		=>	$uid, 
		    	'locks'		=>	$locks
		    );
		    $wdb->insert($tableName, $rows);   		
    	}
    	
    }
    
    public function getUserLocks($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$tableName = $this->getUlTableName($uid);
    	$sql = "SELECT locks FROM ".$tableName." WHERE uid=:uid";
    	$data = $rdb->fetchOne($sql, array('uid'=>$uid));
    	return $data;
    } 
      
    public function checkUserFish($uid, $fishId) 
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$tableName = $this->getTableName($uid);
    	$sql = "SELECT count(*) as num FROM ".$tableName." WHERE uid=:uid AND fishid=:fishid";
    	$data = $rdb->fetchOne($sql, array('uid'=>$uid, 'fishid'=>$fishId));
    	return $data;
    }
        
    public function getMaps()
    {
    	$db = $this->getBasicDB();
    	$rdb = $db['r'];
    	$sql = 'SELECT * FROM island_fish_map';
    	$data = $rdb->fetchAll($sql);
    	return $data;
    }
    
    public function getIslands()
    {
    	$db = $this->getBasicDB();
    	$rdb = $db['r'];
    	$sql = 'SELECT islandid as id,name,fishids FROM island_fish_islands';
    	$data = $rdb->fetchAll($sql);
    	return $data;
    }  

	public function getIslandInfo($islandId) 
	{
		$data = array();
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        $sql = 'SELECT * FROM island_fish_islands WHERE islandid=:islandid';	
        $data = $rdb->fetchRow($sql, array('islandid'=>$islandId));
    	return $data;     
	}    
    
	public function getFishByIslandid($id)
	{
    	$db = $this->getBasicDB();
        $rdb = $db['r'];
        $sql = "SELECT fishid FROM island_fish_info WHERE islandids like '%$id,%'";
        $data = $rdb->fetchAll($sql);
        return $data;
	} 
	
	public function getFishInfo($fishId) 
	{
		$data = array();
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        $sql = 'SELECT * FROM island_fish_info WHERE fishid=:fishid';	
        $data = $rdb->fetchRow($sql, array('fishid'=>$fishId));
    	return $data;     
	}	

	public function getFishAll() 
	{
		$data = array();
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        $sql = 'SELECT * FROM island_fish_info ORDER BY isfish DESC,fishid ASC';	
        $data = $rdb->fetchAll($sql);
    	return $data;     
	}	

	public function getCatchFishes($islandId, $cannonId) 
	{
		$data = array();
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        $sql = 'SELECT * FROM island_fish_probability WHERE islandid=:islandid AND type=:type';	
        $data = $rdb->fetchAll($sql, array('islandid'=>$islandId, 'type'=>$cannonId));
    	return $data;     
	}
	
	public function getUserPlantInfo($uid, $cid)
	{
		$tbname = $this->getPtTableName($uid);
		
		$sql = "SELECT id,status FROM $tbname WHERE uid=:uid AND cid=:cid LIMIT 1";

		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchRow($sql, array('uid' => $uid, 'cid' => $cid));
        return $data;
	}
	
	public function getUserPlantByItemId($uid, $itemId)
	{
		$tbname = $this->getPtTableName($uid);
		
		$sql = "SELECT cid,item_id,level FROM $tbname WHERE uid=:uid AND item_id=:item_id LIMIT 1";

		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchRow($sql, array('uid' => $uid, 'item_id' => $itemId));
        return $data;
	}
	
	public function getFishPlant()
	{
		$db = $this->getBasicDB();
		$rdb = $db['r'];
		$sql = "SELECT * FROM island_fish_plant";
		$data = $rdb->fetchAll($sql);
		return $data;
	}
	
	public function getFishPlantByItemId($itemId)
	{
		$db = $this->getBasicDB();
		$rdb = $db['r'];
		$sql = "SELECT * FROM island_fish_plant WHERE item_id=:item_id";
		$data = $rdb->fetchRow($sql, array('item_id'=>$itemId));
		return $data;
	}
		
	public function getPlantsByItemId($itemId)
	{
		$db = $this->getBasicDB();
		$rdb = $db['r'];
		$sql = "SELECT cid,name,class_name,item_id,pay_time,level,ticket,content FROM island_plant WHERE item_id=:item_id ORDER BY level ASC";
		$data = $rdb->fetchAll($sql, array('item_id'=>$itemId));
		return $data;
	}

	// Admin Tools
	
	public function updateFishById($fishId, $fileds)
	{
		$db = $this->getBasicDB();
		$wdb = $db['w'];	
		$where = $wdb->quoteinto('fishid = ?', $fishId);
    	return $wdb->update("island_fish_info", $fileds, $where);	
	}
	
	public function getCatchFishesByIslandId($islandId) 
	{
		$data = array();
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        $sql = 'SELECT * FROM island_fish_probability WHERE islandid=:islandid ORDER BY type ASC';	
        $data = $rdb->fetchAll($sql, array('islandid'=>$islandId));
    	return $data;     
	}

	public function getFishNameById($fishId) 
	{
		$data = array();
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        $sql = 'SELECT name FROM island_fish_info WHERE fishid=:fishid';	
        $data = $rdb->fetchOne($sql, array('fishid'=>$fishId));
    	return $data;     
	}
	
	public function updateCatchFish($type, $islandId, $fishId, $probability1, $probability2, $probability3, $probability4)
	{
		$db = $this->getBasicDB();
		$wdb = $db['w'];	
		$sql = "UPDATE island_fish_probability SET probability1=:probability1,probability2=:probability2,probability3=:probability3,probability4=:probability4 WHERE islandid=:islandid AND fishid=:fishid AND type=:type";	
		$wdb->query($sql, array('probability1'=>$probability1, 'probability2'=>$probability2, 'probability3'=>$probability3, 'probability4'=>$probability4, 'islandid'=>$islandId, 'fishid'=>$fishId, 'type'=>$type));	
	}

	public 	function getStat($date)
	{
		$tableName = 'stat_catchfish';
		$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
		$rdb = $db['r'];
		$sql='SELECT * FROM '.$tableName.' WHERE create_time=:create_time';
		$info = $rdb->fetchRow($sql, array('create_time'=>$date));
		return $info;
	}

    public function updateFishPlant($id, $fileds)
    {
		$db = $this->getBasicDB();
		$wdb = $db['w'];	
		$where = $wdb->quoteinto('id = ?', $id);
    	return $wdb->update("island_fish_plant", $fileds, $where);			
    }

    public function addFishPlant($fileds)
    {
		$db = $this->getBasicDB();
		$wdb = $db['w'];	
	    $wdb->insert("island_fish_plant", $fileds); 			
    }

    public function updateUserFish($uid, $info)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$tableName = $this->getTableName($uid);
    	$sql = "update $tableName set num=:num where uid=:uid and fishid=:id ";
    	
        return $wdb->query($sql, array('uid'=>$uid, 'num'=>$info['num'], 'id'=>$info['id'])); 
    }
    
    public function getTask()
    {
    	$sql = 'SELECT id,content,fish_id AS catchFishId,fish_num AS catchFishNum,`type`,awardcid,awardnum FROM island_fish_task_static ORDER BY id ASC';
    	
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        	
        return $rdb->fetchAll($sql);  
    }
    
    public function getTaskId()
    {
		$sql = 'SELECT id,fish_id FROM island_fish_task_static ORDER BY id ASC';
    	
		$db = $this->getBasicDB();
        $rdb = $db['r'];	
        	
        return $rdb->fetchAll($sql);  
    }
    
}