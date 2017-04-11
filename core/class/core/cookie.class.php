<?php
/**
 * Created on 2010-5-8
 * 
 * Cookie管理类
 * 
 * @package core
 * @author wangyihuang
 * @version 2.0
 */

class Cookie {
	
	/**
	 * 判断Cookie是否存在
	 */
	static function is_set($name) {
		return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
	}
	
	/**
	 * 获取某个Cookie值
	 */
	static function get($name) {
		
		return Cookie::is_set($name) ? $_COOKIE[C('COOKIE_PREFIX').$name] : '';
	}
	
	/**
	 * 设置某个Cookie值
	 */
	static function set($name, $value, $expire='', $path='', $domain='') {
		
		if($expire=='') {
			$expire	=	C('COOKIE_EXPIRE');
		}
		if(empty($path)) {
			$path = C('COOKIE_PATH');
		}
		if(empty($domain)) {
			$domain	= C('COOKIE_DOMAIN');
		}
		
		$expire	= !empty($expire) ?	time()+$expire : 0;
		
		setcookie(C('COOKIE_PREFIX').$name, $value, $expire, $path, $domain);
		$_COOKIE[C('COOKIE_PREFIX').$name] = $value;
	}
	
	/**
	 * 删除某个Cookie值
	 */
	static function delete($name) {
		Cookie::set($name,'',-1);
		unset($_COOKIE[C('COOKIE_PREFIX').$name]);
	}
	
	/**
	 * 清空Cookie值
	 */
	static function clear() {
		unset($_COOKIE);
	}
}
