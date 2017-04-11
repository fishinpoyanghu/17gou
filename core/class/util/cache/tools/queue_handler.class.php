<?php
/**
 * Created on 2010-6-27
 *
 * 通用队列处理，适合对表进行插入、更新、删除，但不需要再进行例外的处理的情况
 * 
 * 队列元素定义
 * 	array(
 * 		op:$op, // 操作类型，add/update/delete
 * 		alias:$alias, // 表别名，例如score_log
 * 		hash_key:$hash_key, // 分库分表的hash_key的值
 * 		data:$data, // 可选，要操作的数据保存在这里，只针对add/update有效
 * 		where:$where, // 可选，只针对update/delete有效
 * 	)
 * 
 * $queue_handler = new QueueHandler('u', 'terminal_queue');
 * 
 * while($queue_handler->hasNext()) {
 * 		$data = $queue_handler->getNext();
 * 		
 * 		// 如果data是只包含op和data，需要在这里进行重新封装，让data包含alias, hash_key[, where]
 * 		
 * 		$ret = $queue_handler->handleData($data);
 * 		
 * 		// 如果处理成功了
 * 		if ($ret) {
 * 			// 通常，不管是add，update还是delete，很多时候需要更新相应的缓存
 * 			// 在这里，你需要根据你的需要，进行自定义的代码开发：
 * 			// 更新缓存、二次分发数据、...
 * 		}
 * }
 * 
 * @author wangyihuang
 */

class QueueHandler {
	
	// 保存队列对象
	private $queue;
	
	// 保存下一个队列数据的数据
	private $next_data;
	
	// 保存当队列数据为空时的sleep时间
	private $sleep_wait;
	
	// 为在线列表更新使用
	private $update_count=0;
	
	// 记录QueueHandler的启动时间
	private $start_time = 0;
	
	// 是否有最长时间限制，默认是false
	private $max_time_limit = false;
	
	/**
	 * 构造器
	 */
    public function __construct($app_name='', $q_name='', $sleep_wait=0, $max_time_limit=false) {
    	
    	$this->queue = new Queue($app_name, $q_name);
    	
    	$this->next_data = false;
    	
    	$this->sleep_wait = intval($sleep_wait);
    	
    	$this->start_time = time();
    	
    	$this->max_time_limit = $max_time_limit;
    }
    
    /**
     * 判断是否队列里还有数据
     * 
     * @return true/false
     */
    public function hasNext() {
    	
    	// 如果但个实例运行时间超过300秒，返回false，强制返回false，表示木有数据啦
    	// 同时，为了防止有些老脚本没有按照这个修改，修改start_time
    	if ($this->max_time_limit && ((time()-$this->start_time) > 300)) {
    		cron_log('QueueHandler has run over 300 seconds, restart the script');
    		$this->start_time = time();
    		return false;
    	}
    	
    	$this->next_data = $this->getMsg();
    	
    	return $this->next_data;
    }
    
    /**
     * 取得下一个队列数据
     * 
     * @return array
     */
    public function getNext() {
    	
    	return $this->next_data;
    }
    
    /**
     * 往队列里push数据
     * 
     * 警告：慎用
     */
    public function putBack($msg='') {
    	
    	return $this->putMsg($msg);
    }
    
    /**
     * 在线列表处理使用，返回上一次update被更新的条数
     */
    public function getUpdateCount() {
    	
    	return $this->update_count;
    }
    
    /**
     * 处理一个队列数据
     * 
     * @param array data
     * @param boolean replace_into, 是否使用replace into，默认false
     */
    public function handleData($data, $replace_into=false) {
    	
    	if (!is_array($data)) {
    		cron_log("queue_handler.handleData: data is not an array!");
    		$this->next_data = false;
    		return false;
    	}
    	
    	if (!(isset($data['op']) && isset($data['alias']) && isset($data['hash_key']))) {
    		cron_log("queue_handler.handleData: data is invalid!");
    		$this->next_data = false;
    		return false;
    	}
    	
    	// 开始进行处理
    	$db_cfg = load_db_cfg($data['cfg_name'], $data['alias'], $data['hash_key'], 'w');
				
		$db_op = DbOp::getInstance($db_cfg);
		
		// op处理
		$ret = $this->handleOp($db_op, $db_cfg, $data, $replace_into);
		
		// temp! 如果有mysql错误，就输出
		$error = mysql_error();
		if ($error) {
			cron_log("queue_handler.handleData: ".$error);
		}
		
		$this->next_data = false;
		
		return $ret;
    }
    
