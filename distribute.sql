/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : distribute

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-11-26 00:36:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bas_application`
-- ----------------------------
DROP TABLE IF EXISTS `bas_application`;
CREATE TABLE `bas_application` (
  `appId` int(10) NOT NULL AUTO_INCREMENT COMMENT '应用自增ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `appName` varchar(100) NOT NULL DEFAULT '' COMMENT '应用名称',
  `appUrl` varchar(255) DEFAULT NULL COMMENT 'app链接',
  `sortUrl` varchar(255) DEFAULT NULL COMMENT 'app短链接',
  `appIcon` varchar(200) DEFAULT NULL COMMENT 'APP图标',
  `size` int(10) NOT NULL DEFAULT '0' COMMENT '文件大小，单位B',
  `download` int(10) NOT NULL DEFAULT '0' COMMENT '总下载数量',
  `describe` varchar(1000) DEFAULT NULL COMMENT '应用描述',
  `android` varchar(50) DEFAULT NULL COMMENT '安卓包名',
  `ios` varchar(50) DEFAULT NULL COMMENT 'IOS包名',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0启用，1禁用',
  `appImage` varchar(500) DEFAULT NULL COMMENT '应用介绍图',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`appId`),
  KEY `pk_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_application
-- ----------------------------
INSERT INTO `bas_application` VALUES ('1', '10', '测试', 'http://www.baidu.com', 'http://www.baidu.com', null, '102182', '100', '《光明勇士》是一款款萌趣装备沙盒化手游，主打元气Q萌的画风、萌系武器和衣装的自定义组装、boss外观和武器技能的所见即所得。亿种装备外观任性组装！酷萌时装坐骑，闪亮你的个性秀场！策略副本玩不停，趣味玩法乐翻天，冒险小队现在出发！', 'com.aa.s', null, '0', 'https://pp.myapp.com/ma_pic2/0/shot_52754163_2_1542787051/550.png,https://pp.myapp.com/ma_pic2/0/shot_52754163_3_1542787051/550.png,https://pp.myapp.com/ma_pic2/0/shot_52754163_4_1542787051/550.png', '1543086731', '1543078346');

