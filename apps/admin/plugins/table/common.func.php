<?php
/**
 * table共用的函数在这里定义，个性化的函数在table/{目录}/handle.func.php里定义
 *
 * 函数命名规则：tp[v/f/c]_{目录}_{功能}，tp表示table public; v表示validate，校验变量函数; f表示format，格式化变量函数; c表示common, 普通函数
 * 例如有一个共用函数是检查邮箱是否合法，定义如下：
 * 	function tpv_email($email) {...}
 * 重要：函数的第一个参数一定是当前字段本身。
 *
 * @author wangyihuang
 */

/**
 * 替换table的共用变量
 * 
 * @param string $var
 * @param string $tbl
 * @param int $company_id
 * @param int $appid
 * @return string
 */
function tpf_globalvar($var, $tbl='', $company_id=0, $appid=0) {

	$var = str_replace('{$tbl}', $tbl, $var);
	$var = str_replace('{$company_id}', $company_id, $var);
	$var = str_replace('{$appid}', $appid, $var);

	return $var;
}

/**
 * 生成摘要文字
 * @param string $txt
 * @param number $len
 * @return string
 */
function tpf_make_summary($txt, $len=30) {

	$txt = trim(strip_tags($txt));
	$txt = (preg_replace("/\s+/m","",$txt));

	return strval(truncate_utf8($txt, $len, '...'));
}

/**
 * 返回一些共用系统的type类型名称
 * @param unknown $var
 * @return string
 */
function tpf_sys_typename($var) {

	$type_list = array(
			'1' => '社区',
			'2' => '文章',
			'3' => '商品'
	);

	if (empty($var)) return '-';
	return isset($type_list[$var]) ? $type_list[$var] : '未知类型';
}

/**
 * 替换table的共用变量
 *
 * @param string $var
 * @param int $pkid
 * @return string
 */
function tpf_var($var, $pkid=0) {
	
	$var = str_replace('{$pkid}', $pkid, $var);

	return $var;
}

/**
 * 根据vip的值返回app的版本类型
 * @param unknown $var
 * @return string
 */
function tpf_app_vipname($var) {
	
	$vip_list = array(
			'0' => '体验版',
			'1' => '基础版',
	);
	
	if (empty($var)) return '-';
	
	return isset($vip_list[$var]) ? $vip_list[$var] : '-';
}

/**
 * 显示绝对路径的图片
 * 
 * @param string $url
 * @param int $w,
 * @param int $h
 */
function tpf_img_show($url, $w='', $h='') {

	
	if (empty($url)) return '-';
	
	return '<img src="'.$url.(empty($w) ? '' : '" width="'.$w).(empty($h) ? '' : '" height="'.$h).'">';
}

/**
 * 通过用户icon的相对路径，返回icon和icon大图的链接地址
 * @param string $icon
 */
function tpf_user_icon_show($icon) {
	
	// 设置默认头像
	if (empty($icon) || ($icon == 'NULL')) $icon = 'user/icon.png';
	
	$pinfo = pathinfo($icon);
	
	$icon = C('UPLOAD_DOMAIN').$icon;
	$iconraw = C('UPLOAD_DOMAIN').$pinfo['dirname'].'/'.$pinfo['filename'].'_big'.'.'.$pinfo['extension'];
	
	return '<a href="'.$iconraw.'" target="_blank"><img src="'.$icon.'" style="border:1px solid #ccc;" width=40></a>';
}

/**
 * 判断一个字符串是否为空，这里，0表示不为空
 *
 * @param string $var
 * @return boolean true/false
 */
function tpv_notnull($var) {

	return (strlen_utf8($var) == 0) ? false : true;
}

/**
 * 用empty判断一个字符串是否为空
 *
 * @param string $var
 * @return boolean true/false
 */
function tpv_notempty($var) {
    return empty($var) ? false : true;
}

/**
 * 判断是否是数字
 * 
 * @param string $var
 * @return boolean
 */
function tpv_numeric($var) {
	return is_numeric($var);
}


/**
 * 判断一个字符串的长度是否在$min和$max之间
 * 其中一个汉子算2个单位长度
 *
 * @param string $var
 * @param int $min
 * @param int $max
 *
 * @return boolean true/false
 */
function tpv_length($var, $min, $max) {

	$len = strlen_utf8($var, 1);

	return (($len >= $min) && ($len <= $max)) ? true : false;
}

/**
 * 判断一个用户名是否正确，默认不允许为空
 *
 * @param string $var
 * @param int $allow_null 0/1, 0表示允许为空，1表示不允许
 *
 * @return boolean true/false
 */
function tpv_uname($var, $allow_null=0) {

	$var = trim($var);
	if ($allow_null && (strlen($var) == 0)) {
		return true;
	}

	return preg_match('/^[a-zA-Z0-9_]+$/', $var) ? true : false;
}

/**
 * 判断一个appkey是否正确，默认不允许为空
 *
 * @param string $var
 *
 * @return boolean true/false
 */
function tpv_appkey($var) {

	$var = trim($var);

	return preg_match('/^[a-zA-Z0-9_-]+$/', $var) ? true : false;
}

/**
 * 判断一个邮箱是否正确，默认不允许为空
 *
 * @param string $var
 * @param int $allow_null 0/1, 0表示允许为空，1表示不允许
 *
 * @return boolean true/false
 */
function tpv_email($var, $allow_null=0) {

	$var = trim($var);
	if ($allow_null && (strlen($var) == 0)) {
		return true;
	}

	return is_email($var);
}

/**
 * 判断一个手机号码是否正确，默认不允许为空
 *
 * @param string $var
 * @param int $allow_null 0/1, 0表示允许mobile为空，1表示不允许
 *
 * @return boolean true/false
 */
function tpv_mobile($var, $allow_null=0) {

	$var = trim($var);
	if ($allow_null && (strlen($var) == 0)) {
		return true;
	}

	return is_mobile($var);
}

/**
 * 格式化日期
 * 
 * @param int $var
 * @return string
 */
function tpf_date($var) {

	if (empty($var)) return '-';
	return (date('Y') == date('Y', $var)) ? date('m-d H:i:s', $var) : date('Y-m-d H:i:s', $var);
}

/**
 * 格式化IP地址
 * 
 * @param int $var
 * @return string
 */
function tpf_ip($var) {

	if (empty($var)) return '-';
	return long2ip($var);
}

/**
 * 格式化性别
 *
 * @param int $var
 * @return string
 */
function tpf_sex($var) {

	if (empty($var)) return '-';
	if ($var == 1) return '男';
	if ($var == 2) return '女';
	
	return '-';
}

/**
 * 格式化文件大小
 * 
 * @param int $var
 * @return string
 */
function tpf_filesize($var) {
	$var = intval($var);

	return ($var < 100) ? $var.'B' : sprintf('%0.2f', ($var/1024)).'KB';
}

/**
 * 显示Font Awesome图标字体
 * @param string $var
 * @return string
 */
function tpf_fonticon($var) {
	
	if (empty($var)) return '-';
	return '<i class="'.$var.'"></i>';
}