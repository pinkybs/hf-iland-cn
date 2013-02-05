-- MySQL dump 10.13  Distrib 5.1.54, for unknown-linux-gnu (x86_64)
--
-- Host: localhost    Database: kaixin_island_basic
-- ------------------------------------------------------
-- Server version	5.1.54community-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `island_background`
--

DROP TABLE IF EXISTS `island_background`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `island_background` (
  `bgid` int(11) NOT NULL COMMENT '海岛背景id',
  `name` varchar(200) DEFAULT NULL COMMENT '名称',
  `price` int(11) DEFAULT NULL COMMENT '购买价格',
  `price_type` tinyint(4) DEFAULT NULL COMMENT '购买币种,1:coin,2:gold',
  `cheap_price` int(11) DEFAULT '0' COMMENT '折扣价格',
  `cheap_start_time` int(11) DEFAULT '0' COMMENT '开始折扣时间',
  `cheap_end_time` int(11) DEFAULT '0' COMMENT '结束折扣时间',
  `sale_price` int(11) DEFAULT NULL COMMENT '售出价格',
  `introduce` varchar(200) DEFAULT NULL COMMENT '介绍',
  `class_name` varchar(200) DEFAULT NULL COMMENT '图像素材',
  `need_level` int(11) DEFAULT NULL COMMENT '需要等级',
  `add_praise` int(11) DEFAULT '0' COMMENT '好评度增加数',
  `item_type` tinyint(4) DEFAULT NULL COMMENT '11:岛,12:天,13:海,14:船坞',
  `new` tinyint(4) DEFAULT '0' COMMENT '是否新商品,0:非新,1:新  ',
  `can_buy` tinyint(4) DEFAULT '1' COMMENT '是否可以在商店购买,1:可以,0:不可以',
  PRIMARY KEY (`bgid`),
  KEY `need_level` (`need_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `island_background`
--

LOCK TABLES `island_background` WRITE;
/*!40000 ALTER TABLE `island_background` DISABLE KEYS */;
INSERT INTO `island_background` VALUES (22213,'爽蓝海水',0,1,0,0,0,0,NULL,'sea.1.shuanglan',1,1,13,0,0),(22313,'海星岛-海',1,2,0,0,0,200,NULL,'sea.1.wuxing',1,1,13,0,1),(22413,'爱之海',1,2,0,0,0,200,NULL,'sea.1.aizhihai',1,1,13,0,1),(22513,'彩虹岛-海',1,2,0,0,0,200,NULL,'sea.1.caihong',1,1,13,0,1),(22613,'和式岛-海',1,2,0,0,0,200,NULL,'sea.1.heshi',1,1,13,0,1),(22713,'音符海',1,2,0,0,0,200,NULL,'sea.1.yinfu',1,1,13,0,1),(22813,'乌龟岛-海',1,2,0,0,0,200,NULL,'sea.1.wugui',1,1,13,0,1),(22913,'外星岛-海',1,2,0,0,0,200,NULL,'sea.1.waixing',1,1,13,0,1),(23013,'救生圈岛-海洋',0,1,0,0,0,0,NULL,'sea.1.jiushengquan',1,1,13,0,0),(23113,'游乐场海洋',0,1,0,0,0,0,NULL,'sea.1.youle',1,1,13,0,0),(23212,'基本天',0,1,0,0,0,0,NULL,'sky.1.jiben',1,1,12,0,0),(23312,'丘比特天空',1,2,0,0,0,200,NULL,'sky.1.qiubite',1,1,12,0,0),(23412,'紫色天空',0,2,0,0,0,0,NULL,'sky.1.zise',1,1,12,0,0),(23512,'救生圈岛-天',1,2,0,0,0,200,NULL,'sky.1.jiushengquan',1,1,12,0,1),(23612,'漫天飞雪',1,2,0,0,0,200,NULL,'sky.1.maitianfeixue',1,1,12,0,1),(23712,'乌龟岛-天',1,2,0,0,0,200,NULL,'sky.1.wugui',1,1,12,0,1),(23812,'兔月夜',0,1,0,0,0,0,NULL,'sky.1.tuyueye',1,1,12,0,0),(23912,'外星岛-天',1,2,0,0,0,200,NULL,'sky.1.waixing',1,1,12,0,1),(24012,'狼夜',1,2,0,0,0,200,NULL,'sky.1.langye',1,1,12,0,1),(24112,'祥云天空',1,2,0,0,0,200,NULL,'sky.1.xiangyun',1,1,12,0,1),(24212,'双鱼星域',1,2,0,0,0,200,NULL,'sky.1.shuangyuzuo',1,1,12,0,1),(24312,'水瓶星域',1,2,0,0,0,200,NULL,'sky.1.shuipingzuo',1,1,12,0,1),(24412,'射手星域',1,2,0,0,0,200,NULL,'sky.1.sheshouzuo',1,1,12,0,1),(24512,'天蝎星域',1,2,0,0,0,200,NULL,'sky.1.tianxiezuo',1,1,12,0,1),(24612,'天平星域',1,2,0,0,0,200,NULL,'sky.1.tianpingzuo',1,1,12,0,1),(24712,'处女星域',1,2,0,0,0,200,NULL,'sky.1.chunvzuo',1,1,12,0,1),(24812,'狮子星域',1,2,0,0,0,200,NULL,'sky.1.shizizuo',1,1,12,0,1),(24912,'巨蟹星域',1,2,0,0,0,200,NULL,'sky.1.juxiezuo',1,1,12,0,1),(25012,'摩羯星域',1,2,0,0,0,200,NULL,'sky.1.mojiezuo',1,1,12,0,1),(25112,'双子星域',1,2,0,0,0,200,NULL,'sky.1.shuangzizuo',1,1,12,0,1),(25212,'金牛星域',1,2,0,0,0,200,NULL,'sky.1.jinniuzuo',1,1,12,0,1),(25312,'白羊星域',1,2,0,0,0,200,NULL,'sky.1.baiyangzuo',1,1,12,0,1),(25411,'基本岛',0,1,0,0,0,0,NULL,'island.1.001',1,1,11,0,0),(25511,'三叶草岛-岛皮',1,2,0,0,0,200,NULL,'island.1.002',1,1,11,0,1),(25611,'苹果岛-岛皮',1,2,0,0,0,200,NULL,'island.1.003',1,1,11,0,1),(25711,'饼干岛-岛皮',1,2,0,0,0,200,NULL,'island.1.004',1,1,11,0,1),(25811,'外星岛-岛皮',1,2,0,0,0,200,NULL,'island.1.005',1,1,11,0,1),(25914,'标准码头',0,1,0,0,0,0,NULL,'dock.1.001',1,1,14,0,0),(26014,'蓝色码头',0,1,0,0,0,0,NULL,'dock.1.002',1,1,14,0,0),(26114,'粉红码头',1,2,0,0,0,200,NULL,'dock.1.003',1,1,14,0,1),(27211,'阿拉伯岛',2,2,0,0,0,400,NULL,'island.1.006',1,1,11,0,1),(27311,'彩虹岛',0,2,0,0,0,0,NULL,'island.1.007',1,1,11,0,0),(27411,'圣诞岛',2,2,0,0,0,400,NULL,'island.1.008',1,1,11,0,1),(27511,'橙色岛',2,2,0,0,0,400,NULL,'island.1.009',1,1,11,0,1),(27611,'黑胶碟岛',2,2,0,0,0,400,NULL,'island.1.010',1,1,11,0,1),(68813,'咖啡海',100,2,0,0,0,20000,NULL,'sea.1.kafei',6,1,13,1,0),(68911,'咖啡岛',100,2,0,0,0,20000,NULL,'island.1.019',6,1,11,1,0);
/*!40000 ALTER TABLE `island_background` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-06-10 16:11:36
