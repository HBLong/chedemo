<?php
 
defined('IN_IA') or exit('Access Denied');
include MODULE_ROOT . '/inc/common.php';
include MODULE_ROOT . '/inc/mobile/init.php';
$uniacid = $_W["uniacid"];
$openid = $_W['openid'];
$uid = $_W['member']['uid'];
$title = "购票查询";
$act = trim($_GPC['act']);
if ($act == 'clear' && $_W['ispost'] && $_W['isajax']) {
    cleanSearchHistoryByUid($uid);
    die();
}
cleanParentOrder();
$days = getDays($this->module['config']['day']);
$area_all = getAllData('area', ' AND lv = 2', 'sort ASC, pid ASC');
foreach ($area_all AS $key => $area) {
    $station = getAllData('station', " AND city_id = '{$area['id']}'", 'sort ASC');
    if (!empty($station)) {
        $area_all[$key]['station'] = $station;
    }else{
        unset($area_all[$key]);
    }
}
$start_end_date = timeToStr(TIMESTAMP, 'Y-m-j') . " 至 " . timeToStr(strtotime("+{$fr_cp_settings['day']} day"), 'Y-m-j');


$index_route = array();
$sql = "SELECT * FROM " . fr_table('history') . " WHERE uniacid = '{$uniacid}' AND uid = '{$uid}' ORDER BY createtime DESC LIMIT 0,10";
$history_res = pdo_fetchall($sql);
foreach($history_res AS $val) {
    if (!isset($index_route[$val['start_station_id'] . "-" . $val['end_station_id']])) {
        $index_route[$val['start_station_id'] . "-" . $val['end_station_id']] = array(
            "start_station" => getDataById('station', $val['start_station_id']),
            "end_station" => getDataById('station', $val['end_station_id']),
        );
    }
}
$index_route = getRecommendRoute($index_route);
//dump($index_route);die;
include $this->template('index');