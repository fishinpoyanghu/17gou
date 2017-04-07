<?php
/**
 * Created on 2015-11-21
 *
 * 共用数据库访问类
 */

class PubMod extends BaseMod {
	
	/**
	 * 初始化 cfg_name和tbl_alias
	 * 
	 * @param string $cfg_name
	 * @param string $tbl_alias
	 * @param string $pkid_name, 主键名称
	 */
	public function init($cfg_name, $tbl_alias, $pkid_name) {
		
		$pub_data = Factory::getData('pub');
		
		$pub_data->init($cfg_name, $tbl_alias, $pkid_name);
	}
	
	/**
	 * 通过主键ID获得数据库表一行信息
	 * 
	 * @param string $pkid, 主键值
	 */
	public function getRow($pkid) {
		
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->getRow($pkid);
	
		return $ret;
	}
	
	/**
	 * 通过一定条件获得数据库表一行信息
	 * 
	 * @param array $where
	 */
	public function getRowWhere($where) {
		
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->getRowWhere($where);
		
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
	
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->getRowList($where, $start, $pagesize, $orderby,$field);
		
		return $ret;
	}
	
	/**
	 * 通过一定条件得到数据库列表总数
	 * @param array $where
	 * @return int
	 */
	public function getRowTotal($where) {
		
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->getRowTotal($where);
		
		return $ret;
	}
	
	/**
	 * 创建一行数据库信息
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function createRow($data) {
	
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->createRow($data);
	
		return $ret;
	}
	
	
	/**
	 * 批量插入N行数据到数据库表
	 * 
	 * @param array $data_list
	 * @return boolean
	 */
	public function createRows($data_list) {
	
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->createRows($data_list);
	
		return $ret;
	}
	
	/**
	 * 更新一行数据库信息
	 *
	 * @param string $pkid, 主键值
	 * @param array $data
	 */
	public function updateRow($pkid, $data) {
	
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->updateRow($pkid, $data);
	
		return $ret;
	}
	
	/**
	 * 根据一定条件更新数据库信息
	 *
	 * @param array $where
	 * @param array $data
	 */
	public function updateRowWhere($where, $data) {
	
		$pub_data = Factory::getData('pub');
		
		$ret = $pub_data->updateRowWhere($where, $data);
	
		return $ret;
	}
}