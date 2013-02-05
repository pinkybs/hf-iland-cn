<?php

class VoteController extends Zend_Controller_Action
{
	function init()
    {
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    	
	public function doAction()
	{
		$url = 'http://zt.subaonet.com/2011/pgy/Vote.asp?id=74';
		$imgurl = 'http://zt.subaonet.com/2011/pgy/imgchk/validatecode.asp';
		$voteurl = 'http://zt.subaonet.com/2011/pgy/Vote_do.asp';

		/*
		$cookie_jar_index = TEMP_DIR . '/cookie.txt';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar_index);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
		//curl_setopt($ch, CURLOPT_NOBODY, 1);//这个不能打开，否则无法生成cookie文件
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$rs = ob_get_contents();
		ob_clean();
		
		

		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $imgurl);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookie_jar_index);
		ob_start();
		curl_exec($ch2);
		curl_close($ch2);
		$data= ob_get_contents();
		ob_clean();
		
		$code = new Hapyfish2_Bms_Bll_ValidateCode();
		$code->setImage($file);
		$code->getHec($data);
		$code->Draw();
		*/
		
		$client = new Zend_Http_Client();
		$client->setCookieJar(true);
		$client->setUri($url);
		$response = $client->request(Zend_Http_Client::GET);
		//$data = $response->getRawBody();
		
		$cookieJar = $client->getCookieJar();
		//print_r($cookieJar);
		$cookie = $cookieJar->getAllCookies();
		$checkCode = $cookie[0]->getValue();
		$showcheckCode = mb_convert_encoding($checkCode, 'utf-8', 'gbk');
		echo 'CheckCode:' . $showcheckCode . '<br/>';
		
		$client->setUri($imgurl);
		$response = $client->request(Zend_Http_Client::GET);
		$data = $response->getRawBody();
		//file_put_contents(DOC_DIR . '/static/code.bmp', $data);
		
		//echo '<img src="http://bms.happyfishgame.com/static/code.bmp?' . time() . '" /><br/>';
		
		$code = new Hapyfish2_Bms_Bll_ValidateCode();
		$code->setImage($imgurl);
		$code->getHec($data);
		//$code->Draw();
		
		$CheckKey = $code->run();
		echo 'CheckKey:' . $CheckKey . '<br/>';
		
		$pid = $this->_request->getParam('pid', '23');
		
		
		$client->setUri($voteurl);
		$client->resetParameters();
		//$cookieJar = $client->getCookieJar();
		//print_r($cookieJar);
		
		$postData = array(
			'ValidCode' => $checkCode,
			'CheckKey' => $CheckKey,
			'pid' => $pid
		);
		
		//print_r($postData);
		
		$ip = rand(110,162) . '.' . rand(125,178) . '.' . rand(1,254). '.' . rand(1,254);
		
		echo 'IP: ' . $ip . '<br/>';
		
		$ua = array(
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
			'Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-CN; rv:1.9.2.17) Gecko/20110420 Firefox/3.6.17',
			'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Maxthon; .NET CLR 1.1.4322)',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0)',
			'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
			'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)',
			'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; TencentTraveler 4.0; .NET CLR 2.0.50727)'
		);
		
		$uaid = rand(0, 6);
		if ($uaid > 6) {
			$uaid = 2;
		}
		
		echo 'User-Agent:' . $ua[$uaid] . '<br/>';
		
		$headers = array(
			'Host' => 'zt.subaonet.com',
			'User-Agent' => $ua[$uaid],
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language' => 'zh-cn,zh;q=0.5',
			'Accept-Charset' => 'GB2312,utf-8;q=0.7,*;q=0.7',
			'Referer' => $url,
			'X-Forwarded-For' => $ip
		);
		$client->setHeaders($headers);
		$client->setParameterPost($postData);
		$response = $client->request(Zend_Http_Client::POST);
		//$data = $response->getBody();
		//print_r($data);
		
		$t = rand(3, 5) * 1000;
		
		echo '<script>function a(){window.location.reload();} setInterval("a()", ' . $t . ');</script>';
		
		exit;
				
	}
	
	public function doviewAction()
	{
		$viewurl = 'http://zt.subaonet.com/2011/pgy/View.asp?id=74';
		$client = new Zend_Http_Client();
		$client->setUri($viewurl);
		$client->request(Zend_Http_Client::GET);
		$t = rand(3, 5) * 1000;
		echo '<script>function a(){window.location.reload();} setInterval("a()", ' . $t . ');</script>';
		exit;		
	}
	
}