<?


class Hapyfish2_Rest_Stat extends Hapyfish2_Rest_Abstract
{
    public function noop()
    {
        return $this->call_method('staticsapi/noop', array());
    }
    
    //
    
    public function main($day = null)
    {
    	$params = array();
    	if (!empty($day)) {
    		$params['day'] = $day;
    	}
    	
    	return $this->call_method('staticsapi/main', $params);
    }

}