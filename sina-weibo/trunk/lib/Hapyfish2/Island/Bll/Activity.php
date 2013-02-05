<?php

require_once(CONFIG_DIR . '/language.php');
class Hapyfish2_Island_Bll_Activity
{
    protected static $template = array
        (

            'USER_LEVEL_UP' => array(
            	'id'       		=> 1,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_01,//'{_USER_}的小岛升级了！去游览还能拿到免费礼物哦！快去看看吧！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'user_level_up.gif',
        		'templateContent' => '有胆你就来！'
            ),

            'ISLAND_LEVEL_UP' => array(
            	'id'       		=> 2,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_02,//'{_USER_}的小岛在他的努力下又变的更大啦！你们羡慕么~那一起来玩吧！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'island_level_up.gif',
            	'templateContent' => '敢不敢比一比！'
            ),

            'BUILDING_LEVEL_UP' => array(
            	'id'       		=> 3,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_03,//'{_USER_}的建筑又升级了，天啊~~~好漂亮！赶快去凑凑热闹吧！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'building_level_up.gif',
            	'templateContent' => '不要让我BS你哦~'
            ),

            'BOAT_LEVEL_UP' => array(
            	'id'       		=> 4,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_04,//'呃~小木筏？豪华游轮？让我们一起游览全世界！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'boat_level_up.gif',
          	    'templateContent' => '你的小破船不够看啦~'
            ),

            'DOCK_EXPANSION' => array(
            	'id'       		=> 5,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_05,//'{_USER_}的船位置增加了，又有机会拉人了~还等什么呢？',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'dock_expand.gif',
            	'templateContent' => '你有几个船位啊？'
            ),

            'BUILDING_MISSION_COMPLETE'  => array(
            	'id'       		=> 6,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_06,//'哇！又一座世界级建筑出现啦！你想知道是什么？去快乐岛主看看吧！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'building_mission_complete.gif',
            	'templateContent' => '快来看看吧！'
            ),

            'USER_OBTAIN_TITLE' => array(
            	'id'       		=> 7,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_07,//'{_USER_}居然获得了{*title*}称号，不要嫉妒了，加入快乐岛主一起努力吧！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'get_title.gif',
           		'templateContent' => '这个称号你有木有？'
            ),

            'DAILY_MISSION_COMPLETE' => array(
            	'id'       		=> 8,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_08,//'{_USER_}通过一天的努力，所有日常任务都完成了哦！鼓掌~',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'daily_mission_complete.gif',
            	'templateContent' => '努力就有回报，不要羡慕我哦！'
            ),

            'GOD_POOR_CARD' => array(
            	'id'       		=> 9,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_09,//'{_USER_}快乐岛主受到了穷神的骚扰，快去用送神卡帮帮他吧！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'god_card.gif',
            	'templateContent' => '快来帮帮忙~'
            ),

            'Dream_Garden_User_Award' => array(
            	'id'       		=> 10,
            	'send'     		=> true,
                'limit'    		=> false,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_10,//'{_USER_}在快乐岛主中领取了限时“惊喜大礼包”价值10RMB，升级更快，更给力“领先”从现在开始！',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'join.gif',
            	'templateContent' => '不要羡慕我，自己努力哦！'
            ),
            'Starfish_word' =>array(
            	'id'			=> 11,
            	'send'			=> true,
            	'limit'			=> false,
            	'text'			=> LANG_PLATFORM_FEED_MSG_11,//'{_USER_}在快乐岛主中兑换了神秘海星礼物哦，你想知道是什么吗？快来看看吧！',
            	'linktext'		=> '开始游戏',
            	'image'			=> 'starfish.jpg',
          		'templateContent' => '想来看看吗？'
            ),

			'TEAMBUY_FEED' => array(
            	'id'       		=> 12,
            	'send'     		=> true,
                'limit'    		=> false,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_12,
                'linktext'		=> '开始游戏',
            	'image'    		=> 'teambuy.jpg',
            	'templateContent' => '快来参加吧！'
            ),

			'STROM_FEED' => array(
            	'id'       		=> 13,
            	'send'     		=> true,
                'limit'    		=> false,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_13,
                'linktext'		=> '帮帮他',
            	'image'    		=> 'flashstrom.jpg',
            	'templateContent' => '快来帮帮忙~'
            ),
            'OPEN_BOX' => array(
            	'id'       		=> 14,
            	'send'     		=> true,
                'limit'    		=> false,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_14,
                'linktext'		=> '帮帮他',
            	'image'    		=> 'baoxiang.jpg',
            	'templateContent' => '快来帮帮忙~'
            ),
             'USER_JOIN' => array(
            	'id'       		=> 15,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_00,//'阳光！沙滩！尽在快乐岛主！快来加入吧',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'join.gif',
            	'templateContent' => '快来一起玩吧~'
            ),
            'QIXI_SHRE' =>array(
            	'id'       		=> 16,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_15,//'阳光！沙滩！尽在快乐岛主！快来加入吧',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'qixi.jpg',
            	'templateContent' => '快来一起玩吧~'
            ),
            'QIXI_WANT' => array(
            	'id'       		=> 17,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_16,//'阳光！沙滩！尽在快乐岛主！快来加入吧',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'qixi.jpg',
            	'templateContent' => '快来一起玩吧~'
            ),
            'GUOQING' => array(
            	'id'       		=> 18,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_17,//'阳光！沙滩！尽在快乐岛主！快来加入吧',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'guoqing.gif',
            	'templateContent' => '快来一起玩吧~'
            ),
           'HALL_EXCHANGE' => array(
            	'id'       		=> 17,
            	'send'     		=> true,
                'limit'    		=> true,
            	'text'    		=> LANG_PLATFORM_FEED_MSG_20,//'阳光！沙滩！尽在快乐岛主！快来加入吧',
                'linktext'		=> '开始游戏',
            	'image'    		=> 'hall.png',
            	'templateContent' => '快来一起玩吧~'
            ),
        );

