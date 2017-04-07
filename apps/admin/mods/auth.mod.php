<?php
class AuthMod extends BaseMod {

	/**
	 * 通过auth_id获得一条auth信息
	 * @param int $auth_id
	 */
	public function getAuth($auth_id) {

		$auth_data = Factory::getData('auth');
		
		$auth = $auth_data->getAuth($auth_id);
		
		return $auth;
	}

	/**
	 * 通过一定条件获取auth列表
	 *
	 * @param array $where
	 * @param int $start
	 * @param int $pagesize
	 * @param string $orderby
	 */
	public function getAuthList($where, $start=0, $pagesize=20, $orderby='') {

		$auth_data = Factory::getData('auth');
		
		$auth_list = $auth_data->getAuthList($where, $start, $pagesize, $orderby);
		
		return $auth_list;
	}

	/**
	 * 创建一个auth
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function createAuth($data) {

		$auth_data = Factory::getData('auth');
		
		$ret = $auth_data->createAuth($data);
		
		return $ret;
	}

	/**
	 * 更新一个auth
	 *
	 * @param int $auth_id
	 * @param array $data
	 */
	public function updateAuth($auth_id, $data) {

		$auth_data = Factory::getData('auth');
		
		$ret = $auth_data->updateAuth($auth_id, $data);
		
		return $ret;
	}
}
