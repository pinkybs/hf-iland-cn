<?php

/**
 * Event Christmas
 *
 * @package    Island/Event/Bll
 * @copyright  Copyright (c) 2011 Happyfish Inc.
 * @create     2011/11/25    zhangli
*/
class Hapyfish2_Island_Event_Cache_Christmas
{
	/**
	 * @记录用户首次点击任务
	 * @param int $uid
	 * @param int $taskId
	 * @param int $num
	 * @return Array
	 */
	public static function christmasOnceRequest($uid, $taskId, $num)
	{
		$key = 'ev:chrismas:first:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);	
		$merryChristmasTaskVo = $cache->get($key);

		if ($merryChristmasTaskVo === false) {
			$merryChristmasTaskVo = array(array('taskId' => 9, 'taskState' => 0));
		}
	
		foreach ($merryChristmasTaskVo as $fkey => $merryChristmasTask) {
			if ($taskId == $merryChristmasTask['taskId']) {
				$merryChristmasTaskVo[$fkey]['taskState'] = $num;
				break;
			}
		}
	
		$cache->set($key, $merryChristmasTaskVo);

		$result = array('status' => 1);
		$resultVo = array('result' => $result);
		return $resultVo;
	}
	
	/**
	 * @获取第一次点击任务信息
	 * @param int $uid
	 * @return Array
	 */
	public static function getChristmasOnceRequest($uid)
	{
		$key = 'ev:chrismas:first:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
	
		if ($data === false) {
			$data = array(array('taskId' => 9, 'taskState' => 0));

			$cache->set($key, $data);
		}
		
		return $data;
	}
	
	/**
	 * @获取圣诞节领取礼物状态
	 * @param int $uid
	 * @param int $taskId
	 * @return boolean
	 */
	public static  function getGiftFlag($uid, $taskId)
	{
		$key = 'ev:chrismas:getgift:flag:' . $taskId . '1225123' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$flag = $cache->get($key);
		
		if ($flag === false) {
			try {
				$db = Hapyfish2_Island_Event_Dal_Christmas::getDefaultInstance();
				$flag = $db->getGiftFlag($uid, $taskId);
			} catch (Exception $e) {}
		}
		
		return $flag;
	}
	
	/**
	 * @标记领取过礼物
	 * @param int $uid
	 */
	public static function addGiftFlag($uid, $taskId)
	{
		try {
			$db = Hapyfish2_Island_Event_Dal_Christmas::getDefaultInstance();
			$db->addGiftFlag($uid, $taskId);
		} catch (Exception $e) {}
		
		$key = 'ev:chrismas:getgift:flag:' . $taskId . '1225123' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, 1);
	}
	
	/**
	 * @获取公主数据
	 * @param int $id
	 * @return Array
	 */
	public static function getPrincessData($id)
	{
		$dataVo = array(array('id' => 1, 'cid' => '130732', 'list' => array('127241' => 6, '127341' => 6, '127641' => 6)),
						array('id' => 2, 'cid' => '130632', 'list' => array('127341' => 6, '127441' => 6, '127541' => 6)),
						array('id' => 3, 'cid' => '130832', 'list' => array('127241' => 6, '127341' => 6, '127441' => 6)),
						array('id' => 4, 'cid' => '131032', 'list' => array('127441' => 6, '127541' => 6, '127641' => 6)),
						array('id' => 5, 'cid' => '130532', 'list' => array('127241' => 6, '127541' => 6, '127641' => 6)));
						
		foreach ($dataVo as $data) {
			if ($id == $data['id']) {
				$princess = $data;
				break;
			}
		}
		
		return $princess;
	}
	
	public static function checkCard($uid)
	{
		$key = 'ev:joingamecard:get:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		
		return $data;
	}
	
	public static function addCard($uid)
	{
		//每天的23:59:59清空
		$logDate = date('Y-m-d');
		$dtDate = $logDate . ' 23:59:59';
		$endTime = strtotime($dtDate);
		
		$key = 'ev:joingamecard:get:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, 1, $endTime);
	}
}