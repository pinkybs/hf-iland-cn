<?php
class Hapyfish2_Island_Dal_FishCompound
{
	protected static $_instance;
	
	
	public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getBasicDb()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }
    
    public function getTb($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_fish_compound_' . $id;
    }
    
    public function getSTB($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_skill_' . $id;
    }
    
    public function getPrTb($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'island_user_prestige_' . $id;
    }
    
    public function getBasicInfo()
    {
    	$sql = "SELECT id, prefix, fid, `name`, class_name, next_id, fish_type, `level`, speed, `condition`, `unlock`, rate, `type`, skill,`weight`,`content` from island_fish_compound";
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    public  function getUserFishCompound($uid)
    {
    	
    	$tbname = $this->getTb($uid);
    	$sql = "select id, uid, cid, skill, status, gameNum, winNum from $tbname where uid=:uid";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql, array('uid' => $uid));
    }
    
    public function getSkill()
    {
    	$sql = "select id,name,className,level,type,value,continue_time,description,skillclassName from island_fish_skill";
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    public function getTrack()
    {
    	$sql = "select id, `obstacle`, `schedule`, `type`, sea, npc, `limit`,recommendLevel,award,description,content,btncontent from island_fish_track";
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    public function getObstacle()
    {
    	$sql = "select id, `name`, className, `value`, `type`, continue_time from island_fish_obstacle";
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    public function getUserSkill($uid)
    {
    	$tbname = $this->getSTb($uid);
    	$sql = "select cid, uid, `count` from $tbname where uid=:uid";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql, array('uid' => $uid));
    }
    
    public function getUnlock($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$sql = "select id from island_user_checkpoint_unlock where uid=:uid" ;
    	return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function updateUserFish($uid,$info)
    {
    	$tbname = $this->getTb($uid);
    	$sql = "INSERT INTO $tbname (id, uid, cid, skill, status, gameNum, winNum ) VALUES (:id, :uid,:cid,:skill, :status, :gameNum, :winNum) ON DUPLICATE KEY UPDATE cid=:cid,skill=:skill,status=:status,gameNum=:gameNum,winNum=:winNum";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('id'=>$info['id'],'uid'=>$info['uid'],'cid'=>$info['cid'],'skill'=>$info['skill'],'status'=>$info['status'],'gameNum'=>$info['gameNum'],'winNum'=>$info['winNum']));
    }
    
    public function remove($uid,$id)
    {
    	$tbname = $this->getTb($uid);
    	$sql = "delete from $tbname where uid=:uid and id=:id";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('uid'=>$uid,'id'=>$id));
    }
    
    public function updateUserSkill($uid,$info)
    {
    	$tbname = $this->getSTB($uid);
    	$sql = "INSERT INTO $tbname (uid, cid, `count`) VALUES (:uid, :cid, :num) ON DUPLICATE KEY UPDATE `count`=:num";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('uid'=>$info['uid'],'cid'=>$info['cid'],'num'=>$info['count']));
    }
    
    public function getGuide()
    {
    	$sql = "select id,processArray from island_fish_match_guide";
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    public function getUserGuide($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$sql = "select step from island_user_fish_match_guide where uid=:uid" ;
    	return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function updateUserGuide($uid,$step)
    {
    	$sql = "INSERT INTO island_user_fish_match_guide (uid, step) VALUES (:uid, :step) ON DUPLICATE KEY UPDATE `step`=:step";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('uid'=>$uid,'step'=>$step));
    }
    
    public function getAward()
    {
    	$sql = "select id,award1,award2,award3,award4,award5 from island_fih_pve_award";
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    public function insertAward($id,$a1,$a2,$a3,$a4,$a5)
    {
    	$sql = "insert into island_fih_pve_award(id,award1,award2,award3,award4,award5) VALUES(:id,:a1,:a2,:a3,:a4,:a5)";
    	$db = $this->getBasicDb();
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('id'=>$id,'a1'=>$a1,'a2'=>$a2,'a3'=>$a3,'a4'=>$a4,'a5'=>$a5));
    }
    public function updateUnlock($uid,$id)
    {
    	
    	$sql = "insert into island_user_checkpoint_unlock(uid,id) VALUES(:uid,:id) ON DUPLICATE KEY UPDATE `id`=:id";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	return $wdb->query($sql, array('uid'=>$uid,'id'=>$id));
    }
    
    public function updateUserPrestige($uid, $num)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$sql = "INSERT INTO island_user_prestige (uid, num) VALUES (:uid, :num) ON DUPLICATE KEY UPDATE num=:num";
    	return $wdb->query($sql, array('uid'=>$uid,'num'=>$num));
    }
    
    public function getUserPrestige($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$sql = "select  `num` from  island_user_prestige  where uid={$uid}";
    	return  $rdb->fetchOne($sql);
    }
    
    public function getUserRank($uid)
    {
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	$sql = "select uid, `rank`, `winningStreak`, `lifting` from island_user_arena where uid={$uid}";
    	return  $rdb->fetchRow($sql);
    }
    
    public function getPrestigeExchange()
    {
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	$sql = "select id, `type`, cid, prestige, `num`,vip from  island_prestige_exchange";
    	return $rdb->fetchAssoc($sql);
    }
    
    public function getMaxRank()
    {
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	$sql = "select max(rank) from island_user_arena ";
    	return $rdb->fetchOne($sql);
    }
    
    public function updateUserRank($uid,$data)
    {
    	$db = $this->getBasicDb();
    	$wdb = $db['w'];
    	$sql = "INSERT INTO island_user_arena (uid, `rank`, `winningStreak`, `lifting`) VALUES ({$uid},{$data['rank']},{$data['winningStreak']},{$data['lifting']}) ON DUPLICATE KEY UPDATE winningStreak={$data['winningStreak']}, `rank`={$data['rank']},lifting={$data['lifting']}";
    	return $wdb->query($sql);
    }
    
    public function getLimitRank($n)
    {
    	$start = 2000*($n-1);
    	$db = $this->getBasicDb();
    	$rdb = $db['r'];
    	$sql = "select uid from island_user_arena order by `rank` limit {$start}, 2000";
    	return $rdb->fetchCol($sql);
    }
}