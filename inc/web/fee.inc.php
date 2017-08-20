<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'fee';
$table_name = 'fee';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'delete', 'add', 'update'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
/**
 * 列表
 */
if ($act == 'lists') {
    $result = getPageList($table_name, $_GPC['page']);
}

elseif ($act == "add") {
    $id = intval($_GPC['id']);
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
    $postdata['time'] = empty($postdata['time']) ? 0 : intval($postdata['time']);
    $postdata['fee'] = empty($postdata['fee']) ? '' : floatval($postdata['fee']);
    if($postdata['fee'] == ''){
        message('请设置手续费字段!', '', 'error');
    }
    $postdata['uniacid'] = $uniacid;
    if (empty($id)) {
        fr_insert($table_name, $postdata);
        $id = pdo_insertid();
        $action = "add";
    }else{
        fr_update($table_name, $postdata, array('id' => $id, 'uniacid' => $uniacid));
        $action = "edit";
    }
    //保存操作日志
    action_log($id, $action, $table_name);
    message('保存成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
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
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    //保存操作日志
    action_log($id, "delete", $table_name);
    exit();
}

include $this->template("web/fee_" . $act);