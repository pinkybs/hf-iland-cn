<?php

class Hapyfish2_Island_Tool_Clearcache
{
    public function clearAllUserCacheByDB($dbId)
    {
        info_log('/**************start*************/'.date('Y-m-d H:i:s'), 'clearUser');

        $nowTime = time();
        $clearTime = $nowTime - 30*24*60*60;
        //$startClearTime = $clearTime - 24*60*60;
        $start = 0;
        $end = 190;
        
        $dalClearcache = Hapyfish2_Island_Dal_Clearcache::getDefaultInstance();
        $userCount = 0;
        try {
        	for ( $t = 0; $t < 10; $t++ ) {
	            for ( $i=$start; $i < $end; $i++ ) {
	                info_log('DB:'.$dbId.'-table:'.$t.'-p:'.$i.'-t:'.date('Y-m-d H:i:s'), 'clearUser');
	                $uidList = self::getUidListVoDada($i+1, $dbId, $t);
	                $count = 0;
	                for ( $j=0,$jCount=count($uidList); $j<$jCount; $j++ ) {
	                    $uid = $uidList[$j]['uid'];
	                    $cache = Hapyfish2_Cache_Factory::getMC($uid);
	                    
                        $loginInfo = Hapyfish2_Island_HFC_User::getUserLoginInfo($uid);
                        $lastLoginTime = $loginInfo['last_login_time'];
	                    //if ( $lastLoginTime < $clearTime && $lastLoginTime >= $startClearTime ) {
	                    if ( $lastLoginTime < $clearTime ) {
	                        $cache->delete('i:u:exp:' . $uid);
	                        $cache->delete('i:u:coin:' . $uid);
	                        $cache->delete('i:u:gold:' . $uid);
	                        $cache->delete('i:u:level:' . $uid);
	                        $cache->delete('i:u:island:' . $uid);
	                        $cache->delete('i:u:title:' . $uid);
	                        $cache->delete('i:u:cardstatus:' . $uid);
	                        $cache->delete('i:u:starfish:' . $uid);
	                        $cache->delete('i:u:boatavtime' . $uid);
	                        $cache->delete('i:u:login:' . $uid);
	                        $cache->delete('i:u:island:' . $uid);
	                        $cache->delete('i:u:title:' . $uid);
	                        $cache->delete('i:u:cardstatus:' . $uid);
	                        $cache->delete('i:u:currentIsland:' . $uid);
                            $cache->delete('i:u:dock:' . $uid . '_1');
                            $cache->delete('i:u:dock:' . $uid . '_2');
	                        $cache->delete('i:u:dock:' . $uid . '_3');
	                        $cache->delete('i:u:dock:' . $uid . '_4');
	                        $cache->delete('i:u:dock:' . $uid . '_5');
	                        $cache->delete('i:u:dock:' . $uid . '_6');
	                        $cache->delete('i:u:dock:' . $uid . '_7');
	                        $cache->delete('i:u:dock:' . $uid . '_8');
	                        $cache->delete('i:u:card:' . $uid);
	                        $cache->delete('i:u:achdly:' . $uid);
	                        $cache->delete('i:u:ach:' . $uid);
	                        
                            $cache->delete('i:u:activity:' . $uid);
                            $cache->delete('i:u:bg:' . $uid);
                            $cache->delete('i:u:bldids:all:' . $uid);
                            $cache->delete('i:u:bldids:onisl:' . $uid);
                            $cache->delete('i:u:bldids:onisl:' . $uid . ':1');
                            $cache->delete('i:u:bldids:onisl:' . $uid . ':2');
                            $cache->delete('i:u:bldids:onisl:' . $uid . ':3');
                            $cache->delete('i:u:bldids:onisl:' . $uid . ':4');
                            $cache->delete('i:u:giftcntdly:' . $uid);
                            $cache->delete('i:u:damagecntdly:' . $uid);
                            $cache->delete('i:u:mammonStart:' . $uid);
                            $cache->delete('i:u:mammonUsingTime:' . $uid);
                            $cache->delete('i:u:poorStartTime:' . $uid);
                            $cache->delete('i:u:poorUsingTime:' . $uid);
                            $cache->delete('i:u:buchangetf:' . $uid);
                            $cache->delete('bottle:todaytf:' . $uid);
                            $cache->delete('i:u:unlockshcnt:' . $uid);
                            $cache->delete('i:u:unlockshids:' . $uid. ':1');
                            $cache->delete('i:u:unlockshids:' . $uid. ':2');
                            $cache->delete('i:u:unlockshids:' . $uid. ':3');
                            $cache->delete('i:u:unlockshids:' . $uid. ':4');
                            $cache->delete('i:u:unlockshids:' . $uid. ':5');
                            $cache->delete('i:u:unlockshids:' . $uid. ':6');
                            $cache->delete('i:u:unlockshids:' . $uid. ':7');
                            $cache->delete('i:u:unlockshids:' . $uid. ':8');
                            $cache->delete('i:u:feed:' . $uid);
                            $cache->delete('i:u:feed:count:' . $uid);
                            $cache->delete('i:u:gift:log:post:' . $uid);
                            $cache->delete('i:u:gift:log:get:' . $uid);
                            $cache->delete('i:u:gift:log:count:get:' . $uid);
                            $cache->delete('i:u:gift:log:count:post:' . $uid);
                            $cache->delete('i:u:mooch:ship:' . $uid . ':1');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':2');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':3');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':4');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':5');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':6');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':7');
                            $cache->delete('i:u:mooch:ship:' . $uid . ':8');
                            $cache->delete('i:u:pltids:all:' . $uid);
                            $cache->delete('i:u:pltids:onisla:' . $uid);
                            $cache->delete('i:u:pltids:onisla:' . $uid.':1');
                            $cache->delete('i:u:pltids:onisla:' . $uid.':2');
                            $cache->delete('i:u:pltids:onisla:' . $uid.':3');
                            $cache->delete('i:u:pltids:onisla:' . $uid.':4');
                            $cache->delete('island:userplantbyik:' . $uid);
                            $cache->delete('i:tm:opisland:' . $uid.':1');
                            $cache->delete('i:tm:opisland:' . $uid.':2');
                            $cache->delete('i:tm:opisland:' . $uid.':3');
                            $cache->delete('i:tm:opisland:' . $uid.':4');
                            $cache->delete('i:lk:opisland:' . $uid.':1');
                            $cache->delete('i:lk:opisland:' . $uid.':2');
                            $cache->delete('i:lk:opisland:' . $uid.':3');
                            $cache->delete('i:lk:opisland:' . $uid.':4');
                            $cache->delete('i:u:remind:' . $uid);
                            $cache->delete('i:u:remind:count:' . $uid);
                            $cache->delete('i:u:alltask:' . $uid);
                            $cache->delete('i:u:openTask2:' . $uid);
                            $cache->delete('i:u:alltaskdly:' . $uid);
                            $cache->delete('i:u:tenlvl:' . $uid);
                            $cache->delete('i:u:isapp:' . $uid);
                            $cache->delete('i:u:ezinecount:' . $uid);
                            $cache->delete('i:u:getvipbuild:' . $uid);
                            $cache->delete('i:u:getvipgift:' . $uid);
                            $cache->delete('i:u:isltp:' . $uid);
                            $cache->delete('i:u:isfstin:' . $uid);
                            $cache->delete('i:u:help2:' . $uid);
                            $cache->delete('i:u:star:' . $uid);
                            $cache->delete('i:u:star:athena:' . $uid);
                            
