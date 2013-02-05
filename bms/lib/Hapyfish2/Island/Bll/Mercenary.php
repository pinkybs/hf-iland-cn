<?php

class Hapyfish2_Island_Bll_Mercenary
{
	public static function addMercenaryMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addMercenaryMain', 'Hapyfish2_Island_Bll_Mercenary.add');
			return;
		}
		
		$newInfo = array('log_time' => $data['log_time'],
						 'all_count' => $data['all_count'],
						 'rp_list' => $data['rp_list'],
						 'user_level' => $data['user_level'],
						 'role_level' => $data['role_level'],
						 'need_coin' => $data['need_coin'],
						 'refresh_count' => $data['refresh_count'],
						 'useitem_count' => $data['useitem_count'],
						 'dismiss_count' => $data['dismiss_count'],
						 'strthen_count' => $data['strthen_count'],
						 'strthen_coin' => $data['strthen_coin'],
						 'strthen_gem' => $data['strthen_gem'],
						 'strthen_role_level' => $data['strthen_role_level']);
		try {
			$dal = Hapyfish2_Island_Dal_Mercenary::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insertMercenaryMain($newInfo); 
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.err');
		}
	}

	public static function getRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Mercenary::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getRange($begin, $end);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getRp($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Mercenary::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
            if ($d) {
                $level = json_decode($d['rp_list']);
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
	
	public static function getUserLevel($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Mercenary::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
            if ($d) {
                $level = json_decode($d['user_level']);
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
	
	public static function getRoleLevel($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Mercenary::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
            if ($d) {
                $level = json_decode($d['role_level']);
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
	
	public static function getStrthenRoleLevel($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Mercenary::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
            if ($d) {
                $level = json_decode($d['strthen_role_level']);
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