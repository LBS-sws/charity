/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : charitydev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-09-01 10:31:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cy_credit_point
-- ----------------------------
DROP TABLE IF EXISTS `cy_credit_point`;
CREATE TABLE `cy_credit_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `request_id` int(11) NOT NULL COMMENT '慈善分記錄表id',
  `long_type` int(2) NOT NULL DEFAULT '1',
  `year` int(11) NOT NULL COMMENT '年限',
  `start_num` int(11) NOT NULL COMMENT '開始分數',
  `end_num` int(11) DEFAULT NULL COMMENT '結束分數',
  `use_prize` varchar(255) DEFAULT NULL COMMENT '兑换該慈善分的id   ,號分割(未實現)',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='學分年度扣減記錄表';

-- ----------------------------
-- Table structure for cy_credit_request
-- ----------------------------
DROP TABLE IF EXISTS `cy_credit_request`;
CREATE TABLE `cy_credit_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `credit_type` int(11) NOT NULL COMMENT '慈善分配置id',
  `credit_point` int(11) NOT NULL COMMENT '慈善分分數',
  `apply_date` date DEFAULT NULL COMMENT '申請日期',
  `audit_date` date DEFAULT NULL COMMENT '審核日期',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕原因',
  `images_url` varchar(255) DEFAULT NULL COMMENT '圖片地址',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='慈善分申請表';

-- ----------------------------
-- Table structure for cy_credit_type
-- ----------------------------
DROP TABLE IF EXISTS `cy_credit_type`;
CREATE TABLE `cy_credit_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `charity_code` varchar(255) DEFAULT NULL,
  `charity_name` varchar(255) NOT NULL COMMENT '慈善分名稱',
  `charity_point` int(11) NOT NULL DEFAULT '0' COMMENT '慈善分數值',
  `rule` text COMMENT '得分条件',
  `year_sw` int(2) NOT NULL DEFAULT '0' COMMENT '0:無年限限制  1：有年限限制',
  `year_max` int(11) DEFAULT '0' COMMENT '每年限制申請次數',
  `validity` int(11) NOT NULL DEFAULT '5' COMMENT '有效期 1:1年有效期  5:5年有效期',
  `z_index` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `bumen` text COMMENT '適用範圍',
  `bumen_ex` text,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='慈善分配置表';

-- ----------------------------
-- Table structure for cy_prize_request
-- ----------------------------
DROP TABLE IF EXISTS `cy_prize_request`;
CREATE TABLE `cy_prize_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL COMMENT '員工id',
  `prize_type` int(11) NOT NULL COMMENT '兑换配置id',
  `prize_point` int(11) NOT NULL COMMENT '扣減慈善分',
  `apply_num` int(11) NOT NULL DEFAULT '1' COMMENT '申請數量',
  `apply_date` date DEFAULT NULL COMMENT '申請日期',
  `audit_date` date DEFAULT NULL COMMENT '審核日期',
  `remark` text COMMENT '備註',
  `reject_note` text COMMENT '拒絕原因',
  `state` varchar(255) DEFAULT NULL COMMENT '狀態 0：草稿 1：發送  2：拒絕  3：完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='獎金申請表';

-- ----------------------------
-- Table structure for cy_prize_type
-- ----------------------------
DROP TABLE IF EXISTS `cy_prize_type`;
CREATE TABLE `cy_prize_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prize_name` varchar(255) NOT NULL COMMENT '兌換名稱',
  `prize_point` int(11) NOT NULL DEFAULT '0' COMMENT '扣減數值',
  `imges_url` text COMMENT '兌換物品的圖片',
  `z_index` int(11) DEFAULT NULL,
  `prize_remark` text COMMENT '備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='兌換配置表';

-- ----------------------------
-- Table structure for cy_queue
-- ----------------------------
DROP TABLE IF EXISTS `cy_queue`;
CREATE TABLE `cy_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rpt_desc` varchar(250) NOT NULL,
  `req_dt` datetime DEFAULT NULL,
  `fin_dt` datetime DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `status` char(1) NOT NULL,
  `rpt_type` varchar(10) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rpt_content` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cy_queue_param
-- ----------------------------
DROP TABLE IF EXISTS `cy_queue_param`;
CREATE TABLE `cy_queue_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `param_field` varchar(50) NOT NULL,
  `param_value` varchar(500) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cy_queue_user
-- ----------------------------
DROP TABLE IF EXISTS `cy_queue_user`;
CREATE TABLE `cy_queue_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
