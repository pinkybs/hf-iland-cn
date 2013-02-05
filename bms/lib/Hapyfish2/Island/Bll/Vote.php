<?php

class Hapyfish2_Island_Bll_Vote
{
	public static function done()
	{
		$pid = 74;
		$url = 'http://zt.subaonet.com/2011/pgytp/Vote.asp?id=74';
		$imgurl = 'http://zt.subaonet.com/2011/pgytp/imgchk/validatecode.asp';
		$voteurl = 'http://zt.subaonet.com/2011/pgytp/Vote_do.asp';
		
		$client = new Zend_Http_Client();
		$client->setCookieJar(true);
		$client->setUri($url);
		$response = $client->request(Zend_Http_Client::GET);
		
		$cookieJar = $client->getCookieJar();
		$cookie = $cookieJar->getAllCookies();
		$checkCode = $cookie[0]->getValue();
		
		if (empty($checkCode)) {
			info_log('checkCode empty', 'data.empty');
			return;
		}
		
		$showcheckCode = mb_convert_encoding($checkCode, 'utf-8', 'gbk');
		
		$client->setUri($imgurl);
		$response = $client->request(Zend_Http_Client::GET);
		$data = $response->getRawBody();
		
		if (empty($data)) {
			info_log('img data empty', 'data.empty');
			return;
		}
		
		$code = new Hapyfish2_Bms_Bll_ValidateCode();
		$code->setImage($imgurl);
		$code->getHec($data);
		
		$CheckKey = $code->run();
		
		if ($CheckKey === '') {
			info_log('CheckKey empty', 'data.empty');
			return;
		}
		
		$client->setUri($voteurl);
		$client->resetParameters();
		
		$postData = array(
			'ValidCode' => $checkCode,
			'CheckKey' => $CheckKey,
			'pid' => $pid
		);
		
		$ip = rand(110,189) . '.' . rand(125,189) . '.' . rand(1,254). '.' . rand(1,254);
		
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
		$client->request(Zend_Http_Client::POST);
	}
	
	public static function doview()
	{
		$viewurl = 'http://zt.subaonet.com/2011/pgytp/View.asp?id=74';
		$client = new Zend_Http_Client();
		$client->setUri($viewurl);
		$ip = rand(110,189) . '.' . rand(125,189) . '.' . rand(1,254). '.' . rand(1,254);
		
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
		
		$headers = array(
			'Host' => 'zt.subaonet.com',
			'User-Agent' => $ua[$uaid],
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language' => 'zh-cn,zh;q=0.5',
			'Accept-Charset' => 'GB2312,utf-8;q=0.7,*;q=0.7',
			'Referer' => 'http://zt.subaonet.com/2011/pgy/list.asp',
			'X-Forwarded-For' => $ip
		);
		$client->setHeaders($headers);
		$client->request(Zend_Http_Client::GET);
	}
	
}