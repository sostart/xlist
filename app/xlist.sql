/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50156
Source Host           : localhost:3306
Source Database       : xlist

Target Server Type    : MYSQL
Target Server Version : 50156
File Encoding         : 65001

Date: 2017-06-09 17:47:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xlist_lists`
-- ----------------------------
DROP TABLE IF EXISTS `xlist_lists`;
CREATE TABLE `xlist_lists` (
  `id` char(36) NOT NULL DEFAULT '' COMMENT 'UUID',
  `noumenon_id` char(36) NOT NULL DEFAULT '0' COMMENT '镜像的本体ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者',
  `pid` char(36) NOT NULL DEFAULT '0' COMMENT '父级',
  `title` varchar(1000) NOT NULL DEFAULT '',
  `content` varchar(255) NOT NULL DEFAULT '',
  `members` varchar(255) NOT NULL DEFAULT '' COMMENT '成员名,使用逗号分隔',
  `is_composer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '协作 1',
  `sort` decimal(16,15) unsigned NOT NULL DEFAULT '0.000000000000000' COMMENT '排序越大越靠后',
  `is_expand` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已展开',
  `is_completed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已完成',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xlist_lists
-- ----------------------------

-- ----------------------------
-- Table structure for `xlist_users`
-- ----------------------------
DROP TABLE IF EXISTS `xlist_users`;
CREATE TABLE `xlist_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xlist_users
-- ----------------------------
INSERT INTO `xlist_users` VALUES ('1', 'Mingo', '3faf3f2d6766c02cf4b6ac4f33e86ce3');
INSERT INTO `xlist_users` VALUES ('2', 'Jason', '3faf3f2d6766c02cf4b6ac4f33e86ce3');
INSERT INTO `xlist_users` VALUES ('3', 'Robin', '3faf3f2d6766c02cf4b6ac4f33e86ce3');

-- ----------------------------
-- View structure for `ttt`
-- ----------------------------
DROP VIEW IF EXISTS `ttt`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ttt` AS select `l`.`uid` AS `uid` from (`xlist_lists` `l` join `xlist_user_lists` `ul`) ;
