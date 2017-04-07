<?php
/**
 * 表专用的函数在这里定义
 * 
 * 函数命名规则：t[v/f/c]_{目录}_{功能}，v表示validate，校验变量函数; f表示format，格式化变量函数; c表示common, 普通函数
 * 例如 目录admin，有一个函数是检查用户名是否合法，函数名应该是这样：function tv_admin_name($name) {...}
 * 重要：函数的第一个参数一定是当前字段本身。
 * 
 * @author wangyihuang
 */

function tf_appguide_showpic($var, $w='', $h='') {
	
	if (empty($w)) $w = '48';
	
	if (0 === strpos($var, 'icon-')) {
		return '<i class="'.$var.'" style="font-size:'.$w.'px;"></i>';
	}
	
	return '<a href="'.C('UPLOAD_DOMAIN').$var.'" target="_blank"><img src="'.$var.(empty($w) ? '' : '" width="'.$w).(empty($h) ? '' : '" height="'.$h).'"></a>';
}
