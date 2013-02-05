<?php

class StatapiController extends Zend_Controller_Action
{
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }

        $this->info = $info;
        $this->cuid = $info['uid'];

    	$controller = $this->getFrontController();
    	$this->platform = $this->_request->getParam('platform');
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }

    public function mainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
			$info = Hapyfish2_Island_Bll_Day::getMainRange($platform, $begin, $end, true);
			$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function retentionAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
	    	$info = Hapyfish2_Island_Bll_Day::getRetentionRange($platform, $begin, $end);
			$chart1 = Hapyfish2_Island_Bll_Chart::createRetention($info, true, '1');
			$chart2 = Hapyfish2_Island_Bll_Chart::createRetention($info, true, '7', '#ff0000');
			$result = array('data' => $info, 'chart1' => $chart1, 'chart2' => $chart2);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function activeuserlevelAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_Day::getActiveUserLevel($platform, $day);
			$chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function payAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		$rest->server_addr = 'http://stat.island.qzoneapp.com';
		try {
			$result = $rest->payhourofday($day);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function pay2Action()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
        $rest = Hapyfish2_Rest_Factory::getRest($this->platform);
		if (!$rest) {
			$this->echoError('-1', 'apiinfo error');
		}
		$rest->setUser($this->cuid);
		$rest->server_addr = 'http://stat.island.qzoneapp.com';
		try {
			$result = $rest->mainofday($startday, $endday);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function editmemoAction()
    {
    	$day = $this->_request->getParam('day');
    	$memo = $this->_request->getParam('memo');
    	Hapyfish2_Island_Bll_Main::updateMemo($this->platform, $day, $memo);
    	$result = array('res' => 'ok');
    	$this->echoResult($result);
    }

    public function mainhourAction()
    {
        $defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_MainHour::getDay($platform, $day);
			$chart1 = Hapyfish2_Island_Bll_Chart::createAddUserHour($day, $info);
			$chart2 = Hapyfish2_Island_Bll_Chart::createActiveUserHour($day, $info);
			$result = array('data' => $info, 'chart1' => $chart1, 'chart2' => $chart2);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function mainmonthAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ym', strtotime($startday));
    	$end = date('Ym', strtotime($endday));
		try {
			$info = Hapyfish2_Island_Bll_MainMonth::getRange($platform, $begin, $end);
			$chart = null;
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function tutorialAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
	    	$info = Hapyfish2_Island_Bll_Tutorial::getTutorialRange($platform, $begin, $end);
			$result = array('data' => $info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function paymentcompareAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = strtotime($startday);
    	$end = strtotime($endday);
		try {
	    	$info = Hapyfish2_Island_Bll_Payment::getPaymentCompare($platform, $begin, $end);
			$result = array('data' => $info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }


    public function cloadtmAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = str_replace('-', '', $startday);
    	$end = str_replace('-', '', $endday);
		try {

    		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
    		if (!$rest) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$rest->setUser($this->cuid);
		    $info = $rest->listcLoadTmData($begin, $end);

			$this->echoResult($info);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

 	public function propsaleAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$cid = $this->_request->getParam('cid');
    	$platform = $this->_request->getParam('platform');
    	$begin = str_replace('-', '', $startday);
    	$end = str_replace('-', '', $endday);
		try {

    		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
    		if (!$rest) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$rest->setUser($this->cuid);
		    $info = $rest->loadpropData($begin, $end, $cid);
			$this->echoResult($info);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    public function propsalelistAction()
    {
        $startday = $this->_request->getParam('startday');
        $endday = $this->_request->getParam('endday');
        $platform = $this->_request->getParam('platform');
        $priceType = $this->_request->getParam('priceType', 1);
        $sortType = $this->_request->getParam('sortType', 1);
        $begin = date('Ymd', strtotime($startday));
        $end = date('Ymd', strtotime($endday));
        try {
	        //$priceType:1,金币；2,宝石
	        //$sortType:1,销售量排行；2,销售额排行
	        $info = Hapyfish2_Island_Bll_Propsale::getPropsale($platform, $begin, $end, $priceType, $sortType);
            $chart = null;
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    public function sendgoldAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
	    	$info = Hapyfish2_Island_Bll_Sendgold::getSendgoldRange($platform, $begin, $end);
			$result = array('data' => $info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
     public function payclickAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
	    	$info = Hapyfish2_Island_Bll_Payclick::getPayclickRange($platform, $begin, $end);
			$result = array('data' => $info);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    //所有用户等级分布
    public function alluserlevelAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
            $info = Hapyfish2_Island_Bll_Day::getAllUserLevel($platform, $day);
            $chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 2);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //每日升级人数
    public function levelupAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
            $info = Hapyfish2_Island_Bll_Day::getLevelup($platform, $day);
            $chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 3);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //各充值额度人数分布
    public function payamountAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
	        $info = Hapyfish2_Island_Bll_Payment::getPayAmount($platform, $day);
	        $chart = Hapyfish2_Island_Bll_Chart::createPaylist($day, $info, 1);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //每日首次充值的等级分布
    public function payfirstAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
            $info = Hapyfish2_Island_Bll_Payment::getPayfirst($platform, $day);
            $chart = Hapyfish2_Island_Bll_Chart::createPaylist($day, $info, 2);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //所有等级玩家充值次数和总额
    public function payallAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
            $info = Hapyfish2_Island_Bll_Payment::getPayall($platform, $day);
            $chart = Hapyfish2_Island_Bll_Chart::createPaylist($day, $info, 3);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //七天未登录用户-基础信息
    public function lossuserinfoAction()
    {
        $startday = $this->_request->getParam('startday');
        $endday = $this->_request->getParam('endday');
        $platform = $this->_request->getParam('platform');
        $begin = date('Ymd', strtotime($startday));
        $end = date('Ymd', strtotime($endday));

        try {
            $info = Hapyfish2_Island_Bll_LossUser::getLossUserInfoRange($platform, $begin, $end, true);

            //$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //七天未登录用户-等级分布
    public function lossuserlevelAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
            $info = Hapyfish2_Island_Bll_LossUser::getLossUserLevel($platform, $day);
            $chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 4);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

    //七天未登录用户-爱心分布
    public function lossuserloveAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');
        try {
            $info = Hapyfish2_Island_Bll_LossUser::getLossUserLove($platform, $day);
            //$chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 5);
            $result = array('data' => $info, 'chart' => $chart);
            $this->echoResult($result);
        } catch (Exception $e) {
            $this->echoError($e->getCode(), $e->getMessage());
        }
    }

/**********************************************************************/

    //捐赠额度分布
    public function donatespreadAction()
    {
        $defaultDay = date('Ymd');
        $day = $this->_request->getParam('day', $defaultDay);
        $day = date('Ymd', strtotime($day));
        $platform = $this->_request->getParam('platform');

        $info = Hapyfish2_Island_Bll_Day::getDonateSpread($platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createDonateSpread($day, $info);
        $result = array('data' => $info, 'chart' => $chart);
        $this->echoResult($result);
    }

    //捐赠额度走势
    public function donateallAction()
    {
        $startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
	    	$info = Hapyfish2_Island_Bll_Day::getDonateAll($platform, $begin, $end);
			$chart1 = Hapyfish2_Island_Bll_Chart::createDonate($info);

			//rank
			$dal = Hapyfish2_Island_Dal_Donate::getDefaultInstance();
			$row = $dal->getDayDonate($end);
			$rank = null;
			if ($row) {
                $rank = json_decode($row['amount_ranking'], true);
			}

			$result = array('data' => $info, 'chart1' => $chart1, 'rank' => $rank);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
        $this->render();
    }

    //推广活动连接统计数据 feed
    public function feedAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = str_replace('-', '', $startday);
    	$end = str_replace('-', '', $endday);
		try {

    		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
    		if (!$rest) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$rest->setUser($this->cuid);
		    $info = $rest->listPromoteData($begin, $end);

		    $promoteInfo = $info['data'];
		    //get table head data
		    $head = array();
            foreach ($promoteInfo as $key=>&$data) {
                $aryfeedSpread = json_decode($data['feed_click_spread'], true);
                foreach ($aryfeedSpread as $col=>$val) {
                    if (!in_array($col, $head)) {
                        $head[] = $col;
                    }
                }
                $data['ary_feed_spread'] = $aryfeedSpread;
            }
            sort($head);

            //get content
            foreach ($promoteInfo as $key=>&$data) {
                $intNum1 = $intNum2 = $intNum3 = 0;
                //unset($data['ary_feed_spread']['0']);
                foreach ($head as $idx=>$col) {
                    if (!array_key_exists($col, $data['ary_feed_spread'])) {
                        $data['ary_feed_spread'][$col] = '0|0|0';
                    }
                    if ($col) {
                        $aryTmp = explode('|', $data['ary_feed_spread'][$col]);
                        $intNum1 += $aryTmp[0];
                        $intNum2 += $aryTmp[1];
                        $intNum3 += $aryTmp[2];
                    }
                }
                $data['ary_feed_spread']['0'] = $intNum1 . '|' . $intNum2 . '|' . $intNum3;
                ksort($data['ary_feed_spread']);
            }

            $rst = array('data' => $promoteInfo, 'head' => $head);

			$this->echoResult($rst);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }

    //推广活动连接统计数据 promote
    public function promoteAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = str_replace('-', '', $startday);
    	$end = str_replace('-', '', $endday);
		try {

    		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
    		if (!$rest) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$rest->setUser($this->cuid);
		    $info = $rest->listPromoteData($begin, $end);

		    $promoteInfo = $info['data'];
		    //get table head data
		    $head = array();
            foreach ($promoteInfo as $key=>&$data) {
                $arypromoteSpread = json_decode($data['promote_click_spread'], true);
                foreach ($arypromoteSpread as $col=>$val) {
                    if (!in_array($col, $head)) {
                        $head[] = $col;
                    }
                }
                $data['ary_promote_spread'] = $arypromoteSpread;
            }
            sort($head);

            //get content
            foreach ($promoteInfo as $key=>&$data) {
                $intNum1 = $intNum2 = $intNum3 = 0;
                //unset($data['ary_feed_spread']['0']);
                foreach ($head as $idx=>$col) {
                    if (!array_key_exists($col, $data['ary_promote_spread'])) {
                        $data['ary_promote_spread'][$col] = '0|0|0';
                    }
                    if ($col) {
                        $aryTmp = explode('|', $data['ary_promote_spread'][$col]);
                        $intNum1 += $aryTmp[0];
                        $intNum2 += $aryTmp[1];
                        $intNum3 += $aryTmp[2];
                    }
                }
                $data['ary_promote_spread']['0'] = $intNum1 . '|' . $intNum2 . '|' . $intNum3;
                ksort($data['ary_promote_spread']);
            }

            $rst = array('data' => $promoteInfo, 'head' => $head);

		    /*
		    //get table head data
		    $head1 = $head2 = array();
            foreach ($promoteInfo as $key=>&$data) {
                $arySpread1 = json_decode($data['newuser_spread'], true);
                $arySpread2 = json_decode($data['activeuser_spread'], true);
                foreach ($arySpread1 as $col=>$val) {
                    if (!in_array($col, $head1)) {
                        $head1[] = $col;
                    }
                }
                foreach ($arySpread2 as $col=>$val) {
                    if (!in_array($col, $head2)) {
                        $head2[] = $col;
                    }
                }
                $data['ary_newuser'] = $arySpread1;
                $data['ary_activeuser'] = $arySpread2;
            }
            ksort($head1);
            ksort($head2);

            //get content
            foreach ($promoteInfo as $key=>&$data) {
                foreach ($head1 as $idx=>$col) {
                    if (!array_key_exists($col, $data['ary_newuser'])) {
                        $data['ary_newuser'][$col] = 0;
                    }
                }
                foreach ($head2 as $idx=>$col) {
                    if (!array_key_exists($col, $data['ary_activeuser'])) {
                        $data['ary_activeuser'][$col] = 0;
                    }
                }
            }

            $rst = array('data' => $promoteInfo, 'head1' => $head1, 'head2' => $head2);
            */

			$this->echoResult($rst);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }


    //粉丝数·熊猫问答数
    public function fansAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = str_replace('-', '', $startday);
    	$end = str_replace('-', '', $endday);
		try {

    		$rest = Hapyfish2_Rest_Factory::getRest($this->platform);
    		if (!$rest) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$rest->setUser($this->cuid);
		    $info = $rest->listEasyportionData($begin, $end);

		    $dataInfo = $info['data'];
		    //get table head data
		    $head = array();
            foreach ($dataInfo as $key=>&$data) {
                $content = json_decode($data['content'], true);
                $aryQuestion = $content['pandaquestion'];
                foreach ($aryQuestion as $col=>$val) {
                    if (!in_array($col, $head)) {
                        $head[] = $col;
                    }
                }
                $data['ary_content'] = $content;
            }
            sort($head);

            //get content
            foreach ($dataInfo as $key=>&$data) {
                foreach ($head as $idx=>$col) {
                    if (!array_key_exists($col, $data['ary_content']['pandaquestion'])) {
                        $data['ary_content']['pandaquestion'][$col] = '0';
                    }
                }
                ksort($data['ary_content']['pandaquestion']);
            }

            //guide
            $headGuide = array();
            for($i=0;$i<44;$i++) {
                $headGuide[] = $i;
            }
            $main = Hapyfish2_Island_Bll_Day::getMainRange($platform, $begin, $end, false);
		    foreach ($dataInfo as $key=>&$data) {
                $data['ary_content']['guide'][0] = $main[$key]['add_user'];
            }

            $rst = array('data' => $dataInfo, 'head1' => $head, 'head2' => $headGuide);

			$this->echoResult($rst);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
/********************************** alchemy-炼金 ***********************************************/
        
    //佣兵-雇佣-主要信息
    public function mercenarymainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
			$info = Hapyfish2_Island_Bll_Mercenary::getRange($platform, $begin, $end);
			$chart = null;
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //佣兵-雇佣-各佣兵星级分布
    public function mercenaryrpAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_Mercenary::getRp($platform, $day);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 1);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //佣兵-雇佣-经营等级分布
    public function mercenaryuserlevelAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_Mercenary::getUserLevel($platform, $day);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 2);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //佣兵-雇佣-战斗等级分布
    public function mercenaryrolelevelAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_Mercenary::getRoleLevel($platform, $day);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 3);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //佣兵-培养-佣兵等级分布
    public function mercenarystrthenrolelevelAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_Mercenary::getStrthenRoleLevel($platform, $day);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 4);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //订单-主要信息
    public function ordermainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
			$info = Hapyfish2_Island_Bll_Order::getRange($platform, $begin, $end);
			$chart = null;
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //道具-各道具使用分布
    public function itemuseAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_Item::getItemUse($platform, $day);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 5);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //商店-购买物品分布
    public function shopmainAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
    	$sort = $this->_request->getParam('sort', 'count');
    	$order = $this->_request->getParam('order', 'ASC');
    	
		try {
			$info = Hapyfish2_Island_Bll_Shop::getShop($platform, $day, $sort, $order);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 6);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    //合成术-合成物品分布
    public function mixmainAction()
    {
    	$defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
    	$sort = $this->_request->getParam('sort', 'count');
    	$order = $this->_request->getParam('order', 'ASC');
    	
		try {
			$info = Hapyfish2_Island_Bll_Mix::getMix($platform, $day, $sort, $order);
			$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 7);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    public function statmainhourAction()
    {
        $defaultDay = date('Ymd');
    	$day = $this->_request->getParam('day', $defaultDay);
    	$day = date('Ymd', strtotime($day));
    	$platform = $this->_request->getParam('platform');
		try {
			$info = Hapyfish2_Island_Bll_StatMainHour::getDay($platform, $day);
			//$chart1 = Hapyfish2_Island_Bll_Chart::createAddUserHour($day, $info);
			//$chart2 = Hapyfish2_Island_Bll_Chart::createActiveUserHour($day, $info);
			$chart1 = null;
			$chart2 = null;
			$result = array('data' => $info, 'chart1' => $chart1, 'chart2' => $chart2);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
/********************************** 炼金-豆豆  ***********************************************/
    public function fightmainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		try {
			$info = Hapyfish2_Island_Bll_Fight::getRange($platform, $begin, $end);
			$chart = null;
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    public function monterAction()
    {
    	$platform = $this->_request->getParam('platform');
    	$map = $this->_request->getParam('map');
    	$type = $this->_request->getParam('type');
    	$date = $this->_request->getParam('date');
   		 try {
			$info = Hapyfish2_Island_Bll_Fight::getMonter($platform, $map, $type, $date);
			$chart = null;
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
 	public function materAction()
    {
    	$platform = $this->_request->getParam('platform');
    	$map = $this->_request->getParam('map');
    	$type = $this->_request->getParam('type');
    	$date = $this->_request->getParam('date');
   		 try {
			$info = Hapyfish2_Island_Bll_Fight::getMater($platform, $map, $type, $date);
			$chart = null;
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    public function fightlevelAction()
    {
    	$day = $this->_request->getParam('day');
    	$day = date('Ymd', strtotime($day));
    	$map = $this->_request->getParam('map');
     	try {
			$info = Hapyfish2_Island_Bll_Fight::getFightLevel($this->platform, $day, $map);
			$chart = Hapyfish2_Island_Bll_Chart::createDayFight($day, $info, $map, 2);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
	public function operatelevelAction()
    {
    	$day = $this->_request->getParam('day');
    	$day = date('Ymd', strtotime($day));
    	$map = $this->_request->getParam('map');
     	try {
			$info = Hapyfish2_Island_Bll_Fight::getOperateLevel($this->platform, $day, $map);
			$chart = Hapyfish2_Island_Bll_Chart::createDayFight($day, $info, $map, 1);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    
    public function mutualmainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
    	$info = Hapyfish2_Island_Bll_Fight::getMutualDetail($this->platform, $begin, $end);
    	$result = array('data' => $info);
		$this->echoResult($result);
    }
    
    public function repairmainAction()
    {
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
    	$info = Hapyfish2_Island_Bll_Fight::getRepairDetail($this->platform, $begin, $end);
    	$result = array('data' => $info);
		$this->echoResult($result);
    }
    
    
    public function upgrademainAction()
    {
    	
    	$startday = $this->_request->getParam('startday');
    	$endday = $this->_request->getParam('endday');
    	$platform = $this->_request->getParam('platform');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
    	$info = Hapyfish2_Island_Bll_Upgrade::getUpgradeDetail($this->platform, $begin, $end);
    	$result = array('data' => $info);
		$this->echoResult($result);
    }
    
    public function upgradelevelAction()
    {
    	$day = $this->_request->getParam('day');
    	$day = date('Ymd', strtotime($day));
    	$type = $this->_request->getParam('type');
     	try {
			$info = Hapyfish2_Island_Bll_Upgrade::getUpgradetLevel($this->platform, $day, $type);
			$chart = Hapyfish2_Island_Bll_Chart::createDayUpgrade($day, $info, $type);
			$result = array('data' => $info, 'chart' => $chart);
			$this->echoResult($result);
		} catch (Exception $e) {
			$this->echoError($e->getCode(), $e->getMessage());
		}
    }
    
    public function getfaqAction()
    {
    	$start = $this->_request->getParam('startday', 0);
    	$start = strtotime($start.' 00:00:00');
		$end = $this->_request->getParam('endday', 0);
		$end = strtotime($end.' 23:59:59');
		$page = $this->_request->getParam('page', 1);
		$type = $this->_request->getParam('type', 3);
		$status = $this->_request->getParam('status', 2);
		$id = $this->_request->getParam('id', 0);
    	try {
    		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
    		if (!$bot) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$bot->setUser($this->cuid);
    		$data['start'] = $start;
    		$data['end'] = $end;
    		$data['page'] = $page;
    		$data['type'] = $type;
    		$data['status'] = $status;
    		$data['id'] = $id;
		    $info = $bot->stat_getfaq($data);
		    $this->echoResult($info);
		} catch (Exception $e) {
		}
	}
	
	public function exportfaqAction()
	{
		$start = $this->_request->getParam('startday', 0);
    	$start = strtotime($start.' 00:00:00');
		$end = $this->_request->getParam('endday', 0);
		$end = strtotime($end.' 23:59:59');
		$type = $this->_request->getParam('type', 3);
		$status = $this->_request->getParam('status', 2);
		$id = $this->_request->getParam('id', 0);
    	try {
    		$bot = Hapyfish2_Rest_Factory::getBot($this->platform);
    		if (!$bot) {
    			$this->echoError('-1', 'apiinfo error');
    		}
    		$bot->setUser($this->cuid);
    		$data['start'] = $start;
    		$data['end'] = $end;
    		$data['type'] = $type;
    		$data['status'] = $status;
    		$data['id'] = $id;
		    $info = $bot->stat_getexportfaq($data);
		} catch (Exception $e) {
		}
		header("Content-type: text");
        header('Content-Disposition: attachment; filename=faq.txt');
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
		echo "问题类型\t问题id\t门牌号\t问题描述\t详细\t日期\t\n"; 
		if($info){
			foreach($info['data']['data'] as $k=>$v){
				echo $v['type']."\t".$v['id']."\t".$v['uid']."\t".$v['title']."\t".$v['content']."\t".date('Y-m-d', $v['create_time'])."\t\n";
			}
		}
		exit;
	}
	
	public function contrastAction()
	{
		$devp = 'alchemy_renren';
		$p = 'alchemy_kaixin';
		$table = $this->_request->getParam('table', 'all');
		$devbot = Hapyfish2_Rest_Factory::getBot($devp);
		$bot = Hapyfish2_Rest_Factory::getBot($p);
		$bot->setUser($this->cuid);
    	$data['table'] = $table;
		$info = $bot->stat_contrast($data);
		$devInfo = $devbot->stat_contrast($data);
		$result = Hapyfish2_Island_Bll_Contrast::contrast($devInfo,$info);
		echo json_encode($result);
    	exit;
	}
}