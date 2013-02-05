<?php

class StatController extends Zend_Controller_Action
{
    function init()
    {
        $info = Hapyfish2_Bms_Bll_Auth::vailid();
        if (!$info) {
			$this->_redirect('/');
        	exit;
        }

        $this->info = $info;
        $this->view->cuid = $info['uid'];
        $platform = $this->_request->getParam('platform');
        $this->platform = $platform;
        $this->view->platform = $platform;
    	$this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

    public function mainAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		$info = Hapyfish2_Island_Bll_Day::getMainRange($this->platform, $begin, $end, true);

		$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
		$this->view->chart = $chart;
    	$this->render();
    }

    public function main2Action()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		$info = Hapyfish2_Island_Bll_Day::getMainRange($this->platform, $begin, $end, true);
		$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
		$this->view->chart = $chart;
    	$this->render();
    }

    public function mainalchemyAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		$info = Hapyfish2_Island_Bll_Day::getMainRange($this->platform, $begin, $end, true);

		$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
		$this->view->chart = $chart;
    	$this->render();
    }
    
    public function retentionAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
    	$info = Hapyfish2_Island_Bll_Day::getRetentionRange($this->platform, $begin, $end);
		$chart1 = Hapyfish2_Island_Bll_Chart::createRetention($info, true, '1');
		$chart2 = Hapyfish2_Island_Bll_Chart::createRetention($info, true, '7', '#ff0000');

    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->view->chart1 = $chart1;
    	$this->view->chart2 = $chart2;
    	$this->render();
    }

    public function activeuserlevelAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Day::getActiveUserLevel($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }

    public function payAction()
    {
		$this->view->today = date('Y-m-d');
    	$this->render();
    }

    public function pay2Action()
    {
		$t = time();
		$year = date('Y', $t);
		$month = date('m', $t);
    	$this->view->startday = date('Y-m-d',mktime(0,0,0,$month,1,$year));
    	$this->view->endday = date('Y-m-d',mktime(0,0,0,$month+1,0,$year));
    	$this->render();
    }

    public function mainhourAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_MainHour::getDay($this->platform, $day);
		$chart1 = Hapyfish2_Island_Bll_Chart::createAddUserHour($day, $info);
		$chart2 = Hapyfish2_Island_Bll_Chart::createActiveUserHour($day, $info);
    	$this->view->today = $today;
    	$this->view->chart1 = $chart1;
    	$this->view->chart2 = $chart2;
    	$this->render();
    }

    public function mainmonthAction()
    {
    	$startday = '2011-01';
    	$t = time();
    	$endday = date('Y-m', $t);
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$begin = '201101';
    	$end = date('Ym', $t);
		$info = Hapyfish2_Island_Bll_MainMonth::getRange($this->platform, $begin, $end);
		$chart = null;
		$this->view->chart = $chart;
    	$this->render();
    }

    public function tutorialAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');

    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->render();
    }

    public function pay3Action()
    {
		$t = time();
    	$this->view->startday = date('Y-m-d', strtotime("-7 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->view->startday2 = date('Y-m-d', strtotime("-15 day"));
    	$this->view->endday2 = date('Y-m-d', strtotime("-8 day"));
    	$this->render();
    }

    public function cloadtmAction()
    {
    	$this->view->startday = date('Y-m-d', strtotime("-7 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->render();
    }

    public function saleAction()
    {
    	$t = time();
    	$this->view->startday = date('Y-m-d', strtotime("-7 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->view->startday2 = date('Y-m-d', strtotime("-15 day"));
    	$this->view->endday2 = date('Y-m-d', strtotime("-8 day"));
    	$this->render();
    }

    public function salelistAction()
    {
        $t = time();
        $this->view->startday = date('Y-m-d', strtotime("-7 day"));
        $this->view->endday = date('Y-m-d', strtotime("-1 day"));
        $this->view->startday2 = date('Y-m-d', strtotime("-15 day"));
        $this->view->endday2 = date('Y-m-d', strtotime("-8 day"));
        $this->render();
    }

    public function sendgoldAction()
    {
    	$t = time();
    	$this->view->startday = date('Y-m-d', strtotime("-1 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->render();
    }
    public function payclickAction()
    {
    	$t = time();
    	$this->view->startday = date('Y-m-d', strtotime("-1 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->render();
    }

    //商城道具排行榜
    public function propsaleAction()
    {
        $begin = date('Y-m-d', strtotime("-2 day"));
        $end = date('Y-m-d', strtotime("-1 day"));
        $this->view->startday = $begin;
        $this->view->endday = $end;

	    //$priceType:1,金币；2,宝石
	    //$sortType:1,销售量排行；2,销售额排行
        $priceType = 1;
        $sortType = 1;
        $info = Hapyfish2_Island_Bll_Propsale::getPropsale($this->platform, $begin, $end, $priceType, $sortType);
        $chart = null;
        $this->view->chart = $chart;
        $this->render();
    }

    //所有用户等级分布
    public function alluserlevelAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_Day::getAllUserLevel($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 2);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //每日升级人数
    public function levelupAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_Day::getLevelup($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 2);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //各充值额度人数分布
    public function payamountAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_Payment::getPayAmount($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createPaylist($day, $info, 1);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //每日首次充值的等级分布
    public function payfirstAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_Payment::getPayfirst($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createPaylist($day, $info, 2);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //所有等级玩家充值次数和总额
    public function payallAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_Payment::getPayall($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createPaylist($day, $info, 3);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //7天未登录用户信息，基础信息
    public function lossuserinfoAction()
    {
        $startday = date('Y-m-d', strtotime("last month"));
        $endday = date('Y-m-d');
        $this->view->startday = $startday;
        $this->view->endday = $endday;
        $begin = date('Ymd', strtotime($startday));
        $end = date('Ymd', strtotime($endday));
        $info = Hapyfish2_Island_Bll_LossUser::getLossUserInfoRange($this->platform, $begin, $end, true);

        //$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
        $this->view->chart = $chart;
        $this->render();
    }

    //7天未登录用户信息，等级分布
    public function lossuserlevelAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_LossUser::getLossUserLevel($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 4);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //7天未登录用户信息，爱心分布
    public function lossuserloveAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_LossUser::getLossUserLove($this->platform, $day);
        //$chart = Hapyfish2_Island_Bll_Chart::createActiveUserLevelContent($day, $info, 5);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }



    //捐赠额度分布
    public function donatespreadAction()
    {
        $today = date('Y-m-d', strtotime("-1 day"));
        $day = date('Ymd', strtotime($today));
        $info = Hapyfish2_Island_Bll_Day::getDonateSpread($this->platform, $day);
        $chart = Hapyfish2_Island_Bll_Chart::createDonateSpread($day, $info);
        $this->view->today = $today;
        $this->view->chart = $chart;
        $this->render();
    }

    //捐赠额度走势
    public function donateallAction()
    {
        $startday = date('Ymd', strtotime("last month"));
    	$endday = date('Ymd', strtotime("-1 day"));
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
    	$info = Hapyfish2_Island_Bll_Day::getDonateAll($this->platform, $begin, $end);
		$chart1 = Hapyfish2_Island_Bll_Chart::createDonate($info);

    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->view->chart1 = $chart1;
    	$this->render();
    }

    //推广活动连接统计数据 feed
    public function feedAction()
    {
    	$this->view->startday = date('Y-m-d', strtotime("-7 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->render();
    }

    //推广活动连接统计数据 promote
    public function promoteAction()
    {
    	$this->view->startday = date('Y-m-d', strtotime("-7 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->render();
    }

    //粉丝数·熊猫问答数·新手引导
    public function fansAction()
    {
    	$this->view->startday = date('Y-m-d', strtotime("-7 day"));
    	$this->view->endday = date('Y-m-d', strtotime("-1 day"));
    	$this->render();
    }

    
/********************************** alchemy-炼金 ***********************************************/
        
    //佣兵-雇佣-主要信息
    public function mercenarymainAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		$info = Hapyfish2_Island_Bll_Mercenary::getRange($this->platform, $begin, $end);

		//$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
		$chart = null;
		$this->view->chart = $chart;
    	$this->render();
    }
    
    //佣兵-雇佣-各佣兵星级分布
    public function mercenaryrpAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Mercenary::getRp($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 1);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    //佣兵-雇佣-经营等级分布
    public function mercenaryuserlevelAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Mercenary::getUserLevel($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 2);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    //佣兵-雇佣-战斗等级分布
    public function mercenaryrolelevelAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Mercenary::getRoleLevel($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 3);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    //佣兵-培养-佣兵等级分布
    public function mercenarystrthenrolelevelAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Mercenary::getStrthenRoleLevel($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 4);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    //订单-主要信息
    public function ordermainAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$begin = date('Ymd', strtotime($startday));
    	$end = date('Ymd', strtotime($endday));
		$info = Hapyfish2_Island_Bll_Order::getRange($this->platform, $begin, $end);

		//$chart = Hapyfish2_Island_Bll_Chart::createMainContent($info, true);
		$chart = null;
		$this->view->chart = $chart;
    	$this->render();
    }
    
    //订单-各道具使用分布
    public function itemuseAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Item::getItemUse($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 5);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    //商店-购买物品分布
    public function shopmainAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Shop::getShop($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 6);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    //合成术-合成物品分布
    public function mixmainAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Mix::getMix($this->platform, $day);
		$chart = Hapyfish2_Island_Bll_Chart::createDayContent($day, $info, 7);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    public function statmainhourAction()
    {
    	$today = date('Y-m-d', strtotime("-0 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_StatMainHour::getDay($this->platform, $day);
		//$chart1 = Hapyfish2_Island_Bll_Chart::createAddUserHour($day, $info);
		//$chart2 = Hapyfish2_Island_Bll_Chart::createActiveUserHour($day, $info);
		$chart1 = null;
		$chart2 = null;
    	$this->view->today = $today;
    	$this->view->chart1 = $chart1;
    	$this->view->chart2 = $chart2;
    	$this->render();
    }
    
    
/********************************** 炼金-豆豆  ***********************************************/
    public function fightmainAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->render();
    }
    
    public function operatelevelAction(){
    
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
    	$map = Hapyfish2_Island_Bll_Fight::getMapList($this->platform,$day);
		$info = Hapyfish2_Island_Bll_Fight::getOperateLevel($this->platform, $day, 1);
		$chart = Hapyfish2_Island_Bll_Chart::createDayFight($day, $info, 1, 1);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->view->mapList = $map;
    	$this->render();
    }
    
	public function fightlevelAction(){
    
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
    	$map = Hapyfish2_Island_Bll_Fight::getMapList($this->platform,$day);
		$info = Hapyfish2_Island_Bll_Fight::getFightLevel($this->platform, $day, 1);
		$chart = Hapyfish2_Island_Bll_Chart::createDayFight(20120615, $info, 1, 2);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->view->mapList = $map;
    	$this->render();
    }
    
    public function mutualAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->render();
    }
    
    public function repairAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->render();
    }
    
    public function upgradeAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
    	$this->render();
    }
    

 	public function upgradelevelAction()
    {
    	$today = date('Y-m-d', strtotime("-1 day"));
    	$day = date('Ymd', strtotime($today));
		$info = Hapyfish2_Island_Bll_Upgrade::getUpgradetLevel($this->platform, $day, 1);
		$chart = Hapyfish2_Island_Bll_Chart::createDayUpgrade($day, $info, 1);
    	$this->view->today = $today;
    	$this->view->chart = $chart;
    	$this->render();
    }
    
    public function getfaqAction()
    {
    	$startday = date('Y-m-d', strtotime("last month"));
    	$endday = date('Y-m-d');
    	$this->view->startday = $startday;
    	$this->view->endday = $endday;
		$this->render();
    }
    
    public function contrastAction()
    {
    	$list = Hapyfish2_Island_Bll_Contrast::getTableList();
    	$this->view->list = $list;
		$this->render();
    }
}