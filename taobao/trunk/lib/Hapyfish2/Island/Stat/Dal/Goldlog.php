<?php


class Hapyfish2_Island_Stat_Dal_Goldlog
{
    protected static $_instance;
    
    private $_tb = 'stat_sendgoldlog';

    /**
     * Single Instance
     *
     * 
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function insert($info)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    } 
    public function getSendGoldlog($day)
    {
    	$tbname = $this->_tb;
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$sql = "SELECT * FROM $tbname WHERE create_time=:create_time";
    	$rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('create_time' => $day));
    } 
    public function updateMain($allGoldNum, $day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$tbName = 'day_main';
    	$sql = 'UPDATE '.$tbName.' SET send_gold=:gold WHERE log_time=:time';
    	$wdb = $db['w'];
    	$wdb->query($sql, array('gold'=>$allGoldNum, 'time'=>$day));
    } 
}