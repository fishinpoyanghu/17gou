<?php
/**
 * app的一些公用函数
 */

/**
 * 生成appkey
 * 
 * @param int $appid
 * @return string
 */
function ap_generate_appkey($appid) {
	
	$key_list = array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,0,1,2,3,4,5,6,7,8,9,10);
	
	$res = array();
	$c = count($key_list)-1;
	for ($i=0; $i < 4; $i++) {
		
		$str = '';
		for ($m=0; $m < 5; $m++) {
			$str .= $key_list[mt_rand(0, $c)];
		}
		
		$res[] = $str;
	}
	
	$res[] = substr(strtoupper(md5($appid)), 0, 5);
	
	return implode('-', $res);
}

/**
 * 字符串化字符串，如果是null，返回''
 * @param string $str
 * @return string
 */
function ap_strval($str) {
	
	return (null === $str) ? '' : strval($str);
}

/**
 * 通过用户icon的相对路径，返回icon和icon大图的链接地址
 * @param string $icon
 */
function ap_user_icon_url($icon) {

	// 设置默认头像
	if (empty($icon)) $icon = 'user/icon.png';

	$pinfo = pathinfo($icon);
	$prefix = (false === strpos($icon, 'http')) ? C('UPLOAD_DOMAIN').'/' : '';

	return array(
			'icon' => $prefix.$icon,
			'iconraw' => $prefix.$pinfo['dirname'].'/'.$pinfo['filename'].'_big'.'.'.$pinfo['extension'],
	);
}
