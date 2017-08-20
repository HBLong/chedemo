<?php

 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];

//$total_tickets_votes = getCol("shift", "sum(total_votes)"); //总票数
$total_sell_tickets = getCol("order", "sum(number)", " AND status IN(1,3)"); //总售票数
//$total_surplus_votes = $total_tickets_votes - $total_sell_tickets;
$total_price = getCol("order", "sum(`price` + `change_cost`)", " AND status IN(1,2,3)"); //总营业额 
$scroll = intval($_GPC['scroll']);
$year = intval($_GPC['year']);
if ($year > 2015) {
    $starttime = GetMkTime("{$year}-01-01 00:00:00");
    $endtime = GetMkTime("{$year}-12-31 23:59:59");
    $title_date = "{$year}年";
}else{
    $starttime = $_GPC['datelimit']['start'] ? GetMkTime($_GPC['datelimit']['start'] . " 00:00:00") : strtotime('-7day');
    $endtime = $_GPC['datelimit']['end'] ? GetMkTime($_GPC['datelimit']['end'] . " 23:59:59") : TIMESTAMP;
    $title_date = getDateStr($starttime) . "至". getDateStr($endtime);
}
$act = trim($_GPC['act']);
$allow_acts = array(
    'ticket', 'price', 'car'
);
$years = getAllData("order", '', '', " DISTINCT FROM_UNIXTIME(departure_time,'%Y') AS years ", "years");
$years = array_keys($years);
$total = 0;
if (!in_array($act, $allow_acts)) {
    $act = 'ticket';
}
/**
 * 按票统计
 */
if ($act == 'ticket') {
    $labels = $series = $datasets = array();
    $stat = getAllData("order", " AND  createtime >= '$starttime' AND createtime <= '$endtime'", " createtime ASC");
//    dump($stat);die;
    $legend = array('1' => "成功", '3' => "改签", '0' => "未付款", '2' => "退票", '-1' => "取消");
    if ($year > 1970) {
        for($i = 1; $i <= 12; $i++) {
            $labels[] = $i."月";
            foreach ($legend as $key => $value) {
                $datasets[$key][$i."月"] = 0;
            }
        }
    }else{
        for ($i = $starttime; $i <= $endtime; $i+=86400) {
            $day = timeToStr($i, 'm-d');
            $labels[] = $day;
            foreach ($legend as $key => $value) {
                $datasets[$key][$day] = 0;
            }
        }
    }
    for ($i = 0; $i < count($stat); $i++) { 
        if ($year > 1970) {
            $day = timeToStr($stat[$i]['createtime'], 'n月');
        }else{
            $day = timeToStr($stat[$i]['createtime'], 'm-d');
        }
        if (in_array($stat[$i]['status'], array(1, 3))) {
            $total += $stat[$i]['number'];
        }
        $datasets[$stat[$i]['status']][$day] = intval($datasets[$stat[$i]['status']][$day]) + $stat[$i]['number'];
    }
    foreach ($datasets as $key => $value) {
        $series[$key] = array(
            "name" => $legend[$key],
            "type" => "line",
            "data" => array_values($value),
        );
    }
    $labels = array_unique($labels);
    $legend = array_values($legend);
    $series = array_values($series);
    $title = $title_date . " 售票数统计";
    $subtitle = $title_date . " 销售成功总票数：{$total}";
}
/**
 * 按营业额统计
 */
elseif ($act == 'price') {
    $labels = $series = $datasets = array();
    $stat = getAllData("order", " AND  createtime >= '$starttime' AND createtime <= '$endtime' AND status IN(1,2,3)", " createtime ASC");
//    dump($stat);die;
    if ($year > 1970) {
        for($i = 1; $i <= 12; $i++) {
            $day = $i."月";
            $labels[] = $day;
            $datasets[$day] = 0;
        }
    }else{
        for ($i = $starttime; $i <= $endtime; $i+=86400) {
            $day = timeToStr($i, 'm-d');
            $labels[] = $day;
            $datasets[$day] = 0;
        }
    }
    for ($i = 0; $i < count($stat); $i++) { 
        if ($year > 1970) {
            $day = timeToStr($stat[$i]['createtime'], 'n月');
        }else{
            $day = timeToStr($stat[$i]['createtime'], 'm-d');
        }
        $total += $stat[$i]['price'] + $stat[$i]['change_cost'];
        $datasets[$day] = intval($datasets[$day]) + $stat[$i]['price'] + $stat[$i]['change_cost'];
    }
    
    $series = array(
        "name" => "营业额",
        "type" => "line",
        "data" => array_values($datasets),
    );
    $labels = array_unique($labels);
    //$legend = array_values($legend);
    //$series = array_values($series);
    $title = $title_date . "营业额统计";
    $subtitle = $title_date . " 销售营业总额：￥{$total}元";
}

/**
 * 按班车统计
 */
elseif ($act == 'car') {
    $vehicle = getAllData("vehicle");
    $legend = $shift_ids = $labels = $series = $datasets = array();
    
    foreach ($vehicle as $car) {
        $legend[$car['id']] = "车牌：". $car['license_plate'];
//        $shift_id = getAllData("shift", " AND vehicle_id = '{$car['id']}'", "", "id", "id");
//        $shift_ids[$car['id']] = array_keys($shift_id);
    }
    
    $stat = getAllData("order", " AND  createtime >= '$starttime' AND createtime <= '$endtime' AND status IN(1,3)", " createtime ASC");
//    dump($stat);die;
    if ($year > 1970) {
        for($i = 1; $i <= 12; $i++) {
            $labels[] = $i."月";
            foreach ($legend as $key => $value) {
                $datasets[$key][$i."月"] = 0;
            }
        }
    }else{
        for ($i = $starttime; $i <= $endtime; $i+=86400) {
            $day = timeToStr($i, 'm-d');
            $labels[] = $day;
            foreach ($legend as $key => $value) {
                $datasets[$key][$day] = 0;
            }
        }
    }
    for ($i = 0; $i < count($stat); $i++) { 
        if ($year > 1970) {
            $day = timeToStr($stat[$i]['createtime'], 'n月');
        }else{
            $day = timeToStr($stat[$i]['createtime'], 'm-d');
        }
        foreach ($legend as $key => $value) {
            if ($stat[$i]['vehicle_id'] == $key) {
                $datasets[$key][$day] = intval($datasets[$key][$day]) + $stat[$i]['number'];
                break;
            }
        }
        $total += $stat[$i]['number'];
    }
    foreach ($datasets as $key => $value) {
        $series[$key] = array(
            "name" => $legend[$key],
            "type" => "line",
            "data" => array_values($value),
        );
    }
    $labels = array_unique($labels);
    $legend = array_values($legend);
    $series = array_values($series);
    $title = $title_date . " 班车载客数统计";
    $subtitle = $title_date . " 班车载客总数：{$total}";
}
include $this->template("web/stats");
