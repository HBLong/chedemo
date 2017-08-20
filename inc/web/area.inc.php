<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'area';
$table_name = 'area';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'add', 'update', 'delete'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
$pid = intval($_GPC['pid']);
if ($pid > 0) {
    $p_item = getDataById($table_name, $pid);
    if (!empty($p_item)) {
        $lv = $p_item['lv'];
    }else{
        $lv = 1;
        $pid = 0;
    }
}else{
    $lv = 1;
}
$lv_names = array(
    '1' => '省份',
    '2' => '地市',
    '3' => '县区',
);
/**
 * 列表
 */
if ($act == 'lists') {
    $where = " AND pid = '{$pid}'";
    $result = getPageList($table_name, $_GPC['page'], $where, "sort ASC, id DESC");
}
/**
 * 添加修改
 */
else if($act == 'add') {
    $id = intval($_GPC['id']);
    $item = array();
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
    if ($lv >= 3) {
        message('最大只能添加三级地区!', '', 'error');
    }
    $id = intval($_GPC['id']);
    $postdata = $_GPC['postdata'];
    if ($postdata['name'] == '') {
        message('地区名称不能为空', '', 'error');
    }
    $postdata['uniacid'] = $uniacid;
    $postdata['pinyin'] = getPinyin($postdata['name']);
    $postdata['py'] = getPinyin($postdata['name'], 1);
    if (empty($id)) {
        $postdata['lv'] = $pid > 0 ? $lv+1 : $lv;
        fr_insert($table_name, $postdata);
        $action = "add";
    }else{
        fr_update($table_name, $postdata, array('id' => $id, 'uniacid' => $uniacid));
        $action = "edit";
    }
    //保存操作日志
    action_log($postdata['name'], $action, $table_name);
    message('保存成功!', __WURL($fr_model, array('act' => 'lists', 'pid' => $pid)), 'success');
    exit();
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = intval($_GPC['id']);
    if (!deleteArea($id)) {
        message('数据不存在或已删除!', __WURL($fr_model, array('act' => 'lists')), 'error');
    }
    
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}

include $this->template("web/{$fr_model}_" . $act);