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

/**
 * 格式化 素材物料的地址 成 <a href...><img src=... width=... height=... /></a>
 * 
 * @param string $var
 * @param int $w
 * @param int $h
 * @return string
 */
function tf_pic_material_showpic($var, $w, $h) {

	return '<a href="'.C('UPLOAD_DOMAIN').'material/'.$var.'" target="_blank"><img src="'.C('UPLOAD_DOMAIN').'material/'.$var.'" width="'.$w.'" height="'.$h.'"></a>';
}

/**
 * 返回物料的分类名称
 * 
 * @param string $var, 分类ID
 * @param string $type color/style/classify
 */
function tf_pic_material_getcname($var, $type) {

	static $conf = '';
	if (empty($conf)) $conf = include get_app_root().'/conf/material.conf.php';

	return $conf['material'][$type]['c'.$var];
}
