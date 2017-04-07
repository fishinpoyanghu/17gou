<?php
class TableData extends BaseData {

	private $cfg_name='';
	private $tbl_alias='';
	private $pkid_name=0;
	
	/**
	 * 初始化cfg_name和tbl_alias
	 * @param string $cfg_name
	 * @param string $tbl_alias
	 * @param int $pkid_name，表主键字段的名字
	 */
	public function init($cfg_name, $tbl_alias, $pkid_name) {
		
		$this->cfg_name = $cfg_name;
		$this->tbl_alias = $tbl_alias;
		$this->pkid_name = $pkid_name;
	}
	
	/**
	 * 通过pkid获得table一行记录
	 * @param int $pkid
	 */
	public function getTableRow($pkid) {
		
		$pkid = safe_db_data($pkid);
		
		// 第3个参数是用来分库分表的，不需要传空就可以
		// 第4个参数是用来标记是读操作，还是写操作的 r/w
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$ret = $db_op->queryRow("select * from ".$db_cfg['tbl']." where {$this->pkid_name}='".$pkid."'");
		
		return $ret;
	}
	
	/**
	 * 通过一定条件获取table的信息列表
	 * 
	 * @param array $where
	 * @param string $orderby
	 * @param int $start
	 * @param int $pagesize
	 */
	public function getTableList($where, $orderby='', $start=0, $pagesize=20) {
		
		$where = safe_db_data($where);
		$orderby = safe_db_data($orderby);
		$start = intval($start);
		$pagesize = intval($pagesize);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$where_sql = parse_where($where);
//         dump("select * from ".$db_cfg['tbl'].$where_sql." ".$orderby." LIMIT {$start}, {$pagesize}");
		$ret = $db_op->queryList("select * from ".$db_cfg['tbl'].$where_sql." ".$orderby." LIMIT {$start}, {$pagesize}");
		
		return $ret;
	}
	
	/**
	 * 通过一定条件获取table的列表的总数量
	 *
	 * @param array $where
	 */
	public function getTableCount($where) {
	
		$where = safe_db_data($where);
			
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
	
		$db_op = DbOp::getInstance($db_cfg);
	
		$where_sql = parse_where($where);
	
		$ret = $db_op->queryCount("select count(*) as c from ".$db_cfg['tbl'].$where_sql);
	
		return $ret;
	}
	
	/**
	 * 创建一个管理用户
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function createTableRow($data) {
		
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
	 * 通过一定条件更新Table信息
	 * 
	 * @param array $data
	 * @param array $where
	 */
	public function updateTable($data, $where) {
		
		$data = safe_db_data($data);
		$where = safe_db_data($where);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$set_values = parse_data($data);
		$where_sql = parse_where($where);
		
		$ret = $db_op->execute("update ".$db_cfg['tbl']." set ".$set_values.$where_sql);
		
		return $ret;
	}
}