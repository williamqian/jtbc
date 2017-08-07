SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jtbc_aboutus
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_aboutus`;
CREATE TABLE `jtbc_aboutus` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_topic` varchar(200) DEFAULT NULL,
  `a_content` text,
  `a_att` text,
  `a_publish` int(11) DEFAULT '0',
  `a_time` datetime DEFAULT NULL,
  `a_lang` int(11) DEFAULT '0',
  `a_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`a_id`),
  KEY `a_delete` (`a_delete`,`a_lang`,`a_publish`) USING BTREE,
  KEY `a_time` (`a_time`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_aboutus
-- ----------------------------
INSERT INTO `jtbc_aboutus` VALUES ('1', '公司介绍', null, null, '1', '2017-07-11 20:15:30', '0', '0');

-- ----------------------------
-- Table structure for jtbc_career
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_career`;
CREATE TABLE `jtbc_career` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_topic` varchar(200) DEFAULT NULL,
  `c_intro` varchar(200) DEFAULT NULL,
  `c_email` varchar(200) DEFAULT NULL,
  `c_publish` int(11) DEFAULT '0',
  `c_time` datetime DEFAULT NULL,
  `c_lang` int(11) DEFAULT '0',
  `c_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_delete` (`c_delete`,`c_lang`,`c_publish`) USING BTREE,
  KEY `c_time` (`c_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_career
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_case
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_case`;
CREATE TABLE `jtbc_case` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_topic` varchar(200) DEFAULT NULL,
  `c_image` varchar(200) DEFAULT NULL,
  `c_category` int(11) DEFAULT '0',
  `c_content` text,
  `c_att` text,
  `c_publish` int(11) DEFAULT '0',
  `c_time` datetime DEFAULT NULL,
  `c_lang` int(11) DEFAULT '0',
  `c_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_delete` (`c_delete`,`c_lang`,`c_publish`) USING BTREE,
  KEY `c_time` (`c_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_case
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_console_account
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_console_account`;
CREATE TABLE `jtbc_console_account` (
  `ca_id` int(11) NOT NULL AUTO_INCREMENT,
  `ca_username` varchar(50) DEFAULT NULL,
  `ca_password` varchar(50) DEFAULT NULL,
  `ca_email` varchar(50) DEFAULT NULL,
  `ca_role` int(11) DEFAULT '0',
  `ca_lock` int(11) DEFAULT '0',
  `ca_lastip` varchar(50) DEFAULT NULL,
  `ca_lasttime` datetime DEFAULT NULL,
  `ca_time` datetime DEFAULT NULL,
  `ca_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`ca_id`),
  KEY `ca_username` (`ca_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_console_account
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_console_account_login
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_console_account_login`;
CREATE TABLE `jtbc_console_account_login` (
  `cal_id` int(11) NOT NULL AUTO_INCREMENT,
  `cal_account_id` int(11) DEFAULT '0',
  `cal_date` int(11) DEFAULT '0',
  `cal_status` int(11) DEFAULT '0',
  PRIMARY KEY (`cal_id`),
  KEY `cal_account_id` (`cal_account_id`,`cal_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_console_account_login
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_console_log
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_console_log`;
CREATE TABLE `jtbc_console_log` (
  `cl_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_genre` varchar(200) DEFAULT NULL,
  `cl_content` varchar(200) DEFAULT NULL,
  `cl_userip` varchar(200) DEFAULT NULL,
  `cl_account_id` int(200) DEFAULT '0',
  `cl_delete` int(11) DEFAULT '0',
  `cl_time` datetime DEFAULT NULL,
  PRIMARY KEY (`cl_id`),
  KEY `cl_account_id` (`cl_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_console_log
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_console_role
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_console_role`;
CREATE TABLE `jtbc_console_role` (
  `cr_id` int(11) NOT NULL AUTO_INCREMENT,
  `cr_topic` varchar(200) DEFAULT NULL,
  `cr_popedom` varchar(10000) DEFAULT NULL,
  `cr_lang` varchar(200) DEFAULT NULL,
  `cr_delete` int(11) DEFAULT '0',
  `cr_time` datetime DEFAULT NULL,
  PRIMARY KEY (`cr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_console_role
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_consult
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_consult`;
CREATE TABLE `jtbc_consult` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_name` varchar(200) DEFAULT NULL,
  `c_mobile` varchar(200) DEFAULT NULL,
  `c_email` varchar(200) DEFAULT NULL,
  `c_content` varchar(1000) DEFAULT NULL,
  `c_userip` varchar(200) DEFAULT NULL,
  `c_dispose` int(11) DEFAULT '0',
  `c_time` datetime DEFAULT NULL,
  `c_lang` int(11) DEFAULT '0',
  `c_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_delete` (`c_delete`,`c_lang`,`c_dispose`) USING BTREE,
  KEY `c_time` (`c_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_consult
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_news
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_news`;
CREATE TABLE `jtbc_news` (
  `n_id` int(11) NOT NULL AUTO_INCREMENT,
  `n_topic` varchar(200) DEFAULT NULL,
  `n_category` int(11) DEFAULT '0',
  `n_content` text,
  `n_att` text,
  `n_publish` int(11) DEFAULT '0',
  `n_time` datetime DEFAULT NULL,
  `n_lang` int(11) DEFAULT '0',
  `n_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`n_id`),
  KEY `n_delete` (`n_delete`,`n_lang`,`n_publish`) USING BTREE,
  KEY `n_time` (`n_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_news
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_team
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_team`;
CREATE TABLE `jtbc_team` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_topic` varchar(200) DEFAULT NULL,
  `t_photo` varchar(200) DEFAULT NULL,
  `t_position` varchar(200) DEFAULT NULL,
  `t_intro` varchar(200) DEFAULT NULL,
  `t_publish` int(11) DEFAULT '0',
  `t_time` datetime DEFAULT NULL,
  `t_lang` int(11) DEFAULT '0',
  `t_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`t_id`),
  KEY `t_delete` (`t_delete`,`t_lang`,`t_publish`) USING BTREE,
  KEY `t_time` (`t_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_team
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_universal_category
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_universal_category`;
CREATE TABLE `jtbc_universal_category` (
  `uc_id` int(11) NOT NULL AUTO_INCREMENT,
  `uc_topic` varchar(50) DEFAULT NULL,
  `uc_image` varchar(200) DEFAULT NULL,
  `uc_intro` varchar(1000) DEFAULT NULL,
  `uc_fid` int(11) DEFAULT '0',
  `uc_order` int(11) DEFAULT '0',
  `uc_time` datetime DEFAULT NULL,
  `uc_genre` varchar(200) DEFAULT NULL,
  `uc_lang` int(11) DEFAULT '0',
  `uc_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`uc_id`),
  KEY `uc_genre` (`uc_genre`,`uc_delete`,`uc_lang`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_universal_category
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_universal_link
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_universal_link`;
CREATE TABLE `jtbc_universal_link` (
  `ul_id` int(11) NOT NULL AUTO_INCREMENT,
  `ul_topic` varchar(50) DEFAULT NULL,
  `ul_image` varchar(200) DEFAULT NULL,
  `ul_url` varchar(200) DEFAULT NULL,
  `ul_target` varchar(50) DEFAULT NULL,
  `ul_group` int(11) DEFAULT '0',
  `ul_publish` int(11) DEFAULT '0',
  `ul_time` datetime DEFAULT NULL,
  `ul_lang` int(11) DEFAULT '0',
  `ul_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`ul_id`),
  KEY `ul_publish` (`ul_publish`,`ul_lang`,`ul_delete`),
  KEY `ul_group` (`ul_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_universal_link
-- ----------------------------

-- ----------------------------
-- Table structure for jtbc_universal_material
-- ----------------------------
DROP TABLE IF EXISTS `jtbc_universal_material`;
CREATE TABLE `jtbc_universal_material` (
  `um_id` int(11) NOT NULL AUTO_INCREMENT,
  `um_topic` varchar(200) DEFAULT NULL,
  `um_filepath` varchar(200) DEFAULT NULL,
  `um_fileurl` varchar(200) DEFAULT NULL,
  `um_filetype` varchar(20) DEFAULT NULL,
  `um_filesize` int(11) DEFAULT '0',
  `um_filegroup` int(11) DEFAULT '0',
  `um_time` datetime DEFAULT NULL,
  `um_hot` int(11) DEFAULT '0',
  `um_lang` int(11) DEFAULT '0',
  `um_delete` int(11) DEFAULT '0',
  PRIMARY KEY (`um_id`),
  KEY `um_filetype` (`um_filetype`),
  KEY `um_hot` (`um_hot`),
  KEY `um_lang` (`um_lang`,`um_delete`),
  KEY `um_time` (`um_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jtbc_universal_material
-- ----------------------------