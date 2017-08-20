<?php
// +----------------------------------------------------------------------
// | Author: 随便撸源码论坛 <>
// +----------------------------------------------------------------------
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'vehicle';
$table_name = 'vehicle';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'add', 'update', 'delete'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
/**
 * 列表
 */
if ($act == 'lists') {
    $where = '';
    if ($_GPC['station_id'] > 0) {
        $where .= " AND station_id = '{$_GPC['station_id']}'";
    }
    if ($_GPC['license_plate'] != '') {
        $where .= " AND license_plate LIKE '%{$_GPC['license_plate']}%'";
    }
    if ($_GPC['driver_name'] != '') {
        $where .= " AND driver_name LIKE '%{$_GPC['driver_name']}%'";
    }
    $station = getAllData('station');
    $result = getPageList($table_name, $_GPC['page'], $where, ' id DESC');
}
/**
 * 添加修改
 */
else if($act == 'add') {
    $id = intval($_GPC['id']);
    $item = array(
        'seat_numbers' => 0,
    );
    $station = getAllData('station');
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
    $station = getDataById('station', $postdata['station_id']);
    if (empty($station)) {
        message('所属车站不存在!', '', 'error');
    }
    if ($postdata['license_plate'] == '') {
        message('请填写车牌号码', '', 'error');
    }
    if ($postdata['driver_name'] == '') {
        message('请填写司机姓名', '', 'error');
    }
    if ($postdata['driver_phone'] == '') {
        message('请填写司机电话', '', 'error');
    }
    if (intval($postdata['seat_numbers']) <= 0) {
        message('请填写司机电话', '', 'error');
    }
    $postdata['seat_numbers'] = intval($postdata['seat_numbers']);
    $postdata['uniacid'] = $uniacid;
    if (empty($id)) {
        fr_insert($table_name, $postdata);
        $action = 'add';
    }else{
        fr_update($table_name, $postdata, array('id' => $id, 'uniacid' => $uniacid));
        $action = 'edit';
    }
    action_log($postdata['name'], $action, $table_name);
    $params = array(
        'act' => 'lists'
    );
    message('保存成功!', __WURL($fr_model, $params), 'success');
    exit();
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = intval($_GPC['id']);
    $data = getDataById($table_name, $id);
    if (empty($data)) {
        message('数据不存在或已删除!', __WURL($fr_model, array('act' => 'lists')), 'error');
    }
    fr_delete($table_name, array('id' => $id));
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}

include $this->template("web/{$fr_model}_" . $act);