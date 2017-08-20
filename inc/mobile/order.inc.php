<?php
 
include MODULE_ROOT . '/inc/common.php';
include MODULE_ROOT . '/inc/mobile/init.php';
$uniacid = $_W["uniacid"];
$openid = $_W['openid'];
$uid = $_W['member']['uid'];
$act = trim($_GPC['act']);
$allow_acts = array(
    'checkout', 'done', 'info', 'cancel', 'pay', 'changeprice', 'refund_ticket'
);
checkauth();
if (!in_array($act, $allow_acts)) {
    $act = 'checkout';
}
/**
 * 订单确认
 */
if ($act == 'checkout') {
    cleanParentOrder();
    $title = "购票确认";
    $shift_id = intval($_GPC['shift_id']);
    $start_station_id = intval($_GPC['start_station_id']);
    $end_station_id = intval($_GPC['end_station_id']);
    $date = trim($_GPC['date']);
    $_SESSION["fr_cp_token"] = gen_token();
    
    $shift = getShiftInfo($start_station_id, $end_station_id, $shift_id);
    $departure_time = GetMkTime($date . " ". $shift['time']);
    $vehicle = getShiftVehicle($shift_id, $date);
    if (!empty($vehicle)) {
        $shift['vehicle'] = $vehicle;
    }else{
        message("该班次已停班！", '', 'error');
    }
    $shift['surplus_votes'] = getSurplusVotes($shift, $departure_time);
    $seatNumber = getSeatNumber($shift, $departure_time);
    $seatNumberList = getSeatNumberList($shift, $departure_time);
    
    $addons = getAllData('addons', " AND route_id = '{$shift['route_id']}'", "sort ASC");
    
//    dump($route);die;
    if (empty($shift)) {
        message("该班次信息不存在", '', 'error');
    }
    if ($shift['surplus_votes'] <= 0) {
        message("该班次车票已售罄！", '', 'error');
    }
    if ($departure_time < TIMESTAMP) {
        message("该班次已发班！", '', 'error');
    }
//    dump($shift);die;
    $sql = "SELECT * FROM ". fr_table('user') . " WHERE uid = :uid AND uniacid = :uniacid";
    $params = array(":uid" => $uid, ":uniacid" => $uniacid);
    $profile = pdo_fetch($sql, $params);
    $voucher = getVoucher($uid, $shift['start_station_id'], $shift['end_station_id'], $shift_id);
//    dump($voucher);die;
}
/**
 * 订单详情
 */
else if($act == 'info') {
    cleanParentOrder();
    $title = "订单详情";
    $order_id = intval($_GPC['order_id']);
    $order_info = getDataById('order', $order_id);
    //$order_info['seat_number'] = getSeat($order_info);
    if (empty($order_info)) {
        $order_info = getRow("order", " AND parent_id = '{$order_id}'");
        if (!empty($order_info)) {
            redirect(__MURL("user", array('act' => 'order', 'status' => '1')));
        }else{
            message("该订单信息不存在或已删除", '', 'error');
        }
    }
    $shift = getShiftInfo($order_info["start_station_id"], $order_info['end_station_id'], $order_info['shift_id']);
    $route = getRouteData($shift['route_id']);
//    dump($shift);die;
    if ($order_info['voucher_id'] > 0) {
        $voucher = getVoucherById($order_info['voucher_id'], $uid);
    }
    $writeOffQrcodeUrl = genOrderQrcode($order_id);
}
/**
 * 取消订单
 */
else if($act == 'cancel') {
    cleanParentOrder();
    $title = "订单详情";
    $order_id = intval($_GPC['order_id']);
    $order_info = getDataById('order', $order_id);
    $order_info['seat_number'] = getSeat($order_info);
    if (empty($order_info)) {
        message("该订单信息不存在或已删除", '', 'error');
    }
    if ($order_info['status'] != 0) {
        message("该订单无法取消", '', 'error');
    }
    $update_order_data = array(
        'status' => -1
    );
    fr_update('order', $update_order_data, array('id' => $order_id));
    if ($order_info['voucher_id'] > 0) {//改变代金券状态
        updateVoucherStatus($order_info['voucher_id'], $order_info['uid'], 0);
    }
    deleteSeat($order_info);
    message("订单取消成功！");
    die;
}
/**
 * 提交订单
 */
