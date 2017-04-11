<?php
/**
 * Created on 2010-5-27
 *
 * API通用接口
 * 
 * 本类的目的就是为了统一游玩网API调用规范，要求：
 * 1. 所有的API接口都需要通过本class来调用
 * 2. 所有的接口都需要统一输出格式
 * 3. 原则上，能通过GET访问的，不要使用POST
 * 4. 当出现命名相同时，GET优先级高于POST
 * 
 * 返回值规范定义：
 * array (
 * 	code  => 1 成功 除1外都是失败 如果code<0 时，失败信息会被记录日志中
 * 	msg   => 成功或是失败返回的信息
 * 	data  => array(),
 * )
 * 
 * @author wangyihuang
 */

class ClientApi {
	
	private $api_key = 'sse3$%3667&';
	
	private $api_server;
	
	/**
	 * Constructor!
	 */
    public function __construct() {
    	
    	$api_server = array();
    	$api_server['home']   = C('API_HOME_URL');
    	$api_server['dest']   = C('API_DEST_URL');
    	$api_server['feed']   = C('API_FEED_URL');
    	$api_server['user']   = C('API_USER_URL');
    	$api_server['youju']  = C('API_YOUJU_URL');
    	$api_server['location'] = C('API_LOCATION_URL');
		$api_server['record'] = C('API_RECORD_URL');
		$api_server['smsapi'] = C('API_SMSAPI_URL');
		$api_server['party']  = C('API_PARTY_URL');
		$api_server['bbs']    = C('API_BBS_URL');
		$api_server['rule']   = C('API_RULE_URL');
		$api_server['mobile']   = C('API_MOBILE_URL');
		$api_server['invite']   = C('API_INVITE_URL');
    	$this->api_server = $api_server;
    }
	
    /**
     * 执行API调用
     * 
     * @param string server, 采用哪个api_server
     * @param string func, 执行哪个api_server函数
     * @param array gets, 需要GET传输的参数
     * @param array posts, 需要POST传输的参数
     */
    public function doApi($server='', $func='', $gets=array(), $posts=array()) {
    	
    	$ret = array(
    		'code'  => 0,
    		'msg'   => '还没调用到接口',
    		'data'  => array(),
    	);
    	
    	if (empty($server)) {
    		$ret['msg'] = 'server不能为空';
		    client_api_error(sprintf("[E] [%s] [%s] [%s]\n[R] %s\n\n", date("m/d H:i:s"), $server,$func, $ret["error"]));
    		return $ret;
    	}
    	
    	// 判断API函数定义的API SERVER存不存在
    	
    	if ((!isset($this->api_server[$server])) || empty($this->api_server[$server])) {
    		$ret['msg'] = $server.'对应的服务器在API的api_server里未定义或者为空';
		    client_api_error(sprintf("[E] [%s] [%s] [%s]\n[R] %s\n\n", date("m/d H:i:s"), $server,$func, $ret["error"]));
    		return $ret;
    	}
    	
    	// 到这里，表示可以调用API了
    	
    	$url = $this->make_gets($server, $func, $gets);
    	$postdata = $this->make_posts($posts);
    	return $this->execCurl($url, $postdata);
    }
    
    private function make_gets($server, $func, $gets) {
    	
    	$url = $this->api_server[$server];
    	
    	if (!is_array($gets)) $gets = array();
    	
    	$_token = md5(sprintf('%u', crc32($this->api_key . strrev($server."|".$func))));
    	
    	if (!is_array($gets)) $gets = array();

    	if (false === strpos($url, '?')) $url .= '?';
    	
    	$url .= "&server={$server}";
    	$do = "";
    	if (!isset($gets['do'])) {
    		$do = 'do='.urlencode($func);
    	}
    	
    	if (false === strpos($url, '=')) $url .= ''.$do;
    	else $url .= '&'.$do;
    	
    	$url .= '&_token='.urlencode($_token);
    	
    	foreach ($gets as $k=>$v) {
    		$url .= '&'.$k.'='.urlencode($v);
    	}
    	return $url;
    }
    
    private function make_posts($posts) {
    	
    	if (!is_array($posts)) $posts = array();
    	
    	$tmp = array();
    	foreach ($posts as $k=>$v) {
    		$tmp[] = $k.'='.urlencode($v);
    	}
    	
    	return implode('&', $tmp);
    }
    
    private function execCurl($url, $postdata='') {
    	
    	$tmp_time   = microtime(true);
    	$user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
    	
    	$ch = curl_init($url);
    	curl_setopt ($ch, CURLOPT_HEADER, 0);    	
    	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10); // 链接timeout 2秒
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30); // 执行timeout 3秒
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt ($ch, CURLOPT_REFERER, $url);
		
		// 允许重定向
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	
    	// 支持HTTPS(SSL)
    	if (preg_match('/^https/', $url)) {
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
    	}
		
    	// 是否启用POST提交
    	if (!empty($postdata)) {
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    	}
    	
    	$ret    = curl_exec($ch);
    	$header = curl_getinfo($ch);
		$http_code = isset($header['http_code']) ? $header['http_code'] : 200;
		
		$error = '';
		if (!$http_code) {
			$error = curl_error($ch);
		}
		
		curl_close ($ch);
		
		$tmp_time = microtime(true) - $tmp_time;
		
		if ($tmp_time >0.5) {
		    $message = sprintf("[W] [%s] [%s] %s\n[T] %.5f sec consumed\n[D] %s\n\n", date("m/d H:i:s"), $postdata ? "POST": "GET", $url, $tmp_time, json_encode($postdata));
		    client_api_error($message);
		}
		
		// 增加API的简单调试功能
		if (function_exists('C') && C('API_DEBUG')) {
			if (function_exists('dump') && (IS_CLI == 0)) {
				dump($url);
				dump('HTTPCODE: '.$http_code);
				dump('EXEC TIME: '.$tmp_time.' seconds');
				if ($error) dump('ERROR: '.$error);
				dump($ret);
			}
			else {
				echo $url."<br />\n";
				echo 'HTTPCODE='.$http_code."<br />\n";
				echo 'EXEC TIME: '.$tmp_time." seconds<br />\n";
				if ($error) echo 'ERROR: '.$error."<br />\n";
				print_r($ret);
			}
		}
		if (empty($ret) || ($http_code == 201)) {
		    $message = sprintf("[E] [%s] [%s] %s\n[R] %s\n[D] %s\n\n", date("m/d H:i:s"), $postdata ? "POST": "GET", $url, $http_code, json_encode($postdata));
		    client_api_error($message);
		    return false;
		}
		
		$ret = json_decode($ret, true);
		
		// 如果不是数组，表示API返回出错啦
		if (!is_array($ret)) {			
			$ret = array('code'=>-100, 'msg'=>'API返回的信息json_decode结果不是数组');
		}
		
		if ($ret["code"]<0) {
		    client_api_error(sprintf("[E] [%s] [%s] %s\n[R] %s\n[D] %s\n\n", date("m/d H:i:s"), $postdata ? "POST": "GET", $url, $ret["msg"], json_encode($postdata)));
		}
		return $ret;
    }
    
    /**
	 * 用于client api返回结果数据
	 *
	 * @param $code
	 * @param $msg
	 * @param $data
	 */
    public function makeResult($code=0, $msg='', $data=array()) {
    	
    	$ret = array (
    		'code' => $code,
    		'msg'  => $msg,
    	    'data' => $data
        );
	    
	    return $ret;
    }
}
?>