/*
Navicat MySQL Data Transfer

Source Server         : MySQL - Internal
Source Server Version : 50622
Source Host           : 10.69.69.11:3306
Source Database       : pringles

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2017-10-01 09:11:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cheats
-- ----------------------------
DROP TABLE IF EXISTS `cheats`;
CREATE TABLE `cheats` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`plan_id`  int(11) UNSIGNED NOT NULL ,
`plan_name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`plan_game`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`),
INDEX `plan_name` (`plan_name`) USING BTREE ,
INDEX `plan_game` (`plan_name`) USING BTREE ,
INDEX `plan_id` (`plan_id`) USING BTREE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
AUTO_INCREMENT=5
ROW_FORMAT=Compact

;

-- ----------------------------
-- Records of cheats
-- ----------------------------
BEGIN;
INSERT INTO `cheats` VALUES ('1', '3', '[PUBG] Valhalla LVL 1', 'PUBG');
INSERT INTO `cheats` VALUES ('2', '11', '[PUBG] Valhalla LVL 2', 'PUBG');
INSERT INTO `cheats` VALUES ('3', '28', '[H1Z1] DEVELOPER LVL 2', 'H1Z1');
INSERT INTO `cheats` VALUES ('4', '1', '[CS:GO] Fossil dll - Example', 'CSGO');
COMMIT;

-- ----------------------------
-- Table structure for plans
-- ----------------------------
DROP TABLE IF EXISTS `plans`;
CREATE TABLE `plans` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) UNSIGNED NOT NULL ,
`plan_id`  int(11) UNSIGNED NOT NULL ,
`expire`  int(11) UNSIGNED NOT NULL DEFAULT 1 ,
PRIMARY KEY (`id`),
FOREIGN KEY (`plan_id`) REFERENCES `cheats` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
INDEX `plan_id` (`plan_id`) USING BTREE ,
INDEX `plan_user` (`user_id`) USING BTREE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
AUTO_INCREMENT=8
ROW_FORMAT=Compact

;

-- ----------------------------
-- Records of plans
-- ----------------------------
BEGIN;
INSERT INTO `plans` VALUES ('1', '1', '11', '2147483647');
INSERT INTO `plans` VALUES ('2', '1', '3', '2147483647');
INSERT INTO `plans` VALUES ('4', '1', '28', '2147483647');
INSERT INTO `plans` VALUES ('5', '2', '11', '2147483647');
INSERT INTO `plans` VALUES ('6', '3', '11', '2147483647');
INSERT INTO `plans` VALUES ('7', '4', '11', '2147483647');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`username`  varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`password`  varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`admin`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`status`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`hwid`  binary(32) NULL DEFAULT NULL ,
`config`  blob NULL ,
`lastlogin`  varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL ,
`lastip`  varbinary(4) NULL DEFAULT NULL ,
`failedip`  longblob NULL ,
`failedconfig`  longblob NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `username` (`username`) USING BTREE ,
INDEX `hwid` (`hwid`) USING BTREE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
AUTO_INCREMENT=5
ROW_FORMAT=Compact

;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'Smug', '', '1', '1', 0x0000000000000000000000000000000000000000000000000000000000000000, null, null, null, null, null);
INSERT INTO `users` VALUES ('2', 'Jirx', '', '0', '1', 0x0000000000000000000000000000000000000000000000000000000000000000, null, null, null, null, null);
INSERT INTO `users` VALUES ('3', 'Sub', '', '0', '1', 0x0000000000000000000000000000000000000000000000000000000000000000, null, null, null, null, null);
INSERT INTO `users` VALUES ('4', 'Jogn', '', '0', '1', 0x0000000000000000000000000000000000000000000000000000000000000000, null, null, null, null, null);
COMMIT;

-- ----------------------------
-- Auto increment value for cheats
-- ----------------------------
ALTER TABLE `cheats` AUTO_INCREMENT=5;

-- ----------------------------
-- Auto increment value for plans
-- ----------------------------
ALTER TABLE `plans` AUTO_INCREMENT=8;

-- ----------------------------
-- Auto increment value for users
-- ----------------------------
ALTER TABLE `users` AUTO_INCREMENT=5;
SET FOREIGN_KEY_CHECKS=1;
