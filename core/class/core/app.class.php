<?php
/**
 * Created on 2010-5-8
 * 
 * 应用程序入口，
 * 获取并分析用户的请求，分发到不同的控制器
 * 
 * @package core
 * @author wangyihuang
 * @version 2.0
 */

// 类定义开始
class App {
	
	/**
	 * 应用程序初始化
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	public static function init() {
		
		set_error_handler(array('App', 'appError'));
		set_exception_handler(array('App','appException'));
		
		// 加载URL调度器
		// 暂时未实现，可以考虑在这里加入QUERY_STRING方式
		
		// 设置默认控制器名和动作名称
		if (!isset($_GET['c']) || empty($_GET['c'])) $_GET['c'] = 'index';
		if (!isset($_GET['a']) || empty($_GET['a'])) $_GET['a'] = 'index';
		
		// 取得控制器名和动作名称
		$c = $_GET['c'];
		$a = parse_name($_GET['a'], 2);
		
		// 判断控制其名和动作名称的合法性
		if (!preg_match('/^[a-zA-Z0-9_]+$/', $c)) {
			trigger_error('invalid GET param c!', E_USER_ERROR);
		}
		if (!preg_match('/^[a-zA-Z0-9_]+$/', $a)) {
			trigger_error('invalid GET param a!', E_USER_ERROR);
		}
		
		// 设置调试模式
		C('APP_DEBUG') || (isset($_GET['_debug']) ? C('APP_DEBUG', true) : '');
		
		// 如果开启了调试模式，注册exit动作到处理函数
		if (C('APP_DEBUG')) {
			debug();
		}
		
		return array($c, $a);
	}
	
	/**
	 * 执行应用程序
	 * 
	 * @access public
	 * 
	 * @return void
	 * 
	 * @throws AppException
	 */
	public static function exec($c, $a) {
		
		// 通过工厂类获取控制器对象
		$ctrl_obj = Factory::getCtrl($c);
		
		// 调用控制器动作
		$ctrl_obj->{$a}();
	}
	
	/**
	 * 运行应用实例 入口文件使用的快捷方法
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	public static function run() {
		
		list($c, $a) = App::init();
		
		// 记录应用初始化时间
		if (C('APP_DEBUG')) $GLOBALS['_t_app_init'] = microtime(true);
		
		App::exec($c, $a);
	}
	
	/**
     * 自定义错误处理
     *
     * @access public
     *
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     *
     * @return void
     */
    public static function appError($errno, $errstr, $errfile, $errline) {
    	
    	switch ($errno) {
    		case E_ERROR:
    		case E_USER_ERROR:
    			$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
            	halt($errorStr, 2);
            	break;
          		case E_STRICT:
          		case E_USER_WARNING:
          		case E_USER_NOTICE:
          		default:
            		$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
            		Log::record($errorStr,Log::NOTICE);
            		break;
    	}
    }
	
	/**
     * 自定义异常处理
     *
     * @access public
     *
     * @param mixed $e 异常对象
     */
    public static function appException($e) {
        halt($e->__toString(), 1);
    }
    
    /**
     * 显示trace信息
     */
    public static function showTrace() {
    	
    	$_trace = array();
    	
    	$_trace['当前页面'] = $_SERVER['REQUEST_URI'];
        // $_trace['模板缓存'] = $this->tpl_name;
        $_trace['请求方法'] = $_SERVER['REQUEST_METHOD'];
        $_trace['通信协议'] = $_SERVER['SERVER_PROTOCOL'];
        $_trace['请求时间'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
        $_trace['用户代理'] = $_SERVER['HTTP_USER_AGENT'];
        $_trace['执行时间'] = (microtime(true)-$GLOBALS['_t_app_start']).'秒';
        $_trace['会话ID']  = session_id();
        $log    =   Log::$log;
        $_trace['日志记录'] = count($log)?count($log).'条日志<br/>'.implode('<br/>',$log):'无日志记录';
        
        if (RUN_MOD !== 'deploy') {
	        $files =  get_included_files();
	        $_trace['加载文件'] = count($files).str_replace("\n",'<br/>',substr(substr(print_r($files,true),7),0,-2));
        }
        //$_trace =   array_merge($_trace,$this->trace);
        
        include C('APP_TRACE_FILE');
    }
}
