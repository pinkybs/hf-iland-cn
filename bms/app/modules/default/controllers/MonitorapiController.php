<?php

class MonitorapiController extends Zend_Controller_Action
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
        $this->monitorSrc = 'http://114.80.224.89:88/munin';
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
    	echo 'Monitor API V1.0';
    	exit;
    }
	
	public function serverinfoAction()
	{
		$sid = $this->_request->getParam('sid');
		$server = Hapyfish2_Island_Bll_Monitor::getServerById($this->platform, $sid);
		$prefix = $this->monitorSrc . '/' . $server['area_name'] . '/' . $server['name'] . '.' . $server['area_name'];
		$t = time();
		$data = array(
			array('key' => 'Load Average', 'value' => array('day' => $prefix . '/load-day.png' . '?ts=' . $t, 'week' => $prefix . '/load-week.png' . '?ts=' . $t)),
			array('key' => 'CPU Usage', 'value' => array('day' => $prefix . '/cpu-day.png' . '?ts=' . $t, 'week' => $prefix . '/cpu-week.png' . '?ts=' . $t)),
			array('key' => 'eth1 traffic', 'value' => array('day' => $prefix . '/if_eth1-day.png' . '?ts=' . $t, 'week' => $prefix . '/if_eth1-week.png' . '?ts=' . $t)),
			array('key' => 'Memory Usage', 'value' => array('day' => $prefix . '/memory-day.png' . '?ts=' . $t, 'week' => $prefix . '/memory-week.png' . '?ts=' . $t)),
		);
		$this->echoResult(array('data' => $data));
	}
	
    public function updateappAction()
    {
        $appType = $this->_request->getParam('appType');
        $codeType = $this->_request->getParam('codeType');
        $platform = $this->_request->getParam('platform');
    
        try {
            //$info = Hapyfish2_Island_Bll_Day::getMainRange($platform, $begin, $end, true);
            //$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
            
        	if ( $appType == 1 ) {
        		if ( $codeType == 1 ) {
                    $appFile = './test_php.sh';
                    $codeName = '动态(PHP)';
        		}
        		else {
                    $appFile = './test_static.sh';
                    $codeName = '静态(PHP)';
        		}
                $appName = '测服';
        	}
        	else {
                if ( $codeType == 1 ) {
                    $appFile = './prd_php.sh';
                    $codeName = '动态(PHP)';
                }
                else {
                    $appFile = './prd_static.sh';
                    $codeName = '静态(PHP)';
                }
        		$appName = '正服';
        	}
        	
        	$system = 'cd /home/admin/bms_scripts/island/'.$platform.'; '.$appFile;
        	$updateApp = system($system, $updateResult);
        	
            $content = $appName.$codeName.'更新开始';
            
            $info = array('updateLastLine'=>$updateApp, 'updateResult'=>$updateResult);
            $result = array('data' => $info, 'error' => 0, 'content' => $content);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
    public function updateappresultAction()
    {
        $appType = $this->_request->getParam('appType');
        $codeType = $this->_request->getParam('codeType');
        $platform = $this->_request->getParam('platform');
    
        try {
            if ( $appType == 1 ) {
                if ( $codeType == 1 ) {
                    $appFile = './test_php.log';
                    $codeName = '动态(PHP)';
                }
                else {
                    $appFile = './test_static.log';
                    $codeName = '静态(PHP)';
                }
                $appName = '测服';
            }
            else {
                if ( $codeType == 1 ) {
                    $appFile = './prd_php.log';
                    $codeName = '动态(PHP)';
                }
                else {
                    $appFile = './prd_static.log';
                    $codeName = '静态(PHP)';
                }
                $appName = '正服';
            }
            
            $file = '/home/admin/bms_scripts/island/'.$platform.'/'.$appFile;
            //$system = 'cat /home/admin/bms_scripts/island/'.$platform.'/'.$appFile;
            //$updateApp = system($system, $updateResult);
            
            $fileContent = file_get_contents($file);
            $updateResult = explode("\n", $fileContent);
            
            $content = $appName.$codeName.'更新开始';
            
            $info = array('updateResult'=>$updateResult);
            $result = array('data' => $info, 'error' => 0, 'content' => $content);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }
    
	
}