<?php

class Hapyfish2_Island_Bll_PlantNum
{
	/**
	 * out island
	 * @param integer $uid
	 */
	public static function getNum()
	{
		$dal = Hapyfish2_Island_Dal_PlantNum::getDefaultInstance();
		$list863 = array();
		$list864 = array();
		$list865 = array();
		$list866 = array();
		$list867 = array();
		$list868 = array();
		$list869 = array();
		$list8631 = array();
		$list8641 = array();
		$list8651 = array();
		$list8661 = array();
		$list8671 = array();
		$list8681 = array();
		$list8691 = array();
		$list8611 = array();
		for($i=0;$i<=23;$i++){
			for($j=0;$j<=49;$j++){
				$list = $dal->getnum($i,$j,86332);
				$list1 = $dal->getnum($i,$j,86432);
				$list2 = $dal->getnum($i,$j,86532);
				$list3 = $dal->getnum($i,$j,86632);
				$list4 = $dal->getnum($i,$j,86732);
				$list5 = $dal->getnum($i,$j,86832);
				$list6 = $dal->getnum($i,$j,86932);	
				$list863 = array_merge($list863,$list);
				$list864 = array_merge($list864,$list1);
				$list865 = array_merge($list865,$list2);
				$list866 = array_merge($list866,$list3);
				$list867 = array_merge($list867,$list4);
				$list868 = array_merge($list868,$list5);
				$list869 = array_merge($list869,$list6);
			}
		}
		$list8611 = array_merge($list8611,$list867,$list868,$list869);
		
		$num863 = count($list863);
		$num864 = count($list864);
		$num865 = count($list865);
		$num866 = count($list866);
		$num867 = count($list867);
		$num868 = count($list868);
		$num869 = count($list869);
		for($i=0;$i<count($list863);$i++){
        	$source=$list863[$i];
         	if(array_search($source,$list863)==$i){
                $list86311[]=$source;
         	}
		}
		foreach($list86311 as $k=>$v){
			$all[$v] = 1;
		}
		for($i=0;$i<count($list864);$i++){
        	$source=$list864[$i];
         	if(array_search($source,$list864)==$i){
                $list86411[]=$source;
         	}
		}
		foreach($list86411 as $k1=>$v1){
			if(isset($all[$v1])){
				$all[$v1] +=1;
			}else{
				$all[$v1] =1;
			}
		}
		for($i=0;$i<count($list865);$i++){
        	$source=$list865[$i];
         	if(array_search($source,$list865)==$i){
                $list86511[]=$source;
         	}
		}
		foreach($list86511 as $k2=>$v2){
			if(isset($all[$v2])){
				$all[$v2] +=1;
			}else{
				$all[$v2] =1;
			}
		}
		for($i=0;$i<count($list866);$i++){
        	$source=$list866[$i];
         	if(array_search($source,$list866)==$i){
                $list86611[]=$source;
         	}
		}
		foreach($list86611 as $k3=>$v3){
			if(isset($all[$v3])){
				$all[$v3] +=1;
			}else{
				$all[$v3] =1;
			}
		}
		for($i=0;$i<count($list8611);$i++){
        	$source=$list8611[$i];
         	if(array_search($source,$list8611)==$i){
                $result[]=$source;
         	}
		}
         foreach($result as $k5=>$v5){
         if(isset($all[$v5])){
				$all[$v5] +=1;
			}else{
				$all[$v5] =1;
			}
         }
         $results['num3'] = 0;
         $results['num4'] = 0;
         $results['num5'] = 0;
         foreach($all as $k=>$v){
			if($v ==3 ){
				$results['num3']+=1;
			}
			if($v ==4 ){
				$results['num4']+=1;
			}
			if($v ==5 ){
				$results['num5']+=1;
			}
         }
         $resultr['num863'] = $num863;
         $resultr['num864'] = $num864;
         $resultr['num865'] = $num865;
         $resultr['num866'] = $num866;
         $resultr['num867'] = $num867;
         $resultr['num868'] = $num868;
         $resultr['num869'] = $num869;
         $resultr['result'] = $results;
         $resultr = json_encode($resultr);
         return $resultr;
	}
	
