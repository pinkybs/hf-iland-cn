<?php

class Hapyfish2_Island_Bll_Sendgold
{
	public static function getSendgold($platform, $day)
	{
		$data = null;
		try {
			$dalSendgold = Hapyfish2_Island_Dal_Sendgold::getDefaultInstance();
			$dalSendgold->setDbPrefix($platform);
			$data = $dalSendgold->getSendgold($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getSendgoldRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dalSendgold = Hapyfish2_Island_Dal_Sendgold::getDefaultInstance();
			$dalSendgold->setDbPrefix($platform);
			$data = $dalSendgold->getRangeSendgold($begin, $end);
			
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
			info_log($e->getMessage(), 'Sendgold.err');
		}
		
		return $data;
	}

    public static function add($platform, $info)
    {    	
        if (empty($info)) {
            info_log($platform . ': no data', 'Hapyfish2_Island_Bll_Sendgold.add');
            return;
        }
        
        try {
            $dal = Hapyfish2_Island_Dal_Sendgold::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insert($info); 
        } catch (Exception $e) {
        }
    }
    
}