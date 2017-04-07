<?php
/**
 * 表管理 ctrl
 */

class TableCtrl extends BaseCtrl {
	
	private function getTbl() {
		$tbl = gstr('tbl');
		return $tbl;
	}

	public function index() {
		
		$login_user = app_get_login_user(1, 1);
		
		$tbl = $this->getTbl();
		$page = gint('page');
		if ($page < 1) $page = 1;
		$kw = gstr('kw');
		$cids = gstr('cids');
		
		$tree_mod = Factory::getMod('tree');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			die('tbl '.$tbl.' not exists!');
		}
		
		$tbl_with_titlelist = array(
				'app.loading' => array(
						array(
								'text' => '启动设置',
								'url'  => '/?c=table&tbl=app.loading',
								'key'  => 'app.loading',
						),
						array(
								'text' => '启动引导',
								'url'  => '/?c=table&tbl=app.guide',
								'key'  => 'app.guide',
						),
				),
				'app.guide' => array(
						array(
								'text' => '启动设置',
								'url'  => '/?c=table&tbl=app.loading',
								'key'  => 'app.loading',
						),
						array(
								'text' => '启动引导',
								'url'  => '/?c=table&tbl=app.guide',
								'key'  => 'app.guide',
						),
				),
		);
		
		$tbl_head = $t_mod->getListCfg();
// 		if (is_array($tbl_head['_ops'])) $tbl_head['_ops']['_w'] = '80px';
		$base_cfg = $t_mod->getBaseCfg();
		$upload_cfg = $t_mod->getUploadCfg();
		$add_cfg = $t_mod->getAddCfg(false);
		$edit_cfg = $t_mod->getEditCfg(false);
		$del_cfg = $t_mod->getDelCfg();
		$classify_cfg = $t_mod->getClassifyCfg();
		$reset_cfg = $t_mod->getResetCfg();
		
		$where = $base_cfg['where'];
		$orderby = $base_cfg['orderby'];
		
		$search_conds = array();
		
		// 尝试获取目录数据
		$category_list = $t_mod->getCategoryList();
// 		dump($category_list);
		foreach ($category_list as $search_key=>$category) {
			$pkid_key = $base_cfg['search']['category'][$search_key]['options_source']['pkid'];
			$pid_key = $base_cfg['search']['category'][$search_key]['options_source']['parentId'];
			
			// 判断是否用户有选择这个搜索条件
			$cid = gint($search_key);
			$search_conds[$search_key] = $cid;
			if ($cid) {				
				$filter_cids = array($cid);
				
				$tree_mod->findSubNodeIds($category['tree'], $filter_cids, $cid, $pkid_key, $pid_key);
				
				$where[$search_key] = array($filter_cids, 'in');
			}
		}
		/* 搜索目录条件处理 end */
		
		// 如果appid_required是true
		if ($base_cfg['appid_required']) {
			$where['appid'] = $login_user['appid'];
		}
		
		$search_conds['kw'] = $kw;
		$search_uri = '';
		foreach ($search_conds as $k=>$v) {
			$search_uri .= '&'.$k.'='.urlencode($v);
		}
		
		// 如果输入了kw
		if ($kw && isset($base_cfg['search']['searchSql'])) {
			$where[$base_cfg['search']['searchKey']] = array(str_replace('{$kw}', $kw, $base_cfg['search']['searchSql']), 'like');
		}		
		
		$tbl_list = $t_mod->getTableList($where, $orderby, ($page-1)*$base_cfg['pagesize'], $base_cfg['pagesize']);
		
		$tbl_count = $t_mod->getTableCount($where);
		$page_total = ceil($tbl_count/$base_cfg['pagesize']);
		$page_content = page($page_total, $page, "?c=table&tbl={$tbl}{$search_uri}&page", $halfPer=10);

