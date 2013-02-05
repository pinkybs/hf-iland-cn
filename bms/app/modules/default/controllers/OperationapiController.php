<?php

class OperationapiController extends Zend_Controller_Action
{
    protected $cuid;
    
    protected $info;
	
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }
        
        $this->info = $info;
        $this->cuid = $info['uid'];
        $this->platform = $this->_request->getParam('platform');
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }
    
    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }
    
    public function indexAction()
    {
    	echo 'Operation API V1.0';
    	exit;
    }
    
	function sys_linux()
	{
		// CPU
		if (false === ($str = @file("/proc/cpuinfo"))) return false;
		print_r($str);
		$str = implode("", $str);
		@preg_match_all("/models+names{0,}:+s{0,}([ws)(@.]+)([rn]+)/s", $str, $model);
		@preg_match_all("/caches+sizes{0,}:+s{0,}([d.]+s{0,}[A-Z]+[rn]+)/", $str, $cache);
		if (false !== is_array($model[1])) {
		$res['cpu']['num'] = sizeof($model[1]);
		for($i = 0; $i < $res['cpu']['num']; $i++) {
		$res['cpu']['model'][] = $model[1][$i];
		$res['cpu']['cache'][] = $cache[1][$i];
		}
		if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
		if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
		}
		
		// UPTIME
		if (false === ($str = @file("/proc/uptime"))) return false;
		print_r($str);
		$str = explode(" ", implode("", $str));
		$str = trim($str[0]);
		$min = $str / 60;
		$hours = $min / 60;
		$days = floor($hours / 24);
		$hours = floor($hours - ($days * 24));
		$min = floor($min - ($days * 60 * 24) - ($hours * 60));
		if ($days !== 0) $res['uptime'] = $days."天";
		if ($hours !== 0) $res['uptime'] .= $hours."小时";
		$res['uptime'] .= $min."分钟";
		
		// MEMORY
		if (false === ($str = @file("/proc/meminfo"))) return false;
		print_r($str);
		$str = implode("", $str);
		preg_match_all("/MemTotals{0,}:+s{0,}([d.]+).+?MemFrees{0,}:+s{0,}([d.]+).+?SwapTotals{0,}:+s{0,}([d.]+).+?SwapFrees{0,}:+s{0,}([d.]+)/s", $str, $buf);
		
		$res['memTotal'] = round($buf[1][0]/1024, 2);
		$res['memFree'] = round($buf[2][0]/1024, 2);
		$res['memUsed'] = ($res['memTotal']-$res['memFree']);
		$res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
		
		$res['swapTotal'] = round($buf[3][0]/1024, 2);
		$res['swapFree'] = round($buf[4][0]/1024, 2);
		$res['swapUsed'] = ($res['swapTotal']-$res['swapFree']);
		$res['swapPercent'] = (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;
		
		// LOAD AVG
		if (false === ($str = @file("/proc/loadavg"))) return false;
		print_r($str);
		$str = explode(" ", implode("", $str));
		$str = array_chunk($str, 4);
		$res['loadAvg'] = implode(" ", $str[0]);
		
		return $res;
	}
	
	public function sysinfoAction()
	{
		$res = $this->sys_linux();
		$this->echoResult(array('data' => $res));
	}
	
	
}