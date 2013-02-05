/***要写明时间点，谁更新的，更新的目的及具体的sql语句****/

/** 2011/3/21 li wei xiong 用户库，用于记录从梦想花园导入的用户 ，领取的奖励，登录有奖*****/

CREATE TABLE `island_user_event_dreamgarden` (
  `uid` int(11) NOT NULL default '0',
  `time_at` int(11) NOT NULL default '0',
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
