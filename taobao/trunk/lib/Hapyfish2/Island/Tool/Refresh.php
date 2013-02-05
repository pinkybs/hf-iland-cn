<?php

class Hapyfish2_Island_Tool_Refresh
{
	public static $LIST = array(
		'notice' => 'manageapi/refreshnotice',
		'appinfo' => 'manageapi/refreshappinfo'
	);
	
	public static function all($name)
	{
		$result = array();
		if (isset(self::$LIST[$name])) {
			$list = Hapyfish2_Island_Bll_Server::getWebList();
			if (!empty($list)) {
				$host = str_replace('http://', '', HOST);
				foreach ($list as $server) {
					$url = 'http://' . $server['local_ip'] . '/' . self::$LIST[$name];
					$data = Hapyfish2_Island_Bll_Server::requestWeb($host, $url);
					$result[] = array('server_id' => $server['id'], 'result' => $data);
				}
			}
		}
		
		return $result;
	}
}