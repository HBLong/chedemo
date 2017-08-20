<?php
/**
 * 车票系统模块处理程序
 *
 * @author 折H翼H天H使H资H源H社H区 www.zhe yi tian shi.com
 * @url #
 */
defined('IN_IA') or exit('Access Denied');

class Fr_cpModuleProcessor extends WeModuleProcessor {
	public function respond() {
            global $_W;
            $setting = $this->module['config'];
            if ($setting['writeoff'] != '') {
                $writeoff = $setting['writeoff'];
            }else{
                $writeoff = 'fr_cp_writeoff_';
            }
            if (preg_match('/^'.$writeoff.'/', $this->message['content'])) {//核销
                include MODULE_ROOT . '/inc/common.php';
                $order_sn = trim(str_replace($writeoff, "", $this->message['content']));
                if ($order_sn != '') {
                    $order_info = getRow("order", " AND order_sn = '{$order_sn}'");
                    if (empty($order_info)) {
                        return NULL;
                    }
                    $route_id = getDataById("shift", $order_info['shift_id'], "route_id");
                    if (checkWirteOffRoute($this->message['from'], $route_id)) {
                        $shift = getShiftInfo($order_info['start_station_id'], $order_info['end_station_id'], $order_info['shift_id']);
                        $order_info_text = "=======车票信息=======\n";
                        $order_info_text .= "路线：{$shift['start_station']['name']} 至 {$shift['end_station']['name']}\n";
                        $order_info_text .= "发车时间： ".timeToStr($order_info['departure_time'], 'Y-m-d H:i')."\n";
                        $order_info_text .= "座位号： {$order_info['seat_number']}\n";
                        $order_info_text .= "车票价格： ￥{$order_info['price']}\n";
                        $order_info_text .= "订单号： {$order_info['order_sn']}\n";
                        $order_info_text .= "======={$_W['account']['name']}=======\n";
                        
                        if ($order_info['check_ticket'] == 1) {
                            $nickname = getCol("writeoff", "name", " AND openid = '{$order_info['check_ticket_openid']}'");
                            return $this->respText("该车票已被管理员{$nickname}核销\n" . $order_info_text);
                        }
                        $update_data = array(
                            "check_ticket" => 1,
                            "check_ticket_time" => TIMESTAMP,
                            "check_ticket_openid" => $this->message['from'],
                        );
                        fr_update("order", $update_data, array("order_sn" => $order_sn));
                        $qrcode = pdo_fetch("SELECT * FROM ".tablename('qrcode')." WHERE uniacid = :uniacid AND keyword = :keyword LIMIT 1", array(':uniacid' => $_W['uniacid'], ':keyword' => "{$writeoff}{$order_sn}"));
                        pdo_delete('qrcode', array('id' => $qrcode['id'], 'uniacid' => $_W['uniacid']));
                        pdo_delete('qrcode_stat',array('qid' => $qrcode['id'], 'uniacid' => $_W['uniacid']));
                        return $this->respText("订单号为" . $order_sn . "的车票核销成功\n" . $order_info_text);
                    }
                }else{
                    return NULL;
                }
            }
            return NULL;
	}
}