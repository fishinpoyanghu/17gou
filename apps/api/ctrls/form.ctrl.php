<?php
/**
 * 表单api
 * 
 */

class FormCtrl extends BaseCtrl {
	


	public function doForm() {
		
		// 调用测试用例
// 		$this->test_do_form();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'form_id' => array(
						'api_v_numeric|1||form_id不合法',
				),
				'anonymous' => array(
						'api_v_inarray|0;;1||anonymous不合法',
				),
				'options' => array(
						'api_v_json||提交的值不合法'
				),
				'time' => array(
						'api_v_numeric|1||time不合法'
				),
				'checksum' => array(
						'api_v_length|32,32||checksum不合法'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		// 判断time是否合法，较当前时间大于15分钟的，就当做不合法
		if ((time() - $ipt_list['time']) > 900) {
			api_result(5, '已过期，请重新填写');
		}
		
		// 判断checksum合不合法
		if (md5($base['app']['appkey'].$ipt_list['form_id'].$ipt_list['time'].$ipt_list['options']) != $ipt_list['checksum']) {
			api_result(5, '非法参数，checksum校验不通过');
		}
		
		$ipt_list['options'] = api_safe_ipt(json_decode($ipt_list['options'], true));
		
		$pub_mod = Factory::getMod('pub');
		
		// 判断form_id是否存在
		$pub_mod->init('main', 'form', 'form_id');
		$where = array(
				'form_id' => $ipt_list['form_id'],
				'appid' => $base['appid'],
				'stat' => 0,
		);
		
		$form = $pub_mod->getRowWhere($where);
		
		if (!$form) {
			api_result(2, '表单不存在或者已经被删除');
		}
		
		// 判断当前表单是否可以提交
		$this->is_form_active($form, $login_user['uid']);
		
		// 到这里，表示表单可以提交了
		$res_data = array(
				'form_res_id' => get_auto_id(C('AUTOID_M_FORM_RES')),
				'form_id' => $ipt_list['form_id'],
				'uid' => $login_user['uid'],
				'anonymous' => $ipt_list['anonymous'],
				'res' => json_encode($ipt_list['options']),
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod->init('main', 'form_res', 'form_res_id');
		$ret = $pub_mod->createRow($res_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, 'succ');
	}
	
	private function get_form_type_name($type) {
		$type_list = array(
				0 => '表单',
				1 => '投票',
				2 => '调查问卷'
		);
		
		return $type_list[$type];
	}
	
	private function is_form_active($form, $uid) {
		
		$type = $this->get_form_type_name($form['type']);
		
		if (!$form['is_open']) {
			api_result(1, '该'.$type.'还没对外开放，不能提交');
		}
		
		// 判断开始时间
		if ($form['times_start'] && ($form['times_start'] > time())) {
			api_result(1, '该'.$type.'还没开始，不能提交');
		}
		
		if ($form['times_end'] && ($form['times_end'] < time())) {
			api_result(1, '该'.$type.'已经结束，不能提交');
		}
		
		// 判断频率
		$where = array(
				'form_id' => $form['form_id'],
				'uid' => $uid,
				'rt' => array(time() - time()%86400 - 86400*($form['freq_days']-1), '>='),
		);
		
		$pub_mod = Factory::getMod('pub');		
		$pub_mod->init('main', 'form_res', 'form_res_id');
		
		$total = $pub_mod->getRowTotal($where);
		
		if ($total >= $form['freq_count']) {
			api_result(1, $form['freq_days'].'天最多只能提交'.$form['freq_count'].'次');
		}
		
		return true;
	}
}