-- ----------------------------
-- Table structure for `bas_application_down`
-- ----------------------------
DROP TABLE IF EXISTS `bas_application_down`;
CREATE TABLE `bas_application_down` (
  `downId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '下载自增ID',
  `appId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载APPid',
  `apkId` int(10) NOT NULL DEFAULT '0' COMMENT 'apkID',
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '下载版本号',
  `user` char(32) NOT NULL DEFAULT '' COMMENT '下载用户',
  `prov` varchar(20) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(20) NOT NULL DEFAULT '' COMMENT '城市',
  `region` varchar(20) NOT NULL DEFAULT '' COMMENT '地区',
  `lng` float(10,7) NOT NULL DEFAULT '0.0000000' COMMENT '经度',
  `lat` float(10,7) NOT NULL DEFAULT '0.0000000' COMMENT '纬度',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`downId`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_application_down
-- ----------------------------
INSERT INTO `bas_application_down` VALUES ('1', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('2', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('3', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('4', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('5', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('6', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('7', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('8', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('9', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('10', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('11', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('12', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('13', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('14', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('15', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('16', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('17', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('18', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('19', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('20', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');
INSERT INTO `bas_application_down` VALUES ('21', '1', '1', 'v1.0.0', '', '', '', '', '0.0000000', '0.0000000', '1543053359');

-- ----------------------------
-- Table structure for `bas_application_version`
-- ----------------------------
DROP TABLE IF EXISTS `bas_application_version`;
CREATE TABLE `bas_application_version` (
  `apkId` int(10) NOT NULL AUTO_INCREMENT COMMENT 'apkId号',
  `appId` int(10) NOT NULL DEFAULT '0' COMMENT '对应应用ID',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '内部版本号',
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '外部版本号',
  `size` int(10) NOT NULL DEFAULT '0' COMMENT '文件大小，单位B',
  `appUrl` varchar(255) NOT NULL DEFAULT '' COMMENT 'app链接',
  `packageName` varchar(100) DEFAULT NULL COMMENT '包名',
  `platform` enum('ios','android') NOT NULL DEFAULT 'android' COMMENT '平台',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，1上线，0下线',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`apkId`),
  KEY `pk_appId_createTime` (`appId`,`createTime`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_application_version
-- ----------------------------
INSERT INTO `bas_application_version` VALUES ('1', '1', '2019013', 'v1.0.0', '1654545', 'http://www.baidu.com', 'com.aa.s', 'android', '第一个版本更新', '0', '1543053359', null);

-- ----------------------------
-- Table structure for `bas_package`
-- ----------------------------
DROP TABLE IF EXISTS `bas_package`;
CREATE TABLE `bas_package` (
  `packageId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '套餐ID',
  `packageName` varchar(100) NOT NULL DEFAULT '' COMMENT '套餐名称',
  `packageType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '套餐类型 0综合套餐，1增值套餐',
  `upload` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传数量',
  `download` int(10) NOT NULL DEFAULT '0' COMMENT '下载数量',
  `price` int(10) NOT NULL DEFAULT '0' COMMENT '价格，单位（分）',
  `day` int(10) NOT NULL DEFAULT '31' COMMENT '套餐天数，默认一个月',
  `isDelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updateTime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`packageId`),
  UNIQUE KEY `idx_uid_imei` (`packageId`,`packageName`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_package
-- ----------------------------
INSERT INTO `bas_package` VALUES ('4', '测试套餐名称1213', '0', '100', '100', '5100', '31', '0', '0', '1543061039');
INSERT INTO `bas_package` VALUES ('10', '我是测试套餐名', '1', '0', '100', '10000', '31', '0', '1543060261', '1543060261');
INSERT INTO `bas_package` VALUES ('11', '我是测试套餐名2', '0', '0', '2000', '20000', '31', '0', '1543060278', '1543060278');
INSERT INTO `bas_package` VALUES ('12', '我是测试套餐名3', '0', '0', '3000', '29999', '31', '0', '1543060329', '1543060329');
INSERT INTO `bas_package` VALUES ('13', '我是测试套餐名34', '0', '100', '500', '10000', '31', '0', '1543060397', '1543060397');

-- ----------------------------
-- Table structure for `bas_pay`
-- ----------------------------
DROP TABLE IF EXISTS `bas_pay`;
CREATE TABLE `bas_pay` (
  `payId` tinyint(1) NOT NULL,
  `wechat` varchar(100) NOT NULL DEFAULT '' COMMENT 'wechat appid',
  `secret` varchar(100) NOT NULL DEFAULT '' COMMENT 'secret',
  `mchid` varchar(50) NOT NULL DEFAULT '',
  `apikey` varchar(100) NOT NULL,
  `alipay` varchar(100) NOT NULL DEFAULT '' COMMENT 'alipay appid',
  `privateKey` varchar(255) NOT NULL DEFAULT '',
  `publicKey` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`payId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_pay
-- ----------------------------
INSERT INTO `bas_pay` VALUES ('1', 'wxcebf7060d1b21232a', '513497388d2b0313fe385af6af83611a', '100261231a', '085BD8F449F069E4DB3121214CE0AFCa', '20180118019121321a', 'MIIEogIBAAKCAQEAxknS9Om6sosc4NN2rL49OLdgSedizMr2lfPHgnqyQHnXRYMmN1kTwBDzI4qaAaHPVczMvcIcla', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAosLOrQjXWNaYWdSTxrLdiaycSk4iqVPaJidwx0xIO26bq8fxHdkuGPFXWkvaQlHRoa');

-- ----------------------------
-- Table structure for `bas_templet`
-- ----------------------------
DROP TABLE IF EXISTS `bas_templet`;
CREATE TABLE `bas_templet` (
  `templetId` int(10) NOT NULL AUTO_INCREMENT COMMENT '通知消息',
  `templetType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '通知模板类型',
  `title` varchar(100) NOT NULL DEFAULT '',
  `message` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0启用，1禁用',
  `updateTime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`templetId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_templet
-- ----------------------------
INSERT INTO `bas_templet` VALUES ('1', '0', '用户注册成功', '测试下修改111', '0', '1543083796');
INSERT INTO `bas_templet` VALUES ('2', '1', '用户APP被下架', '121手动11额1阿21', '0', '1543083190');
INSERT INTO `bas_templet` VALUES ('3', '2', '新套餐的推出', '', '0', '1543082523');
INSERT INTO `bas_templet` VALUES ('4', '3', '公告', '', '0', '0');

-- ----------------------------
-- Table structure for `bas_user`
-- ----------------------------
DROP TABLE IF EXISTS `bas_user`;
CREATE TABLE `bas_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '绑定手机',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '账号密码',
  `salt` char(4) NOT NULL DEFAULT '' COMMENT '与密码加密随机字符串',
  `wechat` varchar(100) DEFAULT NULL COMMENT '微信账号',
  `imNumber` varchar(20) DEFAULT NULL COMMENT 'QQ号',
  `company` varchar(150) DEFAULT NULL COMMENT '公司名称',
  `job` varchar(50) DEFAULT NULL COMMENT '职位',
  `token` char(32) NOT NULL DEFAULT '' COMMENT '登录token',
  `upload` int(11) unsigned NOT NULL DEFAULT '2' COMMENT 'app上传数量',
  `download` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '每日下载数量',
  `surplus` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余下载点数',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户状态0正常，1禁用',
  `realname` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '实名验证0待审核，1审核通过，2审核失败',
  `packageId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '套餐ID',
  `identityCard` varchar(1000) NOT NULL DEFAULT '' COMMENT '身份证3张图片，逗号分隔，1正面，2反面，3手持',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expireTime` int(11) DEFAULT '0' COMMENT '套餐到期时间',
  `loginTime` int(11) DEFAULT NULL COMMENT '最近登录时间',
  `updateTime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_user
-- ----------------------------
INSERT INTO `bas_user` VALUES ('10', '4402393@qq.com', '', '989593c51c760c68805897e627297b2f', 'butA', 'test1', '4402393', '测试公司', '开发主管', '', '2', '0', '0', '0', '0', '4', 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1543057694864&di=dcea4e8f30534b08edec9a957aed9725&imgtype=0&src=http%3A%2F%2F5b0988e595225.cdn.sohucs.com%2Fimages%2F20171213%2F7f67f9bbd2674008acabaeee278e2d55.jpeg,https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1543057720231&di=91c4c5ea6bfeef8021f49d75784aaf2f&imgtype=0&src=http%3A%2F%2F5b0988e595225.cdn.sohucs.com%2Fimages%2F20181102%2F33105fdb424a48e4abe83db1937e0963.jpeg,https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1543652576&di=af02f8667b4274a9b1c9a3b68f541c73&imgtype=jpg&er=1&src=http%3A%2F%2F5b0988e595225.cdn.sohucs.com%2Fimages%2F20170824%2F5dca0d2cb1744592ada3fa25ed3e3eb3.jpeg', '1543086731', '0', '1542869325', '1543051059');

-- ----------------------------
-- Table structure for `bas_user_renewals`
-- ----------------------------
DROP TABLE IF EXISTS `bas_user_renewals`;
CREATE TABLE `bas_user_renewals` (
  `recId` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `orderNum` char(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `tradeNum` varchar(64) NOT NULL DEFAULT '' COMMENT '支付宝、微信交易订单号',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `oldPackage` int(10) NOT NULL DEFAULT '0' COMMENT '旧套餐ID',
  `packageId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '续费套餐ID',
  `original` int(10) NOT NULL DEFAULT '0' COMMENT '原价',
  `price` int(10) NOT NULL DEFAULT '0' COMMENT '实付',
  `deductible` int(10) NOT NULL DEFAULT '0' COMMENT '剩余套餐抵扣',
  `package` varchar(255) NOT NULL DEFAULT '' COMMENT '续费套餐详细',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态，0待付款，1已付款',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `payType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付方式 0支付宝，1微信',
  `createTime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expiryTime` int(11) NOT NULL DEFAULT '0' COMMENT '订单到期时间，30分钟不支付失效',
  `payTime` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  PRIMARY KEY (`recId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of bas_user_renewals
-- ----------------------------
INSERT INTO `bas_user_renewals` VALUES ('1', '', '', '0', '0', '4', '0', '100000', '0', '', '1', '0', '0', '1543083131', '0', '0');

-- ----------------------------
-- Table structure for `sys_admin`
-- ----------------------------
DROP TABLE IF EXISTS `sys_admin`;
CREATE TABLE `sys_admin` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员自增ID',
  `account` varchar(20) NOT NULL DEFAULT '' COMMENT '登陆账号',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '管理员用户密码（与salt随机字符串md5加密）',
  `salt` char(4) NOT NULL DEFAULT '' COMMENT '与密码加密随机字符串',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `createTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建事件',
  `updateTime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `loginTime` int(11) unsigned DEFAULT NULL COMMENT '最后一次登陆时间',
  `loginIp` varchar(30) DEFAULT NULL COMMENT '登陆IP',
  `loginCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `token` char(32) DEFAULT NULL COMMENT '登陆凭据',
  PRIMARY KEY (`aid`),
  UNIQUE KEY `unique_login` (`account`) USING BTREE,
  UNIQUE KEY `unique_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='管理员用户表';

-- ----------------------------
-- Records of sys_admin
-- ----------------------------
INSERT INTO `sys_admin` VALUES ('1', 'admin', '64b53f33c01213d4052f5d0038f2ed77', 'HoDO', '我是超级管理员', '0', '1543030823', '1543030823', '192.168.31.247', '35', '9b4877d4d2ce76efacc77cf299e5b819');
