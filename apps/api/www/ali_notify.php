<?php
/**
 * alipay支付成功后的通知接口
 */
file_put_contents('/tmp/yydb2.log',var_export($_POST,true)."\r\n",FILE_APPEND);

define('PROJECT_ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
define('APP_ROOT', dirname(dirname(__FILE__)));

// 定义app的名字
$app_name  = 'api';
// 定义app的根目录
$app_root  = PROJECT_ROOT.'/apps/api';

// 初始化应用
$GLOBALS['core_app_cfg'] = array(
    'app_name'  => $app_name,
    'app_root'  => $app_root
);

// 设置时区
date_default_timezone_set('Asia/Shanghai');


require_once PROJECT_ROOT.'/common/libs/alipay/alipay_notify.class.php';
require_once PROJECT_ROOT.'/core/index.php';
require_once APP_ROOT.'/common/public.inc.php';

$alipay_config = array(
    'sign_type' => 'RSA',
    'ali_public_key_path' => PROJECT_ROOT.'/common/libs/alipay/alipay_public_key.pem',
    'transport' => 'https',
    'partner' => '2088421757537010',
    'cacert' => PROJECT_ROOT.'/common/libs/alipay/cacert.pem',
);
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {
    $result = false;
    if($_POST['trade_status'] == 'TRADE_FINISHED') {
        $mod = Factory::getMod('nc_pay');
        $result = $mod->paySuccess($_POST['out_trade_no'], 2, $_POST['trade_no']);
    }
    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        $mod = Factory::getMod('nc_pay');
        $result = $mod->paySuccess($_POST['out_trade_no'], 2, $_POST['trade_no']);
    }
    $msg = $result ? 'success' : 'fail';
}
else {
    $msg = "fail";
}
echo $msg;
exit;
