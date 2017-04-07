<?php
/**
 * @author wangyihuang
 */

class DepartmentMod extends BaseMod {
	
	/**
	 * 通过department_id获得一条department信息
	 * @param int $department_id
	 */
	public function getDepartment($department_id) {
		
		$dep_data = Factory::getData('department');
		
		$dep = $dep_data->getDepartment($department_id);
		
		return $dep;
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
		
		$dep_data = Factory::getData('department');
		
		$ret = $dep_data->getDepartmentList($where, $start, $pagesize, $orderby);
		
		return $ret;
	}
	
	/**
	 * 创建一个department
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function createDepartment($data) {
		
		$dep_data = Factory::getData('department');
		
		$ret = $dep_data->createDepartment($data);
		
		return $ret;
	}
	
	/**
	 * 更新一个department
	 * 
	 * @param int $department_id
	 * @param array $data
	 */
	public function updateDepartment($department_id, $data) {
		
		$dep_data = Factory::getData('department');
		
		$ret = $dep_data->updateDepartment($department_id, $data);
		
		return $ret;
	}
}
