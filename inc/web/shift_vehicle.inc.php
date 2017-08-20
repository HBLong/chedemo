<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'shift_vehicle';
$table_name = 'shift_vehicle';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'delete', 'add', 'update'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
$shift_id = intval($_GPC['shift_id']);
$shift_info = getDataById("shift", $shift_id);
if (empty($shift_info)) {
    message("班次不存在或已删除");
}
$shift_info['route'] = getRouteData($shift_info['route_id']);
$shift_info['time'] = timeToStr(GetMkTime("1990-12-19 " . $shift_info['time']), "H:i");
/**
 * 列表
 */
if ($act == 'lists') {
    $where = " AND shift_id = '{$shift_id}'";
    $result = getPageList($table_name, $_GPC['page'], $where, ' work_date DESC');
    if (!empty($result['list'])) {
        foreach($result['list'] as &$item) {
            $item['vehicle'] = getDataById("vehicle", $item['vehicle_id']);
            //$item['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
        }
    }
}

elseif ($act == "add") {
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $item = getDataById($table_name, $id);
    }
    
    $vehicle_all = getAllData('vehicle', " AND station_id = '{$shift_info['route']['start_station_id']}'");
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
    if(empty($postdata['work_date'])){
        message('排班日期不能为空!', '', 'error');
    }
    $postdata['uniacid'] = $uniacid;
    if (empty($id)) {
        fr_insert($table_name, $postdata);
        $id = pdo_insertid();
        $action = "add";
    }else{
        fr_update($table_name, $postdata, array('id' => $id));
        $action = "edit";
    }
    //保存操作日志
    action_log("{$shift_info['route']['name']} {$shift_info['time']} 班次司机, $id", $action, $table_name);
    message('保存成功!', __WURL($fr_model, array('act' => 'lists', 'shift_id' => $shift_id)), 'success');
    exit();
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = intval($_GPC['id']);
    if (!fr_delete($table_name, array("id" => $id))) {
        message('数据不存在或已删除!', __WURL($fr_model, array('act' => 'lists')), 'error');
    }
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists', 'shift_id' => $shift_id)), 'success');
    //保存操作日志
    action_log("{$shift_info['route']['name']} {$shift_info['time']} 班次司机, $id", "delete", $table_name);
    exit();
}
include $this->template("web/shift_vehicle_" . $act);