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
-- Table structure for table `island_card`
--

DROP TABLE IF EXISTS `island_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `island_card` (
  `cid` int(11) NOT NULL COMMENT '道具id',
  `name` varchar(200) DEFAULT NULL COMMENT '道具名称',
  `class_name` varchar(200) DEFAULT NULL COMMENT '道具类名',
  `introduce` varchar(200) DEFAULT NULL COMMENT '介绍',
  `price` int(11) DEFAULT NULL COMMENT '购买价格',
  `price_type` tinyint(4) DEFAULT NULL COMMENT '购买币种,1:coin,2:gold',
  `cheap_price` int(11) DEFAULT '0' COMMENT '折扣价格',
  `cheap_start_time` int(11) DEFAULT '0' COMMENT '开始折扣时间',
  `cheap_end_time` int(11) DEFAULT '0' COMMENT '结束折扣时间',
  `sale_price` int(11) DEFAULT NULL COMMENT '售出价格',
  `add_exp` int(11) DEFAULT NULL COMMENT '增加经验值',
  `need_level` int(11) DEFAULT NULL COMMENT '需要等级',
  `item_type` tinyint(4) DEFAULT '41' COMMENT '41:功能道具',
  `plant_level` tinyint(4) DEFAULT '0',
  `new` tinyint(4) DEFAULT '0' COMMENT '是否新商品,0:非新,1:新  ',
  `can_buy` tinyint(4) DEFAULT '1' COMMENT '是否可以在商店购买,1:可以,0:不可以',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `island_card`
--

LOCK TABLES `island_card` WRITE;
/*!40000 ALTER TABLE `island_card` DISABLE KEYS */;
INSERT INTO `island_card` VALUES (26241,'船只加速卡I','itemcard.1.jiasu1','减少自己船的到达时间10分钟，每船每轮1次。',20,1,0,0,0,4,0,1,41,0,0,1),(26341,'船只加速卡II','itemcard.1.jiasu2','减少自己船的到达时间50分钟',1,2,0,0,0,200,0,1,41,0,0,1),(26441,'船只加速卡III','itemcard.1.jiasu3','减少自己船的到达时间2.5小时。',2,2,0,0,0,400,0,1,41,0,0,1),(26541,'设施加时卡I','itemcard.1.yanshi1','延长自己设施结算时间+3小时。',1,2,0,0,0,200,0,1,41,0,0,1),(26641,'设施加时卡II','itemcard.1.yanshi2','延长自己设施结算时间+6小时。',2,2,0,0,0,400,0,1,41,0,0,1),(26741,'设施破坏卡','itemcard.1.pohuai','破坏好友设施，出现故障。',200,1,0,0,0,40,0,10,41,0,0,1),(26841,'道具防御卡','itemcard.1.fangyu','12小时防御不利道具卡影响。',2000,1,0,0,0,400,0,10,41,0,0,1),(26941,'流氓抢夺卡','itemcard.1.qiangduo','直接获取好友当前金币的1%',4,2,0,0,0,800,0,10,41,0,0,1),(27041,'设施稽查卡','itemcard.1.jiucha','随机罚好友50,100,500金币',3,2,0,0,0,1,0,10,41,0,0,1),(27141,'码头保安卡','itemcard.1.baoan','6小时防御好友来船坞拉客',2,2,0,0,0,400,0,1,41,0,0,1),(56641,'2星建设卡','itemcard.1.jianshe2','免费升级1星设施至2星',999,2,0,0,0,0,0,3,41,1,0,0),(56741,'3星建设卡','itemcard.1.jianshe3','免费升级2星设施至3星',999,2,0,0,0,0,0,6,41,2,0,0),(56841,'4星建设卡','itemcard.1.jianshe4','免费升级3星设施至4星',999,2,0,0,0,0,0,9,41,3,0,0),(56941,'5星建设卡','itemcard.1.jianshe5','免费升级4星设施至5星',999,2,0,0,0,0,0,12,41,4,0,0),(67141,'送神卡','itemcard.1.songsheng','50%机率帮好友把穷神送走',1000,1,0,0,0,0,0,1,41,0,0,1),(67241,'请神卡','itemcard.1.qingsheng','把好友的财神仙请过来',2,2,0,0,0,0,0,7,41,0,0,1),(67341,'财神卡','itemcard.1.caisheng','2小时增加好友设施结算收入10%',1000,1,0,0,0,0,0,7,41,0,0,1),(67441,'一键收取卡','itemcard.1.yijianshou','一次收取自己岛所有金币',1,2,0,0,0,0,0,12,41,0,0,0),(67541,'超级防御卡','itemcard.1.cjfangyu','30小时防御不利道具卡影响。',1,2,0,0,0,0,0,10,41,0,0,1),(74841,'双倍经验卡','itemcard.1.expdouble','2小时内接船、设施营业经验加倍',999,2,0,0,0,0,0,5,41,0,0,0),(86241,'宝箱钥匙','itemcard.1.bxyaoshi','用于开启宝箱的神秘钥匙',5,2,0,0,0,0,0,1,41,0,0,0);
/*!40000 ALTER TABLE `island_card` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-06-10 16:11:49
