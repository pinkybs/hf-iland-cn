<?php

class Hapyfish2_Island_Stat_Dal_SuperVisitor
{
    protected static $_instance;
    protected static $dbadp;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Stat_Dal_SuperVisitor
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
            $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
            self::$dbadp = $db['w'];
        }
        return self::$_instance;
    }

    public function insertSvInfo($info)
    {
        $tbname = 'super_visitor_demand';
        $wdb = self::$dbadp;

        return $wdb->insert($tbname, $info);
    }

    public function insertCollectionInfo($info)
    {
        $tbname = 'super_visitor_collection';
        $wdb = self::$dbadp;

        return $wdb->insert($tbname, $info);
    }
}