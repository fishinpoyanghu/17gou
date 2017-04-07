<?php
/**
 * App 相关的一些api接口
 * 
 */

class AppCtrl extends BaseCtrl {

public function code() {
    // 标准参数检查
    $base = api_check_base();
    // 得到当前登录用户
    $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
    require COMMON_PATH.'/libs/phpqrcode/phpqrcode.php';
    QRcode::png('http://www.yiqigou888.com/apps/api/www/?c=weixin&a=go_wx&invite_code='.api_get_user_invite_code($login_user['uid']),false,QR_ECLEVEL_L,5);
    }

    public function share(){
        $file = dirname(dirname(dirname(__FILE__))).'/admin/conf/share.conf.php';
        $conf = include($file);
        api_result(0, 'succ', $conf);
    }

    public function question(){
        $base = api_check_base();
        $nc_list = Factory::getMod('nc_list');
        $nc_list->setDbConf('shop', 'question');

        $ret3 = $nc_list->getDataList();
        foreach($ret3 as $k=>$v){
            if($v['title']==''){
                unset($ret3[$k]);
            }
        }
        api_result(0, 'succ',$ret3);

    }

    public function huixiao(){
        require(dirname(dirname(__FILE__)).'/mods/ip.mod.php');
        $ip = new LibIp();
        $i = get_ip();
        $ii = $ip->getlocation($i);
	api_result(0, 'succ', array('huixiao'=>'huixiao'));
        /*if(strpos($ii['country'],'美国')===false){
            api_result(0, 'succ', array('huixiao'=>'f'));
        }else{
            api_result(0, 'succ', array('huixiao'=>'huixiao'));
        }*/
    }

    public function payPage(){
        $order = gstr('order_num');
        $sign = gstr('sign');
        if($order=='' || $sign=='' || md5($order.LOGIN_KEY)!=$sign){
            echo '错误请求';
            exit;
        }
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'order');
        $where = array(
            'order_num' => $order,
            'flag' => 0
        );

        $orderInfo = $nc_list_mod->getDataList($where,array(),array(),array(),false);
        if(empty($orderInfo)){
            exit('无该订单，或者订单已经支付');
        }
        $moneyInfo = json_decode($orderInfo[0]['money_info'], true);
        if(!empty($moneyInfo['need_money'])){
            exit('余额不足，请先充值');
        }
        $this->clean_xss($order);
        $this->clean_xss($sign);
        $data = array(
            'order' => $order,
            'sign' => $sign,
            'money' => $moneyInfo['remain_use'],
        );
        Factory::getView("pay/pay_page", $data);
    }

    public function pay(){
        $order = gstr('order_num');
        $sign = gstr('sign');
        if($order=='' || $sign=='' || md5($order.LOGIN_KEY)!=$sign){
            echo '错误请求';
            exit;
        }
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'order');
        $where = array(
            'order_num' => $order,
            'flag' => 0
        );

        $orderInfo = $nc_list_mod->getDataList($where,array(),array(),array(),false);
        if(empty($orderInfo)){
            exit('无该订单，或者订单已经支付');
        }
        $moneyInfo = json_decode($orderInfo[0]['money_info'], true);
        if(!empty($moneyInfo['need_money'])){
            exit('余额不足，请先充值');
        }

        $mod = Factory::getMod('nc_pay');
        $re = $mod->paySuccess($order);
        if($re){
            $data['msg'] = '支付成功';
        }else{
            $data['msg'] = '支付失败';
        }
        Factory::getView("pay/pay_result", $data);
    }

    function clean_xss(&$string, $low = False){
        if (! is_array ( $string ))
        {
            $string = trim ( $string );
            $string = strip_tags ( $string );
            $string = htmlspecialchars ( $string );
            if ($low)
            {
                return True;
            }
            $string = str_replace ( array ('"', "\\", "'", "/", "..", "../", "./", "//" ), '', $string );
            $no = '/%0[0-8bcef]/';
            $string = preg_replace ( $no, '', $string );
            $no = '/%1[0-9a-f]/';
            $string = preg_replace ( $no, '', $string );
            $no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
            $string = preg_replace ( $no, '', $string );
            return True;
        }
        $keys = array_keys ( $string );
        foreach ( $keys as $key )
        {
            $this->clean_xss ( $string [$key] );
        }
    }
    
    public function notice(){
        $base = api_check_base();
        // 得到当前登录用户
        $page = pstr('page');
        $page = $page < 1 ? 1 : $page;
        $pub_mod = Factory::getMod('pub');

        $where = array(
            'is_del' => 0,
        );
        $pub_mod->init('shop', 'notice', 'id');
        $orderby = 'ORDER BY `ding` DESC,`time` desc';

        $point_list = $pub_mod->getRowList($where, ((int)$page -1 ) * 10, 10, $orderby);
        api_result(0, 'succ',$point_list);
    }
    
    public function checkVersion(){
        $v = pstr('version');
        $a = include (dirname(dirname(dirname(__FILE__))).'/admin/conf/version.conf.php');
        foreach($a as &$vv){
            $vv = urldecode($vv);
        }
        if($v == $a['v']){
            api_result(0, 'succ');
        }

        api_result(201, 'succ',$a);
    }

}
