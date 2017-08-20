<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'writeoff';
$table_name = 'writeoff';
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
    $route_all = getAllData('route');
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
    $postdata['route_id'] = $postdata['route_id'] > 0 ? intval($postdata['route_id']) : 0;
    if(empty($postdata['name'])){
        message('请填写核销人员的姓名!', '', 'error');
    }
    if(empty($postdata['openid'])){
        message('请填写核销人员的OPENID!', '', 'error');
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

include $this->template("web/writeoff_" . $act);