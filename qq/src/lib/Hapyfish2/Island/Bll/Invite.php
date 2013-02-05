<?php

class Hapyfish2_Island_Bll_Invite
{
	public static function add($inviteUid, $newUid)
	{
		Hapyfish2_Island_Bll_InviteLog::add($inviteUid, $newUid);

		//add 2000 coin
		Hapyfish2_Island_HFC_User::incUserCoin($inviteUid, 2000);
		$targetuser = Hapyfish2_Platform_Bll_Factory::getUser($newUid);
		Hapyfish2_Island_Bll_Fragments::updateInviteNum($inviteUid);
		//add card
//		$ok = Hapyfish2_Island_HFC_Card::addUserCard($inviteUid, 26341, 1);
		$ok = Hapyfish2_Island_Bll_StarFish::add($inviteUid,3,'');
		$title = '你成功邀请用户<font color="#379636">'.$targetuser['nickname'].'</font>，获得系统奖励<font color="#FF0000">2000金币</font>和 <font color="#9F01A0">3个海星</font>,赶快去海星商城看下吧！';
		if ($ok) {
			$feed = array(
				'uid' => $inviteUid,
				'actor' => $inviteUid,
				'target' => $newUid,
				'template_id' => 0,
//				'title' => array('cardName' => '加速卡II'),
				'title' => array('title' => $title),
				'type' => 3,
				'create_time' => time()
			);
			Hapyfish2_Island_Bll_Feed::insertMiniFeed($feed);
		} else {
			info_log('[' . $inviteUid . ':' . $newUid, 'invite_failure');
		}
	}

}