<?php

/**
 * Event SpringFestival
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/09    zhangli
*/
class Hapyfish2_Island_Event_Dal_SpringFestival
{
    protected static $_instance;

    protected $table_sf_basic_data = 'island_sf_basic_data';
    protected $table_sf_luckybag_list = 'island_sf_luckybag_list';
    protected $table_sf_dumpling_list = 'island_sf_dumpling_list';
    protected $table_sf_user_data = 'island_sf_user_data';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_SpringFestival
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getBasicData()
    {
		$sql = "SELECT cid,`time`,plant,to_send,`list` FROM $this->table_sf_basic_data ORDER BY id ASC";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
    }

    public function getLuckyBagList()
    {
		$sql = "SELECT item_order,item_name,item_id,item_type,item_num,item_odds FROM $this->table_sf_luckybag_list ORDER BY item_order ASC";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
    }
    
    public function getDumplingBasic()
    {
		$sql = "SELECT item_order,item_name,item_id,item_type,item_num,item_odds FROM $this->table_sf_dumpling_list ORDER BY item_order ASC";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
    }
    
    public function incUserData($uid)
    {
    	$sql = "INSERT INTO $this->table_sf_user_data (uid) VALUES (:uid)";
    	
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('uid' => $uid));
    }
    
    public function getFragmentData($uid)
    {
		$sql = "SELECT data_str,state FROM $this->table_sf_user_data WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        $listVo = $rdb->fetchRow($sql, array('uid' => $uid));
        
        if (!$listVo) {
        	$insert = "INSERT INTO $this->table_sf_user_data (uid) VALUES (:uid)";
        	
        	$wdb = $db['w'];
        	$wdb->query($insert, array('uid' => $uid));
        	
        	$listVo = $rdb->fetchRow($sql, array('uid' => $uid));
        }
        
        return $listVo;
    }

    public function renewFragmentData($uid, $state, $data_str)
    {
    	$sql = "UPDATE $this->table_sf_user_data SET data_str=:data_str,state=:state WHERE uid=:uid";
    	
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];
    	
    	$wdb->query($sql, array('uid' => $uid, 'data_str' => $data_str, 'state' => $state));
    }
    
    public function getCurCrystalNum($uid)
    {
		$sql = "SELECT curcrystal FROM $this->table_sf_user_data WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function renewCurCrystalNum($uid, $num)
    {
		$sql = "UPDATE $this->table_sf_user_data SET curcrystal=:curcrystal WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid, 'curcrystal' => $num));
    }
    
    public function getLuckyBagNum($uid)
    {
		$sql = "SELECT luckybag FROM $this->table_sf_user_data WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function renewLuckyBag($uid, $num)
    {
		$sql = "UPDATE $this->table_sf_user_data SET luckybag=:luckybag WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid, 'luckybag' => $num));
    }
    
    public function getDumplingNum($uid)
    {
		$sql = "SELECT dumpling FROM $this->table_sf_user_data WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function renewDumplingNum($uid, $dnum)
    {
		$sql = "UPDATE $this->table_sf_user_data SET dumpling=:dumpling WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'dumpling' => $dnum));
    }
    
    public function deleteOne($uid)
    {
		$sql = "DELETE FROM $this->table_sf_user_data WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
}