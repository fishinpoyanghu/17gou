<?php
/**
 * 编辑器nav类
 */

class EditorNavCtrl extends BaseCtrl {


	public function navList() {

		// 调用测试用例
// 		$this->test_nav_list();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);

		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('admin', 'app_nav', 'app_nav_id');
		$nav_list = $pub_mod->getRowList($where, 0, 20, 'ORDER BY app_nav_id ASC');
		
		$data = array(
				'bg_color' => ap_strval($app['nav_bgcolor']),
				'nav_list' => array(),
		);
		foreach ($nav_list as $nav) {
			
			$data['nav_list'][] = array(
					'nav_id' => intval($nav['app_nav_id']),
					'name' => ap_strval($nav['name']),
					'url' => ap_strval($nav['url']),
					'edit_url' => ap_strval($nav['edit_url']),
					'icon' => ap_strval($nav['icon']),
					'active_icon' => ap_strval($nav['active_icon']),
					'active_color' => ap_strval($nav['active_color']),
					'color' => ap_strval($nav['color']),
					'rt' => date_friendly($nav['rt']),
			);
		}
		
		api_result(0, 'succ', $data);
	}

	public function saveNavList() {

		// 调用测试用例
// 		$this->test_save_nav_list();

		// 标准参数检查
		$base = api_check_base();

		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);

		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$validate_cfg = array(
				'navs' => array(
				),
		);		
		$plist = api_get_posts($base['appid'], $validate_cfg);
		
		// 判断navs是否合法
		if ($plist['navs']) {
			$plist['navs'] = json_decode($plist['navs'], true);
				
			if (!is_array($plist['navs'])) {
				api_result(5, 'navs不合法');
			}
		}
		else {
			$plist['navs'] = array();
		}
		
		$plist['navs'] = api_safe_ipt($plist['navs']);
		
		$pub_mod = Factory::getMod('pub');
		
		// 先删除导航
		$update_nav = array(
				'stat' => 1,
				'ut' => time(),
		);
		$where_nav = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		
		$pub_mod->init('admin', 'app_nav', 'app_nav_id');
		$ret = $pub_mod->updateRowWhere($where_nav, $update_nav);
		
		if (false === $ret) {
			api_result(1, '数据库错误，请重试#code1');
		}
		
		// 插入导航
		if (count($plist['navs']) > 0) {
			$data_list = array();
			foreach ($plist['navs'] as $nav) {
				$data_list[] = array(
						'app_nav_id' => get_auto_id(C('AUTOID_APPNAV')),
						'appid' => $app['appid'],
						'name' => $nav['name'],
						'url' => $nav['url'],
						'edit_url' => $nav['edit_url'],
						'icon' => $nav['icon'],
						'active_icon' => $nav['active_icon'],
						'active_color' => $nav['active_color'],
						'color' => $nav['color'],
						'weight' => 0,
						'rt' => time(),
						'ut' => time(),
				);
			}
			
			$ret = $pub_mod->createRows($data_list);
			if (!$ret) {
				api_result(1, '数据库错误，请重试#code2');
			}
		}
		
		api_result(0, 'succ');
	}
}