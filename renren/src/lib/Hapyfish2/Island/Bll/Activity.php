<?php

require_once(CONFIG_DIR . '/language.php');
class Hapyfish2_Island_Bll_Activity
{
    protected static $template = array
        (
            'USER_LEVEL_UP' => array(
            	'send'     		=> true,
                'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_14,
                'body'			=> LANG_PLATFORM_FEED_MSG_15,
        		'user_message' 	=> LANG_PLATFORM_FEED_MSG_16,
            	'image'    		=> 'user_level_up.gif'
            ),

            'BUILDING_LEVEL_UP' => array(
            	'send'     		=> true,
                'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_17,
                'body'     		=> LANG_PLATFORM_FEED_MSG_18,
            	'user_message' 	=> LANG_PLATFORM_FEED_MSG_19,
            	'image'    		=> 'building_level_up.gif'
            ),

            'BOAT_LEVEL_UP' => array(
            	'send'     		=> true,
                'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_20,
                'body'     		=> LANG_PLATFORM_FEED_MSG_21,
            	'user_message'	=> LANG_PLATFORM_FEED_MSG_22,
            	'image'    		=> 'boat_level_up.gif'
            ),

            'DOCK_EXPANSION' => array(
            	'send'     		=> true,
            	'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_23,
                'body'     		=> LANG_PLATFORM_FEED_MSG_24,
            	'user_message' 	=> LANG_PLATFORM_FEED_MSG_25,
            	'image'    		=> 'dock_expansion.gif'
            ),

            'MISSION_COMPLETE'  => array(
            	'send'     		=> true,
            	'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_26,
                'body'     		=> LANG_PLATFORM_FEED_MSG_27,
            	'user_message' 	=> LANG_PLATFORM_FEED_MSG_28,
            	'image'    		=> 'mission_complete.gif'
            ),

            'USER_OBTAIN_TITLE' => array(
            	'send'     		=> true,
            	'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_07,
                'body'     		=> LANG_PLATFORM_FEED_MSG_29,
            	'user_message' 	=> '',
            	'image'    		=> 'user_obtain_title.gif'
            ),

            'GOD_POOR_CARD' => array(
            	'send'     		=> true,
            	'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_30,
                'body'     		=> LANG_PLATFORM_FEED_MSG_09,
            	'user_message' 	=> LANG_PLATFORM_FEED_MSG_31,
            	'image'    		=> 'god_card.gif'
            ),
            
			'Dream_Garden_User_Award' => array(
            	'send'     		=> true,
            	'limit'    		=> true,
            	'id'       		=> 1,
            	'title'    		=> LANG_PLATFORM_FEED_MSG_32,
                'body'     		=> LANG_PLATFORM_FEED_MSG_10,
            	'user_message' 	=> LANG_PLATFORM_FEED_MSG_33,
            	'image'    		=> 'join.gif'
            )
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
	    			|| $type == 'Starfish_word'
	    			|| $type == 'STROM_FEED') {

    			$rowUser = Hapyfish2_Platform_Bll_User::getUser($actor);
    			$data['user'] = $rowUser['name'];
    		}

    		$tpl = self::$template[$type];
    	    $send = $tpl['send'];
    	    $time = time();
            if ($send && $tpl['limit']) {
                $send = self::canSend($type, $actor, $time);
            }

			if ($send) {
                $imgUrl = STATIC_HOST . '/apps/island/images/feed/';
                


                $appUrl = 'http://apps.renren.com/'.APP_NAME;
				$app_link = '<a href=\'' . $appUrl . '\'>'. LANG_PLATFORM_BASE_TXT_00 .'</a>';

				if ($data) {
                    $data['app_link'] = $app_link;
                } else {
                    $data = array('app_link' => $app_link);
                }
                
				$text = self::buildTemplate($tpl['title'], $data);
                $linktext= $tpl['linktext'];
				
                $feed = array(
                    'template_bundle_id' => $tpl['id'],
                    'template_data' => array(
                        'images' => array(array('src' => $imgUrl . $tpl['image'], 'href' => $appUrl)),
                        'title' => $text,
                        'body' => $tpl['body']
                    ),
                    'body_general' => '',
                    'user_message_prompt' => '',
                    'user_message' => $tpl['user_message']
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