    public static function canSend($type, $actor, $time)
    {
        $canSend = true;
    	if (!$time) {
    		$time = time();
    	}
    	$lastSendTime = Hapyfish2_Island_Cache_Activity::getLastSendTime($actor);
    	if ($lastSendTime + 600 > $time) {
    		$canSend = false;
    	}

    	return $canSend;
    }

    public static function setSend($type, $actor, $time)
    {
        if (!$time) {
    		$time = time();
    	}
    	Hapyfish2_Island_Cache_Activity::setLastSendTime($actor, $time);
    }

    public static function send($type, $actor, $data = null, $target = null)
    {
    	if(SEND_ACTIVITY && isset(self::$template[$type])) {
    	    if ($type == 'USER_LEVEL_UP'
	    			|| $type == 'ISLAND_LEVEL_UP'
	    			|| $type == 'BUILDING_LEVEL_UP'
	    			|| $type == 'DOCK_EXPANSION'
	    			|| $type == 'DAILY_MISSION_COMPLETE'
	    			|| $type == 'USER_OBTAIN_TITLE'
	    			|| $type == 'GOD_POOR_CARD'
	    			|| $type == 'Dream_Garden_User_Award'
	    			|| $type == 'Starfish_word'
	    			|| $type == 'STROM_FEED'
					|| $type == 'TEAMBUY_FEED'
					|| $type == 'OPEN_BOX'
					|| $type == 'QIXI_SHRE'
					|| $type == 'GUOQING'
					|| $type == 'HALL_EXCHANGE'
					) {

    			/*$rowUser = Hapyfish2_Platform_Bll_User::getUser($actor);
    			$formatName = $rowUser['name'];
    			if ($rowUser['name'] && mb_strlen($rowUser['name'], 'UTF-8') > 10) {
    			    $formatName = mb_substr($rowUser['name'], 0, 6, 'UTF-8');
    			    $formatName .= '...';
    			}
    			$data['user'] = $formatName;*/
				$data['user'] = 'TA';
    		}

    		$tpl = self::$template[$type];
    	    $send = $tpl['send'];
    	    $time = time();
            if ($send && $tpl['limit']) {
//				$send = self::canSend($type, $actor, $time);
            }

			if ($send) {
                $imgUrl = STATIC_HOST . '/apps/island/images/feed/';

                $text = self::buildTemplate($tpl['text'], $data);
                $linktext= $tpl['linktext'];

                $feed = array(
                    'text' => $text,
                	'img' => $imgUrl . $tpl['image'],
                	'linktext' => $linktext,
                	'templateContent' => $tpl['templateContent']
                );

                if ($tpl['limit']) {
                	self::setSend($type, $actor, $time);
                }

                return json_encode($feed);
            }
    	}

    	return null;
    }

    protected static function buildTemplate($tpl, $json_array)
    {
		if (empty($json_array)) {
        	return $tpl;
        }

    	foreach ($json_array as $k => $v) {
            $keys[] = '{*' . $k . '*}';
            $values[] = $v;
        }

        return str_replace($keys, $values, $tpl);
    }
}