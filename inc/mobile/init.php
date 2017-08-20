<?php
 
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['container'] = "wechat";
load()->model("mc");
$userinfo = mc_oauth_userinfo();
if (is_error($userinfo)) {
    message($userinfo['message']);
}
$fans = mc_fansinfo($userinfo['openid']);
$uni_setting = uni_setting($_W['uniacid'], array('passport'));
if(!empty($fans)) {
        $rec = array();
        $member = array();
        if(!empty($fans['uid'])){
            $member = mc_fetch($fans['uid']);
        }
        if (empty($member)) {
            if (!isset($uni_setting['passport']) || empty($uni_setting['passport']['focusreg'])) {
                $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
                $data = array(
                        'uniacid' => $_W['uniacid'],
                        'email' => md5($userinfo['openid']).'@we7.cc',
                        'salt' => random(8),
                        'groupid' => $default_groupid,
                        'createtime' => TIMESTAMP,
                );
                $data['password'] = md5($userinfo['openid'] . $data['salt'] . $_W['config']['setting']['authkey']);
                pdo_insert('mc_members', $data);
                $rec['uid'] = pdo_insertid();
            }
        }

        if(!empty($rec)){
                pdo_update('mc_mapping_fans', $rec, array(
                        'acid' => $_W['acid'],
                        'openid' => $userinfo['openid'],
                        'uniacid' => $_W['uniacid']
                ));
        }else{
            $rec = $member;
        }
} else {
        $rec = array();
        $rec['acid'] = $_W['acid'];
        $rec['uniacid'] = $_W['uniacid'];
        $rec['uid'] = 0;
        $rec['openid'] = $userinfo['openid'];
        $rec['salt'] = random(8);
        if (!isset($uni_setting['passport']) || empty($uni_setting['passport']['focusreg'])) {
               $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
               $data = array(
                        'uniacid' => $_W['uniacid'],
                        'email' => md5($userinfo['openid']).'@we7.cc',
                        'salt' => random(8),
                        'groupid' => $default_groupid,
                        'createtime' => TIMESTAMP,
                );
                $data['password'] = md5($userinfo['openid'] . $data['salt'] . $_W['config']['setting']['authkey']);
                pdo_insert('mc_members', $data);
                $rec['uid'] = pdo_insertid();
        }
        pdo_insert('mc_mapping_fans', $rec);
}
_mc_login(array('uid' => intval($rec['uid'])));

if ($_W['member']['uid'] > 0) {
    $sql = "SELECT * FROM ". fr_table('user') . " WHERE uid = :uid AND uniacid = :uniacid";
    $params = array(":uid" => $_W['member']['uid'], ":uniacid" => $_W['uniacid']);
    $profile = pdo_fetch($sql, $params);
    if (empty($profile)) {
        $profile = array(
            'uniacid' => $_W['uniacid'],
            'uid' => $_W['member']['uid'],
            'name' => $_W['member']['realname'],
            'sex' => $_W['member']['gender'],
            'phone' => $_W['member']['mobile'],
        );
        fr_insert('user', $profile);
    }
}else{
    message("抱歉，您需要先登录才能使用此功能", __buildSiteUrl(url('auth/login')));
}