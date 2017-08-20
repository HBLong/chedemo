<?php
/**
 * 车票系统模块定义
 *
 * @author 随便撸源码论坛 www.suibianlu.com
 * @url #
 */
defined('IN_IA') or exit('Access Denied');

class Fr_cpModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
                include MODULE_ROOT . '/inc/common.php';
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
                    $config = $_GPC['config'];
                    $allow_fields = array(
                        "day", "content", "content2", "vehicle_type", "recommend_size", 'templateid', 'auto_cancel_order', 'content3',
                        "sms_type", "sms_userid", "sms_account", "sms_password", "sms_mobile", "sms_content", "yupiao", "sms_buy_order",
                        "print_type", "print_partner", "print_apiKey", "print_machine_code", "print_mKey", "writeoff", "service_phone"
                    );
                    $dat = array_elements($allow_fields, $config);
                    
                    $dat['print_bg'] = $this->module['config']['print_bg'];
                    $dat['config_lable'] = $this->module['config']['config_lable'];
                    
                    //字段验证, 并获得正确的数据$dat
                    if ($this->saveSettings($dat)) {
                        message('保存配置成功', 'refresh');
                    }
		}
                $settings['day'] = $settings['day'] ? $settings['day'] : 20;
                $settings['recommend_size'] = $settings['recommend_size'] ? $settings['recommend_size'] : 5;
                $settings['auto_cancel_order'] = $settings['auto_cancel_order'] ? $settings['auto_cancel_order'] : 0;
                $settings['vehicle_type'] = $settings['vehicle_type'] ? $settings['vehicle_type'] : "1:大型坐席高级\n2:大型坐席高一级\n3:大型普通客车";
		//这里来展示设置项表单
		include $this->template('setting');
	}

}