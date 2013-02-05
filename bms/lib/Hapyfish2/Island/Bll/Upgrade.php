<?php

class Hapyfish2_Island_Bll_Upgrade
{
	public static function addUpgradeMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addFightMain', 'Hapyfish2_Island_Bll_Fight');
			return;
		}
		$dal = Hapyfish2_Island_Dal_Upgrade::getDefaultInstance();
		$dal->setDbPrefix($platform);
		$dal->insertUpgrade($data);
	}
	
	public static function getUpgradeDetail($platform,$start,$end)
	{
		$dal = Hapyfish2_Island_Dal_Upgrade::getDefaultInstance();
		$dal->setDbPrefix($platform);
		$data = $dal->getUpgrade($start,$end);
		foreach($data as $k => &$v){
			$v['home'] = json_decode($v['home'], true);
			$v['tavern1'] = json_decode($v['tavern1'], true);
			$v['tavern2'] = json_decode($v['tavern2'], true);
			$v['tavern3'] = json_decode($v['tavern3'], true);
			$v['smithy'] = json_decode($v['smithy'], true);
		}
		return $data;
	}
	
	public static function getUpgradetLevel($platform, $date, $type)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Upgrade::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$list = $dal->getUpgrade($date,$date);
			$info = $list[0];
			if($type == 1){
				$home = json_decode($info['home'],true);
				$levelArr = $home['list'];
			}else if($type == 2){
				$home = json_decode($info['tavern1'],true);
				$levelArr = $home['list'];
			}else if($type == 3){
				$home = json_decode($info['tavern2'],true);
				$levelArr = $home['list'];
			}else if($type == 4){
				$home = json_decode($info['tavern3'],true);
				$levelArr = $home['list'];
			}else if($type == 5){
				$home = json_decode($info['smithy'],true);
				$levelArr = $home['list'];
			}
            if ($levelArr) {
                $sortAry = array();
                foreach ($levelArr as $k => $v) {
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