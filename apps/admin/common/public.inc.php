<?php
/**
 * 公共函数定义
 * 
 */

/**
 * 检查api的11个标准参数，并以数组的形式返回检查结果
 * 如果出错了，会直接在函数里返回给客户端
 */
function api_check_base() {

	$ret = array(
			'sessid' => gstr('sessid') ,
			'appid' => gint('appid') ,
			'v'      => gstr('v'),
			'ct'     => gint('ct'),
			'did'    => gstr('did'),
			'os'     => gstr('os'),
			'nm'     => gstr('nm'),
			'mno'    => gstr('mno'),
			'dm'     => gstr('dm'),
			'time'   => gint('time'),
			'sign'   => gstr('sign'),
	);

	// 校验app是否存在
	if (empty($ret['appid'])) {
		echo_result(5, '应用ID缺失');
	}

	$pub_mod = Factory::getMod('pub');
	$pub_mod->init('admin', 'app', 'appid');

	$app = $pub_mod->getRow($ret['appid']);

	if (!$app) {
		echo_result(5, '应用不存在');
	}
	if ($app['stat'] == 1) {
		echo_result(5, '应用被停止使用');
	}
	// 校验app end
	if (isset($_GET['debug'])) {
		// do nothing
	}
	// 校验sign
	if ($ret['sign'] != md5($app['appkey'].$ret['time'].$ret['sessid'])) {
		echo_result(5, '非法访问参数');
	}
	// 校验sign end

	// 校验ct
	if (!in_array($ret['ct'], array(0,1,2,3,4,5))) {
		echo_result(5, '客户端类型参数错误');
	}
	// 校验ct end

	// 额外返回当前的app信息
	$ret['app'] = $app;

	C('base', $ret);

	return $ret;
}

function api_safe_ipt($p) {
	if (is_array($p)) {
		foreach ($p as $k=>$v) {
			$p[$k] = api_safe_ipt($v);
		}
	}
	else {
		$p = htmlspecialchars($p, ENT_QUOTES);
	}
	return $p;
}

/**
 * 把链接地址加上随机参数，并输出
 * 
 * @param string $url
 * @param boolean $return
 */
function app_echo_url($url, $return=false) {
	
	if (false === strpos($url, '?')) {
		$url .= '?'.time();
	}
	else {
		$url .= '&'.time();
	}
	
	if ($return) return $url;
	else echo $url;
}

/**
 * 返回我的app页面URL
 * 
 * @return string
 */
function app_get_myapp_url() {
	return '/?c=app';
}

function app_show_table_ipt_attrs($attr) {
	if (empty($attr) || (!is_array($attr))) return '';
	
	$ret = '';
	foreach ($attr as $k=>$v) {
		$ret .= ' '.$k.'="'.$v.'"';
	}
	
	return $ret;
}

/**
 * 返回当前登录的用户
 * 
 * @param int $login_check, default:0, 0:未登录直接返回，1:未登录跳转到登录页 2:未登录echo_result(98, '请先登录')
 * @param int $appid_check, default:0, 0:没有appid直接返回，1:没有appid跳转到 我的app 页面 2:没有appid echo_result(99, 'appid缺失')
 * @return array
 */
function app_get_login_user($login_check=0, $appid_check=0) {
	
	$login_mod = Factory::getMod('login');
	
	$login_user = $login_mod->getLogin();
	
	// 如果未登录
	if ($login_user['uid'] == 0) {
	
		if ($login_check == 1) {
			redirect('./');
		}
		elseif ($login_check == 2) {
			echo_result(98, '请先登录');
		}
	}
	
	// 先尝试从$_GET参数获取appid，如果存在，覆盖Cookie里的appid	
	$appid = gint('appid');
	$appauth = gstr('appauth');
	
	if ($appid && $appauth) {
		if (_app_check_auth($appauth, $appid.$login_user['sessid'])) {
			$login_user['appid'] = $appid;
			
			Cookie::set('appid', $appid, 2592000);
			Cookie::set('appauth', $appauth, 2592000);
		}
	}
	else {
		$appid = Cookie::get('appid');
		$appauth = Cookie::get('appauth');
		
		if ($appid && _app_check_auth($appauth, $appid.$login_user['sessid'])) {
			$login_user['appid'] = $appid;
		}
	}
// 	dump($appid);dump($appauth);
// 	exit;
	if ($appid_check) {
		
		// 如果appid=0
		if ($login_user['appid'] == 0) {
			if ($login_check == 1) {
				redirect('/?c=app');
			}
			elseif ($login_check == 2) {
				echo_result(99, 'appid缺失');
			}
		}
		// 到这里，如果appid存在，获取appname，并获取对应这个app的company_id
		else {
			$t_mod = Factory::getMod('table');
			$t_mod->tryLoadTable('app');
			
			$app = $t_mod->getTableRow($login_user['appid']);
			if ($app) {
				$login_user['appname'] = $app['name'];
				$login_user['company_id'] = $app['company_id'];
			}
			
			$t_mod->closeTable();
		}
	}
	
	C('appid', $login_user['appid']);
	C('company_id', $login_user['company_id']);
	//是否是超级管理员
	$login_user['is_super'] = $login_mod->isSuperAdmin($login_user['uid']);
	return $login_user;
}

/**
 * 根据appid返回它的uri串 appid={$appid}&appauth={$appauth}
 * 
 * @param string $appid
 * @return string
 */
function get_appid_uri($appid) {
	static $login_user='';
	if (empty($login_user)) $login_user = app_get_login_user(0, 0);
	
	return 'appid='.$appid.'&appauth='._app_encode_auth($appid.$login_user['sessid']);
}

/**
 * 传入一个key，返回一个加密的校验串
 * 
 * @param string $key
 * @return string
 */
function _app_encode_auth($key) {
	
	return md5($key.C('COOKIE_KEY'));
}

/**
 * 传入一个加过密的校验串，以及key，判断校验串是否就是key的加密串
 * 
 * @param string $auth
 * @param string $key
 * @return boolean
 */
function _app_check_auth($auth, $key) {
	
	return (md5($key.C('COOKIE_KEY')) == $auth);
}

/**
 * 防xss
 * @param $string
 * @param bool|False $low
 * @return bool
 */
function clean_xss(&$string, $low = False){
	if (! is_array ( $string ))
	{
		$string = trim ( $string );
		$string = strip_tags ( $string );
		$string = htmlspecialchars ( $string );
		if ($low)
		{
			return True;
		}
		$string = str_replace ( array ('"', "\\", "'", "/", "..", "../", "./", "//" ), '', $string );
		$no = '/%0[0-8bcef]/';
		$string = preg_replace ( $no, '', $string );
		$no = '/%1[0-9a-f]/';
		$string = preg_replace ( $no, '', $string );
		$no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
		$string = preg_replace ( $no, '', $string );
		return True;
	}
	$keys = array_keys ( $string );
	foreach ( $keys as $key )
	{
		$this->clean_xss ( $string [$key] );
	}
}