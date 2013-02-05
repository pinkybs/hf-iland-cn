<?php

class Hapyfish2_Island_Stat_Bll_Goldlog
{
	public static function handle($day, $file)
	{
		$content = file_get_contents($file);
		if (empty($content)) {
			info_log('no data', 'stat.log.catch.fish.err');
			return;
		}
		
		$temp = explode("\n", $content);
		
		$allCount = 0;
		$goldInfo = array('type0' => 0,
						  'type1' => 0,
						  'type2' => 0,
						  'type3' => 0,
						  'type4' => 0,
						  'type5' => 0,
						  'type6' => 0,
						  'type7' => 0,
						  'type8' => 0,
						  'type9' => 0,
						  'type10' => 0,
						  'type11' => 0,
						  'type12' => 0,	
						  'type13' => 0,
						  'type14' => 0,
						  'type15' => 0);
		
		foreach ($temp as $line) {
			if (empty($line)) {
				continue;
			}
			
			$r = explode("\t", $line);
			
			//array($uid, $fishId, $levelId)
			$uid= $r[2];
			$goldNum = $r[3];
			$type = $r[4];
			if($type == '') {
				continue;
			}
			$goldInfo["type".$type] = $goldInfo["type".$type] + $goldNum;
			$allCount = $allCount + $goldNum;
		}
		
		$goldInfo['create_time'] = $day;
        $dalCatchisland = Hapyfish2_Island_Stat_Dal_Goldlog::getDefaultInstance();
        $dalCatchisland->insert($goldInfo);
        
        $dalCatchisland->updateMain($allCount, $day);
		return $goldInfo;
	}
	
	public function getSendGoldLog($day)
	{
        $data = null;
        try {
            $dal = Hapyfish2_Island_Stat_Dal_Goldlog::getDefaultInstance();
            $data = $dal->getSendGoldlog($day); 
        } catch (Exception $e) {
            
        }
        
        return $data;		
	}
}