<?php

class Hapyfish2_Island_Bll_Warehouse
{
	/**
	 * load one user's all items in warehouse
	 * @param integer $uid
	 * @return array $resultVo
	 */
	public static function loadItems($uid)
	{
		//get cards
		$lstCard = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		
		$cardVo = array();
		if ($lstCard) {
			foreach ($lstCard as $cid => $item) {
				if ( $item['count'] > 0 ) {
					$cardVo[] = array($cid, $cid, $item['count']);
				}
			}
		}

		//get buildings
		$lstBuilding = Hapyfish2_Island_HFC_Building::getInWareHouse($uid);
		$buildingVo = array();
		if ($lstBuilding) {
			foreach ($lstBuilding as $building) {
				$buildingVo[] = array($building['id'] . $building['item_type'] , $building['cid'], 1);
			}
		}

		//get plants
		$lstPlant = Hapyfish2_Island_HFC_Plant::getInWareHouse($uid);
		$plantVo = array();
		if ($lstPlant) {
			foreach ($lstPlant as $plant) {
				$plantVo[] = array($plant['id'] . $plant['item_type'] , $plant['cid'], 1, $plant['level']);
			}
		}

		//get background
		$lstBackground = Hapyfish2_Island_Cache_Background::getInWareHouse($uid);
		$backgroundVo = array();
		if ($lstBackground) {
			foreach ($lstBackground as $bg) {
				$backgroundVo[] = array($bg['id'] .  $bg['item_type'], $bg['bgid'], 1);
			}
		}
		
		//get compound
		$compoundList = Hapyfish2_Island_Cache_Compound::getUserbAm($uid);
		$compoundVo = array();
		if($compoundList){
			foreach($compoundList as $k => $v){
				foreach($v as $k1 => $v1){
					$compoundVo[] = array($v1['cid'], $v1['cid'], $v1['num']);
				}
			}
		}

		return array_merge($cardVo, $buildingVo, $plantVo, $backgroundVo, $compoundVo);

	}
}