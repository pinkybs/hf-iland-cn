<?php


class Hapyfish2_Island_Dal_PaymentLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_PaymentLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getPaymentLogTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_paylog_' . $id;
    }
    
    public function getPayment($uid, $limit = 50)
    {
    	$tbname = $this->getPaymentLogTableName($uid);
    	$sql = "SELECT amount,gold,summary,create_time FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insert($uid, $info)
    {
		$tbname = $this->getPaymentLogTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function checkNewPayUser($uid)
    {
    	$tbname = $this->getPaymentLogTableName($uid);
    	
    	$sql = "SELECT uid FROM $tbname WHERE uid=:uid AND amount <> 1";
    	
		$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        $hasData = $rdb->fetchOne($sql, array('uid' => $uid));
        
        $result = $hasData ? 1 : 0;
        
        return $result;
    }
    
    public function getPayInfo($uid)
    {
		$tbname = $this->getPaymentLogTableName($uid);
		
		$sql = "SELECT uid AS id,user_level AS level,FROM_UNIXTIME(create_time) AS tm FROM $tbname WHERE uid=:uid AND create_time >= 1319472001 AND create_time <= 1319990401 ORDER BY create_time DESC LIMIT 1";
		
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    
    public function getNewPayInfo($uid)
    {
    	$tbname = $this->getPaymentLogTableName($uid);
		
		$sql = "SELECT uid FROM $tbname WHERE uid=:uid AND create_time < 1319472001";
		
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
}