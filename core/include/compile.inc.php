<?php
/**
 * Created on 2010-3-13
 *
 * 生成框架的核心编译缓存
 * 
 * @author wangyihuang
 */

function compile_frm() {
	
	$runtime = array();
	
	// 定义核心编译的文件
	$runtime[] = CORE_ROOT.'/include/function.inc.php';
	
	// 加载框架预定义的核心编译文件列表
	$list = include CONFIG_PATH.'/core.inc.php';
	
	// merge两个列表
	$runtime = array_merge($runtime, $list);
	
	// 加载核心编译文件列表
	foreach ($runtime as $key=>$file) {
		if (is_file($file)) require $file;
	}
	
	$core_cfg_name = CONFIG_PATH.'/core.cfg.php';
	
	// 是否启用新版的配置文件方式
	$core_cfg = array();
	$all_cfg = array();
			
	// 加载框架配置文件
	if (is_file($core_cfg_name)) C(include $core_cfg_name);
	
	// 加载缓存服务器列表
	$cache_filename = CONFIG_PATH.'cache/base.inc.php';
	if (is_file($cache_filename)) {
		$cache_list = include $cache_filename;
		C(array('core_cache_list'=>$cache_list));
		unset($cache_list);
	}
	
	// 加载队列服务器列表
	$queue_filename = CONFIG_PATH.'queue/base.inc.php';
	if (is_file($queue_filename)) {
		$queue_list = include $queue_filename;
		C(array('core_queue_list'=>$queue_list));
		unset($queue_list);
	}
			
	// 生成核心编译文件，去掉文件空白和注释以减少大小
	$content = '';
	foreach ($runtime as $file) {
		$content .= compile($file);
	}
	
	// 是否启用新版的配置文件方式
	$content .= ' $core_cfg = '.var_export(C(), true).';C($core_cfg);';
	
    $content = strip_whitespace('<?php'.$content);
    file_put_contents(RUNTIME_PATH.'~bin.php', $content);
    unset($content);
}
?>
