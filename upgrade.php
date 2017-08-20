<?php
$sql="CREATE TABLE IF NOT EXISTS `ims_fr_cp_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `createtime` int(11) DEFAULT '0',
  `action_info` text,
  `ip` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT '0',
  `route_id` int(10) unsigned DEFAULT '0',
  `title` varchar(200) DEFAULT NULL,
  `price` float(10,2) DEFAULT NULL,
  `sort` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `pid` int(10) unsigned DEFAULT '0',
  `sort` int(10) unsigned DEFAULT '0',
  `lv` int(10) unsigned DEFAULT '1',
  `pinyin` varchar(100) DEFAULT '',
  `py` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='地区';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '0：退票；1：改签',
  `time` int(10) unsigned DEFAULT NULL,
  `fee` float(10,2) DEFAULT '0.00',
  `fee_type` tinyint(1) unsigned DEFAULT '0' COMMENT '0百分比；1固定金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned DEFAULT '0',
  `start_station_id` int(11) unsigned DEFAULT '0',
  `end_station_id` int(11) unsigned DEFAULT '0',
  `uid` int(11) unsigned DEFAULT '0',
  `createtime` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `template_id` varchar(200) DEFAULT NULL,
  `sms_content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `type` tinyint(1) DEFAULT '0' COMMENT '0:微信通知；1：短信通知',
  `openid` varchar(200) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `msg_id` int(11) DEFAULT NULL,
  `route_id` int(10) unsigned DEFAULT '0' COMMENT '0：所有路线都通知；其他：指定路线通知',
  `is_add` tinyint(1) unsigned DEFAULT '0' COMMENT '下单时通知',
  `is_pay` tinyint(1) unsigned DEFAULT '0' COMMENT '付款是通知',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `start_station_id` int(10) unsigned DEFAULT NULL,
  `end_station_id` int(10) unsigned DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `idcard` varchar(100) DEFAULT NULL,
  `order_sn` varchar(100) DEFAULT NULL,
  `is_paid` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  `seat_number` int(10) unsigned DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `price` float(10,2) DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL,
  `number` int(10) unsigned DEFAULT '1' COMMENT '票数量',
  `voucher_id` int(10) unsigned DEFAULT '0',
  `isprint` tinyint(1) unsigned DEFAULT '0',
  `openid` varchar(100) DEFAULT NULL,
  `addons_id` varchar(200) DEFAULT NULL,
  `addons` text,
  `froms` char(20) DEFAULT 'mobile',
  `check_ticket` tinyint(1) unsigned DEFAULT '0',
  `check_ticket_time` int(10) unsigned DEFAULT '0',
  `check_ticket_openid` varchar(100) DEFAULT '',
  `departure_time` int(10) unsigned DEFAULT '0' COMMENT '发车时间',
  `before_departure_time` int(10) DEFAULT '0' COMMENT '改签前的发车时间',
  `remarks` text,
  `update_time` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `is_delete` tinyint(1) DEFAULT '0',
  `refund_fee` float(10,2) DEFAULT '0.00' COMMENT '退票款项',
  `change_cost` float(10,2) DEFAULT '0.00' COMMENT '改签费用',
  `admin_uid` int(10) unsigned DEFAULT '0',
  `vehicle_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订票记录';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `start_station_id` int(10) unsigned DEFAULT NULL,
  `end_station_id` int(10) unsigned DEFAULT NULL,
  `recommend` tinyint(1) DEFAULT '0',
  `insurance` tinyint(1) DEFAULT '0',
  `person_phone` varchar(100) DEFAULT NULL,
  `explain` text COMMENT '其它说明',
  `sms_content` text COMMENT '短信内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='路线';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_seat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT '0',
  `order_id` int(10) unsigned DEFAULT '0',
  `shift_id` int(10) unsigned DEFAULT '0',
  `seat_number` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `route_id` int(11) DEFAULT '0',
  `date` date DEFAULT NULL,
  `time` time DEFAULT '00:00:00',
  `total_votes` int(11) DEFAULT '0',
  `surplus_votes` int(11) DEFAULT '0',
  `ticket_price` float(10,2) DEFAULT NULL,
  `vehicle_id` int(10) DEFAULT '0',
  `method` tinyint(1) unsigned DEFAULT '0' COMMENT '购票方式',
  `enable_vouchers` tinyint(1) unsigned DEFAULT '0' COMMENT '启用代金券',
  `refund_ticket` tinyint(1) unsigned DEFAULT '0' COMMENT '是否可退票',
  `sms_content` text COMMENT '短信内容',
  `status` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='班次';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_shift_end_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned DEFAULT '0',
  `end_station_id` int(11) unsigned DEFAULT '0',
  `shift_id` int(11) unsigned DEFAULT '0',
  `recommend` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_shift_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(10) unsigned DEFAULT '0',
  `station_id` int(10) unsigned DEFAULT '0',
  `departure_time` time DEFAULT '00:00:00',
  `ticket_price` float(10,2) unsigned DEFAULT '0.00',
  `recommend` tinyint(1) unsigned DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='配客表';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_shift_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `time` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_shift_vehicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `shift_id` int(10) unsigned DEFAULT '0' COMMENT '班次ID',
  `vehicle_id` int(10) unsigned DEFAULT '0' COMMENT '车辆ID',
  `work_date` date DEFAULT '0000-00-00' COMMENT '排班日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT '0',
  `mobile` varchar(200) DEFAULT '',
  `content` text,
  `createtime` int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `error_msg` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `sort` int(11) DEFAULT '1',
  `province_id` int(10) unsigned DEFAULT '0',
  `city_id` int(10) unsigned DEFAULT '0',
  `district_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='车站';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(200) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT '0',
  `phone` varchar(20) DEFAULT NULL,
  `idcard` varchar(100) DEFAULT NULL,
  `remarks` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户资料';
CREATE TABLE IF NOT EXISTS `ims_fr_cp_vehicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `license_plate` varchar(255) DEFAULT '',
  `driver_name` varchar(255) DEFAULT '',
  `driver_phone` varchar(255) DEFAULT '',
  `seat_numbers` int(10) unsigned DEFAULT '0',
  `station_id` int(10) unsigned DEFAULT '0',
  `vehicle_type` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_voucher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `discount` int(10) DEFAULT NULL,
  `start_station_id` int(10) unsigned DEFAULT NULL,
  `end_station_id` int(11) DEFAULT NULL,
  `come_from` tinyint(3) unsigned DEFAULT '0' COMMENT '1:关注送；2：首次购买送；0：其它',
  `type` tinyint(1) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `order_id` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fr_cp_writeoff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned DEFAULT NULL,
  `route_id` int(10) unsigned DEFAULT NULL,
  `openid` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql);
