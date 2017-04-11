<?php
/**
 * Created on 2010-5-9
 * 
 * DB类，
 * 封装所有相关的DB操作
 * 
 * @package db
 * @author wangyihuang
 * @version 2.0
 */

define('CLIENT_MULTI_RESULTS', 131072); // php调用mysql存储过程返回结果集需要使用这个

class DbOp {

	private $total;
	private $query_res;
	private $conn;
	
	// SQL 执行时间记录
    private $begin_time;
    
    // 是否显示调试信息 如果启用会在日志文件记录sql语句
    public $debug = false;
    
    // DB连接配置信息
    private $db_info = array();
    
    // 数据库连接列表
    private $conn_list = array();
    
    // 保存最近的一条SQL语句
    private $sql = '';
    
    // 当前连接ID
    private $link_id = null;
    
    // 当前查询ID
    private $query_id = null;
    
    // 返回或者记录影响记录数
    private $num_rows = 0;
    
    // 错误消息
    private $error = '';
	
	/**
	 * 构造器
	 */
	public function __construct() {
		//
	}
	
	/**
     * 取得数据库类实例
     * 
     * @param array db_cfg, array(host:$host, user:$user, pass:$pass, tbl:$tbl)
     *
     * @static
     * @access public
     *
     * @return mixed 返回db_op实例
     */
    public static function getInstance($db_cfg) {
    	
    	static $instance_list = array();
    	
    	$link_key = md5($db_cfg['host'].'-'.$db_cfg['user']);
    	
    	if (!isset($instance_list[$link_key])) {
    		
    		$instance_list[$link_key] = new DbOp();
    		$instance_list[$link_key]->initDb($db_cfg, $link_key);
    	}
    	
    	return $instance_list[$link_key];
    }

    /**
     * 初始化数据库信息
     *
     * @access public
     *
     * @param mixed db_cfg 数据库配置信息
     * @param string link_key
     *
     * @return void
     *
     * @throws AppException
     */
    public function initDb($db_cfg, $link_key) {
		
		// 得到DB当前操作表的连接信息
		$this->db_info = $db_cfg;
		$this->db_info['link_key'] = $link_key;
				
		// 得到DB当前操作的SQL语句
		$this->sql = '';
		
		// 是否开启调试
		if (C('APP_DEBUG')) $this->debug = true;
    }
    
    /**
     * 初始化数据库连接
     *
     * @access private
     *
     * @return void
     */
    private function initConnect() {
    	
    	$this->link_id = $this->connect();
    }
    
    /**
     * 连接数据库方法
     * 
     * @access public
     * 
     * @throws AppException
     */
    public function connect() {
    	
    	if (!isset($this->conn_list[$this->db_info['link_key']])) {
    		
    		// 如果是命令行，使用长连接
    		if (IS_CLI) {
    			$this->conn_list[$this->db_info['link_key']] = mysql_pconnect($this->db_info['host'], $this->db_info['user'], $this->db_info['pass']);
    		}
    		else {
    			$this->conn_list[$this->db_info['link_key']] = mysql_connect($this->db_info['host'], $this->db_info['user'], $this->db_info['pass'], true, CLIENT_MULTI_RESULTS);
    		}
    		
    		// 连接失败
	    	if (!$this->conn_list[$this->db_info['link_key']]) {
	    		throw_exception(mysql_error());
	    	}
	    	
	    	// 设置UTF8字符集
	    	mysql_query("SET NAMES 'utf8'", $this->conn_list[$this->db_info['link_key']]);
    	}
    	
    	return $this->conn_list[$this->db_info['link_key']];
    }
	
	/**
     * 
     * 释放查询结果
     * 
     * @access public
     */
    public function free() {
        @mysql_free_result($this->query_id);
        $this->query_id = 0;
    }
    
    /**
     * query, include:R
     * 执行查询 返回数据集
     * 
     * @param string sql, 要查询的sql
     * 
     * @return mixed
     * 
     * @throws AppException
	 * 该方法返回的是多维的数字索引数组，其中每个元素又是通过mysql_fetch_assoc()得到的一维关联数组
     */
    private function query($sql) {
    	
    	if (empty($sql)) {
    		$this->error('查询的SQL语句不能为空');
    		return false;
    	}
    	$this->sql = $sql;
    	
    	last_sql($this->sql);
    	
    	// 初始化数据库连接
		$this->initConnect();
		if (!$this->link_id) return false;
		
		// 释放前次的查询结果
		if ($this->query_id) {
			$this->free();
		}
		
		// R统计次数+1
		$this->Q(1);
		
		// 开始执行R
		$this->query_id = mysql_query($this->sql, $this->link_id);
		
		$this->debug();
		
		if (false === $this->query_id) {
		    $this->error();
            return false;
		}
		else {
			$this->num_rows = mysql_num_rows($this->query_id);
            
            return $this->mysqlFetchAll();
		}
    }
	
