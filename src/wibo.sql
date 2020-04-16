/*
Navicat MySQL Data Transfer

Source Server         : IM-mysql
Source Server Version : 50729
Source Host           : 192.168.91.172:3306
Source Database       : wibo

Target Server Type    : MYSQL
Target Server Version : 50729
File Encoding         : 65001

Date: 2020-04-16 18:23:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for message_list
-- ----------------------------
DROP TABLE IF EXISTS `message_list`;
CREATE TABLE `message_list` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(22) DEFAULT NULL COMMENT '用户id',
  `target_id` int(22) DEFAULT NULL COMMENT '好友id,群聊时候为空',
  `type` tinyint(3) unsigned DEFAULT '1' COMMENT '类型id 1-好友 2-群聊',
  `name` varchar(255) DEFAULT NULL COMMENT '消息名称',
  `status` tinyint(2) DEFAULT '1' COMMENT '分组状态 1-启用 2-已删除',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='会话列表';

-- ----------------------------
-- Records of message_list
-- ----------------------------
INSERT INTO `message_list` VALUES ('3', '80', null, '2', null, '1', '2020-03-26 03:42:15', '2020-03-26 03:42:15');
INSERT INTO `message_list` VALUES ('4', '80', null, '2', null, '1', '2020-03-26 03:43:30', '2020-03-26 03:43:30');
INSERT INTO `message_list` VALUES ('5', '80', '83', '1', null, '1', '2020-03-26 04:14:59', '2020-03-26 04:14:59');
INSERT INTO `message_list` VALUES ('6', '80', '81', '1', null, '1', '2020-03-26 04:16:43', '2020-03-26 04:16:43');
INSERT INTO `message_list` VALUES ('7', '80', '82', '1', null, '1', '2020-03-26 04:16:51', '2020-03-26 04:16:51');

-- ----------------------------
-- Table structure for message_list_detail
-- ----------------------------
DROP TABLE IF EXISTS `message_list_detail`;
CREATE TABLE `message_list_detail` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT,
  `message_from` int(22) DEFAULT NULL COMMENT '消息来源',
  `message_to` int(22) DEFAULT NULL,
  `message_list_id` int(22) DEFAULT NULL,
  `type` tinyint(255) DEFAULT '1' COMMENT '消息类型1-普通消息2-邀请类消息通知',
  `status` tinyint(2) DEFAULT '1' COMMENT '1-正常 2-已删除 3-已撤回',
  `content` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='具体的消息列表';

-- ----------------------------
-- Records of message_list_detail
-- ----------------------------
INSERT INTO `message_list_detail` VALUES ('5', '80', '83', '5', '1', '1', 'wibo_test_message_content3', '2020-03-26 04:14:59', '2020-03-26 04:14:59');
INSERT INTO `message_list_detail` VALUES ('6', '80', '3', '3', '1', '1', 'wibo_test_message_333', '2020-03-26 04:15:46', '2020-03-26 04:15:46');
INSERT INTO `message_list_detail` VALUES ('7', '80', '81', '6', '1', '1', 'wibo_test_message_81', '2020-03-26 04:16:43', '2020-03-26 04:16:43');
INSERT INTO `message_list_detail` VALUES ('8', '80', '82', '7', '1', '1', 'wibo_test_message_82', '2020-03-26 04:16:51', '2020-03-26 04:16:51');

-- ----------------------------
-- Table structure for message_user_list
-- ----------------------------
DROP TABLE IF EXISTS `message_user_list`;
CREATE TABLE `message_user_list` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(22) DEFAULT NULL COMMENT '用户id',
  `message_list_id` int(22) DEFAULT NULL COMMENT '消息id',
  `name` varchar(255) DEFAULT NULL COMMENT '群组中名称',
  `status` tinyint(2) DEFAULT '1' COMMENT '分组状态 1-启用 2-已剔除',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='消息群组人员列表';

-- ----------------------------
-- Records of message_user_list
-- ----------------------------
INSERT INTO `message_user_list` VALUES ('4', '81', '3', null, '1', '2020-03-26 03:42:15', '2020-03-26 03:42:15');
INSERT INTO `message_user_list` VALUES ('5', '80', '3', null, '1', '2020-03-26 03:42:15', '2020-03-26 03:42:15');
INSERT INTO `message_user_list` VALUES ('6', '82', '4', null, '1', '2020-03-26 03:43:30', '2020-03-26 03:43:30');
INSERT INTO `message_user_list` VALUES ('7', '84', '4', null, '1', '2020-03-26 03:43:42', '2020-03-26 03:43:42');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `username` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户名称',
  `password` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '密码(md5(md5(pw)+salt)',
  `salt` varchar(255) DEFAULT NULL COMMENT 'salt值',
  `nick_name` varchar(255) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '1' COMMENT '0-停用 1-启用',
  `sign` varchar(255) DEFAULT NULL COMMENT '个性签名',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('80', 'wibo', '842123d15a2aa9c319ffc5b04e560865', 'UbsWAl', 'wibo_name', '1', null, '1197389486@qq.com', '2020-03-25 12:58:50', '2020-03-25 12:58:50');
INSERT INTO `user` VALUES ('81', 'test', '842123d15a2aa9c319ffc5b04e560865', 'UbsWAl', 'test_name', '1', null, null, '2020-03-25 12:58:56', '2020-03-25 12:58:56');
INSERT INTO `user` VALUES ('82', 'test1', '761e2865a39f393bb5ecf27be1d321e6', 't945zH', 'test1_name', '1', null, '1197389486@qq.com', '2020-03-25 12:59:00', '2020-03-25 12:59:00');
INSERT INTO `user` VALUES ('83', 'admin123', '619b13164301bc6be41fd80908509799', 'EnqNFe', 'admin123_name', '1', null, '1197389486@qq.com', '2020-03-25 12:59:05', '2020-03-25 12:59:05');

-- ----------------------------
-- Table structure for user_friend
-- ----------------------------
DROP TABLE IF EXISTS `user_friend`;
CREATE TABLE `user_friend` (
  `id` int(22) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(22) DEFAULT NULL COMMENT '会员id',
  `type` tinyint(2) DEFAULT '1' COMMENT '好友类型 1-好友 2-群组',
  `f_id` int(22) DEFAULT NULL COMMENT '还有id',
  `status` tinyint(2) DEFAULT '1' COMMENT '用户状态0-停用1-启用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user_friend
-- ----------------------------
INSERT INTO `user_friend` VALUES ('1', '82', '1', '81', '1', '2020-03-25 07:23:57', '2020-03-25 07:23:57');
INSERT INTO `user_friend` VALUES ('2', '83', '1', '81', '1', '2020-03-25 07:30:46', '2020-03-25 07:30:46');
INSERT INTO `user_friend` VALUES ('3', '80', '1', '83', '1', '2020-03-25 12:41:31', '2020-03-25 12:41:31');
INSERT INTO `user_friend` VALUES ('5', '80', '1', '81', '1', '2020-03-25 12:51:02', '2020-03-25 12:51:02');
INSERT INTO `user_friend` VALUES ('6', '80', '1', '82', '1', '2020-03-25 12:51:08', '2020-03-25 12:51:08');
