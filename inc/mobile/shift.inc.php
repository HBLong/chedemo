<?php
 
include MODULE_ROOT . '/inc/common.php';
include MODULE_ROOT . '/inc/mobile/init.php';
$uniacid = $_W["uniacid"];
$openid = $_W['openid'];
$uid = $_W['member']['uid'];
cleanParentOrder();
$title = "班次列表";
$where = '';
$start_date = timeToStr(TIMESTAMP, 'Y-m-d');
$end_date = timeToStr(strtotime("+{$fr_cp_settings['day']} day"), 'Y-m-d');
if ($_GPC['date'] == 'all') {
    $_GPC['date'] = timeToStr(TIMESTAMP, 'Y-m-d');
}
$datetime = GetMkTime($_GPC['date']);
$date = timeToStr($datetime, 'Y-m-d');
if ($date < $start_date) {
    message("当前只能预订 {$start_date} 至 {$end_date} 的班次，请重新选择日期查询。！", '', 'error');
}
if ($date == $start_date) {
    $now_time = timeToStr(TIMESTAMP, 'H:i:00');
    $where .= " AND s.time > '{$now_time}'";
}
$is_all = false;

$zuotian = timeToStr($datetime - 24 * 60 * 60, 'Y-m-d');
$mingtian = timeToStr($datetime + 24 * 60 * 60, 'Y-m-d');
if ($zuotian >= $start_date) {
    $zuotian_url = __MURL('shift', array('date' => $zuotian, 'start_station_id' => $_GPC['start_station_id'], 'end_station_id' => $_GPC['end_station_id']));
    $is_zuotian = TRUE;
}else{
    $is_zuotian = FALSE;
}
if ($mingtian <= $end_date) {
    $mingtian_url = __MURL('shift', array('date' => $mingtian, 'start_station_id' => $_GPC['start_station_id'], 'end_station_id' => $_GPC['end_station_id']));
    $is_mingtian = TRUE;
}else{
    $is_mingtian = FALSE;
}
$start_station_id = intval($_GPC['start_station_id']);
$end_station_id = intval($_GPC['end_station_id']);
if (empty($start_station_id) || empty($end_station_id)) {
    message('请选择乘车站和目的站！', '', 'error');
}
$start_station = getDataById('station', $start_station_id);
$end_station = getDataById('station', $end_station_id);
if (empty($start_station) || empty($end_station)) {
    message('请选择正确的乘车站和目的站！', '', 'error');
}else {
    $result = getShiftList($start_station_id, $end_station_id, $where);
    foreach ($result as $key => $row) {
        $result[$key]['time'] = GetMkTime($date . " " . $row['time']);
        $result[$key]['surplus_votes'] = getSurplusVotes($row, GetMkTime($date . " " . $row['time']));
        $vehicle = getShiftVehicle($row['shift_id'], $date);
        if (!empty($vehicle)) {
            $result[$key]['vehicle'] = $vehicle;
        }else{
            unset($result[$key]);
        }
    }
    //保存搜索记录
    insertSearchHistory($uid, $start_station_id, $end_station_id);
    //dump($result);die;
}

include $this->template("shift");