<?php

class Hapyfish2_Island_Bll_Retention
{
	public static function add($platform, $info)
	{
		if (empty($info)) {
			info_log($platform . ': no data', 'Hapyfish2_Island_Bll_Retention.add');
			return;
		}
		
		try {
			$dalRetention = Hapyfish2_Island_Dal_Retention::getDefaultInstance();
			$dalRetention->setDbPrefix($platform);
			$info1 = array('log_time' => $info['log_time'], 'add_user' => $info['add_user']);
			$data = $dalRetention->insert($info1);
			$t = strtotime($info['log_time']);
			for($i = 1; $i <= 30; $i++) {
				$t -= 86400;
				$d = date('Ymd', $t);
				$ratentionDay = 'day_' . $i;
				$updateInfo = array($ratentionDay => $info[$ratentionDay]);
				try {
					$dalRetention->update($d, $updateInfo);
				} catch (Exception $e) {
					//echo $e->getMessage();
				}
			}
		} catch (Exception $e) {
			//echo $e->getMessage();
		}
	}

}