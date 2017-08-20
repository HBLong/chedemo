<?php
 
defined('IN_IA') or exit('Access Denied');
include_once 'base.func.php';
/**
 * 折6翼7天8使8资9源3社2区
 * @param string $table
 * @param string $istablepre
 * @return string
 */
function fr_table($table, $istablepre = true) {
    $tablename = "fr_cp_" . $table;
    if ($istablepre) {
        $tablename = tablename($tablename);
    }
    return $tablename;
}

function fr_update($table, $data = array(), $params = array(), $glue = "AND") {
    global $_W;
    if (is_array($params) || empty($params)) {
        $params['uniacid'] = $_W['uniacid'];
    }elseif(is_string($params)) {
        $params .= " AND uniacid = '{$_W['uniacid']}' ";
    }
    $data = fr_facade($table, $data);
    return pdo_update(fr_table($table, FALSE), $data, $params, $glue);
}

function fr_insert($table, $data = array(), $replace = FALSE) {
    $data = fr_facade($table, $data);
    return pdo_insert(fr_table($table, FALSE), $data, $replace);
}
function fr_delete($table, $params = array(), $glue = "AND") {
    global $_W;
    if (is_array($params) || empty($params)) {
        $params['uniacid'] = $_W['uniacid'];
    }elseif(is_string($params)) {
        $params .= " AND uniacid = '{$_W['uniacid']}' ";
    }
    return pdo_delete(fr_table($table, FALSE), $params, $glue);
}
function fr_facade($table, $data) {
    $fields = pdo_fetchallfields(fr_table($table));
    // 检查数据字段合法性
    if(!empty($fields)) {
        foreach ($data as $key=>$val){
            if(!in_array($key, $fields, true)){
                unset($data[$key]);
            }
        }
    }
    return $data;
 }
/**
 * 获取列表数据
 * @global type $_W
 * @param type $table_name
 * @param type $page
 * @param type $where
 * @param type $order
 * @param type $page_size
 * @return type
 */
function getPageList($table_name, $page = 1, $where = '', $order = 'id DESC', $page_size = 20) {
    global $_W;
    $return = array();
    $uniacid = $_W["uniacid"];
    $pindex = max(1, intval($page));
    $orderby = '';
    if ($order != '') {
        $orderby = ' ORDER BY '.$order;
    }
    $sql = 'SELECT * FROM '.  fr_table($table_name) . ' WHERE uniacid = :uniacid '.$where. $orderby . '  LIMIT '. ($pindex -1) * $page_size . ',' .$page_size;
    $return['list'] = pdo_fetchall( $sql , array(':uniacid' => $uniacid));
    $countSql = 'SELECT COUNT(*) FROM '.fr_table($table_name).' WHERE uniacid = :uniacid '.$where;
    $return['total'] = pdo_fetchcolumn( $countSql, array(':uniacid' => $uniacid) );
    $return['pager'] = pagination($return['total'], $pindex, $page_size);
    return $return;
}

/**
 * 根据ID获取某表数据
 * @global type $_W
 * @param string $table_name
 * @param int $id
 * @param string $field
 * @param fixed $default
 * @return array
 */
function getDataById($table_name, $id, $field = NULL, $default = array()) {
    global $_W;
    if (empty($table_name) || empty($id)) {
        return $default;
    }
    $id = intval($id);
    $uniacid = $_W['uniacid'];
    $sql = "SELECT * FROM ". fr_table($table_name) . " WHERE id = :id AND uniacid = :uniacid";
    $params = array(":id" => $id, ":uniacid" => $uniacid);
    $item = pdo_fetch($sql, $params);
    return empty($field) ? $item : (empty($item[$field]) ? $default : $item[$field]);
}

/**
 * 获取一行数据
 * @global array $_W
 * @param string $table_name
 * @param string $where
 * @return array
 */
function getRow($table_name, $where = '', $order = '') {
    global $_W;
    if (empty($table_name)) {
        return array();
    }
    $orderby = '';
    if ($order != '') {
        $orderby = ' ORDER BY '.$order;
    }
    $uniacid = $_W['uniacid'];
    $sql = "SELECT * FROM ". fr_table($table_name) . " WHERE uniacid = :uniacid {$where} {$orderby} LIMIT 1";
    $params = array(":uniacid" => $uniacid);
    $row = pdo_fetch($sql, $params);
    return $row;
}
/**
 * 
 * @global type $_W
 * @param string $table_name
 * @param string $field
 * @param string $where
 * @param string $order
 * @return string
 */
function getCol($table_name, $field = '*', $where = '', $order = '') {
    global $_W;
    if (empty($table_name)) {
        return '';
    }
    $orderby = '';
    if ($order != '') {
        $orderby = ' ORDER BY '.$order;
    }
    $uniacid = $_W['uniacid'];
    $sql = "SELECT {$field} FROM ". fr_table($table_name) . " WHERE uniacid = :uniacid {$where} {$orderby} LIMIT 1";
    $params = array(":uniacid" => $uniacid);
    return pdo_fetchcolumn($sql, $params);
}

/**
 * 获取所有数据
 * @global type $_W
 * @param string $table
 * @param string $where
 * @param string $order
 * @param string $field
 * @param string $keyfield
 * @return array
 */
function getAllData($table_name, $where = '', $order = 'id DESC', $field = "*", $keyfield = '') {
    global $_W;
    if (empty($table_name)) {
        return array();
    }
    $orderby = '';
    if ($order != '') {
        $orderby = ' ORDER BY '.$order;
    }
    $id = intval($id);
    $uniacid = $_W['uniacid'];
    $sql = "SELECT {$field} FROM ". fr_table($table_name) . " WHERE uniacid = :uniacid {$where} {$orderby}";
    $params = array(":uniacid" => $uniacid);
    $item = pdo_fetchall($sql, $params, $keyfield);
    return $item;
}


/**
 * 获取路线相关数据
 * @param type $route_id
 * @param type $fr_moudle_name
 * @return boolean
 */
function getRouteData($route_id) {
    $item = getDataById('route', $route_id);
    if (!empty($item)) {
        $item['start_station'] = getDataById('station', $item['start_station_id']);
        $item['end_station'] = getDataById('station', $item['end_station_id']);
        return $item;
    }  else {
        return false;
    }
}

/**
 * 获取车站相关信息
 * @param type $station_id
 * @param type $fr_moudle_name
 * @return boolean
 */
function getStationData($station_id) {
    $item = getDataById('station', $station_id);
    if (!empty($item)) {
        $item['province'] = getDataById('area', $item['province_id']);
        $item['city'] = getDataById('area', $item['city_id']);
        $item['district'] = getDataById('area', $item['district_id']);
        return $item;
    }  else {
        return false;
    }
}

/**
 * 获取用户信息
 * @global type $_W
 * @param type $uid
 * @param type $fr_moudle_name
 * @return type
 */
function getUserData($uid) {
    $uid = intval($uid);
    $where = " AND uid = '{$uid}'";
    return getRow("user", $where);
}

/**
 * 格式化配置数据
 * @param type $type
 * @return type
 */
function format_setting_type($type) {
    $types = explode("\r\n", $type);
    $result = array();
    foreach($types as $item) {
        $res = explode(":", $item);
        if (count($res) == 2) {
            $result[$res[0]] = $res[1];
        }
    }
    return array_filter($result);
}

/**
 * 生成Token
 * @return string
 */
function gen_token() {
    return md5(rand() . md5(md5(time()) + uniqid()));
}

/**
 * 生成web端URL
 * @param type $do
 * @param type $query
 * @return type
 */
function __WURL($do, $query = array(), $noredirect = true, $addhost = true){
    $query['do'] = $do;
    $query['m'] = 'fr_cp';
    return wurl('site/entry', $query, $noredirect, $addhost);
}

/**
 * 生成APP端URL
 * @param type $do
 * @param type $query
 * @param type $noredirect
 * @param type $addhost
 * @return type
 */
function __MURL($do, $query = array(), $noredirect = true, $addhost = true){
    $query['do'] = $do;
    $query['m'] = 'fr_cp';
    return murl('entry', $query, $noredirect, $addhost);
}
 
/**
 * 发送文本消息给用户
 * @global array $_W
 * @param int $acid
 * @param string $openid
 * @param string $msg
 * @return boolean
 */
