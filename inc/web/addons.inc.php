<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'addons';
$table_name = 'addons';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'add', 'update', 'delete'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
$route_id = intval($_GPC['route_id']);
/**
 * 列表
 */
if ($act == 'lists') {
    $route_all = getAllData('route');
    $route_id = intval($_GPC['route_id']) > 0 ? $_GPC['route_id'] : NULL;
    $where = '';
    if (!empty($route_id)) {
        $where .= " AND route_id = '{$route_id}'";
    }
    $result = getPageList($table_name, $_GPC['page'], $where);
    if (!empty($result['list'])) {
        foreach($result['list'] as &$item) {
            $item['route_name'] = getDataById('route', $item['route_id'], "name");
        }
    }
}
/**
 * 添加修改
 */
else if($act == 'add') {
    $id = intval($_GPC['id']);
    $item = array();
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
    if(empty($postdata['route_id'])){
        message('请选择所属路线!', '', 'error');
    }
    if(empty($postdata['title'])){
        message('请填写附加项标题!', '', 'error');
    }
    if(floatval($postdata['price']) < 0){
        message('请填写正确的附加项价钱!', '', 'error');
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
    action_log($postdata['title'], $action, $table_name);
    
    message('保存成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
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
    fr_delete($table_name, array('id' => $id, 'uniacid' => $uniacid));
    
    //保存操作日志
    action_log($data['title'], "delete", $table_name);
    
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}

include $this->template("web/{$fr_model}_" . $act);