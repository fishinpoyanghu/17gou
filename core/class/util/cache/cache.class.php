<?php
/**
 * Created on 2010-4-17
 *
 * Cache
 * 
 * @author wangyihuang
 */

class Cache {
	
	protected $cfg_name;
	
	protected $cfg_key;

    /**
     * 构造函数
     * 
     * @access public
     */
    public function __construct($cfg_name) {
    	
    	if (empty($cfg_name)) {
    		halt(__FUNCTION__.': 没有指定缓存配置文件名称！');
    	}
    	
    	$key = 'core_cache_class_'.$cfg_name;
    	
    	$this->cfg_name = $cfg_name.'17gou';
    	$this->cfg_key  = $key;
    	
    	// 如果配置文件加载过，直接返回
    	if (C($key)) return;
    	
    	// 判断缓存文件是否存在
    	$filename = CONFIG_PATH.'cache/'.$cfg_name.'.cache.php';
		
		// 如果文件不存在
		if (!is_file($filename)) {
			halt('cfg_name:'.$cfg_name.' is invalid, cache config file:'.$filename.' is not found');
		}
    	
    	$class_list = include $filename;
    	
    	// 配置文件返回的不是数组
		if (!is_array($class_list)) {
			halt('return value is not an array in cache config file:'.$filename);
		}
		
		C(array($key=>$class_list));
    }
    
    /**
     * 得到一个缓存值
     * 
     * @param string class 缓存分类名
     * @param string key
     */
    public function get($class, $key) {
    	
    	$cache_conf = $this->load_class($class);
    	
    	// 如果关闭了缓存，直接返回false
    	if ($cache_conf['off']) return false;
    	
    	$cache = get_instance_of('CacheMM', 'connect', array($cache_conf['host'], $cache_conf['port']));
    	
    	// key
    	$key = $this->get_key($key, $class);
    	
    	$this->Q(1);
    	
    	$ret = $cache->get($key);
    	
    	return $this->simple_get($ret, $key, $class);
    }
    
    /**
     * 设置一个缓存
     * 
     * @param string class 缓存分类名
     * @param string key
     * @param string value
     */
    public function set($class, $key, $value) {
    	
    	$cache_conf = $this->load_class($class);
    	
    	$cache = get_instance_of('CacheMM', 'connect', array($cache_conf['host'], $cache_conf['port']));
    	
    	// key
    	$key = $this->get_key($key, $class);
    	
    	$this->W(1);
    	
    	return $cache->set($key, $value, $cache_conf['timeout']);
    }

    public function add($class,$key,$value){
        $cache_conf = $this->load_class($class);

        $cache = get_instance_of('CacheMM', 'connect', array($cache_conf['host'], $cache_conf['port']));
        //key
        $key = $this->get_key($key, $class);

        return $cache->add($key, $value,$cache_conf['timeout']);
    }
    
    /**
     * 删除一个缓存
     * 
     * @param string class 缓存分类名
     * @param string key
     */
    public function delete($class, $key) {
    	
    	$cache_conf = $this->load_class($class);
    	
    	$cache = get_instance_of('CacheMM', 'connect', array($cache_conf['host'], $cache_conf['port']));
    	
    	// key
    	$key = $this->get_key($key, $class);
    	
    	return $cache->delete($key);
    }
    
    /**
     * 读取缓存次数
     */
    public function Q($count='') {    	
    	static $_times = 0;
    	if (empty($count)) return $_times;
    	else $_times++;
    }
    
    /**
     * 写入缓存次数
     */
    public function W($count='') {    	
    	static $_times = 0;
    	if (empty($count)) return $_times;
    	else $_times++;
    }
    
    /**
     * 得到key
     */
    private function get_key($key, $class) {
//     	dump($this->cfg_name.'-'.$class.'-'.$key);
    	if (is_array($key)) {
    		$ret = array();
    		foreach ($key as $k=>$v) {
    			$ret[] = $this->cfg_name.'-'.$class.'-'.$v;
    		}
    		return $ret;
    	}
    	else
    		return $this->cfg_name.'-'.$class.'-'.$key;
    }
    
    /**
     * 多key取值时，最后get，返回结果key简化掉前缀 app和class
     */
    private function simple_get($res, $key, $class) {
    	
    	if (is_array($key)) {
    		$ret = array();
    		$prefix = $this->cfg_name.'-'.$class.'-';
    		foreach ($res as $k=>$v) {
    			$k = str_replace('*&#^$#*=='.$prefix, '', '*&#^$#*=='.$k); // ''*&#^$#*=='.'防止$k中有多个跟prefix一样的字串
    			$ret[$k] = $v;
    		}
    		
    		$ret2 = array();
    		foreach ($key as $k=>$v) {
    			
    			$v = str_replace('*&#^$#*=='.$prefix, '', '*&#^$#*=='.$v); // ''*&#^$#*=='.'防止$v中有多个跟prefix一样的字串
    			
    			if (isset($ret[$v])) $ret2[$v] = $ret[$v];
    			else {
    				$ret2[$v] = false;
    			}
    		}
    		return $ret2;
    	}
    	else return $res;
    }
    
    /**
     * 检查缓存分类名class的合法性
     * 
     * @param string class
     * 
     * @return array array(cache信息，server信息)
     */
    private function load_class($class) {
    	
    	// 如果缓存类型为空
    	if (empty($class)) {
    		halt(__FUNCTION__.': 缓存类型不能为空');
    	}
    	
    	$class_list = C($this->cfg_key);
		
    	if (!isset($class_list[$class])) {
    		halt(__FUNCTION__.': 缓存类型['.$class.']在配置文件里没有定义');
    	}
    	
    	$sid = $class_list[$class]['sid'];
    	
    	// 判断sid是否合法
    	$cache_list = C('core_cache_list');
    	
    	if (!($sid && isset($cache_list[$sid]))) {
    		halt(__FUNCTION__.': 缓存类型['.$class.']定义的sid: '.$sid.'没有在cache.inc.php里定义');
    	}
    	
    	return array(
    		'host' => $cache_list[$sid][0],
    		'port' => $cache_list[$sid][1],
    		'timeout' => $class_list[$class]['timeout'],
    		'off' => isset($class_list[$class]['off']) && $class_list[$class]['off'] ? true : false,
    	);    	
    }
}
