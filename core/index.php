<?php
/**
 * Created on 2015-09-08
 *
 * 框架统一入口文件
 * 
 * @author wangyihuang
 */

ini_set('magic_quotes_gpc', '0'); // 强制关闭magic_quotes_gpc
if (!$GLOBALS['core_app_cfg']) exit;

defined('CORE_ROOT') || define('CORE_ROOT', realpath(dirname(__FILE__)));


/*{{{获取本机IP*/
if(!defined('DATABASE')) define('DATABASE','17gou');
define('RUN_MOD', 'test');

// 定义运行模式  devel/test/deploy
// devel: 开发模式  test:测试模式  deploy:正式部署模式
// 框架会根据不同的模式加载不同的配置文件

// 定义框架核心编译缓存的存放路径
defined('RUNTIME_PATH') || define('RUNTIME_PATH', CORE_ROOT.'/cache/');

// 定义框架配置文件的存放目录
define('CONFIG_PATH', realpath(dirname(CORE_ROOT)).'/config/');

// 定义网站公用部分文件存放目录
define('COMMON_PATH', realpath(dirname(CORE_ROOT)).'/common/');

// 定义上传部分专用的目录地址
define('UPLOAD_PATH', realpath(dirname(CORE_ROOT)).'/uploads/');
if(!defined('PIC_UPLOAD_URL')) define('PIC_UPLOAD_URL', 'uploads/');

// 设置上传的域名
if (!isset($GLOBALS['SYSTEM_CFG']['UPLOAD_LIST'])) {
	$GLOBALS['SYSTEM_CFG']['UPLOAD_LIST'] = array(
				'local'  => 'www.yiqigou888.com',
				'test'  => 'www.g.com',
				'mobile'=>'192.168.0.199'
		);
}

//登录key
if(!defined('LOGIN_KEY')) define('LOGIN_KEY','UDJFH-KFIUS-PLSJF-YTSNS');

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 记录开始运行时间
$GLOBALS['_t_app_start'] = microtime(true);

defined('IS_CLI') || define('IS_CLI', PHP_SAPI=='cli' ? 1 : 0);

// 非local或者test运行环境下，如果存在框架缓存，直接加载

if (is_file(RUNTIME_PATH.'~bin.php')) {
    require RUNTIME_PATH.'~bin.php';
}
else {
    // 加载编译函数文件
    require CORE_ROOT.'/include/compile.inc.php';
    compile_frm();
}


// 如果是local环境，版本号即时随机
if (RUN_MOD == 'local' || RUN_MOD == 'test' || RUN_MOD == 'mobile') {
	C('VERSION_JS', C('VERSION_JS').time());
	C('VERSION_CSS', C('VERSION_CSS').time());
	C('VERSION_IMG', C('VERSION_IMG').time());
}

// 记录加载框架时间
$GLOBALS['_t_app_load'] = microtime(true);

