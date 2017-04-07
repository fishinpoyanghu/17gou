<?php
/**
 * 赞api
 */

class ZanCtrl extends BaseCtrl {
	
	public function addZan() {
		
		// 调用测试用例
// 		$this->test_add_zan();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'target_id' => array(
						'api_v_numeric|1||赞ID不合法',
				),
				'target_type' => array(
						'api_v_inarray|1;;2;;3||赞的目标类型参数不合法'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		/* 取得target */
		$target = false;
		$del_key = 'stat';
		$target_name = 'name';
		$where = array(
				'appid' => $base['appid'],
		);
		
		// 社区
		if ($ipt_list['target_type'] == 1) {
			$pub_mod->init('bbs', 'post', 'post_id');
			$where['post_id'] = $ipt_list['target_id'];
			
			$target = $pub_mod->getRowWhere($where);
			$del_key = 'is_del';
			$target_name = 'title';
		}
		// 文章
		elseif ($ipt_list['target_type'] == 2) {
			$pub_mod->init('main', 'article', 'article_id');
			$where['article_id'] = $ipt_list['target_id'];
			
			$target = $pub_mod->getRowWhere($where);
			$target_name = 'title';
		}
		// 商品
		elseif ($ipt_list['target_type'] == 3) {
			// @todo
			
		}
		
		if (!$target) {
			api_result(1, '点赞对象不存在');
		}
		if ($target[$del_key] == 1) {
			api_result(1, '点赞对象已经被删除');
		}
		
		// 判断用户是否已经赞过了
		$pub_mod->init('bbs', 'zan', 'zan_id');
		
		$where = array(
				'uid' => $login_user['uid'],
				'target_id' => $ipt_list['target_id'],
				'target_type' => $ipt_list['target_type'],
		);
		
		$zan = $pub_mod->getRowWhere($where);
		if ($zan) {
			if ($zan['is_del'] == 0) {
				api_result(0, '之前已经点赞过啦', array('zan_count'=>intval($target['zan_count'])));
			}
			else {
				$update_data = array(
						'is_del' => 0,
						'ut' => time(),
				);
				
				$ret = $pub_mod->updateRow($zan['zan_id'], $update_data);
				
				if (!$ret) {
					api_result(1, '数据库错误，请重试');
				}
			}
		}
		else {
			// 添加赞
			$pub_mod->init('bbs', 'zan', 'zan_id');
			
			$data = array(
					'zan_id' => get_auto_id(C('AUTOID_ZAN')),
					'appid' => $base['appid'],
					'uid' => $login_user['uid'],
					'target_id' => $ipt_list['target_id'],
					'target_type' => $ipt_list['target_type'],
					'target_title' => $target[$target_name],
					'rt' => time(),
					'ut' => time(),
			);
			
			$ret = $pub_mod->createRow($data);
			if (!$ret) {
				api_result(1, '数据库错误，请重试');
			}
		}
		
		// 更新zan_count
		$update_data = array(
				'zan_count' => array(1, 'add'),
				'ut' => time(),
		);
		$ret = false;
		// 社区
		if ($ipt_list['target_type'] == 1) {
			$pub_mod->init('bbs', 'post', 'post_id');
		}
		// 文章
		elseif ($ipt_list['target_type'] == 2) {
			$pub_mod->init('main', 'article', 'article_id');
		}
		// 商品
		elseif ($ipt_list['target_type'] == 3) {
			// @todo
				
		}
		
		$ret = $pub_mod->updateRow($ipt_list['target_id'], $update_data);
		
		$msg_mod = Factory::getMod('msg');
		
		// 如果是社区，通知作者，被赞了
		if (($ipt_list['target_type'] == 1) && ($target['uid'] != $login_user['uid'])) {
			$msg_mod->sendNotify($base['appid'], $target['uid'], $login_user['uid'], 2, $ipt_list['target_id'], $ipt_list['target_type'], '');
		}
		
		api_result(0, '点赞成功', array('zan_count'=>$target['zan_count']+1));
		
	}
	
	public function cancelZan() {
		
		// 调用测试用例
// 		$this->test_cancel_zan();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'target_id' => array(
						'api_v_numeric|1||取消赞ID不合法',
				),
				'target_type' => array(
						'api_v_inarray|1;;2;;3||取消赞的目标类型参数不合法'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		/* 取得target */
		$target = false;
		$where = array(
				'appid' => $base['appid'],
		);
		
		// 社区
		if ($ipt_list['target_type'] == 1) {
			$pub_mod->init('bbs', 'post', 'post_id');
			$where['post_id'] = $ipt_list['target_id'];
				
			$target = $pub_mod->getRowWhere($where);
		}
		// 文章
		elseif ($ipt_list['target_type'] == 2) {
			$pub_mod->init('main', 'article', 'article_id');
			$where['article_id'] = $ipt_list['target_id'];
				
			$target = $pub_mod->getRowWhere($where);
		}
		// 商品
		elseif ($ipt_list['target_type'] == 3) {
			// @todo
				
		}
				
		// 判断用户是否已经赞过了
		$pub_mod->init('bbs', 'zan', 'zan_id');
		
		$where = array(
				'uid' => $login_user['uid'],
				'target_id' => $ipt_list['target_id'],
				'target_type' => $ipt_list['target_type'],
		);
		
		$zan = $pub_mod->getRowWhere($where);
		if (!$zan) {
			api_result(1, '取消赞的对象不存在');
		}		
		
		if ($zan['is_del'] == 1) {
			api_result(0, '之前就已经取消啦', array('zan_count'=>($target?intval($target['zan_count']):0)));
		}
		
		$update_data = array(
				'is_del' => 1,
				'ut' => time(),
		);
		
		$ret = $pub_mod->updateRow($zan['zan_id'], $update_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		// 更新zan_count
		$update_data = array(
				'zan_count' => array(-1, 'add'),
				'ut' => time(),
		);
		$ret = false;
		// 社区
		if ($ipt_list['target_type'] == 1) {
			$pub_mod->init('bbs', 'post', 'post_id');
		}
		// 文章
		elseif ($ipt_list['target_type'] == 2) {
			$pub_mod->init('main', 'article', 'article_id');
		}
		// 商品
		elseif ($ipt_list['target_type'] == 3) {
			// @todo				
		}
		
		$ret = $pub_mod->updateRow($ipt_list['target_id'], $update_data);
		
		api_result(0, '取消赞成功', array('zan_count'=>$target?$target['zan_count']-1:0));
	}
}