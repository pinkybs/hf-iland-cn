<?php

class Hapyfish2_Island_Dal_Fight
{
    protected static $_instance;
    
    private $_prefix = '';
    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Mercenary
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
    
    public function getFightMainTable()
    {
    	return 'island_' . $this->_prefix . '_day_fight';
    }
    
    public function getMonterTable()
    {
    	return 'island_' . $this->_prefix . '_day_monster';
    }
    public function getMaterTable()
    {
    	return 'island_' . $this->_prefix . '_day_mater';
    }
    
	public function getMutualTable()
    {
    	return 'island_' . $this->_prefix . '_day_mutual';
    }
    
    public function getRepairTable()
    {
    	return 'island_' . $this->_prefix . '_day_repair';
    }
    
    public function insertFightMain($info)
    {
		$tbname = $this->getFightMainTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info);
    }
    
	public function getRange($begin, $end)
    {
    	$tbname = $this->getFightMainTable();
    	$sql = "SELECT * FROM $tbname WHERE `date`>=:begin AND `date`<=:end ORDER BY map,`date` DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function insertMonter($data)
    {
    	$tbname = $this->getMonterTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $data);
    }
    
    public function getMonter($map, $type, $date)
    {
    	$tbname = $this->getMonterTable();
    	$db = Hapyfish2_Db_Factory::getDB();
    	$sql = "SELECT * FROM $tbname WHERE `date`=:date and map=:map and type=:type ORDER BY totalNum DESC, pNum DESC";
    	$rdb = $db['r']; 
    	return $rdb->fetchAll($sql, array('date' => $date, 'map' => $map, 'type'=>$type));
    }
    
    public function insertMater($data)
    {
    	$tbname = $this->getMaterTable();
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
    	return $wdb->insert($tbname, $data);
    }
    
    public function getMater($date, $map, $type)
    {
    	
    	$tbname = $this->getMaterTable();
    	$db = Hapyfish2_Db_Factory::getDB();
    	$sql = "SELECT * FROM $tbname WHERE `date`=:date and `map`=:map and `type`=:type ORDER BY totalNum DESC, pNum DESC";
    	$rdb = $db['r']; 
    	return $rdb->fetchAll($sql, array('date' => $date, 'map' => $map, 'type'=>$type));
    }
    
    public function insertMutualMain($data)
    {
    	$tbname = $this->getMutualTable();

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $data);
    }
    
    public function getMutual($start,$end)
    {
    	$tbname = $this->getMutualTable();
    	$sql = "SELECT * FROM $tbname WHERE `date`>=:begin AND `date`<=:end ORDER BY `date` DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $start, 'end' => $end));
    }
    
    public function insertRepairMain($data)
    {
    	$tbname = $this->getRepairTable();
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
    	return $wdb->insert($tbname, $data);
    }
    
    public function getRepair($start,$end)
    {
    	$tbname = $this->getRepairTable();
    	$db = Hapyfish2_Db_Factory::getDB();
    	$sql = "SELECT * FROM $tbname WHERE `date`>=:begin AND `date`<=:end ORDER BY `date` DESC";
    	$rdb = $db['r']; 
    	return $rdb->fetchAll($sql, array('begin' => $start, 'end' => $end));
    	
    }

}