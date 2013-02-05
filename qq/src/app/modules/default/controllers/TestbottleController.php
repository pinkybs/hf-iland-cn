<?php
class TestbottleController extends Zend_Controller_Action
{
	public function init()
    {
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }
    
    // 给玩家发送钥匙
    public function sendcardtouserAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$com = new Hapyfish2_Island_Bll_Compensation();
    	$com->setItem('86241', 10);
    	$com->sendOne($uid,'');
    	echo 'ok';
    	exit();
    }
    
    // 清除今天领奖缓存
    public function clearbottletodaytfAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$key = 'bottle:todaytf:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->delete($key);
		echo 'ok';
		exit();
    }
    
    // 重置掉宝箱缓存
    public function reloadbottleallAction()
   	{
   		$btl_id = $this->_request->getParam('btl_id');
   		
   		$hashkey = 'bottle:list';
		Hapyfish2_Island_Cache_Hash::reloadVal($hashkey);
		
		Hapyfish2_Island_Cache_Bottle::reloadAllByBottleId($btl_id);
		echo 'ok';
		exit();
    }
    
   	public function getbottleallcacheAction()
   	{
		$hashkey = 'bottle:list';
   		$btl_id = $this->_request->getParam('btl_id', 0);	// 哪季
   		$ids = $this->_request->getParam('ids');			// 
   		
   		$value = Hapyfish2_Island_Cache_Bottle::getAllByBottleId($btl_id);
   		
   		if ($btl_id || $btl_id == 0) {
   			
   			Zend_Debug::dump($value);
   			
   			echo 'ok';
   		} else {
   			echo 'no';
   		}
   		
   		
   		exit();
   	}
   	
   	
   	
   	public function prerandAction()
   	{
   		$hashkey = 'bottle:list';
   		//$btl_id = $this->_request->getParam('btl_id');
   		
   		
   		$val = Hapyfish2_Island_Cache_Hash::get($hashkey);
   		
   		
   		
   		echo 'ok';
   		exit();
   	}
   	
    
    public function getbottlehashAction()
    {
    	
    	echo 'ok';
    	exit();
    }
    
    public function getbottlehashmcAction()
    {
    	
    	echo 'ok';
    	exit();
    }
    
    public function getbottlehashdbAction()
    {
    	
    	echo 'ok';
    	exit();
    }

    
    
}
