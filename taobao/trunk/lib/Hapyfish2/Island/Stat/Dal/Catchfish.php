<?php


class Hapyfish2_Island_Stat_Dal_Catchfish
{
    protected static $_instance;
    
    private $_tb = 'stat_catchfish';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Stat_Dal_Openisland
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
    public function update($day,$count)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET count=:count WHERE create_time=:date AND count=0';
    	return $wdb->query($sql, array('count'=>$count, 'date'=>$day));  
    }
    public function updateUserNum($day,$usernums)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET usernums=:usernums WHERE create_time=:date AND usernums=0';
    	return $wdb->query($sql, array('usernums'=>$usernums, 'date'=>$day));  
    }  
    public function updateCoin($day,$coin)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET coin=:coin WHERE create_time=:date AND coin=0';
    	return $wdb->query($sql, array('coin'=>$coin, 'date'=>$day));  
    }   
    public function updateIsland($day, $open_island1, $open_island2, $open_island3, $open_island4, $open_island5, $open_island6, $open_island7, $open_island8, $open_island9, $open_island10, $open_island11, $open_island12, $open_island13, $open_island14)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET open_island1=:open_island1,open_island2=:open_island2,open_island3=:open_island3,open_island4=:open_island4,open_island5=:open_island5,open_island6=:open_island6,open_island7=:open_island7,open_island8=:open_island8,open_island9=:open_island9,open_island10=:open_island10,open_island11=:open_island11,open_island12=:open_island12,open_island13=:open_island13,open_island14=:open_island14 WHERE create_time=:date';
    	return $wdb->query($sql, array('open_island1'=>$open_island1, 'open_island2'=>$open_island2, 'open_island3'=>$open_island3, 'open_island4'=>$open_island4, 'open_island5'=>$open_island5, 'open_island6'=>$open_island6, 'open_island7'=>$open_island7, 'open_island8'=>$open_island8, 'open_island9'=>$open_island9, 'open_island10'=>$open_island10, 'open_island11'=>$open_island11, 'open_island12'=>$open_island12, 'open_island13'=>$open_island13, 'open_island14'=>$open_island14, 'date'=>$day));  
    } 
    public function updateCannon($day, $cannon1, $cannon2)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET cannon1=:cannon1,cannon2=:cannon2 WHERE create_time=:date';
    	return $wdb->query($sql, array('cannon1'=>$cannon1, 'cannon2'=>$cannon2, 'date'=>$day));  
    }  
    public function updateCard($day, $card)
    {
        $tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET card=:card WHERE create_time=:date';
    	return $wdb->query($sql, array('card'=>$card, 'date'=>$day));  
    }

    public function incBrushCard($day, $num)
    {
		$tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET brush_card=:brush_card WHERE create_time=:date';
    	$wdb->query($sql, array('brush_card' => $num, 'date' => $day));  
    } 
	
    public function incBrushNum($day, $num)
    {
		$tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET brush_num=:brush_num WHERE create_time=:date';
    	$wdb->query($sql, array('brush_num' => $num, 'date' => $day));  
    }
    
    public function updateBrushIsland($day, $brush_island1, $brush_island2, $brush_island3, $brush_island4, $brush_island5, $brush_island6, $brush_island7, $brush_island8, $brush_island9, $brush_island10, $brush_island11, $brush_island12, $brush_island13, $brush_island14, $brush_island15)
    {
		$tbname = $this->_tb;

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
		$sql = 'UPDATE '.$tbname.' SET brush_island1=:brush_island1,brush_island2=:brush_island2,brush_island3=:brush_island3,brush_island4=:brush_island4,brush_island5=:brush_island5,brush_island6=:brush_island6,brush_island7=:brush_island7,brush_island8=:brush_island8,brush_island9=:brush_island9,brush_island10=:brush_island10,brush_island11=:brush_island11,brush_island12=:brush_island12,brush_island13=:brush_island13,brush_island14=:brush_island14,brush_island15=:brush_island15 WHERE create_time=:date';
    	$wdb->query($sql, array('brush_island1'=>$brush_island1, 'brush_island2'=>$brush_island2, 'brush_island3'=>$brush_island3, 'brush_island4'=>$brush_island4, 'brush_island5'=>$brush_island5, 'brush_island6'=>$brush_island6, 'brush_island7'=>$brush_island7, 'brush_island8'=>$brush_island8, 'brush_island9'=>$brush_island9, 'brush_island10'=>$brush_island10, 'brush_island11'=>$brush_island11, 'brush_island12'=>$brush_island12, 'brush_island13'=>$brush_island13, 'brush_island14'=>$open_island14, 'brush_island15'=>$open_island15, 'date'=>$day));
    }
    
    public function insertStatCompoundFish($dt,$num,$com,$levelUp,$card)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_compound_fish (`date`,total_num,`com`,levelUp,card) VALUES ($dt,$num,$com,$levelUp,$card)";
    	return $wdb->query($sql);  
    }
    
    public function insertStatCompoundFishDetail($dt,$fid,$total,$s)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_compound_fish_detail (`date`,fid,total_num,s_num) VALUES ($dt,$fid,$total,$s)";
    	return $wdb->query($sql); 
    }
    
    public function insertStatFishStep($dt,$step,$total,$s,$type,$prefix1,$prefix2,$prefix3)
    {
   		$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_fish_step (`date`,step,total,s_num,`type`,prefix1,prefix2,prefix3) VALUES ($dt,$step,$total,$s,$type,$prefix1,$prefix2,$prefix3)";
    	return $wdb->query($sql); 
    }
    
    public function insertStatFishLevelUp($dt,$fid,$total,$s,$level2=0,$level3=0,$level4=0,$level5=0,$level6=0,$level7=0,$level8=0,$level9=0,$level10=0)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_fish_levelUp (`date`,`fid`,`total_num`,`s_num`,`level2`,`level3`,`level4`,`level5`,`level6`,`level7`,`level8`,`level9`,`level10`) VALUES ($dt,$fid,$total,$s,$level2,$level3,$level4,$level5,$level6,$level7,$level8,$level9,$level10)";
        return $wdb->query($sql); 
    }
    
    public function insertComCard($dt,$step,$num)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_fish_Comcard (`date`,`step`,`num`) VALUES ($dt,$step,$num)";
        return $wdb->query($sql); 
    }
    
    public function insertStatFishPve($dt,$tid,$num,$win)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_fish_pve (`date`,`tid`,`num`,`win`) VALUES ($dt,$tid,$num,$win)";
        return $wdb->query($sql); 
    }
    
    public function insertStatFishSea($dt,$sea,$num)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_fish_sea (`date`,`sea`,`num`) VALUES ($dt,$sea,$num)";
        return $wdb->query($sql); 
    }
    
    public function getCompoundFish($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,total_num,`com`,levelUp,card from stat_compound_fish where `date`={$dt}";
    	return $rdb->fetchAll($sql);
    }
    
	public function getFishSea($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,`sea`,`num` from stat_fish_sea where `date`={$dt}";
    	return $rdb->fetchAll($sql);
    }
    
    public function getFishPve($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,`tid`,`num`,`win` from stat_fish_pve where `date`={$dt} order by tid";
    	return $rdb->fetchAll($sql);
    }
    
    public function getComCard($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,`step`,`num` from stat_fish_Comcard where `date`={$dt} order by `step`";
    	return $rdb->fetchAll($sql);
    }
    
    public function getFishLevelUp($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,`fid`,`total_num`,`s_num`,`level2`,`level3`,`level4`,`level5`,`level6`,`level7`,`level8`,`level9`,`level10` from stat_fish_levelUp where `date`={$dt} order by fid";
    	return $rdb->fetchAll($sql);
    }
    
    public function getFishStep($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,step,total,s_num,`type`,prefix1,prefix2,prefix3 from stat_fish_step where `date`={$dt} order by step";
    	return $rdb->fetchAll($sql);
    }
    
    public function getCompoundFishDetail($dt)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,fid,total_num,s_num from stat_compound_fish_detail where `date`={$dt} order by fid";
    	return $rdb->fetchAll($sql);
    }
    
    public function updateCompoundFish($dt,$num)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "update stat_compound_fish set total_num={$num} where `date`={$dt} ";
    	return $wdb->query($sql);  
    }
    
    public function insertSkill($dt,$id,$get,$use)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        $sql = "insert into stat_fish_skill (`date`,`sid`,`get`,`use`) VALUES ($dt,$id,$get,$use)";
        return $wdb->query($sql); 
    }
    
    public function getSkill($date)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,sid,`get`,`use` from stat_fish_skill where `date`={$date} order by sid";
    	return $rdb->fetchAll($sql);
    }
    public function insetPvp($date,$num,$num1,$numarr)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$wdb = $db['w'];
    	$sql = "insert into stat_fish_pvp (`date`,`num`,`num1`,`pnum`, `pnum1`,`snums`,`snumu`,`znums`,`znumu`,`prefix2`,`prefix3`) VALUES ($date,$num,$num1,$numarr[1],$numarr[2],0,0,0,0,0,0)";
        return $wdb->query($sql); 
    }
    
    public function insertSexchange($date,$k,$v)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$wdb = $db['w'];
    	$sql = "insert into stat_fish_sexchange (`date`,`id`,`num`) VALUES ($date,$k,$v)";
        return $wdb->query($sql); 
    }
    
    public function updatePvp($date,$data)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$wdb = $db['w'];
    	$tbname = 'stat_fish_pvp';
    	$where = $wdb->quoteinto('`date` = ?', $date);
        return $wdb->update($tbname, $data, $where); 
    }
    
    public function getZizhi($date)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,id,`num` from stat_fish_sexchange where `date`={$date} order by id";
    	return $rdb->fetchAll($sql);
    }
    
    public function getpvp($date)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `date`,`num`,`num1`,`pnum`, `pnum1`,`snums`,`snumu`,`znums`,`znumu`,`prefix2`,`prefix3` from stat_fish_pvp where `date`={$date}";
    	return $rdb->fetchRow($sql);
    }
}