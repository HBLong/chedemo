<?php
/**
 * 发送短信
 * @param type $mobile
 * @param type $content
 * @param type $config
 * @return boolean
 */

function sendSms($mobile, $content, $config = array()) {
    global $_W;
    if (empty($mobile) || empty($content)) {
        return false;
    }
    if (is_array($mobile)) {
        $mobile = implode(",", $mobile);
    }
    if (empty($config)) {
        $config['sms_userid'] = '13243';
        $config['sms_account'] = 'k1615';
        $config['sms_password'] = '123456';
    }
    $post_data = array();
    $post_data['userid'] = $config['sms_userid'];
    $post_data['account'] = $config['sms_account'];
    $post_data['password'] = $config['sms_password'];
    $post_data['content'] = $content;
    $post_data['mobile'] = $mobile;
    $post_data['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
//    $url='http://www.duanxin10086.com/sms.aspx?action=send';
    $url='http://webservice.duanxin10086.com/enterprise/db2.0/sms.ashx?action=send';
    $o = '';
    foreach ($post_data as $k=>$v)
    {
       $o.="$k=".urlencode($v).'&'; //短信内容需要用urlencode编码下
    }
    $post_data_str = substr($o,0,-1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_str);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
    $result = curl_exec($ch);
    $xml = xml_parser($result);
    $sms_log = array(
        'uniacid' => $_W['uniacid'],
        'mobile' => $mobile,
        'content' => $content,
        'createtime' => TIMESTAMP,
    );
    if ($xml !== false) {
        if ($xml->returnstatus == 'Success') {
            $sms_log['status'] = 1;
            fr_insert('sms_log', $sms_log);
            return TRUE;
        }else{
            $sms_log['status'] = 0;
            $sms_log['error_msg'] = $xml->message;
            fr_insert('sms_log', $sms_log);
            return false;
        }
    }else{
        $sms_log['status'] = 0;
        $sms_log['error_msg'] = $result;
        fr_insert('sms_log', $sms_log);
        return false;
    }
}
function xml_parser($str){   
    $xml_parser = xml_parser_create();   
    if(!xml_parse($xml_parser,$str,true)){   
        xml_parser_free($xml_parser);   
        return false;   
    }else {   
        return simplexml_load_string($str);   
    }   
}   


function sendVerify($mobile, $verifycode, $config) {
    include MODULE_ROOT . "/inc/alidayu/TopSdk.php";
    $tmp_sms_param = explode("\n", $config['sms_param']);
    $sms_param = array();
    foreach ($tmp_sms_param as $param) {
        $tmp_param = explode(":", $param);
        if (count($tmp_param) == 2 && validNumberLetter($tmp_param[0])) {
            $sms_param[trim($tmp_param[0])] = trim($tmp_param[1]);
        }
    }
    $sms_param['code'] = $verifycode;
    
    $c = new TopClient;
    $c->appkey = $config['key'];
    $c->secretKey = $config['secret'];
    $req = new AlibabaAliqinFcSmsNumSendRequest;
//    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($config['free_sign']);
    $req->setSmsParam(json_encode($sms_param));
    $req->setRecNum($mobile);
    $req->setSmsTemplateCode($config['template_code']);
    $resp = $c->execute($req);
    $result = simplexml_obj2array($resp);
    if (isset($result['result']) && $result['result']['success'] == 'true') {
        return true;
    }else{
        return false;
    }
}

// XML转换成数组
function simplexml_obj2array($obj) {
    if (is_object($obj)) {
        $result = array();
        foreach ((array)$obj as $key => $item) {
            $result[$key] = simplexml_obj2array($item);
        }
        return $result;
    }
    return $obj;
}

//云打印

function print_ticket($result, $fr_cp_settings) {
    global $_W;
    if (empty($fr_cp_settings['print_type'])) {
        return true;
    }
    //load()->func('communication');
    $msg = ""; //打印内容
    $msg .= "班次：{$result['start_station']} 至 {$result['end_station']}\r\n";
    $msg .= "发车时间：{$result['datetime']}\r\n";
    $msg .= "姓名：{$result['name']}\r\n";
    $msg .= "电话：{$result['phone']}\r\n";
    $msg .= "身份证：{$result['idcard']}\r\n";
    $msg .= "购票数：{$result['number']}\r\n";
    $msg .= "代金券：{$result['voucher']}\r\n";
    $msg .= "订单总额：{$result['price']}\r\n";
    $msg .= "附加项：\r\n";
    $msg .= "{$result['addons']}\r\n";
    $msg .= "=======================\r\n";
    $msg .= $_W['account']['name'];
    
    $apiKey       = $fr_cp_settings['print_apiKey'];//apiKey
    $mKey         = $fr_cp_settings['print_mKey'];//秘钥
    $partner      = $fr_cp_settings['print_partner'];//用户id
    $machine_code = $fr_cp_settings['print_machine_code'];//打印机终端号
    
    $params = array(
        'partner' => $partner,
        'machine_code' => $machine_code,
        'time' => TIMESTAMP
    );
    $sign = generateSign($params,$apiKey,$mKey);

    $params['sign'] = $sign;
    $params['content'] = $msg;

  
    $url = 'open.10ss.net:8888';//接口端点

    $p = '';
    foreach ($params as $k => $v) {
        $p .= $k.'='.$v.'&';
    }
    $data = rtrim($p, '&');
    
    return liansuo_post($url, $data);
//    $res = ihttp_post($url, $data);
//    if (is_error($res)) {
//        load()->func("logging");
//        logging_run($res['message'], 'error', 'fr_cp_print');
//        return false;
//    }else{
//        return true;
//    }
}
function liansuo_post($url,$data){ // 模拟提交数据函数
    load()->func("logging");
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
           
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        $error = 'Errno'.curl_error($curl);
        logging_run($error, 'error', 'fr_cp_print');
       return false;
    }
    curl_close($curl); // 关键CURL会话
    //logging_run($tmpInfo, 'success', 'fr_cp_print');
    return true; // 返回数据
}    

function generateSign($params, $apiKey, $msign) {
    //所有请求参数按照字母先后顺序排
    ksort($params);
    //定义字符串开始所包括的字符串
    $stringToBeSigned = $apiKey;
    //把所有参数名和参数值串在一起
    foreach ($params as $k => $v) {
        $stringToBeSigned .= urldecode($k.$v);
    }
    unset($k, $v);
    //定义字符串结尾所包括的字符串
    $stringToBeSigned .= $msign;
    //使用MD5进行加密，再转化成大写
    return strtoupper(md5($stringToBeSigned));
}