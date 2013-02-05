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

    public function getGoldTableName($uid,$time){
        $yearmonth = date('Ym',$time);
        $id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_goldlog_' . $yearmonth . '_' . $id;
    }
    public function geteventDB(){
        $key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
    }
    public function getConsumeStep($uid,$start,$end){
        $sql = "SELECT step FROM island_user_event_consume_exchange WHERE uid=:uid and `create_time`>=:start and `create_time`<=:end";
        $db = $this->geteventDB();
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
        $tbname = 'island_user_event_consume_exchange';
        $db = $this->geteventDB();
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
        $sql = "DELETE from island_user_event_consume_exchange where uid=:uid";
        $db = $this->geteventDB();
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
    public function updateConsume($window,$info){
        $tbname = 'island_consume_exchange';
        $cid = $info['cid'];
        $things = $info['things'];
        $gold = $info['gold'];
        $start = $info['start'];
        $end = $info['end'];
        $db = $this->geteventDB();
        $wdb = $db['w'];
    	$sql = "INSERT INTO $tbname VALUES('$window', '$cid', '$things', '$gold', '$start', '$end' ) ON DUPLICATE KEY UPDATE window='$window',cid='$cid', things='$things',gold='$gold',start='$start',end='$end' ";
    	return $wdb->query($sql);
    }
}