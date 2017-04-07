<?php

class FormMod extends BaseMod {
	
	/**
	 * 根据一定条件，得到客户端提交过来的表单数据
	 * 
	 * @param array $ipt_group
	 * @param array $default_set
	 * @param object $t_mod
	 * @return array data
	 */
	public function getData($ipt_group, $default_set, &$t_mod) {
	
		$data = array();
		foreach ($ipt_group as $key=>$v) {
	
			// 忽略展示字段
			if ($v['type'] == 'show') continue;
	
			$p = pstr($key);
	
			// 如果是select/radio/checkbox，检查值是否在options的value里
			if (in_array($v['type'], array('select', 'radio'))) {
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
			
			if ((count($set_ary) > 1) || $set_ary[0] == 'time') {
				$s_func = $set_ary[0];
				$s_params = array();
				if (isset($set_ary[1])) {
					$s_params = explode(',', $set_ary[1]);
				}
		
				$data[$key] = call_user_func_array($s_func, $s_params);
			}
			else {
				$data[$key] = $set;
			}
		}
	
		return $data;
	}
	
	/**
	 * 初始化 add或者edit的inputGroups
	 * @param array $ipt_groups
	 * @param boolean $appid_required
	 */
	public function initInputGroups($ipt_groups, $appid_required=true) {
		
		$pub_mod = Factory::getMod('pub');
		$tree_mod = Factory::getMod('tree');
		
		foreach ($ipt_groups as $k=>$group) {
			
			if (!isset($group['value'])) $ipt_groups[$k]['value'] = '';
			
			// 初始化 select
			if ($group['type'] == 'select') {
				
				if (!(isset($group['options']) && is_array($group['options']))) $group['options'] = array();
				if (isset($group['options_source']) && is_array($group['options_source'])) {
					
					if (!isset($group['options_source']['where'])) $group['options_source']['where'] = array();
					if ($appid_required) $group['options_source']['where']['appid'] = C('appid');
					
					$option_list = $pub_mod->getRowListByCfg($group['options_source']);
					$node_list = $tree_mod->getTree($option_list, 0, $group['options_source']['pkid'], $group['options_source']['parentId']);
					
					$group['options'] = array_merge($group['options'], $tree_mod->tree2Options($node_list, $group['options_source']['pkid'], $group['options_source']['nodeName']));
				}
				
				$ipt_groups[$k] = $group;
			}
		}
		
		return $ipt_groups;
	}
	
	/**
	 * 根据inputGroups和数据库里选出来的一行row，设置inputGroups每个元素的value
	 * 
	 * @param array $ipt_groups
	 * @param array $row
	 * @return array $ipt_groups
	 */
	public function setInputGroupsValue($ipt_groups, $row) {
		
		foreach ($ipt_groups as $k=>$group) {
			
			if (isset($row[$k])) {
				$ipt_groups[$k]['value'] = $row[$k];
			}
		}
		
		return $ipt_groups;
	}
}