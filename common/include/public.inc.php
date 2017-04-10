<?php
/**
 * Created on 2015-09-07
 * 
 * 定义使用本框架网站的一些共用函数
 * 
 * @package include
 * @author wangyihuang
 */

/**
 * include一个view文件
 */
function get_view($view_name) {
	return get_app_root().'/views/'.$view_name.'.view.php';
}

/**
 * 输出CSS路径，例如：
 * echo_css('global.css')
 * echo_css('user/reg.css')
 *
 * @param string css_name
 */
function echo_css($css_name='') {
	echo '<link rel="stylesheet" href="'.C('CSS_DOMAIN').'/'.$css_name.'?'.C('VERSION_CSS').'">'."\n";
}

/*
 * 输出css路径
 * @param $dir_name_string  例如：/css/web.css,js/user/user.js
 * @param $type string   js || css
 * echo('css','golbal.css') 参数二允许为空
 * echo('js','js/user/user.js')
 */
function echo_static($type,$dir_name){
    switch($type){
        case 'css':  echo '<link rel="stylesheet" href="'.C('STATIC_DOMAIN').'/'.$dir_name.'?'.C('VERSION_CSS').'">'."\n";break;
        case 'js':   echo '<script src="'.C('STATIC_DOMAIN').'/'.$dir_name.'?'.C('VERSION_JS').'"></script>'."\n";break;
    }

}


/**
 * 得到CSS的文件路径，例如：
 * get_css('global.css')s
 * get_css('user/reg.css')
 *
 * @param string css_name
 */
function get_css($css_name='') {
	return C('CSS_DOMAIN').'/'.$css_name.'?'.C('VERSION_CSS');
}

/**
 * 输出JS路径，例如:
 * echo_js('global.js')
 * echo_js('user/reg.js')
 *
 * @param string js_name
 */
function echo_js($js_name='') {
	echo '<script src="'.C('JS_DOMAIN').'/'.$js_name.'?'.C('VERSION_JS').'"></script>'."\n";
}

/**
 * 得到JS路径，例如:
 * get_js('global.js')
 * get_js('user/reg.js')
 *
 * @param string js_name
 */
function get_js($js_name='') {
	return C('JS_DOMAIN').'/'.$js_name.'?'.C('VERSION_JS');
}

/**
 * 输出IMG路径，例如:
 * get_img('logo.gif')
 * get_img('office/game.png')
 *
 * @param string img_name
 */
function get_img($img_name='') {

	echo C('IMG_DOMAIN').'/'.$img_name.'?'.C('VERSION_IMG');
}

/**
 * 统计代码
 */
function statics_code() {
	
	echo '<div style="display:none;"><script src="http://s23.cnzz.com/stat.php?id=3447923&web_id=3447923" language="JavaScript"></script></div>';
}

/**
 * 缓存操作函数
 * 
 * @param string op: get/set/delete
 */
 
 
function do_cache($op, $type, $k, $v='', $cfg_name='') {
	static $cache_list=array();
	
	//$type变量的取值只能取app.cfg.php文件中在数组CACHE_H['cache_list']中配置好的值，
	//也就是如果想取其他的值，必须首先在app.cfg.php文件中配置
	
	if (empty($cfg_name)) $cfg_name = get_app_name();
	$cache = new Cache($cfg_name);

    if ($op === 'get') {
		
		$ret = $cache->get($type, $k);

        if (($ret === false) || ($ret === '')) return false;
		
		return $ret;
	}
	elseif ($op === 'set') {	
		return $cache->set($type, $k, $v);
	}
	elseif ($op === 'delete') {
		return $cache->delete($type, $k);
	}elseif($op === 'add'){
        return $cache->add($type, $k,$v);
    }
	else {
		halt(__FUNCTION__.': 非法缓存操作类型:'.$op.'!');
	}
}

/**
 * 队列操作函数
 * 
 * @param string q_name, 队列名称
 * @param string op, 操作类型
 * @param mixed data，要操作的数据，只对add/update有效
 * @param mixed where, 条件，只对update/delete有效
 * @param string cfg_name, 可选，队列配置文件名
 * @param string alias, 可选，表别名
 * @param string hash_key, 可选，分库分表依据字段的值！
 */