else if($act == 'done') {
    cleanParentOrder();
    if (isset($_SESSION['fr_cp_token']) && $_GPC['fr_cp_token'] == $_SESSION['fr_cp_token']) {
        unset($_SESSION['fr_cp_token']);
        $postdata = $_GPC['postdata'];
        $shift_id = intval($postdata['shift_id']);
        $number = intval($postdata['number']);
        $voucher_id = intval($postdata['voucher']);
        $departure_date = timeToStr($postdata['departure_time'], "Y-m-d");
        $seat_number = explode(",", $postdata['seat_number']);
        $voucher = getVoucherById($voucher_id, $uid);
        if (!empty($voucher) && $voucher['status'] == 0) {
            $voucher_id = $voucher['id'];
        }else{
            $voucher_id = 0;
        }
        
        $route = getRouteData($shift['route_id']);
        $shift = getShiftInfo($postdata["start_station_id"], $postdata['end_station_id'], $shift_id);
        $departure_time = GetMkTime($departure_date . " " . $shift['time']);
        $shift['surplus_votes'] = getSurplusVotes($shift, $departure_time);
        if (empty($shift)) {
            message("该班次信息不存在", '', 'error');
        }
        if ($shift['surplus_votes'] <= 0) {
            message("该班次车票已售罄！", '', 'error');
        }
        if ($departure_time < TIMESTAMP) {
            message("该班次已发班！", '', 'error');
        }
        if ($number < 1) {
            message("购票数量错误", '', 'error');
        }
        if ($shift['surplus_votes'] < $number) {
            message("余票不足，请重新选择购票数量", '', 'error');
        }
        if (empty($postdata['name'])) {
            message("请填写购票人信息", '', 'error');
        }
        if (empty($postdata['phone']) || !validMobile($postdata['phone'])) {
            message("请填写正确的手机号码", '', 'error');
        }
        if (empty($postdata['idcard']) || !validIdCard($postdata['idcard'])) {
            message("请填写正确的身份证号码", '', 'error');
        }
//        if ($route['insurance'] == 1) {
//        }
        $addons = getAddons($postdata['addons'], $shift['route_id']);
        $addons_id = array();
        $addons_text = array();
        if (is_array($addons) && !empty($addons)) {
            foreach ($addons AS $addone){
                $addons_id[] = $addone['id'];
                $addons_text[] = $addone['title'] . "[￥{$addone['price']}]";
            }
        }
        $vehicle = getShiftVehicle($shift_id, $departure_date);
        if (!empty($vehicle)) {
            $shift['vehicle'] = $vehicle;
        }
        $parent_id = 0;
        if ($number > 1) {
            $price = order_price($number, $shift['ticket_price'], $voucher_id, $uid, $shift['route_id'], $addons_id);//计算订单价格
            $order_info = array(
                'uniacid' => $uniacid,
                'shift_id' => $shift_id,
                'uid' => $uid,
                'openid' => $openid,
                'name' => $postdata['name'],
                'phone' => $postdata['phone'],
                'idcard' => empty($postdata['idcard']) ? "" : trim($postdata['idcard']),
                'price' => $price,
                'number' => 1,
                'voucher_id' => $voucher_id,
                'addons_id' => implode(",", $addons_id),
                'addons' => implode(";", $addons_text),
                'start_station_id' => $postdata['start_station_id'],
                'end_station_id' => $postdata['end_station_id'],
                'seat_number' => 99999,
    //            'order_sn' => genOrderSN(),
                'departure_time' => $departure_time,
                'createtime' => TIMESTAMP,
                'is_paid' => 0,
                'status' => 0,
                'parent_id' => 0,
                'vehicle_id' => $shift['vehicle']['id']
            );

            $res = fr_insert("order", $order_info);
            $parent_id = pdo_insertid();
            $order_sn = genOrderSN($parent_id);
            fr_update("order", array('order_sn' => $order_sn), array('id' => $parent_id));
        }
        $order_ids = array();
        $order_seat = array();
        for($i=0; $i < $number; $i++) {
            if ($i > 0) {//代金券只能用一个张票
                $voucher_id = 0;
                $addons_id = array();
                $addons_text = array();
            }
            $price = order_price(1, $shift['ticket_price'], $voucher_id, $uid, $shift['route_id'], $addons_id);//计算订单价格
            $order_seat_number = getSeatNumber($shift, $departure_time, $seat_number[$i], $order_seat);
            $order_info = array(
                'uniacid' => $uniacid,
                'shift_id' => $shift_id,
                'uid' => $uid,
                'openid' => $openid,
                'name' => $postdata['name'],
                'phone' => $postdata['phone'],
                'idcard' => empty($postdata['idcard']) ? "" : trim($postdata['idcard']),
                'price' => $price,
                'number' => 1,
                'voucher_id' => $voucher_id,
                'addons_id' => implode(",", $addons_id),
                'addons' => implode(";", $addons_text),
                'start_station_id' => $postdata['start_station_id'],
                'end_station_id' => $postdata['end_station_id'],
                'seat_number' => $order_seat_number,
    //            'order_sn' => genOrderSN(),
                'departure_time' => $departure_time,
                'createtime' => TIMESTAMP,
                'is_paid' => 0,
                'status' => 0,
                'parent_id' => $parent_id,
                'vehicle_id' => $shift['vehicle']['id']
            );
            $order_seat[]  = $order_seat_number;
            $res = fr_insert("order", $order_info);
            if ($res === false) {
                continue;
            }else{
                $order_ids[] = $order_id = $order_info['id'] = pdo_insertid();
                $order_info['order_sn'] = $order_sn = genOrderSN($order_info['id']);
                fr_update("order", array('order_sn' => $order_sn), array('id' => $order_info['id']));
                if ($voucher_id > 0) {//改变代金券状态
                    updateVoucherStatus($voucher_id, $uid, 1);
                }
                
                notificationManager($order_info, 0);//通知管理员
                //$seat_numbers = genSeatNumber($order_info);
               if ($shift['method'] == 1) {
                    $title = "您已成功预订1张车票，共￥{$price}";
                }else{
                    $title = "您已成功购买1张车票，共￥{$price}";
                }

                $templateid = trim($fr_cp_settings['templateid']);
                if ($openid && !empty($templateid) && $shift['method'] == 1) {
                    $postdata = array(
                        'first' => array(
                            'value' => $title,
                            'color' => '#173177',
                        ),
                        'keyword1' => array(
                            'value' => "{$shift['start_station']['name']}->{$shift['end_station']['name']}",
                            'color' => '#173177',
                        ),
                        'keyword2' => array(
                            'value' => timeToStr($shift['time'], "Y-m-d H:i"),
                            'color' => '#173177',
                        ),
                        'keyword3' => array(
                            'value' => $order_sn,
                            'color' => '#173177',
                        ),
                        'keyword4' => array(
                            'value' => '',
                            'color' => '',
                        ),
                        'remark' => array(
                            'value' => $fr_cp_settings['content3'],
                            'color' => '',
                        ),
                    );
                    $url = __MURL('order', array('act' => 'info', 'order_id' => $order_id));
                    sendTplNotice('', $openid, $postdata, $templateid, $url);
                    //发送通知
                }
                if ($fr_cp_settings['print_type'] == 1 && $shift['method'] == 1) {
                    $voucher = array();
                    if ($order_info['voucher_id'] > 0) {
                        $voucher = getVoucherById($order_info['voucher_id'], $order_info['uid']);
                    }
                    $result = array(
                        "start_station" => $shift['start_station']['name'],
                        "end_station" => $shift['end_station']['name'],
                        "datetime" => timeToStr($shift['time']),
                        "name" => $order_info['name'],
                        "phone" => $order_info['phone'],
                        "idcard" => empty($order_info['idcard']) ? "-" : $order_info['idcard'],
                        "number" => $order_info['number'],
                        "voucher" => empty($voucher) ? "无" : $voucher['name'],
                        "price" => "{$order_info['price']}元",
                    );
                    if (print_ticket($result, $fr_cp_settings)) {
                        fr_update('order', array('isprint' => 1), array('id' => $order_id));
                    }
                }
            }
        }
        if (empty($order_ids)) {
            message("服务器忙，请稍候再试");
        }
        
        $user = getRow("user", " AND uid = '{$uid}'");
        $user_update = array();
        if (empty($user['phone'])) {
            $user_update['phone'] = $postdata['phone'];
        }
        if (empty($user['name'])) {
            $user_update['name'] = $postdata['name'];
        }
        if (empty($user['idcard'])) {
            $user_update['idcard'] = $postdata['idcard'];
        }
        if (!empty($user_update)) {
            $rs = fr_update('user', $user_update, array('uid' => $uid));
        }

        if ($shift['method'] == 1) {
            if ($parent_id > 0) {
                fr_delete("order", " AND id = $parent_id");
                message("订票成功", __MURL('user', array('act' => 'order')));
            }else{
                message("订票成功", __MURL('order', array('act' => 'info', 'order_id' => $order_ids[0])));
            }
        }else{
            if ($parent_id > 0) {
                $order_id = $parent_id;
            }else{
                $order_id = $order_ids[0];
            }
            message("座位先付先得，马上支付获得座位！", __MURL('order', array('act' => 'pay', 'order_id' => $order_id)));
        }
    }else{
        message('请重新提交订单！', referer(), 'error');
    }
    exit();
}
/**
 * 订单支付
 */
