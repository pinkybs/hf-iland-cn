<?php


class Hapyfish2_Island_Stat_Dal_Shop
{
    protected static $_instance;
    
    private $_tbplant = 'day_shop_plant';
    

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Stat_Dal_Shop
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function insertPlant($info)
    {
        $tbname = $this->_tbplant;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }
    
    public function insertPlantTemp($info)
    {
        $tbname = 'day_shop_plant_temp';

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }
    
    public function getPlant()
    {
        $tbname = $this->_tbplant;

    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
    	$sql = "SELECT * FROM day_shop_plant ";

        return $rdb->fetchAll($sql);
    }
    
}