<?php

class Hapyfish2_Island_Bll_Day
{
	public static function getMain($platform, $day)
	{
		$data = null;
		try {
			$dalMain = Hapyfish2_Island_Dal_Main::getDefaultInstance();
			$dalMain->setDbPrefix($platform);
			$data = $dalMain->getDay($day);
		} catch (Exception $e) {

		}

		return $data;
	}

	public static function getMainRange($platform, $begin, $end, $desc = true)
	{
		$data = null;
		$sort = $desc ? 'DESC' : 'ASC';
		try {
			$dalMain = Hapyfish2_Island_Dal_Main::getDefaultInstance();
			$dalMain->setDbPrefix($platform);
			$data = $dalMain->getRange($begin, $end, $sort);
		} catch (Exception $e) {

		}

		return $data;
	}

	public static function getRetention($platform, $day)
	{
		$data = null;
		try {
			$dalRetention = Hapyfish2_Island_Dal_Retention::getDefaultInstance();
			$dalRetention->setDbPrefix($platform);
			$data = $dalRetention->getRetention($day);
		} catch (Exception $e) {

		}

		return $data;
	}

	public static function getRetentionRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dalRetention = Hapyfish2_Island_Dal_Retention::getDefaultInstance();
			$dalRetention->setDbPrefix($platform);
			$data = $dalRetention->getRangeRetention($begin, $end);
		} catch (Exception $e) {

		}

		return $data;
	}

	public static function getActiveUserLevel($platform, $day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Island_Dal_ActiveUserLevel::getDefaultInstance();
			$dalLevel->setDbPrefix($platform);
			$d = $dalLevel->getDay($day);
			if ($d) {
				$level = json_decode($d['level']);
				$data = array();
				foreach ($level as $k => $v) {
					$data[] = array('level' => $k, 'count' => $v);
				}
			}
		} catch (Exception $e) {
		}

		return $data;
	}

    public static function getAllUserLevel($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_ActiveUserLevel::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getDayAllUserLevel($day);
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

    public static function getLevelup($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_ActiveUserLevel::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getDayLevelup($day);
            if ($d) {
                $level = json_decode($d['levelup']);
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


    public static function getDonateSpread($platform, $day)
    {
        $data = null;
        try {
            $dal = Hapyfish2_Island_Dal_Donate::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $d = $dal->getDayDonate($day);
            if ($d) {
                $spread = json_decode($d['amount_spread'], true);
                $sortAry = array();
                foreach ($spread as $k => $v) {
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

    public static function getDonateAll($platform, $dayFrom, $dayTo)
    {
        $data = null;
        try {
            $dal = Hapyfish2_Island_Dal_Donate::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $list = $dal->listDayDonate($dayFrom, $dayTo);

            if ($list) {
                $data = array();
                foreach ($list as $key=>$val) {
                    $spread = json_decode($val['amount_spread'], true);
                    $tot = 0;
                    $sortAry = array();
                    foreach ($spread as $k => $v) {
                    	$sortAry[$k] = (int)$v * (int)$k;
                    	$tot += (int)$sortAry[$k];
                    }
                    $sortAry['0'] = $tot;//0-all and 1 5 10 50 100
                    ksort($sortAry);

                    $day = $val['log_time'];
                    $data[$day] = array('log_time' => $day, 'donate' => $sortAry);
                }
            }
        } catch (Exception $e) {
        }
        return $data;
    }
}