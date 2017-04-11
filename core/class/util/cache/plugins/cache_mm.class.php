<?php
/**
 * Created on 2010-4-19
 *
 * Memcached Cache Plugin
 * 
 * @author wangyihuang
 */

// 类定义开始
class CacheMm {
	
	private $mm=null;
	
	private $host='';
	private $port='';
	
	/**
     * 构造函数
     * 
     * @access public
     */
    public function __construct() {
    	// do nothing.
    }
    
    public function __destruct() {
    	
    	$this->_disconnect();
    }
    
    /**
     * 连接
     */
    public function connect($host, $port) {
    	$this->host = $host;
    	$this->port = $port;
    }
    
    /**
     * 连接
     */
    public function _connect() {
    	
    	$this->mm = new Memcache();
    	
    	$this->mm->addServer($this->host, $this->port, false);
    	
    	return;
    }
    
    /**
     * 断开连接
     */
    public function _disconnect() {
    	
    	if (!is_null($this->mm)) {
    		$this->mm->close();
    	}
    }
    
    /**
     * 得到一个缓存值 
     * 
     * @param string key
     */
    public function get($key) {
    	
    	$this->_connect();
    	
    	$ret = $this->mm->get($key);
    	
    	$this->_disconnect();
    	
    	return $ret;
    }    
    
    /**
     * 设置一个缓存
     * 
     * @param string key
     * @param string value
     * @param int timeout, 过期时间，默认0(永不过期)
     */
    public function set($key, $value, $timeout=0) {
    	
    	$this->_connect();
    	
    	$ret = $this->mm->set($key, $value, 0, $timeout);
    	
    	$this->_disconnect();
    	
    	return $ret;
    }

    public function add($key, $value, $timeout=0) {

        $this->_connect();
        $add = $this->mm->add($key, $value, 0, $timeout);

        $this->_disconnect();

        return $add;
    }
    
    /**
     * 删除一个缓存
     * 
     * @param string key
     */
    public function delete($key) {
    	
    	$this->_connect();
    	
    	$ret = $this->mm->delete($key);
    	
    	$this->_disconnect();
    	
    	return $ret;
    }
}
