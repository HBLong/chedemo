<?php

//折D翼D天D使D资D源D社D区D破D解 www.suibianlu.com
defined('IN_IA') or die('Access Denied');
class Fr_cpModuleSite extends WeModuleSite
{
	public function payResult($res)
	{
		global $_W;
		include MODULE_ROOT . '/inc/common.php';
		if ($res['result'] == 'success' && $res['from'] == 'notify') {
			$this->module['config']['sms_mobile'] = explode("\n", $this->module['config']['sms_mobile']);
			$this->module['config']['yupiao'] = explode("\n", $this->module['config']['yupiao']);
			order_paid($res['tid'], $this->module['config']);
		}
		if ($res['from'] == 'return') {
			if ($res['result'] == 'success') {
				message('支付成功！', __MURL('order', array('act' => 'info', 'order_id' => $res['tid'])), 'success');
			} else {
				message('支付失败！', __MURL('order', array('act' => 'info', 'order_id' => $res['tid'])), 'error');
			}
		}
	}
	protected function pay($params = array(), $mine = array())
	{
		global $_W;
		if (!$this->inMobile) {
			message('支付功能只能在手机上使用');
		}
		$params['module'] = $this->module['name'];
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$pars[':module'] = $params['module'];
		$pars[':tid'] = $params['tid'];
		if ($params['fee'] <= 0) {
			$pars['from'] = 'return';
			$pars['result'] = 'success';
			$pars['type'] = '';
			$pars['tid'] = $params['tid'];
			$site = WeUtility::createModuleSite($pars[':module']);
			$method = 'payResult';
			if (method_exists($site, $method)) {
				die($site->{$method}($pars));
			}
		}
		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
		$log = pdo_fetch($sql, $pars);
		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['member']['uid'], 'module' => $this->module['name'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert('core_paylog', $log);
		}
		if ($log['status'] == '1') {
			message('这个订单已经支付成功, 不需要重复支付.');
		}
		$setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
		if (!is_array($setting['payment'])) {
			message('没有有效的支付方式, 请联系网站管理员.');
		}
		$pay = $setting['payment'];
		if (empty($_W['member']['uid'])) {
			$pay['credit']['switch'] = false;
		}
		if (!empty($pay['credit']['switch'])) {
			$credtis = mc_credit_fetch($_W['member']['uid']);
		}
		$you = 0;
		if ($pay['card']['switch'] == 2 && !empty($_W['openid'])) {
			if ($_W['card_permission'] == 1 && !empty($params['module'])) {
				$cards = pdo_fetchall('SELECT a.id,a.card_id,a.cid,b.type,b.title,b.extra,b.is_display,b.status,b.date_info FROM ' . tablename('coupon_modules') . ' AS a LEFT JOIN ' . tablename('coupon') . ' AS b ON a.cid = b.id WHERE a.acid = :acid AND a.module = :modu AND b.is_display = 1 AND b.status = 3 ORDER BY a.id DESC', array(':acid' => $_W['acid'], ':modu' => $params['module']));
				$flag = 0;
				if (!empty($cards)) {
					foreach ($cards as $temp) {
						$temp['date_info'] = iunserializer($temp['date_info']);
						if ($temp['date_info']['time_type'] == 1) {
							$starttime = strtotime($temp['date_info']['time_limit_start']);
							$endtime = strtotime($temp['date_info']['time_limit_end']);
							if (TIMESTAMP < $starttime || TIMESTAMP > $endtime) {
								continue;
							} else {
								$param = array(':acid' => $_W['acid'], ':openid' => $_W['openid'], ':card_id' => $temp['card_id']);
								$num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('coupon_record') . ' WHERE acid = :acid AND openid = :openid AND card_id = :card_id AND status = 1', $param);
								if ($num <= 0) {
									continue;
								} else {
									$flag = 1;
									$card = $temp;
									break;
								}
							}
						} else {
							$deadline = intval($temp['date_info']['deadline']);
							$limit = intval($temp['date_info']['limit']);
							$param = array(':acid' => $_W['acid'], ':openid' => $_W['openid'], ':card_id' => $temp['card_id']);
							$record = pdo_fetchall('SELECT addtime,id,code FROM ' . tablename('coupon_record') . ' WHERE acid = :acid AND openid = :openid AND card_id = :card_id AND status = 1', $param);
							if (!empty($record)) {
								foreach ($record as $li) {
									$time = strtotime(date('Y-m-d', $li['addtime']));
									$starttime = $time + $deadline * 86400;
									$endtime = $time + $deadline * 86400 + $limit * 86400;
									if (TIMESTAMP < $starttime || TIMESTAMP > $endtime) {
										continue;
									} else {
										$flag = 1;
										$card = $temp;
										break;
									}
								}
							}
							if ($flag) {
								break;
							}
						}
					}
				}
				if ($flag) {
					if ($card['type'] == 'discount') {
						$you = 1;
						$card['fee'] = sprintf("%.2f", $params['fee'] * ($card['extra'] / 100));
					} elseif ($card['type'] == 'cash') {
						$cash = iunserializer($card['extra']);
						if ($params['fee'] >= $cash['least_cost']) {
							$you = 1;
							$card['fee'] = sprintf("%.2f", $params['fee'] - $cash['reduce_cost']);
						}
					}
					load()->classs('coupon');
					$acc = new coupon($_W['acid']);
					$card_id = $card['card_id'];
					$time = TIMESTAMP;
					$randstr = random(8);
					$sign = array($card_id, $time, $randstr, $acc->account['key']);
					$signature = $acc->SignatureCard($sign);
					if (is_error($signature)) {
						$you = 0;
					}
				}
			}
		}
		if ($pay['card']['switch'] == 3 && $_W['member']['uid']) {
			$cards = array();
			if (!empty($params['module'])) {
				$cards = pdo_fetchall('SELECT a.id,a.couponid,b.type,b.title,b.discount,b.condition,b.starttime,b.endtime FROM ' . tablename('activity_coupon_modules') . ' AS a LEFT JOIN ' . tablename('activity_coupon') . ' AS b ON a.couponid = b.couponid WHERE a.uniacid = :uniacid AND a.module = :modu AND b.condition <= :condition AND b.starttime <= :time AND b.endtime >= :time  ORDER BY a.id DESC', array(':uniacid' => $_W['uniacid'], ':modu' => $params['module'], ':time' => TIMESTAMP, ':condition' => $params['fee']), 'couponid');
				if (!empty($cards)) {
					foreach ($cards as $key => &$card) {
						$has = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' WHERE uid = :uid AND uniacid = :aid AND couponid = :cid AND status = 1' . $condition, array(':uid' => $_W['member']['uid'], ':aid' => $_W['uniacid'], ':cid' => $card['couponid']));
						if ($has > 0) {
							if ($card['type'] == '1') {
								$card['fee'] = sprintf("%.2f", $params['fee'] * $card['discount']);
								$card['discount_cn'] = sprintf("%.2f", $params['fee'] * (1 - $card['discount']));
							} elseif ($card['type'] == '2') {
								$card['fee'] = sprintf("%.2f", $params['fee'] - $card['discount']);
								$card['discount_cn'] = $card['discount'];
							}
						} else {
							unset($cards[$key]);
						}
					}
				}
			}
			if (!empty($cards)) {
				$cards_str = json_encode($cards);
			}
		}
		//折 翼 天 使  资 源 社 区 破 解 www-折1翼1天1使1资1源社1区1-com
		include $this->template('paycenter');
	}
}