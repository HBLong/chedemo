<?php

 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'order';
$table_name = 'order';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'info', 'delete', 'export', 'get_shift', 'print', 'return', 'changing', 'check_order'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
$area_id = intval($_GPC['area_id']);
$status_text = array('0' => '进行中', '1' => '已完成', '-1' => '已取消', '2' => '已退票', '3' => '改签');
/**
 * 列表
 */
if ($act == 'lists') {
    load()->func("tpl");
    $admin_uids = getAllData("order", " AND admin_uid > 0", "", " DISTINCT admin_uid");
    
    $where = '';
    $status = in_array($_GPC['status'], array('0', '1', '-1', '2', '3')) ? intval($_GPC['status']) : NULL;
    $route_id = intval($_GPC['route_id']) > 0 ? intval( $_GPC['route_id']) : NULL;
    $shift_id = intval($_GPC['shift_id']) > 0 ? intval($_GPC['shift_id']) : NULL;
    $seat_number = intval($_GPC['seat_number']) > 0 ? intval($_GPC['seat_number']) : NULL;
    $admin_uid = intval($_GPC['admin_uid']) > 0 ? intval($_GPC['admin_uid']) : NULL;
    $check_ticket = intval($_GPC['check_ticket']) > 0 ? 1 : NULL;
    $date = intval($_GPC['date']) > 0 ? $_GPC['date'] : "";//getDateStr();
    $order_sn = $_GPC['order_sn'] != '' ? trim($_GPC['order_sn']) : '';
    $froms = $_GPC['froms'] != '' ? trim($_GPC['froms']) : '';
    $name = $_GPC['name'] != '' ? trim($_GPC['name']) : '';
    $idcard = $_GPC['idcard'] != '' ? trim($_GPC['idcard']) : '';
    $phone = $_GPC['phone'] != '' ? trim($_GPC['phone']) : '';
    if ($froms != '') {
        $where .= " AND froms = '{$froms}'";
    }
    if (!is_null($admin_uid)) {
        $where .= " AND admin_uid = '{$admin_uid}'";
    }
    if (!is_null($status)) {
        $where .= " AND status = '{$status}'";
    }
    if (!is_null($seat_number)) {
        $where .= " AND seat_number = '{$seat_number}'";
    }
    if ($check_ticket == 1) {
        $where .= " AND check_ticket = '1'";
    }
    if ($order_sn != '') {
        $where .= " AND order_sn LIKE '%{$order_sn}%'";
    }
    if ($idcard != '') {
        $where .= " AND idcard LIKE '%{$idcard}%'";
    }
    if ($name != '') {
        $where .= " AND name = '{$name}'";
    }
    if ($phone != '') {
        $where .= " AND phone = '{$phone}'";
    }
    if (!empty($route_id)) {
        $shift_all = getAllData('shift', " AND route_id = '{$route_id}'", " time ASC");
        if ($shift_all) {
            foreach ($shift_all as $key => $shift_row) {
                $shift_all[$key]['time'] = timeToStr(GetMkTime("1990-12-19 " . $shift_row['time']), "H:i");
            }
        }
    }
    if (!empty($route_id) && empty($shift_id)) {
        if (!empty($shift_all)) {
            $shift_ids = array();
            foreach ($shift_all as $key => $shift_row) {
                $shift_all[$key]['time'] = timeToStr(GetMkTime("1990-12-19 " . $shift_row['time']), "H:i");
                $shift_ids[] = intval($shift_row['id']);
            }
            if (!empty($shift_ids)) {
                $shift_ids = implode(",", $shift_ids);
                $where .= " AND shift_id IN( {$shift_ids} )";
            }
        }
    } else {
        if (!empty($shift_id)) {
            $where .= " AND shift_id = '{$shift_id}'";
        }
    }
    if ($_GPC['id'] != '') {
        $ids = explode(",", $_GPC['id']);
        $ids = implode(",", array_map(function($id){return intval($id);}, $ids));
        $where .= " AND id IN( {$ids} )";
    }
    if ($date != '') {
        if (!empty($shift_id)) {
            $time = getDataById("shift", $shift_id, "time");
            $departure_time = GetMkTime($date . " " . $time);
            $where .= " AND departure_time = '{$departure_time}'";
        }else{
            $start_date_time = GetMkTime($date . " 00:00:00");
            $end_date_time = GetMkTime($date . " 23:59:59");
            $where .= " AND departure_time > '{$start_date_time}' AND departure_time < '{$end_date_time}'";
        }
    }
    if (checksubmit('export_submit', true)) {
        $list = getAllData($table_name, $where);
        $header = array(
            'order_sn' => '订单号', 'route' => '路线', 'shift' => '班次', 'time' => '发车时间', 
            'number' => '购票数', 'price' => '车票价格', 'seat_number' => '座位号', 
            'name' => '姓名', 'phone' => '手机号码', 'idcard' => '身份证号码',
            'status' => '订单状态', 'voucher' => '代金券', 'createtime' => '预定时间', 'addons' => '附加项', 'froms' => '订单源',
        );
        $keys = array_keys($header);
        $html = "\xEF\xBB\xBF";
        foreach ($header as $li) {
            $html .= $li . "\t ,";
        }
        $html .= "\n";
        if (!empty($list)) {
            $order = array();
            $size = ceil(count($list) / 500);
            for ($i = 0; $i < $size; $i++) {
                $buffer = array_slice($list, $i * 500, 500);
                foreach ($buffer as $row) {
                    //$shift = getDataById('shift', $row['shift_id']);
                    $shift = getShiftInfo($row['start_station_id'], $row['end_station_id'], $row['shift_id']);
                    $route = getDataById('route', $shift['route_id']);
                    if ($row['status'] == 3) {
                        $row['price'] = $row['price'] . "+改签费：{$row['change_cost']}";
                    }
                    $row['route'] = $route['name'];
                    $row['shift'] = $shift['start_station']['name'] . '->' . $shift['end_station']['name'];
                    $row['time'] = timeToStr($row['departure_time'], 'Y-m-d H:i');
                    $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
                    $row['status'] = isset($status_text[$row['status']]) ? $status_text[$row['status']] : '';
                    $row['idcard'] = empty($row['idcard']) ? '--' : $row['idcard'];
                    $voucher = array();
                    if ($row['voucher_id'] > 0) {
                        $voucher = getVoucherById($row['voucher_id'], $row['uid']);
                    }
                    $row['voucher'] = empty($voucher) ? "无" : $voucher['name'];
                    $row['addons'] = empty($row['addons']) ? "无" : $row['addons'];
                    $row['froms'] = $row['froms'] == "WEB" ? "网页" : "微信";
                    foreach ($keys as $key) {
                        $data[] = $row[$key];
                    }
                    $order[] = implode("\t ,", $data) . "\t ,";
                    unset($data);
                }
            }
            $html .= implode("\n", $order);
        }

        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=订单数据.csv");
        echo $html;
        exit();
    }
    $result = getPageList($table_name, $_GPC['page'], $where);
    
    if (!empty($result['list'])) {
        foreach ($result['list'] as $key => $item) {
            $shift = getShiftInfo($item['start_station_id'], $item['end_station_id'], $item['shift_id']);
            $result['list'][$key]['shift'] = $shift;
            $voucher = array();
            if ($item['voucher_id'] > 0) {
                $voucher = getVoucherById($item['voucher_id'], $item['uid']);
            }
            $result['list'][$key]['voucher'] = empty($voucher) ? "无" : $voucher['name'];
            $result['list'][$key]['user'] = getUserData($item['uid']);
            $result['list'][$key]['isRefund'] = checkOrderRefund($item);
            $result['list'][$key]['isChange'] = checkOrderChanged($item);
            //$result['list'][$key]['seat_number'] = getSeat($item);
        }
    }
    $route_all = getAllData('route');
}
/**
 * 添加修改
 */ 
