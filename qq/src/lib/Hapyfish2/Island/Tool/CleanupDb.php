<?php

class Hapyfish2_Island_Tool_CleanupDb
{
    public static $logFile = 'crontask_clearDb';

	public static function cleanQpointBuy($tmExpire)
	{

	    $dbNum = 24;
	    try {
	        info_log('cleanup-qpointbuy:', self::$logFile);
    	    for ($dbid=0; $dbid<$dbNum; $dbid++) {
    	        $db = Hapyfish2_Db_FactoryTool::getDB($dbid);
    	        $wdb = $db['w'];
    	        $strMsg = '';
    	        $dal = Hapyfish2_Island_Dal_QpointBuy::getDefaultInstance();
    	        for ($i=0; $i<10; $i++) {
    	            $rst = $dal->clear($uid, $tmExpire);
                    $strMsg .= $rst->rowCount() . "\t";
                    //$strMsg .= $rst . "\t";
    	        }

    	        info_log('db'.$dbid.': '.$strMsg, self::$logFile);
    	    }
	    } catch (Exception $e) {
			info_log($e->getMessage(), self::$logFile);
			return false;
		}

		return true;
	}

}