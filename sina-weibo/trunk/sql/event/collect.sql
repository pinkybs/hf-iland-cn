SET NAMES 'utf8';

CREATE TABLE `island_hash_collect` (
  `key` varchar(64) NOT NULL,
  `val` text,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert  into `island_hash_collect`(`key`,val) values ('normal_collectgift','a:5:{i:0;a:3:{s:4:\"name\";s:12:\"骷髅水手\";s:3:\"cid\";s:5:\"91932\";s:3:\"tip\";s:57:\"【收集任务】“骷髅水手”海盗宝箱中获得\";}i:1;a:3:{s:4:\"name\";s:12:\"章鱼水手\";s:3:\"cid\";s:5:\"92132\";s:3:\"tip\";s:57:\"【收集任务】“章鱼水手”海盗宝箱中获得\";}i:2;a:3:{s:4:\"name\";s:12:\"猴子水手\";s:3:\"cid\";s:5:\"91532\";s:3:\"tip\";s:57:\"【收集任务】“猴子水手”海盗宝箱中获得\";}i:3;a:3:{s:4:\"name\";s:9:\"海盗旗\";s:3:\"cid\";s:5:\"91421\";s:3:\"tip\";s:54:\"【收集任务】“海盗旗”海盗宝箱中获得\";}i:4;a:3:{s:4:\"name\";s:9:\"红海星\";s:3:\"cid\";s:5:\"50421\";s:3:\"tip\";s:63:\"【收集任务】“红海星”装饰商店购买即可获得\";}}'),('normal_jiangliid','92632'),('normal_time','a:2:{s:5:\"start\";i:1307616539;s:3:\"end\";i:1308499199;}'),('normal_xiaoxi','a:2:{s:5:\"tishi\";s:29:\"3星可升级建筑-黑珍珠\";s:3:\"zhu\";s:102:\"收集“骷髅水手，章鱼水手，猴子水手，海盗旗，红海星”即可获得黑珍珠号\";}');


CREATE TABLE `island_teambuy_info` (
  `gid` VARCHAR(11) NOT NULL COMMENT 'gid-数量',
  `name` VARCHAR(200) DEFAULT NULL COMMENT '名称',
  `start_time` INT(11) DEFAULT NULL COMMENT '参加开始时间',
  `ok_time` INT(11) DEFAULT NULL COMMENT '参加有效时间长度',
  `buy_time` INT(11) DEFAULT NULL COMMENT '购买时间长度',
  `max_price` VARCHAR(200) NOT NULL COMMENT '物品原价*价格类型:1-coin,2-gold',
  `min_price` VARCHAR(200) DEFAULT NULL COMMENT '最低价格*价格类型:1-coin,2-gold',
  `min_num` INT(11) DEFAULT NULL COMMENT '最少人数',
  `max_num` INT(11) DEFAULT NULL COMMENT '最高人数',
  `start_num` INT(11) DEFAULT '0' COMMENT '起始参加人数',
  `bec_num` INT(11) DEFAULT NULL COMMENT 'gold变为coin需要人数',
  `bec_price` INT(11) DEFAULT NULL COMMENT 'gold变为coin开始价格',
  `scale_gold` VARCHAR(200) DEFAULT NULL COMMENT '降价比例gold',
  `scale_coin` VARCHAR(200) DEFAULT NULL COMMENT '降价比例coin',
  `status` INT(2) DEFAULT '1' COMMENT '是否是现在团购的物品:-1不是,1是',
  PRIMARY KEY (`gid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;



INSERT  INTO `island_teambuy_info`(gid,`name`,start_time,ok_time,buy_time,max_price,min_price,min_num,max_num,start_num,bec_num,bec_price,scale_gold,scale_coin,`status`) VALUES ('93632*1','龙舟',1307851200,72,48,'28*2','8*2',1,10000,203,0,0,'1:500','',1);

CREATE TABLE `island_user_teambuy` (
  `uid` int(11) NOT NULL,
  `status` int(2) DEFAULT '-1' COMMENT '用户是否已经购买物品:-1没有,1已经购买',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;