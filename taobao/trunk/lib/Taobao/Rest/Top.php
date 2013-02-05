<?

require_once 'Taobao/Rest/Abstract.php';

class Taobao_Rest_Top extends Taobao_Rest_Abstract
{
    public function user_get($fields = null, $nick = null)
    {
        $params = array();
        if ($fields) {
            $params['fields'] = $fields;
        }
        else {
            $params['fields'] = 'user_id,uid,nick,sex,buyer_credit,seller_credit,location,created,last_visit,birthday,type,status,alipay_no,alipay_bind,alipay_account,email,consumer_protection,vip_info';
        }
        if ($nick) {
            $params['nick'] = $nick;
        }
        return $this->call_method('taobao.user.get', $params);
    }

}