<?php

class Hapyfish2_Island_Bll_Invite
{
	public static function refresh($uid, $list)
	{
		if (empty($list)) {
			return;
		}
		
		$num1 = count($list);
		$log = Hapyfish2_Island_Bll_InviteLog::getAll($uid);
		$all = true;
		if ($log) {
			$num2 = count($log);
			if ($num1 == $num2) {
				return;
			}
			$all = false;
			$tmp = array();
			foreach ($log as $f) {
				$tmp[$f['fid']] = $f['time'];
			}
		}
		
		$data = array();
		foreach ($list as $v) {
			$puid = $v['uid'];
			$time = $v['mtime'];
			$user = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
			if ($user) {
				$fid = $user['uid'];
				if ($all) {
					$data[$fid] = array('fid' => $fid, 'time' => strtotime($time));
				} else {
					if (!isset($tmp[$fid])) {
						$data[$fid] = array('fid' => $fid, 'time' => strtotime($time));
					}
				}
			}
		}

		if (!empty($data)) {
			foreach ($data as $user) {
				self::add($uid, $user['fid'], $user['time']);
			}
		}
	}
	
	public static function add($inviteUid, $newUid, $time = null)
	{
		if (!$time) {
			$time = time();
		}

		Hapyfish2_Island_Bll_InviteLog::add($inviteUid, $newUid, $time);
		Hapyfish2_Island_Bll_Fragments::updateInviteNum($inviteUid);
		//add 1000 coin
		Hapyfish2_Island_HFC_User::incUserCoin($inviteUid, 1000);
		$targetuser = Hapyfish2_Platform_Bll_User::getUser($newUid);
		
		//add card
//		$ok = Hapyfish2_Island_HFC_Card::addUserCard($inviteUid, 26341, 1);
		$ok = Hapyfish2_Island_Bll_StarFish::add($inviteUid,3,'');
		$title = '你成功邀请用户<font color="#379636">'.$targetuser['name'].'</font>，获得系统奖励<font color="#FF0000">1000金币</font>和 <font color="#9F01A0">3个海星</font>,赶快去海星商城看下吧！';
		if ($ok) {
			$feed = array(
						'uid' => $inviteUid,
						'actor' => $inviteUid,
						'target' => $newUid,
						'template_id' => 0,
//						'title' => array('cardName' => '加速卡II'),
						'title' => array('title' => $title),
						'type' => 3,
						'create_time' => $time
					);
			Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
		} else {
			info_log('[' . $inviteUid . ':' . $newUid, 'invite_failure');
		}
	}

}