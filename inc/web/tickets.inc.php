<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];

$act = trim($_GPC['act']);
$allow_acts = array(
    'index', 'sell', 'endCitys', 'query', 'sell_check', 'select_seat'
);
if (!in_array($act, $allow_acts)) {
    $act = 'index';
}

$end_date = timeToStr(strtotime("+{$fr_cp_settings['day']} day"), 'Y-m-d');
$days = getDays2($fr_cp_settings['day']);
//dump($days);die;
$title = "在线售票系统";
$start_date_time = GetMkTime($_GPC['startDate']);
$end_date_time = GetMkTime($end_date);
$start_date_time = $start_date_time < TIMESTAMP ? TIMESTAMP : ($start_date_time > $end_date_time ? $end_date_time : $start_date_time);
$start_date = timeToStr($start_date_time, 'Y-m-d');
if ($act == 'index') {
    $hot_route = getRecommendRoute();
    //dump($hot_route);die;
}

elseif ($act == 'sell') {
    $shift_id = intval($_GPC['shift_id']);
    $start_station_id = intval($_GPC['start_station_id']);
    $end_station_id = intval($_GPC['end_station_id']);
    $numbers = intval($_GPC['numbers']);
    $startDate = trim($_GPC['startDate']);
    $name = trim($_GPC['name']);
    $idcard = trim($_GPC['idcard']);
    $remarks = trim($_GPC['remarks']);
    $phone = trim($_GPC['phone']);
    $seat_number = explode(",", $_GPC['seat_number']);
    if (empty($shift_id) || empty($start_station_id) || empty($end_station_id) || empty($startDate)) {
        message("参数有误！", "", "error");
    }
    $shift_info = getShiftInfo($start_station_id, $end_station_id, $shift_id);
    $departure_time = GetMkTime($startDate . " ". $shift_info['time']);
    $shift_info['surplus_votes'] = getSurplusVotes($shift_info, $departure_time);
    if (empty($shift_info)) {
        message("该班次信息不存在", '', 'error');
    }
    if ($shift_info['surplus_votes'] <= 0) {
        message("该班次车票已售罄！", '', 'error');
    } 
    if ($departure_time < TIMESTAMP) {
        message("该班次已发班！", '', 'error');
    }
    if ($numbers < 1) {
        message("购票数量错误", '', 'error');
    }
    if ($shift_info['surplus_votes'] < $numbers) {
        message("余票不足，请重新选择购票数量", '', 'error');
    }
    $vehicle = getShiftVehicle($shift_id, $startDate);
    if (!empty($vehicle)) {
        $shift_info['vehicle'] = $vehicle;
    }else{
        message("该班次已停班！", '', 'error');
    }
    if (!empty($name) && !empty($idcard)) {
        $where = " AND name = '{$name}' AND idcard = '{$idcard}'";
        $user = getRow("user", $where);
        if (empty($user)) {
            $user = array(
                'uniacid' => $uniacid, 'openid' => '', 'uid' => '',
                'phone' => $phone, 'sex' => 1,
                'name' => $name, 'idcard' => $idcard, 'remarks' => $remarks,
            );
            fr_insert('user', $user);
        }
    }
    $price = order_price(1, $shift_info['ticket_price'], 0, 0, 0, 0);//计算订单价格
    $order_ids = array();
    $order_seat = array();
    for($i = 0; $i < $numbers; $i++) {
        $order_seat_number = getSeatNumber($shift_info, $departure_time, $seat_number[$i], $order_seat);
        $order_info = array(
            'uniacid' => $uniacid,
            'shift_id' => $shift_id,
            'price' => $price,
            'start_station_id' => $start_station_id,
            'end_station_id' => $end_station_id,
            'seat_number' => $order_seat_number,
            'number' => 1,
            'createtime' => TIMESTAMP,
            'is_paid' => 1,
            'status' => 1,
            'froms' => 'WEB',
            'uid' => 0,
            'openid' => '',
            'name' => $name,
            'phone' => $phone,
            'idcard' => $idcard,
            'voucher_id' => 0,
            'addons_id' => "",
            'addons' => "",
            'admin_uid' => $_W['uid'],
            'departure_time' => $departure_time,
            'vehicle_id' => $shift_info['vehicle']['id']
    //            'order_sn' => genOrderSN(),
        );
        $order_seat[]  = $order_seat_number;
        $res = fr_insert("order", $order_info);
        if ($res !== false) {
            $order_ids[] = $order_info['id'] = pdo_insertid();

            //获取订单号
            $order_sn = genOrderSN($order_info['id']);
            fr_update("order", array('order_sn' => $order_sn), array('id' => $order_info['id']));

            $sn = "售{$shift_info['start_station']['name']} 至 {$shift_info['end_station']['name']} 路线 ".  timeToStr($order_info['departure_time'], "Y-m-d H:i")."班次车票1张";
            action_log($sn, "add", "order");
        }
    }
    
    if (!empty($order_ids)) {
        message("下单成功, 打印车票！", __WURL("order", array('act' => 'print','id' => implode(",", $order_ids))), 'success');
    }else{
        message("购票失败请重新下单！", '', 'error');
    }
    exit();
}

