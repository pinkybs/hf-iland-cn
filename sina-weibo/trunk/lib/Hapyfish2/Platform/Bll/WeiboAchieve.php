<?php

class Hapyfish2_Platform_Bll_WeiboAchieve
{
    protected static $_mcKeyPrex = 'i:u:sinawb:achi';

    //protected static $_gameAchieveId = array(3007,3015,3018,3038,3044,3059,3001,3004,3009,3012,3021,3024,3027,3030,3041,3047,3050,3053,3062,3071,3074,3077,3080,3002,3005,3008,3010,3013,3016,3019,3022,3025,3028,3031,3039,3042,3045,3048,3051,3054,3060,3063,3072,3075,3078,3081);

    protected static $_weiboAchieveId = array(3007=>1,3015=>2,3018=>3,3038=>4,3044=>5,3059=>6,3001=>7,3004=>8,3009=>9,3012=>10,
                                              3021=>11,3024=>12,3027=>13,3030=>14,3041=>15,3047=>16,3050=>17,3053=>18,3062=>19,3071=>20,
                                              3074=>21,3077=>22,3080=>23,3002=>24,3005=>25,3008=>26,3010=>27,3013=>28,3016=>29,3019=>30,
                                              3022=>31,3025=>32,3028=>33,3031=>34,3039=>35,3042=>36,3045=>37,3048=>38,3051=>39,3054=>40,
                                              3060=>41,3063=>42,3072=>43,3075=>44,3078=>45,3081=>46);


    public static function checkAchieveId($uid, $achieveId)
    {
    	//return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $sessionKey = $context->get('session_key');
            $rest = SinaWeibo_Client::getInstance();
            $rest->setUser($sessionKey);

            $mckey = self::$_mcKeyPrex . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $lstGained = $cache->get($mckey);
            if (!$lstGained) {
                //get user current achieve from weibo platform
                $lstGained = $rest->listAchieve();
//info_log($uid.'|'.json_encode($lstGained), 'fromsina-checkAchieveId');
                if (null === $lstGained) {
                    return false;
                }
                $cache->set($mckey, $lstGained);
            }

//info_log($achieveId, 'fromsina-checkAchieveId');
            //if is complete
            if (array_key_exists($achieveId, self::$_weiboAchieveId)) {
                $weiboAid = self::$_weiboAchieveId[$achieveId];
                 if (!in_array($weiboAid, $lstGained)) {
//info_log($weiboAid.' call api', 'fromsina-checkAchieveId');
                    $rst = $rest->setAchieve($weiboAid);
                    if ($rst) {
//info_log('delcache', 'fromsina-checkAchieveId');
                        $cache->delete($mckey);
                    }
                }
            }
        }
        catch (Exception $e) {
            //info_log('checkAchieveId_Err:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboAchieve');
            return false;
        }

        return true;
    }

    public static function checkAchieveAll($uid, $aryAchieve)
    {
		return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $sessionKey = $context->get('session_key');
            $rest = SinaWeibo_Client::getInstance();
            $rest->setUser($sessionKey);

            $mckey = self::$_mcKeyPrex . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $lstGained = $cache->get($mckey);
            if (!$lstGained) {
                //get user current achieve from weibo platform
                $lstGained = $rest->listAchieve();
//info_log($uid.'|old|'.json_encode($lstGained), 'fromsina-checkAchieveAll');
                if (null === $lstGained) {
                    return false;
                }
                $cache->set($mckey, $lstGained);
            }

//info_log($uid.'|'.json_encode($aryAchieve), 'fromsina-checkAchieveAll');
            $newComplete = array();
            foreach ($aryAchieve as $data) {
                $gameAchieveId = $data['taskClassId'];
                //achieve complete && achieve in sina achieve && is new complete achieve
                if ($data['state'] != 0 && array_key_exists($gameAchieveId, self::$_weiboAchieveId)) {
                    $weiboAid = self::$_weiboAchieveId[$gameAchieveId];
                    if (!in_array($weiboAid, $lstGained)) {
                        $newComplete[] = $weiboAid;
                    }
                }
            }

//info_log($uid.'|new|'.json_encode($newComplete), 'fromsina-checkAchieveAll');
            //update sina achieve
            if ($newComplete && count($newComplete) > 0) {
                foreach ($newComplete as $data) {
                    $rest->setAchieve($data);
                }
                $cache->delete($mckey);
            }
        }
        catch (Exception $e) {
            //info_log('checkAchieveAll_Err:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboAchieve');
            return false;
        }

        return true;
    }

}