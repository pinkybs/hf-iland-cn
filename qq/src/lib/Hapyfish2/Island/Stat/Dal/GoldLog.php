<?php


class Hapyfish2_Island_Stat_Dal_GoldLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Stat_Dal_GoldLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getGoldLogTableName($tbId, $yearmonth)
    {
    	return 'island_user_goldlog_' . $yearmonth . '_' . $tbId;
    }
    
    public function getGoldLogSumNum($dbId, $tbId, $yearmonth, $startTime, $endTime)
    {
    	$tbname = $this->getGoldLogTableName($tbId, $yearmonth);
    	$sql = "SELECT sum(cost) FROM $tbname WHERE `create_time`>=$startTime AND `create_time`<$endTime ";
    	
        $db = Hapyfish2_Db_Factory::getDB($dbId);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql);
    }

    public function getGoldLog($dbId, $tbId, $yearmonth, $startTime, $endTime)
    {
        $tbname = $this->getGoldLogTableName($tbId, $yearmonth);
        $sql = "SELECT uid,cost,is_vip FROM $tbname WHERE `create_time`>=$startTime AND `create_time`<$endTime ";
        
        $db = Hapyfish2_Db_Factory::getDB($dbId);
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
    }

}