	public static function  dumprobot()
	{
		$list = array(96240,169200,193920,278880,545520,1317840,3589680,7704,19224,82824,118104,198744,405624,1403304,2655864,12048,23808,49728,54048,70128,214368,458688,565488,678528,1334208,1420368,1566288,1614048,1992768,4872,4081,153841,381841,406081,657361,2949841,25225,36985,56665,75385,686185,1372345,1805065,30289,37249,236689,325729,24073,75673,776713,1067593,3783433,100177,121297,190657,308977,464737,999697,1076497,37561,482,3602,95042,250082,728642,1644482,2083682,746,40346,52346,98426,286826,1949786,3093626,19730,291890,309410,134234,651674,1923194,2101034,53858,103778,149858,1693058,1322,6122,6842,114122,148442,117363,844323,1488723,2764083,13467,77067,122187,148827,217947,1582587,2263947,104691,1196451,8955,231195,2255115,2459595,23619,32979,67779,145059,501699,1652979,19803,39963,64923,65643,178443,360363,1071723,189844,194884,302164,899524,1708,3628,8668,250108,734908,1662988,3027388,1492,4852,30532,87412,17836,20476,32476,95116,98476,112156,123196,348796,473116,478876,534316,1058956,1070476,2980,33460,11525,76085,82325,94805,109445,492245,1060805,10349,11309,129389,300509,495149,561389,642749,1848269,34373,50693,60773,76613,170213,506213,1728293,153677,232157,1553837,14741,22421,140741,299141,34925,13686,17766,65526,67446,515526,696006,56910,213150,336510,502110,1092030,1973790,2254830,7734,8214,23814,47814,49494,54294,79494,1158054,1238934,1762134,37758,45198,58878,276798,718878,2045838,19782,7207,22087,140407,238567,309847,79231,94111,115951,378991,900031,53335,78295,87655,216055,493255,854455,863815,1375255,2409415,11359,103519,249199,4313119,176503,620023,705223,1485463,282607,956287,1973647,34088,140408,190328,191768,421928,2178728,2432,11312,30752,292112,322112,2412992,34616,207176,2363096,2774696,14000,14960,20480,104000,139520,376160,421040,434720,652160,656480,769040,1544,4184,13304,2649,33369,52569,348009,538329,789849,2071689,4593,5073,92193,145233,313713,1174593,1945473,7737,515337,2503977,1521,21681,25761,69441,464001,722241,2505,11625,83865,209385,369945,842025,999705,17290,147130,166330,317770,1048570,1114570,1274890,1952890,6274,7474,58354,102274,490594,11098,169738,16162,70162,92002,627682,901282,1852642,2743042,83146,202186,236266,323626,361546,531226,3490,50530,27611,405611,1523771,1983851,2172251,3635,31955,42755,90755,117635,317555,374195,699155,884915,1633475,2252915,4068275,33899,118859,141899,398459,1903979,2197499,189443,225923,279203,506243,956003,65147,72107,159852,84516,97716,114516,232356,593796,2048676,2137956,3050916,103500,256620,19764,23124,46164,68724,137844,219684,698724,880644,349308,502668,1904748,43092,46692,130932,167172,635412,697092,1899492,2110452,3373,6493,74893,129133,266893,381853,543133,49477,83797,209077,287557,1042837,2070277,3421,39661,96541,104701,378781,537901,2761501,21445,52885,66805,143125,1312165,23869,24829,50509,114109,166189,11294,15374,15854,31694,114014,114494,118334,121454,626414,956654,1482494,2193134,129398,821318,920438,2728358,3045638,112862,269822,414782,538142,1077422,1227902,326,87446,104726,730406,3390086,87710,94190,50895,208095,243615,1387455,2439,6279,47799,60999,145719,146199,444039,1114119,1217319,1455879,1628439,46383,113583,174783,1113663,2181663,2838783,6567,35367,96087,13311,85551,178911,440991,584271,647151,12736,42496,259696,654496,742816,1694176,2440,18760,43240,68680,75640,248440,307960,323800,453640,636520,1781560,1867720,2083000,3697720,1024,1264,84304,356944,516544,568,4168,20008,118168,161848,1697,5777,17057,21617,796097,23081,26201,30521,69161,105881,117401,780041,29345,40865,1211825,1547585,58409,194009,389369,824969,905129,1430729,53393,88913,305633,427073,876593,156137,173657,1425977,31218,40818,178578,647058,647778,72762,97002,628122,4344522,3186,145746,510066,1510866,42090,232170,632010,1450650,4434,33714,69954,623634,846834,1280754,2869074,7578,39738,1276698,12882,55602,58002,129139,231379,287299,698419,1087939,763,103243,145723,696763,806203,977803,2138683,2785483,3423883,12547,17827,193987,213907,2682787,3259507,164251,256651,899131,2284171,4146331,4675,8995,1339795,78379,90859,49700,151460,213860,552500,989060,1046420,1637060,2444,6284,132764,265004,461564,1497164,548,188948,247508,1625348,1832468,2196788,2278628,2542628,24812,294572,983372,11636,51716,199796,1598756,6140,39020,10581,160821,186021,764421,5325,48045,111885,119325,375165,749085,869325,1127565,40149,55989,101829,388389,402549,619749,666069,739269,1061109,1233669,1391829,2043909,2253,33933,34653,238173,300093,501213,24982,272422,489382,2096662,2206,6046,12526,78526,94846,122206,123406,1008526,1347646,2143486,43990,50950,246790,334870,346150,413590,456790,510070,2286310,1534,5854,30094,92494,133294,553294,1539454,3143,16343,18263,47063,143063,148823,154103,273863,4847,91727,112847,488447,77111,496151,794711,1581191,2683991,19535,67055,76655,140495,170495,448655,529295,1437935,1509215,2190575,2999,40439,144359);
		$listkey = array_rand($list,500);
		foreach($listkey as $k => $v){
			$uidlist[] = $list[$v];
		}
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$key = 'robot:u:list1';
		$lists = $cache->get($key);
		if($lists === false){
			for($i=1;$i<=500;$i++){
				$lists[$i] = 's'.$i; 
			}
			$cache->set($key,$lists);
		}
		foreach($uidlist as $k1 => $v1){
			if(count($lists)>1){
				$gidk = array_rand($lists,1);
			}else{
				$gidk = 0;
			}
			
			$gid = $lists[$gidk];
			unset($lists[$gidk]);
			$cache->set($key,$lists);
			Hapyfish2_Island_Tool_Robot::dumpInitIsland($v1, $gid);
		}
	}

}