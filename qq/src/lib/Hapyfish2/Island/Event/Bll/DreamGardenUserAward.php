<?php
/**
 * bujisky.li
 * bujisky.li@hapyfish.com
 * */
class Hapyfish2_Island_Event_Bll_DreamGardenUserAward
{



	public static function receive($uid)
	{

		$data = self::check($uid);

		if( $data ) {
			// 已经领取完
			$result['state'] = '-1';
			$result['content'] = 'serverWord_201'; // #todo 错误代码未改
			return $result;
		}
		$send = new Hapyfish2_Island_Bll_Compensation();
		$send->setCoin(77777);
		$send->setItem('67441', 7);
		$send->setItem('26441', 7);
		$send->setItem('74841', 7);
		$send->setItem('104632', 1);
		$ok = $send->sendOne($uid, "恭喜您获得:");
		$coinChange = 77777;
		if($ok){
			$dreamgardenuser = Hapyfish2_Island_Event_Dal_DreamGardenUserAward::getDefaultInstance();
			$dreamgardenuser->insert($uid);
			$data['uid'] = $uid;
			$key = 'i:u:e:invf_dreamgardenuserqixi:' . $uid;
			$key1 = 'i:u:e:q:y:'.$uid ;
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        $cache -> set($key1, 1);
	        $cache->set($key, $data);
		}
		

        $result['feed'] = Hapyfish2_Island_Bll_Activity::send('QIXI_GIFT', $uid);

        $result['status'] = 1;
        $result['coinChange'] = $coinChange;
 		$result['itemBoxChange'] = true;
 		$ret['state'] = '1';
 		$ret['result'] = $result;
		return $ret;

	}
	public static function check( $uid )
	{

		$key = 'i:u:e:invf_dreamgardenuserqixi:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);

        $data = $cache->get($key);
 		$ntime = time();

 		$endtime = "2011-08-18 23:59:59";
 		if($ntime > strtotime($endtime))
 		{
 			return true;
 		}


		if ($data == false) {
			try {
    			$dreamgardenuser = Hapyfish2_Island_Event_Dal_DreamGardenUserAward::getDefaultInstance();
    			$data = $dreamgardenuser->get($uid);

    			$cache->set($key, $data);
				return $data;
			} catch (Exception $e) {
				return true;
			}
		} else {
			return $data;
		}
	}

}