		// 传输给view的数据
		$data = array(
				'login_user' => $login_user,
				'tbl'        => $tbl,
				'title_list' => isset($tbl_with_titlelist[$tbl]) ? $tbl_with_titlelist[$tbl] : false,
				'global_cfg' => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
				'base_cfg'   => $base_cfg,
				'category_list' => $category_list,
				'search_conds' => $search_conds,
				'search_uri' => $search_uri,
				'kw'         => $kw,
				'cids'       => $cids,
				'tbl_head'   => $tbl_head,
				'tbl_list'   => $tbl_list,
				'tbl_count'  => $tbl_count,
				'upload_cfg' => $upload_cfg,
				'add_cfg'    => $add_cfg,
				'edit_cfg'   => $edit_cfg,
				'del_cfg'    => $del_cfg,
				'classify_cfg' => $classify_cfg,
				'reset_cfg'  => $reset_cfg,
				'pagesize'   => $base_cfg['pagesize'],
				'page_total' => $page_total,
				'page_content' => $page_content,
				'menu' => $tbl,
		);
		Factory::getView("table/table", $data);
	}
	
	public function add() {
		
		$login_user = app_get_login_user(1, 1);
		
		$tbl = $this->getTbl();
		
		$form_mod = Factory::getMod('form');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			die($tbl.' not exists!');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		$add_cfg = $t_mod->getAddCfg();
		
		// 如果base_cfg有配置搜索，找出所有的搜索条件
		$conds = array();
		$search_uri = '';
		if (is_array($base_cfg['search']) && isset($base_cfg['search']['searchKey'])) {
			$conds['kw'] = gstr($base_cfg['search']['searchKey']);
			$search_uri .= '&kw='.urlencode($conds['kw']);
			
			if (is_array($base_cfg['search']['category'])) {
				foreach ($base_cfg['search']['category'] as $k=>$category) {
					$conds[$k] = gint($k);
					$search_uri .= '&'.$k.'='.$conds[$k];
				}
			}
		}
		
		$add_cfg['inputGroups'] = $form_mod->setInputGroupsValue($add_cfg['inputGroups'], $conds);
		
		$data = array(
				'login_user'  => $login_user,
				'global_cfg'  => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
				'form_id'     => 'tableform',
				'action_url'  => app_echo_url('/?c=table&a=add_save&tbl='.$tbl, true),
				'refresh_url' => app_echo_url('/?c=table&tbl='.$tbl.$search_uri, true),
				'page_type'   => 'page',
				'form_okbtn'  => $add_cfg['submitBtn'],
				'ipt_list'    => $add_cfg['inputGroups'],
				'form_title'  => $add_cfg['addTitle'],
		);
		
		Factory::getView("table/table_form", $data);
	}
	
	public function edit() {
		
		$login_user = app_get_login_user(1, 1);
		
		// 判断上一级链接是否包含cids和kw，用于保存成功的跳转
		$uri = '';
		if ($_SERVER['HTTP_REFERER']) {
			$url_ary = parse_url($_SERVER['HTTP_REFERER']);
			// parse_str($url_ary['query'], $url_ary);
			
			$uri = $url_ary['query'];
		}
		// 判断 end
		
		$tbl = $this->getTbl();
		$pkid = gint('pkid');
		if (empty($pkid)) {
			die('ID错误');
		}
		
		$form_mod = Factory::getMod('form');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			die($tbl.' not exists!');
		}
		
		$row = $t_mod->getTableRow($pkid);
		
		if (!$row) {
			die('要修改的记录不存在，请重试。');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		$edit_cfg = $t_mod->getEditCfg();
		
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			if ($row['appid'] != $login_user['appid']) {
				redirect(app_get_myapp_url());
			}
		}
		
		// 对row进行适当加工
		$row_trans = $t_mod->translateRow($row, $edit_cfg, true);
		
		$edit_cfg['inputGroups'] = $form_mod->setInputGroupsValue($edit_cfg['inputGroups'], $row_trans);
