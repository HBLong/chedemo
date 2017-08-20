<?php
/**
 * 车票系统模块订阅器
 *
 * @author 折 翼 天 使 资 源 社 区 www.zhe yi tian shi.com
 * @url #
 */
defined('IN_IA') or exit('Access Denied');

class Fr_cpModuleReceiver extends WeModuleReceiver {
	public function receive() {
            global $_W;
            include IA_ROOT . '/addons/fr_cp/inc/function/functions.php';
            load()->model('mc');
            $fromuser = $this->message['from'];
            if (empty($fromuser)) {
                return true;
            }
            if ($this->message['msgtype'] == 'event') {
                if ($this->message['event'] == 'subscribe') {//关注送券
                    $uid = mc_openid2uid($fromuser);
                    $title = "首次关注赠";
                    sendSubscribesVoucher($uid, $title);
//                    sendNotice('', $fromuser, $title);
                }
            }
	}
}