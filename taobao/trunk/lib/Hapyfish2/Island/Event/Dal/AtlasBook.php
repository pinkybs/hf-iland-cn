<?php

/**
 * Event AtlasBook
 *
 * @package    Island/Event/Dal
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2012/01/03    zhangli
*/
class Hapyfish2_Island_Event_Dal_AtlasBook
{
    protected static $_instance;

    protected $table_event_atlasbook_items = 'island_event_atlasbook_items';
    protected $table_event_atlasbook_list = 'island_event_atlasbook_list';

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
	
    //获取已开放的图鉴
	public function getData()
	{
		$sql = "SELECT id,`name`,items FROM $this->table_event_atlasbook_items";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
	}
	
	//获取集齐当前图鉴的人数
	public function getNum()
	{
		$sql = "SELECT id,num FROM $this->table_event_atlasbook_items";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
	}
	
	//更新收集的人数
	public function renewNum($id, $num)
	{
		$sql = "UPDATE $this->table_event_atlasbook_items SET num=:num WHERE id=:id";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('id' => $id, 'num' => $num));
	}
	
	//获取用户拥有的图鉴
	public function getUserData($uid)
	{
		$sql = "SELECT `list_str` FROM $this->table_event_atlasbook_list WHERE uid=:uid";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
	}
	
	//加入用户图鉴信息
	public function incUserData($uid, $str)
	{
		$sql = "INSERT INTO $this->table_event_atlasbook_list (uid, list_str) VALUES (:uid, :list_str)";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'list_str' => $str));
	}
	
	//更新用户的图鉴
	public function renewUserData($uid, $str)
	{
		$sql = "UPDATE $this->table_event_atlasbook_list SET list_str=:list_str WHERE uid=:uid";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'list_str' => $str));
	}
    
	public function getLastID()
	{
		$sql = "SELECT id FROM $this->table_event_atlasbook_items ORDER BY id DESC LIMIT 1";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql);
	}
	
	public function atlasbookaddnew($id)
	{
		$sql = "INSERT INTO $this->table_event_atlasbook_items (id) VALUES (:id)";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('id' => $id));
	}
	
	public function atlasbookupdate($data)
	{
		$sql = "UPDATE $this->table_event_atlasbook_items SET `name`=:name,items=:items WHERE id=:id";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('id' => $data['id'], 'name' => $data['name'], 'items' => $data['items']));
	}
	
	public function atlasbookdel($id)
	{
		$sql = "DELETE FROM $this->table_event_atlasbook_items WHERE id=:id";
		
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('id' => $id));
	}
	
}