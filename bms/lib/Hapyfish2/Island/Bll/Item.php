<?php

class Hapyfish2_Island_Bll_Item
{
	public static function addItemMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addItemMain', 'Hapyfish2_Island_Bll_Item.add');
			return;
		}
		
		$newInfo = array('log_time' 		=> $data['log_time'],
						 'all_count' 		=> $data['all_count'],
						 'use_data' 		=> $data['use_data']);
		try {
			$dal = Hapyfish2_Island_Dal_Item::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insertItemMain($newInfo); 
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.err');
		}
	}

	public static function getRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Item::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getRange($begin, $end);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getItemUse($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Item::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
            if ($d) {
                $useData = json_decode($d['use_data'], true);
                $sortAry = array();
                foreach ($useData as $k => $v) {
                    $sortAry[$k] = array('count' => $v['count'], 'name' => $v['name']);
                }
                ksort($sortAry);
                $data = array();
                
                $g = 1;
                foreach ($sortAry as $i => $j) {
                    $data[] = array('level' => (string)$g, 'cid' => (string)$i, 'count' => $j['count'], 'name' => $j['name']);
                    $g++;
                }
            }
		} catch (Exception $e) {
		}

		return $data;
	}
	
}