function sendNotice($acid, $openid, $msg = ''){
    global $_W;
    if (empty($acid)) {
        $acid = $_W['account']['acid'];
    }
    if (empty($acid) && $_W['uniacid']) {
	$acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
    }
    $acc = WeAccount::create($acid);
    if (empty($acc) || empty($openid) || empty($msg)) {
        return false;
    }
    $sendData = array(
        "touser" => $openid,
        "msgtype" => "text",
        "text" => array(
            "content" => urlencode($msg)
        )
    );
    $data = $acc->sendCustomNotice($sendData);
    return $data;
}

/**
 * 发送模板消息给用户
 * @global array $_W
 * @param int $acid
 * @param string $openid
 * @param array $postdata
 * @param string $template_id
 * @param string $url
 * @param string $topcolor
 * @return boolean
 */
function sendTplNotice($acid, $openid, $postdata, $template_id, $url = '', $topcolor = '#FF683F') {
    global $_W;
    if (empty($acid)) {
        $acid = $_W['account']['acid'];
    }
    if (empty($acid) && $_W['uniacid']) {
	$acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
    }
    $acc = WeAccount::create($acid);
    if (empty($acc) || empty($openid) || empty($postdata) || empty($template_id)) {
        return false;
    }
    $res = $acc->sendTplNotice($openid , $template_id, $postdata, $url, $topcolor);//模板消息
    return $res;
}

/**
 * 获取用户头像
 * @global type $_W
 * @return string
 */
function getMemberAvatar() {
    global $_W;
    load()->model('mc');
    $avatar = '';

    if (!empty($_W['member']['uid'])) {
            $member = mc_fetch(intval($_W['member']['uid']), array('avatar'));
            if (!empty($member)) {
                    $avatar = $member['avatar'];
            }
    }
    if (empty($avatar)) {
            $fan = mc_fansinfo($_W['openid']);
            if (!empty($fan)) {
                    $avatar = $fan['avatar'];
            }
    }
    if (empty($avatar)) {
            $userinfo = mc_oauth_userinfo();
            if (!is_error($userinfo) && !empty($userinfo) && is_array($userinfo) && !empty($userinfo['avatar'])) {
                    $avatar = $userinfo['avatar'];
            }
    }
    if (empty($avatar)) {
        return '';
    } else {
        return $avatar;
    }
}

/**
 * 获取用户昵称
 * @param type $openid
 * @return type
 */
function getNicknameByOpenid($openid) {
    load()->model("mc");
    $member = mc_fetch($openid, array('nickname'));
    return trim($member['nickname']) != '' ? $member['nickname'] : $openid;
}

/**
 * 生成订单号
 * @return string
 */
function genOrderSN($order_id) {
    return date ( 'mdh' ) . rand ( 10, 99 ) . $order_id;
}

function getSurplusVotes($shift_info, $departure_time) {
    $where = " AND shift_id = '{$shift_info['shift_id']}' AND departure_time = '{$departure_time}' AND status IN(1, 3)";
    $order_count = getCol("order", "count(*)", $where);
    return $shift_info['total_votes'] - intval($order_count);
}
/**
 * 获取座位号
 * @param type $shift_info
 * @param type $departure_time
 */
function getSeatNumber($shift_info, $departure_time, $select_seat = 0, $order_seat = array()) {
    $select_seat = intval($select_seat);
    $where = " AND shift_id = '{$shift_info['shift_id']}' AND departure_time = '{$departure_time}' AND status IN(1, 3)";
    $seat_numbers = getAllData("order", $where, " seat_number ASC", "seat_number", "seat_number");
    $seat_numbers = array_keys($seat_numbers);
    if ($select_seat > 0 && !in_array($select_seat, $seat_numbers) && !in_array($select_seat, $order_seat)) {
        return $select_seat;
    }
    $seat_number = 0;
    
    for($i = 1; $i <= $shift_info['total_votes']; $i++) {
        if (!in_array($i, $seat_numbers) && !in_array($i, $order_seat)) {
            $seat_number = $i;
            break;
        }
    }
    return intval($seat_number);
}
/**
 * 获取座位号列表
 * @param type $shift_info
 * @param type $departure_time
 */
function getSeatNumberList($shift_info, $departure_time) {
    $where = " AND shift_id = '{$shift_info['shift_id']}' AND departure_time = '{$departure_time}' AND status IN(1, 3)";
    $seat_numbers = getAllData("order", $where, " seat_number ASC", "seat_number", "seat_number");
    $seat_numbers = array_keys($seat_numbers);
    $seat_number_list = array();
    for($i = 1; $i <= $shift_info['total_votes']; $i++) {
        if (!in_array($i, $seat_numbers)) {
            $seat_number_list[] = array(
                "number" => $i,
                "occupied" => 0,
            );
        }else{
            $seat_number_list[] = array(
                "number" => $i,
                "occupied" => 1,
            );
        }
    }
    return $seat_number_list;
}

function getShiftVote($shift_id) {
    $where = " AND id = '{$shift_id}'";
    $total_votes = getCol("shift", "total_votes", $where);
    return $total_votes;
}
function genSeatNumber($order) {
    $where = " AND shift_id = '{$order['shift_id']}'";
    $seat_number = getCol("seat", "seat_number", $where, " seat_number DESC");
    $seat_number = $seat_number > 0 ? $seat_number + 1 : 1;
    $return_seat_number = array();
    for($i=0; $i<$order['number']; $i++) {
        $return_seat_number[] = $seat_number;
        $seat_data = array(
            "uniacid" => $order['uniacid'],
            "order_id" => $order['id'],
            "shift_id" => $order['shift_id'],
            "seat_number" => $seat_number,
        );
        fr_insert("seat", $seat_data);
        $seat_number++;
    }
    return $return_seat_number;
}

function getSeat($order) {
    $where = " AND shift_id = '{$order['shift_id']}' AND order_id = '{$order['id']}'";
    $result = getAllData("seat", $where, " seat_number ASC", "seat_number");
    $seat_number = array();
    foreach ($result as $seat) {
        $seat_number[] = $seat['seat_number'];
    }
    return implode("|", $seat_number);
}

function deleteSeat($order) {
    $where = array(
        "shift_id" => $order['shift_id'],
        "order_id" => $order['id'],
    );
    fr_delete("seat", $where);
}
/**
 * 改变订单状态
 */
