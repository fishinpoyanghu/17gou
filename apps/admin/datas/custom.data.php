<?php
class CustomData extends BaseData {

	private $cfg_name='shop';
	private $tbl_alias='';

	/**
	 * 获取所有商品列表
	 * @return mixed
	 */
	public function allGoodsList(){
		$this->tbl_alias = 'goods';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select * from {$db_cfg['tbl']} where `stat` = 0 order by `rt` desc";
		return $db_op->queryList($sql);
	}

	/**
	 * 获取耍刷单设置信息
	 * @return mixed
	 */
	public function getInfo(){
		$this->tbl_alias = 'shua';
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$sql = "select * from {$db_cfg['tbl']} where `id` = 1";
		return $db_op->queryRow($sql);
	}

	/**
	 * 保存刷单设置
	 * @param $data
	 * @return int
	 */
	public function save($data){
		$this->tbl_alias = 'shua';
		$data = safe_db_data($data);
		$data = parse_data($data);
		$db_cfg = load_db_cfg($this->cfg_name, $this->tbl_alias, '', 'r');
		$db_op = DbOp::getInstance($db_cfg);
		$res = $db_op->execute("update {$db_cfg['tbl']} set {$data} where `id` = 1");
		return $res === false ? 0 : 1;
	}

}