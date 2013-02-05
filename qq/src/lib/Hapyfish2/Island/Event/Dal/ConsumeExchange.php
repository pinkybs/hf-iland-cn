<?php


class Hapyfish2_Island_Event_Dal_ConsumeExchange
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Event_Dal_NewyearExchange
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getExchangeTableName($uid){
    	$id = floor($uid/24) % 10;
    	return 'island_user_event_consume_exchange_' . $id;
    }
    public function getGoldTableName($uid,$time){
        $yearmonth = date('Ym',$time);
        $id = floor($uid/24) % 10;
    	return 'island_user_goldlog_' . $yearmonth . '_' . $id;
    }
    public function geteventDB(){
        $key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
    }
    public function getConsumeStep($uid,$start,$end){
        $tbname = $this->getExchangeTableName($uid);
        $sql = "SELECT step FROM $tbname WHERE uid=:uid and `create_time`>=:start and `create_time`<=:end";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchCol($sql, array('uid' => $uid,'start' => $start, 'end' => $end));
    }
    public function getGold($uid,$start,$end){
    	$startym = date('Ym',$start);
    	$endym = date('Ym',$end);
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	if($endym - $startym !=0){
    		$tablestart =  $this->getGoldTableName($uid,$start);
    		$sqlstart = "SELECT sum(cost) as gold FROM $tablestart where uid=:uid and create_time>=:start";
    		$startgold = $rdb->fetchOne($sqlstart,array('uid' => $uid,'start' => $start));
    		$endgold = 0;
    		if(date('Ym',time()) == $endym){
    			$tableend = $this->getGoldTableName($uid,$end);
    			$sqlend = "SELECT sum(cost) as gold FROM $tableend where uid=:uid and create_time<=:end";
    			$endgold = $rdb->fetchOne($sqlend,array('uid' => $uid,'end' => $end));
    		}
    		$totalgold = $startgold+$endgold;
    	}else{
    		$tbname = $this->getGoldTableName($uid,$start);
    		$sql = "SELECT sum(cost) as gold FROM $tbname where uid=:uid and create_time>=:start and create_time<=:end";
            $totalgold = $rdb->fetchOne($sql,array('uid' => $uid,'start' => $start,'end' => $end));
    	}
        return $totalgold;
    }
    public function insert($uid, $info)
    {
        $tbname = $this->getExchangeTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->insert($tbname, $info);
    }
    function getConsume(){
        $sql = 'select * from island_consume_exchange order by gold';
        $db = $this->geteventDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }
    function delete($uid){
        $tbname = $this->getExchangeTableName($uid);
        $sql = "DELETE from $tbname where uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
    public function updateConsume($window,$info){
        $tbname = 'island_consume_exchange';
        $db = $this->geteventDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('window = ?', $window);
        $wdb->update($tbname, $info, $where);
    }
}