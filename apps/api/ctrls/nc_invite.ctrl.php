<?php
/**
 * ninecent 邀请api
 */

class NcInviteCtrl extends BaseCtrl {


	public function rebateInfo() {
		
		// 调用测试用例
// 		$this->test_rebate_info();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$data = array(
				'invite_code' => api_get_user_invite_code($login_user['uid']),
				'qrcode'      => C('UPLOAD_DOMAIN').'/user/qrcode.png',
		);
		
		api_result(0, 'succ', $data);
	}
	
	public function rebateList() {
		
		// 调用测试用例
// 		$this->test_rebate_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getPageCond();
		
		$pub_mod = Factory::getMod('pub');
		
		$where = array(
				'uid' => $login_user['uid'],
				'stat' => 0,
		);
		
		$orderby = 'ORDER BY rebate_id DESC';
		
		$pub_mod->init('shop', 'rebate', 'rebate_id');
		$rebate_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $orderby);
		
		$concat_keys = array(
				'nick' => 'rebate_unick',
		);
		$rebate_list = $list_mod->concatTbl($base['appid'], $rebate_list, 'rebate_uid', 'main', 'user', 'uid', $concat_keys);
		
		$data = array();
		foreach ($rebate_list as $rebate) {
			$data[] = array(
					'rebate_id' => intval($rebate['rebate_id']),
					'rebate_uid' => intval($rebate['rebate_uid']),
					'rebate_unick' => ap_strval($rebate['rebate_unick']),
					'pay_money' => sprintf("%.2f", $rebate['pay_money']/100),
					'pay_time' => date_friendly($rebate['pay_time']),
					'rebate_money' => sprintf("%.2f", $rebate['rebate_money']/100),
			);
		}
		
		api_result(0, 'succ', $data);
	}
}