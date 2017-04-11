<?php
/**
 * Created on 2010-7-16
 *
 * 队列Class
 * 
 * $hosts参数的定义规则：
 * $hosts = array(
 * 		's_1' => array('host'=>'192.168.72.27', 'port'=>7121),
 * 		's_2' => array('host'=>'192.168.72.28', 'port'=>7121),
 * 	)
 * 
 * $queue_list参数的定义规则
 * $queues = array(
 * 		'feed'   => array('server_id'=>array('s_1'));
 * 		'online' => array('server_id'=>array('s_1'));
 * 		'exp'    => array('server_id'=>array('s_2'));
 * 	)
 * 
 * 配置文件里这样添加：
 * 'QUEUE' => array(
 * 		'hosts'  => $hosts,
 * 		'queues' => $queues,
 * 	)
 * 
 * 所有Queue的端口号都是7000~8000之间；
 * 每个服务器需要定义一个别名，例如s_1, s_2；定义别名的好处是，当服务器地址发生变化时，别名是不变的，不会影响原有的队列数据分布
 *  
 * @author wangyihuang
 */

class Queue {

    private $cfg_name;
    private $cfg_key;
	private $q_name;  // 队列的名称
	private $real_q_name; // 真实的队列名称
	private $redis; // redis实例
	private $connected=false; // 标记redis是否连接
	
	private $host;
	private $port;
	
	/**
     * 构造函数
     * 
     * @param string cfg_name, 队列配置文件名称，例如home.queue.php的cfg_name是home
     * @param string q_name, 队列的名称
     * @param mixed conf, 队列的配置信息，可选
     * @access public
     */
    public function __construct($cfg_name='', $q_name='') {
    	
    	if (empty($cfg_name)) {
    		halt(__FUNCTION__.': 没有指定cfg_name！');
    	}
    	if (empty($q_name)) {
    		halt(__FUNCTION__.': 没有指定队列名称！');
    	}
    	
    	$key = 'core_queue_name_'.$cfg_name;
    	$this->cfg_name = $cfg_name;
    	$this->cfg_key  = $key;
    	$this->q_name = $q_name;
    	$this->real_q_name = $this->cfg_name . '-' . $this->q_name;
    	
    	// 如果配置文件没有加载过
    	$qname_list = C($key);
    	if (!$qname_list) {
	    	
	    	// 判断缓存文件是否存在
	    	$filename = CONFIG_PATH.'queue/'.$cfg_name.'.queue.php';
			
			// 如果文件不存在
			if (!is_file($filename)) {
				halt('cfg_name:'.$cfg_name.' is invalid, queue config file:'.$filename.' is not found');
			}
	    	
	    	$qname_list = include $filename;
	    	
	    	// 配置文件返回的不是数组
			if (!is_array($qname_list)) {
				halt('return value is not an array in queue config file:'.$filename);
			}
			
			C(array($key=>$qname_list));
    	}
		
		// 判断队列名称是否存在
		if (!isset($qname_list[$q_name])) {
			halt('队列名称['.$q_name.']在配置文件: '.$filename.'里没有定义!');
		}
		
		// 判断队列服务器是否存在
		$queue_list = C('core_queue_list');
    	
    	if (!isset($queue_list[$qname_list[$q_name]])) {
    		halt(__FUNCTION__.': 队列名称['.$q_name.']定义的队列服务器: '.$qname_list[$q_name].'没有在queue.inc.php里定义');
    	}
    	
    	$this->host = $queue_list[$qname_list[$q_name]][0];
    	$this->port = $queue_list[$qname_list[$q_name]][1];
    }
    
    public function __destruct() {
    	
    	$this->disconnect();
    }
    