if(!pdo_fieldexists('fr_cp_action',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_action')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_action',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_action')." ADD `uniacid` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_action',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_action')." ADD `uid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_action',  'createtime')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_action')." ADD `createtime` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_action',  'action_info')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_action')." ADD `action_info` text;");
}
if(!pdo_fieldexists('fr_cp_action',  'ip')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_action')." ADD `ip` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_addons',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_addons')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_addons',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_addons')." ADD `uniacid` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_addons',  'route_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_addons')." ADD `route_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_addons',  'title')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_addons')." ADD `title` varchar(200) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_addons',  'price')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_addons')." ADD `price` float(10,2) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_addons',  'sort')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_addons')." ADD `sort` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_area',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_area',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_area',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `name` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_area',  'pid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `pid` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_area',  'sort')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `sort` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_area',  'lv')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `lv` int(10) unsigned DEFAULT '1';");
}
if(!pdo_fieldexists('fr_cp_area',  'pinyin')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `pinyin` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_area',  'py')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_area')." ADD `py` varchar(20) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_fee',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_fee')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_fee',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_fee')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_fee',  'type')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_fee')." ADD `type` tinyint(1) unsigned DEFAULT '0' COMMENT '0：退票；1：改签';");
}
if(!pdo_fieldexists('fr_cp_fee',  'time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_fee')." ADD `time` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_fee',  'fee')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_fee')." ADD `fee` float(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('fr_cp_fee',  'fee_type')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_fee')." ADD `fee_type` tinyint(1) unsigned DEFAULT '0' COMMENT '0百分比；1固定金额';");
}
if(!pdo_fieldexists('fr_cp_history',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_history')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_history',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_history')." ADD `uniacid` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_history',  'start_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_history')." ADD `start_station_id` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_history',  'end_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_history')." ADD `end_station_id` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_history',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_history')." ADD `uid` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_history',  'createtime')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_history')." ADD `createtime` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_message',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_message')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_message',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_message')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_message',  'title')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_message')." ADD `title` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_message',  'template_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_message')." ADD `template_id` varchar(200) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_message',  'sms_content')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_message')." ADD `sms_content` text;");
}
if(!pdo_fieldexists('fr_cp_notice',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_notice',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_notice',  'type')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `type` tinyint(1) DEFAULT '0' COMMENT '0:微信通知；1：短信通知';");
}
if(!pdo_fieldexists('fr_cp_notice',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `openid` varchar(200) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_notice',  'mobile')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `mobile` varchar(20) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_notice',  'msg_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `msg_id` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_notice',  'route_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `route_id` int(10) unsigned DEFAULT '0' COMMENT '0：所有路线都通知；其他：指定路线通知';");
}
if(!pdo_fieldexists('fr_cp_notice',  'is_add')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `is_add` tinyint(1) unsigned DEFAULT '0' COMMENT '下单时通知';");
}
if(!pdo_fieldexists('fr_cp_notice',  'is_pay')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_notice')." ADD `is_pay` tinyint(1) unsigned DEFAULT '0' COMMENT '付款是通知';");
}
if(!pdo_fieldexists('fr_cp_order',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_order',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'shift_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `shift_id` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'start_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `start_station_id` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'end_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `end_station_id` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `uid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `name` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'phone')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `phone` varchar(20) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'idcard')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `idcard` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'order_sn')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `order_sn` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'is_paid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `is_paid` tinyint(4) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `status` tinyint(4) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'seat_number')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `seat_number` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'createtime')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `createtime` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'price')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `price` float(10,2) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'pay_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `pay_time` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'number')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `number` int(10) unsigned DEFAULT '1' COMMENT '票数量';");
}
if(!pdo_fieldexists('fr_cp_order',  'voucher_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `voucher_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'isprint')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `isprint` tinyint(1) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `openid` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'addons_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `addons_id` varchar(200) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'addons')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `addons` text;");
}
if(!pdo_fieldexists('fr_cp_order',  'froms')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `froms` char(20) DEFAULT 'mobile';");
}
if(!pdo_fieldexists('fr_cp_order',  'check_ticket')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `check_ticket` tinyint(1) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'check_ticket_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `check_ticket_time` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'check_ticket_openid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `check_ticket_openid` varchar(100) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_order',  'departure_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `departure_time` int(10) unsigned DEFAULT '0' COMMENT '发车时间';");
}
if(!pdo_fieldexists('fr_cp_order',  'before_departure_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `before_departure_time` int(10) DEFAULT '0' COMMENT '改签前的发车时间';");
}
if(!pdo_fieldexists('fr_cp_order',  'remarks')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `remarks` text;");
}
if(!pdo_fieldexists('fr_cp_order',  'update_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `update_time` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_order',  'parent_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `parent_id` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'is_delete')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `is_delete` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'refund_fee')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `refund_fee` float(10,2) DEFAULT '0.00' COMMENT '退票款项';");
}
if(!pdo_fieldexists('fr_cp_order',  'change_cost')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `change_cost` float(10,2) DEFAULT '0.00' COMMENT '改签费用';");
}
if(!pdo_fieldexists('fr_cp_order',  'admin_uid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `admin_uid` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_order',  'vehicle_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_order')." ADD `vehicle_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_route',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_route',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_route',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `name` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_route',  'start_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `start_station_id` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_route',  'end_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `end_station_id` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_route',  'recommend')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `recommend` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_route',  'insurance')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `insurance` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_route',  'person_phone')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `person_phone` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_route',  'explain')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `explain` text COMMENT '其它说明';");
}
if(!pdo_fieldexists('fr_cp_route',  'sms_content')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_route')." ADD `sms_content` text COMMENT '短信内容';");
}
if(!pdo_fieldexists('fr_cp_seat',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_seat')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_seat',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_seat')." ADD `uniacid` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_seat',  'order_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_seat')." ADD `order_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_seat',  'shift_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_seat')." ADD `shift_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_seat',  'seat_number')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_seat')." ADD `seat_number` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_shift',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `uniacid` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift',  'route_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `route_id` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift',  'date')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `date` date DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_shift',  'time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `time` time DEFAULT '00:00:00';");
}
if(!pdo_fieldexists('fr_cp_shift',  'total_votes')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `total_votes` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift',  'surplus_votes')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `surplus_votes` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift',  'ticket_price')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `ticket_price` float(10,2) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_shift',  'vehicle_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `vehicle_id` int(10) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift',  'method')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `method` tinyint(1) unsigned DEFAULT '0' COMMENT '购票方式';");
}
if(!pdo_fieldexists('fr_cp_shift',  'enable_vouchers')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `enable_vouchers` tinyint(1) unsigned DEFAULT '0' COMMENT '启用代金券';");
}
if(!pdo_fieldexists('fr_cp_shift',  'refund_ticket')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `refund_ticket` tinyint(1) unsigned DEFAULT '0' COMMENT '是否可退票';");
}
if(!pdo_fieldexists('fr_cp_shift',  'sms_content')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `sms_content` text COMMENT '短信内容';");
}
if(!pdo_fieldexists('fr_cp_shift',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift')." ADD `status` tinyint(1) unsigned DEFAULT '1';");
}
if(!pdo_fieldexists('fr_cp_shift_end_station',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_end_station')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_shift_end_station',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_end_station')." ADD `uniacid` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_end_station',  'end_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_end_station')." ADD `end_station_id` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_end_station',  'shift_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_end_station')." ADD `shift_id` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_end_station',  'recommend')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_end_station')." ADD `recommend` tinyint(1) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'shift_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `shift_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `station_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'departure_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `departure_time` time DEFAULT '00:00:00';");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'ticket_price')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `ticket_price` float(10,2) unsigned DEFAULT '0.00';");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'recommend')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `recommend` tinyint(1) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_shift_station',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_station')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_shift_time',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_time')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_shift_time',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_time')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_shift_time',  'time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_time')." ADD `time` time DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_shift_vehicle',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_vehicle')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_shift_vehicle',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_vehicle')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_shift_vehicle',  'shift_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_vehicle')." ADD `shift_id` int(10) unsigned DEFAULT '0' COMMENT '班次ID';");
}
if(!pdo_fieldexists('fr_cp_shift_vehicle',  'vehicle_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_vehicle')." ADD `vehicle_id` int(10) unsigned DEFAULT '0' COMMENT '车辆ID';");
}
if(!pdo_fieldexists('fr_cp_shift_vehicle',  'work_date')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_shift_vehicle')." ADD `work_date` date DEFAULT '0000-00-00' COMMENT '排班日期';");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `uniacid` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'mobile')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `mobile` varchar(200) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'content')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `content` text;");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'createtime')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `createtime` int(11) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `status` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_sms_log',  'error_msg')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_sms_log')." ADD `error_msg` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_station',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_station',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_station',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `name` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_station',  'sort')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `sort` int(11) DEFAULT '1';");
}
if(!pdo_fieldexists('fr_cp_station',  'province_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `province_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_station',  'city_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `city_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_station',  'district_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_station')." ADD `district_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_user',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_user',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_user',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `openid` varchar(200) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_user',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `uid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_user',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `name` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_user',  'sex')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `sex` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_user',  'phone')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `phone` varchar(20) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_user',  'idcard')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `idcard` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_user',  'remarks')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_user')." ADD `remarks` text;");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'license_plate')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `license_plate` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'driver_name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `driver_name` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'driver_phone')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `driver_phone` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'seat_numbers')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `seat_numbers` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `station_id` int(10) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_vehicle',  'vehicle_type')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_vehicle')." ADD `vehicle_type` tinyint(1) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_voucher',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `uid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'title')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `title` varchar(200) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'discount')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `discount` int(10) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'start_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `start_station_id` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'end_station_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `end_station_id` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'come_from')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `come_from` tinyint(3) unsigned DEFAULT '0' COMMENT '1:关注送；2：首次购买送；0：其它';");
}
if(!pdo_fieldexists('fr_cp_voucher',  'type')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `type` tinyint(1) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'end_time')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `end_time` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'createtime')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `createtime` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_voucher',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `status` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_voucher',  'order_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_voucher')." ADD `order_id` int(11) unsigned DEFAULT '0';");
}
if(!pdo_fieldexists('fr_cp_writeoff',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_writeoff')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fr_cp_writeoff',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_writeoff')." ADD `uniacid` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_writeoff',  'route_id')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_writeoff')." ADD `route_id` int(10) unsigned DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_writeoff',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_writeoff')." ADD `openid` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fr_cp_writeoff',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fr_cp_writeoff')." ADD `name` varchar(100) DEFAULT NULL;");
}

?>