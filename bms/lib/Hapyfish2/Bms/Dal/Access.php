<?php


class Hapyfish2_Bms_Dal_Access
{
    protected static $_instance;
    
    private $_tb_access = 'bms_access';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Bms_Dal_Access
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTable()
    {
    	return $this->_tb_access;
    }
    
    public function getAccessList($uid)
    {
    	$sql = "SELECT a.pid,b.name,b.title,b.project,a.m_1,a.m_2,a.m_3,a.m_4 FROM bms_access AS a, bms_platform AS b WHERE a.pid=b.pid AND a.uid=:uid ORDER BY b.index ASC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAssoc($sql, array('uid' => $uid));
    }
    
    public function getAccess($uid, $pid)
    {
    	$sql = "SELECT a.pid,b.name,b.title,b.project,a.m_1,a.m_2,a.m_3,a.m_4 FROM bms_access AS a, bms_platform AS b WHERE a.pid=b.pid AND a.uid=:uid AND a.pid=:pid";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid, 'pid' => $pid));
    }
    
    public function insert($info)
    {
		$tbname = $this->getTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$wdb->insert($tbname, $info);
    	
    	return $wdb->lastInsertId();
    }
    
    public function update($uid, $pid, $info)
    {
        $tbname = $this->getTable();
        
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	$where1 = $wdb->quoteinto('uid = ?', $uid);
    	$where2 = $wdb->quoteinto('pid = ?', $pid);
    	$where = array($where1, $where2);
    	
        $wdb->update($tbname, $info, $where);
    }
    
    public function delete($uid, $pid)
    {
    	$tbname = $this->getTable();
    	$sql = "DELETE FROM $tbname WHERE uid=:uid AND pid=:pid";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'pid' => $pid));
    }
}