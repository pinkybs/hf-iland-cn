<?php

define('ADMIN_USERNAME','admin'); 					// Admin Username
define('ADMIN_PASSWORD','yewushuang_920');  	// Admin Password

class MycatchfishController extends Zend_Controller_Action
{


	public function init()
	{
		// http 401 验证
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
           $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
			Header("WWW-Authenticate: Basic realm=\"Who is god of wealth, Login\"");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
		}

		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
	}

	public function productAction()
	{
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		$pid = $this->_request->getParam('pid');
		if($act == 'search') {
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$productInfo = $dalFish->getProductById($pid);

			$this->view->productinfo = $productInfo;
		}
		if($act == 'update') {
			$fields = array();
			$fields['name'] = trim($this->_request->getParam('name'));
			$fields['probability'] = trim($this->_request->getParam('probability'));
			$fields['picpath']= trim($this->_request->getParam('picpath'));
			$fields['url'] = trim($this->_request->getParam('url'));
			$fields['content'] = trim($this->_request->getParam('content'));
			$fields['flag'] = trim($this->_request->getParam('flag'));
			$fields['date'] = trim($this->_request->getParam('date'));
			
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$dalFish->updateProductById($pid, $fields);
			//清缓存
			$key = 'i:e:tb:pd';
			$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
			$cache->delete($key);
			$key = 'i:e:u:fishpinfo' .$pid;
			$cache->delete($key);
		}	
		$this->view->pid = $pid;
	}
	public function addproductAction()
	{
		$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		
		$maxPid = $dalFish->getMaxPid();
		$nextPid = $maxPid+1;
		$this->view->newpid = $nextPid;
		
		if($act == 'add') {
			
			$fields = array();
			$fields['pid'] = trim($this->_request->getParam('pid'));
			$fields['name'] = trim($this->_request->getParam('name'));
			$fields['probability'] = trim($this->_request->getParam('probability'));
			$fields['picpath']= trim($this->_request->getParam('picpath'));
			$fields['url'] = '';
			$fields['content'] = '';
			$fields['flag'] = 0;
			$fields['date'] = trim($this->_request->getParam('date'));
			
			if(!$fields['pid'] || !$fields['name'] || !$fields['probability'] || !$fields['picpath'] || !$fields['date']) {
				$this->showMessage('商品信息请填写完整!',HOST.'/mycatchfish/addproduct');
			}
			$nums = trim($this->_request->getParam('nums'));
			$pros1 = trim($this->_request->getParam('pros1'));
			$pros2 = trim($this->_request->getParam('pros2'));
			$pros3 = trim($this->_request->getParam('pros3'));
			$pros4 = trim($this->_request->getParam('pros4'));
			$pros5 = trim($this->_request->getParam('pros5'));
			
			$nums = str_replace("，", ",", $nums);
			$pros1 = str_replace("，", ",", $pros1);
			$pros2 = str_replace("，", ",", $pros2);
			$pros3 = str_replace("，", ",", $pros3);
			$pros4 = str_replace("，", ",", $pros4);
			$pros5 = str_replace("，", ",", $pros5);
			
			if(!$nums || !$pros1 || !$pros2 || !$pros3 || !$pros4 || !$pros5) {
				$this->showMessage('商品概率请填写完整!',HOST.'/mycatchfish/addproduct');
			}
			$count = $dalFish->checkProduct($fields['pid']);
			if($count) {
				$this->showMessage('已经添加过该商品了',HOST.'/mycatchfish/addproduct');
			}else {
				$insertId = $dalFish->addProduct($fields);
				if($insertId) {					
					//清缓存
					$key = 'i:e:tb:pd';
					$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
					$cache->delete($key);
					$key = 'i:e:u:fishpinfo' .$fields['pid'];
					$cache->delete($key);	

					$numArr = @explode(",",$nums);
					$prosArr1 = @explode(",",$pros1);
					$prosArr2 = @explode(",",$pros2);
					$prosArr3 = @explode(",",$pros3);
					$prosArr4 = @explode(",",$pros4);
					$prosArr5 = @explode(",",$pros5);
			
					for($i=0;$i<5;$i++) {
						$prosArr = array();
						if($i==0) {
							$prosArr = $prosArr1;
						}elseif($i==1) {
							$prosArr = $prosArr2;
						}elseif($i==2) {
							$prosArr = $prosArr3;
						}elseif($i==3) {
							$prosArr = $prosArr4;
						}elseif($i==4) {
							$prosArr = $prosArr5;
						}
						for($j=0;$j<=8;$j++) {
							$info = array();
							$info['pid'] = $fields['pid'];
							$info['discount'] = $j;
							$info['level'] = $i+1;	
							$info['num'] = $numArr[$j];
							$info['probability'] = $prosArr[$j];
							$info['urla'] = '';
							$info['urlb'] = '';
							$dalFish->addProbability($info);
						}
						$m = $i+1;
						$key = 'i:e:tb:pd:prob:l:pid:' . $m . ':' . $fields['pid'];	
						$cache->delete($key);				
					}
					$this->showMessage('添加成功',HOST.'/mycatchfish/addproduct');				
				}else {
					$this->showMessage('添加失败',HOST.'/mycatchfish/addproduct');
				}
			}
		}
	}
	public function probabilityAction()
	{
		$this->view->message = '';
		$this->view->hostUrl = HOST;
		$pid = $this->_request->getParam('pid');
		$act = $this->_request->getParam('act');
		if($act == 'search') {
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$list = $dalFish->getProbabilityById($pid);
			if($list) {
				foreach($list as $k=>$v) {
					$productInfo = $dalFish->getProductById($v['pid']);
					$list[$k]['pname'] = $productInfo['name'];
				}
			}
			$this->view->list = $list;
			$this->view->pid = $pid;
		}
		if($act == 'update') {
			$id = $this->_request->getParam('id');
			$probability = $this->_request->getParam('probability');
			$num = $this->_request->getParam('num');
			$urla = $this->_request->getParam('urla');
			$urlb = $this->_request->getParam('urlb');
			
			$count = count($id);
			for($i=0;$i<$count;$i++) {
				$fields = array();
				$iid = $id[$i];
				$fields['probability'] = trim($probability[$i]);
				$fields['num'] = trim($num[$i]);
				$fields['urla'] = trim($urla[$i]);
				$fields['urlb'] = trim($urlb[$i]);
				$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
				$dalFish->updateProbabilityById($iid, $fields);
			}
			//清缓存
			$pids =  $this->_request->getParam('pids');
			$productid = $pids[0];
			$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');	
			for($m=1;$m<=5;$m++) {
				$key = 'i:e:tb:pd:prob:l:pid:' . $m . ':' . $productid;
				$cache->delete($key);
			}
			$this->view->message = '操作成功';
			$this->view->pid = $productid;
		}
	}
	public function addprobabilityAction()
	{
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		if($act == 'add') {
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$fields = array();	
			$fields['pid'] = trim($this->_request->getParam('pid'));
			$fields['discount'] = trim($this->_request->getParam('discount'));
			$fields['level'] = trim($this->_request->getParam('level'));
			$fields['probability'] = trim($this->_request->getParam('probability'));
			$fields['num'] = trim($this->_request->getParam('num'));
			$fields['urla'] = trim($this->_request->getParam('urla'));
			$fields['urlb'] = trim($this->_request->getParam('urlb'));
			//检测是否已经添加过
			$count = $dalFish->checkProbability($fields['pid'], $fields['discount'], $fields['level']);
			if($count) {
				$this->view->msg = '已经添加过了';
			}else {	
				if($fields['pid'] && isset($fields['discount']) && $fields['level']) {
					$insertId = $dalFish->addProbability($fields);
					if($insertId) {					
						$this->view->msg = '添加成功.刚才添加的折扣数为<font color=blue><b>'.$fields['discount'].'</b></font>,领域位置为<font color=blue><b>'.$fields['level'].'</b></font>';
						//清缓存
						$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');	
						for($m=1;$m<=5;$m++) {
							$key = 'i:e:tb:pd:prob:l:pid:' . $m . ':' . $fields['pid'];
							$cache->delete($key);
						}											
					}else {
						$this->view->msg = '添加失败';	
					}	
				}else {
					$this->view->msg = '请填写完整';
				}			
			}			
		}		
	}
	public function statAction()
	{
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		$date = $this->_request->getParam('date');
		if($act == 'search') {
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$info = $dalFish->getStat($date);
			$this->view->info = $info;
		}
		$this->view->date = $date;
	}
	public function rankAction()
	{
		$this->view->hostUrl = HOST;
		$act = $this->_request->getParam('act');
		$date = $this->_request->getParam('date');
		if($act == 'search') {
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$list = $dalFish->getRank($date);
			$this->view->list = $list;
		}
		$this->view->date = $date;
	}
	public function checkplantAction()
	{
		$this->view->hostUrl = HOST;
		$itemId = $this->_request->getParam('itemId');
		$act = $this->_request->getParam('act');
		if($act == 'checkPlant') {
			$dalFish = Hapyfish2_Island_Event_Dal_CatchFish::getDefaultInstance();
			$isset = $dalFish->checkPlant($itemId);
			$msg = '该建筑不存在,请换其他建筑ID!';
			if($isset) {
				$msg = '该建筑存在!';
			}
			echo $msg;
			exit;
		}
		
	}	
	public function showMessage($content, $url)
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
		echo '<script>alert("'.$content.'");window.location.href="'.$url.'";</script>';
		exit;
	}		
}