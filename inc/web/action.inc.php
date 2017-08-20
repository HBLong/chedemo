<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'action';
$table_name = 'action';
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
    $where = !empty($_GPC['ip']) ? " AND ip = '" . addslashes($_GPC['ip']) . "'" : "";
    if ($where != '') {
        $_GPC['page'] = 1;
    }
    $result = getPageList($table_name, $_GPC['page'], $where);
    
    $admin_name = array();
    $iplist = array();
    $res = getAllData($table_name, '', 'id DESC', "DISTINCT ip");
    foreach ($res AS $row) {
        $iplist[$row['ip']] = $row['ip'];
    }
    if (!empty($result['list'])) {
        foreach($result['list'] as &$item) {
            $item['admin_name'] = getAdminName($item['uid']);
        }
    }
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $drop_type_date = isset($_GPC['drop_type_date']) ? $_GPC['drop_type_date'] : '';
    /* 按日期删除日志 */
    if ($drop_type_date) {
        if ($_GPC['log_date'] == '0') {
            redirect(__WURL('action', array('act' => 'lists')));
            exit;
        } elseif ($_GPC['log_date'] > '0') {
            $where = " 1 ";
            switch ($_GPC['log_date']) {
                case '1':
                    $a_week = TIMESTAMP - (3600 * 24 * 7);
                    $where .= " AND createtime <= '" . $a_week . "'";
                    break;
                case '2':
                    $a_month = TIMESTAMP - (3600 * 24 * 30);
                    $where .= " AND createtime <= '" . $a_month . "'";
                    break;
                case '3':
                    $three_month = TIMESTAMP - (3600 * 24 * 90);
                    $where .= " AND createtime <= '" . $three_month . "'";
                    break;
                case '4':
                    $half_year = TIMESTAMP - (3600 * 24 * 180);
                    $where .= " AND createtime <= '" . $half_year . "'";
                    break;
                case '5':
                    $a_year = TIMESTAMP - (3600 * 24 * 365);
                    $where .= " AND createtime <= '" . $a_year . "'";
                    break;
            }
            
            fr_delete($table_name, $where);
        }
    }
    /* 如果不是按日期来删除, 就按ID删除日志 */ 
    else {
        $count = 0;
        foreach ($_GPC['checkboxes'] AS $key => $id) {
            fr_delete($table_name, array('id' => intval($id), 'uniacid' => $uniacid));
        }
    }
    
    //保存操作日志
    action_log("", "delete", $table_name);
    message('删除数据成功!', __WURL('action', array('act' => 'lists')), 'success');
    exit();
}
include $this->template("web/action");