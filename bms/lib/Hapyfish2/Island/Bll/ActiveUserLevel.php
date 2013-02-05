<?php

class Hapyfish2_Island_Bll_ActiveUserLevel
{
	public static function addActiveUserLevel($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data : addActiveUserLevel', 'Hapyfish2_Island_Bll_ActiveUserLevel.add');
			return;
		}
		
		try {
			$dal = Hapyfish2_Island_Dal_ActiveUserLevel::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insertActiveUserLevel($info); 
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.err');
		}
	}

    public static function addUserlevel($platform, $info)
    {
        if (empty($info)) {
            info_log($platform . ': no data : addUserlevel', 'Hapyfish2_Island_Bll_ActiveUserLevel.add');
            return;
        }
        
        try {
            $dal = Hapyfish2_Island_Dal_ActiveUserLevel::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insertUserlevel($info); 
            
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.err');
        }
    }

    public static function addLevelup($platform, $info)
    {
        if (empty($info)) {
            info_log($platform . ': no data : addLevelup', 'Hapyfish2_Island_Bll_ActiveUserLevel.add');
            return;
        }
        
        try {
            $dal = Hapyfish2_Island_Dal_ActiveUserLevel::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insertLevelup($info); 
            
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.err');
        }
    }
}