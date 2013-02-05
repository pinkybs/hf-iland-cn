<?php

/**
 * Event Christmas
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/25    zhangli
*/
class Hapyfish2_Island_Event_Dal_Christmas
{
    protected static $_instance;

    protected $table_chrismas_collect_list = 'island_chrismas_collect_list';

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
	
    public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_invitelog_' . $id;
    }
    
    /**
     * @领取邀请好友奖励
     * @return uid
     */
    public function getInviteFlag($uid)
    {
    	$sql = "SELECT uid FROM island_chrismas_invite WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    /**
     * @记录领取过邀请好友奖励
     * @param int $uid
     */
    public function addInviteFlag($uid, $nowTime)
    {
		$sql = "INSERT INTO island_chrismas_invite (uid, create_time) VALUES (:uid, :create_time)";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'create_time' => $nowTime));
    }
    
    /**
     * @领取收集物品记录
     * @return uid
     */
    public function getGiftFlag($uid, $taskId)
    {
    	$sql = "SELECT uid FROM $this->table_chrismas_collect_list WHERE uid=:uid AND task_id=:task_id";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid, 'task_id' => $taskId));
    }
    
    /**
     * @记录领取过收集物品
     * @param int $uid
     */
    public function addGiftFlag($uid, $taskId)
    {
		$sql = "INSERT INTO $this->table_chrismas_collect_list (uid, task_id) VALUES (:uid, :task_id)";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'task_id' => $taskId));
    }
    
    /**
     * @获取邀请好友列表
     * @param int $uid
     * @param int $time
     * @return Array
     */
    public function getAllFidList($uid, $time)
    {
    	$tbname = $this->getTableName($uid);

    	$sql = "SELECT fid FROM $tbname WHERE uid=:uid AND `time`>$time ORDER BY `time` ASC LIMIT 5";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }
    
    public function clearGetFlag($uid)
    {
    	$tbname = $this->getTableName($uid);
    	
		$sql = "DELETE FROM $this->table_chrismas_collect_list WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
}