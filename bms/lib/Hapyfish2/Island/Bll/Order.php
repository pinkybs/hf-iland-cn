<?php

class Hapyfish2_Island_Bll_Order
{
	public static function addOrderMain($platform, $data)
	{
		if (empty($data)) {
			info_log($platform . ': no data : addOrderMain', 'Hapyfish2_Island_Bll_Order.add');
			return;
		}
		
		$newInfo = array('log_time' 		=> $data['log_time'],
						 'accept_count' 	=> $data['accept_count'],
						 'complete_count' 	=> $data['complete_count'],
						 'fail_count' 		=> $data['fail_count'],
						 'refresh_count' 	=> $data['refresh_count'],
						 'add_coin' 		=> $data['add_coin']);
		try {
			$dal = Hapyfish2_Island_Dal_Order::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->insertOrderMain($newInfo); 
		} catch (Exception $e) {
			info_log($e->getMessage(), 'bot.err');
		}
	}

	public static function getRange($platform, $begin, $end)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Order::getDefaultInstance();
			$dal->setDbPrefix($platform);
			$data = $dal->getRange($begin, $end);
		} catch (Exception $e) {
		}
		
		return $data;
	}
	
}