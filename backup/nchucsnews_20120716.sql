-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生日期: 2012 年 07 月 16 日 15:44
-- 伺服器版本: 6.0.4-alpha-community-log
-- PHP 版本: 6.0.0-dev

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `nchucsnews`
--

-- --------------------------------------------------------

--
-- 表的結構 `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `nid` int(11) NOT NULL COMMENT '新聞ID',
  `title` text COLLATE utf8_unicode_ci NOT NULL COMMENT '標題',
  `article` text COLLATE utf8_unicode_ci NOT NULL COMMENT '新聞內容',
  `news_t` datetime NOT NULL COMMENT '新聞時間',
  `url` text COLLATE utf8_unicode_ci NOT NULL COMMENT '新聞網址',
  `viewcnt` int(11) NOT NULL COMMENT '瀏覽次數',
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='新聞';

-- --------------------------------------------------------

--
-- 表的結構 `news2word`
--

CREATE TABLE IF NOT EXISTS `news2word` (
  `i` int(11) NOT NULL AUTO_INCREMENT COMMENT '無意義',
  `nid` int(11) NOT NULL COMMENT '新聞ID',
  `wid` int(11) NOT NULL COMMENT '詞語ID',
  PRIMARY KEY (`i`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='新聞與詞語對應' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `rss`
--

CREATE TABLE IF NOT EXISTS `rss` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'RSS ID',
  `name` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'RSS 名稱',
  `rss` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'RSS 網址',
  `proc` int(11) NOT NULL COMMENT '抓取的程式ID',
  `varible` text COLLATE utf8_unicode_ci NOT NULL COMMENT '紀錄用變數，可記錄讀取到第幾筆',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='RSS紀錄表格' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `key` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '名稱',
  `val` text COLLATE utf8_unicode_ci NOT NULL COMMENT '內容',
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='系統變數';

-- --------------------------------------------------------

--
-- 表的結構 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'uid',
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `pass` text COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `gender` enum('男','女') COLLATE utf8_unicode_ci NOT NULL,
  `birth` date NOT NULL,
  `login_t` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `fb` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'facebook Email',
  `recommend` text COLLATE utf8_unicode_ci NOT NULL COMMENT '建議的新聞',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='使用者資料' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `viewlog`
--

CREATE TABLE IF NOT EXISTS `viewlog` (
  `vid` int(11) NOT NULL AUTO_INCREMENT COMMENT '瀏覽ID',
  `uid` int(11) NOT NULL COMMENT '使用者ID',
  `nid` int(11) NOT NULL COMMENT '新聞ID',
  `view_t` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '瀏覽時間',
  PRIMARY KEY (`vid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='瀏覽紀錄' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `word`
--

CREATE TABLE IF NOT EXISTS `word` (
  `wid` int(11) NOT NULL AUTO_INCREMENT COMMENT '詞ID',
  `val` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '詞內容',
  `cnt` int(11) NOT NULL COMMENT '計數器',
  `last` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '最後日期',
  PRIMARY KEY (`wid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='詞語紀錄表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
