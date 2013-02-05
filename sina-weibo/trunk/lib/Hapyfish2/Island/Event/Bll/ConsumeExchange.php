<?php


class Hapyfish2_Island_Event_Bll_ConsumeExchange
{
    public static function getConsumeStep($uid,$start,$end){
    	$time = time();
        $key = 'consumeandgive:'.$uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ConsumeEvent = self::getConsumeEvent();
        $data = false;
        $ConsumeStep = $cache->get($key);
        if($ConsumeStep){
            if($ConsumeStep['update_time'] >= $ConsumeEvent['start'] && $ConsumeStep['update_time'] <= $ConsumeEvent['end']){
                $data = $ConsumeStep;
            }
        }
		if ($data === false) {
			try {
    			$dalConsume = Hapyfish2_Island_Event_Dal_ConsumeExchange::getDefaultInstance();
    			$step_list = $dalConsume->getConsumeStep($uid,$start,$end);
    			$data['data'] = $step_list;
    			$data['update_time'] = $time;
    			$cache->set($key, $data);
				return $data;
			} catch (Exception $e) {
				return -1;
			}
		} else {
			return $data;
		}
    }
    public static function getConsumeEvent(){
        $key = 'consumeandgive:';
        $cache = self::getBasicMC();
        $data = $cache->get($key);
		if ($data === false) {
			try {
    			$dalConsume = Hapyfish2_Island_Event_Dal_ConsumeExchange::getDefaultInstance();
    			$consumeList = $dalConsume->getConsume();
    			$data ['data'] = $consumeList;
    			if($consumeList[0]['start']==$consumeList[1]['start'] && $consumeList[0]['start']==$consumeList[2]['start']){
    			    $data ['start'] = $consumeList[0]['start'];
    			}else{
    			    return false;
    			}
			    if($consumeList[0]['end']==$consumeList[1]['end'] && $consumeList[0]['end']==$consumeList[2]['end']){
    			    $data ['end'] = $consumeList[0]['end'];
    			}else{
    			    return false;
    			}
    			$cache->set($key, $data);
				return $data;
			} catch (Exception $e) {
				return -1;
			}
		} else {
			return $data;
		}
    }
    public static function getConsumeGold($uid,$start,$end){
       $dalConsume = Hapyfish2_Island_Event_Dal_ConsumeExchange::getDefaultInstance();
       $gold = $dalConsume->getGold($uid,$start,$end);
       return $gold;
    }
    public static function Exchange($uid,$step){
        $result = array('result' => array('status' => '-1', 'content' => 'serverWord_110'));
    	if ($step < 1 || $step > 3) {
    		return  $result;
    	}
    	$info = self::getConsumeEvent();
    	if($info){
    		foreach($info['data'] as $k => $v){
    			$detail[$v['window']] = $v['cid'];
    		}
    	}
    	$compensation = new Hapyfish2_Island_Bll_Compensation();
		$cid = $detail['window'.$step];
		$window = 'window'.$step;
       	$plantinfo = Hapyfish2_Island_Cache_BasicInfo::getDetailInfo($cid);
       	$tilte = '恭喜你成功领取'.$plantinfo['name'];
       	$compensation->setItem($cid, 1);
		$compensation->setFeedTitle($tilte);
		$ok = $compensation->sendOne($uid, '');
		$consumeEvent = self::getConsumeEvent();
		if ($ok) {
				$time = time();
			try {
				$key = 'consumeandgive:'.$uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$dal = Hapyfish2_Island_Event_Dal_ConsumeExchange::getDefaultInstance();
				$info = array('uid' => $uid, 'step' => $step, 'create_time' => $time);
				$dal->insert($uid, $info);
				$dalConsume = Hapyfish2_Island_Event_Dal_ConsumeExchange::getDefaultInstance();
    			$step_list = $dalConsume->getConsumeStep($uid,$consumeEvent['start'],$consumeEvent['end']);
    			$data['data'] = $step_list;
    			$data['update_time'] = $time;
				$cache->set($key, $data);
			} catch (Exception $e) {
				info_log($e->getMessage(), 'Event_ConsumeExchange');
			}
			$picUrl = STATIC_HOST.'/apps/island/images/event/exchange.jpg';
			$result = array('result' => array('status' => 1),'window' => $window,'picUrl' => $picUrl);
		}

		return $result;
    }
    public static function getBasicMC(){
         $key = 'mc_0';
		 return Hapyfish2_Cache_Factory::getBasicMC($key);
    }
}