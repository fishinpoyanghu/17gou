<?php
/**
 * @author wangyihuang
 */

class TableMod extends BaseMod {
	
	private $load_succ = false;
	private $cfg;
	private $tbl;
	
	/**
	 * 关闭表
	 */
	public function closeTable() {
		$this->load_succ = false;
		$this->cfg = false;
		$this->tbl = '';
	}
	
	/**
	 * 尝试加载表，如果加载失败，返回false，否则返回true
	 * 
	 * @param string $tbl
	 */
	public function tryLoadTable($tbl) {
		
		$file = get_app_root().'/plugins/table/'.$tbl.'/tbl.cfg.php';
		
		if (!is_file($file)) return false;
		
		$this->load_succ = true;
		$this->tbl = $tbl;
		$this->cfg = include $file;
		
		// 加载table的共用函数封装
		$file = get_app_root().'/plugins/table/common.func.php';
		include_once $file;
		
		// 如果定义了handle.func.php，加载
		$file = get_app_root().'/plugins/table/'.$tbl.'/handle.func.php';
		if (is_file($file)) include_once $file;
		
		// 格式化配置文件
		$this->formatCfg();
		
		// 解析Table定义的全局变量
		$this->cfg = $this->parseVariables($this->cfg);
		
		$table_data = Factory::getData('table');
		$table_data->init($this->cfg['base']['db_cfg_name'], $this->cfg['base']['db_tbl_alias'], $this->cfg['base']['pkid']);
		
		return true;
	}
	
	/**
	 * 解析一些Table配置文件的一些全局变量
	 */
	private function parseVariables($cfg) {
		
		foreach ($cfg as $k=>$v) {
			
			if (is_array($v)) {
				$cfg[$k] = $this->parseVariables($v);
			}
			else {
				$cfg[$k] = tpf_globalvar($v, $this->tbl, C('company_id'), C('appid'));
			}
		}
		
		return $cfg;
	}
	
	/**
	 * 格式化配置文件
	 */
	private function formatCfg() {
		
		// base
		if (is_array($this->cfg['base'])) {
			// title
			$this->mkAryKey($this->cfg['base'], 'title', '未定义');
			
			// where
			$this->mkAryKey($this->cfg['base'], 'where', array());
			
			// orderby
			$this->mkAryKey($this->cfg['base'], 'orderby', '');
			
			// pagesize
			$this->mkAryKey($this->cfg['base'], 'pagesize', 10);
			
			// appid_required
			$this->mkAryKey($this->cfg['base'], 'appid_required', true);
			
			// search
			$this->mkAryKey($this->cfg['base'], 'search', array());
			
			// categoryList
			$this->mkAryKey($this->cfg['base'], 'categoryList', array());
			
			// concatList
			$this->mkAryKey($this->cfg['base'], 'concatList', array());
		}
		
		// list
		
		// add
		if (isset($this->cfg['add']) && is_array($this->cfg['add']['inputGroups'])) {
				
			foreach ($this->cfg['add']['inputGroups'] as $k=>$ipt) {
				// name
				$this->mkAryKey($ipt, 'name', '未定义');
		
				// type
				$this->mkAryKey($ipt, 'type', 'text');
		
				// placeholder
				$this->mkAryKey($ipt, 'placeholder', $ipt['name']);
				
				// tips
				$this->mkAryKey($ipt, 'tips', '');
		
				// require
				$this->mkAryKey($ipt, 'require', false);
		
				// validate
				$this->mkAryKey($ipt, 'validate', array());
				
				// attr
				$this->mkAryKey($ipt, 'attr', array());
		
				$this->cfg['add']['inputGroups'][$k] = $ipt;
			}
			
			// submitBtn
			$this->mkAryKey($this->cfg['add'], 'submitBtn', array());
			$this->mkAryKey($this->cfg['add']['submitBtn'], 'name', '提交');
		}
		
		// edit
		if (isset($this->cfg['edit']) && is_array($this->cfg['edit']['inputGroups'])) {
				
			foreach ($this->cfg['edit']['inputGroups'] as $k=>$ipt) {
				// name
				$this->mkAryKey($ipt, 'name', '未定义');
		
				// type
				$this->mkAryKey($ipt, 'type', 'text');
		
				if ($ipt['type'] == 'show') {
					// format
					$this->mkAryKey($ipt, 'format', '');
				}
				else {
					// placeholder
					$this->mkAryKey($ipt, 'placeholder', $ipt['name']);
						
					// require
					$this->mkAryKey($ipt, 'require', false);
						
					// validate
					$this->mkAryKey($ipt, 'validate', array());
				}
				
				// attr
				$this->mkAryKey($ipt, 'attr', array());
		
				$this->cfg['edit']['inputGroups'][$k] = $ipt;
			}
		}
		
		// delete
		
		// reset
	}
	
