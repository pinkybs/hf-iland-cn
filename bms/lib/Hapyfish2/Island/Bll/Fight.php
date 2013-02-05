<?php

class Hapyfish2_Island_Bll_Fight
{
	public static function addFightMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addFightMain', 'Hapyfish2_Island_Bll_Fight');
			return;
		}
		$dal = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
		$dal->setDbPrefix($platform);
		foreach($data['main'] as $k => $info){
			try {
				$dal->insertFightMain($info); 
			} catch (Exception $e) {
				info_log($e->getMessage(), 'bot.err');
			}
		}
		
		foreach($data['monter'] as $k=>$v){
			$dal->insertMonter($v);
		}
		foreach($data['mater'] as $k=>$v){
			$dal->insertMater($v);
		}
	}
	
	public static function crawlFightMain($bot, $day, $platform)
	{
		try {
			$result = $bot->stat_fight($day);
			$data = $result['data'];
			self::addFightMain($platform, $data);
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.crawl.statmain');
		}
	}
	
	public static function getRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getRange($begin, $end);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getMonter($platform, $map, $type, $date)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getMonter($map, $type, $date);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getMater($platform, $map, $type, $date)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getMater($date, $map, $type);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getFightLevel($platform, $day , $map)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getRange($day,$day);
			foreach($d as $k=>$v){
				if($v['map'] == $map){
					$info = json_decode($v['userlevel'], true);
				}
			}
            if ($info) {
                $sortAry = array();
                foreach ($info['list'] as $k => $v) {
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
	
	public static function getOperateLevel($platform, $day, $map)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getRange($day,$day);
			foreach($d as $k=>$v){
				if($v['map'] == $map){
					$info = json_decode($v['level'], true);
				}
			}
            if ($info) {
                $sortAry = array();
                foreach ($info['list'] as $k => $v) {
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
	
	public static function getFightAll($platform, $day, $type)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getRange($day,$day);
			foreach($d as $k=>$v){
				$info['map'] = $v['map'];
				if($type == 1){
					$list = json_decode($v['userlevel'],true);
				}else{
					$list = json_decode($v['level'],true);
				}
				
				$sortAry = array();
                foreach ($list['list'] as $k1 => $v1) {
                    $sortAry[$k1] = $v1;
                }
                ksort($list['list']);
                foreach($list['list'] as $k1=>$v1){
                	 $detail[] = array('level' => (string)$k1, 'count' => $v1);
                }
                $info['list'] =$detail;
				$data[] = $info;
			}
		} catch (Exception $e) {
		}

		return $data;
	}
	
	public static function getMapList($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getRange($day,$day);
			foreach($d as $k=>$v){
				$data[] = $v['map'];
			}
		} catch (Exception $e) {
		}

		return $data;
	}
	
	public static function  addMutualMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addFightMain', 'Hapyfish2_Island_Bll_Fight');
			return;
		}
		$dal = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
		$dal->setDbPrefix($platform);
		$dal->insertMutualMain($data);
	}
	
	public static function getMutualDetail($platform, $start, $end)
	{
		$data = null;
		try{
			$dalLevel = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getMutual($start,$end);
			if($d){
				foreach($d as $info){
					$list['date'] = $info['date'];
					$list['help'] = json_decode($info['help'],true);
					$list['resist'] = json_decode($info['resist'],true);
					$list['seize'] = json_decode($info['seize'],true);
					$list['gift'] = json_decode($info['gift'],true);
					$data[] = $list;
				}
			}
		} catch (Exception $e) {
		}

		return $data;
	}
	
	public static function addRepairMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addFightMain', 'Hapyfish2_Island_Bll_Fight');
			return;
		}
		$dal = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
		$dal->setDbPrefix($platform);
		$dal->insertRepairMain($data);
	}
	
	public static function getRepairDetail($platform, $start, $end)
	{
		$data = null;
		try{
			$dalLevel = Hapyfish2_Island_Dal_Fight::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getRepair($start,$end);
			if($d){
				foreach($d as $info){
					$list['date'] = $info['date'];
					$list['total'] = $info['total'];
					$list['num'] = $info['num'];
					$list['cost'] = $info['cost'];
					$data[] = $list;
				}
			}
		} catch (Exception $e) {
		}

		return $data;
	}
	
}