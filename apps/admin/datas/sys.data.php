<?php
class SysData extends BaseData {

	private $cfg_name='main';
	private $tbl_alias='sys'; 
	 

	/**
	 * 分佣设置信息
	 * @return mixed
	 */
	public function sysset(){
		$this->tbl_alias = 'sysset';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		return $db_op->queryList("select * from {$db_cfg['tbl']} limit 1");
	}

	/**
	 * 保存分佣设置
	 * @param $data
	 * @return int
	 */
	public function savesysset($data){
		$this->tbl_alias = 'sysset';
		$data = safe_db_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg); 
 

		$set_values = parse_data($data);
		$sql = "update {$db_cfg['tbl']} set {$set_values}  "; 
		$res = $db_op->execute($sql);  
		return $res;
	 
	}

	 


}