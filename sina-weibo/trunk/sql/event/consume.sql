set names 'utf8';
DROP TABLE IF EXISTS `island_consume_exchange`;

CREATE TABLE `island_consume_exchange` (
  `window` varchar(100) NOT NULL COMMENT '窗口',
  `cid` int(11) NOT NULL COMMENT '兑换物品id',
  `things` varchar(100) DEFAULT NULL COMMENT '物品名称',
  `gold` int(11) DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  PRIMARY KEY (`window`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `island_user_event_consume_exchange`;

CREATE TABLE `island_user_event_consume_exchange` (
  `uid` int(11) NOT NULL,
  `step` int(4) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  KEY `uid` (`uid`,`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;