<?php

/**
 * Event ReceivePlant
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2012 Happyfish Inc.
 * @create     2012/01/11    zhangli
*/
class Hapyfish2_Island_Event_Dal_ReceivePlant
{
    protected static $_instance;

    protected $table_event_exchange_list = 'island_event_exchange_list';

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
	
	public function getExchangeAble($uid)
	{
		$sql = "SELECT list_str FROM $this->table_event_exchange_list WHERE uid=:uid";
    	
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
	}
	
	public function renewExchangeAble($uid, $listStr)
	{
		$sql = "UPDATE $this->table_event_exchange_list SET list_str=:list_str WHERE uid=:uid";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'list_str' => $listStr));
	}
	
	public function incExchangeAble($uid, $listStr)
	{
		$sql = "insert into $this->table_event_exchange_list (uid, list_str) values (:uid, :list_str)";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'list_str' => $listStr));
	}
}