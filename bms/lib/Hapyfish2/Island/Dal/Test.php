<?php


class Hapyfish2_Island_Dal_Test
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_Test
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function copy($platform1, $platform2, $t)
    {
    	$tb1 = 'happyfish_bms.island_' . $platform1 . '_day_main';
    	$tb2 = 'test_renren_island.compute_hour_count_' . $platform2;
    	$tb3 = 'test_renren_island.compute_day_count_' . $platform2;
    	
    	$t2 = $t + 86400;
    	$d = date('Ymd', $t);
    	
    	$sql = "INSERT INTO $tb1(log_time,total_count,add_user,active,pay_total_amount,pay_user_count,memo)
SELECT a.*,b.* FROM (SELECT '$d' AS log_time,all_user_count AS total_count 
FROM $tb2 
WHERE create_time>$t AND create_time<$t2 ORDER BY id DESC LIMIT 1) AS a
JOIN
(SELECT add_user_count AS add_user,active_count AS active,gold_count AS pay_total_amount,pay_count AS pay_user_count,IFNULL(memo,'') AS memo
FROM $tb3
WHERE create_time>=$t AND create_time<$t2 ) AS b";

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
        $wdb->query($sql);
    }
    
    public function getoldactivelevel($platform, $t)
    {
    	$tb = 'test_renren_island.compute_level_count_' . $platform;
    	
    	$t2 = $t + 86400;
    	
    	$sql = "SELECT `level`,`count` FROM $tb WHERE create_time>$t AND create_time<$t2";
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $rdb = $db['r'];
        
		return $rdb->fetchAll($sql);
    }
    
    public function insertnewactivelevel($platform, $info)
    {
    	$tb = 'happyfish_bms.island_' . $platform . '_day_active_user_level';
    	
        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
    	return $wdb->insert($tb, $info);
    }
    
    public function copyhour($platform1, $platform2, $t)
    {
    	$tb1 = 'happyfish_bms.island_' . $platform1 . '_day_main_hour';
    	$tb2 = 'test_renren_island.compute_hour_count_' . $platform2;
    	
    	$t2 = $t + 86400;
    	
    	$sql = "INSERT INTO $tb1(log_time,add_user,active_user2)
SELECT DATE_FORMAT(FROM_UNIXTIME(create_time), '%Y%m%d%H') AS log_time,add_user_count AS add_user,active_count AS active_user2 
FROM $tb2
WHERE create_time>$t AND create_time<$t2";

        $db = Hapyfish2_Db_Factory::getDB();
        $wdb = $db['w'];
        
        $wdb->query($sql);
    }
    
}