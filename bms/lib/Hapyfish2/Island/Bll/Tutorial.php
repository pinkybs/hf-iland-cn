<?php

class Hapyfish2_Island_Bll_Tutorial
{
	public static function getTutorial($platform, $day)
	{
		$data = null;
		try {
			$dalTutorial = Hapyfish2_Island_Dal_Tutorial::getDefaultInstance();
			$dalTutorial->setDbPrefix($platform);
			$data = $dalTutorial->getTutorial($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getTutorialRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dalTutorial = Hapyfish2_Island_Dal_Tutorial::getDefaultInstance();
			$dalTutorial->setDbPrefix($platform);
			$data = $dalTutorial->getRangeTutorial($begin, $end);
			if (!empty($data)) {
				for($i = 0, $len = count($data); $i < $len; $i++) {
					$d = $data[$i]['data'];
					if (empty($d)) {
						$data[$i]['data'] = array();
					} else {
						$data[$i]['data'] = json_decode($d, true);
					}
				}
			}
		} catch (Exception $e) {
			info_log($e->getMessage(), 'tutorial.err');
		}
		
		return $data;
	}

    public static function add($platform, $info)
    {    	
        if (empty($info)) {
            info_log($platform . ': no data', 'Hapyfish2_Island_Bll_Tutorial.add');
            return;
        }
        
        try {
            $dal = Hapyfish2_Island_Dal_Tutorial::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insert($info); 
        } catch (Exception $e) {
        }
    }
    
}