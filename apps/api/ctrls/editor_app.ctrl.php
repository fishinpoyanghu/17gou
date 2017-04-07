<?php
/**
 * 编辑器app类
 */

class EditorAppCtrl extends BaseCtrl {
	

	
	public function appTplList() {
		
		// 调用测试用例
// 		$this->test_app_tpl_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		$cid = pint('cid');
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getPageCond();
		
		$pub_mod = Factory::getMod('pub');
		
		$where = array(
				'is_tpl' => 1,
		);
		if ($cid) $where['cid'] = $cid;
		$orderby = 'ORDER BY tpl_weight DESC, appid ASC';
		
		$pub_mod->init('admin', 'app', 'appid');
		$app_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $orderby);
		
		$data_list = array();
		foreach ($app_list as $app) {
			
			$data_list[] = array(
					'appid' => intval($app['appid']),
					'name' => ap_strval($app['name']),
					'cid' => intval($app['tpl_category_id']),
					'imgs' => explode(',', ap_strval($app['tpl_imgs'])),
					'description' => ap_strval($app['desc']),
					'qrcode' => ap_strval($app['qrcode']),
			);
		}
		
		api_result(0, 'succ', $data_list);
	}
	
	public function saveApp() {
		
		// 调用测试用例
// 		$this->test_save_app();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, '要编辑的app不存在');
		}
		
		$validate_cfg = array(
				'name' => array(
						'api_v_length|1,6||APP名字必须1-6个字之间',
						'api_v_notspace||APP名字不能包含空格',
				),
				'icon' => array(
				),
				'loading_imgs' => array(
				),
				'guid_imgs' => array(
				),
				'platform' => array(
						'api_v_inarray|all;;android;;ios;;||platform参数不合法'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list = api_safe_ipt($ipt_list);
		
		$pub_mod = Factory::getMod('pub');
		
		// 保存app
		$update_app = array(
				'name' => $ipt_list['name'],
				'icon' => $ipt_list['icon'],
				'platform' => $ipt_list['platform'],
				'ut' => time(),
		);
		
		$pub_mod->init('admin', 'app', 'appid');
		$ret = $pub_mod->updateRow($app['appid'], $update_app);
		
		if (false === $ret) {
			api_result(1, '数据库错误，请重试#code1');
		}
		
		// 保存loading
		$update_loading = array(
				'stat' => 1,
				'ut' => time(),
		);
		$where_loading = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		
		$pub_mod->init('admin', 'app_loading', 'app_loading_id');
		$ret = $pub_mod->updateRowWhere($where_loading, $update_loading);
		
		if (false === $ret) {
			api_result(1, '数据库错误，请重试#code2');
		}
		
		if ($ipt_list['loading_imgs']) {
			
			$data_list = array();
			foreach ($ipt_list['loading_imgs'] as $loading_img) {
				$data_list[] = array(
						'app_loading_id' => get_auto_id(C('AUTOID_APPLOADING')),
						'appid' => $base['appid'],
						'img' => $loading_img,
						'url' => '',
						'type' => 0,
						'weight' => 0,
						'rt' => time(),
						'ut' => time(),
				);
			}
			
			$pub_mod->createRows($data_list);
		}
		if ($ipt_list['guid_imgs']) {
				
			$data_list = array();
			foreach ($ipt_list['guid_imgs'] as $guid_img) {
				$data_list[] = array(
						'app_loading_id' => get_auto_id(C('AUTOID_APPLOADING')),
						'appid' => $base['appid'],
						'img' => $guid_img,
						'url' => '',
						'type' => 1,
						'weight' => 0,
						'rt' => time(),
						'ut' => time(),
				);
			}
				
			$pub_mod->createRows($data_list);
		}
		
		api_result(0, 'succ');
	}
	
	public function getApp() {

		// 调用测试用例
// 		$this->test_get_app();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, '要获取的app不存在');
		}
		
		// 得到启动图片和引导图片
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('admin', 'app_loading', 'app_loading_id');
		
		$where = array(
				'appid' => $app['appid'],
				'type'  => 0,
				'stat'  => 0,
		);
		$loading_list = $pub_mod->getRowList($where, 0, 20, ' ORDER BY app_loading_id ASC');
		
		$where['type'] = 1;
		$guid_list = $pub_mod->getRowList($where, 0, 20, ' ORDER BY app_loading_id ASC');
		
		$loading_imgs = array();
		foreach ($loading_list as $loading) {
			$loading_imgs[] = $loading['img'];
		}
		
		$guid_imgs = array();
		foreach ($guid_list as $guid) {
			$guid_imgs[] = $guid['img'];
		}
		
		// 输出
		$data = array(
				'appid' => intval($app['appid']),
				'appkey' => ap_strval($app['appkey']),
				'name' => ap_strval($app['name']),
				'icon' => ap_strval($app['icon']),
				'big_icon' => ap_strval($app['big_icon']),
				'loading_imgs' => implode(',', $loading_imgs),
				'guid_imgs' => implode(',', $guid_imgs),
				'platform' => ap_strval($app['platform']),
				'qrcode' => ap_strval($app['qrcode']),
				'rt' => date_friendly($app['rt']),
		);
		
		api_result(0, 'succ', $data);
	}
	
	public function deleteApp() {

		// 调用测试用例
// 		$this->test_delete_app();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, '要删除的app不存在');
		}

		$pub_mod = Factory::getMod('pub');
		
		// 到这里，表示可以删除app
		$update_data = array(
				'stat' => 1,
				'ut' => time(),
		);
		
		$pub_mod->init('admin', 'app', 'appid');
		
		$ret = $pub_mod->updateRow($app['appid'], $update_data);
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, 'succ');
	}
	
	public function myAppList() {
	
		// 调用测试用例
// 		$this->test_my_app_list();
	
		// 标准参数检查
		$base = api_check_base();
	
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
	
		$pub_mod = Factory::getMod('pub');
	
		$where = array(
				'uid' => $login_user['uid'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app', 'appid');
		$app_list = $pub_mod->getRowList($where);
	
		$data_list = array();
	
		foreach ($app_list as $app) {
				
			$data_list[] = array(
					'appid' => intval($app['appid']),
					'appkey' => ap_strval($app['appkey']),
					'name' => ap_strval($app['name']),
					'icon' => ap_strval($app['icon']),
					'big_icon' => ap_strval($app['big_icon']),
					'qrcode' => ap_strval($app['qrcode']),
					'rt' => date_friendly($app['rt']),
			);
		}
	
		// 到这里，可以输出了
		api_result(0, 'succ', $data_list);
	}
	
	public function createApp() {
		
		// 调用测试用例
// 		$this->test_create_app();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'copy_from' => array(
						'api_v_numeric|1||模板ID不合法',
				),
				'name' => array(
						'api_v_length|1,6||APP名字必须1-6个字之间',
						'api_v_notspace||APP名字不能包含空格',
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		// 判断copy_from的app存不存在
		$pub_mod->init('admin', 'app', 'appid');
		$tpl_app = $pub_mod->getRow($ipt_list['copy_from']);
		
		if (!$tpl_app) {
			api_result(2, '要使用的模板不存在');
		}
		if ($tpl_app['stat'] == 1) {
			api_result(1, '要使用的模板被禁用');
		}
		
		// 判断是否是模板app
		if ($tpl_app['is_tpl'] != 1) {
			api_result(1, '不允许使用的模板');
		}
		
		// 这里，判断用户是否有权限使用该模板
		// @todo
		
		// 判断，判断用户是否有权限创建app
		// @todo
		
		/* 到这里，表示可以开始创建app了
		   分6步走 */
		// 1. 复制t_app表的记录
		$data = array(
				'appid' => get_auto_id(C('AUTOID_APP')),
				'uid' => $login_user['uid'],
				'company_id' => 1, // @todo
				'appkey' => ap_generate_appkey($base['appid']),
				'name' => $ipt_list['name'],
				'copy_from' => $ipt_list['copy_from'],
				'is_tpl' => 0,
				'nav_bgcolor' => $tpl_app['nav_bgcolor'],
				'top_bgcolor' => $tpl_app['top_bgcolor'],
				'bgcolor' => $tpl_app['bgcolor'],
				'qrcode' => '',
				'icon' => $tpl_app['icon'],
				'big_icon' => $tpl_app['big_icon'],
				'desc' => $tpl_app['desc'],
				'date_start' => date('Y-m-d', time()),
				'date_overdue' => date('Y-m-d', time()+315360000), // 默认10年过期
				'rt' => time(),
				'ut' => time(),
		);
		
		// 这里，生成二维码
		// @todo
		$qrcode = '';
		$data['qrcode'] = $qrcode;
		
		$pub_mod->init('admin', 'app', 'appid');
		$ret = $pub_mod->createRow($data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试#code1');
		}
		
		// 2. 复制t_app_loading表的记录
		$where = array(
				'appid' => $ipt_list['copy_from'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_loading', 'app_loading_id');
		$loading_list = $pub_mod->getRowList($where);
		
		$loading_data_list = array();
		if ($loading_list && count($loading_list) > 0) {
			
			foreach ($loading_list as $loading) {
				$loading_data_list[] = array(
						'app_loading_id' => get_auto_id(C('AUTOID_APPLOADING')),
						'appid' => $data['appid'],
						'img' => $loading['img'],
						'url' => $loading['url'],
						'type' => $loading['type'],
						'weight' => $loading['weight'],
						'rt' => time(),
						'ut' => time(),
				);
			}
			
			$ret = $pub_mod->createRows($loading_data_list);
			
			if (!$ret) {
				api_result(1, '数据库错误，请重试#code2');
			}
		}
		
		// 3. 复制t_app_nav表的记录
		$where = array(
				'appid' => $ipt_list['copy_from'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_nav', 'app_nav_id');
		$nav_list = $pub_mod->getRowList($where);
		
		$nav_data_list = array();
		if ($nav_list && (count($nav_list) > 0)) {			
			foreach ($nav_list as $nav) {
				$nav_data_list[] = array(
						'app_nav_id' => get_auto_id(C('AUTOID_APPNAV')),
						'appid' => $data['appid'],
						'name' => $nav['name'],
						'edit_url' => $nav['edit_url'],
						'url' => $nav['url'],
						'icon' => $nav['icon'],
						'active_icon' => $nav['active_icon'],
						'active_color' => $nav['active_color'],
						'color' => $nav['color'],
						'weight' => $nav['weight'],
						'rt' => time(),
						'ut' => time(),
				);
			}
			
			$ret = $pub_mod->createRows($nav_data_list);
			
			if (!$ret) {
				api_result(1, '数据库错误，请重试#code3');
			}
		}
		
		// 4. 复制t_app_page表的记录
		$where = array(
				'appid' => $ipt_list['copy_from'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		$page_list = $pub_mod->getRowList($where);
		
		$page_data_list = array();		
		if ($page_list && (count($page_list) > 0)) {
			foreach ($page_list as $page) {
				$page_data_list[] = array(
						'app_page_id' => get_auto_id(C('AUTOID_APP_PAGE')),
						'appid' => $data['appid'],
						'tpl_id' => $page['tpl_id'],
						'name' => $page['name'],
						'description' => $page['description'],
						'bg_color' => $page['bg_color'],
						'type' => $page['type'],
						'edit_degree' => $page['edit_degree'],
						'is_update' => 0,
						'components' => $page['components'],
						'is_nav' => $page['is_nav'],
						'rt' => time(),
						'ut' => time(),
				);
			}
			
			$ret = $pub_mod->createRows($page_data_list);
			
			if (!$ret) {
				api_result(1, '数据库错误，请重试#code4');
			}
		}
		
		// 5. 创建app的目录
		
		// 6. 复制代码到app目录
		
		// 做app的输出
		$out_data = array(
				'appid' => intval($data['appid']),
				'appkey' => ap_strval($data['appkey']),
				'uid' => intval($login_user['uid']),
				'name' => ap_strval($data['name']),
				'icon' => ap_strval($data['icon']),
				'big_icon' => ap_strval($data['big_icon']),
				'enter_img' => '',
				'loading_img' => array(),
				'top_bg_color' => ap_strval($data['top_bgcolor']),
				'copy_from' => intval($data['copy_from']),
				'create_time' => $data['rt'],
				'update_time' => $data['ut'],
				'publish' => 0,
				'platform' => '',
				'navs' => array(
						'bg_color' => ap_strval($data['nav_bgcolor']),
						'nav_list' => array(),
				),
				'pages' => array(),
		);
		
		foreach ($loading_data_list as $loading) {
				
			if ($loading['type'] == 0) {
				$out_data['enter_img'] = $loading['img'];
			}
			elseif ($loading['type'] == 1) {
				$out_data['loading_img'][] = $loading['img'];
			}
		}
		
		foreach ($nav_data_list as $nav) {
			$out_data['navs']['nav_list'][] = $this->format_nav($nav);
		}
		
		foreach ($page_data_list as $page) {
			$out_data['pages'][] = $this->format_page($page);
		}
		
		api_result(0, 'APP创建成功', $out_data);
	}

	public function getAppConfig() {
	
		// 调用测试用例
// 		$this->test_get_app_config();
	
		// 标准参数检查
		$base = api_check_base();
	
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, '要获取的app不存在');
		}
		
		$pub_mod = Factory::getMod('pub');
	
		// 2. 取得t_app_loading表的记录
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_loading', 'app_loading_id');
		$loading_list = $pub_mod->getRowList($where);
	
		// 3. 取得t_app_nav表的记录
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_nav', 'app_nav_id');
		$nav_list = $pub_mod->getRowList($where);
		
		// 4. 取得t_app_page表的记录
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		$page_list = $pub_mod->getRowList($where);
	
		// 做app的输出
		$out_data = array(
				'appid' => intval($app['appid']),
				'appkey' => ap_strval($app['appkey']),
				'uid' => intval($login_user['uid']),
				'name' => ap_strval($app['name']),
				'icon' => ap_strval($app['icon']),
				'big_icon' => ap_strval($app['big_icon']),
				'enter_img' => '',
				'loading_img' => array(),
				'top_bg_color' => ap_strval($app['top_bgcolor']),
				'copy_from' => intval($app['copy_from']),
				'create_time' => intval($app['rt']),
				'update_time' => intval($app['ut']),
				'publish' => ($app['publish']) ? true : false,
				'platform' => ap_strval($app['platform']),
				'navs' => array(
						'bg_color' => ap_strval($app['nav_bgcolor']),
						'nav_list' => array(),
				),
				'pages' => array(),
		);
		
		foreach ($loading_list as $loading) {
			
			if ($loading['type'] == 0) {
				$out_data['enter_img'] = $loading['img'];
			}
			elseif ($loading['type'] == 1) {
				$out_data['loading_img'][] = $loading['img'];
			}
		}
	
		foreach ($nav_list as $nav) {
			$out_data['navs']['nav_list'][] = $this->format_nav($nav);
		}
	
		foreach ($page_list as $page) {
			$out_data['pages'][] = $this->format_page($page);
		}
	
		api_result(0, 'succ', $out_data);
	}
	
	private function format_nav($nav) {
		
		return array(
				'name' => ap_strval($nav['name']),
				'url' => ap_strval($nav['url']),
				'edit_url' => ap_strval($nav['edit_url']),
				'icon' => ap_strval($nav['icon']),
				'active_icon' => ap_strval($nav['active_icon']),
				'active_color' => ap_strval($nav['active_color']),
				'color' => ap_strval($nav['color']),
		);
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
				'type' => intval($page['type']),
				'edit_degree' => intval($page['edit_degree']),
				'is_update' => $page['is_update'] ? true : false,
				'components' => $components,
		);
		
		return $data;
	}
}
