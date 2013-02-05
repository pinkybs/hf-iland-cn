<?php

class Hapyfish_Island_Customer_Bll_Auth
{
	public static function login($name, $pwd)
	{
		include_once CONFIG_DIR . '/account.php';
		if (isset($ACCOUNT_LIST[$name])) {
			$user = $ACCOUNT_LIST[$name];
			$chkPwd = md5($pwd. ':' . BASE_SECRET);
			if ($user['pwd'] == $chkPwd) {
				return $user;
			} else {
				return null;
			}
		}
		
		return null;
	}

}