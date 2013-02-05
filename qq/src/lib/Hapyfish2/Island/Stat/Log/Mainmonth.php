<?php

class Hapyfish2_Island_Stat_Log_Mainmonth
{
	public static function handle($month, $tempdir) 
	{
		$acvite = 0;
		$uidList = array();
		
        $tempfile = "101/$day/all-101-$day.log";
        
        if (in_array($m, array(1,3,5,7,8,10,12))) {
        	$j = 31;
        }
        else if ($m == 2 ) {
        	$j = 28;
        }
        else {
        	$j = 30;
        }
        
		for ($i=1; $i<=$j; $i++) {
			if ($i<10) {
				$i = '0'.$i;
			}
			$day = $month.$i;
			$file = $tempdir."101/$day/all-101-$day.log";
		
	        $content = file_get_contents($file);
	        if (!empty($content)) {
		        $tempData = explode("\n", $content);
		        foreach ( $tempData as  $line ) {
		        	if (empty($line)) {
		        		continue;
		        	}
		        	$r = explode("\t", $line);
		        	$uid= $r[2];
		        	if (!isset($uidList[$uid])) {
		        		$uidList[$uid] = 1;
		        		$acvite++;
		        	}
		        }
            }
		}
		
		$newData = array('log_time' => $month, 'active_user' => $acvite);
		$dal = Hapyfish2_Island_Stat_Dal_MainMonth::getDefaultInstance();
		$dal->insert($newData);
		
		return;
	}

    public static function getMainMonth($month)
    {
        $data = null;
        try {
            $dal = Hapyfish2_Island_Stat_Dal_MainMonth::getDefaultInstance();
            $data = $dal->getMainMonth($month); 
        } catch (Exception $e) {
            
        }
        
        return $data;
    }
}