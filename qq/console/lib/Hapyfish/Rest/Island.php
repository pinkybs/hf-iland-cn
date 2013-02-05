<?

require_once 'Hapyfish/Rest/Abstract.php';

class Hapyfish_Rest_Island extends Hapyfish_Rest_Abstract
{
    public function getUserInfo($uid)
    {
        return $this->call_method('openapi/userinfo', array('uid' => $uid));
    }
    
    public function getUserCardInfo($uid)
    {
        return $this->call_method('openapi/usercardinfo', array('uid' => $uid));
    }
    
    public function getWatchUser($uid)
    {
        return $this->call_method('openapi/watchuser', array('uid' => $uid));
    }
    
    public function getCoinLog($uid)
    {
        return $this->call_method('openapi/coinlog', array('uid' => $uid));
    }
    
    public function getInviteLog($uid)
    {
    	return $this->call_method('openapi/invitelog', array('uid' => $uid));
    }
    
    public function getNotice($type)
    {
    	return $this->call_method('manageapi/getnotice', array('type' => $type));
    }
    
    public function updateNotice($info)
    {
    	return $this->call_method('manageapi/updatenotice', $info);
    }

}