else if ($act == 'info') {
    $id = intval($_GPC['id']);
    $order_info = getDataById($table_name, $id);
    //$order_info['seat_number'] = getSeat($order_info);
    if (empty($order_info)) {
        message("该订单不存在或已删除");
    }
    //$shift = getDataById('shift', $order_info['shift_id']);
    $shift = getShiftInfo($order_info['start_station_id'], $order_info['end_station_id'], $order_info['shift_id']);
    $route = getRouteData($shift['route_id']);
    $shift['vehicle'] = getDataById("vehicle", $order_info['vehicle_id']);
    if ($order_info['voucher_id'] > 0) {
        $voucher = getVoucherById($order_info['voucher_id'], $order_info['uid']);
    }
}
/**
 * 车票打印
 */ 
else if ($act == 'print') {
    if (is_string($_GPC['id']) || is_numeric($_GPC['id'])) {
        $ids = explode(",", $_GPC['id']);
    }
    $ids = implode(",", array_map(function($id){return intval($id);}, $ids));
    $where .= " AND id IN( {$ids} )";
    $order_infos = getAllData($table_name, $where);
    //$order_info['seat_number'] = getSeat($order_info);
    if (empty($order_infos)) {
        message("该订单不存在或已删除");
    }
    $shifts = array();
    foreach($order_infos AS &$order_info) {
        if (!isset($shifts["{$order_info['start_station_id']}-{$order_info['end_station_id']}-{$order_info['shift_id']}"])) {
            $shifts["{$order_info['start_station_id']}-{$order_info['end_station_id']}-{$order_info['shift_id']}"] = getShiftInfo($order_info['start_station_id'], $order_info['end_station_id'], $order_info['shift_id']);
            $shifts['vehicle'] = getShiftVehicle($order_info['shift_id'], timeToStr($order_info['departure_time'], "Y-m-d"));
            
        }
        if ($order_info['voucher_id'] > 0) {
            $voucher = getVoucherById($order_info['voucher_id'], $order_info['uid']);
        }
        $shift = $shifts["{$order_info['start_station_id']}-{$order_info['end_station_id']}-{$order_info['shift_id']}"];
        /* 可视化 */
        /* 快递单 */
        $order_info['print_bg'] = empty($this->module['config']['print_bg']) ? '' : toimage($this->module['config']['print_bg']);
        /* 取快递单背景宽高 */
        if (!empty($order_info['print_bg'])) {
            $_size = @getimagesize($order_info['print_bg']);
            if ($_size != false) {
                $order_info['print_bg_size'] = array('width' => $_size[0], 'height' => $_size[1]);
            }
        }

        if (empty($order_info['print_bg_size'])) {
            $order_info['print_bg_size'] = array('width' => '468', 'height' => '193');
        }

        /* 标签信息 */
        $lable_box = array();
        for($i=0; $i < 10; $i++) {
            $index = $i == 0 ? "" : $i;
            $lable_box["t_date{$index}"] = getDateStr($order_info['departure_time']); //乘车日期
            $lable_box["t_time{$index}"] = timeToStr($order_info['departure_time'], "H:i"); //发车时间
            $lable_box["t_license_plate{$index}"] = $shifts['vehicle']['license_plate']; //车牌号
            $lable_box["t_start_station_name{$index}"] = $shift['start_station']['name']; //始发站
            $lable_box["t_end_station_name{$index}"] = $shift['end_station']['name']; //终点站

            $lable_box["t_order_sn{$index}"] = $order_info['order_sn']; //订单号
            $lable_box["t_seat_number{$index}"] = $order_info['seat_number']; //座位号
            $lable_box["t_price{$index}"] = $order_info['price']; //车票价钱
            $lable_box["t_name{$index}"] = $order_info['name']; //客户姓名
            $lable_box["t_phone{$index}"] = $order_info['phone']; //客户电话
            $lable_box["t_idcard{$index}"] = $order_info['idcard']; //客户身份证
        }

        //标签替换
        $temp_config_lable = explode('||,||', $this->module['config']['config_lable']);
        if (!is_array($temp_config_lable)) {
            $temp_config_lable[] = $this->module['config']['config_lable'];
        }
        foreach ($temp_config_lable as $temp_key => $temp_lable) {
            $temp_info = explode(',', $temp_lable);
            if (is_array($temp_info)) {
                $temp_info[1] = $lable_box[$temp_info[0]];
            }
            $temp_config_lable[$temp_key] = implode(',', $temp_info);
        }
        $order_info['config_lable'] = implode('||,||', $temp_config_lable);
    }
    $order_config = array();
    $eval_script = '';
    $print_div = '';
    foreach($order_infos as $order2) {
        $print_div .= "<div class='main' id='print{$order2['id']}'></div>";
        $eval_script .= "_create_tickets_print({$order2['id']});";
        $order_config[$order2['id']] = array(
            "config_lable" => "{$order2['config_lable']}",
            "print_bg" => "{$order2['print_bg']}",
            "width" => "{$order2['print_bg_size']['width']}px",
            "height" => "{$order2['print_bg_size']['height']}px",
        );
    }
}
/**
 * 退票
 */ 