function do_queue($q_name='', $op, $data='', $where='', $cfg_name='', $alias='', $hash_key='') {
	
	static $queue_list=array();
	
	if (empty($cfg_name)) $cfg_name = get_app_name();
	
	if (!in_array($op, array('add', 'update', 'delete'))) {
		halt(__FUNCTION__.': 非法队列操作类型!');
	}
	
	if (!isset($queue_list[$cfg_name.'__'.$q_name])) {
		$queue_list[$cfg_name.'__'.$q_name] = new Queue($cfg_name, $q_name);
	}
	
	$queue = $queue_list[$cfg_name.'__'.$q_name];
	
	$str = array(
			'op'=>$op,
		);
	if (!empty($data))     $str['data']     = $data;
	if (!empty($where))    $str['where']    = $where;
	if (!empty($alias))    $str['alias']    = $alias;
	if (!empty($hash_key)) $str['hash_key'] = $hash_key;
	
	$str = json_encode($str);
	
	try {
		$ret = $queue->put($str);
		
		if ($ret === true) return true;
		
		return false;
	}
	catch (Exception $ex) {
		
		/// 这里可能需要进行log记录
		/// @todo
		
		return false;
	}
}

/**
 * 得到凌晨的时间戳
 */
function get_lingchen_stamp() {
	return strtotime(date('Y-m-d', time()));
}

/**
 * $_GET get string
 */
function gstr($name) {
	
	if (isset($_GET[$name])) return $_GET[$name];
	
	return '';
}

/**
 * $_GET get int
 */
function gint($name) {
	
	if (isset($_GET[$name])) {
		return is_numeric($_GET[$name]) ? intval($_GET[$name]) : false;
	}
	
	return 0;
}

/**
 * $_GET get float
 */
function gfloat($name) {
	
	if (isset($_GET[$name])) return floatval($_GET[$name]);
	
	return 0;
}

/**
 * $_POST get string
 */
function pstr($name) {
	
	if (isset($_POST[$name])) return $_POST[$name];
	
	return '';
}

/**
 * $_POST get int
 */
function pint($name) {
	
	if (isset($_POST[$name]) && is_numeric($_POST[$name])) return intval($_POST[$name]);
	
	return 0;
}

/**
 * $_POST get float
 */
function pfloat($name) {
	
	if (isset($_POST[$name])) return floatval($_POST[$name]);
	
	return 0;
}

/**
 * 解析数据库更新操作的data_list数据
 *
 * @param array data
 *
 * @return string, 拼接好的SQL语句段
 */
function parse_insert_data_list($data_list) {
	
	if (!is_array($data_list)) return '';
	if (count($data_list) < 1) return '';
	
	$keys = array_keys($data_list[0]);
	
	$key_ary = array();
	foreach ($keys as $key) {
		$key_ary[] = "`".$key."`";
	}
	
	$sql = " (".implode(",", $key_ary).") VALUES ";
	
	$v_list = array();
	foreach ($data_list as $data) {
		
		$v_ary = array();
		foreach ($keys as $key) {
			
			$v_ary[] = "'".$data[$key]."'";
		}
		
		$v_list[] = "(".implode(",", $v_ary).")";
	}
	
	$sql .= implode(",", $v_list);
	
	return $sql;
}

/**
 * 解析数据库更新操作的data数据
 * 
 * @param array data
 * 
 * @return string, 拼接好的SQL语句段
 */
function parse_data($data){
	
	if (!is_array($data)) return '';
	
	$ret = array();
	
	foreach ($data as $k=>$v) {
		
		if (is_array($v)) {
			if (count($v) < 2) {
				trigger_error(__FUNCTION__.'非法的parse_data: '.var_export($data, true), E_USER_ERROR);
			}
			$v[1] = strtolower($v[1]);
			if (!in_array($v[1], array('add'))) {
				trigger_error(__FUNCTION__.'非法的parse_data: '.var_export($data, true).'，目前只支持add', E_USER_ERROR);
			}
			
			$v[0] = intval($v[0]);
			
			/// 判断，如果$v[0]<0，改成minus
			if ($v[0] < 0) {
				$ret[] = '`'.$k.'`=`'.$k.'`'.$v[0];
			}
			else {
				$ret[] = '`'.$k.'`=`'.$k.'`+'.$v[0];
			}
		}
		else {
			$ret[] = '`'.$k.'`=\''.$v.'\'';
		}
	}
	return implode(',', $ret);
}

/**
 * 辅助函数，
 * data['where']解析，目前只支持and
 * 	array(
 * 		'user_id'=>20101,
 * 		'level' => array(1, '>'), /// 这里支持>,<,>=,<=
 * 		'level' => array(1, '>', 'level2'), /// 表示level用level2替代
 * 		'type'  => array(array(1,2,3), 'in'), 
 * 		'record_time'=>54232,
 * 	)
 */
