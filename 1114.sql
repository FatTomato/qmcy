-- MySQL dump 10.13  Distrib 5.7.14, for Win64 (x86_64)
--
-- Host: localhost    Database: xcx
-- ------------------------------------------------------
-- Server version	5.7.14

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
-- Table structure for table `xcx_ad`
--

DROP TABLE IF EXISTS `xcx_ad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_ad` (
  `ad_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `ad_name` varchar(255) NOT NULL COMMENT '广告名称',
  `ad_content` text COMMENT '广告内容',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`ad_id`),
  KEY `ad_name` (`ad_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_ad`
--

LOCK TABLES `xcx_ad` WRITE;
/*!40000 ALTER TABLE `xcx_ad` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_ad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_ads`
--

DROP TABLE IF EXISTS `xcx_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_ads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned DEFAULT '0' COMMENT '发表者id',
  `shop_id` int(11) unsigned NOT NULL DEFAULT '0',
  `post_keywords` varchar(150) NOT NULL DEFAULT '' COMMENT 'seo keywords',
  `post_date` datetime DEFAULT '1000-01-01 00:00:00' COMMENT 'post创建日期，永久不变，一般不显示给用户',
  `post_content` longtext COMMENT 'post内容',
  `post_title` text COMMENT 'post标题',
  `post_excerpt` text COMMENT 'post摘要',
  `post_status` int(2) DEFAULT '1' COMMENT 'post状态，1已审核，0未审核',
  `post_discount` varchar(150) NOT NULL DEFAULT '' COMMENT '折扣信息',
  `start_time` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '活动开始时间',
  `end_time` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '活动结束时间',
  `post_expire` int(2) DEFAULT '1' COMMENT '1未过期，0已过期',
  `post_mime_type` varchar(100) DEFAULT '',
  `smeta` varchar(100) NOT NULL DEFAULT '' COMMENT '活动图片',
  `store_name` varchar(100) NOT NULL DEFAULT '' COMMENT '商家名称',
  `store_addr` varchar(100) NOT NULL DEFAULT '' COMMENT '商家地址',
  `store_contact` varchar(100) NOT NULL DEFAULT '' COMMENT '商家联系人',
  `store_phone` varchar(100) NOT NULL DEFAULT '' COMMENT '商家联系电话',
  `post_hits` varchar(5000) NOT NULL DEFAULT '',
  `post_like` varchar(5000) NOT NULL DEFAULT '',
  `istop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '置顶 1置顶； 0不置顶',
  `recommended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐 1推荐 0不推荐',
  `store_time` varchar(60) NOT NULL DEFAULT '' COMMENT 'shop_time',
  PRIMARY KEY (`id`),
  KEY `post_author` (`post_author`),
  KEY `end_time` (`end_time`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=435 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_ads`
--

LOCK TABLES `xcx_ads` WRITE;
/*!40000 ALTER TABLE `xcx_ads` DISABLE KEYS */;
INSERT INTO `xcx_ads` VALUES (432,1,0,'广告1','2017-10-26 20:46:36','<p>广告1</p>','广告1','广告1',1,'广告1','2017-10-26 20:46:00','2017-10-31 20:46:00',1,'','portal/20171026/59f1d93ecde70.png','广告1','广告1','广告1','广告1','1,3','',0,1,''),(433,1,0,'广告2','2017-10-26 20:47:02','<p>广告2</p>','广告2','广告2',1,'广告2','2017-10-26 20:47:00','2017-10-31 20:47:00',1,'','portal/20171026/59f1d95ce3962.png','广告2','广告2','广告2','广告2','','',1,1,''),(434,1,0,'广告3','2017-10-26 20:54:09','<p>广告3</p>','广告3','广告3',1,'广告3','2017-10-26 20:54:00','2017-10-31 20:54:00',1,'','portal/20171026/59f1db0370309.png','广告3','广告3','广告3','广告3','1,2,3','',0,1,'');
/*!40000 ALTER TABLE `xcx_ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_ads_relationships`
--

DROP TABLE IF EXISTS `xcx_ads_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_ads_relationships` (
  `adsid` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'ads表里广告id',
  `cg_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`adsid`),
  KEY `category_id` (`cg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Portal 广告分类对应表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_ads_relationships`
--

LOCK TABLES `xcx_ads_relationships` WRITE;
/*!40000 ALTER TABLE `xcx_ads_relationships` DISABLE KEYS */;
INSERT INTO `xcx_ads_relationships` VALUES (9,434,4,5,1),(7,432,5,3,1),(8,433,4,3,1);
/*!40000 ALTER TABLE `xcx_ads_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_asset`
--

DROP TABLE IF EXISTS `xcx_asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_asset` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `key` varchar(50) NOT NULL COMMENT '资源 key',
  `filename` varchar(50) DEFAULT NULL COMMENT '文件名',
  `filesize` int(11) DEFAULT NULL COMMENT '文件大小,单位Byte',
  `filepath` varchar(200) NOT NULL COMMENT '文件路径，相对于 upload 目录，可以为 url',
  `uploadtime` int(11) NOT NULL COMMENT '上传时间',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1：可用，0：删除，不可用',
  `meta` text COMMENT '其它详细信息，JSON格式',
  `suffix` varchar(50) DEFAULT NULL COMMENT '文件后缀名，不包括点',
  `download_times` int(11) NOT NULL DEFAULT '0' COMMENT '下载次数',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='资源表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_asset`
--

LOCK TABLES `xcx_asset` WRITE;
/*!40000 ALTER TABLE `xcx_asset` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_asset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_auth_access`
--

DROP TABLE IF EXISTS `xcx_auth_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_auth_access` (
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(255) NOT NULL COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) DEFAULT NULL COMMENT '权限规则分类，请加应用前缀,如admin_',
  KEY `role_id` (`role_id`),
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限授权表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_auth_access`
--

LOCK TABLES `xcx_auth_access` WRITE;
/*!40000 ALTER TABLE `xcx_auth_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_auth_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_auth_rule`
--

DROP TABLE IF EXISTS `xcx_auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` varchar(30) NOT NULL DEFAULT '1' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `param` varchar(255) DEFAULT NULL COMMENT '额外url参数',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=184 DEFAULT CHARSET=utf8 COMMENT='权限规则表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_auth_rule`
--

LOCK TABLES `xcx_auth_rule` WRITE;
/*!40000 ALTER TABLE `xcx_auth_rule` DISABLE KEYS */;
INSERT INTO `xcx_auth_rule` VALUES (1,'Admin','admin_url','admin/content/default',NULL,'内容管理',1,''),(2,'Api','admin_url','api/guestbookadmin/index',NULL,'所有留言',1,''),(3,'Api','admin_url','api/guestbookadmin/delete',NULL,'删除网站留言',1,''),(4,'Comment','admin_url','comment/commentadmin/index',NULL,'评论管理',1,''),(5,'Comment','admin_url','comment/commentadmin/delete',NULL,'删除评论',1,''),(6,'Comment','admin_url','comment/commentadmin/check',NULL,'评论审核',1,''),(7,'Portal','admin_url','portal/adminpost/index',NULL,'文章管理',1,''),(8,'Portal','admin_url','portal/adminpost/listorders',NULL,'文章排序',1,''),(9,'Portal','admin_url','portal/adminpost/top',NULL,'文章置顶',1,''),(10,'Portal','admin_url','portal/adminpost/recommend',NULL,'文章推荐',1,''),(11,'Portal','admin_url','portal/adminpost/move',NULL,'批量移动',1,''),(12,'Portal','admin_url','portal/adminpost/check',NULL,'文章审核',1,''),(13,'Portal','admin_url','portal/adminpost/delete',NULL,'删除文章',1,''),(14,'Portal','admin_url','portal/adminpost/edit',NULL,'编辑文章',1,''),(15,'Portal','admin_url','portal/adminpost/edit_post',NULL,'提交编辑',1,''),(16,'Portal','admin_url','portal/adminpost/add',NULL,'添加文章',1,''),(17,'Portal','admin_url','portal/adminpost/add_post',NULL,'提交添加',1,''),(18,'Portal','admin_url','portal/adminterm/index',NULL,'分类管理',1,''),(19,'Portal','admin_url','portal/adminterm/listorders',NULL,'文章分类排序',1,''),(20,'Portal','admin_url','portal/adminterm/delete',NULL,'删除分类',1,''),(21,'Portal','admin_url','portal/adminterm/edit',NULL,'编辑分类',1,''),(22,'Portal','admin_url','portal/adminterm/edit_post',NULL,'提交编辑',1,''),(23,'Portal','admin_url','portal/adminterm/add',NULL,'添加分类',1,''),(24,'Portal','admin_url','portal/adminterm/add_post',NULL,'提交添加',1,''),(25,'Portal','admin_url','portal/adminpage/index',NULL,'页面管理',1,''),(26,'Portal','admin_url','portal/adminpage/listorders',NULL,'页面排序',1,''),(27,'Portal','admin_url','portal/adminpage/delete',NULL,'删除页面',1,''),(28,'Portal','admin_url','portal/adminpage/edit',NULL,'编辑页面',1,''),(29,'Portal','admin_url','portal/adminpage/edit_post',NULL,'提交编辑',1,''),(30,'Portal','admin_url','portal/adminpage/add',NULL,'添加页面',1,''),(31,'Portal','admin_url','portal/adminpage/add_post',NULL,'提交添加',1,''),(32,'Admin','admin_url','admin/recycle/default',NULL,'回收站',1,''),(33,'Portal','admin_url','portal/adminpost/recyclebin',NULL,'文章回收',1,''),(34,'Portal','admin_url','portal/adminpost/restore',NULL,'文章还原',1,''),(35,'Portal','admin_url','portal/adminpost/clean',NULL,'彻底删除',1,''),(36,'Portal','admin_url','portal/adminpage/recyclebin',NULL,'页面回收',1,''),(37,'Portal','admin_url','portal/adminpage/clean',NULL,'彻底删除',1,''),(38,'Portal','admin_url','portal/adminpage/restore',NULL,'页面还原',1,''),(39,'Admin','admin_url','admin/extension/default',NULL,'扩展工具',1,''),(40,'Admin','admin_url','admin/backup/default',NULL,'备份管理',1,''),(41,'Admin','admin_url','admin/backup/restore',NULL,'数据还原',1,''),(42,'Admin','admin_url','admin/backup/index',NULL,'数据备份',1,''),(43,'Admin','admin_url','admin/backup/index_post',NULL,'提交数据备份',1,''),(44,'Admin','admin_url','admin/backup/download',NULL,'下载备份',1,''),(45,'Admin','admin_url','admin/backup/del_backup',NULL,'删除备份',1,''),(46,'Admin','admin_url','admin/backup/import',NULL,'数据备份导入',1,''),(47,'Admin','admin_url','admin/plugin/index',NULL,'插件管理',1,''),(48,'Admin','admin_url','admin/plugin/toggle',NULL,'插件启用切换',1,''),(49,'Admin','admin_url','admin/plugin/setting',NULL,'插件设置',1,''),(50,'Admin','admin_url','admin/plugin/setting_post',NULL,'插件设置提交',1,''),(51,'Admin','admin_url','admin/plugin/install',NULL,'插件安装',1,''),(52,'Admin','admin_url','admin/plugin/uninstall',NULL,'插件卸载',1,''),(53,'Admin','admin_url','admin/slide/default',NULL,'幻灯片',1,''),(54,'Admin','admin_url','admin/slide/index',NULL,'幻灯片管理',1,''),(55,'Admin','admin_url','admin/slide/listorders',NULL,'幻灯片排序',1,''),(56,'Admin','admin_url','admin/slide/toggle',NULL,'幻灯片显示切换',1,''),(57,'Admin','admin_url','admin/slide/delete',NULL,'删除幻灯片',1,''),(58,'Admin','admin_url','admin/slide/edit',NULL,'编辑幻灯片',1,''),(59,'Admin','admin_url','admin/slide/edit_post',NULL,'提交编辑',1,''),(60,'Admin','admin_url','admin/slide/add',NULL,'添加幻灯片',1,''),(61,'Admin','admin_url','admin/slide/add_post',NULL,'提交添加',1,''),(62,'Admin','admin_url','admin/slidecat/index',NULL,'幻灯片分类',1,''),(63,'Admin','admin_url','admin/slidecat/delete',NULL,'删除分类',1,''),(64,'Admin','admin_url','admin/slidecat/edit',NULL,'编辑分类',1,''),(65,'Admin','admin_url','admin/slidecat/edit_post',NULL,'提交编辑',1,''),(66,'Admin','admin_url','admin/slidecat/add',NULL,'添加分类',1,''),(67,'Admin','admin_url','admin/slidecat/add_post',NULL,'提交添加',1,''),(68,'Admin','admin_url','admin/ad/index',NULL,'网站广告',1,''),(69,'Admin','admin_url','admin/ad/toggle',NULL,'广告显示切换',1,''),(70,'Admin','admin_url','admin/ad/delete',NULL,'删除广告',1,''),(71,'Admin','admin_url','admin/ad/edit',NULL,'编辑广告',1,''),(72,'Admin','admin_url','admin/ad/edit_post',NULL,'提交编辑',1,''),(73,'Admin','admin_url','admin/ad/add',NULL,'添加广告',1,''),(74,'Admin','admin_url','admin/ad/add_post',NULL,'提交添加',1,''),(75,'Admin','admin_url','admin/link/index',NULL,'友情链接',1,''),(76,'Admin','admin_url','admin/link/listorders',NULL,'友情链接排序',1,''),(77,'Admin','admin_url','admin/link/toggle',NULL,'友链显示切换',1,''),(78,'Admin','admin_url','admin/link/delete',NULL,'删除友情链接',1,''),(79,'Admin','admin_url','admin/link/edit',NULL,'编辑友情链接',1,''),(80,'Admin','admin_url','admin/link/edit_post',NULL,'提交编辑',1,''),(81,'Admin','admin_url','admin/link/add',NULL,'添加友情链接',1,''),(82,'Admin','admin_url','admin/link/add_post',NULL,'提交添加',1,''),(83,'Api','admin_url','api/oauthadmin/setting',NULL,'第三方登陆',1,''),(84,'Api','admin_url','api/oauthadmin/setting_post',NULL,'提交设置',1,''),(85,'Admin','admin_url','admin/menu/default',NULL,'菜单管理',1,''),(86,'Admin','admin_url','admin/navcat/default1',NULL,'前台菜单',1,''),(87,'Admin','admin_url','admin/nav/index',NULL,'菜单管理',1,''),(88,'Admin','admin_url','admin/nav/listorders',NULL,'前台导航排序',1,''),(89,'Admin','admin_url','admin/nav/delete',NULL,'删除菜单',1,''),(90,'Admin','admin_url','admin/nav/edit',NULL,'编辑菜单',1,''),(91,'Admin','admin_url','admin/nav/edit_post',NULL,'提交编辑',1,''),(92,'Admin','admin_url','admin/nav/add',NULL,'添加菜单',1,''),(93,'Admin','admin_url','admin/nav/add_post',NULL,'提交添加',1,''),(94,'Admin','admin_url','admin/navcat/index',NULL,'菜单分类',1,''),(95,'Admin','admin_url','admin/navcat/delete',NULL,'删除分类',1,''),(96,'Admin','admin_url','admin/navcat/edit',NULL,'编辑分类',1,''),(97,'Admin','admin_url','admin/navcat/edit_post',NULL,'提交编辑',1,''),(98,'Admin','admin_url','admin/navcat/add',NULL,'添加分类',1,''),(99,'Admin','admin_url','admin/navcat/add_post',NULL,'提交添加',1,''),(100,'Admin','admin_url','admin/menu/index',NULL,'后台菜单',1,''),(101,'Admin','admin_url','admin/menu/add',NULL,'添加菜单',1,''),(102,'Admin','admin_url','admin/menu/add_post',NULL,'提交添加',1,''),(103,'Admin','admin_url','admin/menu/listorders',NULL,'后台菜单排序',1,''),(104,'Admin','admin_url','admin/menu/export_menu',NULL,'菜单备份',1,''),(105,'Admin','admin_url','admin/menu/edit',NULL,'编辑菜单',1,''),(106,'Admin','admin_url','admin/menu/edit_post',NULL,'提交编辑',1,''),(107,'Admin','admin_url','admin/menu/delete',NULL,'删除菜单',1,''),(108,'Admin','admin_url','admin/menu/lists',NULL,'所有菜单',1,''),(109,'Admin','admin_url','admin/setting/default',NULL,'设置',1,''),(110,'Admin','admin_url','admin/setting/userdefault',NULL,'个人信息',1,''),(111,'Admin','admin_url','admin/user/userinfo',NULL,'修改信息',1,''),(112,'Admin','admin_url','admin/user/userinfo_post',NULL,'修改信息提交',1,''),(113,'Admin','admin_url','admin/setting/password',NULL,'修改密码',1,''),(114,'Admin','admin_url','admin/setting/password_post',NULL,'提交修改',1,''),(115,'Admin','admin_url','admin/setting/site',NULL,'网站信息',1,''),(116,'Admin','admin_url','admin/setting/site_post',NULL,'提交修改',1,''),(117,'Admin','admin_url','admin/route/index',NULL,'路由列表',1,''),(118,'Admin','admin_url','admin/route/add',NULL,'路由添加',1,''),(119,'Admin','admin_url','admin/route/add_post',NULL,'路由添加提交',1,''),(120,'Admin','admin_url','admin/route/edit',NULL,'路由编辑',1,''),(121,'Admin','admin_url','admin/route/edit_post',NULL,'路由编辑提交',1,''),(122,'Admin','admin_url','admin/route/delete',NULL,'路由删除',1,''),(123,'Admin','admin_url','admin/route/ban',NULL,'路由禁止',1,''),(124,'Admin','admin_url','admin/route/open',NULL,'路由启用',1,''),(125,'Admin','admin_url','admin/route/listorders',NULL,'路由排序',1,''),(126,'Admin','admin_url','admin/mailer/default',NULL,'邮箱配置',1,''),(127,'Admin','admin_url','admin/mailer/index',NULL,'SMTP配置',1,''),(128,'Admin','admin_url','admin/mailer/index_post',NULL,'提交配置',1,''),(129,'Admin','admin_url','admin/mailer/active',NULL,'注册邮件模板',1,''),(130,'Admin','admin_url','admin/mailer/active_post',NULL,'提交模板',1,''),(131,'Admin','admin_url','admin/setting/clearcache',NULL,'清除缓存',1,''),(132,'User','admin_url','user/indexadmin/default',NULL,'用户管理',1,''),(133,'User','admin_url','user/indexadmin/default1',NULL,'用户组',1,''),(134,'User','admin_url','user/indexadmin/index',NULL,'本站用户',1,''),(135,'User','admin_url','user/indexadmin/ban',NULL,'拉黑会员',1,''),(136,'User','admin_url','user/indexadmin/cancelban',NULL,'启用会员',1,''),(137,'User','admin_url','user/oauthadmin/index',NULL,'第三方用户',1,''),(138,'User','admin_url','user/oauthadmin/delete',NULL,'第三方用户解绑',1,''),(139,'User','admin_url','user/indexadmin/default3',NULL,'管理组',1,''),(140,'Admin','admin_url','admin/rbac/index',NULL,'角色管理',1,''),(141,'Admin','admin_url','admin/rbac/member',NULL,'成员管理',1,''),(142,'Admin','admin_url','admin/rbac/authorize',NULL,'权限设置',1,''),(143,'Admin','admin_url','admin/rbac/authorize_post',NULL,'提交设置',1,''),(144,'Admin','admin_url','admin/rbac/roleedit',NULL,'编辑角色',1,''),(145,'Admin','admin_url','admin/rbac/roleedit_post',NULL,'提交编辑',1,''),(146,'Admin','admin_url','admin/rbac/roledelete',NULL,'删除角色',1,''),(147,'Admin','admin_url','admin/rbac/roleadd',NULL,'添加角色',1,''),(148,'Admin','admin_url','admin/rbac/roleadd_post',NULL,'提交添加',1,''),(149,'Admin','admin_url','admin/user/index',NULL,'管理员',1,''),(150,'Admin','admin_url','admin/user/delete',NULL,'删除管理员',1,''),(151,'Admin','admin_url','admin/user/edit',NULL,'管理员编辑',1,''),(152,'Admin','admin_url','admin/user/edit_post',NULL,'编辑提交',1,''),(153,'Admin','admin_url','admin/user/add',NULL,'管理员添加',1,''),(154,'Admin','admin_url','admin/user/add_post',NULL,'添加提交',1,''),(155,'Admin','admin_url','admin/plugin/update',NULL,'插件更新',1,''),(156,'Admin','admin_url','admin/storage/index',NULL,'文件存储',1,''),(157,'Admin','admin_url','admin/storage/setting_post',NULL,'文件存储设置提交',1,''),(158,'Admin','admin_url','admin/slide/ban',NULL,'禁用幻灯片',1,''),(159,'Admin','admin_url','admin/slide/cancelban',NULL,'启用幻灯片',1,''),(160,'Admin','admin_url','admin/user/ban',NULL,'禁用管理员',1,''),(161,'Admin','admin_url','admin/user/cancelban',NULL,'启用管理员',1,''),(162,'Demo','admin_url','demo/adminindex/index',NULL,'',1,''),(163,'Demo','admin_url','demo/adminindex/last',NULL,'',1,''),(166,'Admin','admin_url','admin/mailer/test',NULL,'测试邮件',1,''),(167,'Admin','admin_url','admin/setting/upload',NULL,'上传设置',1,''),(168,'Admin','admin_url','admin/setting/upload_post',NULL,'上传设置提交',1,''),(169,'Portal','admin_url','portal/adminpost/copy',NULL,'文章批量复制',1,''),(170,'Admin','admin_url','admin/menu/backup_menu',NULL,'备份菜单',1,''),(171,'Admin','admin_url','admin/menu/export_menu_lang',NULL,'导出后台菜单多语言包',1,''),(172,'Admin','admin_url','admin/menu/restore_menu',NULL,'还原菜单',1,''),(173,'Admin','admin_url','admin/menu/getactions',NULL,'导入新菜单',1,''),(174,'Portal','admin_url','portal/admininfo/default',NULL,'信息管理',1,''),(175,'Portal','admin_url','portal/admincicle/default',NULL,'圈子管理',1,''),(176,'Portal','admin_url','portal/admincategory/index',NULL,'分类管理',1,''),(177,'Portal','admin_url','portal/admincicleterm/index',NULL,'分类管理',1,''),(178,'Portal','admin_url','portal/admininfo/index',NULL,'信息管理',1,''),(179,'Portal','admin_url','portal/adminciclepost/index',NULL,'圈子管理',1,''),(180,'Portal','admin_url','portal/adminmember/index',NULL,'会员管理',1,''),(181,'Portal','admin_url','portal/admincategorys/index',NULL,'分类管理',1,''),(182,'Portal','admin_url','portal/adminads/index',NULL,'广告管理',1,''),(183,'Portal','admin_url','portal/adminshop/index',NULL,'店铺管理',1,'');
/*!40000 ALTER TABLE `xcx_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_categorys`
--

DROP TABLE IF EXISTS `xcx_categorys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_categorys` (
  `cg_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(200) DEFAULT NULL COMMENT '分类名称',
  `slug` varchar(200) DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-信息分类；1-圈子分类',
  `taxonomy` varchar(32) DEFAULT NULL COMMENT '分类类型',
  `description` longtext COMMENT '分类描述',
  `parent` bigint(20) unsigned DEFAULT '0' COMMENT '分类父id',
  `icon` varchar(100) NOT NULL COMMENT '分类icon',
  `count` bigint(20) DEFAULT '0' COMMENT '分类文章数',
  `path` varchar(500) DEFAULT NULL COMMENT '分类层级关系路径',
  `seo_title` varchar(500) DEFAULT NULL,
  `seo_keywords` varchar(500) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `list_tpl` varchar(50) DEFAULT NULL COMMENT '分类列表模板',
  `one_tpl` varchar(50) DEFAULT NULL COMMENT '分类文章页模板',
  `listorder` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`cg_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Portal 分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_categorys`
--

LOCK TABLES `xcx_categorys` WRITE;
/*!40000 ALTER TABLE `xcx_categorys` DISABLE KEYS */;
INSERT INTO `xcx_categorys` VALUES (4,'游泳健身','',0,NULL,'游泳健身游泳健身',0,'portal/20171024/59eedc917024a.png',0,'0-4',NULL,NULL,NULL,NULL,NULL,5,1),(5,'王者荣耀','',1,NULL,'王者荣耀',0,'portal/20171024/59eedcbf1c8f5.png',0,'0-5',NULL,NULL,NULL,NULL,NULL,3,1),(6,'舞动人生','',1,NULL,'舞动人生舞动人生',0,'portal/20171029/59f5814d652b1.png',0,'0-6',NULL,NULL,NULL,NULL,NULL,0,1),(7,'房屋租售','',0,NULL,'房屋租售房屋租售',0,'portal/20171102/59fae212d739a.png',0,'0-7',NULL,NULL,NULL,NULL,NULL,0,1),(8,'买房租房','',0,NULL,'买房租房买房租房',7,'portal/20171102/59fae23486abd.png',0,'0-7-8',NULL,NULL,NULL,NULL,NULL,0,1),(9,'卖房出租','',0,NULL,'卖房出租卖房出租',7,'portal/20171102/59fae25b070d8.png',0,'0-7-9',NULL,NULL,NULL,NULL,NULL,0,1);
/*!40000 ALTER TABLE `xcx_categorys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_cicles_relationships`
--

DROP TABLE IF EXISTS `xcx_cicles_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_cicles_relationships` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'member_id',
  `cg_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '圈子id',
  `cg_name` varchar(200) NOT NULL DEFAULT '' COMMENT '圈子name',
  `addtime` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '加入时间',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1已加入，0已退出',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `category_id` (`cg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='圈子-用户关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_cicles_relationships`
--

LOCK TABLES `xcx_cicles_relationships` WRITE;
/*!40000 ALTER TABLE `xcx_cicles_relationships` DISABLE KEYS */;
INSERT INTO `xcx_cicles_relationships` VALUES (1,1,5,'王者荣耀','2010-10-05 20:20:20',1),(2,1,6,'舞动人生','2010-10-05 20:20:50',1),(3,2,6,'舞动人生','2010-10-05 20:20:20',1),(4,3,6,'舞动人生','2010-10-05 20:20:20',1);
/*!40000 ALTER TABLE `xcx_cicles_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_comments`
--

DROP TABLE IF EXISTS `xcx_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_table` varchar(100) NOT NULL COMMENT '评论内容所在表，不带表前缀',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论内容 id',
  `url` varchar(255) DEFAULT NULL COMMENT '原文地址',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '被评论的用户id',
  `full_name` varchar(50) DEFAULT NULL COMMENT '评论者昵称',
  `email` varchar(255) DEFAULT NULL COMMENT '评论者邮箱',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '评论时间',
  `content` text NOT NULL COMMENT '评论内容',
  `type` smallint(1) NOT NULL DEFAULT '1' COMMENT '评论类型；1实名评论',
  `parentid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '被回复的评论id',
  `path` varchar(500) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态，1已审核，0未审核',
  PRIMARY KEY (`id`),
  KEY `comment_post_ID` (`post_id`),
  KEY `comment_approved_date_gmt` (`status`),
  KEY `comment_parent` (`parentid`),
  KEY `table_id_status` (`post_table`,`post_id`,`status`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_comments`
--

LOCK TABLES `xcx_comments` WRITE;
/*!40000 ALTER TABLE `xcx_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_common_action_log`
--

DROP TABLE IF EXISTS `xcx_common_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_common_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) DEFAULT '0' COMMENT '用户id',
  `object` varchar(100) DEFAULT NULL COMMENT '访问对象的id,格式：不带前缀的表名+id;如posts1表示xx_posts表里id为1的记录',
  `action` varchar(50) DEFAULT NULL COMMENT '操作名称；格式规定为：应用名+控制器+操作名；也可自己定义格式只要不发生冲突且惟一；',
  `count` int(11) DEFAULT '0' COMMENT '访问次数',
  `last_time` int(11) DEFAULT '0' COMMENT '最后访问的时间戳',
  `ip` varchar(15) DEFAULT NULL COMMENT '访问者最后访问ip',
  PRIMARY KEY (`id`),
  KEY `user_object_action` (`user`,`object`,`action`),
  KEY `user_object_action_ip` (`user`,`object`,`action`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='访问记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_common_action_log`
--

LOCK TABLES `xcx_common_action_log` WRITE;
/*!40000 ALTER TABLE `xcx_common_action_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_common_action_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_daily_points`
--

DROP TABLE IF EXISTS `xcx_daily_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_daily_points` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'member_id',
  `addtime` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '起始标志日期,',
  `point` smallint(5) DEFAULT '0' COMMENT 'point',
  `recommend_num` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '每日拉新数',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `addtime` (`addtime`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=273 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_daily_points`
--

LOCK TABLES `xcx_daily_points` WRITE;
/*!40000 ALTER TABLE `xcx_daily_points` DISABLE KEYS */;
INSERT INTO `xcx_daily_points` VALUES (270,1,'2017-11-04 00:00:00',30,0),(271,1,'2017-11-11 00:00:00',-90,0),(272,3,'2017-11-14 00:00:00',10,0);
/*!40000 ALTER TABLE `xcx_daily_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_detail_points`
--

DROP TABLE IF EXISTS `xcx_detail_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_detail_points` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) unsigned DEFAULT '0' COMMENT 'member_id',
  `addtime` datetime DEFAULT '1000-01-01 00:00:00' COMMENT 'action time,',
  `point` smallint(5) DEFAULT '0' COMMENT 'point',
  `action` tinyint(1) unsigned DEFAULT '0' COMMENT '0-发布圈子;1-被点赞;2-评论;3-名片;4-发布便民;5-违规;6-最新活动;7-拉新',
  PRIMARY KEY (`id`),
  KEY `member_date` (`member_id`,`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=287 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_detail_points`
--

LOCK TABLES `xcx_detail_points` WRITE;
/*!40000 ALTER TABLE `xcx_detail_points` DISABLE KEYS */;
INSERT INTO `xcx_detail_points` VALUES (281,1,'2017-11-04 09:26:09',30,0),(282,1,'2017-11-11 09:18:28',-100,4),(283,1,'2017-11-11 09:23:03',-100,4),(284,1,'2017-11-11 09:24:33',-100,4),(285,1,'2017-11-11 10:33:27',10,2),(286,3,'2017-11-14 04:25:28',10,1);
/*!40000 ALTER TABLE `xcx_detail_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_guestbook`
--

DROP TABLE IF EXISTS `xcx_guestbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL COMMENT '留言者姓名',
  `email` varchar(100) NOT NULL COMMENT '留言者邮箱',
  `title` varchar(255) DEFAULT NULL COMMENT '留言标题',
  `msg` text NOT NULL COMMENT '留言内容',
  `createtime` datetime NOT NULL COMMENT '留言时间',
  `status` smallint(2) NOT NULL DEFAULT '1' COMMENT '留言状态，1：正常，0：删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_guestbook`
--

LOCK TABLES `xcx_guestbook` WRITE;
/*!40000 ALTER TABLE `xcx_guestbook` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_guestbook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_info_comments`
--

DROP TABLE IF EXISTS `xcx_info_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_info_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `from_mid` int(11) NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `to_mid` int(11) NOT NULL DEFAULT '0' COMMENT '被评论的用户id',
  `from_name` varchar(50) DEFAULT '',
  `to_name` varchar(50) DEFAULT '',
  `from_userphoto` varchar(255) NOT NULL DEFAULT '',
  `to_userphoto` varchar(255) NOT NULL DEFAULT '',
  `createtime` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `content` text NOT NULL COMMENT '评论内容',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态，1-未违规，0-违规',
  PRIMARY KEY (`id`),
  KEY `comment_post_ID` (`post_id`),
  KEY `comment_approved_date_gmt` (`status`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_info_comments`
--

LOCK TABLES `xcx_info_comments` WRITE;
/*!40000 ALTER TABLE `xcx_info_comments` DISABLE KEYS */;
INSERT INTO `xcx_info_comments` VALUES (1,423,1,0,'user1','','','','2017-10-01 20:20:20','content111',1),(2,423,2,1,'user2','user1','','','2017-10-01 20:20:30','content222',1),(3,423,3,0,'user3','','','','2017-10-01 20:20:40','content333',1),(4,423,1,2,'user1','user2','','','2017-10-01 20:20:50','content11sdsd1',1),(5,424,1,0,'user1','','','','2017-11-02 09:20:13','content test',1),(6,424,3,1,'user3','user1','','','2017-11-02 09:21:49','content test',1),(7,425,1,0,'user1','','','','2017-11-11 10:33:27','ssssssss',1);
/*!40000 ALTER TABLE `xcx_info_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_infos`
--

DROP TABLE IF EXISTS `xcx_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_infos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned DEFAULT '0' COMMENT '发表者id',
  `post_keywords` varchar(150) DEFAULT '' COMMENT 'keywords',
  `post_date` datetime DEFAULT '1000-01-01 00:00:00' COMMENT 'post创建日期',
  `post_content` varchar(255) DEFAULT NULL COMMENT 'post内容',
  `post_addr` varchar(100) NOT NULL DEFAULT '' COMMENT '发布地址',
  `post_parent` bigint(20) unsigned DEFAULT '0' COMMENT 'post的父级post id,表示post层级关系',
  `smeta` varchar(255) DEFAULT '' COMMENT '缩略图；格式为json',
  `stars` varchar(5000) NOT NULL DEFAULT '',
  `comment_count` int(20) DEFAULT '0' COMMENT '评论数量',
  `post_hits` int(11) DEFAULT '0' COMMENT 'post点击数',
  `post_like` varchar(5000) NOT NULL DEFAULT '',
  `istop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '置顶 1置顶； 0不置顶',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-信息分类；1-圈子分类',
  PRIMARY KEY (`id`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `post_date` (`post_date`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=435 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_infos`
--

LOCK TABLES `xcx_infos` WRITE;
/*!40000 ALTER TABLE `xcx_infos` DISABLE KEYS */;
INSERT INTO `xcx_infos` VALUES (423,1,'','2017-10-27 20:20:20','content1','addr1',0,'','3',4,0,'2',0,0),(424,2,'','2017-10-27 20:20:20','content2','addr2',0,'','1',0,0,'1,3',0,0),(425,3,'','2017-10-27 20:20:20','content3','addr3',0,'','2',0,0,'1,2',0,0),(429,3,'','2017-11-03 03:04:15','content4654654','addr546546546456',0,'','',0,0,'',0,0),(430,3,'','2017-11-03 03:04:56','content4654654','addr546546546456',0,'[\"smeta1\",\"smeta2\"]','',0,0,'',0,0),(431,1,'','2017-11-11 09:15:11','content','addr',0,'[\"\"]','',0,0,'',0,0),(432,1,'','2017-11-11 09:18:28','content','addr',0,'[\"\"]','',0,0,'',0,0),(433,1,'','2017-11-11 09:23:03','content','addr',0,'[\"\"]','',0,0,'',0,0),(434,1,'','2017-11-11 09:24:33','content','addr',0,'[\"\"]','',0,0,'',0,0);
/*!40000 ALTER TABLE `xcx_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_infos_relationships`
--

DROP TABLE IF EXISTS `xcx_infos_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_infos_relationships` (
  `infosid` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'infos表里信息id',
  `cg_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `cg_name` varchar(200) NOT NULL DEFAULT '' COMMENT '分类name',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`infosid`),
  KEY `category_id` (`cg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='Portal 信息分类对应表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_infos_relationships`
--

LOCK TABLES `xcx_infos_relationships` WRITE;
/*!40000 ALTER TABLE `xcx_infos_relationships` DISABLE KEYS */;
INSERT INTO `xcx_infos_relationships` VALUES (4,423,4,'游泳健身',2,0),(5,424,5,'王者荣耀',3,1),(6,425,4,'游泳健身',1,1),(7,429,8,'买房租房',0,1),(8,430,8,'买房租房',0,1),(9,431,8,'买房租房',0,1),(10,432,8,'买房租房',0,1),(11,433,8,'买房租房',0,1),(12,434,8,'买房租房',0,1);
/*!40000 ALTER TABLE `xcx_infos_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_links`
--

DROP TABLE IF EXISTS `xcx_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL COMMENT '友情链接地址',
  `link_name` varchar(255) NOT NULL COMMENT '友情链接名称',
  `link_image` varchar(255) DEFAULT NULL COMMENT '友情链接图标',
  `link_target` varchar(25) NOT NULL DEFAULT '_blank' COMMENT '友情链接打开方式',
  `link_description` text NOT NULL COMMENT '友情链接描述',
  `link_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `link_rating` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接评级',
  `link_rel` varchar(255) DEFAULT NULL COMMENT '链接与网站的关系',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='友情链接表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_links`
--

LOCK TABLES `xcx_links` WRITE;
/*!40000 ALTER TABLE `xcx_links` DISABLE KEYS */;
INSERT INTO `xcx_links` VALUES (1,'http://www.thinkcmf.com','ThinkCMF','','_blank','',1,0,'',0);
/*!40000 ALTER TABLE `xcx_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_member`
--

DROP TABLE IF EXISTS `xcx_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_member` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `islock` tinyint(1) DEFAULT '0',
  `username` varchar(60) DEFAULT '' COMMENT '用户名',
  `userphoto` varchar(64) DEFAULT '' COMMENT '头像',
  `user_email` varchar(100) DEFAULT '' COMMENT '登录邮箱',
  `phone` varchar(20) DEFAULT '' COMMENT 'phone',
  `realname` varchar(40) DEFAULT '' COMMENT '真是姓名',
  `firmname` varchar(40) DEFAULT '' COMMENT '公司名',
  `job` varchar(40) DEFAULT '' COMMENT '职务',
  `sex` smallint(1) DEFAULT '0' COMMENT '性别;0：保密，1：男；2：女',
  `last_login_time` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '最后登录时间',
  `logintime` int(11) DEFAULT '0' COMMENT '登陆次数',
  `addtime` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '第一次登陆时间',
  `user_status` int(11) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `email_status` bigint(11) DEFAULT '0' COMMENT '邮箱状态,1:已激活,0:未激活',
  `user_type` smallint(1) DEFAULT '2' COMMENT '用户类型，1:admin ;2:会员',
  `invite_userid` bigint(20) DEFAULT '0' COMMENT '邀请人id',
  `gold` varchar(40) DEFAULT '' COMMENT '金币数量',
  `exp` varchar(40) DEFAULT '' COMMENT '经验',
  `level` varchar(10) DEFAULT '' COMMENT '等级',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `username` (`username`),
  KEY `invite_userid` (`invite_userid`),
  KEY `islock` (`islock`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_member`
--

LOCK TABLES `xcx_member` WRITE;
/*!40000 ALTER TABLE `xcx_member` DISABLE KEYS */;
INSERT INTO `xcx_member` VALUES (1,0,'user1','userphoto1','','13332324545','','','',0,'1000-01-01 00:00:00',0,'1000-01-01 00:00:00',1,0,2,0,'','2000',''),(2,0,'user2','userphoto2','','13332324546','','','',0,'1000-01-01 00:00:00',0,'1000-01-01 00:00:00',1,0,2,0,'','2000',''),(3,0,'user3','userphoto3','','13332324547','','','',0,'1000-01-01 00:00:00',0,'1000-01-01 00:00:00',1,0,2,0,'','2010','');
/*!40000 ALTER TABLE `xcx_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_members_relationships`
--

DROP TABLE IF EXISTS `xcx_members_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_members_relationships` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `follow_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'member_id',
  `follow_name` varchar(60) NOT NULL DEFAULT '' COMMENT 'follow_name',
  `follow_photo` varchar(100) NOT NULL DEFAULT '',
  `fan_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'fan_id',
  `fan_name` varchar(200) NOT NULL DEFAULT '' COMMENT 'fan_name',
  `fan_photo` varchar(100) NOT NULL DEFAULT '',
  `addtime` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '关注时间',
  PRIMARY KEY (`id`),
  KEY `follow_id` (`follow_id`),
  KEY `fan_id` (`fan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='用户关注关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_members_relationships`
--

LOCK TABLES `xcx_members_relationships` WRITE;
/*!40000 ALTER TABLE `xcx_members_relationships` DISABLE KEYS */;
INSERT INTO `xcx_members_relationships` VALUES (1,1,'user1','',2,'user2','','2010-10-10 20:20:20'),(2,1,'user1','',3,'user3','','2010-10-10 20:20:20'),(3,3,'user3','',2,'user2','','2010-10-10 20:20:20'),(4,2,'user2','',1,'user1','','2010-10-10 20:20:20');
/*!40000 ALTER TABLE `xcx_members_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_menu`
--

DROP TABLE IF EXISTS `xcx_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `app` varchar(30) NOT NULL DEFAULT '' COMMENT '应用名称app',
  `model` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作名称',
  `data` varchar(50) NOT NULL DEFAULT '' COMMENT '额外参数',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单类型  1：权限认证+菜单；0：只作为菜单',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1显示，0不显示',
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `icon` varchar(50) DEFAULT NULL COMMENT '菜单图标',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序ID',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parentid` (`parentid`),
  KEY `model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=197 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_menu`
--

LOCK TABLES `xcx_menu` WRITE;
/*!40000 ALTER TABLE `xcx_menu` DISABLE KEYS */;
INSERT INTO `xcx_menu` VALUES (1,0,'Admin','Content','default','',0,1,'内容管理','th','',30),(2,1,'Api','Guestbookadmin','index','',1,1,'所有留言','','',0),(3,2,'Api','Guestbookadmin','delete','',1,0,'删除网站留言','','',0),(4,1,'Comment','Commentadmin','index','',1,1,'评论管理','','',0),(5,4,'Comment','Commentadmin','delete','',1,0,'删除评论','','',0),(6,4,'Comment','Commentadmin','check','',1,0,'评论审核','','',0),(7,1,'Portal','AdminPost','index','',1,1,'文章管理','','',1),(8,7,'Portal','AdminPost','listorders','',1,0,'文章排序','','',0),(9,7,'Portal','AdminPost','top','',1,0,'文章置顶','','',0),(10,7,'Portal','AdminPost','recommend','',1,0,'文章推荐','','',0),(11,7,'Portal','AdminPost','move','',1,0,'批量移动','','',1000),(12,7,'Portal','AdminPost','check','',1,0,'文章审核','','',1000),(13,7,'Portal','AdminPost','delete','',1,0,'删除文章','','',1000),(14,7,'Portal','AdminPost','edit','',1,0,'编辑文章','','',1000),(15,14,'Portal','AdminPost','edit_post','',1,0,'提交编辑','','',0),(16,7,'Portal','AdminPost','add','',1,0,'添加文章','','',1000),(17,16,'Portal','AdminPost','add_post','',1,0,'提交添加','','',0),(18,1,'Portal','AdminTerm','index','',0,1,'分类管理','','',2),(19,18,'Portal','AdminTerm','listorders','',1,0,'文章分类排序','','',0),(20,18,'Portal','AdminTerm','delete','',1,0,'删除分类','','',1000),(21,18,'Portal','AdminTerm','edit','',1,0,'编辑分类','','',1000),(22,21,'Portal','AdminTerm','edit_post','',1,0,'提交编辑','','',0),(23,18,'Portal','AdminTerm','add','',1,0,'添加分类','','',1000),(24,23,'Portal','AdminTerm','add_post','',1,0,'提交添加','','',0),(25,1,'Portal','AdminPage','index','',1,1,'页面管理','','',3),(26,25,'Portal','AdminPage','listorders','',1,0,'页面排序','','',0),(27,25,'Portal','AdminPage','delete','',1,0,'删除页面','','',1000),(28,25,'Portal','AdminPage','edit','',1,0,'编辑页面','','',1000),(29,28,'Portal','AdminPage','edit_post','',1,0,'提交编辑','','',0),(30,25,'Portal','AdminPage','add','',1,0,'添加页面','','',1000),(31,30,'Portal','AdminPage','add_post','',1,0,'提交添加','','',0),(32,1,'Admin','Recycle','default','',1,1,'回收站','','',4),(33,32,'Portal','AdminPost','recyclebin','',1,1,'文章回收','','',0),(34,33,'Portal','AdminPost','restore','',1,0,'文章还原','','',1000),(35,33,'Portal','AdminPost','clean','',1,0,'彻底删除','','',1000),(36,32,'Portal','AdminPage','recyclebin','',1,1,'页面回收','','',1),(37,36,'Portal','AdminPage','clean','',1,0,'彻底删除','','',1000),(38,36,'Portal','AdminPage','restore','',1,0,'页面还原','','',1000),(39,0,'Admin','Extension','default','',0,1,'扩展工具','cloud','',90),(40,39,'Admin','Backup','default','',1,0,'备份管理','','',0),(41,40,'Admin','Backup','restore','',1,1,'数据还原','','',0),(42,40,'Admin','Backup','index','',1,1,'数据备份','','',0),(43,42,'Admin','Backup','index_post','',1,0,'提交数据备份','','',0),(44,40,'Admin','Backup','download','',1,0,'下载备份','','',1000),(45,40,'Admin','Backup','del_backup','',1,0,'删除备份','','',1000),(46,40,'Admin','Backup','import','',1,0,'数据备份导入','','',1000),(47,39,'Admin','Plugin','index','',1,1,'插件管理','','',0),(48,47,'Admin','Plugin','toggle','',1,0,'插件启用切换','','',0),(49,47,'Admin','Plugin','setting','',1,0,'插件设置','','',0),(50,49,'Admin','Plugin','setting_post','',1,0,'插件设置提交','','',0),(51,47,'Admin','Plugin','install','',1,0,'插件安装','','',0),(52,47,'Admin','Plugin','uninstall','',1,0,'插件卸载','','',0),(53,39,'Admin','Slide','default','',1,1,'幻灯片','','',1),(54,53,'Admin','Slide','index','',1,1,'幻灯片管理','','',0),(55,54,'Admin','Slide','listorders','',1,0,'幻灯片排序','','',0),(56,54,'Admin','Slide','toggle','',1,0,'幻灯片显示切换','','',0),(57,54,'Admin','Slide','delete','',1,0,'删除幻灯片','','',1000),(58,54,'Admin','Slide','edit','',1,0,'编辑幻灯片','','',1000),(59,58,'Admin','Slide','edit_post','',1,0,'提交编辑','','',0),(60,54,'Admin','Slide','add','',1,0,'添加幻灯片','','',1000),(61,60,'Admin','Slide','add_post','',1,0,'提交添加','','',0),(62,53,'Admin','Slidecat','index','',1,1,'幻灯片分类','','',0),(63,62,'Admin','Slidecat','delete','',1,0,'删除分类','','',1000),(64,62,'Admin','Slidecat','edit','',1,0,'编辑分类','','',1000),(65,64,'Admin','Slidecat','edit_post','',1,0,'提交编辑','','',0),(66,62,'Admin','Slidecat','add','',1,0,'添加分类','','',1000),(67,66,'Admin','Slidecat','add_post','',1,0,'提交添加','','',0),(68,39,'Admin','Ad','index','',1,1,'网站广告','','',2),(69,68,'Admin','Ad','toggle','',1,0,'广告显示切换','','',0),(70,68,'Admin','Ad','delete','',1,0,'删除广告','','',1000),(71,68,'Admin','Ad','edit','',1,0,'编辑广告','','',1000),(72,71,'Admin','Ad','edit_post','',1,0,'提交编辑','','',0),(73,68,'Admin','Ad','add','',1,0,'添加广告','','',1000),(74,73,'Admin','Ad','add_post','',1,0,'提交添加','','',0),(75,39,'Admin','Link','index','',0,1,'友情链接','','',3),(76,75,'Admin','Link','listorders','',1,0,'友情链接排序','','',0),(77,75,'Admin','Link','toggle','',1,0,'友链显示切换','','',0),(78,75,'Admin','Link','delete','',1,0,'删除友情链接','','',1000),(79,75,'Admin','Link','edit','',1,0,'编辑友情链接','','',1000),(80,79,'Admin','Link','edit_post','',1,0,'提交编辑','','',0),(81,75,'Admin','Link','add','',1,0,'添加友情链接','','',1000),(82,81,'Admin','Link','add_post','',1,0,'提交添加','','',0),(83,39,'Api','Oauthadmin','setting','',1,1,'第三方登陆','leaf','',4),(84,83,'Api','Oauthadmin','setting_post','',1,0,'提交设置','','',0),(85,0,'Admin','Menu','default','',1,1,'菜单管理','list','',20),(86,85,'Admin','Navcat','default1','',1,1,'前台菜单','','',0),(87,86,'Admin','Nav','index','',1,1,'菜单管理','','',0),(88,87,'Admin','Nav','listorders','',1,0,'前台导航排序','','',0),(89,87,'Admin','Nav','delete','',1,0,'删除菜单','','',1000),(90,87,'Admin','Nav','edit','',1,0,'编辑菜单','','',1000),(91,90,'Admin','Nav','edit_post','',1,0,'提交编辑','','',0),(92,87,'Admin','Nav','add','',1,0,'添加菜单','','',1000),(93,92,'Admin','Nav','add_post','',1,0,'提交添加','','',0),(94,86,'Admin','Navcat','index','',1,1,'菜单分类','','',0),(95,94,'Admin','Navcat','delete','',1,0,'删除分类','','',1000),(96,94,'Admin','Navcat','edit','',1,0,'编辑分类','','',1000),(97,96,'Admin','Navcat','edit_post','',1,0,'提交编辑','','',0),(98,94,'Admin','Navcat','add','',1,0,'添加分类','','',1000),(99,98,'Admin','Navcat','add_post','',1,0,'提交添加','','',0),(100,85,'Admin','Menu','index','',1,1,'后台菜单','','',0),(101,100,'Admin','Menu','add','',1,0,'添加菜单','','',0),(102,101,'Admin','Menu','add_post','',1,0,'提交添加','','',0),(103,100,'Admin','Menu','listorders','',1,0,'后台菜单排序','','',0),(104,100,'Admin','Menu','export_menu','',1,0,'菜单备份','','',1000),(105,100,'Admin','Menu','edit','',1,0,'编辑菜单','','',1000),(106,105,'Admin','Menu','edit_post','',1,0,'提交编辑','','',0),(107,100,'Admin','Menu','delete','',1,0,'删除菜单','','',1000),(108,100,'Admin','Menu','lists','',1,0,'所有菜单','','',1000),(109,0,'Admin','Setting','default','',0,1,'设置','cogs','',100),(110,109,'Admin','Setting','userdefault','',0,1,'个人信息','','',0),(111,110,'Admin','User','userinfo','',1,1,'修改信息','','',0),(112,111,'Admin','User','userinfo_post','',1,0,'修改信息提交','','',0),(113,110,'Admin','Setting','password','',1,1,'修改密码','','',0),(114,113,'Admin','Setting','password_post','',1,0,'提交修改','','',0),(115,109,'Admin','Setting','site','',1,1,'网站信息','','',0),(116,115,'Admin','Setting','site_post','',1,0,'提交修改','','',0),(117,115,'Admin','Route','index','',1,0,'路由列表','','',0),(118,115,'Admin','Route','add','',1,0,'路由添加','','',0),(119,118,'Admin','Route','add_post','',1,0,'路由添加提交','','',0),(120,115,'Admin','Route','edit','',1,0,'路由编辑','','',0),(121,120,'Admin','Route','edit_post','',1,0,'路由编辑提交','','',0),(122,115,'Admin','Route','delete','',1,0,'路由删除','','',0),(123,115,'Admin','Route','ban','',1,0,'路由禁止','','',0),(124,115,'Admin','Route','open','',1,0,'路由启用','','',0),(125,115,'Admin','Route','listorders','',1,0,'路由排序','','',0),(126,109,'Admin','Mailer','default','',1,1,'邮箱配置','','',0),(127,126,'Admin','Mailer','index','',1,1,'SMTP配置','','',0),(128,127,'Admin','Mailer','index_post','',1,0,'提交配置','','',0),(129,126,'Admin','Mailer','active','',1,1,'注册邮件模板','','',0),(130,129,'Admin','Mailer','active_post','',1,0,'提交模板','','',0),(131,109,'Admin','Setting','clearcache','',1,1,'清除缓存','','',1),(132,0,'User','Indexadmin','default','',1,1,'用户管理','group','',10),(133,132,'User','Indexadmin','default1','',1,0,'用户组','','',0),(134,133,'User','Indexadmin','index','',1,1,'本站用户','leaf','',0),(135,134,'User','Indexadmin','ban','',1,0,'拉黑会员','','',0),(136,134,'User','Indexadmin','cancelban','',1,0,'启用会员','','',0),(137,133,'User','Oauthadmin','index','',1,1,'第三方用户','leaf','',0),(138,137,'User','Oauthadmin','delete','',1,0,'第三方用户解绑','','',0),(139,132,'User','Indexadmin','default3','',1,1,'管理组','','',0),(140,139,'Admin','Rbac','index','',1,1,'角色管理','','',0),(141,140,'Admin','Rbac','member','',1,0,'成员管理','','',1000),(142,140,'Admin','Rbac','authorize','',1,0,'权限设置','','',1000),(143,142,'Admin','Rbac','authorize_post','',1,0,'提交设置','','',0),(144,140,'Admin','Rbac','roleedit','',1,0,'编辑角色','','',1000),(145,144,'Admin','Rbac','roleedit_post','',1,0,'提交编辑','','',0),(146,140,'Admin','Rbac','roledelete','',1,1,'删除角色','','',1000),(147,140,'Admin','Rbac','roleadd','',1,1,'添加角色','','',1000),(148,147,'Admin','Rbac','roleadd_post','',1,0,'提交添加','','',0),(149,139,'Admin','User','index','',1,1,'管理员','','',0),(150,149,'Admin','User','delete','',1,0,'删除管理员','','',1000),(151,149,'Admin','User','edit','',1,0,'管理员编辑','','',1000),(152,151,'Admin','User','edit_post','',1,0,'编辑提交','','',0),(153,149,'Admin','User','add','',1,0,'管理员添加','','',1000),(154,153,'Admin','User','add_post','',1,0,'添加提交','','',0),(155,47,'Admin','Plugin','update','',1,0,'插件更新','','',0),(156,109,'Admin','Storage','index','',1,1,'文件存储','','',0),(157,156,'Admin','Storage','setting_post','',1,0,'文件存储设置提交','','',0),(158,54,'Admin','Slide','ban','',1,0,'禁用幻灯片','','',0),(159,54,'Admin','Slide','cancelban','',1,0,'启用幻灯片','','',0),(160,149,'Admin','User','ban','',1,0,'禁用管理员','','',0),(161,149,'Admin','User','cancelban','',1,0,'启用管理员','','',0),(166,127,'Admin','Mailer','test','',1,0,'测试邮件','','',0),(167,109,'Admin','Setting','upload','',1,1,'上传设置','','',0),(168,167,'Admin','Setting','upload_post','',1,0,'上传设置提交','','',0),(169,7,'Portal','AdminPost','copy','',1,0,'文章批量复制','','',0),(174,100,'Admin','Menu','backup_menu','',1,0,'备份菜单','','',0),(175,100,'Admin','Menu','export_menu_lang','',1,0,'导出后台菜单多语言包','','',0),(176,100,'Admin','Menu','restore_menu','',1,0,'还原菜单','','',0),(177,100,'Admin','Menu','getactions','',1,0,'导入新菜单','','',0),(187,0,'Portal','AdminInfo','index','',1,1,'信息管理','','',0),(196,0,'Portal','AdminShop','index','',1,1,'店铺管理','','',0),(193,0,'Portal','AdminMember','index','',1,1,'会员管理','','',0),(195,0,'Portal','AdminAds','index','',1,1,'广告管理','','',0),(194,0,'Portal','AdminCategorys','index','',1,1,'分类管理','','',0);
/*!40000 ALTER TABLE `xcx_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_nav`
--

DROP TABLE IF EXISTS `xcx_nav`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '导航分类 id',
  `parentid` int(11) NOT NULL COMMENT '导航父 id',
  `label` varchar(255) NOT NULL COMMENT '导航标题',
  `target` varchar(50) DEFAULT NULL COMMENT '打开方式',
  `href` varchar(255) NOT NULL COMMENT '导航链接',
  `icon` varchar(255) NOT NULL COMMENT '导航图标',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `listorder` int(6) DEFAULT '0' COMMENT '排序',
  `path` varchar(255) NOT NULL DEFAULT '0' COMMENT '层级关系',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='前台导航表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_nav`
--

LOCK TABLES `xcx_nav` WRITE;
/*!40000 ALTER TABLE `xcx_nav` DISABLE KEYS */;
INSERT INTO `xcx_nav` VALUES (1,1,0,'首页','','home','',1,0,'0-1'),(2,1,0,'列表演示','','a:2:{s:6:\"action\";s:17:\"Portal/List/index\";s:5:\"param\";a:1:{s:2:\"id\";s:1:\"1\";}}','',1,0,'0-2'),(3,1,0,'瀑布流','','a:2:{s:6:\"action\";s:17:\"Portal/List/index\";s:5:\"param\";a:1:{s:2:\"id\";s:1:\"2\";}}','',1,0,'0-3');
/*!40000 ALTER TABLE `xcx_nav` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_nav_cat`
--

DROP TABLE IF EXISTS `xcx_nav_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_nav_cat` (
  `navcid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '导航分类名',
  `active` int(1) NOT NULL DEFAULT '1' COMMENT '是否为主菜单，1是，0不是',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`navcid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='前台导航分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_nav_cat`
--

LOCK TABLES `xcx_nav_cat` WRITE;
/*!40000 ALTER TABLE `xcx_nav_cat` DISABLE KEYS */;
INSERT INTO `xcx_nav_cat` VALUES (1,'主导航',1,'主导航');
/*!40000 ALTER TABLE `xcx_nav_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_oauth_user`
--

DROP TABLE IF EXISTS `xcx_oauth_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_oauth_user` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `from` varchar(20) NOT NULL COMMENT '用户来源key',
  `name` varchar(30) NOT NULL COMMENT '第三方昵称',
  `head_img` varchar(200) NOT NULL COMMENT '头像',
  `uid` int(20) NOT NULL COMMENT '关联的本站用户id',
  `create_time` datetime NOT NULL COMMENT '绑定时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(16) NOT NULL COMMENT '最后登录ip',
  `login_times` int(6) NOT NULL COMMENT '登录次数',
  `status` tinyint(2) NOT NULL,
  `access_token` varchar(512) NOT NULL,
  `expires_date` int(11) NOT NULL COMMENT 'access_token过期时间',
  `openid` varchar(40) NOT NULL COMMENT '第三方用户id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_oauth_user`
--

LOCK TABLES `xcx_oauth_user` WRITE;
/*!40000 ALTER TABLE `xcx_oauth_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_oauth_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_options`
--

DROP TABLE IF EXISTS `xcx_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL COMMENT '配置名',
  `option_value` longtext NOT NULL COMMENT '配置值',
  `autoload` int(2) NOT NULL DEFAULT '1' COMMENT '是否自动加载',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='全站配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_options`
--

LOCK TABLES `xcx_options` WRITE;
/*!40000 ALTER TABLE `xcx_options` DISABLE KEYS */;
INSERT INTO `xcx_options` VALUES (1,'member_email_active','{\"title\":\"ThinkCMF\\u90ae\\u4ef6\\u6fc0\\u6d3b\\u901a\\u77e5.\",\"template\":\"<p>\\u672c\\u90ae\\u4ef6\\u6765\\u81ea<a href=\\\"http:\\/\\/www.thinkcmf.com\\\">ThinkCMF<\\/a><br\\/><br\\/>&nbsp; &nbsp;<strong>---------------<strong style=\\\"white-space: normal;\\\">---<\\/strong><\\/strong><br\\/>&nbsp; &nbsp;<strong>\\u5e10\\u53f7\\u6fc0\\u6d3b\\u8bf4\\u660e<\\/strong><br\\/>&nbsp; &nbsp;<strong>---------------<strong style=\\\"white-space: normal;\\\">---<\\/strong><\\/strong><br\\/><br\\/>&nbsp; &nbsp; \\u5c0a\\u656c\\u7684<span style=\\\"FONT-SIZE: 16px; FONT-FAMILY: Arial; COLOR: rgb(51,51,51); LINE-HEIGHT: 18px; BACKGROUND-COLOR: rgb(255,255,255)\\\">#username#\\uff0c\\u60a8\\u597d\\u3002<\\/span>\\u5982\\u679c\\u60a8\\u662fThinkCMF\\u7684\\u65b0\\u7528\\u6237\\uff0c\\u6216\\u5728\\u4fee\\u6539\\u60a8\\u7684\\u6ce8\\u518cEmail\\u65f6\\u4f7f\\u7528\\u4e86\\u672c\\u5730\\u5740\\uff0c\\u6211\\u4eec\\u9700\\u8981\\u5bf9\\u60a8\\u7684\\u5730\\u5740\\u6709\\u6548\\u6027\\u8fdb\\u884c\\u9a8c\\u8bc1\\u4ee5\\u907f\\u514d\\u5783\\u573e\\u90ae\\u4ef6\\u6216\\u5730\\u5740\\u88ab\\u6ee5\\u7528\\u3002<br\\/>&nbsp; &nbsp; \\u60a8\\u53ea\\u9700\\u70b9\\u51fb\\u4e0b\\u9762\\u7684\\u94fe\\u63a5\\u5373\\u53ef\\u6fc0\\u6d3b\\u60a8\\u7684\\u5e10\\u53f7\\uff1a<br\\/>&nbsp; &nbsp; <a title=\\\"\\\" href=\\\"http:\\/\\/#link#\\\" target=\\\"_self\\\">http:\\/\\/#link#<\\/a><br\\/>&nbsp; &nbsp; (\\u5982\\u679c\\u4e0a\\u9762\\u4e0d\\u662f\\u94fe\\u63a5\\u5f62\\u5f0f\\uff0c\\u8bf7\\u5c06\\u8be5\\u5730\\u5740\\u624b\\u5de5\\u7c98\\u8d34\\u5230\\u6d4f\\u89c8\\u5668\\u5730\\u5740\\u680f\\u518d\\u8bbf\\u95ee)<br\\/>&nbsp; &nbsp; \\u611f\\u8c22\\u60a8\\u7684\\u8bbf\\u95ee\\uff0c\\u795d\\u60a8\\u4f7f\\u7528\\u6109\\u5feb\\uff01<br\\/><br\\/>&nbsp; &nbsp; \\u6b64\\u81f4<br\\/>&nbsp; &nbsp; ThinkCMF \\u7ba1\\u7406\\u56e2\\u961f.<\\/p>\"}',1),(6,'site_options','            {\n            		\"site_name\":\"xcx\",\n            		\"site_host\":\"http://xcxadmin.com/\",\n            		\"site_root\":\"\",\n            		\"site_icp\":\"\",\n            		\"site_admin_email\":\"chengzheng67@126.com\",\n            		\"site_tongji\":\"\",\n            		\"site_copyright\":\"\",\n            		\"site_seo_title\":\"xcx\",\n            		\"site_seo_keywords\":\"xcx\",\n            		\"site_seo_description\":\"xcx\"\n        }',1);
/*!40000 ALTER TABLE `xcx_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_plugins`
--

DROP TABLE IF EXISTS `xcx_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_plugins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(50) NOT NULL COMMENT '插件名，英文',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '插件名称',
  `description` text COMMENT '插件描述',
  `type` tinyint(2) DEFAULT '0' COMMENT '插件类型, 1:网站；8;微信',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态；1开启；',
  `config` text COMMENT '插件配置',
  `hooks` varchar(255) DEFAULT NULL COMMENT '实现的钩子;以“，”分隔',
  `has_admin` tinyint(2) DEFAULT '0' COMMENT '插件是否有后台管理界面',
  `author` varchar(50) DEFAULT '' COMMENT '插件作者',
  `version` varchar(20) DEFAULT '' COMMENT '插件版本号',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `listorder` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_plugins`
--

LOCK TABLES `xcx_plugins` WRITE;
/*!40000 ALTER TABLE `xcx_plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_posts`
--

DROP TABLE IF EXISTS `xcx_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned DEFAULT '0' COMMENT '发表者id',
  `post_keywords` varchar(150) NOT NULL COMMENT 'seo keywords',
  `post_source` varchar(150) DEFAULT NULL COMMENT '转载文章的来源',
  `post_date` datetime DEFAULT '2000-01-01 00:00:00' COMMENT 'post发布日期',
  `post_content` longtext COMMENT 'post内容',
  `post_title` text COMMENT 'post标题',
  `post_excerpt` text COMMENT 'post摘要',
  `post_status` int(2) DEFAULT '1' COMMENT 'post状态，1已审核，0未审核,3删除',
  `comment_status` int(2) DEFAULT '1' COMMENT '评论状态，1允许，0不允许',
  `post_modified` datetime DEFAULT '2000-01-01 00:00:00' COMMENT 'post更新时间，可在前台修改，显示给用户',
  `post_content_filtered` longtext,
  `post_parent` bigint(20) unsigned DEFAULT '0' COMMENT 'post的父级post id,表示post层级关系',
  `post_type` int(2) DEFAULT '1' COMMENT 'post类型，1文章,2页面',
  `post_mime_type` varchar(100) DEFAULT '',
  `comment_count` bigint(20) DEFAULT '0',
  `smeta` text COMMENT 'post的扩展字段，保存相关扩展属性，如缩略图；格式为json',
  `post_hits` int(11) DEFAULT '0' COMMENT 'post点击数，查看数',
  `post_like` int(11) DEFAULT '0' COMMENT 'post赞数',
  `istop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '置顶 1置顶； 0不置顶',
  `recommended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐 1推荐 0不推荐',
  PRIMARY KEY (`id`),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`id`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `post_date` (`post_date`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Portal文章表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_posts`
--

LOCK TABLES `xcx_posts` WRITE;
/*!40000 ALTER TABLE `xcx_posts` DISABLE KEYS */;
INSERT INTO `xcx_posts` VALUES (1,1,'阿斯顿','阿三','2017-10-24 14:05:57','<p>阿三</p>','啊手动阀手动阀','阿斯顿',1,1,'2017-10-24 14:06:22',NULL,0,1,'',0,'{\"thumb\":\"portal\\/20171024\\/59eed84985a38.png\",\"template\":\"\"}',0,0,0,0),(2,1,'asdfa','ads','2017-10-25 16:58:42','<p>asd</p>','asdfasdfasf','ad',1,1,'2017-10-25 16:58:50',NULL,0,1,'',0,'{\"thumb\":\"\",\"template\":\"\"}',0,0,0,0),(3,1,'ads','ad','2017-10-25 16:58:54','<p>asd</p>','ads','ad',3,1,'2017-10-25 16:59:07',NULL,0,1,'',0,'{\"thumb\":\"\",\"template\":\"\"}',0,0,0,0);
/*!40000 ALTER TABLE `xcx_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_role`
--

DROP TABLE IF EXISTS `xcx_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '角色名称',
  `pid` smallint(6) DEFAULT NULL COMMENT '父角色ID',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` int(3) NOT NULL DEFAULT '0' COMMENT '排序字段',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_role`
--

LOCK TABLES `xcx_role` WRITE;
/*!40000 ALTER TABLE `xcx_role` DISABLE KEYS */;
INSERT INTO `xcx_role` VALUES (1,'超级管理员',0,1,'拥有网站最高管理员权限！',1329633709,1329633709,0);
/*!40000 ALTER TABLE `xcx_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_role_user`
--

DROP TABLE IF EXISTS `xcx_role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_role_user` (
  `role_id` int(11) unsigned DEFAULT '0' COMMENT '角色 id',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户角色对应表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_role_user`
--

LOCK TABLES `xcx_role_user` WRITE;
/*!40000 ALTER TABLE `xcx_role_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_route`
--

DROP TABLE IF EXISTS `xcx_route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '路由id',
  `full_url` varchar(255) DEFAULT NULL COMMENT '完整url， 如：portal/list/index?id=1',
  `url` varchar(255) DEFAULT NULL COMMENT '实际显示的url',
  `listorder` int(5) DEFAULT '0' COMMENT '排序，优先级，越小优先级越高',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1：启用 ;0：不启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='url路由表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_route`
--

LOCK TABLES `xcx_route` WRITE;
/*!40000 ALTER TABLE `xcx_route` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_route` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_shop`
--

DROP TABLE IF EXISTS `xcx_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_shop` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(60) NOT NULL DEFAULT '0' COMMENT '商店名称',
  `shop_pic` varchar(60) DEFAULT '' COMMENT 'shop_pic',
  `shop_addr` varchar(150) NOT NULL DEFAULT '' COMMENT '商店地址',
  `shop_major` varchar(150) NOT NULL DEFAULT '' COMMENT '主营业务',
  `shop_time` varchar(150) NOT NULL DEFAULT '' COMMENT '营业时间',
  `shop_contact` varchar(60) DEFAULT '' COMMENT 'shop_contact',
  `shop_phone` varchar(20) NOT NULL DEFAULT '' COMMENT '商家联系电话',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT 'member_id',
  `shop_detail` varchar(150) NOT NULL DEFAULT '' COMMENT '商家详情',
  `add_time` datetime DEFAULT '1000-01-01 00:00:00' COMMENT 'add_time',
  `start_time` datetime DEFAULT '1000-01-01 00:00:00' COMMENT 'start_time',
  `end_time` datetime DEFAULT '1000-01-01 00:00:00' COMMENT 'end_time',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否展示 0-否  1-是',
  `is_shiti` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否实体 0-否  1-是',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否新店 0-否  1-是',
  `istop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '置顶 1置顶； 0不置顶',
  `recommended` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐 1推荐 0不推荐',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=438 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_shop`
--

LOCK TABLES `xcx_shop` WRITE;
/*!40000 ALTER TABLE `xcx_shop` DISABLE KEYS */;
INSERT INTO `xcx_shop` VALUES (436,'name','portal/20171107/5a01c1412a31f.png','addr','major','09:00:00--20:00:00','contact','13332324546',2,'<p>content</p>','2017-11-07 22:19:54','2017-11-07 22:19:00','2017-11-30 22:19:00',1,1,0,0,0),(437,'adsf ','','','','','','',0,'','2017-11-10 11:36:52','2017-11-10 23:36:00','2017-11-30 23:36:00',1,1,1,0,0);
/*!40000 ALTER TABLE `xcx_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_shop_relationships`
--

DROP TABLE IF EXISTS `xcx_shop_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_shop_relationships` (
  `sid` bigint(20) NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'ads������id',
  `cg_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '����id',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '����',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '״̬��1������0������',
  PRIMARY KEY (`sid`),
  KEY `category_id` (`cg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Portal �̼ҷ����Ӧ��';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_shop_relationships`
--

LOCK TABLES `xcx_shop_relationships` WRITE;
/*!40000 ALTER TABLE `xcx_shop_relationships` DISABLE KEYS */;
INSERT INTO `xcx_shop_relationships` VALUES (10,436,6,0,1),(11,437,6,0,1);
/*!40000 ALTER TABLE `xcx_shop_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_slide`
--

DROP TABLE IF EXISTS `xcx_slide`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_slide` (
  `slide_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slide_cid` int(11) NOT NULL COMMENT '幻灯片分类 id',
  `slide_name` varchar(255) NOT NULL COMMENT '幻灯片名称',
  `slide_pic` varchar(255) DEFAULT NULL COMMENT '幻灯片图片',
  `slide_url` varchar(255) DEFAULT NULL COMMENT '幻灯片链接',
  `slide_des` varchar(255) DEFAULT NULL COMMENT '幻灯片描述',
  `slide_content` text COMMENT '幻灯片内容',
  `slide_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  `listorder` int(10) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`slide_id`),
  KEY `slide_cid` (`slide_cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='幻灯片表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_slide`
--

LOCK TABLES `xcx_slide` WRITE;
/*!40000 ALTER TABLE `xcx_slide` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_slide` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_slide_cat`
--

DROP TABLE IF EXISTS `xcx_slide_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_slide_cat` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL COMMENT '幻灯片分类',
  `cat_idname` varchar(255) NOT NULL COMMENT '幻灯片分类标识',
  `cat_remark` text COMMENT '分类备注',
  `cat_status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1显示，0不显示',
  PRIMARY KEY (`cid`),
  KEY `cat_idname` (`cat_idname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='幻灯片分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_slide_cat`
--

LOCK TABLES `xcx_slide_cat` WRITE;
/*!40000 ALTER TABLE `xcx_slide_cat` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_slide_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_term_relationships`
--

DROP TABLE IF EXISTS `xcx_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_term_relationships` (
  `tid` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'posts表里文章id',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`tid`),
  KEY `term_taxonomy_id` (`term_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Portal 文章分类对应表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_term_relationships`
--

LOCK TABLES `xcx_term_relationships` WRITE;
/*!40000 ALTER TABLE `xcx_term_relationships` DISABLE KEYS */;
INSERT INTO `xcx_term_relationships` VALUES (1,1,1,3,1),(2,2,2,0,1),(3,3,2,0,1);
/*!40000 ALTER TABLE `xcx_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_terms`
--

DROP TABLE IF EXISTS `xcx_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `name` varchar(200) DEFAULT NULL COMMENT '分类名称',
  `slug` varchar(200) DEFAULT '',
  `taxonomy` varchar(32) DEFAULT NULL COMMENT '分类类型',
  `description` longtext COMMENT '分类描述',
  `parent` bigint(20) unsigned DEFAULT '0' COMMENT '分类父id',
  `count` bigint(20) DEFAULT '0' COMMENT '分类文章数',
  `path` varchar(500) DEFAULT NULL COMMENT '分类层级关系路径',
  `seo_title` varchar(500) DEFAULT NULL,
  `seo_keywords` varchar(500) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `list_tpl` varchar(50) DEFAULT NULL COMMENT '分类列表模板',
  `one_tpl` varchar(50) DEFAULT NULL COMMENT '分类文章页模板',
  `listorder` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态，1发布，0不发布',
  PRIMARY KEY (`term_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Portal 文章分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_terms`
--

LOCK TABLES `xcx_terms` WRITE;
/*!40000 ALTER TABLE `xcx_terms` DISABLE KEYS */;
INSERT INTO `xcx_terms` VALUES (1,'列表演示','','article','',0,0,'0-1','','','','list','article',0,1),(2,'瀑布流','','article','',0,0,'0-2','','','','list_masonry','article',0,1);
/*!40000 ALTER TABLE `xcx_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_user_favorites`
--

DROP TABLE IF EXISTS `xcx_user_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL COMMENT '用户 id',
  `title` varchar(255) DEFAULT NULL COMMENT '收藏内容的标题',
  `url` varchar(255) DEFAULT NULL COMMENT '收藏内容的原文地址，不带域名',
  `description` varchar(500) DEFAULT NULL COMMENT '收藏内容的描述',
  `table` varchar(50) DEFAULT NULL COMMENT '收藏实体以前所在表，不带前缀',
  `object_id` int(11) DEFAULT NULL COMMENT '收藏内容原来的主键id',
  `createtime` int(11) DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_user_favorites`
--

LOCK TABLES `xcx_user_favorites` WRITE;
/*!40000 ALTER TABLE `xcx_user_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcx_user_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_users`
--

DROP TABLE IF EXISTS `xcx_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码；sp_password加密',
  `user_nicename` varchar(50) NOT NULL DEFAULT '' COMMENT '用户美名',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网站',
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像，相对于upload/avatar目录',
  `sex` smallint(1) DEFAULT '0' COMMENT '性别；0：保密，1：男；2：女',
  `birthday` date DEFAULT '2000-01-01' COMMENT '生日',
  `signature` varchar(255) DEFAULT NULL COMMENT '个性签名',
  `last_login_ip` varchar(16) DEFAULT NULL COMMENT '最后登录ip',
  `last_login_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '最后登录时间',
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '注册时间',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `user_status` int(11) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `user_type` smallint(1) DEFAULT '1' COMMENT '用户类型，1:admin ;2:会员',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '金币',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  PRIMARY KEY (`id`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_users`
--

LOCK TABLES `xcx_users` WRITE;
/*!40000 ALTER TABLE `xcx_users` DISABLE KEYS */;
INSERT INTO `xcx_users` VALUES (1,'admin','###768709a5b8b6c83f8d5492e387c25415','admin','chengzheng67@126.com','',NULL,0,'2000-01-01',NULL,'127.0.0.1','2017-11-14 10:39:08','2017-10-19 01:01:23','',1,0,1,0,'');
/*!40000 ALTER TABLE `xcx_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcx_weekly_points`
--

DROP TABLE IF EXISTS `xcx_weekly_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcx_weekly_points` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) unsigned DEFAULT '0' COMMENT 'member_id',
  `addtime` datetime DEFAULT '1000-01-01 00:00:00' COMMENT '起始标志日期,',
  `point` smallint(5) DEFAULT '0' COMMENT 'point',
  `recommend_num` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '每周拉新数',
  PRIMARY KEY (`id`),
  KEY `point` (`point`),
  KEY `addtime` (`addtime`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=274 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcx_weekly_points`
--

LOCK TABLES `xcx_weekly_points` WRITE;
/*!40000 ALTER TABLE `xcx_weekly_points` DISABLE KEYS */;
INSERT INTO `xcx_weekly_points` VALUES (271,1,'2017-10-30 00:00:00',30,0),(272,1,'2017-11-06 00:00:00',-90,0),(273,3,'2017-11-13 00:00:00',10,0);
/*!40000 ALTER TABLE `xcx_weekly_points` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-14 22:12:14
