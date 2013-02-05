<?php

class Hapyfish2_Island_Bll_Activity
{
    protected static $template = array
        (
            'USER_LEVEL_UP'     => array('body' => '玩海岛的人有木有？{*user*}在欢乐海岛又升级了~还不抓紧，也和TA比一比？！', 'image' => 'yonghushengji.gif'),
            'ISLAND_LEVEL_UP'   => array('body' => '{*user*}的欢乐海岛又变大了，你的海岛咋样？敢不敢比一比谁的大？','image' => 'daoyukuozhan.gif'),
            'BUILDING_LEVEL_UP' => array('body' => '房子不涨价是个幻想~海岛建筑升星星是理想~{*user*}的建筑都升了，你的呢？','image' => 'sheshishengji.gif'),
            'BOAT_LEVEL_UP'     => array('body' => '车子房子不是问题，游艇你有木有？敢不敢比比谁的游艇等级高？','image' => 'chuanshengji.gif'),
            'DOCK_EXPANSION'    => array('body' => '欢乐海岛不讲计划生育，我们欢迎游客光临海岛~比如{*user*}就做的很好嘛~~加船位，拉游客~大家都要给力哦','image' => 'chuanweishengji.gif'),
            'MISSION_COMPLETE'  => array('body' => '读书要考试~工作要绩效~玩个游戏还要做任务~完成欢乐海岛任务的人伤不起啊','image' => 'jianshe.gif'),
            'USER_OBTAIN_TITLE' => array('body' => '{*user*}现在是{*title*}啦！你没看懂没关系，来欢乐海岛就明白了！敢不敢来啊','image' => 'huodechenghao.gif'),
        	'DAMAGE_PLANT'	    => array('body' => '一个笨拉灯倒下了，千万个{*user*}站起来了！{*user*}在欢乐海岛中破坏了{*owner*}的建筑！', 'image' => 'pohuai.gif'),
        	'OPEN_BOX'          => array('body' => '{*user*}跟随杰克船长探险，得到了一个超级宝箱，需要你的帮忙才能打开！快来帮帮他吧！你也能分得一份财宝噢！','image' => 'baoxiang.gif'),
        	'STROM_FEED'	    => array('body' => '{*user*}的小岛上突然来临“风暴”啦！朋友们快来帮帮我！讨厌的“风暴”又来了，大家快点来赶走它吧，！阳光沙滩等你来哦','image' =>'wuyun.gif'),
        	'USER_JOIN'         => array('body' => '阳光？沙滩？美女？帅哥！尽在欢乐海岛！赶快加入吧~', 'image'=>'join.jpg'),
        	'QIXI_SHRE'         => array('body' =>'{*user*}，在快乐岛主的七夕节活动中获得活动奖励{*name*}，感谢大家对他的 帮助，欢迎大家来看看','image'=>'qixi.jpg'),
        	'QIXI_WANT'         => array('body' =>'我在参加快乐岛主七夕情人节“鹊桥之恋”活动，帮忙送我一根羽毛 吧','image'=>'qixi.jpg'),
        	'QIXI_GIFT'         => array('body' =>'{*user*}在【欢乐海岛】浪漫七夕活动“鹊桥之恋”中获得—七夕祝福礼包哦~ 7夕节？你还是一个人！进入欢乐海岛乐乐陪你一起过七夕，更有祝福礼包等你拿','image'=>'qixi.jpg'),
        	'XIAO_FEI'         	=> array('body' =>'{*user*}在【欢乐海岛】中获得了兔兔宝贝箱一个，快去看看里面都有什么哦~点击链接进入游戏，阳光沙滩美女畅游海岛咯','image'=>'exchange.jpg'),
        	'PANIC_BUY'         => array('body' => '我刚刚在【欢乐海岛】以吐血价抢到了{*sendname*}，还不快来占便宜~', 'image' => 'songli.gif'),
        	'PANIC_BOX'			=> array('body' => '买便宜东西竟然还送宝箱，更多实惠，尽在【欢乐海岛】', 'image' => 'songli.gif'),
        	'HALL_EXCHANGE'		=> array('body' => '万圣节，我和吸血鬼有个约会~！', 'image' => 'hall.png'),
        );

    public static function send($type, $actor, $data = null)
    {
		if(isset(self::$template[$type])) {
    		$rowUser = Hapyfish2_Platform_Bll_Factory::getUser($actor);
    		$data['user'] = $rowUser['nickname'];


	    		$tpl = self::$template[$type];
	            $imgUrl = STATIC_HOST . '/apps/island/images/feed/';
				$text = self::buildTemplate($tpl['body'], $data);
	            $feed = array(
	            	'body' => $text,
	            	'image' => $imgUrl.$tpl['image'],
	            );
	            return json_encode($feed);
			}
    	return null;
    }

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
		if($type == 'STROM_FEED'){
			$canSend = true;
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

    protected static function buildTemplate($tpl, $json_array)
    {
        foreach ($json_array as $k => $v) {
            $keys[] = '{*' . $k . '*}';
            $values[] = $v;
        }

        return str_replace($keys, $values, $tpl);
    }
}