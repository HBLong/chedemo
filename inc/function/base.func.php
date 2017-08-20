<?php

include_once 'charset.func.php';
include_once 'time.func.php';
include_once 'other.func.php';

/**
  Validate an email address.
  Provide email address (raw input)
  Returns true if the email address has the email
  address format and the domain exists.
 */
function validEmail($email) {
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            // character not valid in local part unless 
            // local part is quoted
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }
        if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
            // domain not found in DNS
            $isValid = false;
        }
    }
    return $isValid;
}

/**
 * 验证邮编
 * @param type $str
 * @return type
 */
function validPost($post) {
    return validRegexp('/^[0-9]\d{5}$/', $val);
}

/**
 * 验证手机号码
 * @param type $val
 * @return type
 */
function validMobile($val) {
    return validRegexp('/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/', $val);
}

/**
 * 验证身份证号码
 * @param type $idCard
 * @return type
 */
function validIdCard($val) {
    return validRegexp('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $val);
}

/**
 * 验证数字
 * @param type $val
 * @return type
 */
function validNumber($val) {
    return validRegexp('/^[1-9]\\d*|0$/', $val);
}

/**
 * 验证字母
 * @param type $val
 * @return type
 */
function validLetter($val) {
    return validRegexp('/^[A-Za-z]+$/', $val);
}

/**
 * 验证数字与字母
 * @param type $val
 * @return type
 */
function validNumberLetter($val) {
    return validRegexp('/^[A-Za-z0-9]+$/', $val);
}

/**
 * 根据正则验证文本
 * @param type $val
 * @return type
 */
function validRegexp($regexp, $val) {
    return !!preg_match($regexp, $val);
}
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else {
        return $output;
    }
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '') {
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0) {
            $str .= $msg;
        }
        exit($str);
    }
}

//拼音的缓冲数组
$pinyins = Array();
/**
 *  获取拼音以gbk编码为准
 *
 * @access    public
 * @param     string  $str     字符串信息
 * @param     int     $ishead  是否取头字母
 * @param     int     $isclose 是否关闭字符串资源
 * @return    string
 */
if (!function_exists('getPinyin')) {
    function getPinyin($str, $ishead = 0, $isclose = 1, $lang = 'utf-8') {
        if ($lang == 'utf-8') {
            return _getPinyin(utf82gb($str), $ishead, $isclose);
        } else {
            return _getPinyin($str, $ishead, $isclose);
        }
    }

}

/**
 *  获取拼音信息
 *
 * @access    public
 * @param     string  $str  字符串
 * @param     int  $ishead  是否为首字母
 * @param     int  $isclose  解析后是否释放资源
 * @return    string
 */
function _getPinyin($str, $ishead = 0, $isclose = 1) {
    global $pinyins;
    $restr = '';
    $str = trim($str);
    $slen = strlen($str);
    if ($slen < 2) {
        return $str;
    }
    if (count($pinyins) == 0) {
        $fp = fopen( __DAT__ . '/pinyin.dat', 'r');
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
        }
        fclose($fp);
    }
    for ($i = 0; $i < $slen; $i++) {
        if (ord($str[$i]) > 0x80) {
            $c = $str[$i] . $str[$i + 1];
            $i++;
            if (isset($pinyins[$c])) {
                if ($ishead == 0) {
                    $restr .= $pinyins[$c];
                } else {
                    $restr .= $pinyins[$c][0];
                }
            } else {
                $restr .= "";
            }
        } else if (preg_match("/[a-z0-9]/i", $str[$i])) {
            $restr .= $str[$i];
        } else {
            $restr .= "";
        }
    }
    if ($isclose == 0) {
        unset($pinyins);
    }
    return $restr;
}

function fr_log($log, $type = 'normal', $filename = 'fr_cp') {
    load()->func("logging");
    logging_run($log, $type, $filename);
}