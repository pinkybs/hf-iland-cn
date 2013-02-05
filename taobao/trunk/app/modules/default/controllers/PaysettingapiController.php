<?php

class PaysettingapiController extends Zend_Controller_Action
{
	function vaild()
	{
		
	}
	
	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}
		
		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
		}
		
		return $uid;
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
    
	public function itemlistAction()
	{
		$swfList = array(
			'mapbg.swf',
			'building1.swf',
			'building2.swf',
			'building3.swf',
			'building4.swf',
			'building5.swf',
			'building6.swf',
			'building7.swf',
			'building8.swf',
			'building9.swf',
			'building10.swf',
			'building11.swf',
			'building12.swf',
			'building13.swf',
			'building14.swf',
			'building15.swf',
			'building16.swf',
			'itemcard1.swf',
			'items1.swf',
			'island1.swf',
			'sky1.swf',
			'sea1.swf',
			'dock1.swf',
		);
		
		$defaultBgClass = 'mapBg1';
		
		$buildingList = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
		$data = array();
		
		foreach ($buildingList as $v) {
			$data[] = array(
				'cid' => (int)$v['cid'],
				'mapClass' => $v['class_name'],
				'values' => array($v['name']),
				'type' => 'building'
			);
		}
		
		$plantList = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
		foreach ($plantList as $v) {
			$data[] = array(
				'cid' => (int)$v['cid'],
				'mapClass' => $v['class_name'],
				'values' => array($v['name']),
				'type' => 'plant'
			);
		}
		
		$cardList = Hapyfish2_Island_Cache_BasicInfo::getCardList();
		foreach ($cardList as $v) {
			$data[] = array(
				'cid' => (int)$v['cid'],
				'mapClass' => $v['class_name'],
				'values' => array($v['name']),
				'type' => 'card'
			);
		}
		
		$backgoundList = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
		foreach ($backgoundList as $v) {
			$data[] = array(
				'cid' => (int)$v['bgid'],
				'mapClass' => $v['class_name'],
				'values' => array($v['name']),
				'type' => 'card'
			);
		}
		
		$result = array(
			'swfs' => $swfList,
			'bgClass' => $defaultBgClass,
			'images' => $data
		);
		echo json_encode($result);
		exit;
	}
}