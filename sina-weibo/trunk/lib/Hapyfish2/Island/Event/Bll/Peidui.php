<?php
/**
 * bujisky.li
 * bujisky.li@hapyfish.com
 * */
class Hapyfish2_Island_Event_Bll_Peidui
{
	public static function Peidui($cid, $time, $pcid) 
	{
		$db = array();
		for($i=0;$i<DATABASE_NODE_NUM;$i++){
			for($j=0;$j<=49;$j++){
				$db[$i][]= DATABASE_NODE_NUM*$j + $i;
			}
		}
		$dal = Hapyfish2_Island_Event_Dal_Peidui::getDefaultInstance();
		foreach($db as $k => $v){
			foreach($v as $k1 => $v1){
				$uidlist = $dal->getUid($v1, $cid, $time);
				if($uidlist){
					foreach($uidlist as $k2 => $v2){
						$com = new Hapyfish2_Island_Bll_Compensation();
						$com->setItem($pcid, 1); 
						$ok = $com->sendOne($v2, '中秋收集任务配对：');
						if($ok){
							info_log($v2, 'zhongqiu_peidui'.$pcid);
						}
					}
				}
			}
		}
	}
		
}