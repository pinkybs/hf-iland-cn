<?php

class Hapyfish2_Island_Tool_FishAward
{
	public static function dumpInitIsland($uid, $gmuid = 134)
	{
		$userIsland = self::initCacheIsland($uid, $gmuid);
		$file =  TEMP_DIR . '/robots/'.$gmuid . '.cache';
		$data = json_encode($userIsland);
		file_put_contents($file, $data);
		return $data;
	}
	
	public static function getData()
	{
		$file =  TEMP_DIR . '/award.csv';
		$list = array();
		 $info=fopen($file,"r");
	        $q=0;
	        while ($data=fgetcsv ($info,10000,",")){
	        	$arr = array();
            	++$q;
	 	       if($q ===1)
	 	       {continue;}
	 	      for($i = 1;$i<=20;$i++){
	 	     	$arr1 = array();
	 	     	$rate = $data[3+$i];
	 	     	if($rate > 0){
	 	     		$arr1 = array((int)$data[1],(int)$data[2],(int)$data[3],(int)$rate);
	 	     		if($q<=17){
	 	     			$po = 0;
	 	     		}else if($q > 17 && $q <=30){
	 	     			$po = 1;
	 	     		}else if($q > 30 && $q <=43){
	 	     			$po = 2;
	 	     		}else if($q > 43 && $q <=56){
	 	     			$po = 3;
	 	     		}else {
	 	     			$po = 4;
	 	     		}
	 	     		$list[$i][$po][] = $arr1;
	 	     	}
	 	      }
         
          }
		fclose($info);
		return $list;
	}
	public static function saveDb()
	{
		$data = self::getData();
		$result = array();
		$arr = array();
		$dal = Hapyfish2_Island_Dal_FishCompound::getDefaultInstance();
		foreach($data as $k=>$v){
			foreach($v as $k1=>$v1){
				$result[$k][$k1] = json_encode($v1);
			}
			$dal->insertAward($k,$result[$k][0],$result[$k][1],$result[$k][2],$result[$k][3],$result[$k][4],$result[$k][5]);
		}
	}
}