else if($act == 'pay') {
    $title = "订单支付";
    $order_id = intval($_GPC['order_id']);
    $order_info = getDataById("order", $order_id);
    if ($order_info['seat_number'] == 99999) {//多订单合并付款
        $seat_numbers = getAllData("order", " AND parent_id = '{$order_id}'", " seat_number ASC", "seat_number", "seat_number");
        $seat_numbers = array_keys($seat_numbers);
    }
    //$order_info['seat_number'] = getSeat($order_info);
    if (empty($order_info)) {
        message("订单信息不存在或已删除", '', 'error');
    }
    if ($order_info['ispaid'] == 1) {
        message('这个订单已经支付成功, 不需要重复支付.');
    }
    if ($order_info['status'] == -1) {
        message('这个订单已经被取消，不能支付.');
    }
    //$shift = getDataById('shift', $order_info['shift_id']);
//    $route = getRouteData($shift['route_id']);
    $shift = getShiftInfo($order_info["start_station_id"], $order_info['end_station_id'], $order_info['shift_id']);
    if (!empty($seat_numbers)) {
        foreach ($seat_numbers as $seat_number) {
            $seat_number2 = getSeatNumber($shift, $order_info['departure_time'], $seat_number);
            if ($seat_number != $seat_number2) {
                message("该座位号“{$seat_number}”已被其他人预定，请分开付款", __MURL('user', array('act' => 'order', 'status' => '0')));
            }
        }
    }else{
        $seat_number2 = getSeatNumber($shift, $order_info['departure_time'], $order_info["seat_number"]);
        if ($order_info["seat_number"] != $seat_number2) {
            message('该座位已被其他人预定，请重新下单');
        }
    }
    if ($shift['method'] == 1) {
        message('该班次只接受预订车票！');
    }
    if ($shift['surplus_votes'] < 1) {
        message('该班次车票已售完！');
    }
    if ($shift['surplus_votes'] < $order_info['number']) {
        message("余票不足，请重新下单！", '', 'error');
    }
    
    $params = array(
        'tid' => $order_info['id'],      //充值模块中的订单号，此号码用于业务模块中区分订单，交易的识别码
        'ordersn' => $order_info['order_sn'],  //收银台中显示的订单号
        'title' => "{$shift['start_station']['name']}->{$shift['end_station']['name']}",          //收银台中显示的标题
        'datetime' => timeToStr($order_info['departure_time'], 'Y-m-d H:i'),          //收银台中显示的标题
        'seat_number' => !empty($seat_numbers) ? implode(",", $seat_numbers) : $order_info['seat_number'],          //收银台中显示的标题
        'fee' => floatval($order_info['price']),      //收银台中显示需要支付的金额,只能大于 0
        'user' => $uid,     //付款用户, 付款的用户名(选填项)
    );
    $_W['page']['footer'] = '<div></div>';
}
/**
 * 申请退票
 */
