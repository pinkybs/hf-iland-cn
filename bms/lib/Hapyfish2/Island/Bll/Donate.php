<?php

class Hapyfish2_Island_Bll_Donate
{

    public static function addDayDonate($platform, $info)
    {
        if (empty($info)) {
            info_log($platform . ': no data : addDayDonate', 'Hapyfish2_Island_Bll_Donate');
            return false;
        }

        try {
            $dal = Hapyfish2_Island_Dal_Donate::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insUpd($info);

            return true;
        } catch (Exception $e) {
            info_log('addDayDonate:'.$e->getMessage(), 'Hapyfish2_Island_Bll_Donate');
            return false;
        }
    }
}