<?php
/**
 * 表管理 ctrl
 */

class StaffCtrl extends BaseCtrl {
	
	public function edit() {
	
		$login_user = app_get_login_user(1, 1);
	
		$admin_id = gint('admin_id');
		if (!$admin_id) {
			die('admin_id缺失');
		}
	
		$tbl = 'admin';
		$form_mod = Factory::getMod('form');
		$tree_mod = Factory::getMod('tree');
		$t_mod = Factory::getMod('table');
	
		$t_mod->tryLoadTable($tbl);
	
		$edit_cfg = $t_mod->getEditCfg();
		
		$admin = $t_mod->getTableRow($admin_id);
		
		if (!$admin) {
			die($admin_id.'对应的记录不存在');
		}
		
		if ($admin['company_id'] != C('company_id')) {
			die('只能编辑当前APP下的员工');
		}
		
		$edit_cfg['inputGroups'] = $form_mod->setInputGroupsValue($edit_cfg['inputGroups'], $admin);
		
		$data = array(
				'form_id'  => 'staffedit',
				'action_url' => '/?c=staff&a=edit_save&admin_id='.$admin_id,
				'form_okbtn' => $edit_cfg['submitBtn'],
				'ipt_list' => $edit_cfg['inputGroups'],
		);
	
		Factory::getView("staff/staff_form", $data);
	}
	
	public function editSave() {
	
		$login_user = app_get_login_user(1, 1);
	
		$admin_id = gint('admin_id');
		if (!$admin_id) {
			echo_result(1, 'admin_id缺失');
		}
	
		$tbl = 'admin';
		$tree_mod = Factory::getMod('tree');
		$t_mod = Factory::getMod('table');
	
		$t_mod->tryLoadTable($tbl);
		
		$base_cfg = $t_mod->getBaseCfg();
		$edit_cfg = $t_mod->getEditCfg();
	
		$admin = $t_mod->getTableRow($admin_id);
	
		if (!$admin) {
			echo_result(1, $admin_id.'对应的记录不存在');
		}
	
		if ($admin['company_id'] != C('company_id')) {
			echo_result(1, '只能编辑当前APP下的员工');
		}
		
		$default_set = isset($edit_cfg['defaultSet']) ? $edit_cfg['defaultSet'] : array();
		$ipt_group = $edit_cfg['inputGroups'];
		
		$data = $this->getData($ipt_group, $default_set, $t_mod);
		
		$where = array(
				'admin_id' => $admin_id,
				'appid' => $login_user['appid'],
		);
		
		$ret = $t_mod->updateTable($data, $where);
		
		if ($ret) {
			echo_result(0, '编辑成功', array('department_id'=>$data['department_id']));
		}
	
		echo_result(1, '数据库错误');
	}
	
	public function add() {
		
		$login_user = app_get_login_user(1, 1);
		
		$dep_id = gint('dep_id');
		
		$tbl = 'admin';
		$tree_mod = Factory::getMod('tree');
		$t_mod = Factory::getMod('table');
		
		$t_mod->tryLoadTable($tbl);
		
		$add_cfg = $t_mod->getAddCfg();
		$add_cfg['inputGroups']['department_id']['value'] = $dep_id;
		
		$data = array(
				'form_id'  => 'staffadd',
				'action_url' => '/?c=staff&a=add_save',
				'form_okbtn' => $add_cfg['submitBtn'],
				'ipt_list' => $add_cfg['inputGroups'],
		);
		
		Factory::getView("staff/staff_form", $data);
	}
	
	public function addSave() {
		
		$login_user = app_get_login_user(2, 2);
		
		$tbl = 'admin';
		$t_mod = Factory::getMod('table');
		
		$t_mod->tryLoadTable($tbl);
		
		$base_cfg = $t_mod->getBaseCfg();
		$add_cfg = $t_mod->getAddCfg();
		
		$default_set = isset($add_cfg['defaultSet']) ? $add_cfg['defaultSet'] : array();
		$ipt_group = $add_cfg['inputGroups'];
		
		// ...
		$data = $this->getData($ipt_group, $default_set, $t_mod);
		
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			$data['appid'] = $login_user['appid'];
		}
		
		// 特殊：添加company_id
		$data['company_id'] = C('company_id');
		
		// 这里，添加到数据库
		if (!isset($data[$base_cfg['pkid']])) {
			$data[$base_cfg['pkid']] = get_auto_id($base_cfg['autoidCode']);
		}
		
		$ret = $t_mod->createTableRow($data);
		
		if ($ret) {
			echo_result(0, '创建成功', $data);
		}
		
		echo_result(1, '数据库错误');
	}
	
	private function getData($ipt_group, $default_set, &$t_mod) {
		
		$data = array();
		foreach ($ipt_group as $key=>$v) {
				
			// 忽略展示字段
			if ($v['type'] == 'show') continue;
				
			$p = pstr($key);
		
			// 如果是select/radio/checkbox，检查值是否在options的value里
			if (in_array($v['type'], array('select', 'radio', 'checkbox'))) {
				$ov_list = array();
				foreach ($v['options'] as $option) {
					$ov_list[] = $option['value'];
				}
		
				if (!in_array($p, $ov_list)) {
					echo_result(1, $v['name'].'值非法');
				}
			}
			// 开始做合法性检查
			if (is_array($v['validate'])) {
		
				foreach ($v['validate'] as $validate) {
					$validate = $t_mod->_parseSyntex($validate);
		
					// 是否是唯一性检查函数
					if ($validate['base'] == 'tpc_do_unique') {
						$where = array(
								$key => $p,
						);
						$_tmp = $t_mod->getTableList($where, '', 0, 1);
						$ret = $_tmp ? false : true;
					}
					else {
						$ret = $t_mod->_runFunc($validate['base'], $validate['params'], $p);
					}
		
					if (!$ret) {
		
						echo_result(1, $validate['text']);
					}
				}
			}
		
			// 如果type是password，做加密处理
			if ($v['type'] == 'password') {
				$p = md5($p);
			}
		
			// 到这里，表示检验合格啦
			$data[$key] = $p; // htmlspecialchars($p, ENT_QUOTES); 保存时，尽量保持原文，避免一些字段是存储代码的情况
		}
		
		// 添加默认字段
		foreach ($default_set as $key=>$set) {
			$set_ary = explode('|', $set);
		
			$s_func = $set_ary[0];
			$s_params = array();
			if (isset($set_ary[1])) {
				$s_params = explode(',', $set_ary[1]);
			}
		
			$data[$key] = call_user_func_array($s_func, $s_params);
		}
		
		return $data;
	}
}
