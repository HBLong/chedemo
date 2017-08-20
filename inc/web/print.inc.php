<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];

$act = trim($_GPC['act']);
$allow_acts = array(
    'index', 'print_upload', 'do_edit_print_template', 'recovery_default_template', 'print_del', 'print'
);

$lables = array(
    "date" => "乘车日期",
    "time" => "发车时间",
    "license_plate" => "车牌号",
    "seat_number" => "座位号",
    "price" => "车票价钱",
    "start_station_name" => "始发站",
    "end_station_name" => "终点站",
    "order_sn" => "订单号",
    "name" => "客户姓名",
    "phone" => "客户电话",
    "idcard" => "客户身份证",
);
if (!in_array($act, $allow_acts)) {
    $act = 'index';
}
load()->func("tpl");

if ($act == 'index') {
    $print_bg = toimage($this->module['config']['print_bg']);
    $config_lable = $this->module['config']['config_lable'];
    $config_lables = explode("||,||", $config_lable);
    
    $lable_index = array();
    foreach ($lables as $key => $value) {
        foreach ($config_lables as $label) {
            $label = explode(",", $label);
            $index = str_replace("t_" . $key, "", $label[0]);
            if (intval($index) > 0) {
                $lable_index["t_" . $key] = intval($index);
            }
        }
    }
}

elseif ($act == 'print_upload') {
    $this->module['config']['print_bg'] = toimage($_GPC['print_bg']);
    $this->saveSettings($this->module['config']);
    message("上传成功", __WURL("print"), "success");
}

elseif ($act == 'print_del') {
    $this->module['config']['print_bg'] = "";
    $this->saveSettings($this->module['config']);
    message("上传成功", __WURL("print"), "success");
}

elseif ($act == 'do_edit_print_template') {
    $this->module['config']['config_lable'] = $_GPC['config_lable'];
    $this->saveSettings($this->module['config']);
    message("设置成功", __WURL("print"), "success");
}

elseif ($act == 'recovery_default_template') {
    $this->module['config']['print_bg'] = "";
    $this->module['config']['config_lable'] = "";
    $this->saveSettings($this->module['config']);
    message("设置成功", __WURL("print"), "success");
}
include $this->template("web/print_" . $act);