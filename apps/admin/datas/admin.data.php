<?php
class AdminData extends BaseData {

	private $cfg_name='admin';
	private $tbl_alias='admin';
	
	/**
	 * 通过uid获得一条管理用户信息
	 * @param int $admin_id
	 */
	public function getAdmin($admin_id) {
		
		// 在data层，每个传进来的参数，都必须调用safe_db_data参数
		$admin_id = safe_db_data($admin_id);
		
		// 第3个参数是用来分库分表的，不需要传空就可以
		// 第4个参数是用来标记是读操作，还是写操作的 r/w
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$ret = $db_op->queryRow("select * from ".$db_cfg['tbl']." where admin_id='".$admin_id."'");
		
		return $ret;
	}
	
	/**
	 * 通过一定条件获取管理员列表
	 * 
	 * @param array $where
	 * @param int $start
	 * @param int $pagesize
	 */
	public function getAdminList($where, $start=0, $pagesize=20) {
		
		$where = safe_db_data($where);
		$start = intval($start);
		$pagesize = intval($pagesize);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$where_sql = parse_where($where);
		
		$ret = $db_op->queryList("select * from ".$db_cfg['tbl'].$where_sql." LIMIT {$start}, {$pagesize}");
		
		return $ret;
	}
	
	/**
	 * 创建一个管理用户
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function createAdmin($data) {
		
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
	 * 更新一个管理用户
	 * 
	 * @param int $admin_id
	 * @param array $data
	 */
	public function updateAdmin($admin_id, $data) {
		
		$admin_id = safe_db_data($admin_id);
		$data = safe_db_data($data);
		
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		
		$db_op = DbOp::getInstance($db_cfg);
		
		$set_values = parse_data($data);
		
		$ret = $db_op->execute("update ".$db_cfg['tbl']." set ".$set_values." where admin_id='".$admin_id."'");
		
		return $ret;
	}

	/**
	 * 是否是超级管理员
	 * @param $uid
	 * @return mixed
	 */
	public function isSuperAdmin($uid){
		$uid = safe_db_data($uid);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryRow("select `type` from {$db_cfg['tbl']} where `admin_id` = {$uid}");
	}

	/**
	 * 管理员列表
	 * @param $page
	 * @param $num
	 * @return array
	 */
	public function adminList($page,$num){
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$limit =  "limit ".($page-1)*$num.",$num";
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select * from {$db_cfg['tbl']} {$limit} ";
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']}");
		if(!$count['c']) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
		);
	}

	/**
	 * 添加管理员
	 * @param $data
	 * @return mixed
	 */
	public function addAdmin($data){
		$data = safe_db_data($data);
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->execute("insert into {$db_cfg['tbl']} set {$data}");
	}

	/**
	 * 修改管理员账户信息
	 * @param $data
	 * @return mixed
	 */
	public function modifyAdmin($data){
		$data = safe_db_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'w');
		$db_op = DbOp::getInstance($db_cfg);
		if($data['type']){
			$sql = "update {$db_cfg['tbl']} set `type` = abs(`type` - 1) where `admin_id` = {$data['id']}";
		}else{
			$sql = "update {$db_cfg['tbl']} set `stat` = abs(`stat` - 1) where `admin_id` = {$data['id']}";
		}
		return $db_op->execute($sql);
	}

	 
	/**
	 * 用户列表
	 * @param int $page
	 * @param string $num
	 * @param array $searchArr
	 * @return array
	 */
	public function userList($page=1,$num='',$searchArr=''){
		$this->tbl_alias = 'user';
		$page = safe_db_data($page);
		$num = safe_db_data($num);
		$keyword = safe_db_data($searchArr['keyword']);
		$limit = $num ? "limit ".($page-1)*$num.",$num" : '';
		$db_cfg = load_db_cfg('shop', $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);

 		if($keyword){
			$and .= " and a.`nick` like '%{$keyword}%'";
		}
		 
 	 	$and .= $searchArr['type']?" and a.`type`={$searchArr['type']}":''; 
	 	 
		$sql = "select a.*,b.`wx_openid` from {$db_cfg['tbl']} as a left join ".DATABASE.".`t_wxuser` as b on b.uid=a.uid where 1 {$and} order by a.`rt` desc {$limit}";
	  
		$count = $db_op->queryRow("select count(*) as c from {$db_cfg['tbl']} as a  where 1 {$and}");
 		$commission_uid = $db_op->queryRow("select commission_uid from ".DATABASE.".`t_sysset` ");
		 //var_dump($commission_uid['commission_uid']);exit;
		$commission_user = $db_op->queryList("select uid,nick from {$db_cfg['tbl']}    WHERE uid IN ({$commission_uid['commission_uid']}) ");

		if($count['c']==0) return array();
		return array(
			'list' => $db_op->queryList($sql),
			'total' => $count['c'],
			'keyword' => $keyword,
			'commission_uid'=>$commission_uid['commission_uid'],
			'commission_user'=>$commission_user
		);
	}
}