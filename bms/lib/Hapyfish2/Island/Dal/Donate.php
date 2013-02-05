<?php

class Hapyfish2_Island_Dal_Donate
{
    protected static $_instance;

    private $_prefix = '';
    private $_tbname = 'day_donate';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Payment
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function setDbPrefix($prefix)
    {
    	$this->_prefix = $prefix;
    }

    public function getTableName()
    {
        return 'island_' . $this->_prefix . '_' . $this->_tbname;
    }

    public function getDayDonate($day)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";

        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('day' => $day));
    }

    public function listDayDonate($dayFrom, $dayTo)
    {
        $tbname = $this->getTableName();
        $sql = "SELECT * FROM $tbname WHERE log_time>=:dayFrom AND log_time<=:dayTo ORDER BY log_time";

        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql, array('dayFrom' => $dayFrom, 'dayTo' => $dayTo));
    }

    public function insUpd($info)
    {
        $tbname = $this->getTableName();
        $sql = "INSERT INTO $tbname (log_time,amount_spread,amount_ranking) VALUES (:log_time,:amount_spread,:amount_ranking)"
              . " ON DUPLICATE KEY UPDATE amount_spread=:amount_spread,amount_ranking=:amount_ranking ";

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        return $wdb->query($sql, array('log_time'=>$info['log_time'], 'amount_spread'=>$info['amount_spread'], 'amount_ranking'=>$info['amount_ranking']));
    }

}