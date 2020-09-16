/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-09-16 14:12:23
*/
-- ----------------------------
-- Table structure for cy_credit_type
-- ----------------------------
ALTER TABLE cy_credit_type ADD COLUMN review_str varchar(255) NULL COMMENT '每次考核時間次數'  AFTER rule;
