<?php
/**
 * Created on 2010-5-9
 * 
 * 公用的函数库
 * 
 * 此文件提供了网站所应用到的共用函数文件
 * 
 * @package include
 * @author wangyihuang
 * @version 2.0
 */

/**
 * 获取/设置配置值
 */
function C($name=null, $value=null) {
	
    static $_config = array();
    // 无参数时获取所有
    if(empty($name)) return $_config;
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        
        if (!strpos($name,'.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name])? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        // 二维数组设置和获取支持
        $name = explode('.',$name);
        $name[0]   = strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    // 批量设置
    if(is_array($name))
        return $_config = array_merge($_config,array_change_key_case($name));
        
    return null;// 避免非法参数
}

/**
 * MYSQL START TRANSACTION
 */
function mysql_start_transaction() {
	
	$db_cfg = load_db_cfg('autoid', 'res', '');
	
	$db_op = DbOp::getInstance($db_cfg);
	
	$db_op->execute("SET AUTOCOMMIT=0");
	$db_op->execute("START TRANSACTION");
}

/**
 * MYSQL COMMIT
 */
function mysql_end_transaction() {
	
	$db_cfg = load_db_cfg('autoid', 'res', '');
	
	$db_op = DbOp::getInstance($db_cfg);
	
	$db_op->execute("COMMIT");
}

/**
 * auto_id
 */
function get_auto_id($k=0, $count=1) {

    if($k==808){
        $count = mt_rand(1,20);
    }

	if (!is_numeric($k)) {
		die('get_auto_id key invalid, integer required.');
	}
	
	$k = intval($k);
	$count = intval($count);
	
	if (($k < 1) || ($k > 65535)) {
		die('get_auto_id key invalid, 0<$k<65536 required.');
	}
	
	$db_cfg = load_db_cfg('autoid', 'idcenter', '');
	
	$db_op = DbOp::getInstance($db_cfg);
	
	$ret = $db_op->execute("update ".$db_cfg['tbl']." set id=last_insert_id(id+{$count}) where k={$k}");
	
	if (!$ret) {
		
		$db_op->execute("insert into ".$db_cfg['tbl']." (k,id) values ({$k},0)");
		$ret = $db_op->execute("update ".$db_cfg['tbl']." set id=last_insert_id(id+{$count}) where k={$k}");
	}
	
	return $db_op->getInsertId(); //取得上一步 INSERT 操作产生的 ID 
}

function get_app_name() {
	return (isset($GLOBALS['core_is_api']) && $GLOBALS['core_is_api']) ? $GLOBALS['core_app_api_cfg']['app_name'] : $GLOBALS['core_app_cfg']['app_name']; 
}

function get_app_root() {
	return (isset($GLOBALS['core_is_api']) && $GLOBALS['core_is_api']) ? $GLOBALS['core_app_api_cfg']['app_root'] : $GLOBALS['core_app_cfg']['app_root']; 
}

function get_app_data_root() {
	return (isset($GLOBALS['core_is_api']) && $GLOBALS['core_is_api']) ? $GLOBALS['core_app_api_cfg']['data_root'] : $GLOBALS['core_app_cfg']['data_root']; 
}

/**
 * 加载DB配置文件
 * 
 * @param string cfg_name, 例如user.db.php的cfg_name就是user
 * @param string tbl_alias, 数据库表别名，DB配置文件里的tbl_list里的key
 * @param string hash_key, 分库分表的HASH KEY
 * @param string mode, 读写分离使用，r/w/rw, default:rw
 */
function load_db_cfg($cfg_name, $tbl_alias, $hash_key='', $mode='rw') {
	
	$key = 'core_db_'.$cfg_name;
	
	$db_cfg = C($key);
	
	// 如果配置文件还没有加载
	if (!is_array($db_cfg)) {
		
		$filename = CONFIG_PATH.'db/'.$cfg_name.'.db.php';
		
		// 如果文件不存在
		if (!is_file($filename)) {
			halt('config file:'.$filename.' is not found');
		}
		
		$db_cfg = include $filename;
		
		C(array($key=>$db_cfg));
		
		// 配置文件返回的不是数组
		if (!is_array($db_cfg)) {
			halt('return value is not an array in config file:'.$filename);
		}
	}
	
	// 判断tbl_alias是否被定义
	if (!isset($db_cfg['tbl_list'][$tbl_alias])) {
		halt('table alias is not defined in config file:'.$filename);
	}
	
	$split = $db_cfg['tbl_list'][$tbl_alias]['split'];
	$ret = array(
		'host' => $db_cfg['w_server'][0],
		'user' => $db_cfg['w_server'][1],
		'pass' => $db_cfg['w_server'][2],
		'tbl'  => '',
	);
	
	// 当r_server为空时，设置它和w_server一样
	if (!(is_array($db_cfg['r_server']) && (count($db_cfg['r_server']) > 0))) {
		$db_cfg['r_server'] = array($db_cfg['w_server']);
	}
	
	// 如果是读模式r
	if ($mode == 'r') {
		
		$r_rand = mt_rand(0, count($db_cfg['r_server'])-1);
		
		$ret['host'] = $db_cfg['r_server'][$r_rand][0];
		$ret['user'] = $db_cfg['r_server'][$r_rand][1];
		$ret['pass'] = $db_cfg['r_server'][$r_rand][2];
	}
	
	// 为数组时，表示系统默认分库分表规则，格式必须是: array(库的个数，每个库包含的表的个数)。HASH!
	if (is_array($split)) {
		
		$m = bcmod($hash_key, $split[0]);
		$n = bcmod(floor(bcdiv($hash_key, $split[0])), $split[1]);
		
		$ret['tbl'] = $db_cfg['db'].'_'.$m.'.'.$db_cfg['tbl_list'][$tbl_alias]['tbl'].'_'.$n;
	}
	// 为no时，库名会自动补上"_0", 表名不变
	elseif ($split == 'no') {
		$ret['tbl'] = $db_cfg['db'].'_0.'.$db_cfg['tbl_list'][$tbl_alias]['tbl'];
	}
	// 为off时，库名则不自动补上"_0"，完全保持原样
	elseif ($split == 'off') {
		$ret['tbl'] = $db_cfg['db'].'.'.$db_cfg['tbl_list'][$tbl_alias]['tbl'];
	}
	// 为define时，表示由用户自定义
	elseif ($split == 'define') {
		// @todo
		halt('[define] split mode has not beed implemented.');
	}
	
	return $ret;
}

/**
 * 返回一个sess_id，一个md5的串
 * @param int user_id
 * @param string ip
 * 
 * 在这里我简单的陈述一下该框架如何用memcached服务实现session的功能:(不再需要session_start())
 * 第一步:可以很简单的得到用户的user_id;
 * 第二步:通过调用sess_id()函数对用户的user_id和ip进行md5处理，最后得到一个md5串，
 * 		并把该md5串作为一个“session id”;
 * 第三步:把第二步得到的"session id"通过Cookie::set()方法放在cookie文件中，便于在不同的页面中可以调用该"session id";
 * 第四步:把需要在不同页面之间传递的变量放在同一个关联数组中，然后通过调用框架中的的do_cache()函数
 * 		把该关联数组放到memcached服务器上存储，注意在这里把“session id”作为键，关联数组作为值
 * 第五步:如果想从不同页面取出传递的值，首先要从第三步的cookie文件中通过Cookie::get()方法取出"session id"(键)，然后调用框架中的的do_cache()函数
 * 		把关联数组取出，进而可以取出相应的值;
 */
function sess_id($user_id='', $ip='') {
	
	if (empty($user_id)) $user_id = 'r'.mt_rand();
	if (empty($ip)) $ip = get_ip();
	
	$s1 = substr((md5($user_id . $ip . time() . microtime(true) . mt_rand())), 8, 25);
	$s2 = substr(md5($s1), 8, 4);
	
	return $user_id.'-'.$s1.$s2;
}

/**
 * 设置或者返回最后一次执行的sql
 */
function last_sql($sql_str='') {
	
	static $sql='';
	
	if ($sql_str) {
		$sql = $sql_str;
		return true;
	}
	
	return $sql;
}

/**
 * 得到一个友好显示的时间
 * 
 * 不到一分钟，显示"*秒前"
 * 大于一分钟小于一小时，显示"*分钟前"
 * 大于一小时小于今天，显示"*时*分"
 * 大于今天小于昨天，显示"昨天"大于昨天小于前天，显示"前天"
 * 大于前天小于一年，显示"*月*日"；大于一年，显示"*年*月*日"
 * 
 * note: 一年的是跨年了，而不是365天
 * 
 * @param long $timestamp
*/
function date_friendly($timestamp) {
	
	if (!is_numeric($timestamp)) return '-';
	
	if ($timestamp < 1) return '-';
		
	$curr_stamp = time();
	$time = $curr_stamp - $timestamp;
	
	// 今天的开始时间
	$today = strtotime(date('Y-m-d', $curr_stamp)); // $curr_stamp - $curr_stamp%86400;
	
	// 今年的开始时间
	$this_year = date('Y', $curr_stamp).'-1-1 00:00:00';
	$this_year_stamp = strtotime($this_year);
	
	$ret = '';
	
	if ($time <= 0) $time = 1;
	
	// 不到一秒钟，显示"刚刚"
	if ($time < 1) $ret = '刚刚';
	// 不到一分钟，显示"*秒前"
	elseif ($time < 60) $ret = $time . '秒前';
	// 大于一分钟小于一小时，显示"*分钟前"
	elseif ($time < 3600) $ret = floor($time/60).'分钟前';
	// 大于一小时小于今天，显示"*时*分"
	elseif ($timestamp > $today) $ret = date('H时i分', $timestamp);
	// 大于今天小于昨天，显示"昨天"
	elseif (($timestamp <= $today) && ($timestamp > ($today - 86400))) $ret = '昨天';
	// 大于昨天小于前天，显示"前天"
	elseif (($timestamp <= ($today - 86400)) && ($timestamp > ($today - 172800))) $ret = '前天';
	// 大于前天小于一年，显示"*月*日"
	elseif (($timestamp <= ($today - 172800)) && ($timestamp > $this_year_stamp)) $ret = date('m月d日', $timestamp);
	// 大于一年，显示"yyyy年*月*日"
	else {
		$ret = date('Y年m月d日', $timestamp);
	}
		
	return $ret;
}

/**
 * 把一个字符串从大写=》小写
 * 
 * @param string str
 */
function tolower($str) {
	
	return strtr($str, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
}

/**
 * 把一个字符串从小写=》大写
 * 
 * @param string str
 */
function toupper($str) {
	
	return strtr($str, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
}

/**
 * 判断一个UTF8字符串的长度
 * 
 * @param string $str
 * @param int $unit 1/2/3 中的一个值, default:2
 *           1表示$str每个汉字当做长度1来统计
 *           2表示$str每个汉字当做长度2来统计
 *           3表示$str每个汉字当做长度3来统计
 */
function strlen_utf8($str, $unit=2) {
	$len = 0;
	
	$result = substr_utf8($str, 0, strlen($str), true);
	$count = count($result);
	for ($i = 0; $i < $count; $i++) {
		$len += ord($result[$i]) > 127 ? $unit : 1;
	}	
	return $len;
}

/**
 * UTF8字符串的子字符串函数
 * 
 * @param string $str
 * @param string $start
 * @param string $length
 */
function substr_utf8($str, $start=0, $length=-1, $return_ary=false) {
    $len = strlen($str);if ($length == -1) $length = $len;
    $r = array();
    $n = 0;
    $m = 0;
    
    for($i = 0; $i < $len; $i++) {
        $x = substr($str, $i, 1);
        $a  = base_convert(ord($x), 10, 2);
        $a = substr('00000000'.$a, -8);
        if ($n < $start) {
            if (substr($a, 0, 1) == 0) {
            }elseif (substr($a, 0, 3) == 110) {
                $i += 1;
            }elseif (substr($a, 0, 4) == 1110) {
                $i += 2;
            }
            $n++;
        }else {
            if (substr($a, 0, 1) == 0) {
                $r[] = substr($str, $i, 1);
            }elseif (substr($a, 0, 3) == 110) {
                $r[] = substr($str, $i, 2);
                $i += 1;
            }elseif (substr($a, 0, 4) == 1110) {
                $r[] = substr($str, $i, 3);
                $i += 2;
            }else {
                $r[] = '';
            }
            if (++$m >= $length) {
                break;
            }
        }
    }
    
    return $return_ary ? $r : implode('',$r);
}

/**
 * 字符串阶段，不管是汉字还是字母，都算一个字符
 */
function truncate_utf8($str, $len, $etc='...') {
	
	$ary = substr_utf8($str, 0, strlen($str), true);
	
	if ($len >= count($ary)) return $str;
	
	$ret = '';
	for ($i=0; $i < ($len-1); $i++) {
		$ret .= $ary[$i];
	}
	
	return $ret.$etc;
	/*
	$bak_str = $str;
	$str = substr_utf8($str, 0, strlen($str), true);
	
	// 如果字符串长度小于截断长度，直接返回
	if ($len >= count($str)) return $bak_str;
	
	// 26个字母
	// i l I 占0.5
	// 其他的占0.8 汉字 1.0
	$c = count($str);
	
	$total_len = 0;
	
	for ($i=0; $i < $c; $i++) {
		
		if (ord($str[$i]) > 127) {
			$total_len = $total_len + 1;
			$str[$i] = array($str[$i], 2);
		}
		elseif (in_array($str[$i], array('i', 'l', 'I'))) {
			$total_len = $total_len + 1; //0.5;
			$str[$i] = array($str[$i], 1);
		}
		else {
			$total_len = $total_len + 1; //0.8;
			$str[$i] = array($str[$i], 1);
		}
		
		if (ceil($total_len) >= $len) {
			
			$str = array_slice($str, 0, ($i+1));
			
			break;
		}
	}
	
	$total_len = ceil($total_len);
	
	$c = count($str);
	
	$etc_len = strlen($etc);
	$etc_len2 = 0;
	for ($i=($c-1); $i >=0; $i--) {
		
		if ($etc_len2 >= $etc_len) {
			
			$str = array_slice($str, 0, ($i+1));
			break;
		}
		
		$etc_len2 += $str[$i][1];
	}
	
	$c = count($str);
	$ret = '';
	for ($i=0; $i < $c; $i++) {
		$ret .= $str[$i][0];
	}
	
	return $ret.$etc;
	*/
}

/**
 * 检查字符串是否是UTF8编码
 * 
 * @param string $string 字符串
 * 
 * @return boolean
 */
function is_utf8($str) {
	return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
}

/**
 * MySQL指令安全过滤
 * 
 * 支持数据递归过滤
 * 
 * @param mixed data 
 */
function safe_db_data($data) {
	
	if (is_array($data) || is_object($data)) {
		foreach ($data as &$v) {
			$v = safe_db_data($v);
		}
	} else {
		return mysql_escape_string($data);
	}
	
	return $data;
}

/**
 * 字符串命名风格转换
 * type
 * =0 将Java风格转换为C的风格 UserModel=>user_model
 * =1 将C风格转换为Java的风格 user_model=>UserModel
 * 
 * @param string name 字符串
 * @param int type 转换类型 default：1
 * 
 * @return string
 */
function parse_name($name, $type=1) {
    if ($type == 1) {
        return ucfirst(preg_replace("/_([a-zA-Z0-9])/e", "strtoupper('\\1')", $name));
    }
    else if ($type == 2) {
    	return (preg_replace("/_([a-zA-Z0-9])/e", "strtoupper('\\1')", $name));
    }
    else {
        $name = preg_replace("/[A-Z]/", "_\\0", $name);
        return strtolower(trim($name, "_"));
    }
}

/**
 * 得到客户端IP
 * 
 * @param int ip2long, 0/1, 1表示把ip转化成长整形
 */
function get_ip($ip2long=0) {
 
     global $_SERVER;
     $ip = '';
     
     if (isset($_GET['clientip'])) {
     	$ip = $_GET['clientip'];
     }
     elseif (getenv('HTTP_CLIENT_IP')) {
         $ip = getenv('HTTP_CLIENT_IP');
     }
     elseif (getenv('HTTP_X_FORWARDED_FOR')) {
         $ip = getenv('HTTP_X_FORWARDED_FOR'); 

     }
     elseif (getenv('REMOTE_ADDR')) {
         $ip = getenv('REMOTE_ADDR');
     }
     else {
         $ip = $_SERVER['REMOTE_ADDR'];
     }
     return $ip2long ? ip2long($ip) : $ip;
}

/**
 * top redirect
 */
function top_redirect($url='') {
	if (empty($url)) $url = C('SITE_DOMAIN');
    echo '<script type="text/javascript">top.window.location.href="'.$url.'"</script>';
    exit;
}

/**
 * 系统重定向函数
 * @param string $url
 * @param string $msg, optional
 * @param int $time, second, optional
 */
function redirect($url,$msg='',$time=0) {
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($msg))
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    
    // 封装msg的样式
    $msg = '<div style="font-size:12px;color:#fff;background:#f90;line-height:24px;width:250px;text-align:center;">'.$msg.'</div>';
    
    if (!headers_sent()) {
        // redirect
        if(0===$time) {
            header("Location: ".$url);
        }else {
            header('Content-type: text/html; charset=utf-8');
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0)
            $str   .=   $msg;
        exit($str);
    }
}

/**
 * 开始调试模式
 */
function debug() {
	static $debug_off = true;
	
	C('APP_DEBUG', true);
	if ($debug_off) {
		register_shutdown_function(array('App', 'showTrace'));
		$debug_off = false;
	}
}

/**
 * 浏览器友好的变量输出
 * 
 * @param mixed $var
 * @param boolean $echo
 * @param mixed $label
 * @param boolean $strict
 * 
 * @example dump($var_ary);
 * 
 * @author wangyihuang <wangyihuang@gmail.com>
 */
function dump($var) {
	
	if (!headers_sent()) header('Content-type: text/html; charset=utf-8'); // temp
	
    $label = '';
    
     if(!extension_loaded('xdebug')) {
	    ob_start();
	    var_dump($var);
	    $output = ob_get_clean();
	    
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre>'. $label. htmlspecialchars($output, ENT_QUOTES). '</pre>';
        
        echo $output;
        return ;
    }
    else {
    	ini_set('xdebug.var_display_max_children', 1024 );//xdebug.var_display_max_children Type: integer, Default value: 128
    	ini_set('xdebug.var_display_max_data', 10240 );//Type: integer, Default value: 512
    	ini_set('xdebug.var_display_max_depth', 10);
    	
    	if (is_array($var)) {
    		var_dump($var);
    	}
    	else {
    		echo $var.'<br />';
    	}
    	return ;
    }
}

/**
 * 取得对象实例 支持调用类的静态方法
 * 
 * @param string $name，对象类名
 * @param string $method，对象静态方法名
 * @param array $args，$method要传入的参数
 */
function get_instance_of($name, $method='',$args=array()) {
    
    static $_instance = array();
    $identify   =   empty($args)?$name.$method:$name.$method.to_guid_string($args);
    
    if (!isset($_instance[$identify])) {
        if(class_exists($name)){
            $o = new $name();
            if($method && method_exists($o,$method)){
                if(!empty($args)) {
                    call_user_func_array(array(&$o, $method), $args);
                }else {
                    $o->$method();
                }
            }
            $_instance[$identify] = $o;
        }
        else
            halt('实例化一个不存在的类！:'.$name);
    }
    
    return $_instance[$identify];
}

/**
 * 自定义异常处理
 */
function throw_exception($msg, $type='AppException', $code=0) {
    if(IS_CLI) exit($msg);
    if(class_exists($type, false))
        throw new $type($msg, $code, true);
    else
        halt($msg);        // 异常类型不存在则输出错误信息字串
}

// 错误输出
// type=1 异常
// type=2 错误
function halt($error, $type=1) {
    
    if(IS_CLI) exit($error);
    
    if (in_array(RUN_MOD, array('test2', 'deploy'))) {
    	
    	header('Location:'.C('INDEX_URL').'/unfound');
    	exit;
    }
    $e = array();
    if (C('APP_DEBUG')) {
        //调试模式下输出错误信息
        if(!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
            $traceInfo='';
            $time = date("y-m-d H:i:m");
            foreach($trace as $t) {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")<br/>";
            }
            $e['trace']  = $traceInfo;
        }
        else {
            $e = $error;
        }
        
        // 包含异常页面模板
        include C('APP_EXCEPTION_FILE');
    }
    else
    {
        //否则定向到错误页面
        $error_page = C('ERROR_PAGE');
        if(!empty($error_page)) {
            redirect($error_page);
        }
        else {
            if(C('SHOW_ERROR_MSG'))
                $e['message'] =  is_array($error)?$error['message']:$error;
            else
                $e['message'] = C('ERROR_MESSAGE');
                
            // 包含异常页面模板
            include C('APP_EXCEPTION_FILE');
        }
    }
    exit;
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 */
function to_guid_string($mix) {
    if(is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    }elseif(is_resource($mix)){
        $mix = get_resource_type($mix).strval($mix);
    }else{
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * 是否是合法的Email
 * 
 * @param mixed $email
 * 
 * @return boolean
 */
function is_email($email) {
	if (!eregi("^([a-z0-9_.]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$", $email)) return false;
	return true;
}

/**
 * 是否是合法的手机号码
 * 
 * @param mixed mobile
 * 
 * @return boolean
 */
function is_mobile($mobile) {
	if (preg_match('/^1[345789][0-9]{9,9}$/', $mobile)) return true;	
	return false;
}

/**
 * 判断手机是否合法，11位，且以13、15、18开头
 */
function is_phone($var) {return _regex($var, 'phone'); }

/**
 * 判断QQ号码是否合法，QQ号码必须是数字或者以qq.com结尾的邮箱
 */
function is_qq($var) {
	$regex1 = '/^[1-9]\d{4,15}$/';
	$regex2 = '/^[0-9a-zA-Z_\-\.]+@qq\.com$/';
	
	if (preg_match($regex1, $var)) return true;
	if (preg_match($regex2, $var)) return true;
	
	return false;
}

function _regex($value,$rule) {
    $validate = array(
        'require'=> '/.+/',
        'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
        'currency' => '/^\d+(\.\d+)?$/',
        'number' => '/\d+$/',
        'zip' => '/^[1-9]\d{5}$/',
        'integer' => '/^[-\+]?\d+$/',
        'double' => '/^[-\+]?\d+(\.\d+)?$/',
        'english' => '/^[A-Za-z]+$/',
        'phone' => '/^1(3|5|8)\d{9}$/',
        'qq' => '/^[1-9]\d{4,15}(@qq\.com)?$/',
    );
    
    // 检查是否有内置的正则表达式
    if(!isset($validate[strtolower($rule)])) return false;
    
    $rule   =   $validate[strtolower($rule)];
    
    return preg_match($rule,$value)===1;
}

//[RUNTIME]
// 编译文件
function compile($filename,$runtime=false) {
    $content = file_get_contents($filename);
    $content = trim($content);
    // 替换预编译指令
//    $content = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s','',$content);
    $content = substr(trim($content),5);
    if('?>' == substr($content,-2))
        $content = substr($content,0,-2);
    return $content;
}

// 去除代码中的空白和注释
function strip_whitespace($content) {
    $stripStr = '';
    //分析php源码
    $tokens =   token_get_all($content);
    $last_space = false;
    
    for ($i = 0, $j = count ($tokens); $i < $j; $i++) {
    	
        if (is_string ($tokens[$i])) {
            $last_space = false;
            $stripStr .= $tokens[$i];
        }
        else {
            switch ($tokens[$i][0]) {
                //过滤各种PHP注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //过滤空格
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr .= ' ';
                        $last_space = true;
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr .= $tokens[$i][1];
            }
        }
    }
    
    return $stripStr;
}

/**
 * 禁用页面缓存
 */
function nocache_headers() {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
}

/**
 * 除法运算
 */
function division($divisor, $dividend, $point = 2, $mul = 0){
	$result = 0;
	$dividend = floatval($dividend);
	$divisor = floatval($divisor);
	if (empty($dividend)){
		if($mul == 0){
			return sprintf("%.{$point}f",0.00000);
		}else{
			return sprintf("%.{$point}f",1.0000000 * $mul);
		}
	}

	if(empty($divisor)){
		return sprintf("%.{$point}f",0.00000);
	}
	$result = sprintf("%.{$point}f",$divisor/$dividend);
	return $result;
}

/**
 * 打印数据
 */
function printPre($data){
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}
