<?php
/**
 * 表管理 ctrl
 */

class DepartmentCtrl extends BaseCtrl {
	
	public function edit() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_id = gint('dep_id');
		if (!$dep_id) {
			die('dep_id error!');
		}
		
		$dep_mod = Factory::getMod('department');
		$dep = $dep_mod->getDepartment($dep_id);
		
		if ($dep['company_id'] != $company_id) {
			die('非法操作，没有权限编辑部门');
		}
		
		$data = array(
				'dep_id' => $dep_id,
				'dep' => $dep,
		);
		
		Factory::getView("staff/department_edit", $data);
	}
	
	public function ajaxEditSave() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_id = gint('dep_id');
		if (!$dep_id) {
			echo_result(1, 'dep_id error!');
		}
		
		$dep_mod = Factory::getMod('department');
		$dep = $dep_mod->getDepartment($dep_id);
		
		if ($dep['company_id'] != $company_id) {
			echo_result(1, '没有权限编辑部门');
		}
		
		$dep_name = gstr('dep_name');
		if ((strlen_utf8($dep_name, 1) < 1) || (strlen_utf8($dep_name, 1) > 10)) {
			echo_result(1, '部门名字长度范围只允许1-10个字');
		}
		$dep_name = htmlspecialchars($dep_name);
				
		$data = array(
				'name' => $dep_name,
				'ut' => time(),				
		);
		
		$ret = $dep_mod->updateDepartment($dep_id, $data);
		if (!$ret) {
			echo_result(1, '数据库错误，请稍后重试');
		}
		
		echo_result(0, 'succ', $data);
	}
	
	public function add() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_pid = gint('dep_pid');
		if (!$dep_pid) {
			die('dep_pid error!');
		}
		
		$dep_mod = Factory::getMod('department');
		$dep = $dep_mod->getDepartment($dep_pid);
		
		if ($dep['company_id'] != $company_id) {
			die('非法操作，没有权限创建部门');
		}
		
		$data = array(
				'dep_pid' => $dep_pid,
				'dep' => $dep,
		);
		
		Factory::getView("staff/department_add", $data);
	}
	
	public function ajaxAddSave() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_pid = gint('dep_pid');
		if (!$dep_pid) {
			echo_result(1, 'dep_pid error!');
		}
		
		$dep_mod = Factory::getMod('department');
		$dep = $dep_mod->getDepartment($dep_pid);
		
		if ($dep['company_id'] != $company_id) {
			echo_result(1, '没有权限创建部门');
		}
		
		$dep_name = gstr('dep_name');
		if ((strlen_utf8($dep_name, 1) < 1) || (strlen_utf8($dep_name, 1) > 10)) {
			echo_result(1, '部门名字长度范围只允许1-10个字');
		}
		$dep_name = htmlspecialchars($dep_name);
				
		$data = array(
				'department_id' => get_auto_id(C('AUTOID_DEPARTMENT')),
				'pid' => $dep_pid,
				'company_id' => $dep['company_id'],
				'name' => $dep_name,
				'rt' => time(),
				'ut' => time(),				
		);
		
		$ret = $dep_mod->createDepartment($data);
		if (!$ret) {
			echo_result(1, '数据库错误，请稍后重试');
		}
		
		echo_result(0, 'succ', $data);
	}
	
	public function ajaxDelDepartment() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_id = gint('dep_id');
		if (!$dep_id) {
			echo_result(1, 'dep_id invalid');
		}
		
		$dep_mod = Factory::getMod('department');
		$dep = $dep_mod->getDepartment($dep_id);
		
		if (!$dep) {
			echo_result(1, '要删除的部门不存在，请刷新页面');
		}
		
		if ($dep['company_id'] != $company_id) {
			echo_result(1, '非法操作，没有权限删除这个部门');
		}
		
		// 顶级部门不能删除
		if ($dep['pid'] == 0) {
			echo_result(1, '顶级部门不能删除');
		}
		
		// 标记删除
		$update_data = array(
				'stat' => 1,
				'ut' => time(),
		);
		
		$dep_mod->updateDepartment($dep_id, $update_data);
		
		// 更新admin表，把$dep_id部门下的所有员工department_id改成$dep['pid']
		$tbl = 'admin';
		$t_mod = Factory::getMod('table');
		
		$t_mod->tryLoadTable($tbl);
		
		$update_data = array(
				'department_id' => $dep['pid'],
				'ut' => time(),
		);
		$where = array(
				'department_id' => $dep_id,
		);
		$t_mod->updateTable($update_data, $where);
		
		echo_result(0, 'succ');
	}
	
	public function ajaxGetStaffList() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_id = gint('dep_id');
		if (!$dep_id) {
			echo_result(1, 'fail');
		}
		$page = gint('page');
		if ($page < 1) $page = 1;
		
		$kw = gstr('kw');
		
		$dep_mod = Factory::getMod('department');
		
		$where = array(
				'company_id' => $company_id,
				'stat' => 0,
		);
		$orderby = ' ORDER BY pid DESC, department_id DESC';
		
		$dep_list = $dep_mod->getDepartmentList($where, 0, 2048, $orderby);
		
		$dep = $dep_mod->getDepartment($dep_id);
		$dep_ids = array($dep_id);
		
		$this->findSubNodeIds($dep_list, $dep_ids, $dep_id);
		
		$tbl = 'admin';
		$t_mod = Factory::getMod('table');
		
		$t_mod->tryLoadTable($tbl);
		
		$base_cfg = $t_mod->getBaseCfg();
		$tbl_head = $t_mod->getListCfg();
		if (is_array($tbl_head['_ops'])) $tbl_head['_ops']['_w'] = '80px';
		
		$where = array(
				'company_id' => $company_id,
				'department_id' => array($dep_ids, 'in'),
				'stat' => 0,
		);
		// 如果appid_required是true
		if ($base_cfg['appid_required']) {
			$where['appid'] = $login_user['appid'];
		}
		// 如果输入了kw
		if ($kw && isset($base_cfg['search']['searchSql'])) {
			$where[$base_cfg['search']['searchKey']] = array(str_replace('{$kw}', $kw, $base_cfg['search']['searchSql']), 'like');
		}
		
		$orderby = ' ORDER BY admin_id DESC';
		
		$tbl_list = $t_mod->getTableList($where, $orderby, ($page-1)*$base_cfg['pagesize'], $base_cfg['pagesize']);
		
		$tbl_count = $t_mod->getTableCount($where);
		
		$data = array(
				'tbl_data' => $tbl_list,
				'tbl_head' => $tbl_head,
				'tbl_count'=> $tbl_count,
				'page'     => $page,
				'pagesize' => $base_cfg['pagesize'],
		);
		
		echo_result(0, 'succ', $data);
	}

	public function index() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 这里要通过一定方式获取company_id
		// 先写死
		$company_id = 1;
		
		$dep_mod = Factory::getMod('department');
		
		$where = array(
				'company_id' => $company_id,
				'stat' => 0,
		);
		$orderby = ' ORDER BY pid DESC, department_id DESC';
		
		$dep_list = $dep_mod->getDepartmentList($where, 0, 2048, $orderby);
		
		$company = $dep_mod->getDepartment($company_id);
		$company['level'] = 1;
		$company['expand'] = 1;
		$company['path'] = array();
				
		$node_list = array(
				$company_id => $company,
		);
		$this->findSubTree($dep_list, $node_list, $company, 1);
		
		// Table相关的一些配置处理
		$tbl = 'admin';
		$t_mod = Factory::getMod('table');
		
		$t_mod->tryLoadTable($tbl);
		
		$tbl_head = $t_mod->getListCfg();
		if (is_array($tbl_head['_ops'])) $tbl_head['_ops']['_w'] = '80px';
		$base_cfg = $t_mod->getBaseCfg();
		$add_cfg = $t_mod->getAddCfg();
		$edit_cfg = $t_mod->getEditCfg();
		$del_cfg = $t_mod->getDelCfg();
		$classify_cfg = $t_mod->getClassifyCfg();
		$reset_cfg = $t_mod->getResetCfg();
				
		$data = array(
				'login_user' => $login_user,
				'node_list' => $node_list,
				'company' => $company,
				'tbl'        => $tbl,
				'global_cfg' => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
				'base_cfg'   => $base_cfg,
				'tbl_head'   => $tbl_head,
				'add_cfg'    => $add_cfg,
				'edit_cfg'   => $edit_cfg,
				'del_cfg'    => $del_cfg,
				'classify_cfg' => $classify_cfg,
				'reset_cfg'  => $reset_cfg,
		);
		
		Factory::getView("staff/department", $data);
	}
	
	/**
	 * 找到一个节点的所有子节点
	 * 
	 * @param array $dep_list
	 * @param array $dep_ids
	 * @param int $pid
	 */
	private function findSubNodeIds(&$dep_list, &$dep_ids, $pid) {
		
		foreach ($dep_list as $k=>$dep) {
			if ($dep['pid'] == $pid) {
				$dep_ids[] = $dep['department_id'];
				unset($dep_list[$k]);
				
				$this->findSubNodeIds($dep_list, $dep_ids, $dep['department_id']);
			}
		}
	}
	
	/**
	 * 找到一个节点的所有子节点树
	 * 
	 * @param array $dep_list
	 * @param array $node_list
	 * @param array $parent
	 * @param int $level
	 */
	private function findSubTree(&$dep_list, &$node_list, $parent, $level) {
		
		$pid = $parent['department_id'];
		
		foreach ($dep_list as $k=>$dep) {
			
			if ($dep['pid'] == $pid) {
				$dep['level'] = $level+1;
				$dep['expand'] = ($dep['level'] > 1) ? 0 : 1;
				$dep['path'] = $parent['path'];
				$dep['path'][] = $pid;
				
				foreach ($dep['path'] as $m) {
					$node_list[$m]['count'] += $dep['count'];
				}
				
				$node_list[$dep['department_id']] = $dep;
				unset($dep_list[$k]);
				
				$this->findSubTree($dep_list, $node_list, $dep, $dep['level']);
			}
		}
	}
}
