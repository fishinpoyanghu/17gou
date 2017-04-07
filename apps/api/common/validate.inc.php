<?php
/**
 * 常用的核对函数封装
 */

function api_get_posts($appid, $validate_cfg) {
	
	$pub_mod = Factory::getMod('pub');
	
	$ipt_list = array();

	foreach ($validate_cfg as $k=>$v) {
		
		$p = pstr($k);

		foreach ($v as $m=>$set) {
			
			$validate = _api_parse_syntex($set);
			
			// 是否是唯一性检查函数
			if ($validate['base'] == 'api_unique') {
				$where = array(
						$k => $p,
						'appid' => $appid,
				);
				$pub_mod->init($validate['params'][0], $validate['params'][1], $validate['params'][2]);
				$_tmp = $pub_mod->getRowWhere($where);
				$ret = $_tmp ? false : true;
			}
			else {
				$ret = _api_run_func($validate['base'], $validate['params'], $p);
			}
			
			if (!$ret) {
				
				api_result(5, $validate['text']);
			}
		}
		$ipt_list[$k] = $p;
	}

	return $ipt_list;
}

function api_v_notnull($p) {
	return (strlen_utf8($p) == 0) ? false : true;
}

function api_v_numeric($p, $great0=false) {
	if (!is_numeric($p)) return false;
	
	$p = intval($p);
	if ($great0 && ($p < 1)) return false;
	
	return true;
}

function api_v_length($p, $min, $max) {

	$len = strlen_utf8($p, 1);
	
	return (($len >= $min) && ($len <= $max)) ? true : false;
}

function api_v_json($p) {
	
	if (empty($p)) return false;
	
	$ary = json_decode($p, true);
	
	if ($ary && is_array($ary)) return true;
	
	return false;
}

function api_v_notspace($p) {
	
	// 半角空格
	if (false !== strpos($p, ' ')) return false;
	// 全角空格
	if (false !== strpos($p, '　')) return false;
	
	return true;
}

function api_v_password($p) {
	return (preg_match('/^[a-zA-Z0-9_]+$/', $p) && preg_match('/[a-z]+/',$p) && preg_match('/[0-9]+/',$p)) ? true : false;
}

function api_v_iscolor($p, $allownull=1) {
	
	if ($allownull && (!api_v_notnull($p))) return true;
	
	return preg_match('/^#[a-zA-Z]{6}$/', $p) ? true : false;
}

function api_v_inarray($p, $ary) {
	
	return in_array($p, $ary);
}

function api_v_mobile($p) {
	return is_mobile($p);
}

function api_v_mcode($p) {
	
}

/**
 *
 * @param string $func
 * @param array $params
 * @param mixed $pre_arg，前置参数，默认是null，如果不是null，将会array_unshift到$params的第一个元素
 */
function _api_run_func($func, $params, $pre_arg=null) {

	if ($pre_arg !== null) {
		array_unshift($params, $pre_arg);
	}

	return call_user_func_array($func, $params);
}

/**
 * 解析table定义的语法
 *
 * 语法定义：
 * xxx|yyy,zzz||ppp：xxx是函数名；xxx函数会在除了当前操作字段外，传入yyy和zzz变量（多个变量以,分隔，之间不允许有空格，分隔符是|)；ppp是描述文字，用于函数返回的提示或者其他用途（记住分隔符是||)
 *
 * @param string $str
 * @return arrray(
 * 		'base'   => xxx,
 * 		'params' => array(yyy, zzz),
 * 		'text'   => ppp
 * 	)
 */
function _api_parse_syntex($str) {

	$ret = array(
			'base' => '',
			'params' => array(),
			'text' => 'undefined text(通常是配置文件语法错误)',
	);

	$str_ary = explode('||', $str);
	$str = $str_ary[0];

	if (count($str_ary) >= 2) $ret['text'] = $str_ary[1];

	$str_ary = explode('|', $str);
	$ret['base'] = $str_ary[0];

	if (count($str_ary) >= 2) {
		$ret['params'] = explode(',', $str_ary[1]);
		
		foreach ($ret['params'] as $k=>$v) {
			if (false !== strpos($v, ';;')) {
				$ret['params'][$k] = explode(';;', $v);
			}
		}
	}

	return $ret;
}