	/**
	 * 得到基础配置信息
	 * @return array:
	 */
	public function getBaseCfg() {
		$this->cfg['base']['where'] = isset($this->cfg['base']['where']) ? $this->cfg['base']['where'] : array();
		$this->cfg['base']['orderby'] = isset($this->cfg['base']['orderby']) ? $this->cfg['base']['orderby'] : '';
		
		return $this->cfg['base'];
	}
	
	public function getListCfg() {
		return $this->cfg['list'];
	}
	
	public function getUploadCfg() {
		return isset($this->cfg['upload']) ? $this->cfg['upload'] : false;
	}
	
	private function mkAryKey(&$ary, $new_key, $default_value) {
		
		if (!isset($ary[$new_key])) $ary[$new_key] = $default_value;
	}
	
	/**
	 * 得到add的config
	 * @param boolean $init, default:true, 是否做配置文件的初始化
	 * @return boolean
	 */
	public function getAddCfg($init=true) {
		
		$add_cfg = isset($this->cfg['add']) ? $this->cfg['add'] : false;
		
		if ($add_cfg && $init) {
			$form_mod = Factory::getMod('form');
			$base_cfg = $this->getBaseCfg();
			
			$add_cfg['inputGroups'] = $form_mod->initInputGroups($add_cfg['inputGroups'], $base_cfg['appid_required']);
		}
		
		return $add_cfg;
	}
	
	/**
	 * 得到edit的config
	 * @param boolean $init, default:true, 是否做配置文件的初始化
	 * @param boolean $key, 编辑的key：edit或者edit2
	 * @return boolean
	 */
	public function getEditCfg($init=true, $key='edit') {
		
		$edit_cfg = isset($this->cfg[$key]) ? $this->cfg[$key] : false;
		
		if ($edit_cfg && $init) {
			$form_mod = Factory::getMod('form');
			$base_cfg = $this->getBaseCfg();
				
			$edit_cfg['inputGroups'] = $form_mod->initInputGroups($edit_cfg['inputGroups'], $base_cfg['appid_required']);
		}
		
		return $edit_cfg;
	}
	
	public function getDelCfg() {
		return isset($this->cfg['delete']) ? $this->cfg['delete'] : false;
	}
	
	public function getResetCfg() {
		return isset($this->cfg['reset']) ? $this->cfg['reset'] : false;
	}
	
	/**
	 * 得到classify的config
	 * @param boolean $init, default:true, 是否做配置文件的初始化
	 * @return boolean
	 */
	public function getClassifyCfg($init=true) {
		$classify_cfg = isset($this->cfg['classify']) ? $this->cfg['classify'] : false;
		
		if ($classify_cfg && $init) {
			$form_mod = Factory::getMod('form');
			$base_cfg = $this->getBaseCfg();
			
			$classify_cfg['inputGroups'] = $form_mod->initInputGroups($classify_cfg['inputGroups'], $base_cfg['appid_required']);
		}
		
		return $classify_cfg;
	}
	
