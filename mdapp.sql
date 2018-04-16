/*
Navicat MySQL Data Transfer

Source Server         : 本机
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : mdapp

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-04-16 19:51:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for md_dict
-- ----------------------------
DROP TABLE IF EXISTS `md_dict`;
CREATE TABLE `md_dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '字典名称',
  `code` int(11) DEFAULT NULL COMMENT '字典编码',
  `level` tinyint(3) DEFAULT '1' COMMENT '层级-1是基层',
  `is_open` tinyint(2) DEFAULT '1' COMMENT '是否开启-1前端可以显示2不显示',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间 null未删除',
  PRIMARY KEY (`id`),
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of md_dict
-- ----------------------------
INSERT INTO `md_dict` VALUES ('1', '用户身份', '1001', '1', '1', '2018-03-26 18:57:12', null);
INSERT INTO `md_dict` VALUES ('2', '平台超级管理员', '1001001', '2', '1', '2018-03-26 18:57:12', null);
INSERT INTO `md_dict` VALUES ('3', '加盟商', '1001002', '2', '1', '2018-03-26 18:57:12', null);
INSERT INTO `md_dict` VALUES ('4', '系统维护员', '1001003', '2', '1', '2018-03-26 18:57:12', null);

-- ----------------------------
-- Table structure for md_log
-- ----------------------------
DROP TABLE IF EXISTS `md_log`;
CREATE TABLE `md_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(11) unsigned DEFAULT NULL COMMENT '操作码-对应dict表',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '操作说明',
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '访问链接-对应菜单表url',
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '请求客户端IP',
  `table` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '修改表格',
  `data_id` int(11) DEFAULT NULL COMMENT '数据ID',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '新增时间',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '最后更改时间',
  `created_user` int(11) DEFAULT NULL COMMENT '添加人',
  `old` text COLLATE utf8_unicode_ci COMMENT '旧字段',
  `new` text COLLATE utf8_unicode_ci COMMENT '新字段',
  PRIMARY KEY (`id`),
  KEY `操作人` (`created_user`) USING BTREE,
  KEY `操作码以及操作的数据表` (`code`,`table`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of md_log
-- ----------------------------
INSERT INTO `md_log` VALUES ('51', '1002001', '删除菜单', 'admin/Core/deleteMenu/id/19', '0.0.0.0', 'Menus', '19', '2018-03-27 20:09:36', null, null, '1', null, null);
INSERT INTO `md_log` VALUES ('52', '18', '新增菜单', 'admin/core/savemenu', '0.0.0.0', 'menus', '20', '2018-03-27 20:18:11', null, null, '1', null, null);
INSERT INTO `md_log` VALUES ('53', '1002004', '新增角色', 'admin/core/saverole', '0.0.0.0', 'roles', '4', '2018-03-29 21:37:15', null, null, '1', null, null);
INSERT INTO `md_log` VALUES ('54', '1002004', '删除角色', 'admin/Core/deleteRole/id/4', '0.0.0.0', 'Roles', '4', '2018-03-29 21:42:57', null, null, '1', null, null);
INSERT INTO `md_log` VALUES ('55', '1002004', '新增角色', 'admin/core/saverole', '0.0.0.0', 'roles', '5', '2018-03-29 21:43:59', null, null, '1', null, null);
INSERT INTO `md_log` VALUES ('56', '18', '编辑菜单', 'admin/core/savemenu', '0.0.0.0', 'menus', '7', '2018-04-07 16:37:52', null, null, '1', 'a:1:{s:12:\"菜单名称\";s:6:\"其他\";}', 'a:1:{s:12:\"菜单名称\";s:16:\"其他-master-up\";}');
INSERT INTO `md_log` VALUES ('57', '18', '编辑菜单', 'admin/core/savemenu', '0.0.0.0', 'menus', '7', '2018-04-07 16:39:35', null, null, '1', 'a:1:{s:12:\"菜单名称\";s:6:\"其他\";}', 'a:1:{s:12:\"菜单名称\";s:16:\"其他-master-up\";}');
INSERT INTO `md_log` VALUES ('58', '18', '编辑菜单', 'admin/core/savemenu', '0.0.0.0', 'menus', '6', '2018-04-07 16:41:32', null, null, '1', 'a:1:{s:12:\"菜单名称\";s:6:\"图标\";}', 'a:1:{s:12:\"菜单名称\";s:13:\"图标-master\";}');

-- ----------------------------
-- Table structure for md_menus
-- ----------------------------
DROP TABLE IF EXISTS `md_menus`;
CREATE TABLE `md_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0' COMMENT '父级菜单-对应本表ID',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '菜单名称',
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '菜单地址',
  `flag` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '菜单图标',
  `type` int(11) DEFAULT '1001001' COMMENT '对应角色-对应系统参数表-1001 ',
  `is_menu` tinyint(2) DEFAULT '0' COMMENT '是否为左侧菜单链接-0按钮事件 1菜单链接,会显示在左侧列表',
  `is_open` tinyint(2) DEFAULT '1' COMMENT '是否开通-1开通 2禁用  考虑到后期客户套餐的维护',
  `sort` int(11) DEFAULT '0' COMMENT '菜单排序',
  `level` tinyint(3) unsigned DEFAULT '0' COMMENT '菜单级别',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '新增时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '是否删除-如果是删除 此时间就是删除时间',
  `created_user` int(11) DEFAULT '0' COMMENT '添加人-对应user表ID ',
  PRIMARY KEY (`id`),
  KEY `用户索引` (`created_user`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of md_menus
-- ----------------------------
INSERT INTO `md_menus` VALUES ('1', '0', '系统首页', 'admin/Index/welcome', 'fa fa-ambulance', '1001001', '1', '1', '76', '0', '2018-03-27 19:48:07', null, null, '0');
INSERT INTO `md_menus` VALUES ('2', '0', '元素', '22222', 'icon-th ', '1001001', '1', '1', null, '0', '2018-03-26 21:21:03', '2018-03-26 21:21:03', '2018-03-26 21:21:03', '0');
INSERT INTO `md_menus` VALUES ('3', '0', '列表', 'user/Login/login', 'fa fa-calculator', '1001001', '1', '1', '10', '0', '2018-03-26 21:19:08', null, null, '0');
INSERT INTO `md_menus` VALUES ('5', '0', '表单', '44444', 'fa fa-edit', '1001001', '1', '1', '4', '0', '2018-03-26 21:19:52', null, null, '0');
INSERT INTO `md_menus` VALUES ('6', '0', '图标-master', '', 'fa fa-cogs', '1001001', '1', '1', '0', '0', '2018-04-07 16:41:32', '2018-04-07 16:41:32', null, '0');
INSERT INTO `md_menus` VALUES ('7', '0', '其他-master-up', '', 'fa fa-comment-o', '1001001', '1', '1', '0', '0', '2018-04-07 16:39:35', '2018-04-07 16:39:35', null, '0');
INSERT INTO `md_menus` VALUES ('8', '7', '登录', '', '', '1001001', '1', '1', '0', '1', '2018-03-25 12:58:35', null, null, '0');
INSERT INTO `md_menus` VALUES ('9', '7', '退出', '', '', '1001001', '1', '1', '0', '1', '2018-03-25 12:58:35', '2018-03-21 22:38:08', null, '0');
INSERT INTO `md_menus` VALUES ('10', '9', '三级目录', 'dsads', null, '1001001', '0', '1', '20', '3', '2018-03-25 20:48:01', '2018-03-25 20:48:01', '2018-03-25 20:48:01', null);
INSERT INTO `md_menus` VALUES ('11', '0', '系统设置', '', 'fa fa-windows', '1001001', '1', '1', '51', '0', '2018-03-26 21:42:13', null, null, '0');
INSERT INTO `md_menus` VALUES ('12', '11', '菜单列表', 'admin/core/menus', 'fa fa-bars', '1001001', '1', '1', '55', '1', '2018-03-27 19:49:53', '2018-03-21 22:24:46', null, '0');
INSERT INTO `md_menus` VALUES ('15', '12', '新增菜单', 'admin/core/storesenu', '', '1001001', '0', '1', '30', '2', '2018-03-27 19:35:02', null, null, '0');
INSERT INTO `md_menus` VALUES ('18', '12', '编辑菜单', 'admin/core/savemenu', '', '1001001', '0', '1', '30', '2', '2018-03-27 19:35:24', null, null, '0');
INSERT INTO `md_menus` VALUES ('19', '11', '测试2', '/paltform/Syscore/addSettle', '', '1001001', '1', '1', '9', '1', '2018-03-27 20:09:36', '2018-03-27 20:09:36', '2018-03-27 20:09:36', '0');
INSERT INTO `md_menus` VALUES ('20', '11', '用户组', 'admin/core/role', 'fa fa-group', '1001001', '1', '1', '25', '1', null, null, null, '0');

-- ----------------------------
-- Table structure for md_roles
-- ----------------------------
DROP TABLE IF EXISTS `md_roles`;
CREATE TABLE `md_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `franchisee` int(11) unsigned DEFAULT '0' COMMENT '加盟商-0平台运营 其他对应user表franchisee_id',
  `pid` int(11) DEFAULT '0' COMMENT '所属组-0平台角色 其他为加盟商下角色',
  `top` int(11) DEFAULT '0' COMMENT '最上级角色',
  `level` int(11) DEFAULT '1' COMMENT '角色层级-最高级为1依次递减',
  `name` varchar(50) DEFAULT '' COMMENT '角色名称',
  `menu_ids` varchar(255) DEFAULT '' COMMENT '角色权限',
  `sort` int(11) DEFAULT '0' COMMENT '排序-越大越靠前',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '最后更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `created_user` int(11) DEFAULT NULL COMMENT '添加人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of md_roles
-- ----------------------------
INSERT INTO `md_roles` VALUES ('1', '0', '0', '0', '1', '超级管理员', '', '0', '2018-03-27 20:52:49', null, null, '1');
INSERT INTO `md_roles` VALUES ('2', '0', '0', '0', '1', '系统维护员', '', '0', null, null, null, null);
INSERT INTO `md_roles` VALUES ('3', '0', '2', '0', '2', '系统测试员', '', '0', null, null, null, null);
INSERT INTO `md_roles` VALUES ('5', '0', '3', '2', '3', '测试A组', '7,8,9', '9', null, null, null, null);

-- ----------------------------
-- Table structure for md_user
-- ----------------------------
DROP TABLE IF EXISTS `md_user`;
CREATE TABLE `md_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `franchisee_id` int(11) DEFAULT NULL COMMENT '加盟商ID-对应franchisee表',
  `roles_id` int(11) DEFAULT NULL COMMENT '所属角色',
  `account_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '账号',
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '密码-md5',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '头像',
  `nick_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '昵称',
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手机号',
  `type` int(11) DEFAULT '1001002' COMMENT '用户身份-对应字典表1001',
  `real_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '真实姓名',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '最后更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `created_user` int(11) DEFAULT NULL COMMENT '添加人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of md_user
-- ----------------------------
INSERT INTO `md_user` VALUES ('1', null, '1', 'whlphper', '202cb962ac59075b964b07152d234b70', '', 'whlphper', null, '1001001', '王红亮', '2018-03-20 22:18:59', null, null, null);
INSERT INTO `md_user` VALUES ('2', null, '2', 'i from master', '', null, '', null, '1001002', null, null, null, null, null);
INSERT INTO `md_user` VALUES ('3', null, '2', 'i from master2', '', null, '', null, '1001002', null, null, null, null, null);
