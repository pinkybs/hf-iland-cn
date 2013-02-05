<?php

class Hapyfish2_Island_Bll_UserExt
{
	public static function getUserMicroblog($uid)
	{
		try {
			$dal = Hapyfish2_Island_Dal_UserExt::getDefaultInstance();
	    	$row = $dal->get($uid);
		}
        catch (Exception $e) {
        	info_log('getUserMicroblog Err:'.$uid, 'Bll_UserExt_Err');
            info_log($e->getMessage(), 'Bll_UserExt_Err');
            return '';
        }

	    return empty($row) ? '' : $row['tblog'];
	}

	public static function saveMicroblog($uid, $name, $canChange=false)
	{
		if (empty($name) || mb_strlen($name) > 32) {
			return false;
		}

		try {
			$dal = Hapyfish2_Island_Dal_UserExt::getDefaultInstance();
	    	$row = $dal->get($uid);

	    	if (!empty($row) && !empty($row['tblog']) && !$canChange) {
	    		return false;
	    	}

	    	$info = array('tblog' => $name);
	    	if (empty($row)) {
	    		$info['uid'] = $uid;
	    		$dal->insert($uid, $info);
	    	}
	    	else {
	    		$dal->update($uid, $info);
	    	}
		}
        catch (Exception $e) {
        	info_log('saveMicroblog Err:'.$uid, 'Bll_UserExt_Err');
            info_log($e->getMessage(), 'Bll_UserExt_Err');
            return false;
        }

	    return true;
	}

}