else if($act == 'refund_ticket') {
    redirect(__MURL("order"));
    cleanParentOrder();
    $order_id = intval($_GPC['order_id']);
    $order_info = getDataById('order', $order_id);
    if (empty($order_info)) {
        message("该订单信息不存在或已删除", '', 'error');
    }
    $shift = getDataById('shift', $order_info['shift_id']);
    if ($order_info['status'] != 1 || $shift['refund_ticket'] != 1) {
        message("该订单不符合退票申请要求", '', 'error');
    }
    $update_order_data = array(
        'status' => 2
    );
    fr_update('order', $update_order_data, array('id' => $order_id));
    
    if ($order_info['voucher_id'] > 0) {//改变代金券状态
        updateVoucherStatus($order_info['voucher_id'], $order_info['uid'], 0);
    }
    deleteVoucherByOrderId($order_id, $uid); //删除该订单送的代金券
    message("已成功申请退票！");
    die;
}
/**
 * 计算订单价格
 */
else if($act == 'changeprice') {
    $ticket_price = floatval($_GPC['ticket_price']);
    $number = intval($_GPC['number']);
    $voucher_id = intval($_GPC['voucher']);
    $route_id = intval($_GPC['route_id']);
    $addons_id = trim($_GPC['addons_id']);
    $price = order_price($number, $ticket_price, $voucher_id, $uid, $route_id, $addons_id);
    echo floatval($price);exit;
}
include $this->template("order_" . $act);