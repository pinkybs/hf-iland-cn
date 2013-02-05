<?php

class Hapyfish2_Island_Tool_Fans
{
	public static function fetchOfficialFan()
	{
	    $officialId = '2155826421';
	    $wbAppKey = '1701900471';
	    $wbAppSecret = 'afafdd89752cb606d6d7191813faaa86';
	    $testId = 'happyfishtest3@163.com';
	    $testPw = '123456';
	    $mKey = 'island:tool:wb:admfan';
	    $today = date('Ymd');
	    $result = null;
	    try {
    		$wbApi = new SinaWeibo_Weiboapp($wbAppKey, $wbAppSecret);
            $aryToken = $wbApi->getAccessTokenByPass($testId, $testPw);
    	    //{"access_token":"6161345e62e1d11763443ff586974fba","expires_in":7200,"refresh_token":"e1dd956936cd3e0f618ef63c2c1f11b9"}
            if ($aryToken && isset($aryToken['access_token']) && isset($aryToken['refresh_token'])) {
                $wbApi->setRest($aryToken['access_token'], $aryToken['refresh_token']);
                $user = $wbApi->getUser($officialId);
                if ($user && isset($user['followers_count'])) {
                    $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
                    $cache->set($mKey, array($user['followers_count'], $today));
                    $result = $user['followers_count'];
                }
                else {
                    throw new Exception('failed to get official user info');
                }
    	    }
    	    else {
    	        throw new Exception('failed to get access token');
    	    }
	    }
	    catch (Exception $e) {
	        info_log($e->getMessage(), 'Hapyfish2_Island_Tool_Fans');
	    }
		return $result;
	}

	public static function getOfficialFan()
	{
	    $today = date('Ymd');
	    $mKey = 'island:tool:wb:admfan';
        $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
        $info = $cache->get($mKey);
        /*if (!$info) {
            return self::fetchOfficialFan();
        }
        if ($info && isset($info[1]) && $today > $info[1]) {
            return self::fetchOfficialFan();
        }*/

        return $info[0];
	}

	public static function setOfficialFan($num)
	{
	    $mKey = 'island:tool:wb:admfan';
	    $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
        $cache->set($mKey, array($num, date('Ymd')));
        return true;
	}

}