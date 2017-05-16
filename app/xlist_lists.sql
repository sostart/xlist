/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50156
Source Host           : localhost:3306
Source Database       : xlist

Target Server Type    : MYSQL
Target Server Version : 50156
File Encoding         : 65001

Date: 2017-05-16 17:30:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `xlist_lists`
-- ----------------------------
DROP TABLE IF EXISTS `xlist_lists`;
CREATE TABLE `xlist_lists` (
  `id` char(36) NOT NULL DEFAULT '' COMMENT 'UUID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者',
  `pid` char(36) NOT NULL DEFAULT '0' COMMENT '父级',
  `title` varchar(1000) NOT NULL DEFAULT '',
  `content` varchar(255) NOT NULL DEFAULT '',
  `members` varchar(255) NOT NULL DEFAULT '' COMMENT '成员名,使用逗号分隔',
  `is_completed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已完成',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已删除',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xlist_lists
-- ----------------------------
INSERT INTO `xlist_lists` VALUES ('2cd8da41-fc70-1dea-4dd7-ee0f3461efd5', '1', '0', '123', '', '', '1', '0', '1494901105', '1494903024');
INSERT INTO `xlist_lists` VALUES ('b0a86c7b-8c51-2f13-1727-3179798f66b8', '1', '0', '123123', '', '', '0', '0', '1494903042', '1494903045');
INSERT INTO `xlist_lists` VALUES ('a0c154dd-d415-085c-00b1-4b073ebebf9e', '1', '0', '', '', '', '0', '0', '1494917755', '0');

-- ----------------------------
-- Table structure for `xlist_user_lists`
-- ----------------------------
DROP TABLE IF EXISTS `xlist_user_lists`;
CREATE TABLE `xlist_user_lists` (
  `uid` int(10) unsigned NOT NULL COMMENT '0',
  `lid` char(36) NOT NULL DEFAULT '',
  `pid` char(36) NOT NULL DEFAULT '0' COMMENT '父级',
  `sort` decimal(16,15) unsigned NOT NULL DEFAULT '0.000000000000000' COMMENT '排序越大越靠后',
  `is_expand` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已展开',
  UNIQUE KEY `uid_lid` (`uid`,`lid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xlist_user_lists
-- ----------------------------
INSERT INTO `xlist_user_lists` VALUES ('1', '2cd8da41-fc70-1dea-4dd7-ee0f3461efd5', '0', '0.100000000000000', '1');
INSERT INTO `xlist_user_lists` VALUES ('1', 'b0a86c7b-8c51-2f13-1727-3179798f66b8', '2cd8da41-fc70-1dea-4dd7-ee0f3461efd5', '0.100000000000000', '0');
INSERT INTO `xlist_user_lists` VALUES ('1', 'a0c154dd-d415-085c-00b1-4b073ebebf9e', '0', '0.200000000000000', '0');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xlist_users
-- ----------------------------
INSERT INTO `xlist_users` VALUES ('1', 'Mingo', '3faf3f2d6766c02cf4b6ac4f33e86ce3');
