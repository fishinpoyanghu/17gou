<?php
/**
 * 举报api
 */
class ExposeCtrl extends BaseCtrl {


	
	public function doExpose() {
		
		// 调用测试用例
// 		$this->test_do_expose();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'p_uid' => array(
				),
				'target_id' => array(
						'api_v_numeric|1||target_id不合法',
				),
				'target_type' => array(
						'api_v_inarray|1;;2;;3||target_type参数不合法'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$data = array(
				'expose_id' => get_auto_id(C('AUTOID_M_EXPOSE')),
				'appid' => $base['appid'],
				'uid' => $login_user['uid'],
				'target_type' => $ipt_list['target_type'],
				'target_id' => $ipt_list['target_id'],
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'expose', 'expose_id');
		
		$pub_mod->createRow($data);
		
		api_result(0, '举报成功');
	}
}