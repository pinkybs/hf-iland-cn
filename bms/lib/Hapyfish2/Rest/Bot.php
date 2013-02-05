<?

class Hapyfish2_Rest_Bot extends Hapyfish2_Rest_Abstract
{
    public function stat_maxuid()
    {
        return $this->call_method('staticsapi/maxuid');
    }

    public function stat_main($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/main', $params);
    }

    public function stat_retention($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/retention', $params);
    }

    public function stat_activeuserlevel($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/activeuserlevel', $params);
    }

    public function stat_payment($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/payment', $params);
    }

    public function stat_paymentofcal($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/paymentofcal', $params);
    }

    public function stat_mainhour($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/mainhour', $params);
    }

    public function stat_statmainhour($day = null)
    {
        $params = array();
        if ($day) {
        	$params['day'] = $day;
        }
    	return $this->call_method('staticsapi/statmainhour', $params);
    }
    
    public function stat_mainmonth($month = null)
    {
        $params = array();
        if ($month) {
            $params['month'] = $month;
        }
        return $this->call_method('staticsapi/mainmonth', $params);
    }

    public function stat_tutorial($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/tutorial', $params);
    }
    public function stat_sendgold($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/sendgold', $params);
    }
    public function stat_payclick($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/payclick', $params);
    }
    //商城道具排行榜
    public function stat_propsale($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/getpropsaledata', $params);
    }
    //所有用户等级分布
    public function stat_userlevel($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/userlevellist', $params);
    }
    //每日升级人数
    public function stat_levelup($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/levelup', $params);
    }

    //充值相关（额度分布，等级分布）
    public function stat_paylist($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/paylist', $params);
    }

    //七天未登录用户信息
    public function stat_lossuser($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/statlossuser', $params);
    }

    public function stat_donate($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/donatelist', $params);
    }
    
    //炼金-佣兵-雇佣、培养
    public function stat_mercenarymain($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/mercenarymain', $params);
    }
    
    //炼金-订单
    public function stat_ordermain($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/ordermain', $params);
    }
    
    //炼金-道具
    public function stat_itemmain($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/itemmain', $params);
    }
    
    //炼金-商店
    public function stat_shopmain($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/shopmain', $params);
    }
    
    //炼金-合成术
    public function stat_mixmain($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/mixmain', $params);
    }
    
    //战斗信息
	public function stat_fight($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/fightmain', $params);
    }
    //交互
	public function stat_mutual($day = null)
    {
        $params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/mutualmain', $params);
    }
    //修理
    public function stat_repair($day)
    {
    	$params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/repairmain', $params);
    }
    
    public function stat_upgrade($day)
    {
    	$params = array();
        if ($day) {
            $params['day'] = $day;
        }
        return $this->call_method('staticsapi/upgrade', $params);
    }
    
    public function stat_getfaq($params)
    {
    	return $this->call_method('staticsapi/getfaq', $params);
    }
    
	public function stat_getexportfaq($params)
    {
    	return $this->call_method('staticsapi/getexportfaq', $params);
    }
    
    public function stat_contrast($params)
    {
    	return $this->call_method('staticsapi/contrast', $params);
    }
    
}