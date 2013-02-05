<?php

class Hapyfish2_Island_Bll_LossUser
{
	public static function addLossUser($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data : addLossUser', 'Hapyfish2_Island_Bll_LossUser.add');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_LossUser::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insertLossUser($info); 
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.err');
		}
	}

    public static function getLossUserInfoRange($platform, $begin, $end, $desc = true)
    {
        $data = null;
        $sort = $desc ? 'DESC' : 'ASC';
        try {
            $dalMain = Hapyfish2_Island_Dal_LossUser::getDefaultInstance();
            $dalMain->setDbPrefix($platform);
            $data = $dalMain->getRange($begin, $end, $sort);
        } catch (Exception $e) {

        }

        return $data;
    }
	
    //等级分布
    public static function getLossUserInfo($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_LossUser::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getDay($day);
            if ($d) {            
            	$userCount = $d['user_count'];
                $avgWood = $d['avg_wood'];
                $avgStone = $d['avg_stone'];
            	
                $data = array('user_count' => $userCount,
                              'avg_wood' => $avgWood,
                              'avg_stone' => $avgStone);
            }
        } catch (Exception $e) {
        }

        return $data;
    }
    
	//等级分布
    public static function getLossUserLevel($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_LossUser::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getDay($day);
            if ($d) {            
                $level = json_decode($d['level']);
                $sortAry = array();
                foreach ($level as $k => $v) {
                    $sortAry[$k] = $v;
                }
                ksort($sortAry);
                $data = array();
                foreach ($sortAry as $i => $j) {
                    $data[] = array('level' => (string)$i, 'count' => $j);
                }
            }
        } catch (Exception $e) {
        }

        return $data;
    }
	
    //爱心分布
    public static function getLossUserLove($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_LossUser::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getDay($day);
            if ($d) {
                $level = json_decode($d['love']);
                $sortAry = array();
                foreach ($level as $k => $v) {
                    $sortAry[$k] = $v;
                }
                ksort($sortAry);
                $data = array();
                foreach ($sortAry as $i => $j) {
                    $data[] = array('level' => (string)$i, 'count' => $j);
                }
            }
        } catch (Exception $e) {
        }

        return $data;
    }
    
}