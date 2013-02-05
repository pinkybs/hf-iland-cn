<?php

class Hapyfish2_Island_Bll_Payment
{
	public static function getPaymentCompare($platform, $begin, $end)
	{
		$data = array();
		try {
			$dalPayment = Hapyfish2_Island_Dal_Payment::getDefaultInstance();
			$dalPayment->setDbPrefix($platform);
			$data['newuser_count'] = $dalPayment->getNewPayUserCount($begin, $end);
			$data['newuser_amount'] = $dalPayment->getNewUserPayAmount($begin, $end);
			$data['totaluser_count'] = $dalPayment->getTotalPayUserCount($begin, $end);
			$data['totaluser_amount'] = $dalPayment->getTotalPayAmount($begin, $end);
		} catch (Exception $e) {
			
		}
		
		return $data;
	}

    public static function addPaylist($platform, $info)
    {
        if (empty($info)) {
            info_log($platform . ': no data : addPaylist', 'Hapyfish2_Island_Bll_Payment.addPaylist');
            return;
        }
        
        try {
            $dal = Hapyfish2_Island_Dal_Payment::getDefaultInstance();
            $dal->setDbPrefix($platform);
            $data = $dal->insertPaylist($info); 
            
        } catch (Exception $e) {
            info_log($e->getMessage(), 'bot.err');
        }
    }
    
    public static function getPayAmount($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_Payment::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getPaylist($day);
            if ($d) {
                $payAmunt = json_decode($d['amount_list']);
                $sortAry = array();
                foreach ($payAmunt as $k => $v) {
                    $sortAry[$k] = $v;
                }
                ksort($sortAry);
                $data = array();
                foreach ($sortAry as $i => $j) {
                	//level 为了统计图参数统计，本应 amount
                    $data[] = array('level' => (string)$i, 'count' => $j);
                }
            }
        } catch (Exception $e) {
        }
        
        return $data;
    }

    public static function getPayfirst($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_Payment::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getPaylist($day);
            if ($d) {
                $payArray = json_decode($d['first_pay_list']);
                $sortAry = array();
                foreach ($payArray as $k => $v) {
                    $sortAry[$k] = $v;
                }
                ksort($sortAry);
                $data = array();
                foreach ($sortAry as $i => $j) {
                    //level 为了统计图参数统计
                    $data[] = array('level' => (string)$i, 'count' => $j);
                }
            }
        } catch (Exception $e) {
        }
        return $data;
    }

    public static function getPayall($platform, $day)
    {
        $data = null;
        try {
            $dalLevel = Hapyfish2_Island_Dal_Payment::getDefaultInstance();
            $dalLevel->setDbPrefix($platform);
            $d = $dalLevel->getPaylist($day);
            if ($d) {
                $payArray = json_decode($d['all_pay_list']);
                $sortAry = array();
                foreach ($payArray as $k => $v) {
                    $sortAry[$k] = $v;
                }
                ksort($sortAry);
                $data = array();
                foreach ($sortAry as $i => $j) {
                    //level 为了统计图参数统计
                    $data[] = array('level' => (string)$i, 'count' => $j->count, 'amount' => $j->amount);
                }
            }
        } catch (Exception $e) {
        }
        
        return $data;
    }
}