/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-12-28 14:12:23
*/
-- ----------------------------
-- Table structure for cy_credit_request
-- ----------------------------
ALTER TABLE cy_credit_request ADD COLUMN type_state  int(1) NOT NULL DEFAULT 2 COMMENT '1:專員 2：結束'  AFTER images_url;
ALTER TABLE cy_credit_request ADD COLUMN one_audit  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  AFTER images_url;
ALTER TABLE cy_credit_request ADD COLUMN one_date  datetime NULL DEFAULT NULL  AFTER images_url;
ALTER TABLE cy_credit_request ADD COLUMN two_audit  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER images_url;
ALTER TABLE cy_credit_request ADD COLUMN two_date  datetime NULL DEFAULT NULL  AFTER images_url;
