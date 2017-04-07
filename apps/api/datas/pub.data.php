<?php
class PubData extends BaseData {
	
	private $cfg_name = '';
	private $tbl_alias = '';
	private $pkid_name = '';
	
	/**
	 * 初始化 cfg_name和tbl_alias
	 * 
	 * @param string $cfg_name
	 * @param string $tbl_alias
	 * @param string $pkid_name, 主键名称
	 */
	public function init($cfg_name, $tbl_alias, $pkid_name) {
		
		$this->cfg_name = $cfg_name;
		$this->tbl_alias = $tbl_alias;
		$this->pkid_name = safe_db_data($pkid_name);
	}
	
	/**
	 * 通过主键ID获得数据库表一行信息
	 * 
	 * @param string $pkid, 主键值
	 */
	public function getRow($pkid) {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
	
		// 在data层，每个传进来的参数，都必须调用safe_db_data参数		
		$pkid = safe_db_data($pkid);
	
		// 第3个参数是用来分库分表的，不需要传空就可以
		// 第4个参数是用来标记是读操作，还是写操作的 r/w
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
	
		$db_op = DbOp::getInstance($db_cfg);
// 		dump("select * from ".$db_cfg['tbl']." where ".$this->pkid_name."='".$pkid."' LIMIT 1");
		$ret = $db_op->queryRow("select * from ".$db_cfg['tbl']." where ".$this->pkid_name."='".$pkid."' LIMIT 1");
	
		return $ret;
	}
	
	/**
	 * 通过一定条件获得数据库表一行信息
	 *
	 * @param array $where
	 */
	public function getRowWhere($where) {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
		
		// 在data层，每个传进来的参数，都必须调用safe_db_data参数
		$where = safe_db_data($where);
		
		// 第3个参数是用来分库分表的，不需要传空就可以
		// 第4个参数是用来标记是读操作，还是写操作的 r/w
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$where_sql = parse_where($where);
// 		dump("select * from ".$db_cfg['tbl'].$where_sql." LIMIT 1");
		$ret = $db_op->queryRow("select * from ".$db_cfg['tbl'].$where_sql." LIMIT 1");
		
		return $ret;
	}
	
	/**
	 * 通过一定条件获取数据库列表信息
	 *
	 * @param array $where
	 * @param int $start
	 * @param int $pagesize
	 * @param string $orderby
	 */
	public function getRowList($where, $start=0, $pagesize=20, $orderby='',$field='') {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
	
		$where = safe_db_data($where);
		$start = intval($start);
		$pagesize = intval($pagesize);
	
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
	
		$db_op = DbOp::getInstance($db_cfg);
	
		$where_sql = parse_where($where);
		
// 		dump("select * from ".$db_cfg['tbl'].$where_sql." ".$orderby." LIMIT {$start}, {$pagesize}");
        if(!$field){
            $field = '*';
        }
		$ret = $db_op->queryList("select {$field} from ".$db_cfg['tbl'].$where_sql." ".$orderby." LIMIT {$start}, {$pagesize}");

		return $ret;
	}
	
	/**
	 * 通过一定条件得到数据库列表总数
	 * @param array $where
	 * @return int
	 */
	public function getRowTotal($where) {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
		
		$where = safe_db_data($where);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$where_sql = parse_where($where);
		
		$ret = $db_op->queryCount("select count(*) as c from ".$db_cfg['tbl'].$where_sql);
// 		dump("select count(*) as c from ".$db_cfg['tbl'].$where_sql);
		return $ret;
	}
	
	/**
	 * 创建一行数据库信息
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function createRow($data) {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
	
		$data = safe_db_data($data);
	
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
	
		$db_op = DbOp::getInstance($db_cfg);
	
		// parse_data用来把要插入的数据解析成 “字段='xxx', 字段2='xxx', ...”的格式
		// 还有一个函数是parse_where，请直接查看源代码
		$set_values = parse_data($data);
// 		dump("insert into ".$db_cfg['tbl']." set ".$set_values);
		C('debug_sql', "insert into ".$db_cfg['tbl']." set ".$set_values);
		$ret = $db_op->execute("insert into ".$db_cfg['tbl']." set ".$set_values);
	
		return $ret;
	}
	
	
	/**
	 * 批量插入N行数据到数据库表
	 * 
	 * @param array $data_list
	 * @return boolean
	 */
	public function createRows($data_list) {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
	
		$data_list = safe_db_data($data_list);
	
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
	
		$db_op = DbOp::getInstance($db_cfg);
	
		$insert_sql = "insert into ".$db_cfg['tbl'].parse_insert_data_list($data_list);
		
		$ret = $db_op->execute($insert_sql);
	
		return $ret;
	}
	
	/**
	 * 更新一行数据库信息
	 * 
	 * @param string $pkid, 主键值
	 * @param array $data
	 */
	public function updateRow($pkid, $data) {
		
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
		
		$pkid = safe_db_data($pkid);
		$data = safe_db_data($data);
	
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
	
		$db_op = DbOp::getInstance($db_cfg);
	
		$set_values = parse_data($data);
// 		dump("update ".$db_cfg['tbl']." set ".$set_values." where ".$this->pkid_name."='".$pkid."'");
		$ret = $db_op->execute("update ".$db_cfg['tbl']." set ".$set_values." where ".$this->pkid_name."='".$pkid."' LIMIT 1");
	
		return $ret;
	}
	


	/**
	 * 根据一定条件更新数据库信息
	 *
	 * @param array $where
	 * @param array $data
	 */
	public function updateRowWhere($where, $data) {
	
		if (empty($this->cfg_name) || empty($this->tbl_alias) || empty($this->pkid_name)) return false;
		
		$where = safe_db_data($where);
		$data = safe_db_data($data);
	
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
	
		$db_op = DbOp::getInstance($db_cfg);

		$where_sql = parse_where($where);
		$set_values = parse_data($data);
		
// 		dump("update ".$db_cfg['tbl']." set ".$set_values.$where_sql);
		$ret = $db_op->execute("update ".$db_cfg['tbl']." set ".$set_values.$where_sql);
	
		return $ret;
	}
}