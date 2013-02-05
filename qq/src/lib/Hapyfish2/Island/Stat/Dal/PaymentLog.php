<?php


class Hapyfish2_Island_Stat_Dal_PaymentLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Stat_Dal_PaymentLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getPaymentLogTableName($id)
    {
    	return 'island_user_paylog_' . $id;
    }
    
    public function getPayOrderFlowTableName($tbId, $yearmonth)
    {
    	return 'island_user_payorder_flow_' . $yearmonth . '_' . $tbId;
    }
    
    public function getPaymentLogData($dbId, $tbId, $begin, $end)
    {
    	$tbname = $this->getPaymentLogTableName($tbId);
    	$sql = "SELECT uid,amount,gold,user_level,create_time FROM $tbname WHERE create_time>=:begin AND create_time<:end";

        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function getPayOrderFlowOfSuccess($dbId, $tbId, $yearmonth, $startTime, $endTime)
    {
    	$tbname = $this->getPayOrderFlowTableName($tbId, $yearmonth);
    	$sql = "SELECT `time`,amount,is_vip,item_id,item_num,result,platform,uid,user_level FROM $tbname WHERE `time`>=$startTime AND `time`<$endTime AND cmd=7";
    	
        $db = Hapyfish2_Db_Factory::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    

}