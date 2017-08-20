<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'sms_log';
$table_name = 'sms_log';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'delete'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
/**
 * 列表
 */
if ($act == 'lists') {
    $status = in_array($_GPC['status'], array('0','1','-1', '2')) ? $_GPC['status'] : NULL;
    $where = '';
    if (!is_null($status)) {
        $where .= " AND status = '{$status}'";
    }
    $result = getPageList($table_name, $_GPC['page'], $where);
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = trim($_GPC['id']);
    if ($id == 'all') {
        fr_delete($table_name, array('uniacid' => $uniacid));
        $action = 'batch_delete';
    }else{
        fr_delete($table_name, array('id' => intval($id), 'uniacid' => $uniacid));
        $action = 'delete';
    }
    //保存操作日志
    action_log("", $action, $table_name);
    message('删除数据成功!', __WURL('sms', array('act' => 'lists')), 'success');
    exit();
}
include $this->template("web/sms_log");