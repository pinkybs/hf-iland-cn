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
    
}