    /**
     * 连接队列服务器
     * 
     * @param boolean persistent true/false, default:false
     */
    public function connect($persistent=false, $database=15) {
    	
    	$host = array(
    		'host' => $this->host,
    		'port' => $this->port,
    		'database' => $database,
    	);
    	
    	if (!isset($host['database'])) $host['database'] = $database;
    	$host['connection_persistent'] = $persistent;
    	
    	// 如果已连接，直接返回吖
    	//if ($this->connected) return true;
    	
    	try {
    		$this->redis = new Predis_Client($host);
    		$this->connected = true;
    	}
    	catch(Exception $ex) {
    		$this->connected = false;
    		
    		throw_exception('redis 队列服务连接失败');
    	}
    }
    
    /**
     * 关闭连接
     */
    public function disconnect() {
    	
    	if (is_object($this->redis)) {
    		$this->connected = false;
    		$this->redis->disconnect();
    	}
    }
    
    /**
     * 将文本信息放入一个队列
     *  如果入队列成功，返回布尔值：true
     *  如果如队列失败，返回布尔值：false
     * 
     * @param string $data
     */
    public function put($data) {
    	
    	$this->connect();
    	
    	$ret = $this->redis->lpush($this->real_q_name, $data);
    	
    	$this->disconnect();
    	
    	if ($ret) return true;
    	else return false;
    }
    
    /**
     * 警告！高级方法，如果你对队列的机制不是完全的了解，请不要使用！
     * 
     * 将文本信息追加的队列的尾部
     *  如果入队列成功，返回布尔值：true
     *  如果如队列失败，返回布尔值：false
     * 
     * @param string $data
     */
    public function append($data) {
    	
    	$this->connect();
    	
    	$ret = $this->redis->rpush($this->real_q_name, $data);
    	
    	$this->disconnect();
    	
    	if ($ret) return true;
    	else return false;
    }
    
    /**
     * 从一个队列中取出文本信息
     *  返回该队列的内容
     *  如果没有未被取出的队列，则返回文本信息：HTTPSQS_GET_END
     */
    public function get() {
    	
    	$this->connect();
    	
    	$ret = $this->redis->rpop($this->real_q_name);
    	
    	$this->disconnect();
    	
    	if (is_null($ret)) return 'HTTPSQS_GET_END';
    	
    	return $ret;
    }
	
	/**
	 * 查看队列状态
	 */
    public function status() {
    	
    	$this->connect();
    	
    	$ret = $this->redis->info();
    	
    	$this->disconnect();
    	
    	return $ret;
    }
    
    /**
     * persistent put
     * 将文本信息放入一个队列
     *  如果入队列成功，返回布尔值：true
     *  如果如队列失败，返回布尔值：false
     * 
     * @param string $data
     */
    public function pput($data) {
    	
    	$this->connect(true);
    	
    	$ret = $this->redis->lpush($this->real_q_name, $data);
    	
    	if ($ret) return true;
    	else return false;
    }
    
    /**
     * 警告！高级方法，如果你对队列的机制不是完全的了解，请不要使用！
     * 
     * 将文本信息追加的队列的尾部
     *  如果入队列成功，返回布尔值：true
     *  如果如队列失败，返回布尔值：false
     * 
     * @param string $data
     */
    public function pappend($data) {
    	
    	$this->connect(true);
    	
    	$ret = $this->redis->rpush($this->real_q_name, $data);
    	
    	if ($ret) return true;
    	else return false;
    }
    
    /**
     * persistent get
     * 从一个队列中取出文本信息
     *  返回该队列的内容
     *  如果没有未被取出的队列，则返回文本信息：HTTPSQS_GET_END
     */
    public function pget() {
    	
    	$this->connect(true);
    	
    	$ret = $this->redis->rpop($this->real_q_name);
    	
    	if (is_null($ret)) return 'HTTPSQS_GET_END';
    	
    	return $ret;
    }
	
	/**
	 * persistent status
	 * 查看队列状态
	 */
    public function pstatus() {
    	
    	$this->connect(true);
    	
    	return $this->redis->info();
    }
}
