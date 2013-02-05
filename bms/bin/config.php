<?php

define('APP_DIR', ROOT_DIR . '/app');
define('CONFIG_DIR', APP_DIR . '/config');
define('LIB_DIR', ROOT_DIR . '/lib');
define('TMP_DIR', ROOT_DIR . '/tmp');
define('LOG_DIR', ROOT_DIR . '/logs');

ini_set('display_errors', false);

date_default_timezone_set('Asia/Shanghai');

set_include_path(LIB_DIR . PATH_SEPARATOR . get_include_path());

include 'Zend/Loader.php';
Zend_Loader::registerAutoload();

function err_log($msg)
{
	$logfile = LOG_DIR . '/err.bin.log';
	
	$time = date('Y-m-d H:i:s');
	
	file_put_contents($logfile, $time . "\t" . $msg . "\r\n", FILE_APPEND);
}

function debug_log($msg)
{
	$logfile = LOG_DIR . '/debug.bin.log';
	
	$time = date('Y-m-d H:i:s');
	
	file_put_contents($logfile, $time . "\t" . $msg . "\r\n", FILE_APPEND);
}

function info_log($msg, $prefix = 'default')
{
	$logfile = LOG_DIR . '/info.' . $prefix . '.bin.log';
	
	$time = date('Y-m-d H:i:s');
	
	file_put_contents($logfile, $time . "\t" . $msg . "\r\n", FILE_APPEND);
}
