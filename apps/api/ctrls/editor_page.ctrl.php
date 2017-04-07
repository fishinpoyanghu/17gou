<?php
/**
 * 编辑器page类
 */

class EditorPageCtrl extends BaseCtrl {
	

	public function deletePage() {

		// 调用测试用例
// 		$this->test_delete_page();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}

		$pub_mod = Factory::getMod('pub');
				
		$validate_cfg = array(
				'page_id' => array(
						'api_v_numeric|1||页面ID非法',
				),
		);
		$plist = api_get_posts($base['appid'], $validate_cfg);
		
		$where = array(
				'app_page_id' => $plist['page_id'],
				'appid' => $app['appid'],
				'stat' => 0,
		);
		
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		$page = $pub_mod->getRowWhere($where);
		
		if (!$page ) {
			api_result(2, '要删除的页面不存在');
		}
		
		// 到这里，表示可以删除page
		$update_data = array(
				'stat' => 1,
				'ut' => time(),
		);
		
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		
		$ret = $pub_mod->updateRow($plist['page_id'], $update_data);
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, 'succ');
	}
	
	public function savePage() {

		// 调用测试用例
// 		$this->test_save_page();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);

		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$pub_mod = Factory::getMod('pub');
		
		$validate_cfg1 = array(
				'page_id' => array(
						'api_v_numeric|1||页面ID非法',
				),
				'name' => array(
						'api_v_length|0,20||页面名称长度不能超过20个字',
				),
				'description' => array(
						'api_v_length|0,200||页面描述长度不能超过200个字',
				),
				'bg_color' => array(
						'api_v_iscolor|1||颜色不合法',
				),
				'type' => array(
						'api_v_length|0,10||type不能超过10个字',
				),
				'edit_degree' => array(
						
				),
		);
		$validate_cfg2 = array(
				'components' => array(
					
				),
		);
		
		$plist1 = api_get_posts($base['appid'], $validate_cfg1);
		$plist2 = api_get_posts($base['appid'], $validate_cfg2);
		
		$plist1['edit_degree'] = intval($plist1['edit_degree']);
		$plist1 = api_safe_ipt($plist1);
		
		// 判断components是否合法
		if ($plist2['components']) {
			$plist2['components'] = json_decode($plist2['components'], true);
			
			if (!is_array($plist2['components'])) {
				api_result(5, 'components不合法');
			}
		}
		else {
			$plist2['components'] = array();
		}
		
		$plist2['components'] = json_encode($plist2['components']);
		
		// 判断page_id是否合法
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		
		$page = $pub_mod->getRow($plist1['page_id']);
			
		if (!$page) {
			api_result(2, '要编辑的页面不存在#code1');
		}
		
		if ($page['appid'] != $app['appid']) {
			api_result(2, '要编辑的页面不存在#code2');
		}
		
		// 到这里，表示可以保存了
		$update_data = array(
				'name' => $plist1['name'],
				'description' => $plist1['description'],
				'bg_color' => $plist1['bg_color'],
				'type' => $plist1['type'],
				'edit_degree' => $plist1['edit_degree'],
				'components' => $plist2['components'],
				'is_update' => 0,
				'ut' => time(),
		);
		
		$ret = $pub_mod->updateRow($plist1['page_id'], $update_data);
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, 'succ');
	}
	
	public function initPage() {

		// 调用测试用例
// 		$this->test_init_page();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);

		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$tpl_id = gint('tpl_id');
		
		$pub_mod = Factory::getMod('pub');
		$app_page_id = get_auto_id(C('AUTOID_APP_PAGE'));

		// 如果使用了模板，从模板里复制
		if ($tpl_id > 0) {
			
			$pub_mod->init('admin', 'tpl', 'tpl_id');
			$tpl = $pub_mod->getRow($tpl_id);
			
			if (!$tpl) {
				api_result(2, '要使用的模板不存在#code1');
			}
			
			// 这里要判断用户有没有使用该模板的权限
			// @todo
			
			// 这里要扣除用户使用该模板需要扣除的积分和金币
			// @todo
			
			if ($tpl['page_id'] < 1) {
				api_result(1, '恭喜，你发现错误啦，程序猿没把这个模板配置对');
			}
			$pub_mod->init('admin', 'app_page', 'app_page_id');
			$page = $pub_mod->getRow($tpl['page_id']);
			
			if (!$page) {
				api_result(2, '要使用的模板不存在#code2');
			}
			
			// 开始做page的复制咯
			$page_data = array(
					'app_page_id' => $app_page_id,
					'appid' => $app['appid'],
					'tpl_id' => $page['tpl_id'],
					'name' => $page['name'],
					'description' => $page['description'],
					'bg_color' => $page['bg_color'],
					'type' => $page['type'],
					'edit_degree' => $page['edit_degree'],
					'is_update' => 0,
					'components' => $page['components'],
					'is_nav' => 0,
					'rt' => time(),
					'ut' => time(),
			);
			
			$ret = $pub_mod->createRow($page_data);
			if (!$ret) {
				api_result(1, '数据库错误，请重试#code1');
			}
		}
		else {
			$page_data = array(
					'app_page_id' => $app_page_id,
					'appid' => $app['appid'],
					'tpl_id' => 0,
					'name' => '',
					'description' => '',
					'bg_color' => '',
					'type' => '',
					'components' => '',
					'is_nav' => 0,
					'rt' => time(),
					'ut' => time(),
			);
			
			$pub_mod->init('admin', 'app_page', 'app_page_id');
			$ret = $pub_mod->createRow($page_data);
			if (!$ret) {
				api_result(1, '数据库错误，请重试#code2');
			}
		}
		
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		$app_page = $pub_mod->getRow($app_page_id);
		
		// 到这里，成功啦，返回页面的信息
		api_result(0, 'succ', $this->format_page($page));
	}

	public function getNavPageList() {

		// 调用测试用例
// 		$this->test_get_nav_page_list();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);

		$pub_mod = Factory::getMod('pub');
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$where = array(
				'appid' => $app['appid'],
				'is_nav' => 1,
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		$page_list = $pub_mod->getRowList($where);
		
		$data_list = array();
		
		foreach ($page_list as $page) {
			
			$data_list[] = $this->format_page($page);
		}
		
		// 到这里，可以输出了
		api_result(0, 'succ', $data_list);
	}
	
	private function format_page($page) {
		
		$components = array();
		if ($page['components']) {
			$components = json_decode($page['components'], true);
		
			if (!is_array($components)) $components = array();
		}
			
		$data = array(
				'page_id' => intval($page['app_page_id']),
				'tpl_id'  => intval($page['tpl_id']),
				'name' => ap_strval($page['name']),
				'description' => ap_strval($page['description']),
				'bg_color' => ap_strval($page['bg_color']),
				'type' => ap_strval($page['type']),
				'edit_degree' => intval($page['edit_degree']),
// 				'is_update' => $page['is_update'] ? true : false,
				'components' => $components,
		);
		
		return $data;
	}
}
