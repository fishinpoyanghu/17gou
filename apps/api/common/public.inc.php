<?php
/**
 * 公共函数定义
 *
 */

// 测试用例使用的函数
function api_testcase_base($thisappid = '', $thisappkey = '') {

	if (!isset($_GET['nc'])) {
		$appkey = 'NKFSD-IJNBT-LDGAV-XYNVV';

		$_GET['sessid'] = '1-67ee2438f464fcfbb87a8ff381e1';
		$_GET['appid'] = '10002';
	}
	else {
		$appkey = 'DDV02-N710UJ-2MR2G-K2DXK-9103C';

		$_GET['sessid'] = '2-9b3eaad09300f45267dc29b533d5';
		$_GET['appid'] = '10003';
	}
	if(!empty($thisappid)){
		$_GET['appid'] = $thisappid;
	}
	if(!empty($thisappkey)){
		$appkey = $thisappkey;
	}
	$_GET['v'] = '0.0.1';
	$_GET['ct'] = '2';
	$_GET['did'] = 'didtestdid';
	$_GET['os'] = 'android 5.0';
	$_GET['nm'] = 'wifi';
	$_GET['mno'] = '联通';
	$_GET['dm'] = '640*1036';
	$_GET['time'] = time();
	$_GET['sign'] = md5($appkey.$_GET['time'].$_GET['sessid']);
}

function app_get_login_user($sessid, $appid, $login_force=false) {

	$login_mod = Factory::getMod('login');
	$ret = $login_mod->getLogin($sessid);

	if ($ret && ($ret['appid'] == $appid)) {
		return $ret;
	}

	if ($login_force) {
		api_result(6, '请先登录');
	}

	return false;
}

/**
 * 得到当前操作的APP
 */
function api_get_curr_app($login_uid, $appid=0) {

	if (!$appid) $appid = pint('appid');
	if (!$appid) $appid = isset($_FILES['appid']) ? intval($_FILES['appid']) : 0;

	if (!$appid) {
		return false;
	}

	$pub_mod = Factory::getMod('pub');
	$pub_mod->init('admin', 'app', 'appid');

	$where = array(
			'appid' => $appid,
			'stat' => 0,
			'uid' => $login_uid,
	);

	$app = $pub_mod->getRowWhere($where);

	if (!$app) return false;

	return $app;
}

/**
 * 给一个连接拼上更多参数
 *
 * @param string url
 * @param array params
 */
function api_add_url_params($url, $params) {

	$p = array();
	foreach ($params as $k=>$v) {
		$p[] = $k.'='.urlencode($v);
	}

	$p = implode('&', $p);

	if (false === strpos($url, '?')) {
		$url .= '?'.$p;
	}
	else {
		$url .= '&'.$p;
	}

	return $p;
}

/**
 * 根据 $user 返回用户登录需要返回的信息
 * @param array $user
 * @return array
 */
function api_get_output_user_data($user) {

	$iconinfo = ap_user_icon_url($user['icon']);

	if ((!$user['rebate_uid']) && ((time() - $user['rt']) > 259200)) {
		$user['rebate_uid'] = 999;
	}

	$data = array(
			'uid' => intval($user['uid']),
			'name' => strval($user['name']),
			'nick' => strval($user['nick']),
			'icon' => $iconinfo['icon'],
			'iconraw' => $iconinfo['iconraw'],
			'sex' => intval($user['sex']),
			'exp' => intval($user['exp']),
			'score' => intval($user['score']),
			'money' => bcadd($user['money'],$user['yongjin'],2),
			'level' => intval($user['level']),
			'sys_new' => intval($user['sys_new']),
			'notify_reply_new' => intval($user['notify_reply_new']),
			'notify_zan_new' => intval($user['notify_zan_new']),
			'notify_hongbao_new' => intval($user['notify_hongbao_new']),
			'notify_invite_new' => intval($user['notify_invite_new']),
			'notify_lucky_new' => intval($user['notify_lucky_new']),
			'msg_new' => intval($user['msg_new']),
			'last_check' => intval($user['last_check']),
			'signature' => strval($user['signature']),
			'rebate_uid' => intval($user['rebate_uid']),
			'phone'=>strval($user['phone']),
			'type'=>strval($user['type']),
			'lucky_packet'=>$user['lucky_packet']
	);

	return $data;
}

/**
 * 根据 $user 返回用户获取普通用户需要返回的信息
 * @param array $user
 * @return array
 */
