<?php
class DepartmentData extends BaseData {

	private $cfg_name='admin';
	private $tbl_alias='department';
	
	/**
	 * 通过department_id获得一条department信息
	 * @param int $department_id
	 */
	public function getDepartment($department_id) {
		
		// 在data层，每个传进来的参数，都必须调用safe_db_data参数
		$department_id = safe_db_data($department_id);
		
		// 第3个参数是用来分库分表的，不需要传空就可以
		// 第4个参数是用来标记是读操作，还是写操作的 r/w
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$ret = $db_op->queryRow("select * from ".$db_cfg['tbl']." where department_id='".$department_id."'");
		
		return $ret;
	}
	
	/**
	 * 通过一定条件获取department列表
	 * 
	 * @param array $where
	 * @param int $start
	 * @param int $pagesize
	 * @param string $orderby
	 */
	public function getDepartmentList($where, $start=0, $pagesize=20, $orderby='') {
		
		$where = safe_db_data($where);
		$start = intval($start);
		$pagesize = intval($pagesize);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$where_sql = parse_where($where);
		
		$ret = $db_op->queryList("select * from ".$db_cfg['tbl'].$where_sql." ".$orderby." LIMIT {$start}, {$pagesize}");
		
		return $ret;
	}
	
	/**
	 * 创建一个department
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function createDepartment($data) {
		
		$data = safe_db_data($data);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		// parse_data用来把要插入的数据解析成 “字段='xxx', 字段2='xxx', ...”的格式
		// 还有一个函数是parse_where，请直接查看源代码
		$set_values = parse_data($data);
		
		$ret = $db_op->execute("insert into ".$db_cfg['tbl']." set ".$set_values);
		
		return $ret;
	}
	
	/**
	 * 更新一个department
	 * 
	 * @param int $department_id
	 * @param array $data
	 */
	public function updateDepartment($department_id, $data) {
		
		$department_id = safe_db_data($department_id);
		$data = safe_db_data($data);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$set_values = parse_data($data);
		
		$ret = $db_op->execute("update ".$db_cfg['tbl']." set ".$set_values." where department_id='".$department_id."'");
		
		return $ret;
	}
}