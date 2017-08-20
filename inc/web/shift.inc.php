<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'shift';
$table_name = 'shift';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'add', 'update', 'delete', 'setStatus'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}


/**
 * 列表
 */
if ($act == 'lists') {
    $route_id = intval($_GPC['route_id']);
    $where = '';
    if (!empty($route_id)) {
        $route = getRouteData($route_id);
        if (empty($route)) {
            message('路线不存在或已删除!', '', 'error');
        }
        $where = " AND route_id = '{$route_id}'";
    }
    $result = getPageList($table_name, $_GPC['page'], $where);
    if (!empty($result['list'])) {
        foreach($result['list'] as &$item) {
            $item['route'] = getRouteData($item['route_id']);
            $item['sold_votes'] = $item['total_votes'] - $item['surplus_votes'];
            $item['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
        }
    }
//    dump($result['list']);die;
    $route_all = getAllData('route');
}
/**
 * 添加修改
 */
else if($act == 'add') {
    load()->func('tpl');
    $id = intval($_GPC['id']);
    $route_id = intval($_GPC['route_id']);
    if (empty($route_id)) {
        $route_id = getCol("route", "id");
    }
    $item = array(
        "refund_ticket" => 1,
        "enable_vouchers" => 1,
        "status" => 1,
        "total_votes" => 50,
    );
    $shift_end_index = $index = 1;
    if (!empty($id)) {
        $item = getDataById($table_name, $id);
        if (!empty($item)) {
            $item['datetime'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
            $route_id = $item['route_id'];
            $item['shift_station'] = getAllData('shift_station', " AND shift_id = '{$item['id']}'", "id ASC");
            $item['shift_end_station'] = getAllData('shift_end_station', " AND shift_id = '{$item['id']}'", "id ASC");
            if (!empty($item['shift_station'])) {
                foreach ($item['shift_station'] as $key => $value) {
                    $item['shift_station'][$key]['datetime'] = timeToStr(GetMkTime("1990-12-19 " . $value['departure_time']), "H:i");
                }
                $index = $item['shift_station'][count($item['shift_station']) - 1]['id'] + 1;
                $shift_end_index = $item['shift_end_station'][count($item['shift_end_station']) - 1]['id'] + 1;
            }
        }
    }
    $province = getAllData('area', " AND pid = 0", 'sort ASC');
    $station_all = getAllData('station', '', 'sort ASC');
    $route = getRouteData($route_id);
    $vehicle_all = getAllData('vehicle', " AND station_id = '{$route['start_station_id']}'");
    $time_all = getAllData('shift_time', '', 'time ASC');
    if (!empty($time_all)) {
        foreach($time_all as $key=>$tm) {
            $time_all[$key]['time'] = timeToStr(GetMkTime("1990-12-19 " . $tm['time']), "H:i");
        }
    }
//    dump($item);die;
//    dump($vehicle_all);die;
    if (empty($route)) {
        message('路线不存在或已删除!', '', 'error');
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
    $shift_station_data = $_GPC['shift_station'];
    $delete_shift_station = $_GPC['delete_shift_station'];
    $shift_end_station_data = $_GPC['shift_end_station'];
    $delete_shift_end_station = $_GPC['delete_shift_end_station'];

    var_dump([
            'postdata' => $postdata,
            'shift_station_data' => $shift_station_data,
            'delete_shift_station' => $delete_shift_station,
            'shift_end_station_data' => $shift_end_station_data,
            'delete_shift_end_station' => $delete_shift_end_station,
        ]);
//    dump($_POST);die;
//    dump($postdata);die;
//    dump($shift_station_data);die;
    $route = getDataById('route', $postdata['route_id']);
    $postdata['time'] = timeToStr(GetMkTime("1990-12-19 " . $postdata['datetime'] . ":00"), "H:i");
    $postdata['date'] = '';//date("Y-m-d", $postdata['time']);
    $postdata['total_votes'] = intval($postdata['total_votes']);
    $postdata['ticket_price'] = floatval($postdata['ticket_price']);
    if (empty($route)) {
        message('该路线不存在!', '', 'error');
    }
    if (empty($postdata['datetime'])) {
        message('请选择发车时间!', '', 'error');
    }
    if (empty($postdata['total_votes'])) {
        message('总票数必须大于0!', '', 'error');
    }
    if (empty($postdata['ticket_price'])) {
        message('票价必须大于0!', '', 'error');
    }
    $postdata['surplus_votes'] = isset($postdata['surplus_votes'])? intval($postdata['surplus_votes']) : $postdata['total_votes'];
    $postdata['uniacid'] = $uniacid;
    if (empty($id)) {
        fr_insert($table_name, $postdata);
        $id = pdo_insertid();
        $action = "add";
    }else{
//        unset($postdata['total_votes']);
//        unset($postdata['surplus_votes']);
        fr_update($table_name, $postdata, array('id' => $id, 'uniacid' => $uniacid));
        $action = "edit";
    }
    //配客站 start
    $shift_stationid = array();
    if (!empty($shift_station_data)) {
        foreach ($shift_station_data['station'] as $key => $station_id) {
            $insert_data = array();
            if ($route['start_station_id'] == $station_id || $route['end_station_id'] == $station_id) {
                continue;
            }
            $station = getDataById('station', $station_id);
            if (empty($station)) {
                fr_delete("shift_station", array("shift_id" => $id, 'uniacid' => $uniacid, "station_id" => $station_id));
                continue;
            }
            $insert_data['uniacid'] = $uniacid;
            $insert_data['shift_id'] = $id;
            $insert_data['station_id'] = $station_id;
            $insert_data['departure_time'] = empty($shift_station_data['time'][$key]) ? $postdata['time'] : timeToStr(GetMkTime("1990-12-19 " . $shift_station_data['time'][$key] . ":00"), "H:i");
            $insert_data['ticket_price'] = floatval($shift_station_data['ticket_price'][$key]) > 0 ? floatval($shift_station_data['ticket_price'][$key]) : $postdata['ticket_price'];
            $insert_data['recommend'] = empty($shift_station_data['recommend'][$key]) ? 0 : 1;
            $sql = "SELECT * FROM ". fr_table('shift_station') . " WHERE (id = :id OR station_id = :station_id) AND uniacid = :uniacid AND shift_id = :shift_id";
            $params = array(":id" => $key, ":uniacid" => $uniacid, ":shift_id" => $id, ":station_id" => $station_id);
            $shift_station = pdo_fetch($sql, $params);
            if (!empty($shift_station)) {
                fr_update('shift_station', $insert_data, array('id' => $shift_station['id'], 'uniacid' => $uniacid, "shift_id" => $id));
            }else{
                fr_insert('shift_station', $insert_data);
            }
            $shift_stationid[] = $station_id;
        }
    }else{
        fr_delete("shift_station", array("shift_id" => $id, 'uniacid' => $uniacid));
    }
    if ($delete_shift_station) {
        fr_delete("shift_station", array("shift_id" => $id, 'uniacid' => $uniacid, 'id' => explode(",", $delete_shift_station)));
    }
    //配客站 end
    //下客站 start
    if (!empty($shift_end_station_data)) {
        foreach ($shift_end_station_data['station'] as $key => $station_id) {
            $insert_data = array();
            if ($route['start_station_id'] == $station_id || $route['end_station_id'] == $station_id || in_array($station_id, $shift_stationid)) {
                continue;
            }
            if (intval($station_id) > 0){
                $station = getDataById('station', $station_id);
                if (empty($station)) {
                    fr_delete("shift_end_station", array("shift_id" => $id, 'uniacid' => $uniacid, "end_station_id" => $station_id));
                    continue;
                }
            }
            $insert_data['uniacid'] = $uniacid;
            $insert_data['shift_id'] = $id;
            $insert_data['end_station_id'] = $station_id;
            $insert_data['recommend'] = empty($shift_end_station_data['recommend'][$key]) ? 0 : 1;
            
            $sql = "SELECT * FROM ". fr_table('shift_end_station') . " WHERE (id = :id OR end_station_id = :end_station_id) AND uniacid = :uniacid AND shift_id = :shift_id";
            $params = array(":id" => $key, ":uniacid" => $uniacid, ":shift_id" => $id, ":end_station_id" => $station_id);
            $shift_end_station = pdo_fetch($sql, $params);
            if (!empty($shift_end_station)) {
                fr_update('shift_end_station', $insert_data, array('id' => $shift_end_station['id'], 'uniacid' => $uniacid, "shift_id" => $id));
            }else{
                fr_insert('shift_end_station', $insert_data);
            }
        }
    }else{
        fr_delete("shift_end_station", array("shift_id" => $id, 'uniacid' => $uniacid));
    }
    if ($delete_shift_end_station) {
        fr_delete("shift_end_station", array("shift_id" => $id, 'uniacid' => $uniacid, 'id' => explode(",", $delete_shift_end_station)));
    }
    //下客站 end
    
    //保存操作日志
    action_log($route['name']  . "下的{$postdata['datetime']}班次信息", $action, $table_name);
    message('保存成功!', __WURL($fr_model, array('act' => 'lists', 'route_id' => $route['id'])), 'success');
    exit();
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = intval($_GPC['id']);
    $shift = getDataById($table_name, $id);
    if (empty($shift)) {
        message("该班次不存在或已删除");
    }
    deleteShift($id);
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists', 'route_id' => $shift['route_id'])), 'success');
    exit();
}
/**
 * 改变状态
 */
elseif ($act == "setStatus") {
    $id = intval($_GPC['id']);
    $info = getDataById($table_name, $id);
    if (!empty($info)) {
        $status = intval($info['status']) > 0 ? 0 : 1;
        $rs = fr_update($table_name, array('status' => $status), array('id' => $id));
        if ($rs !== false) {
            message($status, '', 'success');
        }else{
            message("操作失败！", '', 'error');
        }
    }else{
        message("数据不存在或已删除！", '', 'error');
    }
    exit;
}

include $this->template("web/{$fr_model}_" . $act);