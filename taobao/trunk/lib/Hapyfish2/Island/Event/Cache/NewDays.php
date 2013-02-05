<?php

/**
 * Event NewDays
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/12/23    zhangli
*/
class Hapyfish2_Island_Event_Cache_NewDays
{
	/**
	 * @获取锤子砸中物品概率
	 * @param int $eid
	 * @return Array
	 */
	public static function getEggData($eid)
	{
		//0->2,1->0,2->1,3->2,4->元,5->旦,6->快,7->乐,
		//8->船只加速卡I,9->双倍经验卡,10->船只加速卡II,11->金币赠送卡,12->加速捕鱼卡,13->一键收钱卡,14->3星建设卡,15->船只加速卡III
		$key = 'ev:newdays:items';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$list = $cache->get($key);

		if ($list === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_NewDays::getDefaultInstance();
				$list = $db->getListArr();
			} catch (Exception $e) {}

			if ($list) {
				$cache->set($key, $list, 3600 * 24 * 15);
			}
		}
		
		foreach ($list as $ls) {
			if ($ls['eid'] == $eid) {
				$dataCo = $ls;
				break;
			}
		}
		
		return $dataCo;
	}
	
	/**
	 * @根据ID获取物品CID
	 * @param int $gainID
	 * @return Array
	 */
	public static function getCardID($gainID)
	{
		//0->2,1->0,2->1,3->2,4->元,5->旦,6->快,7->乐,
		//8->船只加速卡I,9->双倍经验卡,10->船只加速卡II,11->金币赠送卡,12->加速捕鱼卡,13->一键收钱卡,14->3星建设卡,15->船只加速卡III
		$cids = array('0' => '133241', '1' => '133041', '2' => '133141', '3' => '133341', '4' => '133741', '5' => '133441', '6' => '133541', '7' => '133641',
					'8' => '26241', '9' => '74841', '10' => '26341', '11' => '130341', '12' => '110441', '13' => '67441', '14' => '56741', '15' => '26441');
		
		foreach ($cids as $key => $cid) {
			if ($gainID == $key) {
				return $cid;
			}
		}
		
		return 0;
	}
	
	/**
	 * @获取当日金币锤子的次数
	 * @param int $uid
	 * @return int
	 */
	public static function getWoodenHammerNum($uid)
	{
		$key = 'ev:newdays:woodenhammer:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);

		return $data;
	}

	/**
	 * @增加当日使用金币锤子的次数
	 * @param unknown_type $uid
	 */
	public static function addWoodenHammerNum($uid)
	{
		$key = 'ev:newdays:woodenhammer:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
	
		$data += 1;

		$logDate = date('Y-m-d');
		$dtDate = $logDate . ' 23:59:59';
		$endTime = strtotime($dtDate);
		
		$cache->set($key, $data, $endTime);
	}
	
	/**
	 * @获取当前兑换礼包的信息
	 * @param int $pid
	 * @return Array
	 */
	public static function getData($pid)
	{
		//item:兑换需求,list:奖励物品
		$dataVo = array(array('pid' => 1, 'item' => array('133241' => 1, '133041' => 1, '133141' => 1, '133341' => 1), 'list' => array('132132' => 1, '74841' => 10)),
						array('pid' => 2, 'item' => array('133741' => 1, '133441' => 1, '133541' => 1, '133641' => 1), 'list' => array('132232' => 1, '74841' => 10, '110441' => 20, '2' => 5)),
						array('pid' => 3, 'item' => array('133241' => 1, '133041' => 1, '133141' => 1, '133341' => 1, '133741' => 1, '133441' => 1, '133541' => 1, '133641' => 1), 'list' => array('132332' => 1, '132632' => 1, '74841' => 10, '110441' => 40, '2' => 10)));
		
		foreach ($dataVo as $itemVo) {
			if ($itemVo['pid'] == $pid) {
				$data = $itemVo;
				break;
			}
		}
						
		return $data;
	}
	
}