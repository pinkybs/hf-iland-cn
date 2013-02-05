<?php

class Hapyfish2_Platform_Bll_WeiboRank
{
    protected static $_mcKeyPrex = 'i:u:sinawb:rk';

    public static function setRank($uid, $rankType, $value)
    {
    	//return true;
        try {

            if (!self::canSetRank($uid, $rankType)) {
//info_log($uid.'|'.$rankType.'|'.$value, 'wbranksetlimit');
                return false;
            }

            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $sessionKey = $context->get('session_key');
            $rest = SinaWeibo_Client::getInstance();
            $rest->setUser($sessionKey);

            $mckey = self::$_mcKeyPrex . $rankType . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $rankVal = $cache->get($mckey);
            if ($rankVal != $value) {
                $rtn = $rest->setRank($rankType, $value);
//info_log($rankType.'|'.$uid.'|'.$rankVal.'-'.$value.':'.json_encode($rtn) ,'sinarank');
                if ($rtn['errorCode'] == 0) {
                    $cache->set($mckey, $value);
                }
            }
        }
        catch (Exception $e) {
            info_log('setRank:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboRank');
            return false;
        }

        return true;
    }

    public static function canSetRank($uid, $rankType)
    {
        $result = false;
        try {
            if (WB_RANK_COST == $rankType) {
                $mckey = self::$_mcKeyPrex . 'ck' .$rankType . $uid;
                $cache = Hapyfish2_Cache_Factory::getMC($uid);
                $lastTime = $cache->get($mckey);
                $tm = time();
                if (empty($lastTime) || $tm-$lastTime >= 600) {
                    $cache->set($mckey, $tm);
                    $result = true;
                }
            }
            else {
                $result = true;
            }
        }
        catch (Exception $e) {
            info_log('checkSetRankTooFast:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboRank');
            return false;
        }

        return $result;
    }

}