-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               6.0.4-alpha-community-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2013-01-23 14:06:57
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for nchucsnews
DROP DATABASE IF EXISTS `nchucsnews`;
CREATE DATABASE IF NOT EXISTS `nchucsnews` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `nchucsnews`;


-- Dumping structure for table nchucsnews.log
DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `logid` int(11) NOT NULL AUTO_INCREMENT COMMENT '紀錄編號',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '時間',
  `from` text COLLATE utf8_unicode_ci NOT NULL COMMENT '從何而來',
  `text` text COLLATE utf8_unicode_ci NOT NULL COMMENT '記錄內容',
  PRIMARY KEY (`logid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.news
DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `nid` int(11) NOT NULL AUTO_INCREMENT COMMENT '新聞ID',
  `rid` int(11) NOT NULL COMMENT '從哪一個rss抓來的',
  `title` text COLLATE utf8_unicode_ci NOT NULL COMMENT '標題',
  `article` text COLLATE utf8_unicode_ci NOT NULL COMMENT '新聞內容',
  `news_t` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '新聞時間',
  `url` text COLLATE utf8_unicode_ci NOT NULL COMMENT '新聞網址',
  `viewcnt` int(11) NOT NULL COMMENT '瀏覽次數',
  `ckipsvr` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='新聞';

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.news2word
DROP TABLE IF EXISTS `news2word`;
CREATE TABLE IF NOT EXISTS `news2word` (
  `i` int(11) NOT NULL AUTO_INCREMENT COMMENT '無意義',
  `nid` int(11) NOT NULL COMMENT '新聞ID',
  `wid` int(11) NOT NULL COMMENT '詞語ID',
  `cnt` int(11) NOT NULL,
  PRIMARY KEY (`i`),
  KEY `nid` (`nid`),
  KEY `wid` (`wid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='新聞與詞語對應';

-- Data exporting was unselected.


-- Dumping structure for view nchucsnews.news2wordg
DROP VIEW IF EXISTS `news2wordg`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `news2wordg` (
	`i` INT(11) NOT NULL DEFAULT '0' COMMENT '無意義',
	`nid` INT(11) NOT NULL COMMENT '新聞ID',
	`wid` INT(11) NOT NULL COMMENT '詞語ID',
	`cnt` INT(11) NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view nchucsnews.newsbycategory
DROP VIEW IF EXISTS `newsbycategory`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `newsbycategory` (
	`rid` INT(11) NOT NULL DEFAULT '0' COMMENT 'RSS ID',
	`name` TEXT NOT NULL COMMENT 'RSS 名稱' COLLATE 'utf8_unicode_ci',
	`cnt` BIGINT(21) NOT NULL DEFAULT '0'
) ENGINE=MyISAM;


-- Dumping structure for view nchucsnews.newsbydate
DROP VIEW IF EXISTS `newsbydate`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `newsbydate` (
	`date` VARBINARY(10) NOT NULL DEFAULT '',
	`cnt` BIGINT(21) NOT NULL DEFAULT '0'
) ENGINE=MyISAM;


-- Dumping structure for table nchucsnews.news_error
DROP TABLE IF EXISTS `news_error`;
CREATE TABLE IF NOT EXISTS `news_error` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `url` text COLLATE utf8_unicode_ci NOT NULL COMMENT '網址',
  `msg` text COLLATE utf8_unicode_ci NOT NULL COMMENT '信息',
  `t` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '時間',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.ontology
DROP TABLE IF EXISTS `ontology`;
CREATE TABLE IF NOT EXISTS `ontology` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `nid` int(10) NOT NULL,
  `weight` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.recommend
DROP TABLE IF EXISTS `recommend`;
CREATE TABLE IF NOT EXISTS `recommend` (
  `recoid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `date` date NOT NULL,
  `method` int(11) NOT NULL,
  `nids` text COLLATE utf8_unicode_ci NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`recoid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='推薦表格';

-- Data exporting was unselected.


-- Dumping structure for view nchucsnews.ridnewswordsum
DROP VIEW IF EXISTS `ridnewswordsum`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `ridnewswordsum` (
	`rid` INT(11) NULL DEFAULT NULL COMMENT '從哪一個rss抓來的',
	`nid` INT(11) NOT NULL COMMENT '新聞ID',
	`cnt` BIGINT(21) NOT NULL DEFAULT '0',
	`sum` DECIMAL(32,0) NULL DEFAULT NULL
) ENGINE=MyISAM;


-- Dumping structure for table nchucsnews.rss
DROP TABLE IF EXISTS `rss`;
CREATE TABLE IF NOT EXISTS `rss` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'RSS ID',
  `name` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'RSS 名稱',
  `rss` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'RSS 網址',
  `proc` int(11) NOT NULL COMMENT '抓取的程式ID',
  `varible` text COLLATE utf8_unicode_ci NOT NULL COMMENT '紀錄用變數，可記錄讀取到第幾筆',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='RSS紀錄表格';

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.setting
DROP TABLE IF EXISTS `setting`;
CREATE TABLE IF NOT EXISTS `setting` (
  `key` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '名稱',
  `val` text COLLATE utf8_unicode_ci NOT NULL COMMENT '內容',
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='系統變數';

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.similarity
DROP TABLE IF EXISTS `similarity`;
CREATE TABLE IF NOT EXISTS `similarity` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid1` int(10) NOT NULL,
  `uid2` int(10) NOT NULL,
  `simi` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid1` (`uid1`),
  KEY `uid2` (`uid2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'uid',
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `pass` text COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `gender` enum('男','女') COLLATE utf8_unicode_ci NOT NULL,
  `birth` date NOT NULL,
  `login_t` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `fb` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'facebook Email',
  `setting` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者資料';

-- Data exporting was unselected.


-- Dumping structure for table nchucsnews.viewlog
DROP TABLE IF EXISTS `viewlog`;
CREATE TABLE IF NOT EXISTS `viewlog` (
  `vid` int(11) NOT NULL AUTO_INCREMENT COMMENT '瀏覽ID',
  `uid` int(11) NOT NULL COMMENT '使用者ID',
  `nid` int(11) NOT NULL COMMENT '新聞ID',
  `view_t` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '瀏覽時間',
  PRIMARY KEY (`vid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='瀏覽紀錄';

-- Data exporting was unselected.


-- Dumping structure for view nchucsnews.viewlog_rid
DROP VIEW IF EXISTS `viewlog_rid`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `viewlog_rid` (
	`uid` INT(11) NOT NULL COMMENT '使用者ID',
	`nid` INT(11) NOT NULL COMMENT '新聞ID',
	`view_t` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '瀏覽時間',
	`rid` INT(11) NULL DEFAULT NULL COMMENT '從哪一個rss抓來的'
) ENGINE=MyISAM;


-- Dumping structure for table nchucsnews.word
DROP TABLE IF EXISTS `word`;
CREATE TABLE IF NOT EXISTS `word` (
  `wid` int(11) NOT NULL AUTO_INCREMENT COMMENT '詞ID',
  `val` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '詞內容',
  `last` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後日期',
  `ban` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '標記是否是黑名單',
  PRIMARY KEY (`wid`),
  KEY `val` (`val`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='詞語紀錄表';

-- Data exporting was unselected.


-- Dumping structure for view nchucsnews.wordridsum
DROP VIEW IF EXISTS `wordridsum`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `wordridsum` (
	`wid` INT(11) NOT NULL COMMENT '詞語ID',
	`rid` INT(11) NULL DEFAULT NULL COMMENT '從哪一個rss抓來的',
	`cnt` BIGINT(21) NOT NULL DEFAULT '0',
	`sum` DECIMAL(32,0) NULL DEFAULT NULL
) ENGINE=MyISAM;


-- Dumping structure for view nchucsnews.news2wordg
DROP VIEW IF EXISTS `news2wordg`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `news2wordg`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` VIEW `news2wordg` AS select `news2word`.* from `news2word` left join `word` on `news2word`.`wid` = `word`.`wid` where `word`.`ban` = 0 ;


-- Dumping structure for view nchucsnews.newsbycategory
DROP VIEW IF EXISTS `newsbycategory`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `newsbycategory`;
CREATE ALGORITHM=UNDEFINED DEFINER=`taichunmin`@`%` VIEW `newsbycategory` AS select `rss`.`rid`,`rss`.`name`, count(`nid`) as 'cnt' from `news`,`rss` where `rss`.`rid`=`news`.`rid` group by `rss`.`rid` ;


-- Dumping structure for view nchucsnews.newsbydate
DROP VIEW IF EXISTS `newsbydate`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `newsbydate`;
CREATE ALGORITHM=UNDEFINED DEFINER=`taichunmin`@`%` VIEW `newsbydate` AS select LEFT(`news_t`,10) as 'date', count(`nid`) as 'cnt' from `news` group by LEFT(`news_t`,10) ;


-- Dumping structure for view nchucsnews.ridnewswordsum
DROP VIEW IF EXISTS `ridnewswordsum`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `ridnewswordsum`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` VIEW `ridnewswordsum` AS select `news`.`rid`,`news2word`.`nid`,count(`wid`) as 'cnt',sum(`cnt`) as 'sum' from `news2word` left join `news` on `news2word`.`nid`=`news`.`nid` group by `nid` ;


-- Dumping structure for view nchucsnews.viewlog_rid
DROP VIEW IF EXISTS `viewlog_rid`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `viewlog_rid`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` VIEW `viewlog_rid` AS select uid,viewlog.nid,view_t,rid from viewlog left join news on viewlog.nid = news.nid ;


-- Dumping structure for view nchucsnews.wordridsum
DROP VIEW IF EXISTS `wordridsum`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `wordridsum`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` VIEW `wordridsum` AS select news2word.wid,news.rid,count(`cnt`) as 'cnt',sum(`cnt`) as 'sum' from news2word left join news on news2word.nid = news.nid group by news.`rid`,`wid` ;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