function order_paid($order_id, $fr_cp_settings) {
    global $_W;
    $order_id = intval($order_id);
    if ($order_id > 0) {
        $order_info = getDataById('order', $order_id);
        if ($order_info && $order_info['is_paid'] == 0) {
            $update_order_data = array(
                'status' => 1,
                'is_paid' => 1,
                'pay_time' => TIMESTAMP
            );
            fr_update('order', $update_order_data, "(id = '{$order_id}' OR parent_id = '{$order_id}')");
            notificationManager(array_merge($order_info, $update_order_data), 1);//通知管理员

            $shift = getShiftInfo($order_info["start_station_id"], $order_info['end_station_id'], $order_info['shift_id']);
            $templateid = trim($fr_cp_settings['templateid']);
            $title = "您已成功购买{$order_info['number']}张车票，共￥{$order_info['price']}";
            if ($order_info['openid'] && !empty($templateid)) {
                $postdata = array(
                    'first' => array(
                        'value' => $title,
                        'color' => '#173177',
                    ),
                    'keyword1' => array(
                        'value' => "{$shift['start_station']['name']}->{$shift['end_station']['name']}",
                        'color' => '#173177',
                    ),
                    'keyword2' => array(
                        'value' => timeToStr($order_info['departure_time'], "Y-m-d H:i"),
                        'color' => '#173177',
                    ),
                    'keyword3' => array(
                        'value' => $order_info['order_sn'],
                        'color' => '#173177',
                    ),
                    'keyword4' => array(
                        'value' => '',
                        'color' => '',
                    ),
                    'remark' => array(
                        'value' => $fr_cp_settings['content3'],
                        'color' => '',
                    ),
                );
                $url = __MURL('order', array('act' => 'info', 'order_id' => $order_info['id']));
                sendTplNotice('', $order_info['openid'], $postdata, $templateid, $url);
                //发送通知
            }

            //发送短信给用户
            if ($fr_cp_settings['sms_type'] != 0 && $fr_cp_settings['sms_buy_order'] == 1 && !empty($shift['sms_content'])) {
                $find = array("{LUXIAN}","{SHIJIAN}", "{PIAO}");
                $replace = array(
                    "{$shift['start_station']['name']}至{$shift['end_station']['name']}",
                    timeToStr($order_info['departure_time'], 'Y年m月d日 H:i'),
                    $order_info['number']
                );
                $sms_content = str_replace($find, $replace, $shift['sms_content']);
                if ($fr_cp_settings['sms_type'] == 1) {
                    load()->model('cloud');
                    cloud_prepare();
                    if (validMobile($order_info['phone'])) {
                        cloud_sms_send($order_info['phone'], $sms_content);
                    }
                }    
                if ($fr_cp_settings['sms_type'] == 2) {
                    if (validMobile($order_info['phone'])) {
                        sendSms($order_info['phone'], $sms_content, $fr_cp_settings);
                    }
                }    
            }
            $shift_surplus_votes = getSurplusVotes($shift, $order_info['departure_time']);
            if (in_array($shift_surplus_votes, $fr_cp_settings['yupiao']) && $fr_cp_settings['sms_type'] != 0 && !empty($fr_cp_settings['sms_content'])) {
                $find = array("{LUXIAN}","{SHIJIAN}","{YUPIAO}");
                $replace = array(
                    "{$shift['start_station']['name']}至{$shift['end_station']['name']}",
                    timeToStr($order_info['departure_time'], 'Y年m月d日 H:i'),
                    $shift_surplus_votes,
                );
                $sms_content = str_replace($find, $replace, $fr_cp_settings['sms_content']);
                if (validMobile($shift['person_phone'])) {
                    $fr_cp_settings['sms_mobile'][] = $shift['person_phone'];
                    $fr_cp_settings['sms_mobile'] = array_unique($fr_cp_settings['sms_mobile']);
                }
                if ($fr_cp_settings['sms_type'] == 1) {
                    load()->model('cloud');
                    cloud_prepare();
                    if (!empty($fr_cp_settings['sms_mobile']) && !empty($sms_content)) {
                        foreach ($fr_cp_settings['sms_mobile'] as $phone) {
                            if (validMobile(trim($phone))) {
                                cloud_sms_send(trim($phone), $sms_content);
                            }
                        }
                    }
                }    
                if ($fr_cp_settings['sms_type'] == 2) {
                    if (!empty($fr_cp_settings['sms_mobile']) && !empty($sms_content)) {
                        foreach ($fr_cp_settings['sms_mobile'] as $phone) {
                            if (validMobile(trim($phone))) {
                                sendSms(trim($phone), $sms_content, $fr_cp_settings);
                            }
                        }
                    }
                } 
            }
            
            if ($fr_cp_settings['print_type'] == 1) {
                $voucher = array();
                if ($order_info['voucher_id'] > 0) {
                    $voucher = getVoucherById($order_info['voucher_id'], $order_info['uid']);
                }
                $result = array(
                    "start_station" => $shift['start_station']['name'],
                    "end_station" => $shift['end_station']['name'],
                    "datetime" => timeToStr($order_info['departure_time']),
                    "name" => $order_info['name'],
                    "phone" => $order_info['phone'],
                    "idcard" => empty($order_info['idcard']) ? "-" : $order_info['idcard'],
                    "number" => $order_info['number'],
                    "voucher" => empty($voucher) ? "无" : $voucher['name'],
                    "price" => "{$order_info['price']}元",
                    "addons" => empty($order_info['addons']) ? "无" : trim(str_replace(";", "\r\n", $order_info['addons'])),
                );
                if (print_ticket($result, $fr_cp_settings)) {
                    fr_update('order', array('isprint' => 1), array('id' => $order_id));
                }
            }
            
            if ($order_info['voucher_id'] > 0) {//改变代金券状态
                updateVoucherStatus($order_info['voucher_id'], $order_info['uid'], 2);
            }
            /*送券 start*/
            $total = getCol("order", "COUNT(*)", " AND uid = '{$order_info['uid']}' AND status = 1 AND is_paid = 1");
            if ($total == 1) {//首次购买代金券
                $title = "首次购买赠";
                sendFirstBuyingVoucher($order_info['uid'], $title, 10, $order_id);
            }
            $route = getRouteData($shift['route_id']);
            if ($order_info['number'] >= 5 && $order_info['number'] < 10) {//订票5到9张送一张免费券
                $title = "买5送1赠";
                sendFreeVoucher($order_info['uid'], $title, $order_info['start_station_id'], $route['end_station_id'], $order_id);
            }else if ($order_info['number'] >= 10) {//订票10张以上送三张免费券
                $title = "买10送3赠";
                sendFreeVoucher($order_info['uid'], $title, $order_info['start_station_id'], $route['end_station_id'], $order_id);
                sendFreeVoucher($order_info['uid'], $title, $order_info['start_station_id'], $route['end_station_id'], $order_id);
                sendFreeVoucher($order_info['uid'], $title, $order_info['start_station_id'], $route['end_station_id'], $order_id);
            }
            //每次购买获得同线路10元代金券
            sendRouteVoucher($order_info['uid'], '买1送1赠', 10, $order_info['start_station_id'], $route['end_station_id'], $order_id);
            /*送券 end*/
        }
    }
}
function getAddons($addons_id, $route_id = 0) {
    global $_W;
    if (empty($addons_id)) {
        return '';
    }
    $where = "";
    if (is_array($addons_id)) {
        $addons_id = array_unique($addons_id);
        $where .= " AND id IN(".  implode(",", $addons_id).")";
    }else if(is_numeric($addons_id)){
        $where .= " AND id = '{$addons_id}'";
    }else{
        $where .= " AND id IN({$addons_id})";
    }
    
    if ($route_id > 0) {
        $where .= " AND route_id = '{$route_id}'";
    }
    $addons = getAllData("addons", $where, "sort ASC");
    return $addons;
}
/**
 * 计算订单价格
 * @param int $number
 * @param int $ticket_price
 * @param int $voucher_id
 * @return int
 */
function order_price($number, $ticket_price, $voucher_id, $uid, $route_id, $addons_id) {
    global $_W;
    $price = $ticket_price * $number;
    if ($route_id > 0 && !empty($addons_id)) {
        $addons = getAddons($addons_id, $route_id);
        if (is_array($addons) && !empty($addons)) {
            foreach($addons AS $addone) {
                $price += floatval($addone['price']);
            }
        }
    }
    if ($voucher_id > 0) {
        $voucher = getVoucherById($voucher_id, $uid);
        if (!empty($voucher) && $voucher['status'] == 0) {
            if ($voucher['discount'] > 0) {
                $price -= $voucher['discount'];
            }else{
                $price -= $ticket_price;
            }
        }
    }
    return floatval($price) < 0 ? 0 : $price;
}

/**
 * 手机端获取班次列表
 * @global type $_W
 * @param type $start_station_id
 * @param type $end_station_id
 * @param type $where
 * @return array
 */
