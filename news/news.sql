-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-10-11 03:02:52
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `news`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_article`
--

CREATE TABLE IF NOT EXISTS `zx_article` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(50) NOT NULL DEFAULT '工大学子' COMMENT '原文作者',
  `time` int(11) NOT NULL,
  `edittime` int(11) NOT NULL,
  `editor` int(11) NOT NULL DEFAULT '0' COMMENT '编写者',
  `from` varchar(50) NOT NULL,
  `fromurl` varchar(120) DEFAULT NULL,
  `type` int(11) NOT NULL COMMENT '资讯分类',
  `thumb` varchar(100) DEFAULT NULL COMMENT '缩略图',
  `top` int(11) NOT NULL DEFAULT '0',
  `click` int(11) NOT NULL,
  `realclick` int(11) DEFAULT '0',
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `examine` int(11) DEFAULT '0',
  `reviewer` int(11) DEFAULT NULL COMMENT '审核员',
  `reviewtime` int(11) DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- 表的结构 `zx_category`
--

CREATE TABLE IF NOT EXISTS `zx_category` (
  `cid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '分类名称'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_guideinfo`
--

CREATE TABLE IF NOT EXISTS `zx_guideinfo` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  `avater` varchar(40) DEFAULT NULL,
  `blogcount` int(5) DEFAULT '0',
  `commentcount` int(5) DEFAULT '0',
  `visitcount` int(5) DEFAULT '0',
  `college` int(5) DEFAULT NULL,
  `guideid` int(20) DEFAULT NULL,
  `blogname` varchar(20) DEFAULT NULL,
  `collegename` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=98 ;

-- --------------------------------------------------------

--
-- 表的结构 `zx_subscription`
--

CREATE TABLE IF NOT EXISTS `zx_subscription` (
  `uid` int(11) NOT NULL,
  `tag` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_tag`
--

CREATE TABLE IF NOT EXISTS `zx_tag` (
  `aid` int(11) NOT NULL,
  `tag` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_user`
--

CREATE TABLE IF NOT EXISTS `zx_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `pwd` varchar(32) NOT NULL,
  `realname` varchar(30) NOT NULL,
  `avatar` varchar(100) NOT NULL COMMENT '头像',
  `type` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