	/**
	 * execute, include:C/U/D
	 * 
	 * @param string sql, 要执行的sql
     * 
     * @return integer
     * 
     * @throws AppException
	 *该方法返回执行SQL语句所影响的记录数。
	 */
	public function execute($sql) { 
		
		if (empty($sql)) {
    		$this->error('执行的SQL语句不能为空');
    		return false;
    	}
		$this->sql = $sql;
		last_sql($this->sql);
				
		// 初始化数据库连接
		$this->initConnect(true);
		if (!$this->link_id) return false;
		
		// 释放前次的查询结果
		if ($this->query_id) {
			$this->free();
		}
		
		// CUD统计次数+1
		$this->W(1);
		
		$result = mysql_query($this->sql, $this->link_id);
		
		$this->debug();
		
		if (false === $result) {
			// 这里可以记录日志
			$this->error();
            return false;
		}
		else {
			$this->num_rows = mysql_affected_rows($this->link_id);
            return $this->num_rows;
		}		
	}
	
	/**
	 * 以key-value的形式返回整个查询数据集
	 * 
	 * @param string sql
	 */
	public function queryList($sql) {
		return $this->query($sql);
	}
	
	/**
	 * 以key-value的形式返回查询数据集的第一行
	 * 
	 * @param string sql
	 */
	public function queryRow($sql) {
		//die($sql);

		$rows = $this->query($sql);
		
		if (is_array($rows)) {
			if (count($rows) > 0) {
				return array_shift($rows);
			}
			else {
				return array();
			}
		}
		
		return false;		
	}
	
	/** 
	 * 返回数据集中第一条记录的第一个字段的值
	 * 
	 * @param string sql
	 */
	public function queryCount($sql) {
		
		$rows = $this->query($sql);
		
		if (is_array($rows) && (count($rows) > 0)) {
			return array_shift($rows[0]);
		}
		else {
			return false;
		}		
	}
	
	public function getInsertId() {
		
		// 取得上一步 INSERT 操作产生的 ID 
		return mysql_insert_id($this->link_id);
	}
    
    /**
     * 获取最近一次查询的sql语句
     *
     * @access public
     *
     * @return string
     */
    public function getLastSql() {
        return $this->sql;
    }

    /**
     * 获取最近的错误信息
     *
     * @access public
     *
     * @return string
     */
    public function getLastError() {
        return $this->error;
    }
	
	/**
	 * 获得所有的查询数据
     * 
     * @access private
     * 
     * @return array
	 */
	private function mysqlFetchAll() {
		
		// 返回数据集
        $ret = array();
        if($this->num_rows >0) {
            while($row = mysql_fetch_assoc($this->query_id)){
                $ret[]   =   $row;
            }
            mysql_data_seek($this->query_id, 0);
        }
        return $ret;
	}
	
	/**
     * 数据库调试 记录当前SQL的信息（sql语句、runtime）
     *
     * @access private
     */
    private function debug() {
        
        // 记录操作结束时间
        if ( $this->debug )    {
            $runtime    =   number_format(microtime(true) - $this->begin_time, 6);
            Log::record(" RunTime:".$runtime."s SQL = ".$this->sql,Log::SQL);
        }
    }
	
	/**
     * 查询次数更新或者查询
     *
     * @access public
     *
     * @param mixed $times
     *
     * @return void
     */
    public function Q($times='') {
    	
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }
        else{
            $_times++;
            // 记录开始执行时间
            $this->begin_time = microtime(true);
        }
    }

    /**
     * 写入次数更新或者查询
     *
     * @access public
     *
     * @param mixed $times
     *
     * @return void
     */
    public function W($times='') {
    	
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }
        else{
            $_times++;
            // 记录开始执行时间
            $this->begin_time = microtime(true);
        }
    }
    
    /**
     * 关闭数据库
     * 
     * @access public
     * 
     * @throws AppException
     */
    public function close() {
    	
        if (!empty($this->query_id))
            @mysql_free_result($this->query_id);
        if ($this->link_id && !mysql_close($this->link_id)){
            throw_exception($this->error());
        }
        $this->link_id = 0;
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * 
     * @access public
     * 
     * @return string
     */
    public function error($err_msg='') {
    	
    	if (empty($err_msg)) {
        	$this->error .= "<br />\nMYSQL错误: ".mysql_error($this->link_id);
    	}
    	else {
    		$this->error = $err_msg;
    	}
        
        $this->error .= "<br />\nSQL语句: ".$this->sql;
        
        if ($this->debug) trigger_error($this->error, E_USER_ERROR);
        
        return $this->error;
    }

   /**
     * 析构方法
     * 
     * @access public
     */
    public function __destruct() {
        // 关闭连接
        $this->close();
    }
}