	/**
	 * 尝试获取目录列表，限制最多读取1024条目录数据
	 * 
	 * @return mixed array/false
	 */
	public function getCategoryList() {
		
		$base_cfg = $this->getBaseCfg();
		
		// 如果没有定义 categoryList 或者 配置不对，直接返回false
		if (!(isset($base_cfg['search']['category']) && count($base_cfg['search']['category']) > 0)) return false;
		
		$pub_mod = Factory::getMod('pub');
		$tree_mod = Factory::getMod('tree');
		
		$ret = array();
		
		$option_list = array();
		foreach ($base_cfg['search']['category'] as $k=>$category) {
			if (!is_array($category['options'])) $category['options'] = array();
			
			if (is_array($category['options_source']) && (count($category['options_source']) >0)) {
				
				if (!isset($category['options_source']['where'])) $category['options_source']['where'] = array();
				if ($base_cfg['appid_required']) $category['options_source']['where']['appid'] = C('appid');
				
				$row_list = $pub_mod->getRowListByCfg($category['options_source']);
				$node_list = $tree_mod->getTree($row_list, 0, $category['options_source']['pkid'], $category['options_source']['parentId']);
				
				foreach ($node_list as $m=>$node) {
					if ($node['level'] > $category['options_source']['treeLevel']) {
						unset($node_list[$m]);
					}
				}
				$option_list = $tree_mod->tree2Options($node_list, $category['options_source']['pkid'], $category['options_source']['nodeName']);
			}
			
			$ret[$k] = array(
					'option_list' => array_merge($category['options'], $option_list),
					'tree' => $node_list,
			);
		}
		
		return $ret;
	}
	
	public function translateRow($row, $edit_cfg, $ipt_htmlspecialchars=false) {
	
		$t_mod = Factory::getMod('table');
	
		$ipt_groups = $edit_cfg['inputGroups'];
	
		foreach ($ipt_groups as $key=>$v) {
	
			if (!isset($row[$key])) {
				$row[$key] = '';
				continue;
			}
	
			// 如果是展示字段，判断format
			if (($v['type'] == 'show') && $v['format']) {
	
				$format = $t_mod->_parseSyntex($v['format']);
	
				$row[$key] = $t_mod->_runFunc($format['base'], $format['params'], $row[$key]);
			}
			else {
				$row[$key] = $ipt_htmlspecialchars ? htmlspecialchars($row[$key], ENT_QUOTES) : $row[$key];
			}
		}
	
		return $row;
	}
	
	private function getTree($data, $pid, $curr_level, $category_list_cfg, $path='') {
		
		$tree = array();
		
		if ($curr_level > $category_list_cfg['treeLevel']) return $tree;
		
		foreach ($data as $k=>$v) {
			
			$path_new = empty($path) ? $v[$category_list_cfg['pkid']] : $path.','.$v[$category_list_cfg['pkid']];
			
			// 如果有定义base.categoryList.parentId
			if ($category_list_cfg['parentId']) {
			
				// 父亲找到儿子
				if ($v[$category_list_cfg['parentId']] == $pid) {
					unset($data[$k]);
					//$v['sub_list'] = $this->getTree($data, $v[$category_list_cfg['pkid']], ++$curr_level, $category_list_cfg);
					$tree[$v[$category_list_cfg['pkid']]] = array(
							$category_list_cfg['pkid'] => $v[$category_list_cfg['pkid']],
							$category_list_cfg['parentId'] => $pid,
							'name' => $v[$category_list_cfg['nodeName']],
							'short' => truncate_utf8($v[$category_list_cfg['nodeName']], 4, '..'),
							'path' => $path_new,
							'sub_list' => $this->getTree($data, $v[$category_list_cfg['pkid']], $curr_level+1, $category_list_cfg, $path_new),
					);
				}
			}
			else {
				$tree[$v[$category_list_cfg['pkid']]] = array(
						$category_list_cfg['pkid'] => $v[$category_list_cfg['pkid']],
						'name' => $v[$category_list_cfg['nodeName']],
						'short' => truncate_utf8($v[$category_list_cfg['nodeName']], 4, '..'),
						'path' => $path_new,
						'sub_list' => array(), //$this->getTree($data, $v[$category_list_cfg['pkid']], $curr_level+1, $category_list_cfg, $path_new),
				);
			}
		}
		
		return $tree;		
	}
	
