<?php
 
defined('IN_IA') or exit('Access Denied');
define('__DAT__', MODULE_ROOT . '/resource/dat');
define('__CSS__', MODULE_URL . 'resource/css');
define('__IMG__', MODULE_URL . 'resource/images');
define('__JS__', MODULE_URL . 'resource/js');
include_once 'function/functions.php';

$footer_off = TRUE;
$fr_moudle_name = 'fr_cp_';
global $_GPC, $_W, $fr_cp_settings;

$fr_web_page_title = array(
    'area' => '地区',
    'station' => '车站',
    'route' => '线路',
    'shift' => '班次',
    'order' => '订单',
    'user' => '用户',
    'vehicle' => '车辆',
    'notice' => '通知',
    'message' => '通知内容',
    'writeoff' => '核销',
    'shifttime' => "发车时间设置",
    'shift_vehicle' => "班次司机安排",
);
$replace_content = array(
    "{LUXIAN}" => "路线",
    "{SHIJIAN}" => "发车时间",
    "{PIAO}" => "购票数",
    "{SEAT_NUMBER}" => "座位号",
    "{ORDER_SN}" => "订单号",
    "{CHEPAI}" => "车牌号",
    "{NAME}" => "购票人姓名",
    "{PHONE}" => "购票人手机号码",
    "{IDCARD}" => "购票人身份证",
    "{STATUS}" => "订单状态",
    "{CREATETIME}" => "下单时间",
);
/*
 * 添加默认设置
 */
if (empty($this->module['config'])) {
    $dat = array(
        "templateid" => '',
        "content" => '',
        "content2" => '',
        "vehicle_type" => "1:大型坐席高级\n2:大型坐席高一级\n3:大型普通客车",
        "day" => 20,
        "recommend_size" => 5,
        "auto_cancel_order" => 0,
        "sms_type" => 0,
        "sms_buy_order" => 0,
        "print_type" => 0,
        "sms_mobile" => '',
        "yupiao" => '',
        "writeoff" => 'fr_cp_writeoff_',
    );
    $this->saveSettings($dat);
}
$fr_cp_settings = $this->module['config'];
$fr_cp_settings['vehicle_type'] = format_setting_type($fr_cp_settings['vehicle_type']);
$fr_cp_settings['sms_mobile'] = is_string($fr_cp_settings['sms_mobile']) ? explode("\r\n", $fr_cp_settings['sms_mobile']) : array();
$fr_cp_settings['yupiao'] = is_string($fr_cp_settings['yupiao']) ? explode("\r\n", $fr_cp_settings['yupiao']) : array();
if ($fr_cp_settings['auto_cancel_order'] > 0) {
    auto_cancel_order($fr_cp_settings['auto_cancel_order']);
}
//$order_id = 169;
//$update_order_data = array(
//    'status' => 1,
//    'is_paid' => 1,
//    'pay_time' => TIMESTAMP
//);