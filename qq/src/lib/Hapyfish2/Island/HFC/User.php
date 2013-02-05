<?php

class Hapyfish2_Island_HFC_User
{
	public static function getUserVO($uid)
	{
		$keys = array(
			'i:u:exp:' . $uid,
			'i:u:coin:' . $uid,
			'i:u:level:' . $uid,
			'i:u:island:' . $uid,
			'i:u:title:' . $uid,
			'i:u:cardstatus:' . $uid
		);

		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->getMulti($keys);

		if ($data === false) {
			return null;
		}

		$userVO = array('uid' => $uid);

		$userExp = $data[$keys[0]];
		if ($userExp === null) {
			try {
			    $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $userExp = $dalUser->getExp($uid);
	            $cache->add($keys[0], $userExp);
			} catch (Exception $e) {
				return null;
			}
		}
		if (!$userExp) {
			$userExp = 0;
		}
		$userVO['exp'] = $userExp;

		$userCoin = $data[$keys[1]];
		if ($userCoin === null) {
			try {
			    $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $userCoin = $dalUser->getCoin($uid);
	            $cache->add($keys[1], $userCoin);
			} catch (Exception $e) {
				return null;
			}
		}
		if (!$userCoin) {
			$userCoin = 0;
		}
		$userVO['coin'] = $userCoin;

		$userVO['gold'] = -1;

		$userLevel = $data[$keys[2]];
		if ($userLevel === null) {
			try {
			    $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $userLevel = $dalUser->getLevel($uid);
	            if ($userLevel) {
	            	$cache->add($keys[2], $userLevel);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userVO['level'] = $userLevel[0];
		$userVO['island_level'] = $userLevel[1];
		$userVO['island_level_2'] = isset($userLevel[2]) ? $userLevel[2] : 0;
		$userVO['island_level_3'] = isset($userLevel[3]) ? $userLevel[3] : 0;
		$userVO['island_level_4'] = isset($userLevel[4]) ? $userLevel[4] : 0;

		$userIsland = $data[$keys[3]];
		if ($userIsland === null) {
			try {
			    $dalUserIsland = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
	            $userIsland = $dalUserIsland->get($uid);
	            if ($userIsland) {
	            	$cache->add($keys[3], $userIsland);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userVO['position_count'] = $userIsland[1];

		//扩岛功能
		$userVO['current_island'] = isset($userIsland[34]) ? $userIsland[34] : 1;
		$unlockIsland = isset($userIsland[35]) ? $userIsland[35] : '1';

		//get unlock island info,0:未解锁,1:已解锁
		$desertIslandState = 0;
		$hawaiiIslandState = 0;
		$iceLandState = 0;
		$tmp = split(',', $unlockIsland);
		foreach ( $tmp as $id ) {
			switch ( $id ) {
				case 2 :
					$desertIslandState = 1;
					break;
				case 3 :
					$hawaiiIslandState = 1;
					break;
				case 4 :
					$iceLandState = 1;
					break;
			}
		}
		$userVO['desertIslandState'] = $desertIslandState;
		$userVO['hawaiiIslandState'] = $hawaiiIslandState;
		$userVO['iceLandState'] = $iceLandState;
		$userVO['unlockIsland'] = $unlockIsland;

		//get bg info
		//if ( $userVO['current_island'] == 1 ) {
			$userVO['praise'] = $userIsland[0];
			$userVO['bg_island'] = $userIsland[2];
			$userVO['bg_island_id'] = $userIsland[3];
			$userVO['bg_sky'] = $userIsland[4];
			$userVO['bg_sky_id'] = $userIsland[5];
			$userVO['bg_sea'] = $userIsland[6];
			$userVO['bg_sea_id'] = $userIsland[7];
			$userVO['bg_dock'] = $userIsland[8];
			$userVO['bg_dock_id'] = $userIsland[9];
		//}
		//else if ( $userVO['current_island'] == 2 ) {
			$userVO['praise_2'] = $userIsland[36];
			$userVO['bg_island_2'] = $userIsland[10];
			$userVO['bg_island_id_2'] = $userIsland[11];
			$userVO['bg_sky_2'] = $userIsland[12];
			$userVO['bg_sky_id_2'] = $userIsland[13];
			$userVO['bg_sea_2'] = $userIsland[14];
			$userVO['bg_sea_id_2'] = $userIsland[15];
			//$userVO['bg_dock_2'] = $userIsland[16];
			//$userVO['bg_dock_id_2'] = $userIsland[17];
		//}
		//else if ( $userVO['current_island'] == 3 ) {
			$userVO['praise_3'] = $userIsland[37];
			$userVO['bg_island_3'] = $userIsland[18];
			$userVO['bg_island_id_3'] = $userIsland[19];
			$userVO['bg_sky_3'] = $userIsland[20];
			$userVO['bg_sky_id_3'] = $userIsland[21];
			$userVO['bg_sea_3'] = $userIsland[22];
			$userVO['bg_sea_id_3'] = $userIsland[23];
			//$userVO['bg_dock_3'] = $userIsland[24];
			//$userVO['bg_dock_id_3'] = $userIsland[25];
		//}
		//else if ( $userVO['current_island'] == 4 ) {
			$userVO['praise_4'] = $userIsland[38];
			$userVO['bg_island_4'] = $userIsland[26];
			$userVO['bg_island_id_4'] = $userIsland[27];
			$userVO['bg_sky_4'] = $userIsland[28];
			$userVO['bg_sky_id_4'] = $userIsland[29];
			$userVO['bg_sea_4'] = $userIsland[30];
			$userVO['bg_sea_id_4'] = $userIsland[31];
			//$userVO['bg_dock_4'] = $userIsland[32];
			//$userVO['bg_dock_id_4'] = $userIsland[33];
		//}

        $userTitle = $data[$keys[4]];
		if($userTitle === null) {
			try {
			    $dalUserTitle= Hapyfish2_Island_Dal_UserTitle::getDefaultInstance();
	            $userTitle = $dalUserTitle->get($uid);
	            if ($userTitle) {
	            	$cache->add($keys[4], $userTitle);
	            } else {
	            	return null;
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$userVO['title'] = $userTitle[0];
		$userVO['title_list'] = $userTitle[1];

        $cardStatus = $data[$keys[5]];
		if($cardStatus === null) {
			try {
			    $dalCardStatus= Hapyfish2_Island_Dal_CardStatus::getDefaultInstance();
	            $cardStatus = $dalCardStatus->get($uid);
	            if ($cardStatus) {
	            	$cache->add($keys[5], $cardStatus);
	            } else {
	            	return null;
	            }
			} catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$cardStatus[5] = isset($cardStatus[5]) ? $cardStatus[5] : 0;
		$cardStatus[6] = isset($cardStatus[6]) ? $cardStatus[6] : 0;
		$cardStatus[8] = isset($cardStatus[8]) ? $cardStatus[8] : 0;
		$cardStatus[9] = isset($cardStatus[9]) ? $cardStatus[9] : 0;
		$userVO['insurance'] = $cardStatus[0];
		$userVO['defense'] = $cardStatus[1];
		$userVO['onekey'] = $cardStatus[5];
		$userVO['doubelexp'] = $cardStatus[6];
		$userVO['mammon'] = $cardStatus[8];
		$userVO['poor'] = $cardStatus[9];

		$userVO['next_level_exp'] = Hapyfish2_Island_Cache_BasicInfo::getUserLevelExp($userVO['level'] + 1);

        return $userVO;
	}

	public static function getUser($uid, $fields)
    {
    	$keys = array();
    	$getExp = false;
    	$getCoin = false;
    	$getLevel = false;
    	if (isset($fields['exp'])) {
    		$keyExp = 'i:u:exp:' . $uid;
    		$keys[] = $keyExp;
    		$getExp = true;
    	}

    	if (isset($fields['coin'])) {
    		$keyCoin = 'i:u:coin:' . $uid;
    		$keys[] = $keyCoin;
    		$getCoin = true;
    	}

		if (isset($fields['level'])) {
			$keyLevel = 'i:u:level:' . $uid;
    		$keys[] = $keyLevel;
    		$getLevel = true;
    	}

    	if (empty($keys)) {
    		return null;
    	}

    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->getMulti($keys);
		if ($data === false) {
			return null;
		}

		$user = array('uid' => $uid);

		if ($getExp) {
			$userExp = $data[$keyExp];
			if ($userExp === null) {
				try {
				    $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
		            $userExp = $dalUser->getExp($uid);
		            $cache->add($keyExp, $userExp);
				} catch (Exception $e) {
					return null;
				}
			}
			if (!$userExp) {
				$userExp = 0;
			}
			$user['exp'] = $userExp;
		}

		if ($getCoin) {
			$userCoin = $data[$keyCoin];
			if ($userCoin === null) {
				try {
				    $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
		            $userCoin = $dalUser->getCoin($uid);
		            $cache->add($keyCoin, $userCoin);
				} catch (Exception $e) {
					return null;
				}
			}
			if (!$userCoin) {
				$userCoin = 0;
			}
			$user['coin'] = $userCoin;
		}

		if ($getLevel) {
			$userLevel = $data[$keyLevel];
			if ($userLevel === null) {
				try {
				    $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
		            $userLevel = $dalUser->getLevel($uid);
		            if ($userLevel) {
		            	$cache->add($keyLevel, $userLevel);
		            } else {
		            	return null;
		            }
				} catch (Exception $e) {
					return null;
				}
			}
			$user['level'] = $userLevel[0];
			$user['island_level'] = $userLevel[1];
			$user['island_level_2'] = isset($userLevel[2]) ? $userLevel[2] : 1;
			$user['island_level_3'] = isset($userLevel[3]) ? $userLevel[3] : 1;
			$user['island_level_4'] = isset($userLevel[4]) ? $userLevel[4] : 1;
		}

        return $user;
    }

    public static function getUserExp($uid)
    {
        $key = 'i:u:exp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$exp = $cache->get($key);
        if ($exp === false) {
        	try {
	            $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $exp = $dalUser->getExp($uid);
	            $cache->add($key, $exp);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $exp;
    }

    public static function getUserCoin($uid)
    {
        $key = 'i:u:coin:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$coin = $cache->get($key);
        if ($coin === false) {
        	try {
	            $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $coin = $dalUser->getCoin($uid);
	            $cache->add($key, $coin);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $coin;
    }

    public static function getUserGold($uid)
    {
        return 0;
    }

    public static function getUserStarFish($uid)
    {
        $key = 'i:u:starfish:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$starfish = $cache->get($key);
        if ($starfish === false) {
        	try {
	            $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $starfish = $dalUser->getStarFish($uid);
	            $cache->add($key, $starfish);
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $starfish;
    }

    public static function getUserLevel($uid)
    {
        $key = 'i:u:level:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $data = $dalUser->getLevel($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        $data[2] = isset($data[2]) ? $data[2] : 1;
        $data[3] = isset($data[3]) ? $data[3] : 1;
        $data[4] = isset($data[4]) ? $data[4] : 1;
        return array('level' => $data[0], 'island_level' => $data[1], 'island_level_2' => $data[2], 'island_level_3' => $data[3], 'island_level_4' => $data[4]);
    }

    public static function updateUserExp($uid, $userExp, $savedb = true)
    {
		$key = 'i:u:exp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $userExp);
        	if ($ok) {
        		try {
        			$info = array('exp' => $userExp);
        			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $userExp);
        }
    }

    public static function incUserExp($uid, $expChange)
    {
    	if ($expChange <= 0) {
    		return false;
    	}

    	$userExp = self::getUserExp($uid);
    	if ($userExp === null) {
    		return false;
    	}

    	$userExp += $expChange;
    	
    	$savedb = false;
    	if ( $expChange > 10 ) {
    		$savedb = true;
    	}

    	return self::updateUserExp($uid, $userExp, $savedb);
    }

    public static function updateUserCoin($uid, $userCoin, $savedb = false)
    {
		$key = 'i:u:coin:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $userCoin);
        	if ($ok) {
        		try {
        			$info = array('coin' => $userCoin);
        			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $userCoin);
        }
    }

    public static function incUserCoin($uid, $coinChange, $savedb = false)
    {
    	if ($coinChange <= 0) {
    		return false;
    	}

    	$userCoin = self::getUserCoin($uid);
    	if ($userCoin === null) {
    		return false;
    	}

    	$userCoin += $coinChange;
    	
    	if ( $coinChange > 100 ) {
    		$savedb = true;
    	}

    	return self::updateUserCoin($uid, $userCoin, $savedb);
    }

    public static function decUserCoin($uid, $coinChange, $savedb = false)
    {
    	if ($coinChange <= 0) {
    		return false;
    	}

    	$userCoin = self::getUserCoin($uid);
    	if ($userCoin === null) {
    		return false;
    	}

    	if ($userCoin < $coinChange) {
    		return false;
    	}

    	$userCoin -= $coinChange;

    	if ( $coinChange > 100 ) {
    		$savedb = true;
    	}
    	
    	return self::updateUserCoin($uid, $userCoin, $savedb);
    }

    public static function incUserExpAndCoin($uid, $expChange, $coinChange)
    {
    	self::incUserExp($uid, $expChange);
    	self::incUserCoin($uid, $coinChange);
    }

    public static function updateUserStarFish($uid, $userStarFish, $savedb = false)
    {
		$key = 'i:u:starfish:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $userStarFish);
        	if ($ok) {
        		try {
        			$info = array('starfish' => $userStarFish);
        			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $userStarFish);
        }
    }

    public static function incUserStarFish($uid, $starChange, $savedb = false)
    {
    	if ($starChange <= 0) {
    		return false;
    	}

    	$userStarFish = self::getUserStarFish($uid);
    	if ($userStarFish === null) {
    		return false;
    	}

    	$userStarFish += $starChange;

    	return self::updateUserStarFish($uid, $userStarFish, $savedb);
    }

    public static function decUserStarFish($uid, $starChange, $savedb = false)
    {
    	if ($starChange <= 0) {
    		return false;
    	}

    	$userStarFish = self::getUserStarFish($uid);
    	if ($userStarFish === null) {
    		return false;
    	}

    	if ($userStarFish < $starChange) {
    		return false;
    	}

    	$userStarFish -= $starChange;

    	return self::updateUserStarFish($uid, $userStarFish, $savedb);
    }

    public static function updateUserLevel($uid, $levelInfo)
    {
		$data = array($levelInfo['level'], $levelInfo['island_level'], $levelInfo['island_level_2'], $levelInfo['island_level_3'], $levelInfo['island_level_4']);

    	$key = 'i:u:level:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        //$savedb = $cache->canSaveToDB($key, 900);
        $savedb = true;
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('level' => $levelInfo['level'],
        						  'island_level' => $levelInfo['island_level'],
        						  'island_level_2' => $levelInfo['island_level_2'],
        						  'island_level_3' => $levelInfo['island_level_3'],
        						  'island_level_4' => $levelInfo['island_level_4']);
        			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function getUserLoginInfo($uid)
    {
        $key = 'i:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
	            $data = $dalUser->getLoginInfo($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        $data[4] = isset($data[4]) ? $data[4] : 0;
        $data[5] = isset($data[5]) ? $data[5] : 12;
        $loginInfo = array(
        	'last_login_time' => $data[0],
        	'today_login_count' => $data[1],
        	'active_login_count' => $data[2],
        	'max_active_login_count' => $data[3],
        	'all_login_count' => $data[4],
        	'star_login_count' => $data[5]
        );

        return $loginInfo;
    }

    public static function updateUserLoginInfo($uid, $loginInfo, $savedb = false)
    {
    	$data = array(
    		$loginInfo['last_login_time'], $loginInfo['today_login_count'],
    		$loginInfo['active_login_count'], $loginInfo['max_active_login_count'],
    		$loginInfo['all_login_count'],$loginInfo['star_login_count']
    	);

    	$key = 'i:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 3600);
        }

        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$dalUser = Hapyfish2_Island_Dal_User::getDefaultInstance();
        			$dalUser->update($uid, $loginInfo);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function getUserIsland($uid)
    {
        $key = 'i:u:island:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUserIsland = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
	            $data = $dalUserIsland->get($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        //praise,position_count,bg_island,bg_island_id,bg_sky,bg_sky_id,bg_sea,bg_sea_id,bg_dock,bg_dock_id
        $userIsland = array(
        	'praise' => $data[0],
        	'position_count' => $data[1],
        	'bg_island' => $data[2],
        	'bg_island_id' => $data[3],
        	'bg_sky' => $data[4],
        	'bg_sky_id' => $data[5],
        	'bg_sea' => $data[6],
        	'bg_sea_id' => $data[7],
        	'bg_dock' => $data[8],
        	'bg_dock_id' => $data[9],
        	'bg_island_2' => isset($data[10]) ? $data[10] : 0,
        	'bg_island_id_2' => isset($data[11]) ? $data[11] : 0,
        	'bg_sky_2' => isset($data[12]) ? $data[12] : 0,
        	'bg_sky_id_2' => isset($data[13]) ? $data[13] : 0,
        	'bg_sea_2' => isset($data[14]) ? $data[14] : 0,
        	'bg_sea_id_2' => isset($data[15]) ? $data[15] : 0,
        	'bg_dock_2' => isset($data[16]) ? $data[16] : 0,
        	'bg_dock_id_2' => isset($data[17]) ? $data[17] : 0,
        	'bg_island_3' => isset($data[18]) ? $data[18] : 0,
        	'bg_island_id_3' => isset($data[19]) ? $data[19] : 0,
        	'bg_sky_3' => isset($data[20]) ? $data[20] : 0,
        	'bg_sky_id_3' => isset($data[21]) ? $data[21] : 0,
        	'bg_sea_3' => isset($data[22]) ? $data[22] : 0,
        	'bg_sea_id_3' => isset($data[23]) ? $data[23] : 0,
        	'bg_dock_3' => isset($data[24]) ? $data[24] : 0,
        	'bg_dock_id_3' => isset($data[25]) ? $data[25] : 0,
        	'bg_island_4' => isset($data[26]) ? $data[26] : 0,
        	'bg_island_id_4' => isset($data[27]) ? $data[27] : 0,
        	'bg_sky_4' => isset($data[28]) ? $data[28] : 0,
        	'bg_sky_id_4' => isset($data[29]) ? $data[29] : 0,
        	'bg_sea_4' => isset($data[30]) ? $data[30] : 0,
        	'bg_sea_id_4' => isset($data[31]) ? $data[31] : 0,
        	'bg_dock_4' => isset($data[32]) ? $data[32] : 0,
        	'bg_dock_id_4' => isset($data[33]) ? $data[33] : 0,
        	'current_island' => isset($data[34]) ? $data[34] : 1,
        	'unlock_island' => isset($data[35]) ? $data[35] : '1',
        	'praise_2' => isset($data[36]) ? $data[36] : 0,
        	'praise_3' => isset($data[37]) ? $data[37] : 0,
        	'praise_4' => isset($data[38]) ? $data[38] : 0
        );

        return $userIsland;
    }

    public static function updateFieldUserIsland($uid, $fieldInfo, $savedb = false)
    {
    	$userIsland = self::getUserIsland($uid);
    	if ($userIsland) {
    		foreach ($fieldInfo as $k => $v) {
    			if (isset($userIsland[$k])) {
    				$userIsland[$k] = $v;
    			}
    		}
			return self::updateUserIsland($uid, $userIsland, $savedb);
    	}
    }

    public static function updateUserIsland($uid, $userIsland, $savedb = false)
    {
    	$data = array(
    		$userIsland['praise'], $userIsland['position_count'],
    		$userIsland['bg_island'], $userIsland['bg_island_id'],$userIsland['bg_sky'], $userIsland['bg_sky_id'], $userIsland['bg_sea'], $userIsland['bg_sea_id'],$userIsland['bg_dock'], $userIsland['bg_dock_id'],
    		$userIsland['bg_island_2'], $userIsland['bg_island_id_2'],$userIsland['bg_sky_2'], $userIsland['bg_sky_id_2'], $userIsland['bg_sea_2'], $userIsland['bg_sea_id_2'],$userIsland['bg_dock_2'], $userIsland['bg_dock_id_2'],
    		$userIsland['bg_island_3'], $userIsland['bg_island_id_3'],$userIsland['bg_sky_3'], $userIsland['bg_sky_id_3'], $userIsland['bg_sea_3'], $userIsland['bg_sea_id_3'],$userIsland['bg_dock_3'], $userIsland['bg_dock_id_3'],
    		$userIsland['bg_island_4'], $userIsland['bg_island_id_4'],$userIsland['bg_sky_4'], $userIsland['bg_sky_id_4'], $userIsland['bg_sea_4'], $userIsland['bg_sea_id_4'],$userIsland['bg_dock_4'], $userIsland['bg_dock_id_4'],
    		$userIsland['current_island'], $userIsland['unlock_island'], $userIsland['praise_2'], $userIsland['praise_3'], $userIsland['praise_4']
    	);

		$key = 'i:u:island:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);

		if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
		}

        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array(
        				'praise' => $userIsland['praise'], 'position_count' => $userIsland['position_count'],
        				'bg_island' => $userIsland['bg_island'], 'bg_island_id' => $userIsland['bg_island_id'],
						'bg_sky' => $userIsland['bg_sky'], 'bg_sky_id' => $userIsland['bg_sky_id'],
        				'bg_sea' => $userIsland['bg_sea'], 'bg_sea_id' => $userIsland['bg_sea_id'],
						'bg_dock' => $userIsland['bg_dock'], 'bg_dock_id' => $userIsland['bg_dock_id'],
        				'bg_island_2' => $userIsland['bg_island_2'], 'bg_island_id_2' => $userIsland['bg_island_id_2'],
						'bg_sky_2' => $userIsland['bg_sky_2'], 'bg_sky_id_2' => $userIsland['bg_sky_id_2'],
        				'bg_sea_2' => $userIsland['bg_sea_2'], 'bg_sea_id_2' => $userIsland['bg_sea_id_2'],
						'bg_dock_2' => $userIsland['bg_dock_2'], 'bg_dock_id_2' => $userIsland['bg_dock_id_2'],
        				'bg_island_3' => $userIsland['bg_island_3'], 'bg_island_id_3' => $userIsland['bg_island_id_3'],
						'bg_sky_3' => $userIsland['bg_sky_3'], 'bg_sky_id_3' => $userIsland['bg_sky_id_3'],
        				'bg_sea_3' => $userIsland['bg_sea_3'], 'bg_sea_id_3' => $userIsland['bg_sea_id_3'],
						'bg_dock_3' => $userIsland['bg_dock_3'], 'bg_dock_id_3' => $userIsland['bg_dock_id_3'],
        				'bg_island_4' => $userIsland['bg_island_4'], 'bg_island_id_4' => $userIsland['bg_island_id_4'],
						'bg_sky_4' => $userIsland['bg_sky_4'], 'bg_sky_id_4' => $userIsland['bg_sky_id_4'],
        				'bg_sea_4' => $userIsland['bg_sea_4'], 'bg_sea_id_4' => $userIsland['bg_sea_id_4'],
						'bg_dock_4' => $userIsland['bg_dock_4'], 'bg_dock_id_4' => $userIsland['bg_dock_id_4'],
						'current_island' => $userIsland['current_island'], 'unlock_island' => $userIsland['unlock_island'],
        			);
        			$dalUserIsland = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
        			$dalUserIsland->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function changeIslandPraise($uid, $change, $islandId, $userIsland = null)
    {
    	if (!$userIsland) {
    		$userIsland = self::getUserIsland($uid);
    	}

        switch ( $islandId ) {
            case 2 :
    			$userIsland['praise_2'] += $change;
            	break;
            case 3 :
    			$userIsland['praise_3'] += $change;
            	break;
            case 4 :
    			$userIsland['praise_4'] += $change;
            	break;
            default :
    			$userIsland['praise'] += $change;
            	break;
        }

    	return self::updateUserIsland($uid, $userIsland);
    }

    public static function getUserTitle($uid)
    {
        $key = 'i:u:title:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalUserTitle = Hapyfish2_Island_Dal_UserTitle::getDefaultInstance();
	            $data = $dalUserTitle->get($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	}catch (Exception $e) {
        		return null;
        	}
        }

        //title,title_list
        $userTitle = array(
        	'title' => $data[0],
        	'title_list' => $data[1]
        );

        return $userTitle;
    }

    public static function updateUserTitle($uid, $titleInfo)
    {
		$key = 'i:u:title:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = array($titleInfo['title'], $titleInfo['title_list']);

		$savedb = $cache->canSaveToDB($key, 900);
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array(
        				'title' => $titleInfo['title'], 'title_list' => $titleInfo['title_list']
        			);
        			$dalUserTitle = Hapyfish2_Island_Dal_UserTitle::getDefaultInstance();
        			$dalUserTitle->update($uid, $info);
        		} catch (Exception $e) {
        			err_log($e->getMessage());
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function gainTitle($uid, $title)
    {
    	$userTitle = self::getUserTitle($uid);
    	if (!$userTitle) {
    		return false;
    	}

    	$titleList = $userTitle['title_list'];
    	if (!empty($titleList)) {
    		$tmp = split(',', $titleList);
    		if (in_array($title, $tmp)) {
    			return false;
    		}
    	} else {
    		$tmp = array();
    	}

    	$tmp[] = $title;

    	$newUserTitle = array('title' => $userTitle['title'], 'title_list' => join(',', $tmp));

		self::updateUserTitle($uid, $newUserTitle);
    }

    public static function getCardStatus($uid)
    {
        $key = 'i:u:cardstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalCardStatus= Hapyfish2_Island_Dal_CardStatus::getDefaultInstance();
	            $data = $dalCardStatus->get($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	}catch (Exception $e) {
        		return null;
        	}
        }

        $data[5] = isset($data[5]) ? $data[5] : 0;
        $data[6] = isset($data[6]) ? $data[6] : 0;
        $data[7] = isset($data[7]) ? $data[7] : 0;
        $data[8] = isset($data[8]) ? $data[8] : 0;
        $data[9] = isset($data[9]) ? $data[9] : 0;
        //uid,card_0,card_1,card_2,card_3,card_4
        $cardStatus = array(
        	'uid' => $uid,
        	'insurance' => $data[0],
        	'defense' => $data[1],
        	'fortune' => $data[2],
        	'poverty' => $data[3],
        	//'unknown' => $data[4],
        	'card_4' => $data[4],
        	'onekey' => $data[5],
        	'doubleexp' => $data[6],
        	'card_7' => $data[7],
        	'mammon' => $data[8],
        	'poor' => $data[9]
        );

        return $cardStatus;
    }

    public static function updateCardStatus($uid, $status, $savedb = false)
    {
        $data = array(
    		$status['insurance'], $status['defense'], $status['fortune'], $status['poverty'],
    		$status['card_4'], $status['onekey'], $status['doubleexp'], $status['card_7'],
    		$status['mammon'], $status['poor']
    	);
    	//$data = array($status['insurance'], $status['defense'], $status['fortune'], $status['poverty'], $status['unknown']);
        $key = 'i:u:cardstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array(
                		'card_0' => $status['insurance'], 'card_1' => $status['defense'],
        				'card_5' => $status['onekey'], 'card_6' => $status['doubleexp']
        				//'card_0' => $status['insurance'], 'card_1' => $status['defense']
        			);
        			$dalCardStatus= Hapyfish2_Island_Dal_CardStatus::getDefaultInstance();
        			$dalCardStatus->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

    public static function getUserCurrentIsland($uid, $islandId)
    {
        $key = 'i:u:currentIsland:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if ($islandId === false) {
        	try {
	            $dalUser = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
	            $islandId = $dalUser->get($uid);

				$cache->update($key, $islandId);
        	} catch (Exception $e) {
        		return null;
        	}
        }
        return $islandId;
    }

    public static function updateUserCurrentIsland($uid, $islandId, $savedb = false)
    {
        $key = 'i:u:currentIsland:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('current_island' => $islandId);
        			$dalUserIsland = Hapyfish2_Island_Dal_UserIsland::getDefaultInstance();
        			$dalUserIsland->update($uid, $info);
        		} catch (Exception $e) {
        		}
        	}
        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }

}