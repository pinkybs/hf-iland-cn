<?php

/**
 * Event ThanksDay
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/14    zhangli
*/
class Hapyfish2_Island_Event_Dal_ThanksDay
{
    protected static $_instance;

    protected $tbName = 'island_thday_incsite';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Task
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @入驻好友工地统计
     * @param int $uid
     * @param int $fid
     * @param int $time
     */
    public function incSite($uid, $fid, $time)
    {
    	$sql = "INSERT INTO $this->tbName (from_uid, to_uid, for_time) VALUES (:from_uid, :to_uid, :for_time)";
    	
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('from_uid' => $uid, 'to_uid' => $fid, 'for_time' => $time));
    }
    
    /**
     * @获取购买爱心记录
     * @param int $uid
     * @return int
     */
    public function getBuyLove($uid)
    {
    	$sql = "SELECT love FROM island_thday_buylove WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    /**
     * @首次购买爱心记录
     * @param int $uid
     * @param int $love
     */
    public function incBuyLove($uid, $love)
    {
        $sql = "INSERT INTO island_thday_buylove (uid, love) VALUES (:uid, :love)";
    	
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$wdb->query($sql, array('uid' => $uid, 'love' => $love));
    }
    
    /**
     * @增加用户购买爱心值
     * @param int $uid
     * @param int $love
     */
    public function addBuyLove($uid, $love)
    {
    	$sql = "UPDATE island_thday_buylove SET love=love+:love WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'love' => $love));
    }
    
    public function getFlag($uid)
    {
        $sql = "SELECT `level` FROM island_thday_plant WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function incFlag($uid, $level)
    {
		$sql = "INSERT INTO island_thday_plant (uid, `level`) VALUES (:uid, :level)";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'level' =>$level));
    }
    
    public function updateFlag($uid, $level)
    {
        $sql = "UPDATE island_thday_plant SET level=:level WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'level' => $level));
    }
    
    public function getAllUser()
    {
		$sql = "SELECT uid FROM island_thday_plant WHERE `level`=5";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchCol($sql);
    }
    
    public function getLoveMax($uid)
    {
    	$sql = "SELECT love FROM island_thday_lovemax WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function incLoveMax($uid, $love)
    {
    	$sql = "INSERT INTO island_thday_lovemax (uid, love) VALUES (:uid, :love)";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'love' => $love));
    }
    
    public function renewLoveMax($uid, $love)
    {
		$sql = "UPDATE island_thday_lovemax SET love=:love WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'love' => $love));
    }
    
}