// 		dump($edit_cfg['inputGroups']);
		$data = array(
				'login_user'  => $login_user,
				'global_cfg'  => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
				'form_id'     => 'tableform',
				'action_url'  => app_echo_url('/?c=table&a=edit_save&tbl='.$tbl.'&pkid='.$pkid, true),
				'refresh_url' => app_echo_url('/?'.$uri, true),
				'page_type'   => 'page',
				'form_okbtn'  => $edit_cfg['submitBtn'],
				'ipt_list'    => $edit_cfg['inputGroups'],
				'form_title'  => $edit_cfg['editTitle'],
		);
		
		Factory::getView("table/table_form", $data);
	}
	
	public function classify() {
		
		$login_user = app_get_login_user(1, 1);
		
		$tbl = $this->getTbl();
		$pkids = gstr('pkids');
		if (empty($pkids)) {
			die('ID错误');
		}
		
		$form_mod = Factory::getMod('form');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			die($tbl.' not exists!');
		}
		
		$classify_cfg = $t_mod->getClassifyCfg();
		$base_cfg = $t_mod->getBaseCfg(false);
		
		$data = array(
				'login_user'  => $login_user,
				'global_cfg'  => array('nav'=>$base_cfg['nav'], 'menu_id'=>$base_cfg['menu_id']),
				'form_id'     => 'tableform',
				'action_url'  => app_echo_url('/?c=table&a=classify_save&tbl='.$tbl.'&pkids='.$pkids, true),
				'refresh_url' => '',
				'page_type'   => 'dialog',
				'form_okbtn'  => array('name'=>'提交'),
				'ipt_list'    => $classify_cfg['inputGroups'],
		);
		
		Factory::getView("table/table_form_dialog", $data);
	}
	
	public function addSave() {
		
		$login_user = app_get_login_user(2, 2);
		
		$tbl = $this->getTbl();
		
		$form_mod = Factory::getMod('form');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			echo_result(1, '表不存在');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		$add_cfg = $t_mod->getAddCfg();
		
		$default_set = isset($add_cfg['defaultSet']) ? $add_cfg['defaultSet'] : array();
		$ipt_groups = $add_cfg['inputGroups'];
		
		// ...
		$data = $form_mod->getData($ipt_groups, $default_set, $t_mod);
				
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			$data['appid'] = $login_user['appid'];
		}
		
		// 这里，添加到数据库
		if (!isset($data[$base_cfg['pkid']])) {
			$data[$base_cfg['pkid']] = get_auto_id($base_cfg['autoidCode']);
		}
		
		$ret = $t_mod->createTableRow($data);
		
		if ($ret) {
			echo_result(0, '添加成功');
		}		
		
		echo_result(1, '数据库错误', $data);
		//echo_result(1, '数据库错误');
	}
	
	public function editSave() {
		
		$login_user = app_get_login_user(2, 2);
		
		$tbl = $this->getTbl();
		$pkid = gint('pkid');
		if (empty($pkid)) {
			echo_result(1, 'ID错误');			
		}
		
		$form_mod = Factory::getMod('form');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			echo_result(1, '表不存在');
		}
		
		$row = $t_mod->getTableRow($pkid);
		
		if (!$row) {
			echo_result(1, '要修改的记录不存在，请重试。');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		$edit_cfg = $t_mod->getEditCfg();
		
		$default_set = isset($edit_cfg['defaultSet']) ? $edit_cfg['defaultSet'] : array();
		$ipt_groups = $edit_cfg['inputGroups'];
		
		// ...
		$data = $form_mod->getData($ipt_groups, $default_set, $t_mod);
		
		$where = array(
				$base_cfg['pkid'] => $pkid,
		);
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			$where['appid'] = $login_user['appid'];
		}
		
		$ret = $t_mod->updateTable($data, $where);
		
		if ($ret) {
			echo_result(0, '编辑成功');
		}
		
		echo_result(1, '数据库错误');
	}
	
	public function classifySave() {
		
		$login_user = app_get_login_user(2, 2);
		
		$tbl = $this->getTbl();
		
		$pkids = gstr('pkids');
		if (empty($pkids)) {
			echo_result(1, 'ID错误');
		}
		$pkids = explode(',', $pkids);
		
		$form_mod = Factory::getMod('form');
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			echo_result(1, 'tbl error');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		$classify_cfg = $t_mod->getClassifyCfg();
		$default_set = isset($classify_cfg['defaultSet']) ? $classify_cfg['defaultSet'] : array();
		$ipt_groups = $classify_cfg['inputGroups'];
		
		// ...
		$data = $form_mod->getData($ipt_groups, $default_set, $t_mod);
				
		$where = array(
				$base_cfg['pkid'] => array($pkids, 'in'),
		);
		
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			$where['appid'] = $login_user['appid'];
		}
		
		$ret = $t_mod->updateTable($data, $where);
		
		echo_result(0, '修改成功');
	}
	
	public function del() {
		
		$login_user = app_get_login_user(2, 2);
		
		$tbl = $this->getTbl();
		$id = gint('id');
		// 尝试获取ids，看是不是批量删除
		if (!$id) {
			$id = explode(',', gstr('ids'));
		}
		else {
			$id = array($id);
		}
		
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			echo_result(1, 'tbl error');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		$del_cfg = $t_mod->getDelCfg();
		$default_set = isset($del_cfg['defaultSet']) ? $del_cfg['defaultSet'] : array();
		
		if (!$del_cfg) {
			echo_result(1, '不支持此功能，请先在配置文件配置。');
		}
		
		$pkid = $base_cfg['pkid'];
		
		$data = array(
				$del_cfg['del_key'] => $del_cfg['del_value'],
		);
		
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
		
		$where = array(
				$pkid => array($id, 'in'),
		);
		
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			$where['appid'] = $login_user['appid'];
		}
		
		$ret = $t_mod->updateTable($data, $where);
		
		echo_result(0, 'succ');
	}
	
	public function reset() {

		$login_user = app_get_login_user(2, 2);
	
		$tbl = $this->getTbl();
		$id = gint('id');
		
		if (!$id) {
			echo_result(1, '参数错误');
		}
		
		$id = array($id);
	
		$t_mod = Factory::getMod('table');
	
		if (!$t_mod->tryLoadTable($tbl)) {
			echo_result(1, 'fail');
		}
	
		$base_cfg = $t_mod->getBaseCfg();
		$reset_cfg = $t_mod->getResetCfg();
		$default_set = isset($reset_cfg['defaultSet']) ? $reset_cfg['defaultSet'] : array();
	
		if (!$reset_cfg) {
			echo_result(1, '不支持此功能，请先在配置文件配置。');
		}
	
		$pkid = $base_cfg['pkid'];
		
		$password_new = '';
		if ($reset_cfg['type'] == 'random') {
			$password_new = random_password();
		}
		else {
			echo_result(1, '配置文件出错，请检查');
		}
		$data = array(
				$reset_cfg['key'] => md5($password_new),
		);
		
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
		
		$where = array(
				$pkid => array($id, 'in'),
		);
		
		// 如果有 appid_required
		if ($base_cfg['appid_required']) {
			$where['appid'] = $login_user['appid'];
		}
	
		$ret = $t_mod->updateTable($data, $where);
	
		echo_result(0, $password_new);
	}
	


}