function api_get_output_simple_user_data($user) {

	$iconinfo = ap_user_icon_url($user['icon']);

	$data = array(
			'uid' => intval($user['uid']),
			'name' => strval($user['name']),
			'nick' => strval($user['nick']),
			'icon' => $iconinfo['icon'],
			'iconraw' => $iconinfo['iconraw'],
			'sex' => intval($user['sex']),
			'exp' => intval($user['exp']),
			'score' => intval($user['score']),
			'level' => intval($user['level']),
			'signature' => strval($user['signature']),
	);

	return $data;
}

/**
 * 给某个表的某个字段值加上 $step的值，一般是pv
 * @param string $cfg_name
 * @param string $tbl_alias
 * @param string $pkid_name, 主键名称
 * @param int $pkid
 * @param number $step
 */
function api_increase_count($cfg_name, $tbl_alias, $pkid_name, $pkid, $step=1) {

	$pub_mod = Factory::getMod('pub');
	$pub_mod->init($cfg_name, $tbl_alias, $pkid_name);
	$step = intval($step);

	$update_data = array(
			'pv' => array($step, 'add'),
			'ut' => time(),
	);
	return $pub_mod->updateRow($pkid, $update_data);
}

/**
 * 通过$url得到图片要被裁剪的2个大小的路径
 *
 * @param string $url
 * @return array
 */
function api_cut_iconname($url) {

	$pinfo = pathinfo($url);

	return array(
			'icon' => $pinfo['dirname'].'/'.$pinfo['filename'].'_n'.'.'.$pinfo['extension'],
			'iconraw' => $pinfo['dirname'].'/'.$pinfo['filename'].'_n_big'.'.'.$pinfo['extension'],
	);
}

/**
 * @param string $url
 * @return string
 */
function api_get_icon_uri($url) {

	return preg_replace('/http:\/\/.*?\//m', '', $url);
}

/**
 * 把形如 piclib/loading/p1_n.jpg 转化成
 * array(
 * 		'url' => 'piclib/loading/p1_n.jpg',
 * 		'urlraw' => 'piclib/loading/p1_n_big.jpg',
 * 		'urltrue' => 'piclib/loading/p1.jpg'
 * )
 * @param unknown $path
 */
function api_get_pic_allpath($path) {

	$info = pathinfo($path);

	$basename = str_replace('_n.'.$info['extension'], '', $info['basename']);
	$basename = str_replace('_n_big.'.$info['extension'], '', $basename);
	$basename = str_replace('.'.$info['extension'], '', $basename);

	return array(
			'url' => C('UPLOAD_DOMAIN').'/'.$info['dirname'].'/'.$basename.'_n.'.$info['extension'],
			'urlraw' => C('UPLOAD_DOMAIN').'/'.$info['dirname'].'/'.$basename.'_n_big.'.$info['extension'],
			'urltrue' => C('UPLOAD_DOMAIN').'/'.$info['dirname'].'/'.$basename.'.'.$info['extension'],
	);
}

/**
 * 生成摘要文字
 * @param string $txt
 * @param number $len
 * @return string
 */
function api_make_summary($txt, $len=30) {

	$txt = trim(strip_tags($txt));
	$txt = (preg_replace("/\s+/m","",$txt));

	return strval(truncate_utf8($txt, $len, '...'));
}


/**
 * 返回当前毫秒级别的时间戳
 *
 * 假如 microtime()返回值是：0.33519300 1451995496
 * 本函数返回是: array('s'=>1451995496, 'ms'=>'335');
 *
 * @return array(
 * 		's' => $s, // 秒级别时间戳
 * 		'ms' => $ms, // 毫秒级别的字符串
 * )
 */
function api_get_microtime() {

	$t = microtime();
	list($t1, $t2) = explode(' ', $abc);

	$ms = str_replace('0.', '', sprintf('%.3f', $t1));

	return array(
			's'  => intval($t2), // 秒级别时间戳
			'ms' => $ms, // 毫秒级别的字符串
	);
}

/**
 * 根据邀请码，得到用户的uid
 *
 * @param int
 * @param string $code
 * @return int uid
 */
