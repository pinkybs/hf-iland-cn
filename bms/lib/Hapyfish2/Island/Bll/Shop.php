<?php

class Hapyfish2_Island_Bll_Shop
{
	public static function addShopMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addShopMain', 'Hapyfish2_Island_Bll_Shop.add');
			return;
		}
		
		$newInfo = array('log_time' 		=> $data['log_time'],
						 'all_count' 		=> $data['all_count'],
						 'buy_data' 		=> $data['buy_data']);
		try {
			$dal = Hapyfish2_Island_Dal_Shop::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insertShopMain($newInfo); 
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.err');
		}
	}

	public static function getRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Shop::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getRange($begin, $end);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
	public static function getShop($platform, $day, $sort = 'count', $order = 'ASC')
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_Shop::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
            if ($d) {
                $data = json_decode($d['buy_data'], true);
                
                $sortAry = self::array_sort($data, $sort, $order);
                $info = array();
                $g = 1;
                foreach ($sortAry as $i => $j) {
                    $info[] = array('level' => (string)$g, 
                    				'cid' => (string)$i, 
                    				'count' => $j['count'], 
                    				'name' => $j['name'], 
                    				'needCoin' => $j['needCoin'], 
                    				'needGem' => $j['needGem']);
                    $g++;
                }
            }
		} catch (Exception $e) {
		}

		return $info;
	}
	
	public static function array_sort($array, $on, $order = 'ASC')
	{
	    $new_array = array();
	    $sortable_array = array();
	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }
	        switch ($order) {
	            case 'ASC':
	                asort($sortable_array);
	            break;
	            case 'DESC':
	                arsort($sortable_array);
	            break;
	        }
	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }
	    return $new_array;
	}
}