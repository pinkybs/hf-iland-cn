<?php

class Hapyfish2_Island_Bll_MoveData
{
	public static function getMoveData($params)
	{
		try {
			$dal = Hapyfish2_Island_Dal_MoveData::getDefaultInstance();
			$data = $dal->get($params);
		} catch (Exception $e) {}

		return $data;
	}

	public static function incMoveData($params)
	{
		$info = array('from_uid' => $params['old_uid'],
						'to_uid' => $params['uid'],
						'from_api' => $params['selectApi'],
						'to_api' => $params['platform'],
						'mtime' => time());
		
		try {
			$dal = Hapyfish2_Island_Dal_MoveData::getDefaultInstance();
			$dal->insert($info);
		} catch (Exception $e) {}
	}
	
}