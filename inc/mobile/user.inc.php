<?php
 
defined('IN_IA') or exit('Access Denied');
include MODULE_ROOT . '/inc/common.php';
include MODULE_ROOT . '/inc/mobile/init.php';
$uniacid = $_W["uniacid"];
$openid = $_W['openid'];
$uid = $_W['member']['uid'];
$title = "我的帐号";
$act = trim($_GPC['act']);
$allow_acts = array(
    'index', 'order', 'profile', 'voucher'
);
checkauth();
if (!in_array($act, $allow_acts)) {
    $act = 'index';
}
cleanParentOrder();
if ($act == 'index') {
    
}
/**
 * 个人信息
 */
else if($act == 'profile') {
    if ($_W['ispost'] && $_W['isajax'] ) {
        $postdata = $_GPC['postdata'];
        $return = array('error' => 1, 'msg' => 'error');
        if (empty($postdata['name'])) {
            $return['msg'] = '请填写您的真实姓名！';
            echo json_encode($return);exit;
        }
        if (empty($postdata['phone']) || !validMobile($postdata['phone'])) {
            $return['msg'] = '请填写正确的手机号码！';
            echo json_encode($return);exit;
        }
        $rs = fr_update('user', $postdata, array('uid' => $uid, 'uniacid' => $uniacid));
        if ($rs === FALSE) {
            $return['msg'] = '服务器忙，请稍候再试！';
        }else{
            $return['msg'] = '保存信息成功！';
            $return['error'] = 0;
            $return['url'] = __MURL('user', array('act' => "profile"));
            echo json_encode($return);exit;
        }
    }
    $sql = "SELECT * FROM ". fr_table('user') . " WHERE uid = :uid AND uniacid = :uniacid";
    $params = array(":uid" => $uid, ":uniacid" => $uniacid);
    $profile = pdo_fetch($sql, $params);
    $area_all = getAllData('area');
}
/**
 * 订单
 */
else if($act == 'order') {
    $title = "我的订单";
    $status = in_array($_GPC['status'], array("-1", "0", "1", "2")) ? $_GPC['status'] : 0;
    $order_all = getAllData('order', " AND uid = '{$uid}' AND status = '{$status}'", "createtime DESC");
    foreach($order_all AS $key => $val) {
        $shift = getDataById('shift', $val['shift_id']);
//        $route = getRouteData($shift['route_id']);
        $order_all[$key]['shift'] = $shift;
        $order_all[$key]['start_station'] = getDataById('station', $val['start_station_id']);
        $order_all[$key]['end_station'] = getDataById('station', $val['end_station_id']);
    }
}
/**
 * 我的券
 */
else if($act == 'voucher') {
    $title = "我的券";
    $voucher_list = getAllData('voucher', " AND uid = '{$uid}'", "status ASC, createtime DESC");
//    dump($voucher_list);die;
}
include $this->template('user_' . $act);