function parse_where($where) {
	
	if (!is_array($where)) return '';
	if (count($where) < 1) return '';

	$ret = array();
	
	foreach ($where as $k=>$v) {
		
		if (is_array($v)) {
			
			if (count($v) >= 3) $k = $v[2];
			
			if (($v[1] == 'in') && is_array($v[0])) {
				
				foreach ($v[0] as $m=>$n) {
					$v[0][$m] = "'".$n."'";
				}
				
				$ret[] = '`'.$k.'` '.$v[1].' ('.implode(',', $v[0]).')';
			}
			else {
				$ret[] = '`'.$k.'`'.$v[1].'\''.$v[0].'\'';
			}
		}
		else {
			$ret[] = '`'.$k.'`=\''.$v.'\'';
		}
	}
	
	return ' WHERE '. implode(' AND ', $ret);
}

/**
 * 转化成阳历+农历的日期格式
 * 
 * @param int t, 时间戳
 */
function lunar_date($t) {
	
	require_once CORE_ROOT.'/include/class.lunar.php';
	
	if (empty($t)) $t = time();
	
	$l = new Lunar();
	$y = date("Y", $t);
	$m = date("m", $t);
	$d = date("d", $t);
	$res = $l->cal($y, $m, $d);
	
	return $y.'年'.$m.'月'.$d.'日 '.$res ["week"].' | 农历 '.$res["month"].$res["day"];
}

/**
 * 随机生成6-10位数的密码
 */
function random_password() {
	
	$len = mt_rand(6,10);
	$src = array(0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);
	
	$pass = '';
	for ($i=0; $i < $len; $i++) {
		
		$pass .= $src[mt_rand(0, count($src)-1)];
	}
	
	return $pass;
}

/*
 * 函数功能 :
 *            分页输出
 * 必选参数 :
		totalPage 总分页数量
		currentPage 当前页码
		url 分页链接
         可选参数 :
	    halfPer 二分之一的每页的信息数
	调用分页函数:$pageHtml		= page ( $totalPage , $currentPage,$url ,$halfPer);
 */
function page($totalPage, $currentPage, $url, $halfPer=10) {
	$currentPage = $currentPage < 1 ? 1 : $currentPage;
	$re = "<li><a href=\"$url=1\"><i class=\" icon-double-angle-left\"></i></a></li>\n<li><a href=\"$url=".($currentPage==1?1:$currentPage-1)."\"><i class=\"icon-angle-left\"></i></a></li>\n";

	for ($i=$currentPage - $halfPer,$i > 1 || $i = 1, $j = $currentPage + $halfPer, $j < $totalPage || $j = $totalPage;$i <= $j ;$i++) {

		$re .= "<li><a".(($i == $currentPage)?" style=\"background-color:#03a9f5\"":"")." href=\"{$url}={$i}\">{$i}</a></li>\n";
	}

	$re .= "<li><a href=\"$url=".($currentPage>=$totalPage?$currentPage:$currentPage+1)."\"><i class=\"icon-angle-right\"></i></a></li>\n<li><a href=\"$url=".$totalPage."\"><i class=\" icon-double-angle-right\"></i></a></li>\n";

	if($totalPage>1) {
		$re="<ul class=\"pagination pagination-sm\">{$re}</ul>";
		return $re;
	}

	return '';
}

/**
 * 输出Ajax返回结果
 */
function echo_result($code, $msg='', $data='') {
	
	$callback = gstr('callback');
	
	$res = make_result($code, $msg, $data);
	
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
	exit;
}

function make_result($code, $msg, $data='') {
	
	$ret = array (
		'code' => intval($code),
		'msg'  => strval($msg),
	    'data' => $data
    );
    
    return $ret;
}

/**
 * 根据生日得到用户的星座
 * 
 * @param int $year
 * @param int $month
 * @param int $day
 */
