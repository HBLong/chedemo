<?php
 
include MODULE_ROOT . '/inc/common.php';
$uniacid = $_W["uniacid"];
$fr_model = 'user';
$table_name = 'user';
$act = trim($_GPC['act']);
$allow_acts = array(
    'lists', 'delete', 'add', 'update', 'voucher'
);
if (!in_array($act, $allow_acts)) {
    $act = 'lists';
}
/**
 * 列表
 */
if ($act == 'lists') {
    $where = '';
    $name = $_GPC['name'] != '' ? trim($_GPC['name']) : '';
    $idcard = $_GPC['idcard'] != '' ? trim($_GPC['idcard']) : '';
    $mobile = $_GPC['mobile'] != '' ? trim($_GPC['mobile']) : '';
    if ($name != '') {
        $where .= " AND name LIKE '%{$name}%'";
    }
    if ($idcard != '') {
        $where .= " AND idcard LIKE '%{$idcard}%'";
    }
    if ($mobile != '') {
        $where .= " AND phone LIKE '%{$mobile}%'";
    }
    if (checksubmit('export_submit', true)) {
        $list = getAllData($table_name, $where);
        $header = array(
            'name' => '姓名', 'idcard' => '身份证', 'phone' => '手机号码', 'sex' => '性别',
        );
        $keys = array_keys($header);
        $html = "\xEF\xBB\xBF";
        foreach ($header as $li) {
            $html .= $li . "\t ,";
        }
        $html .= "\n";
        if (!empty($list)) {
            $order = array();
            $size = ceil(count($list) / 500);
            for ($i = 0; $i < $size; $i++) {
                $buffer = array_slice($list, $i * 500, 500);
                foreach ($buffer as $row) {
                    if (empty($row['name']) && empty($row['idcard']) && empty($row['phone'])) {
                        continue;
                    }
                    $row['sex'] = $row['sex'] == 0 ? '女' : '男';
                    foreach ($keys as $key) {
                        $data[] = $row[$key];
                    }
                    $order[] = implode("\t ,", $data) . "\t ,";
                    unset($data);
                }
            }
            $html .= implode("\n", $order);
        }

        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=用户数据.csv");
        echo $html;
        exit();
    }
    $result = getPageList($table_name, $_GPC['page'], $where);
}
/**
 * 添加修改
 */
else if($act == 'add') {
    $id = intval($_GPC['id']);
    $item = getDataById($table_name, $id);
    if (empty($item)) {
        message("该用户信息不存在或已删除");
    }
    $area_all = getAllData('area');
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
//    $postdata['uniacid'] = $uniacid;
    fr_update($table_name, $postdata, array('id' => $id, 'uniacid' => $uniacid));
    action_log($postdata['name'], "edit", $table_name);
    message('保存成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}
/**
 * 删除数据
 */
else if($act == 'delete') {
    $id = intval($_GPC['id']);
    fr_delete($table_name, array('id' => $id, 'uniacid' => $uniacid));
    action_log($id, "delete", $table_name);
    message('删除数据成功!', __WURL($fr_model, array('act' => 'lists')), 'success');
    exit();
}

/**
 * 会员优惠券
 */
else if($act == 'voucher') {
    $uid = intval($_GPC['id']);
    $result = getPageList('voucher', $_GPC['page'], " AND uid = '{$uid}'", "status ASC, discount ASC");
    
}
include $this->template("web/{$fr_model}_" . $act);