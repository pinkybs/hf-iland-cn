<?php

class Hapyfish2_Island_Stat_Bll_Payment
{
	public static function cal($day)
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
		
        //$dalPayFlow = Hapyfish2_Island_Dal_PayFlow::getDefaultInstance();
		try {
			$dalPay = Hapyfish2_Island_Stat_Dal_PaymentLog::getDefaultInstance();
			for ($i = 0; $i < 24; $i++) {
				for ($j = 0; $j < 10; $j++) {
					//$data = $dalPay->getPaymentLogData($i, $j, $begin, $end);
					$data = $dalPay->getPayOrderFlowOfSuccess($i, $j, $yearmonth, $begin, $end);
					$allData[] = $data;
					if ($data) {
						foreach ($data as $row) {
							if ( $row['platform'] == 1 || $row['platform'] == 2 || $row['platform'] == 0 ) {
								if ($row['result'] != 0) {
									$loseAmount += $row['amount'];
								}
								else {
									$amount += $row['amount'];
								}
								
								//$gold += $row['gold'];
								$count++;
								if ( !isset($uidTemp[$row['uid']]) ) {
									$userCount++;
									$uidTemp[$row['uid']] = 1;
								}
							}
						}
					}
				}
			}
			
			return array('amount' => $amount, 'loseAmount' => $loseAmount, 'gold' => $gold, 'count' => $count, 'userCount' => $userCount, 'status' => 1, 'day' => $day, 'allData' => $allData);
		} catch (Exception $e) {
			return array('amount' => $amount, 'loseAmount' => $loseAmount, 'gold' => $gold, 'count' => $count, 'userCount' => $userCount, 'status' => 2);
		}
	}

}