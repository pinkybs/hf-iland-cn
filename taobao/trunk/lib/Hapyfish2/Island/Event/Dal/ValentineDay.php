<?php

/**
 * Event ValentineDay
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/02/06    zhangli
*/
class Hapyfish2_Island_Event_Dal_ValentineDay
{
    protected static $_instance;

    protected $table_val_user_data = 'island_val_user_data';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_ValentineDay
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    //获取用户的玫瑰数据
    public function getRoseList($uid)
    {
		$sql = "SELECT rose_1,rose_2,rose_3,rose_4,rose_5,rose_6 FROM $this->table_val_user_data WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    
    //更新用户的玫瑰数量
    public function renewRoseList($uid, $roseList)
    {
		$sql = "UPDATE $this->table_val_user_data SET rose_1=:rose_1,rose_2=:rose_2,rose_3=:rose_3,rose_4=:rose_4,rose_5=:rose_5,rose_6=:rose_6 WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid,
        					'rose_1' => $roseList['rose_1'],
        					'rose_2' => $roseList['rose_2'],
        					'rose_3' => $roseList['rose_3'],
        					'rose_4' => $roseList['rose_4'],
        					'rose_5' => $roseList['rose_5'],
        					'rose_6' => $roseList['rose_6']));
    }
    
    public function getRoseGroups()
    {
        $sql = "SELECT gid,name,tips,needs,awards FROM island_val_group ";
        
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAssoc($sql);
    }
    
    public function getFirstQuest($uid)
    {
		$sql = "SELECT uid FROM $this->table_val_user_data WHERE uid=:uid";

		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function incFirstQuest($uid)
    {
    	$sql = "INSERT INTO $this->table_val_user_data (uid) VALUES (:uid)";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }

}