function api_decode_invite_code($appid, $code) {

	if (empty($code)) return 0;

	if ($code == '88888') return -1;

	$ary = array(
			'a' => 0,
			'b' => 1,
			'c' => 2,
			'd' => 3,
			'e' => 4,
			'f' => 5,
			'g' => 6,
			'h' => 7,
			'i' => 8,
			'j' => 9,
	);

	$len = strlen($code);
	$i = 0;

	$uid = '';
	while ($i < $len) {
		$tmp = substr($code, $i, 1);

		if (!isset($ary[$tmp])) return 0;
		$uid .= $ary[$tmp];
		$i++;
	}

	$uid = intval($uid);
	$uid = $uid - 29486;

	if ($uid < 1) return 0;

	// 这里，判断uid合不合法
	$pub_mod = Factory::getMod('pub');
	$pub_mod->init('main', 'user', 'uid');

	$where = array(
			'uid' => $uid,
			'appid' => $appid,
	);
	$user = $pub_mod->getRowWhere($where);

	if (!$user) return 0;

	return $uid;
}

/**
 * 根据用户ID，得到用户的邀请码
 *
 * @param int uid
 */
function api_get_user_invite_code($uid) {

	$uid = strval(intval($uid) + 29486);

	$ary = array(
			0 => 'a',
			1 => 'b',
			2 => 'c',
			3 => 'd',
			4 => 'e',
			5 => 'f',
			6 => 'g',
			7 => 'h',
			8 => 'i',
			9 => 'j',
	);

	$len = strlen($uid);
	$i = 0;

	$code = '';
	while ($i < $len) {
		$code .= $ary[substr($uid, $i, 1)];
		$i++;
	}

	return $code;
}

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
			'time'   => gstr('time'),//xiang gint改为gstr
			'sign'   => gstr('sign'),
	);

	// 校验app是否存在
	if (empty($ret['appid'])) {
		api_result(5, '应用ID缺失');
	}

	$pub_mod = Factory::getMod('pub');
	$pub_mod->init('admin', 'app', 'appid');

	$app = $pub_mod->getRow($ret['appid']);

	if (!$app) {
		api_result(5, '应用不存在');
	}
	if ($app['stat'] == 1) {
		api_result(5, '应用被停止使用');
	}
	// 校验app end
	if (isset($_GET['debug'])) {
		// do nothing
	}
	// 校验sign
	if ($ret['sign'] != md5($app['appkey'].$ret['time'].$ret['sessid'])) {
		 api_result(5, '非法访问参数');
	}
	// 校验sign end

	// 校验ct
	if (!in_array($ret['ct'], array(0,1,2,3,4,5))) {
		api_result(5, '客户端类型参数错误');
	}
	// 校验ct end

	// 额外返回当前的app信息
	$ret['app'] = $app;

	C('base', $ret);

	return $ret;
}


/**
 * 输出Api返回结果
 */
function api_result($code, $msg, $data=array(), $sessid='') {
	$base = C('base');
	$login_user = app_get_login_user($base['sessid'], $base['appid'], false);

	$callback = gstr('callback');

	$res = array (
			'code' => intval($code),
			'msg'  => strval($msg),
			'data' => $data,
			'sessid' =>strval($sessid),
			'new' => array(
					'sys_new' => 0,
					'notify_reply_new' => 0,
					'notify_zan_new' => 0,
					'notify_hongbao_new' => 0,
					'notify_invite_new' => 0,
					'notify_lucky_new' => 0,
					'msg_new' => 0,
			)
	);
	if(isset($data['count'])){
		$res['count']=$data['count'];
		unset($res['data']['count']); 
	}

	if ($login_user && ($login_user['uid'] > 0)) {
		$res['new'] = array(
				'sys_new' => intval($login_user['sys_new']),
				'notify_reply_new' => intval($login_user['notify_reply_new']),
				'notify_zan_new' => intval($login_user['notify_zan_new']),
				'notify_hongbao_new' => intval($login_user['notify_hongbao_new']),
				'notify_invite_new' => intval($login_user['notify_invite_new']),
				'notify_lucky_new' => intval($login_user['notify_lucky_new']),
				'msg_new' => intval($login_user['msg_new']),
		);
	}

	if (isset($_GET['debug'])) {
		dump($res);
	}
	else {
		if ($callback) {
			echo $callback.'('.json_encode($res).')';
		}
		else {
			echo json_encode($res);
		}
	}
    if($_REQUEST['sessid']){
        do_cache('delete', 'xxx', urlencode($_GET['c'].$_GET['a'].$_REQUEST['sessid']));
    }
	exit;
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
