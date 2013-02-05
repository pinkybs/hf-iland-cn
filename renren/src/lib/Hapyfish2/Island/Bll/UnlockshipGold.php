<?php

class Hapyfish2_Island_Bll_UnlockshipGold
{
	public static  function addunlockship($tid, $month)
	{
		if(!$tid || !in_array($tid, array(4, 5, 6, 3))) {
			return 'Not has tid';
		}

		if($month != 5 && $month != 6) {
			return 'Not has month';
		}

		$month = '0' . $month;

		$year = 2011;
		$yearmonth = $year . $month;

		$dalLog = Hapyfish2_Island_Dal_ConsumeLog::getDefaultInstance();
		$tnID = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		foreach ($tnID as $key=>$id) {
			$hasLog[$id] = $dalLog->getFXUnlockshipGold($tid, $id, $yearmonth);

			foreach ($hasLog[$id] as $log) {
				$boatID = substr($log['cid'], 2, 1);
				$addGold = 0;

				if($boatID == 1) {
					$addGold = $log['cost'] - 1;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 1, $log, $yearmonth);
					}
				} else if($boatID == 2) {
					$addGold = $log['cost'] - 2;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 2, $log, $yearmonth);
					}
				} else if($boatID == 3) {
					$addGold = $log['cost'] - 8;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 8, $log, $yearmonth);
					}
				} else if($boatID == 4) {
					$addGold = $log['cost'] - 15;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 15, $log, $yearmonth);
					}
				} else if($boatID == 5) {
					$addGold = $log['cost'] - 24;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 24, $log, $yearmonth);
					}
				} else if($boatID == 6) {
					$addGold = $log['cost'] - 36;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 36, $log, $yearmonth);
					}
				} else if($boatID == 7) {
					$addGold = $log['cost'] - 53;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 53, $log, $yearmonth);
					}
				} else if($boatID == 8) {
					$addGold = $log['cost'] - 75;
					if($addGold > 0) {
						$dalLog->updateFXUnlockshipGold($tid, $id, 75, $log, $yearmonth);
					}
				}

				if($addGold > 0) {
					$toSendGold = array('gold' => $addGold, 'type' => 0, 'time' => time());

					try {
						$ok = Hapyfish2_Island_Bll_Gold::add($log['uid'], $toSendGold);
						info_log('uid:'.$log['uid'].' gold:'.$addGold, 'unlockShipToSendGold');
					}
					catch (Exception $e) {
						info_log($log['uid'] . ' ' . $e, 'unlockShipError');
					}

					if($ok) {
						$title = '系统给您<font color="#FF0000">'.$addGold.'</font>宝石,修复了船只升级价格问题,我们根据您的额外支出给予补偿';

			        	$minifeed = array('uid' => $log['uid'],
			                              'template_id' => 0,
			                              'actor' => $log['uid'],
			                              'target' => $log['uid'],
			                              'title' => array('title' => $title),
			                              'type' => 3,
			                              'create_time' => time());

						Hapyfish2_Island_Bll_Feed::insertMiniFeed($minifeed);
					}
				}
			}
		}

		return 'OK';
	}

}

