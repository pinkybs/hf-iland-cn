<?php

class Hapyfish2_Island_Stat_Bll_GoldLog
{
	public static function getSumCost($day)
	{
		$begin = strtotime($day);
		$end = $begin + 86400;
		$amount = 0;
		$loseAmount = 0;
		$gold = 0;
		$count = 0;
		$userCount = 0;
		$uidTemp = array();
		$allData = array();
		$yearmonth = date('Ym', $begin);
		$sumCost = 0;
		$isVipCost = 0;
		$noVipCost = 0;
		
		try {
			$dalPay = Hapyfish2_Island_Stat_Dal_GoldLog::getDefaultInstance();
			for ($i = 0; $i < 24; $i++) {
				for ($j = 0; $j < 10; $j++) {
					$log = $dalPay->getGoldLog($i, $j, $yearmonth, $begin, $end);
					foreach ( $log as $data ) {
						if ( $data['is_vip'] == 1 ) {
							$isVipCost += $data['cost'];
						}
						else {
							$noVipCost += $data['cost'];
						}
						$sumCost += $data['cost'];
					}
				}
			}
			
			$isVipCost = round($isVipCost*0.8);
			$allSumCost = $isVipCost + $noVipCost;
			return array('sumCost' => $sumCost, 'isVipCost'=> $isVipCost, 'noVipCost'=>$noVipCost, 'allSumCost'=>$allSumCost);
		} catch (Exception $e) {
			return false;
		}
	}

}