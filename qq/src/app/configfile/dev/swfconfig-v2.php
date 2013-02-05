<?php

// swf list
$swfList = array(
    STATIC_HOST . '/swf_v2/swc.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/swc2.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/swc3.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/levelUp.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/building1.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/building2.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/building3.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/building4.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/building5.swf?v=2010122001',
    STATIC_HOST . '/swf_v2/building6.swf?v=2010122001',
    STATIC_HOST . '/swf_v2/building7.swf?v=2011011301',
    STATIC_HOST . '/swf_v2/building8.swf?v=2011011301',
	STATIC_HOST . '/swf_v2/building9.swf?v=2011011701',
    STATIC_HOST . '/swf_v2/island1.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/sky1.swf?v=2010122001',
    STATIC_HOST . '/swf_v2/sea1.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/dock1.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/boat1.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/itemcard1.swf?v=2011012003',
    STATIC_HOST . '/swf_v2/player1.swf?v=2010122001',
    STATIC_HOST . '/swf_v2/sound1.swf?v=2010111701',
    STATIC_HOST . '/swf_v2/chongzhiIcon.swf?v=2010112901',
    STATIC_HOST . '/swf_v2/actIconSwc.swf?v=2010112901',
    STATIC_HOST . '/swf_v2/signWinUi.swf?v=2010112901',
);

$otherSwfs = array(
    'localeTxt'         => '/',
    'help'              => 'swf_v2/helpV2View.swf?v=2010111701',
	'news'				=> 'swf_v2/news.swf?v=2010111701',
	'exmall'			=> 'swf_v2/externalMall.swf?v=2011011901'
);

$mainswf = STATIC_HOST . '/swf_v2/piao6Sns.swf?v=' . time();

$bgMusic = STATIC_HOST . '/swf_v2/sound1.mp3?v=2010111701';

// interface list
$interface = array(
    'swfHostURL'        => STATIC_HOST . '/swf_v2/',
    'jpgHostURL'        => STATIC_HOST . '/jpg/',
    'interfaceHostURL'  => HOST . '/',
    'loadFriends'       => 'apiv2/getfriends',
    'loadInit'          => 'apiv2/inituser?v=2011012101',
    'loadIsland'        => 'apiv2/initisland',
    'loadDock'          => 'apiv2/initdock',
    'recive'            => 'apiv2/receiveboat',
    'steal'             => 'apiv2/moochvisitor',
    'dockUpgrade'       => 'apiv2/addboat',
    'loadShop'          => 'apiv2/loadshop',
    'loadItems'         => 'apiv2/loaditems',
    'saleItems'         => 'apiv2/saleitem',
    'useItem'           => 'apiv2/usecard',
    'buyItem'           => 'apiv2/buyitem',
    'saveDiy'           => 'apiv2/diyisland',
    'loadDiary'         => 'apiv2/readfeed',
    'loadUserInfo'      => 'apiv2/inituserinfo',
    'changeHelp'        => 'apiv2/changehelp',
    'buildingPay'       => 'apiv2/harvestplant',
    'takeBuildingEvent' => 'apiv2/manageplant',
    'buildingUpgrade'   => 'apiv2/upgradeplant',
    'buildingSteal'     => 'apiv2/moochplant',
    'readTask'          => 'apiv2/readtask',
    'finishTask'        => 'apiv2/finishtask',
    'loadTitles'        => 'apiv2/readtitle',
    'selectTitle'       => 'apiv2/changetitle',
    'loadBoatClassState'=> 'apiv2/readship',
    'selectBoat'        => 'apiv2/changeship',
    'unLockBoat'        => 'apiv2/unlockship',
    'loadRemind'        => 'apiv2/readremind',
    'sendRemind'        => 'apiv2/addremind',
	'getGemNum'			=> 'apiv2/getgold',
	'getGiftList'		=> 'apiv2/getgiftpackagelist',
	'openPack'			=> 'apiv2/opengiftpackage',
	'updateGiftNum'		=> 'apiv2/getgiftpackagenum',
	'useAllPay'			=> 'apiv2/harvestallplant',
	'cdkExchange'		=> 'event/checkcdkey',
	'evtgainlevtengift'	=> 'event/tenlvlaward',
	'goldegg'			=> 'event/goldegg',
	'testGift'			=> 'event/testgift',
	'qqGet5DayAward'	=> 'event/active5day',
	'qqGetSignDay'		=> 'event/getactiveday',
	'collectCatsItems'	=> 'event/getkitteninfo',
	'exchangeCat'		=> 'event/exchangekitten',
	'exchangeFinal'		=> 'event/exchangekittenfinal',
	'readYaoQingState'	=> 'event/getinviteflowstate',
	'getYaoQingStepAward'	=> 'event/inviteaward',
	'OpenBrideToGetFBNums'     	=> 'event/loadnewyearitem',
	'OpenBrideToGetUsers'     	=> 'event/loadnewyearawardlist',
	'OpenBrideToOpenBride'     	=> 'event/opennewyearredpaper',
	'OpenBrideToCheckFLS'     	=> 'event/checknewyeargained',
	'OpenBrideToTranslate'     	=> 'event/exchangenewyearaward',
	'exChangeTuZiCdKey'			=> 'event/cdkeyrabbit',
	'buySalesPack'     			=> 'event/salemall',
	'caishenTodayInfo'     		=> 'event/getwealthgod',
	'caishen'     				=> 'event/wealthgod'
);

$swfResult = array(
    'swfs'      => $swfList,
    'otherSwfs' => $otherSwfs,
    'mainswf'   => $mainswf,
    'bgMusic'	=> $bgMusic,
    'interface' => $interface
);