	                        /** event ***/
	                        $cache->delete('ev:hall:card:chance:' . $uid);
	                        $cache->delete('ev:hall:card:' . $uid);
	                        $cache->delete('ev:hall:time:' . $uid);
	                        $cache->delete('ev:thday:build:' . $uid);
	                        $cache->delete('ev:thday:love:' . $uid);
	                        $cache->delete('evlock:thday:loveincThr:' . $uid);
                            $cache->delete('ev:thday:addlove:' . $uid);
                            $cache->delete('ev:thday:loveMax:' . $uid);
                            $cache->delete('ev:thday:site:1' . $uid);
                            $cache->delete('ev:thday:site:2' . $uid);
                            $cache->delete('ev:thday:site:3' . $uid);
                            $cache->delete('ev:thday:site:4' . $uid);
                            $cache->delete('ev:thday:site:flag:1' . $uid);
                            $cache->delete('ev:thday:site:flag:2' . $uid);
                            $cache->delete('ev:thday:site:flag:3' . $uid);
                            $cache->delete('ev:thday:site:flag:4' . $uid);
                            $cache->delete('ev:thday:site:num:' . $uid);
                            $cache->delete('i:u:casinop:' . $uid);
                            $cache->delete('collectgift_haveget_' . $uid);
                            $cache->delete('normal_collectgift_haveget_' . $uid);
                            $cache->delete('i:u:e:invf_gold2:' . $uid);
                            $cache->delete('i:u:e:m-a:' . $uid);
                            $cache->delete('i:u:e:m-a:send:' . $uid);
                            $cache->delete('i:u:e:g:f:list' . $uid);
                            $cache->delete('i:u:oneshop:gift:get_status:' . $uid);
                            $cache->delete('i:e:oneshop:box:qishu:' . $uid);
                            $cache->delete('i:e:oneshop:gift:bigbox:' . $uid);
                            $cache->delete('qixi:get:gift:' . $uid);
                            $cache->delete('event:xmas:fair:qixi:daily:' . $uid);
                            $cache->delete('i:e:teambuy:buygood:' . $uid);
                            $cache->delete('i:e:teambuy:check:' . $uid);
                            $cache->delete('event_timegift_' . $uid);
                            $cache->delete('i:u:upgradegift:ft:' . $uid);
                            $cache->delete('i:u:eventsendrose:' . $uid);
                            $cache->delete('event_xmas_fair_daily_' . $uid);
                            $cache->delete('i:u:xmas_fair:' . $uid);
                            
