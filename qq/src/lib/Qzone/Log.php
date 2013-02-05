<?php

class Qzone_Log
{
	protected $_logger;
	
	public  $logid;
	public $appid;
	public $iVersion;
	
	public $debug;
	public $logfile;

    protected static $_instance;

    public function __construct($logid, $appid)
    {
        $this->logid = $logid;
        $this->appid = $appid;
        $this->iVersion = 1;
        $this->debug = false;
        $this->_logger = new tmsglog_z(3, 'app');
    }

    /**
     * single instance of Qzone_Log
     *
     * @return Qzone_Log
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self('1000000013', APP_ID);
        }

        return self::$_instance;
    }
    
    public function setLogFile($file)
    {
    	$this->logfile = $file;
    	$this->debug = true;
    }
    
	public function saveLog($msg)
	{
		file_put_contents($this->logfile, $msg, FILE_APPEND);
	}
	
	public function getClient()
	{
		$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$intIp = 0;
		if ($ip) {
			$ipData = explode('.', $ip);
			if (count($ipData) == 4) {
				foreach ($ipData as $k => $v) {
					$intIp += (int)$v * pow(256, 3 - $k);
				}
			}
		}
		
		return $intIp;
	}
	
	public function report($uid, $info)
	{
		$intIp = $this->getClient();
		$operTime = time();
		if (!isset($info['coinChange'])) {
			$moneyChange = 0;
		} else {
			$moneyChange = $info['coinChange'];
		}
		if (!isset($info['expChange'])) {
			$expChange = 0;
		} else {
			$expChange = $info['expChange'];
		}
		$sessionKey = '';
		
		$ret = $this->_logger->netprintf($this->logid, $uid, "%d%d%d%d%d%d%d%s%d%d%d%d%s",
			$this->appid, $this->iVersion, $info['iSource'], $info['iCmd'], $info['iState'],
			$uid, $info['ownerUid'], $info['openid'], $intIp, $operTime, $moneyChange, $expChange, $sessionKey
		);
		
		//info_log($ret, 'report_result');
		
		if ($this->debug) {
			$msg = $uid . "\t" . $this->appid . "\t" . $this->iVersion . "\t" . $info['iSource'] . "\t"
				 . $info['iCmd'] . "\t" . $info['iState'] . "\t" . $uid . "\t" . $info['ownerUid'] . "\t"
				 . $info['openid'] . "\t" . $intIp . "\t" . $operTime . "\t" . $moneyChange . "\t" . $expChange
				 . "\t" . $sessionKey . "\n";
				 
			$this->saveLog($msg);
		}

	}

}