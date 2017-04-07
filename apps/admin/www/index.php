<?php
/**
 * Created on 2010-9-18
 *
 * 应用入口
 * 
 * @author wangyihuang
 */
// 定义框架所在路径
defined('CORE_ROOT') || define('CORE_ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/core');

// 定义app的名字
$app_name  = 'admin';
// 定义app的根目录
$app_root  = realpath(dirname(CORE_ROOT).'/apps/admin');
//静态文件目录
if(!defined('SYS_STATIC_URL')) define('SYS_STATIC_URL','/apps/admin/static');
if(!defined('UPLOAD_URL')) define('UPLOAD_URL','http://www.yiqigou888.com/');
if(!defined('USER_ICON_URL')) define('USER_ICON_URL','http://www.yiqigou888.com/uploads/');
//app_id
if(!defined('APP_ID')) define('APP_ID',10002);

/**
 * 数据库
 */
if(!defined('DATABASE')) define('DATABASE','17gou');

// 定义app的数据目录
$data_root = realpath(dirname(CORE_ROOT).'/data');

// 初始化应用
$GLOBALS['core_app_cfg'] = array(
		'app_name'  => $app_name,
		'app_root'  => $app_root,
		'data_root' => $data_root,
);

/// 定义各种运行模式网站的域名
$GLOBALS['SYSTEM_CFG'] = array(
    // 定义app的域名
    'SITE_LIST' => array(
        'local'  => 'www.yiqigou888.com/apps/admin/www',
        'test'  => 'www.g.com/apps/admin/www'
    ),
    'STATIC_LIST' => array(
        'local'  => 'www.yiqigou888.com/apps/admin/static',
        'test'  => 'www.g.com/apps/admin/static',
    ),
    'UPLOAD_LIST' => array(
        'local'  => 'www.yiqigou888.com/uploads',
        'test'  => 'www.g.com/uploads',
    ),
    'API_LIST' => array(
        'local'  => 'www.yiqigou888.com/apps/api/www',
        'test'  => 'www.g.com/apps/api/www',

    ),
    'COOKIE_PREFIX' => array(
        'local'  => 'l_',
        'test'  => 'l_',
    ),
    'COOKIE_DOMAIN' => array(
        'local'  => 'www.yiqigou888.com',
         'test'  => 'www.g.com',
    )
);

// 加载框架
include CORE_ROOT.'/index.php';

// C('COOKIE_DOMAIN', 'local.example.com');

// 如果是local和test模式，开启错误显示
if ((RUN_MOD == 'local') || (RUN_MOD == 'test')) {
	/*error_reporting(0);
	ini_set('display_errors', 'off');*/
}
error_reporting(E_PARSE  );
    ini_set('display_errors', 'on');
// 引入admin app的公共函数
include $app_root.'/common/public.inc.php';
include $app_root.'/common/validate.inc.php';
if($_GET['c']!='index' && $_GET['c']!=''){    app_get_login_user(1);}



// 实例化一个网站应用实例
App::run();

