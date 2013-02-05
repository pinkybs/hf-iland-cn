<?php

define('ROOT_DIR', realpath('../'));
include_once ROOT_DIR . '/bin/config.php';

try {
	$dbId = 7;
	info_log('/****savedbAllUser - islandId = '.$dbId.' - start*****/', 'savedbAllUser_'.$dbId);

    info_log('# savedbAllUser - start - #', 'savedbAllUser_'.$dbId);
		//2012-02-26 00:00:00
		$startTime = 1330185600;
		
		$db = Hapyfish2_Island_Dal_Savediy::getDefaultInstance();
			
		//for($i=0;$i<8;$i++) {
			for($j=0;$j<10;$j++) {
				$count = 0;
				info_log('savedbAllUser:tableId-'.$j, 'savedbAllUser_'.$dbId);
				$uidList = $db->getUidListByPage($dbId, $j, $startTime);
				$kCount = count($uidList);
				//for ( $k=0;$k<$kCount;$k++) {
					Hapyfish2_Island_Tool_SaveOldUserCache::saveOne(2959738);
					$count++;
					//Hapyfish2_Island_Tool_SaveOldUserCache::savePlant($uidList[$k]['uid']);
					//Hapyfish2_Island_Tool_SaveOldUserCache::saveBuilding($uidList[$k]['uid']);
					info_log('savedbAllUser:'.$uidList[$k]['uid'].'--count:'.$count, 'savedbAllUser_'.$dbId);
				//}
			}
		//}
		info_log('# savedbAllUser - end - #', 'savedbAllUser_'.$dbId);
		
	info_log('/****savedbAllUser - end*******/', 'savedbAllUser_'.$dbId);
	exit;
}
catch (Exception $e) {
	err_log($e->getMessage());
}