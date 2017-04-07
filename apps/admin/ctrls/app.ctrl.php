<?php
/**
 * App展示处理控制器
 */

class AppCtrl extends BaseCtrl {

	public function index() {
		
		$login_user = app_get_login_user(1, 0);
		
		$pub_mod = Factory::getMod('pub');
		
		$where = array(
//				'uid' => $login_user['uid'],
				'stat' => 0,
		);
		$orderby = 'ORDER BY appid ASC';
		$pub_mod->init('admin', 'app', 'appid');
		
		$app_list = $pub_mod->getRowList($where, 0, 128, $orderby);
		
		$data = array(
				'login_user' => $login_user,
				'app_list' => $app_list,
				'hide_header' => true,
				'logo_title' => '选择APP',
		);
		
		Factory::getView("app/my_app", $data);
	}
	
	/**
	 * App相关的各种编辑信息
	 */
	public function info() {
		
		$login_user = app_get_login_user(1, 1);
		
		$allow_tbl_list = array(
				'app.info',
				'app.protocol',
		);
		
		$tbl = gstr('tbl');
		if (empty($tbl)) $tbl = 'app.info';
		
		if (!in_array($tbl, $allow_tbl_list)) {
			dump('不允许的tbl参数');exit;
		}
		
		$pkid = C('appid');
		
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
		$edit_cfg = $t_mod->getEditCfg(true);
		
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
				'hide_cancel' => true,
				'form_id'     => 'tableform',
				'action_url'  => app_echo_url('/?c=table&a=edit_save&tbl='.$tbl.'&pkid='.$pkid, true),
				'refresh_url' => app_echo_url('/?c=app&a=info&tbl='.$tbl, true),
				'page_type'   => 'page',
				'form_okbtn'  => $edit_cfg['submitBtn'],
				'ipt_list'    => $edit_cfg['inputGroups'],
				'form_title'  => $edit_cfg['editTitle'],
		);
		
		Factory::getView("table/table_form", $data);
	}
	
	/**
	 * App支付相关的信息
	 */
	public function pay() {
		
		$login_user = app_get_login_user(1, 1);
		
		$type = gstr('type');
		if (empty($type)) $type = 'wx';
		
		$type_list = array(
				'wx' => 'app.pay',
				'wxmp' => 'app.paymp',
		);
		
		if (!isset($type_list[$type])) {
			dump('type参数错误');exit;
		}
		
		$tbl = $type_list[$type];
		$pkid = C('appid');
		
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
		$edit_cfg = $t_mod->getEditCfg(true);
		
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
				'hide_cancel' => true,
				'type'        => $type,
				'form_id'     => 'tableform',
				'action_url'  => app_echo_url('/?c=table&a=edit_save&tbl='.$tbl.'&pkid='.$pkid, true),
				'refresh_url' => app_echo_url('/?c=app&a=pay&type='.$type, true),
				'page_type'   => 'page',
				'form_okbtn'  => $edit_cfg['submitBtn'],
				'ipt_list'    => $edit_cfg['inputGroups'],
				'form_title'  => $edit_cfg['editTitle'],
		);
		
		Factory::getView("app/app_form", $data);
	}
}