function getShiftList($start_station_id, $end_station_id, $where = '') {
    global $_W;
    if (empty($start_station_id) || empty($end_station_id)) {
        return array();
    }
    $fields = " s.id AS shift_id, s.time, s.date, s.total_votes, s.surplus_votes, s.ticket_price, s.method, s.enable_vouchers, s.refund_ticket, s.vehicle_id "
            . ", r.id AS route_id, r.start_station_id, r.end_station_id ";
    $sql = "SELECT {$fields} FROM ". fr_table('shift') . " AS s "
            . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
            . " WHERE r.start_station_id = '{$start_station_id}' AND r.end_station_id = '{$end_station_id}' AND s.uniacid = '{$_W['uniacid']}' {$where} ORDER BY s.time ASC";
    $result = pdo_fetchall($sql);
    
//    dump($result);
    //配客点
    $sql = "SELECT * FROM ". fr_table('shift_station') . " WHERE station_id = '{$start_station_id}' AND uniacid = '{$_W['uniacid']}' ORDER BY departure_time ASC";
    $result2 = pdo_fetchall($sql);
    $shift_station = array();
//    dump($result2);
    if (!empty($result2)) {
        foreach($result2 AS $item2) {
            $sql = "SELECT {$fields} FROM ". fr_table('shift') . " AS s "
            . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
            . " WHERE s.id = '{$item2['shift_id']}' AND r.end_station_id = '{$end_station_id}' AND s.uniacid = '{$_W['uniacid']}' {$where} ORDER BY s.time ASC";
            $shift_station[] = $item2['station_id'];
            $result3 = pdo_fetchall($sql);
            if (!empty($result3)) {
                foreach($result3 AS $k3 => $v3) {
                    $result3[$k3]['start_station_id'] = $item2['station_id'];
                    $result3[$k3]['time'] = $item2['departure_time'];
                    $result3[$k3]['ticket_price'] = $item2['ticket_price'];
                    $result[] = $result3[$k3];
                }
            }
        }
    }
    //下客点
    $sql = "SELECT * FROM ". fr_table('shift_end_station') . " AS ses"
            . " LEFT JOIN ". fr_table('shift') . " AS s ON s.id = ses.shift_id"
            . " WHERE ses.end_station_id = '{$end_station_id}' AND ses.uniacid = '{$_W['uniacid']}' ORDER BY s.time ASC";
    $result4 = pdo_fetchall($sql);
    $shift_end_station = array();
    if (!empty($result4)) {
        foreach($result4 AS $item4) {
            $sql = "SELECT {$fields} FROM ". fr_table('shift') . " AS s "
            . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
            . " WHERE s.id = '{$item4['shift_id']}' AND r.start_station_id = '{$start_station_id}' AND s.uniacid = '{$_W['uniacid']}' {$where} ORDER BY s.time ASC";
            $shift_end_station[] = $item4['end_station_id'];
            $result5 = pdo_fetchall($sql);
            if (!empty($result5)) {
                foreach($result5 AS $k5 => $v5) {
                    $result5[$k5]['end_station_id'] = $item4['end_station_id'];
                    $result[] = $result5[$k5];
                }
            }
        }
    }
    if (!empty($shift_station) && !empty($shift_end_station)) {
        $shift_station = array_unique($shift_station);
        $shift_end_station = array_unique($shift_end_station);
        $sql = "SELECT ss.*, ses.end_station_id "
                . "FROM ". fr_table('shift_station') . " AS ss "
                . "LEFT JOIN ". fr_table('shift_end_station') . " AS ses ON ss.shift_id = ses.shift_id "
                . " WHERE ss.station_id IN(".  implode(",", $shift_station).") AND ses.end_station_id  IN(".  implode(",", $shift_end_station).") AND ss.uniacid = '{$_W['uniacid']}' ORDER BY ss.departure_time ASC";
        
        $result6 = pdo_fetchall($sql);
        if (!empty($result6)) {
            foreach($result6 AS $k6 => $v6) {
                $sql = "SELECT {$fields} FROM ". fr_table('shift') . " AS s "
                    . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
                    . " WHERE s.id = '{$v6['shift_id']}' AND s.uniacid = '{$_W['uniacid']}' {$where} ORDER BY s.time ASC";
                    
                $result7 = pdo_fetchall($sql);
                if (!empty($result7)) {
                    foreach($result7 AS $k7 => $v7) {
                        $result7[$k7]['end_station_id'] = $v6['end_station_id'];
                        $result7[$k7]['start_station_id'] = $v6['station_id'];
                        $result7[$k7]['time'] = $v6['departure_time'];
                        $result7[$k7]['ticket_price'] = $v6['ticket_price'];
                        $result[] = $result7[$k7];
                    }
                }
            }
        }
    }
//    dump($result);die;
    if (empty($result)) {
        return array();
    }
    $res = array();
    foreach ($result as $key => $item) {
        if (isset($res[$item['shift_id']])) {
            continue;
        }
        $result[$key]['start_station'] = getStationData($result[$key]['start_station_id']);
        $result[$key]['end_station'] = getStationData($result[$key]['end_station_id']);
        //$result[$key]['vehicle'] = getDataById("vehicle", $result[$key]['vehicle_id']);
        $result[$key]['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
        $res[$item['shift_id']] = $result[$key];
    }
    return $res;
}

/**
 * 手机端获取班次详细
 * @global type $_W
 * @param type $start_station_id
 * @param type $end_station_id
 * @param type $where
 * @param type $fr_moudle_name
 * @return array
 */
function getShiftInfo($start_station_id, $end_station_id, $shift_id) {
    global $_W;
    if (empty($start_station_id) || empty($end_station_id) || empty($shift_id)) {
        return array();
    }
    $fields = "s.id AS shift_id, s.time, s.date, s.total_votes, s.surplus_votes, s.ticket_price, s.method, s.enable_vouchers, s.refund_ticket, s.vehicle_id "
            . ", r.id AS route_id, r.start_station_id, r.end_station_id, r.person_phone, r.explain, r.sms_content ";
    $sql = "SELECT {$fields}"
            . "FROM ". fr_table('shift') . " AS s "
            . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
            . " WHERE r.start_station_id = '{$start_station_id}' AND r.end_station_id = '{$end_station_id}' AND s.uniacid = '{$_W['uniacid']}' AND s.id = '{$shift_id}' ORDER BY s.time ASC";
    $result = pdo_fetchall($sql);
    $sql = "SELECT * FROM ". fr_table('shift_station') . " WHERE station_id = '{$start_station_id}' AND shift_id = '{$shift_id}' AND uniacid = '{$_W['uniacid']}'";
    $result2 = pdo_fetchall($sql);
    $shift_station = array();
    if (!empty($result2)) {
        foreach($result2 AS $item2) {
            $sql = "SELECT {$fields}"
            . "FROM ". fr_table('shift') . " AS s "
            . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
            . " WHERE s.id = '{$item2['shift_id']}' AND r.end_station_id = '{$end_station_id}' AND s.uniacid = '{$_W['uniacid']}' ORDER BY s.time ASC";
            
            $shift_station[] = $item2['station_id'];
            $result3 = pdo_fetchall($sql);
            if (!empty($result3)) {
                foreach($result3 AS $k3 => $v3) {
                    $result3[$k3]['start_station_id'] = $item2['station_id'];
                    $result3[$k3]['time'] = $item2['departure_time'];
                    $result3[$k3]['ticket_price'] = $item2['ticket_price'];
                    $result[] = $result3[$k3];
                }
            }
        }
    }
    
    //下客点
    $sql = "SELECT * FROM ". fr_table('shift_end_station') . " WHERE end_station_id = '{$end_station_id}' AND shift_id = '{$shift_id}' AND uniacid = '{$_W['uniacid']}'";
    $result4 = pdo_fetchall($sql);
    $shift_end_station = array();
    if (!empty($result4)) {
        foreach($result4 AS $item4) {
            $sql = "SELECT {$fields}"
            . "FROM ". fr_table('shift') . " AS s "
            . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
            . " WHERE s.id = '{$item4['shift_id']}' AND r.start_station_id = '{$start_station_id}' AND s.uniacid = '{$_W['uniacid']}' {$where} ORDER BY s.time ASC";
            $shift_end_station[] = $item4['end_station_id'];
            $result5 = pdo_fetchall($sql);
            if (!empty($result5)) {
                foreach($result5 AS $k5 => $v5) {
                    $result5[$k5]['end_station_id'] = $item4['end_station_id'];
                    $result[] = $result5[$k5];
                }
            }
        }
    }
    
    if (!empty($shift_station) && !empty($shift_end_station)) {
        $shift_station = array_unique($shift_station);
        $shift_end_station = array_unique($shift_end_station);
        $sql = "SELECT ss.*, ses.end_station_id "
                . "FROM ". fr_table('shift_station') . " AS ss "
                . "LEFT JOIN ". fr_table('shift_end_station') . " AS ses ON ss.shift_id = ses.shift_id "
                . " WHERE ss.station_id IN(".  implode(",", $shift_station).") AND ses.end_station_id  IN(".  implode(",", $shift_end_station).") AND ss.shift_id = '{$shift_id}' AND ss.uniacid = '{$_W['uniacid']}'";
        
        $result6 = pdo_fetchall($sql);
        if (!empty($result6)) {
            foreach($result6 AS $k6 => $v6) {
                    $sql = "SELECT {$fields}"
                    . "FROM ". fr_table('shift') . " AS s "
                    . "LEFT JOIN ". fr_table('route') . " AS r ON s.route_id = r.id "
                    . " WHERE s.id = '{$v6['shift_id']}' AND s.uniacid = '{$_W['uniacid']}' {$where} ORDER BY s.time ASC";
                    
                $result7 = pdo_fetchall($sql);
                if (!empty($result7)) {
                    foreach($result7 AS $k7 => $v7) {
                        $result7[$k7]['end_station_id'] = $v6['end_station_id'];
                        $result7[$k7]['start_station_id'] = $v6['station_id'];
                        $result7[$k7]['time'] = $v6['departure_time'];
                        $result7[$k7]['ticket_price'] = $v6['ticket_price'];
                        $result[] = $result7[$k7];
                    }
                }
            }
        }
    }
//    dump($result);die;
    if (empty($result)) {
        return array();
    }
    foreach ($result as $key => $item) {
        $result[$key]['start_station'] = getStationData($result[$key]['start_station_id']);
        $result[$key]['end_station'] = getStationData($result[$key]['end_station_id']);
        //$result[$key]['vehicle'] = getDataById("vehicle", $result[$key]['vehicle_id']);
        $result[$key]['time'] = timeToStr(GetMkTime("1990-12-19 " . $item['time']), "H:i");
    }
    return $result[0];
}

/**
 * 送券
 * @param int $uid 用户ID
 * @param string $title 券说明
 * @param int $discount 券金额 0：免费券
 * @param int $type 1:代金券；2：免费券
 * @param int $come_from 1:关注送；2：首次购买送；0：其它
 * @param int $start_station_id 线路始发站
 * @param int $end_station_id 线路目的站
 * @param int $order_id 订单ID
 * @param int $end_time 到期时间
 */
function sendVoucher($uid, $title, $discount, $type = 1, $come_from = 0, $start_station_id = 0, $end_station_id = 0, $order_id = 0, $end_time = 0) {
    global $_W;
    if (empty($title) || empty($uid)) {
        return false;
    }
    if ($type == 2) {//免费券 金额设置为0
        $discount = 0;
    }else{
        if ($discount <= 0) { //代金券金额必须大于0
            return false;
        }
    }
    if ($start_station_id > 0 || $end_station_id > 0) {
        $start_station = getDataById('station', $start_station_id);
        $end_station = getDataById('station', $end_station_id);
        if (empty($start_station) || empty($end_station)) {
            return false;
        }
    }
    $data = array(
        "uniacid" => $_W['uniacid'],
        'uid' => $uid,
        'title' => $title,
        'discount' => $discount,
        'type' => $type,
        'come_from' => $come_from,
        'start_station_id' => $start_station_id,
        'end_station_id' => $end_station_id,
        'order_id' => $order_id,
        'end_time' => $end_time,
        'createtime' => TIMESTAMP,
        'status' => 0,
    );
    fr_insert('voucher', $data);
}

/**
 * 送免费券
 * @param int $uid
 * @param string $title
 * @param int $start_station_id
 * @param int $end_station_id
 * @param int $order_id
 * @param int $end_time
 */
function sendFreeVoucher($uid, $title, $start_station_id = 0, $end_station_id = 0, $order_id = 0, $end_time = 0) {
    sendVoucher($uid, $title, 0, 2, 0, $start_station_id, $end_station_id, $order_id, $end_time);
}

/**
 * 送指定线路代金券
 * @param int $uid
 * @param string $title
 * @param int $discount
 * @param int $start_station_id
 * @param int $end_station_id
 * @param int $order_id
 * @param int $end_time
 */
function sendRouteVoucher($uid, $title, $discount = 10, $start_station_id = 0, $end_station_id = 0, $order_id = 0, $end_time = 0) {
    sendVoucher($uid, $title, $discount, 1, 0, $start_station_id, $end_station_id, $order_id, $end_time);
}

/**
 * 首次关注送券
 * @param int $uid
 * @param string $title
 * @param int $discount
 * @param int $end_time
 */
function sendSubscribesVoucher($uid, $title, $discount = 10, $end_time = 0) {
    global $_W;
    $sql = "SELECT * FROM " . tablename('fr_cp_voucher') . " WHERE `uid` = :uid AND `uniacid` = :uniacid AND `come_from` = 1";
    $params = array(
        ":uid" => $uid,
        ":uniacid" => $_W['uniacid'],
    );
    $res = pdo_fetch($sql, $params);
    if (empty($res)) {
        for($i=0; $i < 10; $i++) {//关注送十张代金券
            sendVoucher($uid, $title, $discount, 1, 1, 0, 0, 0, $end_time);
        }
    }
}

/**
 * 首次购买送券
 * @param int $uid
 * @param string $title
 * @param int $discount
 * @param int $end_time
 */
function sendFirstBuyingVoucher($uid, $title, $discount = 10, $order_id = 0, $end_time = 0) {
    global $_W;
    $sql = "SELECT * FROM " . tablename('fr_cp_voucher') . " WHERE uid = :uid AND uniacid = :uniacid AND come_from = 2";
    $params = array(
        ":uid" => $uid,
        ":uniacid" => $_W['uniacid'],
    );
    $res = pdo_fetch($sql, $params);
    if (empty($res)) {
        sendVoucher($uid, $title, $discount, 1, 2, 0, 0, $order_id, $end_time);
    }
}

/**
 * 获取用户能使用的代金券
 * @param int $uid
 * @param int $start_station_id
 * @param int $end_station_id
 * @param int $shift_id
 */
function getVoucher($uid, $start_station_id, $end_station_id, $shift_id) {
    global $_W;
    $shift = getShiftInfo($start_station_id, $end_station_id, $shift_id);
    if (empty($shift) || $shift['enable_vouchers'] == 0) {
        return FALSE;
    }
    $tablename = tablename("fr_cp_voucher");
    $voucher = array();
    $params = array(
        ':uid' => $uid,
        ':uniacid' => $_W['uniacid']
    );
    if ($shift['enable_vouchers'] == 1) {
        $route = getRouteData($shift['route_id']);
        $start_ids = getShiftStationId($shift_id);
        $start_ids[] = $route['start_station_id'];
        $start_ids = array_unique($start_ids);
        if (!empty($start_ids)) {
            $start_id_str = implode(",", $start_ids);
            $sql = "SELECT * FROM {$tablename} WHERE `start_station_id` IN ({$start_id_str}) AND `end_station_id` = '{$end_station_id}' AND status = 0 AND uid = :uid AND uniacid = :uniacid  ORDER BY discount ASC";
            $voucher = pdo_fetchall($sql, $params);//限制券
        }
    }
    $sql = "SELECT * FROM {$tablename} WHERE `start_station_id` = 0 AND `end_station_id` = 0 AND status = 0 AND uid = :uid AND uniacid = :uniacid ORDER BY discount ASC";
    
    $voucher2 = pdo_fetchall($sql, $params);//通用券
    $voucher = array_merge($voucher, $voucher2);
    if (!empty($voucher)) {
        foreach ($voucher as $key => $item) {
            $voucher[$key]['name'] = $item['discount'] > 0 ? "{$item['discount']}元代金券" : "免费券";
            $voucher[$key]['tips'] = $item['start_station_id'] > 0 ? "限制路线使用" : "通用";
        }
    }
    return $voucher;
}

/**
 * 获取代金券详情
 * @global type $_W
 * @param type $id
 * @param type $uid
 * @return boolean
 */
function getVoucherById($id, $uid) {
    global $_W;
    if (empty($id) || empty($uid)) {
        return false;
    }
    $id = intval($id);
    $uniacid = $_W['uniacid'];
    $sql = "SELECT * FROM ". tablename('fr_cp_voucher') . " WHERE id = :id AND uniacid = :uniacid AND uid = :uid";
    $params = array(":id" => $id, ":uid" => $uid, ":uniacid" => $uniacid);
    $item = pdo_fetch($sql, $params);
    if (!empty($item)) {
        $item['name'] = $item['discount'] > 0 ? "{$item['discount']}元代金券" : "免费券";
    }
    return $item;
}

/**
 * 改变代金券状态
 * @global type $_W
 * @param type $id
 * @param type $uid
 * @param type $status
 */
function updateVoucherStatus($id, $uid, $status = 0) {
    global $_W;
    $id = intval($id);
    $uniacid = $_W['uniacid'];
    $params = array("id" => $id, "uid" => $uid, "uniacid" => $uniacid);
    $data = array('status' => $status);
    fr_update('voucher', $data, $params);
}

/**
 * 删除指定订单送的代金券
 * @global type $_W
 * @param type $order_id
 * @param type $uid
 */
function deleteVoucherByOrderId($order_id, $uid) {
    global $_W;
    $order_id = intval($order_id);
    $uniacid = $_W['uniacid'];
    $params = array("order_id" => $order_id, "uid" => $uid, "uniacid" => $uniacid);
    fr_delete('voucher', $params);
}

/**
 * 获取班次的所有配客点
 * @global type $_W
 * @param type $shift_id
 */
function getShiftStation($shift_id) {
    $result = getAllData("shift_station", " AND shift_id = '{$shift_id}'");
    return $result;
}

/**
 * 获取班次的所有配客点ID
 * @param type $shift_id
 * @return type
 */
function getShiftStationId($shift_id) {
    $result = getShiftStation($shift_id);
    $station_ids = array();
    foreach ($result as $item) {
        $station_ids[] = $item['station_id'];
    }
    return $station_ids;
}
/**
 * 获取班次的所有下客点
 * @global type $_W
 * @param type $shift_id
 */
function getShiftEndStation($shift_id) {
    $result = getAllData("shift_end_station", " AND shift_id = '{$shift_id}'");
    return $result;
}

/**
 * 获取班次的所有下客点ID
 * @param type $shift_id
 * @return type
 */
function getShiftEndStationId($shift_id) {
    $result = getShiftStation($shift_id);
    $station_ids = array();
    foreach ($result as $item) {
        $station_ids[] = $item['end_station_id'];
    }
    return $station_ids;
}

/**
 * 保存搜索记录
 * @global array $_W
 * @param int $uid
 * @param int $strat_staion_id
 * @param int $end_station_id
 * @return boolean
 */
function insertSearchHistory($uid, $start_station_id, $end_station_id) {
    global $_W;
    if (empty($uid) || empty($start_station_id) || empty($end_station_id)) {
        return false;
    }
    $sql = "SELECT * FROM " . fr_table("history") . " WHERE uid = :uid AND uniacid = :uniacid AND start_station_id = :start_station_id AND end_station_id = :end_station_id";
    $params = array(
        ":uid" => $uid,
        ":uniacid" => $_W['uniacid'],
        ":start_station_id" => $start_station_id,
        ":end_station_id" => $end_station_id,
    );
    $row = pdo_fetch($sql, $params);
    $insertData = array(
        "uniacid" => $_W['uniacid'],
        "uid" => $uid,
        "start_station_id" => $start_station_id,
        "end_station_id" => $end_station_id,
        "createtime" => TIMESTAMP,
    );
    if (!empty($row)) {
        $insertData['id'] = $row['id'];
    }
    fr_insert('history', $insertData, true);
}

/**
 * 清除历史记录
 * @global array $_W
 * @param int $day
 * @return boolean
 */
function cleanSearchHistory($day = NULL) {
    global $_W;
    if (intval($day) === 0) {
        return true;
    }
    if ($day === NULL) {
        $time = TIMESTAMP - (30 * 24 * 60 * 60);//清除30天前的数据
    }else{
        $time = TIMESTAMP - ($day * 24 * 60 * 60);
    }
    
    fr_delete('history', "createtime <= '{$time}' AND uniacid = '{$_W['uniacid']}'");
}
/**
 * 清除历史记录
 * @global array $_W
 * @param int $day
 * @return boolean
 */
function cleanSearchHistoryByUid($uid) {
    global $_W;
    if (empty($uid)) {
        return true;
    }
    
    fr_delete('history', "uid = '{$uid}' AND uniacid = '{$_W['uniacid']}'");
}

/**
 * 自动取消未付款订单
 * @global type $_W
 * @param type $time
 * @return boolean
 */
function auto_cancel_order($time) {
    global $_W;
    if ($time <= 0) {
        return true;
    }
    $sql = "SELECT id FROM " . fr_table("shift") . " WHERE uniacid = :uniacid AND method = :method";
    $params = array(":uniacid" => $_W['uniacid'], ":method" => 0);
    $res = pdo_fetchall($sql, $params, 'id');
    if (empty($res)) {
        return true;
    }
    $shift_id = implode(",", array_keys($res));
    $cancel_time = TIMESTAMP - $time * 60;//分钟
    $condition = "uniacid = '{$_W['uniacid']}' AND shift_id IN({$shift_id}) AND createtime <= '$cancel_time' AND status = 0";
    fr_update("order", array('status' => '-1'), $condition);
}

function action_log($sn = '', $action = "", $content = "") {
    global $_W;
    $log_action = array(
        //-- 操作类型
        'add' => '添加', 'delete' => '删除', 'edit' => '编辑',
        'setup' => '设置', 'sell' => '售票', 'return' => '退票',
        'batch_delete' => '批量删除', 'batch_add' => '批量添加', 'batch_upload' => '批量上传', 'batch_edit' => '批量编辑', 'wirteoff' => "核销",
        //-- 操作内容
        'area' => '地区', 'station' => '车站', 'route' => '线路', 'shift' => '班次', 'order' => '订单', 
        'user' => '用户', 'addon' => '附加项', 'voucher' => '优惠券', 'sms_log' => '短信日志', 'action' => '操作日志', 
        'vehicle' => '车辆', 'notice' => '通知设置', 'message' => "通知内容", 'fee' => "退票/改签设置", 'shift_time' => "发车时间设置", 'shift_vehicle' => "班次司机安排"
    );
    
    $log_info = $log_action[$action] . $log_action[$content] . ': '. addslashes($sn);
    $data = array(
        "createtime" => TIMESTAMP,
        "uid" => $_W['uid'],
        "action_info" => stripslashes($log_info),
        "ip" => getip(),
        "uniacid" => $_W['uniacid']
    );
    fr_insert("action", $data);
}

/**
 * 删除地区相关信息
 * @param type $area_id
 * @return boolean
 */
function deleteArea($area_id) {
    $data = getDataById("area", $area_id);
    if (empty($data)) {
        return false;
    }
    $result = getAllData("area", " AND pid = '{$area_id}'");
    if (!empty($result)) {
        foreach ($result as $area) {
            deleteArea($area['id']);
        }
    }
    if ($data['lv'] == 1) {
        $where = " AND province_id = '{$area_id}'";
    }elseif ($data['lv'] == 2) {
        $where = " AND city_id = '{$area_id}'";
    }else{
        $where = " AND district_id = '{$area_id}'";
    }
    $stationall = getAllData("station", $where);
    foreach ($stationall as $station) {
        deleteStation($station['id']);
    }
    fr_delete("area", array('id' => $area_id));
    //保存操作日志
    action_log($data['name'] . "及相关的车站，路线，班次，订单信息", "delete", "area");
    return true;
}

/**
 * 删除车站相关信息
 * @param type $station_id
 */
function deleteStation($station_id) {
    $data = getDataById("station", $station_id);
    if (empty($data)) {
        return false;
    }
    $routeall = getAllData("route", " AND (start_station_id = '{$station_id}' OR end_station_id = '{$station_id}')");
    fr_delete("station", array('id' => $station_id));
    foreach($routeall AS $route) {
        deleteRoute($route['id']);
    }
    //保存操作日志
    action_log($data['name'] . "及相关的路线，班次，订单信息", "delete", "station");
}
/**
 * 删除路线相关信息
 * @param type $route_id
 */
function deleteRoute($route_id) {
    $data = getDataById("route", $route_id);
    if (empty($data)) {
        return false;
    }
    fr_delete("route", array('id' => $route_id));
    $shiftall = getAllData("shift", " AND route_id = '{$route_id}'");
    foreach($shiftall AS $shift) {
        deleteShift($shift['id']);
    }
    
    //保存操作日志
    action_log($data['name'] . "及相关的班次，订单信息", "delete", "route");
}

function deleteShift($shift_id) {
    $data = getDataById("shift", $shift_id);
    if (empty($data)) {
        return false;
    }
    $route = getDataById('route', $data['route_id']);
    fr_delete("shift", array('id' => $shift_id));
    fr_delete("shift_station", array('shift_id' => $shift_id));
    fr_delete("order", array('shift_id' => $shift_id));
    fr_delete("shift_end_station", array('shift_id' => $shift_id));
    fr_delete("shift_vehicle", array('shift_id' => $shift_id));
    //保存操作日志
    action_log($route['name']  . "下的".  timeToStr(GetMkTime("1990-12-19 " . $data['time']), "H:i") . "班次及相关的订单信息", "delete", "shift");
}


function getTopAreaName($area_id) {
    $area = getRow("area", " AND id = '{$area_id}'");
    if ($area['pid'] == 0) {
        return $area['name'];
    }else{
        return getTopAreaName($area['pid']);
    }
}

function getAreaByName($area_name) {
    return getRow("area", " AND name = '{$area_name}'");
}

function getRecommendRoute(&$index_route = array()) {
    global $_W;
    $uniacid = $_W['uniacid'];
    $sql = "SELECT r.start_station_id, r.end_station_id, r.recommend FROM " . fr_table('shift') . " AS s "
            . "LEFT JOIN " . fr_table('route') . " AS r ON s.route_id = r.id "
            . "WHERE r.recommend = 1 AND s.uniacid = '{$uniacid}'";
    $index_res0 = pdo_fetchall($sql);

    //dump($index_res0);die;
    foreach($index_res0 AS $val) {
        if ($val['recommend'] == 1) {
            if (!isset($index_route[$val['start_station_id'] . "-" . $val['end_station_id']])) {
                $index_route[$val['start_station_id'] . "-" . $val['end_station_id']] = array(
                    "start_station" => getDataById('station', $val['start_station_id']),
                    "end_station" => getDataById('station', $val['end_station_id']),
                );
            }
        }
    }
    $sql = "SELECT ss.station_id, ss.recommend AS ss_recommend, r.start_station_id, r.end_station_id, r.recommend FROM " . fr_table('shift_station') . " AS ss "
            . "LEFT JOIN " . fr_table('shift') . " AS s ON ss.shift_id = s.id "
            . "LEFT JOIN " . fr_table('route') . " AS r ON s.route_id = r.id "
            . "WHERE (ss.recommend = 1 OR r.recommend = 1) AND s.uniacid = '{$uniacid}'";// AND s.time > " . TIMESTAMP
    $index_res = pdo_fetchall($sql);

    //dump($index_res);die;
    foreach($index_res AS $val) {
        if ($val['recommend'] == 1) {
            if (!isset($index_route[$val['start_station_id'] . "-" . $val['end_station_id']])) {
                $index_route[$val['start_station_id'] . "-" . $val['end_station_id']] = array(
                    "start_station" => getDataById('station', $val['start_station_id']),
                    "end_station" => getDataById('station', $val['end_station_id']),
                );
            }
        }
        if ($val['ss_recommend'] == 1) {
            if (!isset($index_route[$val['station_id'] . "-" . $val['end_station_id']])) {
                $index_route[$val['station_id'] . "-" . $val['end_station_id']] = array(
                    "start_station" => getDataById('station', $val['station_id']),
                    "end_station" => getDataById('station', $val['end_station_id']),
                );
            }
        }
    }
    $sql = "SELECT ses.end_station_id AS ses_end_station_id, ses.recommend AS ses_recommend, r.start_station_id, r.end_station_id, r.recommend FROM " . fr_table('shift_end_station') . " AS ses "
            . "LEFT JOIN " . fr_table('shift') . " AS s ON ses.shift_id = s.id "
            . "LEFT JOIN " . fr_table('route') . " AS r ON s.route_id = r.id "
            . "WHERE (ses.recommend = 1 OR r.recommend = 1) AND s.uniacid = '{$uniacid}' AND ses.uniacid = '{$uniacid}' AND r.uniacid = '{$uniacid}'";// AND s.time > " . TIMESTAMP;
    $index_res2 = pdo_fetchall($sql);
    //dump($index_res2);die;
    foreach($index_res2 AS $val) {
        if ($val['recommend'] == 1) {
            if (!isset($index_route[$val['start_station_id'] . "-" . $val['end_station_id']])) {
                $index_route[$val['start_station_id'] . "-" . $val['end_station_id']] = array(
                    "start_station" => getDataById('station', $val['start_station_id']),
                    "end_station" => getDataById('station', $val['end_station_id']),
                );
            }
        }
        if ($val['ses_recommend'] == 1) {
            if (!isset($index_route[$val['start_station_id'] . "-" . $val['ses_end_station_id']])) {
                $index_route[$val['start_station_id'] . "-" . $val['ses_end_station_id']] = array(
                    "start_station" => getDataById('station', $val['start_station_id']),
                    "end_station" => getDataById('station', $val['ses_end_station_id']),
                );
            }
        }
    }
    $sql = "SELECT ses.end_station_id, ses.recommend AS ses_recommend, ss.station_id AS start_station_id, ss.recommend AS ss_recommend FROM " . fr_table('shift_end_station') . " AS ses "
            . "LEFT JOIN " . fr_table('shift_station') . " AS ss ON ses.shift_id = ss.shift_id "
            . "LEFT JOIN " . fr_table('shift') . " AS s ON ss.shift_id = s.id "
            . "WHERE (ses.recommend = 1 AND ss.recommend = 1) AND ss.uniacid = '{$uniacid}' AND ses.uniacid = '{$uniacid}'";// AND s.time > " . TIMESTAMP;
    $index_res3 = pdo_fetchall($sql);
    foreach($index_res3 AS $val) {
        if ($val['ses_recommend'] == 1 && $val['ss_recommend'] == 1) {
            if (!isset($index_route[$val['start_station_id'] . "-" . $val['end_station_id']])) {
                $index_route[$val['start_station_id'] . "-" . $val['end_station_id']] = array(
                    "start_station" => getDataById('station', $val['start_station_id']),
                    "end_station" => getDataById('station', $val['end_station_id']),
                );
            }
        }
    }
    return $index_route;
}

function cleanParentOrder() {
    global $_W;
    $params = array(
        "openid" => $_W['openid'],
        "seat_number" => 99999,
    );
    fr_delete("order", $params);
}

/**
 * 
 * @param type $order_info
 * @param type $type 0下单通知，1付款通知
 */
function notificationManager($order_info, $type = 0) {
    global $fr_cp_settings;
    if (!in_array($type, array(0, 1))) {
        return false;
    }
    $route_id = getDataById("shift", $order_info['shift_id'], "route_id");
    if ($type == 1) {
        $result = getAllData("notice", " AND is_pay = 1 AND (route_id = 0 OR route_id = '{$route_id}')");
    }else{
        $result = getAllData("notice", " AND is_add = 1 AND (route_id = 0 OR route_id = '{$route_id}')");
    }
    
    if (empty($result)) {
        return false;
    }
    
    foreach ($result as $row) {
        $message = getDataById("message", $row['msg_id']);
        $sms_content = replaceSmsContent($order_info, $message['sms_content']);
        if ($row['type'] == 0) { //微信通知
            if ($row['openid'] == '') {continue;}
            if ($message['template_id'] != '') {
                sendNotice(NULL, $row['openid'], $sms_content);
            }else{
                sendNotice(NULL, $row['openid'], $sms_content);
            }
        }else{//短信通知
            if (!validMobile($row['mobile'])) {continue;}
            if ($fr_cp_settings['sms_type'] == 1) {
                load()->model('cloud');
                cloud_prepare();
                cloud_sms_send($row['mobile'], $sms_content);
            }    
            if ($fr_cp_settings['sms_type'] == 2) {
                sendSms($row['mobile'], $sms_content, $fr_cp_settings);
            }
        }
        
    }
}

function replaceSmsContent($order_info, $message) {
    $replace_content = array(
        "{LUXIAN}" => "路线", "{SHIJIAN}" => "发车时间", "{PIAO}" => "购票数", "{SEAT_NUMBER}" => "座位号", "{ORDER_SN}" => "订单号",
        "{CHEPAI}" => "车牌号", "{NAME}" => "购票人姓名",  "{PHONE}" => "购票人手机号码", "{IDCARD}" => "购票人身份证", 
        "{STATUS}" => "订单状态", "{CREATETIME}" => "下单时间",
    );
    $status_text = array('0' => '进行中', '1' => '已完成', '-1' => '已取消', '2' => '已退票', '3' => '改签');
    $shift = getShiftInfo($order_info["start_station_id"], $order_info['end_station_id'], $order_info['shift_id']);
    $search = array_keys($replace_content);
    $shift_vehicle = getShiftVehicle($order_info['shift_id'], getDateStr($order_info['departure_time']));
    $replace = array(
                    "{$shift['start_station']['name']}至{$shift['end_station']['name']}",
                    timeToStr($order_info['departure_time'], 'Y年m月d日 H:i'),
                    $order_info['number'],
                    $order_info['seat_number'],
                    $order_info['order_sn'],
                    $shift_vehicle['license_plate'],
                    $order_info['name'],
                    $order_info['phone'],
                    $order_info['idcard'],
                    $status_text[$order_info['status']],
                    timeToStr($order_info['createtime'], 'Y-m-d H:i:s'),
                );
    return str_replace($search, $replace, $message);
}

function genRule($content = "fr_cp_writeoff_", $name = "管理员核销车票规则") {
    global $_W, $fr_cp_settings;
    if ($fr_cp_settings['writeoff'] != '') {
        $content = "^" . $fr_cp_settings['writeoff'];
    }else{
        $content = "^" . $content;
    }
    $rule_sql = "SELECT * FROM ".tablename('rule')." WHERE uniacid = :uniacid AND module = :module AND name = :name LIMIT 1";
    $rule_params = array(
        ':uniacid' => $_W['uniacid'],
        ':name' => $name,
        ':module' => "fr_cp",
    );
    $rule = pdo_fetch($rule_sql, $rule_params);
    if (empty($rule)) {
        $rule = array(
                'uniacid' => $_W['uniacid'],
                'name' => $name,
                'module' => "fr_cp",
                'status' => 1,
                'displayorder' => 255,
        );
        
        pdo_insert('rule', $rule);
        $rid = pdo_insertid();
    }else{
        $rid = $rule['id'];
    }
    $rule_k_sql = "SELECT * FROM ".tablename('rule_keyword')." WHERE uniacid = :uniacid AND module = :module AND content = :content AND rid = :rid LIMIT 1";
    $rule_k_params = array(
        ':uniacid' => $_W['uniacid'],
        ':content' => $content,
        ':module' => "fr_cp",
        ':rid' => $rid,
    );
    $rule_keyword = pdo_fetch($rule_k_sql, $rule_k_params);
    if (empty($rule_keyword)) {
        $krow = array(
                'rid' => $rid,
                'uniacid' => $_W['uniacid'],
                'module' => $rule['module'],
                'status' => $rule['status'],
                'displayorder' => $rule['displayorder'],
                'type' => 3,
                'content' => $content,
        );
        pdo_insert('rule_keyword', $krow);
    }
}

function genOrderQrcode($order_id) {
    global $fr_cp_settings;
    genRule();
    $order = getDataById("order", $order_id);
    if (empty($order) || !in_array($order['status'], array(1, 3))) {//非已完成的订单或改签的订单
        return false;
    }
    if ($fr_cp_settings['writeoff'] != '') {
        $writeoff = $fr_cp_settings['writeoff'];
    }
    $keyword = $writeoff. $order['order_sn'];
    return genQrcode($keyword, "车票系统核销用的二维码". $order_id);
}
function genQrcode($keyword = '', $title = 'fr_cp') {
    global $_W;
    if (empty($keyword)) {
        return FALSE;
    }
    $sql = "SELECT * FROM ".tablename('qrcode')." WHERE uniacid = :uniacid AND keyword = :keyword LIMIT 1";
    $params = array(':uniacid' => $_W['uniacid'], ':keyword' => "{$keyword}");
    $qrcode = pdo_fetch($sql, $params);
    if (!empty($qrcode)) {
        $qrcode_img_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($qrcode['ticket']);
        $qrcode['endtime'] = $qrcode['createtime'] + $qrcode['expire'];
        if (TIMESTAMP > $qrcode['endtime']) {
            if (!empty($qrcode)) {
                pdo_delete('qrcode', array('id' => $qrcode['id'], 'uniacid' => $_W['uniacid']));
                pdo_delete('qrcode_stat',array('qid' => $qrcode['id'], 'uniacid' => $_W['uniacid']));
                $not_qrcode = true;
            }
        }
    }else{
        $not_qrcode = true;
    }
    if ($not_qrcode) {
        $acid = $_W['account']['acid'];
        if (empty($acid) && $_W['uniacid']) {
            $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
        }
        $uniacccount = WeAccount::create($acid);
        if (empty($uniacccount)) {
            return false;
        }
        $qrcid = pdo_fetchcolumn("SELECT qrcid FROM ".tablename('qrcode')." WHERE acid = :acid AND model = '1' ORDER BY qrcid DESC LIMIT 1", array(':acid' => $acid));
        $barcode = array(
            'expire_seconds' =>30 * 60,
            'action_name' => 'QR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_id' => !empty($qrcid) ? ($qrcid+1) : 100001
                ),
            ),
        );
        $result = $uniacccount->barCodeCreateDisposable($barcode);
        if(!is_error($result)) {
            $insert = array(
                    'uniacid' => $_W['uniacid'],
                    'acid' => $acid,
                    'qrcid' => $barcode['action_info']['scene']['scene_id'],
                    'scene_str' => $barcode['action_info']['scene']['scene_str'],
                    'keyword' => $keyword,
                    'name' => $title,
                    'model' => 1,
                    'ticket' => $result['ticket'],
                    'url' => $result['url'],
                    'expire' => $result['expire_seconds'],
                    'createtime' => TIMESTAMP,
                    'status' => '1',
                    'type' => 'scene',
            );
            pdo_insert('qrcode', $insert);
            $qrcode_img_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($result['ticket']);
        } else {
            fr_log($result['message']);
            return false;
        }
    }
    
    return $qrcode_img_url;
}

