<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'station';
$table_name = 'station';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'add', 'update', 'delete', 'getarea', 'getstation'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
/**
 * 列表
 */
if ($act == 'lists') {
    $where = '';
    if ($_GPC['province_id'] > 0) {
        $where .= " AND province_id = '{$_GPC['province_id']}'";
    }
    if ($_GPC['city_id'] > 0) {
        $where .= " AND city_id = '{$_GPC['city_id']}'";
    }
    if ($_GPC['district_id'] > 0) {
        $where .= " AND district_id = '{$_GPC['district_id']}'";
    }
    
    $province = getAllData('area', " AND pid = 0", 'sort ASC');
    $result = getPageList($table_name, $_GPC['page'], $where, ' city_id ASC, sort ASC, id DESC');
}
/**
 * 添加修改
 */
else if($act == 'add') {
    $id = intval($_GPC['id']);
    $item = array(
//        'area_id' => $area_id,
        'sort' => 1,
    );
    $province = getAllData('area', " AND pid = 0", 'sort ASC');
    if (!empty($id)) {
        $item = getDataById($table_name, $id);
    }
    if ($item['province_id'] > 0) {
        $city = getAllData('area', " AND pid = '{$item['province_id']}'", 'sort ASC');
    }
    if ($item['city_id'] > 0) {
        $district = getAllData('area', " AND pid = '{$item['city_id']}'", 'sort ASC');
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
    if ($postdata['name'] == '') {
        message('车站名称不能为空', '', 'error');
    }
    $province = getDataById('area', $postdata['province_id']);
    if (empty($province)) {
        message('请选择车站所在地区!', '', 'error');
    }
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
        'act' => 'lists', 
        'province_id' => $postdata['province_id'],
        'city_id' => $postdata['city_id'],
        'district_id' => $postdata['district_id'],
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
    if (!deleteStation($station_id)) {
        message('数据不存在或已删除!', __WURL($fr_model, array('act' => 'lists')), 'error');
    }
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}
else if ($act == 'getarea') {
    $pid = intval($_GPC['pid']);
    $city = getAllData('area', " AND pid = '{$pid}'", 'sort ASC');
    echo json_encode($city);die;
}
else if ($act == 'getstation') {
    $province_id = intval($_GPC['province_id']);
    $city_id = intval($_GPC['city_id']);
    $district_id = intval($_GPC['district_id']);
    $where = '';
    $where .= $province_id > 0 ? " AND province_id = '{$province_id}'" : "";
    $where .= $city_id > 0 ? " AND city_id = '{$city_id}'" : "";
    $where .= $district_id > 0 ? " AND district_id = '{$district_id}'" : "";
    $result = getAllData($table_name, $where, 'sort ASC');
    echo json_encode($result);die;
}
include $this->template("web/{$fr_model}_" . $act);