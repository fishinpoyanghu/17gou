<?php
/**
 * Created on 2010-9-18
 *
 * 应用入口
 * 
 * @author wangyihuang
 */
define('A_PATH','http://www.g.com/apps/api/www');
define('W_PATH','http://www.g.com/apps/webapp/www');
define('PC_PATH','http://www.g.com/pc');
// 定义框架所在路径
defined('CORE_ROOT') || define('CORE_ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/core');

// 定义app的名字
$app_name  = 'api';
// 定义app的根目录
$app_root  = realpath(dirname(CORE_ROOT).'/apps/api');
// 定义app的数据目录
$data_root = realpath(dirname(CORE_ROOT).'/uploads');

// 初始化应用
$GLOBALS['core_app_cfg'] = array(
		'app_name'  => $app_name,
		'app_root'  => $app_root,
		'data_root' => $data_root,
);

// 定义各种运行模式网站的域名
$GLOBALS['SYSTEM_CFG'] = array(
		// 定义app的域名 
    'SITE_LIST' => array(
        'local'  => 'www.g.com/apps/admin/www',
        'test'  => 'www.g.com/apps/admin/www'
    ),
    'STATIC_LIST' => array(
        'local'  => 'www.g.com/apps/admin/static',
        'test'  => 'www.g.com/apps/admin/static',
    ),
    'UPLOAD_LIST' => array(
        'local'  => 'www.g.com/uploads',
        'test'  => 'www.g.com/uploads',
    ),
    'API_LIST' => array(
        'local'  => 'www.g.com/apps/api/www',
        'test'  => 'www.g.com/apps/api/www',

    ),
    'COOKIE_PREFIX' => array(
        'local'  => 'l_',
        'test'  => 'l_',
    ),
    'COOKIE_DOMAIN' => array(
        'local'  => 'www.g.com',
         'test'  => 'www.g.com',
    )
);

// 加载框架
include CORE_ROOT.'/index.php';

// C('COOKIE_DOMAIN', 'local.example.com');

// 如果是local和test模式，开启错误显示
if ((RUN_MOD == 'local') || (RUN_MOD == 'test')) {
	error_reporting(0);
	ini_set('display_errors', 'off');
}

// 引入admin app的公共函数
include $app_root.'/common/public.inc.php';
include $app_root.'/common/validate.inc.php';

// 解析一些特殊post到$_POST
$post_data = file_get_contents('php://input', true);
if ($post_data) {
	$post_data = json_decode($post_data, true);
	if (is_array($post_data)) {
		foreach ($post_data as $k=>$v) {
			$_POST[$k] = $v;
		}
	}
}

if($_REQUEST['sessid']){
    $m = do_cache('add','xxx',urlencode($_GET['c'].$_GET['a'].$_REQUEST['sessid']),1);
    if(!$m && $_GET['c'].$_GET['a']!='nc_activityactivity_list'){
        exit;
    }
}
// 实例化一个网站应用实例
App::run();

if($_REQUEST['sessid']){
    do_cache('delete', 'xxx', urlencode($_GET['c'].$_GET['a'].$_REQUEST['sessid']));
}