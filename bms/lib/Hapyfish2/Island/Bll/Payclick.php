<?php

class Hapyfish2_Island_Bll_Payclick
{
	public static function getPayclick($platform, $day)
	{
		$data = null;
		try {
			$dalPayclick = Hapyfish2_Island_Dal_Payclick::getDefaultInstance();
			$dalPayclick->setDbPrefix($platform);
			$data = $dalPayclick->getPayclick($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getPayclickRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dalPayclick = Hapyfish2_Island_Dal_Payclick::getDefaultInstance();
			$dalPayclick->setDbPrefix($platform);
			$data = $dalPayclick->getRangePayclick($begin, $end);
			
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
			info_log($e->getMessage(), 'Payclick.err');
		}
		
		return $data;
	}

    public static function add($platform, $info)
    {    	
        if (empty($info)) {
            info_log($platform . ': no data', 'Hapyfish2_Island_Bll_payclick.add');
            return;
        }
        
        try {
        	print_r($info);
            $dal = Hapyfish2_Island_Dal_Payclick::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insert($info); 
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
    }
    
}