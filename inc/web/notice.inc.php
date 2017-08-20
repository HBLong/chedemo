<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'notice';
$table_name = 'notice';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'delete', 'add', 'update', 'setIsAdd', 'setIsPay'
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
    $message_all = getAllData('message');
    $item = array(
        "type" => 0
    );
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
    $postdata['type'] = $postdata['type'] == 1 ? 1 : 0;
    $postdata['route_id'] = $postdata['route_id'] > 0 ? intval($postdata['route_id']) : 0;
    if($postdata['type'] == 0 && empty($postdata['openid'])){
        message('请填写接收通知人的OPENID!', '', 'error');
    }
    if($postdata['type'] == 1){
        if(empty($postdata['mobile'])){
            message('请填写接收通知人的手机号码!', '', 'error');
        }
        if(!validMobile($postdata['mobile'])){
            message('请填写正确格式的手机号码!', '', 'error');
        }
    }
    if(empty($postdata['msg_id'])){
        message('请选择通知内容!', '', 'error');
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

/**
 * 改变状态
 */
elseif ($act == "setIsAdd") {
    $id = intval($_GPC['id']);
    $info = getDataById($table_name, $id);
    if (!empty($info)) {
        $status = intval($info['is_add']) > 0 ? 0 : 1;
        $rs = fr_update($table_name, array('is_add' => $status), array('id' => $id));
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
/**
 * 改变状态
 */
elseif ($act == "setIsPay") {
    $id = intval($_GPC['id']);
    $info = getDataById($table_name, $id);
    if (!empty($info)) {
        $status = intval($info['is_pay']) > 0 ? 0 : 1;
        $rs = fr_update($table_name, array('is_pay' => $status), array('id' => $id));
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

include $this->template("web/notice_" . $act);