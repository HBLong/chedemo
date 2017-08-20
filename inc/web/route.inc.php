<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'route';
$table_name = 'route';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'add', 'update', 'delete'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
$area_id = intval($_GPC['area_id']);
/**
 * 列表
 */
if ($act == 'lists') {
    $result = getPageList($table_name, $_GPC['page']);
    if (!empty($result['list'])) {
        foreach($result['list'] as &$item) {
            $start_station = getDataById('station', $item['start_station_id']);
//            $start_station_area = getDataById('area', $start_station['area_id'], 'name');
            $end_station = getDataById('station', $item['end_station_id']);
//            $end_station_area = getDataById('area', $end_station['area_id'], 'name');
            $item['start_station_name'] = $start_station['name'];
            $item['end_station_name'] = $end_station['name'];
        }
    }
}
/**
 * 添加修改
 */
else if($act == 'add') {
    $id = intval($_GPC['id']);
    $item = array();
    $station_all = getAllData('station', '', 'sort ASC');
//    dump($station_all);die;
    $province = getAllData('area', " AND pid = 0", 'sort ASC');
    if (!empty($id)) {
        $item = getDataById($table_name, $id);
    }
}
/**
 * 保存数据
 */
else if($act == 'update') {
    if (!checksubmit('submit')) {
        message('Token错误!', '', 'error');
    }
    $id = intval($_GPC['id']);
    $postdata = $_GPC['postdata'];
    if(empty($postdata['name'])){
        message('请填写线路名称!', '', 'error');
    }
    if(empty($postdata['start_station_id'])){
        message('请选择始发站!', '', 'error');
    }
    if(empty($postdata['start_station_id'])){
        message('请选择终点站!', '', 'error');
    }
    if ($postdata['start_station_id'] == $postdata['end_station_id']) {
        message('始发站不能和终点站相同!', '', 'error');
    }
    $start_station = getDataById('station', $postdata['start_station_id']);
    $end_station = getDataById('station', $postdata['end_station_id']);
    if (empty($start_station)) {
        message('始发站不存在!', '', 'error');
    }
    if (empty($end_station)) {
        message('始发站不存在!', '', 'error');
    }
    $result = getAllData($table_name, " AND name = '{$postdata['name']}' AND id != '{$id}'");
    if(!empty($result)){
        message('该路线名称已存在!', '', 'error');
    }
    $result = getAllData($table_name, " AND start_station_id = '{$postdata['start_station_id']}' AND end_station_id = '{$postdata['end_station_id']}' AND id != '{$id}'");
    if(!empty($result)){
        message('该路线已存在!', '', 'error');
    }
    $postdata['uniacid'] = $uniacid;
    if (empty($id)) {
        fr_insert($table_name, $postdata);
        $action = "add";
    }else{
        fr_update($table_name, $postdata, array('id' => $id, 'uniacid' => $uniacid));
        $action = "edit";
    }
    //保存操作日志
    action_log($postdata['name'], $action, $table_name);
    message('保存成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = intval($_GPC['id']);
    $data = getDataById("route", $id);
    if (empty($data)) {
        message('数据不存在或已删除!', __WURL($fr_model, array('act' => 'lists')), 'error');
    }
    deleteRoute($id);
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}

include $this->template("web/{$fr_model}_" . $act);