	                        $plantList = $dalClearcache->getUserPlantList($uid);
	                        for ( $m=0,$mCount=count($plantList); $m<$mCount; $m++ ) {
	                            $plantItemId = $plantList[$m]['id'];
	                            $cache->delete('i:u:plt:' . $uid . ':' . $plantItemId);
	                        }
	                        
	                        $buildingList = $dalClearcache->getUserBuildingList($uid);
                            for ( $n=0,$nCount=count($buildingList); $n<$nCount; $n++ ) {
                                $buildingItemId = $buildingList[$n]['id'];
                                $cache->delete('i:u:bld:' . $uid . ':' . $buildingItemId);
                            }
	                        
	                        $userCount = $userCount + 1;
	                        $count++;
	                    }
	                }
                    info_log('$userCount-:'.$userCount.'-nowcount:'.$count, 'clearUser');
	            }
            }
        
            /*$cache->delete('ev:hall:card');
            $cache->delete('ev:thday:robot');
            $cache->delete('ev:thday:gift');
            $cache->delete('ev:thday:rank:new');
            $cache->delete('ev:thday:rank:min');
            $cache->delete('ev:BlackDay:buyNum');
            $cache->delete('event:pointchalist');
            $cache->delete('collectcontrolswitch');
            $cache->delete('i:e:oneshop:gift');
            $cache->delete('i:e:oneshop:gift:hasnum');
            $cache->delete('event_xmas_qixi_fair');
            $cache->delete('i:e:teambuy:info');
            $cache->delete('i:e:teambuy:opened');
            $cache->delete('event_valentine_exchange_list');
            $cache->delete('event_valentine_rank_list');
            $cache->delete('event_xmas_fair');*/
            
            info_log('alluserCount:'.$userCount, 'clearUser');
        }
        catch (Exception $e) {
            info_log('error:'.$e->getMessage(), 'clearUser');
        }
        
        info_log('/**************end*************/'.date('Y-m-d H:i:s'), 'clearUser');
    }
    
    public static function getUidListVoDada($pageIndex, $dbId, $tableId)
    {
        $uidListVo = self::restore($pageIndex, $dbId, $tableId);
        return Zend_Json::decode($uidListVo);
    }
    
    public static function restore($pageIndex, $dbId, $tableId)
    {
        $file = '/data/temp/uid/'.$dbId.'/'.$tableId.'/uid.' . $pageIndex . '.cache';
        if (is_file($file)) {
            return file_get_contents($file);
        } else {
            return self::dump($pageIndex, $dbId, $tableId);
        }
    }
    
    public static function dump($pageIndex, $dbId, $tableId)
    {
        $resultUidList = self::getUidListVo($pageIndex, $dbId, $tableId);
        $file = '/data/temp/uid/'.$dbId.'/'.$tableId.'/uid.' . $pageIndex . '.cache';
        $data = json_encode($resultUidList);
        file_put_contents($file, $data);
        return $data;
    }
    
    public static function getUidListVo($pageIndex, $dbId, $tableId)
    {
        $resultUidList = array();
        
        $dalClearcache = Hapyfish2_Island_Dal_Clearcache::getDefaultInstance();
        $resultUidList = $dalClearcache->getUidListByPage($pageIndex, 1000, $dbId, $tableId);
        return $resultUidList;
    }
    
	
}