else if ($act == 'return') {
    $order_id = intval($_GPC['id']);
    $order_info = getDataById('order', $order_id);
    if (empty($order_info)) {
        message("该订单信息不存在或已删除", '', 'error');
    }
    $refund_fee = checkOrderRefund($order_info);
    if ($refund_fee === false) {
        message("该订单不符合退票申请要求", '', 'error');
    }
    if ($refund_fee['fee_type'] == 0) {
        $fee = $order_info['price'] * ($refund_fee['fee'] / 100);
    }else{
        $fee = $refund_fee['fee'];
    }
    $refundable = $order_info['price'] - $fee;
    if ($_W['ispost']) {
        $update_order_data = array(
            'status' => 2,
            'update_time' => TIMESTAMP,
            'refund_fee' => $refundable,
            'price' => $fee,
        );
        fr_update('order', $update_order_data, array('id' => $order_id));
        action_log("退票操作，订单号：{$order_info['order_sn']};", "edit", "order");
        if ($order_info['voucher_id'] > 0) {//改变代金券状态
            updateVoucherStatus($order_info['voucher_id'], $order_info['uid'], 0);
        }
        deleteVoucherByOrderId($order_id, $uid); //删除该订单送的代金券
        message('退票操作成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
        exit();
    }
    $shift = getShiftInfo($order_info['start_station_id'], $order_info['end_station_id'], $order_info['shift_id']);
    $route = getRouteData($shift['route_id']);
    $shift['vehicle'] = getDataById("vehicle", $order_info['vehicle_id']);
    if ($order_info['voucher_id'] > 0) {
        $voucher = getVoucherById($order_info['voucher_id'], $order_info['uid']);
    }
}

else if ($act == 'changing') {
    $order_id = intval($_GPC['id']);
    $order_info = getDataById('order', $order_id);
    if (empty($order_info)) {
        message("该订单信息不存在或已删除", '', 'error');
    }
    $refund_fee = checkOrderChanged($order_info);
    if ($refund_fee === false) {
        message("该订单不符合改签申请要求", '', 'error');
    }
    if ($refund_fee['fee_type'] == 0) {
        $fee = $order_info['price'] * ($refund_fee['fee'] / 100);
    }else{
        $fee = $refund_fee['fee'];
    }
    if ($_W['ispost']) {
        $change_date = timeToStr(GetMkTime($_GPC['change_date']), "Y-m-d");
        $change_shift_id = $_GPC['change_shift_id'];
        $change_shift = getShiftInfo($order_info['start_station_id'], $order_info['end_station_id'], $change_shift_id);
        $change_departure_time = GetMkTime($change_date . " " . $change_shift['time']);
        $change_shift['surplus_votes'] = getSurplusVotes($change_shift, $change_departure_time);
        
        if ($change_departure_time < TIMESTAMP) {
            message("改签时间有误，不能小于当前时间", '', 'error');
        }
        if ($change_departure_time == $order_info['departure_time']) {
            message("改签时间有误，改签时间不能与车票现在时间相同", '', 'error');
        }
        
        if ($change_shift['surplus_votes'] < 1) {
            message("该班次余票不足，请重新其他班次", '', 'error');
        }
        $seat_number = getSeatNumber($change_shift, $change_departure_time, $order_info['seat_number']);
        
        $update_order_data = array(
            'shift_id' => $change_shift_id,
            'status' => 3,
            'seat_number' => $seat_number,
            'update_time' => TIMESTAMP,
            'change_cost' => $fee,
            'before_departure_time' => $order_info['departure_time'],
            'departure_time' => $change_departure_time,
        );
        
        fr_update('order', $update_order_data, array('id' => $order_id));
        $sn = "改签操作，订单号：{$order_info['order_sn']};" . timeToStr($order_info['departure_time'], "Y-m-d H:i") . "改签至" . timeToStr($change_departure_time, "Y-m-d H:i");
        action_log("改签操作，订单号：{$order_info['order_sn']};", "edit", "order");
        message('改签操作成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
        exit();
    }
    $shift = getShiftInfo($order_info['start_station_id'], $order_info['end_station_id'], $order_info['shift_id']);
    $route = getRouteData($shift['route_id']);
    
    $shift_all = getAllData("shift", " AND route_id = '{$shift['route_id']}'", " time ASC");
    foreach($shift_all as $key => $item) {
        $shift_all[$key]['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
    }
    $shift['vehicle'] = getDataById("vehicle", $order_info['vehicle_id']);
    if ($order_info['voucher_id'] > 0) {
        $voucher = getVoucherById($order_info['voucher_id'], $order_info['uid']);
    }
}
/**
 * 删除数据
 */ 
else if ($act == 'delete') {
    $id = intval($_GPC['id']);
    $item = getDataById($table_name, $id);
    if ($item['status'] != -1) {
        message("该订单不允许删除！");
    }
    action_log("订单号：{$item['order_sn']};订单状态：{$status_text[$item['status']]}", "delete", "order");
    fr_delete($table_name, array('id' => $id, 'uniacid' => $uniacid));
    //deleteSeat($order_info);//删除座位号
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}
/**
 * 获取班次
 */ 
else if ($act == 'get_shift') {
    $route_id = intval($_GPC['route_id']);
    $shift_all = getAllData('shift', " AND route_id = '{$route_id}'", " time ASC");
    $return = array(
        'html' => '',
        'error' => 1
    );
    if (!empty($shift_all)) {
        $shift_ids = array();
        $html = '<option value="" selected="selected">所有班次</option>';
        foreach ($shift_all as $row) {
            $row['time'] = timeToStr(GetMkTime("1990-12-19 " . $row['time']), "H:i");
            $html .= '<option value="' . $row['id'] . '">发车时间：' . $row['time'] . '</option>';
        }
        $return['html'] = $html;
    }
    exit(json_encode($return));
}
elseif ($act == 'check_order') {
    if (empty($_GPC['fr_cp_last_check'])) {
        isetcookie('fr_cp_last_check', TIMESTAMP, 0, true);
        $res = array('error' => 0, 'new_orders' => 0, 'new_paid' => 0, 'fr_cp_last_check1' => $_GPC['fr_cp_last_check']);
        exit(json_encode($res));
    }

    /* 新订单 */
    $new_orders = getCol("order", "count(*)", " AND createtime >= '{$_GPC['fr_cp_last_check']}' AND froms = 'mobile'");

    /* 新付款的订单 */
    $new_paid = getCol("order", "count(*)", " AND pay_time >= '{$_GPC['fr_cp_last_check']}' AND froms = 'mobile'");

    isetcookie('fr_cp_last_check', TIMESTAMP, 0, true);

    if (!(is_numeric($new_orders) && is_numeric($new_paid))) {
        $res = array('error' => 1, 'fr_cp_last_check2' => $_GPC['fr_cp_last_check']);
        exit(json_encode($res));
    }else{
        $res = array('error' => 0, 'new_orders' => $new_orders, 'new_paid' => $new_paid, 'fr_cp_last_check3' => $_GPC['fr_cp_last_check']);
        exit(json_encode($res));
    }
}

include $this->template("web/{$fr_model}_" . $act);
