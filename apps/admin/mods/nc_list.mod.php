<?php
/**
 * @since 2016-01-05
 */
class NcListMod extends BaseMod{
	
	public $dbConf;
	
	/**
	 * 设置mysql配置
	 * @param string $cfg_name
	 * @param string $tbl_alias
	 */
	public function setDbConf($cfg_name, $tbl_alias) {
		$this->dbConf = load_db_cfg($cfg_name, $tbl_alias, '', 'rw');
	}
	
	/**
	 * 获取数据
	 * @param array $where
	 * @param array $order
	 * @param array $limit
	 * @param array $column 需要的字段
	 * @return multitype:
	 */
	public function getDataList($where = array(), $column = array(), $order = array(), $limit = array(), $noEmpty = true){
		if(empty($this->dbConf)) return array();
	
		$whereSql = '';
		$orderSql = '';
		$limitSql = '';
		$selectSql = '*';
		if($where){
			$where = safe_db_data($where);
			$whereSql = parse_where($where);
		}
		//组装order
		if($order){
			$orderSql = "order by ";
			$orderType = array('desc','asc');
			foreach($order as $key=>$val){
				$val = (in_array($val, $orderType)) ? $val : 'asc';
				$orderSql .= "{$key} {$val},";
			}
			$orderSql = rtrim($orderSql, ',');
		}
		if($limit){
			$limit['begin'] = empty($limit['begin']) ? 1 : $limit['begin'];
			$limit['length'] = empty($limit['length']) ? 10 : $limit['length'];
			$begin = $limit['begin'] - 1;
			if($begin < 0){
				$begin = 0;
			}
			$limitSql = "limit {$begin},{$limit['length']}";
		}
		if($column){
			$selectSql = '`'.implode('`,`', $column).'`';
		}
	
		$db_op = DbOp::getInstance($this->dbConf);
		$ret = $db_op->queryList("select {$selectSql} from {$this->dbConf['tbl']} {$whereSql} {$orderSql} {$limitSql}");
		if($noEmpty && empty($ret)){
			api_result(0, '数据为空');
		}
		return $ret;
	}
	
	/**
	 * 获取一条数据
	 */
	public function getDataOne($where = array(), $column = array(), $order = array(), $limit = array(), $noEmpty = true){
		$data = $this->getDataList($where, $column, $order, $limit, $noEmpty);
		if(empty($data)) return array();
		return $data[0];
	}
	
	/**
	 * 连表查询
	 * @param string $sql
	 * @param boolean $noEmpty
	 * @return array
	 */
	public function getDataJoinTable($join, $where = array(), $column = array(), $order = array(), $limit = array(), $noEmpty = true){
		if(empty($this->dbConf)) return array();
	
		$whereSql = '';
		$orderSql = '';
		$limitSql = '';
		$selectSql = '*';
		//组装where
		if($where){
			$where = safe_db_data($where);
			foreach ($where as $k=>$v) {
				if (is_array($v)) {
					if (count($v) >= 3) $k = $v[2];
					if (($v[1] == 'in') && is_array($v[0])) {
						foreach ($v[0] as $m=>$n) {
							$v[0][$m] = "'".$n."'";
						}
						$ret[] = $k.' '.$v[1].' ('.implode(',', $v[0]).')';
					}else {
						$ret[] = $k.' '.$v[1].' \''.$v[0].'\'';
					}
				}else {
					$ret[] = $k.'=\''.$v.'\'';
				}
			}
			$whereSql = ' WHERE '. implode(' AND ', $ret);
		}
		//组装order
		if($order){
			$orderSql = "order by ";
			$orderType = array('desc','asc');
			foreach($order as $key=>$val){
				$val = (in_array($val, $orderType)) ? $val : 'asc';
				$orderSql .= "{$key} {$val},";
			}
			$orderSql = rtrim($orderSql, ',');
		}
		//组装limit
		if($limit){
			$limit['begin'] = empty($limit['begin']) ? 1 : $limit['begin'];
			$limit['length'] = empty($limit['length']) ? 10 : $limit['length'];
			$begin = $limit['begin'] - 1;
			if($begin < 0){
				$begin = 0;
			}
			$limitSql = "limit {$begin},{$limit['length']}";
		}
		//组装column
		if($column){
			$selectSql = implode(',', $column);
		}
	
		$db_op = DbOp::getInstance($this->dbConf);
		$sql = "select {$selectSql} from {$join['from']} ";
		//组装join
		foreach($join['join'] as $val){
			$sql .= " {$val['join_type']} {$val['join_table']} on ".implode(" and ", $val['on']);
		}
		$sql .= " {$whereSql} {$orderSql} {$limitSql}";
		$ret = $db_op->queryList($sql);
		if($noEmpty && empty($ret)){
			api_result(0, '数据为空');
		}
		return $ret;
	}
	
	/**
	 * 执行原声sql获取数据
	 */
	public function getDataBySql($sql, $noEmpty = true){
		if(empty($this->dbConf)) return array();
		$db_op = DbOp::getInstance($this->dbConf);
		$ret = $db_op->queryList($sql);
		if($noEmpty && empty($ret)){
			api_result(0, '数据为空');
		}
		return $ret;
	}
	
	/**
	 * 插入数据
	 */
	public function insertData($data){
		if(empty($this->dbConf)) return false;
		$data = safe_db_data($data);
		$set_values = parse_data($data);
		
		$db_op = DbOp::getInstance($this->dbConf);
		C('debug_sql', "insert into ".$this->dbConf['tbl']." set ".$set_values);
		return $db_op->execute("insert into ".$this->dbConf['tbl']." set ".$set_values);
	}
	
	/**
	 * 插入二维数组
	 * 二维数组的各个key顺序必须一致
	 */
	public function insertMultyData($data){
		if(empty($this->dbConf)) return false;
		if(empty($data)) return true;
		
		$sql = "insert into ".$this->dbConf['tbl'];
		$key = array_keys($data[0]);
		$sql .= "(`".implode("`,`", $key)."`)values";
		foreach($data as $val){
			$sql .= "('".implode("','", $val)."'),";
		}
		$sql = rtrim($sql, ',');
		$db_op = DbOp::getInstance($this->dbConf);
		return $db_op->execute($sql);
	}
	
	/**
	 * 执行原声sql
	 */
	public function executeSql($sql){
		if(empty($this->dbConf)) return array();
		$db_op = DbOp::getInstance($this->dbConf);
		
		return $db_op->execute($sql);
	}
	
	/**
	 * 更新数据
	 */
	public function updateData($where, $data){
		if(empty($this->dbConf)) return array();
		
		$data = safe_db_data($data);
		$set_values = parse_data($data);
		$where = safe_db_data($where);
		$whereSql = parse_where($where);
		
		$db_op = DbOp::getInstance($this->dbConf);
		$sql = "update {$this->dbConf['tbl']} set {$set_values} {$whereSql}";
		
		$ret = $db_op->execute($sql);
		
		return $ret;
	}
	
	/**
	 * 转为数组
	 */
	public function toArray($result){
		$data = array();
		if(!empty($result)){
			foreach($result as $val){
				$data[] = $val;
			}
		}
		return $data;
	}
}