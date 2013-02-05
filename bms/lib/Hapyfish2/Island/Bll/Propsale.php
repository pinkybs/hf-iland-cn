<?php

class Hapyfish2_Island_Bll_Propsale
{
	//$priceType:1,金币；2,宝石
	//$sortType:1,销售量排行；2,销售额排行
	public static function getPropsale($platform, $start, $end, $priceType = 1, $sortType = 1)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Island_Dal_Propsale::getDefaultInstance();
			$dal->setDbPrefix($platform);
			if ( $priceType == 1 ) {
				$list = $dal->getPropsaleCoinList($start, $end, $sortType);
				$count = $dal->getPropsaleCoinCount($start, $end, $sortType);
			}
			else {
				$list = $dal->getPropsaleGoldList($start, $end, $sortType);
                $count = $dal->getPropsaleGoldCount($start, $end, $sortType);
			}
		} catch (Exception $e) {

		}
		$data = array('list'=>$list, 'count'=>$count);
		return $data;
	}
	
    public static function add($platform, $info)
    {    	
        if (empty($info)) {
            info_log($platform . ': no data', 'Hapyfish2_Island_Bll_Propsale.add');
            return;
        }
        $data = $info[0];
        
        try {
            $dal = Hapyfish2_Island_Dal_Propsale::getDefaultInstance();
            $dal->setDbPrefix($platform);
            
            foreach($data as $k => $v){
                $newData = array(
                    'cid' => $v['cid'],
                    'date'=> $v['date'],
                    'num' => $v['num'],
                    'gold' => $v['gold'],
                    'coin' => $v['coin']
                );
                $dal->insert($newData);
            }
            
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
    }
    
}