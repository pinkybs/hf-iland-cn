<?php

class Hapyfish2_Island_Bll_Friend
{
	public static function getRankList($uid, $pageIndex = 1, $pageSize = 50)
	{

		$friendList = array();
		$friendList[] = array(
			'uid' => 134,
			'name' => '乐乐',
			'face' => STATIC_HOST . '/apps/island/images/lele2.jpg',
			'exp' => 999999999,
			'level' => 99,
			'activity' =>2,
			'canSteal' => 0
		);
		$robot = Hapyfish2_Island_Bll_Robot::getFriendList($uid);
		if($robot){
			foreach($robot as $k => $v){
				$friendList[] = $v;
			}
		}
		$fids = Hapyfish2_Platform_Bll_Factory::getFriendIds($uid);
		if (empty($fids)) {
			$fids = array($uid);
		} else {
			$fids[] = $uid;
		}

		foreach ($fids as $fid) {
			$userInfo = Hapyfish2_Island_HFC_User::getUser($fid, array('exp' => 1, 'level' => 1));
			if ($userInfo) {
				$info = Hapyfish2_Platform_Bll_Factory::getUser($fid);
				$Activity = Hapyfish2_Island_Bll_SearchFriend::getUserActivity($fid);
    			$faceUrl = $info['figureurl'];
                if (strpos($info['figureurl'], 'http://') === false) {
                    $faceUrl = 'http://' . $faceUrl;
                }
				$friendList[] = array(
					'uid' => $fid,
					'name' => $info['nickname'],
					'face' => $faceUrl,
					'exp' => $userInfo['exp'],
					'level' => $userInfo['level'],
					'activity' => $Activity,
					'canSteal' => 0
				);
			}
		}

		return array('friends' => $friendList, 'maxPage' => 1);
	}

}