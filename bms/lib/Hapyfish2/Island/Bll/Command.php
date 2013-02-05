<?php

class Hapyfish2_Island_Bll_Command
{
	const COMMAND_DIR = '/home/admin/website/bms/bin';
	
	public static function updatePHPSource4Test($platform, $cuid)
	{
		$command = self::COMMAND_DIR . '/test.sh';
		
		exec($command, $res, $rc);
		print_r($res);
		
		//异步执行
		//$cmd="command";
		//system("{$cmd} > /dev/null &");
		//passthru("/usr/bin/php /path/to/script.php ".$argv_parameter." >> /path/to/log_file.log 2>&1 &");
	}
	
}