elseif ($act == 'sell_check') {
    $shift_id = intval($_GPC['shift_id']);
    $start_station_id = intval($_GPC['start_station_id']);
    $end_station_id = intval($_GPC['end_station_id']);
    $startDate = trim($_GPC['startDate']);
    if (empty($shift_id) || empty($start_station_id) || empty($end_station_id)) {
        message("参数有误！", "", "error");
    }
    $shift_info = getShiftInfo($start_station_id, $end_station_id, $shift_id);
    $departure_time = GetMkTime($startDate . " ". $shift_info['time']);
    $shift_info['surplus_votes'] = getSurplusVotes($shift_info, $departure_time);
    $shift_info['seat_number'] = getSeatNumber($shift_info, $departure_time);
    
    if (empty($shift_info)) {
        message("该班次不存在或已删除！", "", "error");
    }
    if ($departure_time < TIMESTAMP) {
        message("该班次已发车！", "", "error");
    }
    if ($shift_info['surplus_votes'] <= 0) {
        message("该班次车票已售完！", "", "error");
    }
    message($shift_info, "", "success");
}

elseif ($act == 'query') {
    $hot_route = getRecommendRoute();
    $end_where = $start_where = "";
    if (intval($_GPC['start_station_id']) > 0 && intval($_GPC['end_station_id']) > 0) {
        $start_station = getDataById("station", $_GPC['start_station_id']);
        $end_station = getDataById("station", $_GPC['end_station_id']);
        $start_where .= " AND id = '{$_GPC['start_station_id']}'";
        $end_where .= " AND id = '{$_GPC['end_station_id']}'";
        $_GPC['startCityName'] = getCol("area", "name", " AND id = '{$start_station['city_id']}'");
        $_GPC['endCityName'] = getCol("area", "name", " AND id = '{$end_station['city_id']}'");
    }
    
    $start_area = getAreaByName($_GPC['startCityName']);
    if (empty($start_area)) {
        message("出发城市不存在！", __WURL('tickets', array('act' => 'index')), "error");
    }
    $end_area = getAreaByName($_GPC['endCityName']);
    if (empty($end_area)) {
        message("到达城市不存在！", __WURL('tickets', array('act' => 'index')), "error");
    }
    if ($start_area['lv'] == 2) {
        $start_where .= " AND city_id = '{$start_area['id']}'";
    }elseif($start_area['lv'] == 3) {
        $start_where .= " AND district_id = '{$start_area['id']}'";
    }else{
        $start_where .= " AND province_id = '{$start_area['id']}'";
    }

    if ($end_area['lv'] == 2) {
        $end_where .= " AND city_id = '{$end_area['id']}'";
    }elseif($start_area['lv'] == 3) {
        $end_where .= " AND district_id = '{$end_area['id']}'";
    }else{
        $end_where .= " AND province_id = '{$end_area['id']}'";
    }
    
    $start_station = getAllData("station", $start_where);
    if (empty($start_station)) {
        message("出发城市没有开通线上售票功能", "", "error");
    }
    $end_station = getAllData("station", $end_where);
    if (empty($end_station)) {
        message("到达城市没有开通线上售票功能", "", "error");
    }
    if ($start_date == date("Y-m-d")) {
        //$day_end_time = GetMkTime(date("Y-m-d 23:59:59", TIMESTAMP));
        $date_where = " AND s.time >= '" . timeToStr(TIMESTAMP, "H:i:00") . "'";
    }else{
        $date_where = "";
    }
    $result2 = $result = array();
    foreach ($start_station as $start_item) {
        foreach ($end_station as $end_item) {
            $shift_list = getShiftList($start_item['id'], $end_item['id'], $date_where);
            $result2 = array_merge($result2, $shift_list);
        }    
    }
    foreach ($result2 as $key => $item) {
        $departure_time = GetMkTime($start_date . " ". $item['time']);
        $vehicle = getShiftVehicle($item['shift_id'], $start_date);
        $result2[$key]['surplus_votes'] = getSurplusVotes($item, $departure_time);
        if (!empty($vehicle)) {
            $result2[$key]['vehicle'] = $vehicle;
        }else{
            unset($result2[$key]);
        }
    }
    $page_size = 10;
    $k = 0;
    $dataCount = count($result2);
    $pageCount = ceil($dataCount / $page_size);
    for($i = 0; $i < $pageCount; $i++) {
        $result[$i] = array_slice($result2, $i * $page_size, $page_size);
    }
}

elseif ($act == 'endCitys') {
    $area = getAllData("area", " AND lv != 1", " lv asc, sort asc");
    $d = array();
    foreach($area AS $item) {
       $d[] = array(
           "id" => $item['id'],
           "name" => $item['name'],
           "orderby" => $item['sort'],
           "pinyin" => $item['pinyin'],
           "simplepy" => $item['py'],
           "provinceName" => getTopAreaName($item['pid']),
           "typeNo" => 0
       ); 
    }
    exit(json_encode($d));
}

elseif ($act == 'select_seat') {
    $shift_id = intval($_GPC['shift_id']);
    $start_station_id = intval($_GPC['start_station_id']);
    $end_station_id = intval($_GPC['end_station_id']);
    $startDate = trim($_GPC['startDate']);
    
    $shift = getShiftInfo($start_station_id, $end_station_id, $shift_id);
    $departure_time = GetMkTime($startDate . " ". $shift['time']);
    $shift['surplus_votes'] = getSurplusVotes($shift, $departure_time);
    $seatNumber = getSeatNumber($shift, $departure_time);
    $seatNumberList = getSeatNumberList($shift, $departure_time);
    include $this->template("web/select_seat");
    exit();
}
$area = getAllData("area", " AND lv != 1", " lv asc, sort asc");
$d = array();
foreach($area AS $item) {
   $d[] = array(
       "id" => $item['id'],
       "name" => $item['name'],
       "orderby" => $item['sort'],
       "pinyin" => $item['pinyin'],
       "simplepy" => $item['py'],
       "provinceName" => getTopAreaName($item['pid']),
       "typeNo" => 0
   ); 
}
include $this->template("web/tickets_" . $act);