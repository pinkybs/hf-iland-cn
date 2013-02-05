<?php

class MailController extends Zend_Controller_Action
{
    protected $cuid;
    
    protected $info;
    
	private static $_config=array('auth'	=>	'',
								  'username'=>	'',
								  'password'=>	''
								  );
 	private static $_sender=array('name'	=>	'happyfish',
								  'email'	=>	'happyfish@happyfishgame.com'
								  );
	private static $_host = 'mail.happyfishgame.com';

	private static $_mailBody = array(
		1	=>	'<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style>table{font-size:13px;}</style></head><body>
				 <table width=500 align=center>
				 <tr><td>《快乐森林》全新改版啦！新的任务系统，新的铺路功能，不用在纠结没有好友的帮忙，就算1个人也能开心的玩到爽，更有各种全新的动物即将出现，老虎？狮子？统统OUT！凤凰，恐龙超时空动物闪亮登场？还在等什么，更多更萌的动物等你来宠，更有机会赢得4月四川熊猫基地游哦！赶紧来看看吧！</td></tr>
				 <tr><td><img src="http://gg.blueidea.com/2011/diandian/diandian_533_104_1.jpg"></td></tr>
				 <tr><td>礼包代码:xxxxxxxxxx</td></tr>
				 <tr><td>回归即可获得价值388元大礼包(<a href="http://www.baidu.com" target="_blank">点击领取</a>)</td></tr>
				 </table>
				 </body>
				 </html>'
	);
    function init()
    {
    	require_once 'Zend/Mail.php';
		require_once 'Zend/Mail/Transport/Smtp.php'; 
		   
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
    	echo 'Customer Tools API V1.0';
    	exit;
    }
     
    public function sendmailAction()
    {
    	$mailBody = self::$_mailBody;
    	$platforms = array("taobao", "weibo", "fb_taiwan", "fb_thailand", "ipanda_taobao");
    	$platform = $this->_request->getParam('platform');
    	if(!in_array($platform, $platforms)) {
    		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
			echo '<script>alert("该平台暂未开放此功能!");</script>';    		
    		exit;
    	}
    	$now     = date('Ymd');
    	$date    = date('Y-m-d');
    	$sender  = self::$_sender;
    	$host 	 = HOST;  
    	  	
    	$subject = $this->_request->getParam('subject');
    	$content = $this->_request->getParam('content');
    	
		$demoId = $this->_request->getParam('demoId');
		if($demoId) {
			$content = $mailBody[$demoId];
		}
    	$email = $this->_request->getParam('email');
    	$email = str_replace("，", ",", $email);
    	$emails = @explode(",", $email);
 		$count = count($emails);
 		$success = 0; 
 		$error = 0;	
		if($emails) {
			foreach($emails as $v) {
				$toMail = $v;
				$toName = $v;
				try {		
					$transport = new Zend_Mail_Transport_Smtp(self::$_host, self::$_config);
					$mail = new Zend_Mail("UTF-8");
					$mail->setBodyHtml($content);
					$mail->setFrom($sender['email'], $sender['name']);
					$mail->addTo($toMail, $toName);
					$mail->setSubject($subject.'('.$date.')');
					$mail->send($transport);
					$success ++;
					Hapyfish2_Island_Bll_Mail::updateMail($platform, $now, 1);
		    	}catch(Exception $e) {
		    		$msg = $e->getMessage();
		    		$error ++;
		    		Hapyfish2_Island_Bll_Mail::updateMail($platform, $now, 0);
		    		info_log(json_encode($msg));
		    	}				
			}
		}
		
    	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
		echo '<script>alert("已发送,成功('.$success.'),失败('.$error.')!");</script>';		
    	exit;
    }
}