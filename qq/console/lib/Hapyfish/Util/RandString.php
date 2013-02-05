<?

class Hapyfish_Util_RandString
{
	public static function generate($len = 8, $format = 'ALL')
	{ 
		switch($format) {
			case 'ALL':
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
				break;
			case 'CHAR':
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
				break;
			case 'NUMBER':
				$chars = '0123456789';
				break;
			default:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
				break;
		}
		
		mt_srand((double)microtime()*1000000*getmypid()); 
		$password = '';
		$charLen = strlen($chars);
		for($i = 0; $i < $len; $i++) {
			$password .= substr($chars, (mt_rand()%$charLen), 1);
		}

		return $password;
	}
}