	public function getFilterCidList($tree, $cids) {
		
		$ret = array(
				'do_filter' => false,
				'last_cid' => 0,
				'cid_list' => array(),
		);
		
		if (empty($cids)) return $ret;
		if (!$tree) return $ret;
		
		$cid_list = explode(',', $cids);
		
		// 先根据 $cids 切换到树 $cids对应的最下一层
		while (count($cid_list) > 0) {
			
			$last_cid =  intval(array_shift($cid_list));
			
			if ($last_cid == 0) {
				if (count($cid_list) > 0) {
					$ret['do_filter'] = false;
					return $ret;
				}
				else {
					break;
				}
			}
			
			// 非法last_cid
			if (!isset($tree[$last_cid])) {
				$ret['do_filter'] = false;
				return $ret;
			}
			
			$ret['do_filter'] = true;
			$ret['last_cid'] = $last_cid;
			$ret['cid_list'] = array($last_cid);
			
			if (isset($tree[$last_cid]['sub_list']) && is_array($tree[$last_cid]['sub_list'])) {
				$tree = $tree[$last_cid]['sub_list'];
			}
			else {
				break;
			}
		}		
		
		$ret['cid_list'] = array_merge($ret['cid_list'], $this->getSubCidList($tree));
		
		return $ret;
	}
	
	private function getSubCidList($tree) {
		
		if (is_array($tree)) {
			$cid_list = array_keys($tree);
			
			foreach ($tree as $sub_tree) {
				
				if (isset($sub_tree['sub_list']) && is_array($sub_tree['sub_list'])) {
					$cid_list = array_merge($cid_list, $this->getSubCidList($sub_tree['sub_list']));
				}
				else {
					continue;
				}
			}
			
			return $cid_list;
		}
		
		return array();
	}
	
	public function findTreePath($tree, $last_cid, &$path) {
		
		foreach ($tree as $k=>$sub_tree) {
			
			if ($k == $last_cid) {
				$path = $sub_tree['path'];
				break;
			}
			
			if (isset($sub_tree['sub_list']) && count($sub_tree['sub_list']) > 0) {
				$this->findTreePath($sub_tree['sub_list'], $last_cid, $path);
			}
		}
	}
	
	/**
	 * 尝试读取关联表信息，并添加到$row_list上
	 * 
	 * @param array $row_list
	 * @return array 尝试关联后的$row_list
	 */
	private function tryDoConcat($row_list) {
		
		$base_cfg = $this->getBaseCfg();
		
		// 如果没有定义 关联表 信息，不操作直接返回
		if (count($base_cfg['concatList']) < 1) return $row_list;
		
		// 列表为空时，不操作直接返回
		if (count($row_list) < 1) return $row_list;
		
		foreach ($base_cfg['concatList'] as $concat) {
			
			// 获取需要读取关联表的ID列表
			$concatid_list = array();
			foreach ($row_list as $k=>$row) {
				$concatid_list[$row[$concat['concatid']]] = 1;
			}
			
			$concatid_list = array_keys($concatid_list);
			
			// 开始读取关联表
			$table_data = Factory::getData('table');
			$table_data->init($concat['db_cfg_name'], $concat['db_tbl_alias'], $concat['pkid']);
			
			$where = array(
					$concat['pkid'] => array($concatid_list, 'in'),
			);
			
			$ret = $table_data->getTableList($where, '', 0, count($concatid_list));
			
			// 读取完，重新把data层切回基础表
			$table_data->init($base_cfg['db_cfg_name'], $base_cfg['db_tbl_alias'], $base_cfg['pkid']);
			
			// 转化成以 pkid 为逐渐的hash数组
			$concat_row_list = array();
			foreach ($ret as $m) {
				$concat_row_list[$m[$concat['pkid']]] = $m;
			}
			
			// 开始对$row_list做匹配
			foreach ($row_list as $n=>$row) {
				
				if (isset($concat_row_list[$row[$concat['concatid']]])) {
					
					foreach ($concat['keys'] as $p1=>$p2) {
						$row_list[$n][$p2] = isset($concat_row_list[$row[$concat['concatid']]][$p1]) ? $concat_row_list[$row[$concat['concatid']]][$p1] : '';
					}
				}
				else {
					foreach ($concat['keys'] as $p1=>$p2) {
						$row_list[$n][$p2] = '';
					}
				}
			}
		}
		
		return $row_list;
	}
	