    /**
     * op处理
     */
    private function handleOp(&$db_op, $db_cfg, $data, $replace_into) {
    	
    	// add
    	if ($data['op'] == 'add') {
    		
    		if (!isset($data['data'])) {
    			
    			cron_log("queue_handler.handleData->handleOp: data invalid, no data!");
    			return false;
    		}
    		
    		$data_values = parse_data($data['data']);
    		
    		if ($replace_into) {
    			$sql = 'REPLACE INTO '.$db_cfg['tbl'].' SET '.$data_values;
    		}
    		else {
    			$sql = 'INSERT INTO '.$db_cfg['tbl'].' SET '.$data_values;
    		}
    		$this->debug($sql);
    		    		
    		$ret = $db_op->execute($sql);
    		
    		if ($ret === false) {
    			cron_log("queue_handler.handleData->handleOp: insert error: ".$sql);
    			
    			return false;
    		}
    		
    		return true;
    	}
    	// update
    	elseif ($data['op'] == 'update') {
    		
    		if (!isset($data['data'])) {
    			
    			cron_log("queue_handler.handleData->handleOp: data invalid, no data!");
    			return false;
    		}
    		
    		$data_values = parse_data($data['data']);
    		
    		$where = '';
    		
    		if (isset($data['where'])) {
    			$where = ' WHERE ' . $this->parse_where($data['where']);
    		}
    		
    		$sql = 'UPDATE '.$db_cfg['tbl'].' SET '.$data_values.$where;
    		$this->debug($sql);
    		
    		$ret = $db_op->execute($sql);
    		
    		if ($ret === false) {
    			
    			$this->update_count = 0;
    			cron_log("queue_handler.handleData->handleOp: update error: ".$sql);    			
    			return false;
    		}
    		
    		$this->update_count = $ret;
    		
    		return true;
    	}
    	// delete
    	elseif ($data['op'] == 'delete') {
    		
    		if (!isset($data['where'])) {
    			
    			echo "data invalid, no where!\n";
    			return false;
    		}
    		
    		$where = ' WHERE ' . $this->parse_where($data['where']);
    		
    		$sql = 'DELETE FROM '.$db_cfg['tbl'].$where;
    		$this->debug($sql);
    		
    		$ret = $db_op->execute($sql);
    		
    		if ($ret === false) {
    			
    			cron_log("queue_handler.handleData->handleOp: delete error: ".$sql);    			
    			return false;
    		}
    		
    		return true;
    	}
    }
    
    /**
     * 调试函数，记录/打印Sql
     * 
     * @param string sql, 可选，当为空时表示打印
     */
    public function debug($sql='') {
    	
    	static $sql_list=array();
    	
    	if (empty($sql)) {
    		print_r($sql_list);
    	}
    	else {
    		$sql_list[] = $sql;
    	}
    	
    	return ;
    }
    
    /**
     * 辅助函数，
     * data['where']解析，目前只支持and
     * 	array(
     * 		'user_id'=>20101,
     * 		'record_time'=>54232,
     * 	)
     */
    private function parse_where($where) {
    	
    	if (!is_array($where)) return '';
	
		$ret = array();
		
		foreach ($where as $k=>$v) {
			
			$ret[] = '`'.$k.'`=\''.$v.'\'';
		}
		
		return implode(' AND ', $ret);
    }
    
    /**
     * hasNext辅助函数
     * 
     * 取得下一个队列数据
     */
    private function getMsg() {
    	
    	// pget，使用长连接！
    	try {
    		$ret = $this->queue->pget();
    	}
    	catch (Exception $ex) {
    		cron_log("queue_handler.handleData->getMsg: pget exception");
    		$this->checkSleep();
    		return false;
    	}
    	
    	// temp! 跟踪队列错误
    	if ($ret === false) {
    		cron_log("queue_handler.handleData->getMsg: queue error! [".date('Y-m-d H:i:s', time())."]");
    	}
    	if (($ret === false) || ($ret === 'HTTPSQS_GET_END')) {
    		$this->checkSleep();
    		return false;
    	}
    	
    	$ret = json_decode($ret, true);
    	
    	return $ret;
    }
    
    /**
     * 重新往队列里push数据
     */
    private function putMsg($msg) {
    	
    	// pget，使用长连接！
    	try {
    		$ret = $this->queue->pput($msg);
    	}
    	catch (Exception $ex) {
    		cron_log("queue_handler.handleData->putMsg: pput exception");
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 检测当队列里没有数据时，是否需要进入sleep
     */
    private function checkSleep() {
    	
    	if ($this->sleep_wait > 0) {
    		//echo "go to sleep ".$this->sleep_wait." seconds\n";
    		sleep($this->sleep_wait);
    	}
    }
}
?>