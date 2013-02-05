<?php


class Hapyfish2_Island_Stat_Dal_MainMonth
{
    protected static $_instance;
    
    private $_tb_main_month = 'month_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Stat_Dal_MainMonth
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
        $tbname = $this->_tb_main_month;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

        return $wdb->insert($tbname, $info);
    }
    
    public function getMainMonth($month)
    {
        $tbname = $this->_tb_main_month;
        $sql = "SELECT log_time,active_user FROM $tbname WHERE log_time=:log_time";
        
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('log_time' => $month));
    }
    
    
    
}