	/**
	 * 通过pkid获得table一行记录
	 * @param int $pkid
	 */
	public function getTableRow($pkid) {
		
		if (false === $this->load_succ) return false;
		
		$table_data = Factory::getData('table');
		
		$row = $table_data->getTableRow($pkid);
		
		return $row;
	}
	
	/**
	 * 通过一定条件获取table的信息列表
	 *
	 * @param array $where
	 * @param int $start
	 * @param string $orderby
	 * @param int $pagesize
	 */
	public function getTableListSimple($where, $orderby='', $start=0, $pagesize=20) {
		
		if (false === $this->load_succ) return false;
		
		$table_data = Factory::getData('table');
		
		$row_list = $table_data->getTableList($where, $orderby, $start, $pagesize);
		
		return $row_list;
	}
	
	/**
	 * 通过一定条件获取table的信息列表，并根据配置文件进行加工处理
	 * 
	 * @param array $where
	 * @param int $start
	 * @param string $orderby
	 * @param int $pagesize
	 */
	public function getTableList($where, $orderby='', $start=0, $pagesize=20) {
		
		if (false === $this->load_succ) return false;

		$table_data = Factory::getData('table');
		
		$row_list = $table_data->getTableList($where, $orderby, $start, $pagesize);
		
		if (!is_array($row_list)) return false;
		
		// 如果定义了关联表，把关联表的信息加到$row_list
		$row_list = $this->tryDoConcat($row_list);
				
		$pkid = $this->cfg['base']['pkid'];
		
		$base_cfg = $this->getBaseCfg();
		$edit_cfg = $this->getEditCfg();
		$del_cfg  = $this->getDelCfg();
		$del_confirm_msg = isset($del_cfg['confirmMsg']) ? htmlspecialchars($del_cfg['confirmMsg'], ENT_QUOTES) : '确定要进行此操作？';
		
		// 对list按照配置文件进行解析
		$list_cfg = $this->getListCfg();
		$ret = array();
		foreach ($row_list as $row) {
			
			$tmp_row = array();
			foreach ($list_cfg as $k=>$v) {
				
				// 如果是操作key
				if ($k == '_ops') {
					$link_list = array();
					foreach ($v['cnt'] as $m) {
						
						$m = $this->_parseSyntex($m);
						
						$cnt_type = $m['base'];
						$cnt_text = strlen($m['text']) > 0 ? $m['text'] : '';
						
						if ($cnt_type == 'edit') {
							if (empty($cnt_text)) $cnt_text = '编辑';
							if ($edit_cfg['open'] == 'modal') {
								$link_list[] = '<a data-id="'.$row[$pkid].'" data-open="dialog" style="margin-right:3px;">'.$cnt_text.'</a>';
							}
							else {
								$link_list[] = '<a href="'.app_echo_url(tpf_var($base_cfg['pageEditUrl'], $row[$pkid]), true).'" style="margin-right:3px;">'.$cnt_text.'</a>';
							}
						}
						elseif ($cnt_type == 'delete') {
							if (empty($cnt_text)) $cnt_text = '删除';
							$link_list[] = '<a data-id="'.$row[$pkid].'" class="js-delete" msg-confirm="'.$del_confirm_msg.'" style="margin-right:3px;">'.$cnt_text.'</a>';
						}
						elseif ($cnt_type == 'reset') {
							if (empty($cnt_text)) $cnt_text = '重置密码';
							$link_list[] = '<a data-id="'.$row[$pkid].'" class="js-reset" style="margin-right:3px;">'.$cnt_text.'</a>';
						}
					}
					$tmp_row[$k] = "<span row-type=\"td-ops\" style=\"\">".implode(" ", $link_list)."</span>";
				}
// 				elseif (!isset($row[$k])) {
// 					$tmp_row[$k] = 'NULL';
// 					continue;
// 				}
				else {
					
					if (isset($v['format']) && $v['format']) {
						
						$format = $this->_parseSyntex($v['format']);						
						$tmp_row[$k] = $this->_runFunc($format['base'], $format['params'], $row[$k]);
					}
					else {
						$tmp_row[$k] = $row[$k];
						
						// 如果没有值，设置显示是-
						if (strlen($tmp_row[$k]) == 0) {
							$tmp_row[$k] = '-';
						}
					}
				}
			}
			
			$ret[] = $tmp_row;
		}
		
		return $ret;
	}
	
