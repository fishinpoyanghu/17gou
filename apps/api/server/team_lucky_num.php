<?php
/**
 * @since 2016-01-22
 * note 后端脚本，生成幸运号，定时跑
 */
define('DATABASE','17gou');
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
error_reporting(0);

require_once PROJECT_ROOT.'/common/libs/wxpay/WxPay.Api.php';
require_once PROJECT_ROOT.'/core/index.php';
require_once APP_ROOT.'/common/public.inc.php';

$mod = Factory::getMod('team_lucky_num');
$mod->run();