/**
 * 检查是否有核销权限
 * @param type $openid
 * @param type $route_id
 * @return type
 */
function checkWirteOffRoute($openid, $route_id = 0) {
    $where = " AND openid = '{$openid}' AND (route_id = 0 OR route_id = '{$route_id}') ";
    $count = getCol("writeoff", "count(*)", $where);
    return $count > 0 ? true : false;
}

/**
 * 判断订单是否可退票
 * @param type $order
 * @return boolean
 */
function checkOrderRefund($order) {
    if (empty($order) || !in_array($order['status'], array(1, 3)) || $order['check_ticket'] == 1) {
        return false;
    }
    
    $shift = getDataById('shift', $order['shift_id']);
    if ($shift['refund_ticket'] != 1) {
        return false;
    }
    $departure_time = ($order['departure_time'] - TIMESTAMP) / 60;//离发车时间，单位：分钟
    if ($departure_time <= 0) {
        return false;
    }
    $fee_list = getFee($type = 0);
    $return = array();
    foreach ($fee_list AS $fee) {
        if ($departure_time > $fee['time']) {
            $return = $fee;
            break;
        }
    }
    if (empty($return)) {
        return false;
    }
    return $return;
}
function checkOrderChanged($order) {
    if (empty($order) || !in_array($order['status'], array(1)) || $order['check_ticket'] == 1) {
        return false;
    }
    $departure_time = ($order['departure_time'] - TIMESTAMP) / 60;//离发车时间，单位：分钟
    if ($departure_time <= 0) {
        return false;
    }
    $fee_list = getFee($type = 1);
    $return = array();
    foreach ($fee_list AS $fee) {
        if ($departure_time > $fee['time']) {
            $return = $fee;
            break;
        }
    }
    if (empty($return)) {
        return false;
    }
    return $return;
}
function getFee($type = 0) {
    $result = getAllData("fee", " AND type = '{$type}'", " time DESC");
    return $result;
}
function getAdminName($uid) {
    static $admin_names = array();
    if (!isset($admin_names[$uid])) {
        $sql = "SELECT username FROM " . tablename("users") . " WHERE uid = '{$uid}'";
        $admin_names[$uid] = pdo_fetchcolumn($sql);
    }
    return $admin_names[$uid];
}

function getShiftVehicle($shift_id, $work_date) {
    $res = getRow('shift_vehicle', " AND shift_id = '{$shift_id}' AND work_date = '{$work_date}'");
    if (empty($res)) {
        return array();
    }
    return getDataById("vehicle", $res['vehicle_id']);
}