	/**
	 * 通过一定条件获取table的列表的总数量
	 *
	 * @param array $where
	 */
	public function getTableCount($where) {
		
		if (false === $this->load_succ) return false;
		
		$table_data = Factory::getData('table');
		
		$ret = $table_data->getTableCount($where);
			
		return $ret;
	}
	
	/**
	 * 创建一个管理用户
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function createTableRow($data) {
		
		if (false === $this->load_succ) return false;
	
		$table_data = Factory::getData('table');
		
		$ret = $table_data->createTableRow($data);
			
		return $ret;
	}
	
	/**
	 * 通过一定条件更新Table信息
	 * 
	 * @param array $data
	 * @param array $where
	 */
	public function updateTable($data, $where) {
		
		if (false === $this->load_succ) return false;
	
		$table_data = Factory::getData('table');
		
		$ret = $table_data->updateTable($data, $where);
			
		return $ret;
	}
	
	/**
	 * 解析table定义的语法
	 * 
	 * 语法定义：
	 * xxx|yyy,zzz||ppp：xxx是函数名；xxx函数会在除了当前操作字段外，传入yyy和zzz变量（多个变量以,分隔，之间不允许有空格，分隔符是|)；ppp是描述文字，用于函数返回的提示或者其他用途（记住分隔符是||)
	 * 
	 * @param string $str
	 * @return arrray(
	 * 		'base'   => xxx,
	 * 		'params' => array(yyy, zzz),
	 * 		'text'   => ppp
	 * 	)
	 */
	public function _parseSyntex($str) {
		
		$ret = array(
				'base' => '',
				'params' => array(),
				'text' => 'undefined text(通常是配置文件语法错误)',
		);
		
		$str_ary = explode('||', $str);
		$str = $str_ary[0];
		
		if (count($str_ary) >= 2) $ret['text'] = $str_ary[1];
		
		$str_ary = explode('|', $str);
		$ret['base'] = $str_ary[0];
		
		if (count($str_ary) >= 2) $ret['params'] = explode(',', $str_ary[1]);
		
		return $ret;
	}
	
	/**
	 * 
	 * @param string $func
	 * @param array $params
	 * @param mixed $pre_arg，前置参数，默认是null，如果不是null，将会array_unshift到$params的第一个元素
	 */
	public function _runFunc($func, $params, $pre_arg=null) {
		
		if ($pre_arg !== null) {
			array_unshift($params, $pre_arg);
		}
		
		return call_user_func_array($func, $params);
	}
}