function get_xingzuo($year=0, $month=0, $day=0) {
 	
 	$month = $month;
 	$date = $day;
 	
 	if ($day < 1) return false;
 	 	
 	$ret = false;
 	
 	if (($month == 1 && $date >= 20) || ($month == 2 && $date <= 18)) { $ret = array(1, "水瓶座");}   
 	if ($month == 1 && $date > 31) { $ret = false;}   
 	if (($month == 2 && $date >= 19) || ($month == 3 && $date <= 20)) { $ret = array(2, "双鱼座");}   
 	if ($month == 2 && $date > 29) { $ret = false;}   
 	if (($month == 3 && $date >= 21) || ($month == 4 && $date <= 19)) { $ret = array(3, "白羊座");}   
 	if ($month == 3 && $date > 31) { $ret = false;}   
 	if (($month == 4 && $date >= 20) || ($month == 5 && $date <= 20)) { $ret = array(4, "金牛座");} 
 	if ($month == 4 && $date > 30) { $ret = false;}   
 	if (($month == 5 && $date >= 21) || ($month == 6 && $date <= 21)) { $ret = array(5, "双子座");}   
 	if ($month == 5 && $date > 31) { $ret = false;}   
 	if (($month == 6 && $date >= 22) || ($month == 7 && $date <= 22)) { $ret = array(6, "巨蟹座");}   
 	if ($month == 6 && $date > 30) { $ret = false;}   
 	if (($month == 7 && $date >= 23) || ($month == 8 && $date <= 22)) { $ret = array(7, "狮子座");}   
 	if ($month == 7 && $date > 31) { $ret = false;}   
 	if (($month == 8 && $date >= 23) || ($month == 9 && $date <= 22)) { $ret = array(8, "处女座");}   
 	if ($month == 8 && $date > 31) { $ret = false;}   
 	if (($month == 9 && $date >= 23) || ($month == 10 && $date <= 22)) { $ret = array(9, "天秤座");}   
 	if ($month == 9 && $date > 30) { $ret = false;}   
 	if (($month == 10 && $date >= 23) || ($month == 11 && $date <= 21)) { $ret = array(10, "天蝎座");}   
 	if ($month == 10 && $date > 31) { $ret = false;}   
 	if (($month == 11 && $date >= 22) || ($month == 12 && $date <= 21)) { $ret = array(11, "射手座");}   
 	if ($month == 11 && $date > 30) { $ret = false;}   
 	if (($month == 12 && $date >= 22) || ($month == 1 && $date <= 19)) { $ret = array(12, "摩羯座");}   
 	if ($month == 12 && $date > 31) { $ret = false;}
 	
 	return $ret;
}

/**
 * 执行CURL访问一个页面，并返回页面的HTML代码
 * 用户SDO注册、登录相关
 */
function curl_page($url, $post=0, $postdata='', $connect_timeout=3, $timeout=3) {
	
	$a = microtime(true);
	
	if (is_array($postdata)) $postdata = _make_curl_posts($postdata);
	
	$user_agent ="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
	
	$ch = curl_init($url);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
//    	curl_setopt ($ch, CURLOPT_COOKIE , $this->cookie);
	
	// 允许重定向
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	// 支持HTTPS(SSL)
	if (preg_match('/^https/', $url)) {
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
	}
	 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
	curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt ($ch, CURLOPT_REFERER, $url);
	
	// 是否启用POST提交
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	}
	
	$ret = curl_exec($ch);

	$header = curl_getinfo($ch);
	
	// 更新http_code
	$http_code = isset($header['http_code']) ? $header['http_code'] : 200;
	
	$error = '';
	if ($http_code != 200) {
		$error = curl_error($ch);
	}
	
	curl_close ($ch);
	
	$b = microtime(true);
	
	$exec_time = $b-$a;
    
    /// 如果执行时间大于1秒，记录DNS
	if ($exec_time > 1.0) {
		
		$header_str = 'total_time:'.$header['total_time'].',namelookup_time:'.$header['namelookup_time'].',connect_time'.$header['connect_time'].',pretransfter_time:'.$header['pretransfer_time'];
//	    api_log($header_str);
    }
    
    if (empty($ret) || ($http_code == 201)) {
		
//		api_log('返回值为空或者http_code201');
	    
	    return false;
	}
	
	return $ret;
}

function _make_curl_posts($posts) {
	
	if (!is_array($posts)) $posts = array();
	
	$tmp = array();
	foreach ($posts as $k=>$v) {
		$tmp[] = $k.'='.urlencode($v);
	}
	
	return implode('&', $tmp);
}

/**
 * 得到当前页面的链接
 */
function get_page_url() {
	
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
function get_qq_face($str){
	$str = str_replace(">",'<；',$str);
	$str = str_replace(">",'>；',$str);
	$str = str_replace("\n",'>；br/>；',$str);
	$str = preg_replace("[\[em_([0-9]*)\]]","<img src=\"".STATIC_SITE_BBS."/images/faces/$1.gif\" />",$str);
	return $str;
}
function get_date_str($rt){
	$time = time();
	$poor = ceil(($time-$rt)/3600);
	if($poor < 24){
		$hour_time = $poor.'小时之内';
	}else if($poor > 24 && $poor < 48){
		$hour_time = '1天前';
	}else if($poor > 48 && $poor < 240){
		$hour_time = '几天前';
	}else if($poor > 240){
		$hour_time = '很久以前';
	}
	return $hour_time;
}
