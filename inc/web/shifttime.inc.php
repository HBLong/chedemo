<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'shifttime';
$table_name = 'shift_time';
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
    if (!empty($result['list'])) {
        foreach($result['list'] as &$item) {
            $item['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
        }
    }
}

elseif ($act == "add") {
    $id = intval($_GPC['id']);
    if (!empty($id)) {
        $item = getDataById($table_name, $id);
        $item['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
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
    $times = array_filter($_GPC['time']);
    if(empty($times)){
        message('时间不能为空!', '', 'error');
    }
    $postdata['uniacid'] = $uniacid;
    if (empty($id)) {
        $ids = array();
        foreach($times AS $time) {
            $postdata['time'] = $time;
            fr_insert($table_name, $postdata);
            $ids[] = pdo_insertid();
        }
        $id = implode(",", $ids);
        $action = count($ids) > 1 ? "batch_add" : "add";
    }else{
        $postdata['time'] = $times[0];
        fr_update($table_name, $postdata, array('id' => $id));
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
include $this->template("web/shift_time_" . $act);