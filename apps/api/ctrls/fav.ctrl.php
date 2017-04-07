<?php
/**
 * 收藏api
 */

class FavCtrl extends BaseCtrl {
	

	
	public function favList() {
		
		// 调用测试用例
// 		$this->test_fav_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getFavListCond();
		
		$where = array(
				'appid' => $base['appid'],
				'uid'   => $login_user['uid'],
				'is_del'  => 0,
		);
		$orderby = 'ORDER BY fav_id DESC';
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'fav', 'fav_id');
		
		$fav_list = $pub_mod->getRowList($where, ($plist['from']-1), $plist['count'], $orderby);
// 		dump($fav_list);
		$fav_list2 = array(
				'1' => array(),
				'2' => array(),
				'3' => array(),
		);
		foreach ($fav_list as $fav) {
			$fav_list2[$fav['target_type']][] = $fav;
		}
		
		// 关联社区
		$concat_keys = array(
				'title' => 'target_title',
				'pics' => 'target_cover',
				'content' => 'target_content',
				'is_del'  => 'target_stat',
		);
		$fav_list2["1"] = $list_mod->concatTbl($base['appid'], $fav_list2["1"], 'target_id', 'bbs', 'post', 'post_id', $concat_keys);
		
		// 关联文章
		$concat_keys = array(
				'title' => 'target_title',
				'cover' => 'target_cover',
				'content' => 'target_content',
				'summary' => 'target_summary',
				'stat'  => 'target_stat',
		);
		$fav_list2["2"] = $list_mod->concatTbl($base['appid'], $fav_list2["2"], 'target_id', 'main', 'article', 'article_id', $concat_keys);
		
		// 关联商品
		// @todo
		
		$fav_list2 = array_merge($fav_list2["1"], $fav_list2["2"], $fav_list2["3"]);
		
		// 重新排序$fav_list2
		$fav_list = $list_mod->reorderList($fav_list, $fav_list2, 'fav_id');
		
		// 如果target_cover有多张图片，只去第一张，并格式化输出
		$data = array();
		foreach ($fav_list as $fav) {
			
			if ($fav['target_cover']) {
				
				$ary = explode(',', $fav['target_cover']);
				$fav['target_cover'] = $ary[0];
			}
			
			if ((!isset($fav['target_summary'])) || empty($fav['target_summary'])) {
				$fav['target_summary'] = $fav['target_content'];
			}
				
			$data[] = array(
					'fav_id' => intval($fav['fav_id']),
					'target_type' => intval($fav['target_type']),
					'target_id' => intval($fav['target_id']),
					'target_title' => ap_strval($fav['target_title']),
					'target_cover' => ap_strval($fav['target_cover']),
					'target_summary' => api_make_summary($fav['target_summary'], 30),
					'target_stat' => intval($fav['target_stat']),
					'rt' => date_friendly($fav['rt']),
			);
		}
		
		api_result(0, '获取收藏列表成功', $data);
	}
	
	public function addFav() {
		
		// 调用测试用例
// 		$this->test_add_fav();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'target_id' => array(
						'api_v_numeric|1||收藏ID不合法',
				),
				'target_type' => array(
						'api_v_inarray|1;;2;;3||收藏的目标类型参数不合法'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		/* 取得target_id */
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
			api_result(1, '收藏对象不存在');
		}
		if ($target[$del_key] == 1) {
			api_result(1, '收藏对象已经被删除');
		}
		
		// 判断用户是否已经收藏过了
		$pub_mod->init('bbs', 'fav', 'fav_id');
		
		$where = array(
				'uid' => $login_user['uid'],
				'target_id' => $ipt_list['target_id'],
				'target_type' => $ipt_list['target_type'],
		);
		
		$fav = $pub_mod->getRowWhere($where);
		if ($fav) {
			if ($fav['is_del'] == 0) {
				api_result(0, '之前已经收藏过啦', array('fav_count'=>intval($target['fav_count'])));
			}
			else {
				$update_data = array(
						'is_del' => 0,
						'ut' => time(),
				);
				
				$ret = $pub_mod->updateRow($fav['fav_id'], $update_data);
				
				if (!$ret) {
					api_result(1, '数据库错误，请重试');
				}
			}
		}
		else {
			// 添加收藏
			$pub_mod->init('bbs', 'fav', 'fav_id');
			
			$data = array(
					'fav_id' => get_auto_id(C('AUTOID_FAV')),
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
		
		// 更新fav_count
		$update_data = array(
				'fav_count' => array(1, 'add'),
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
		
		api_result(0, '添加收藏成功', array('fav_count'=>$target['fav_count']+1));
		
	}
	
	public function cancelFav() {
		
		// 调用测试用例
// 		$this->test_cancel_fav();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'target_id' => array(
						'api_v_numeric|1||取消收藏ID不合法',
				),
				'target_type' => array(
						'api_v_inarray|1;;2;;3||取消收藏的目标类型参数不合法'
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
				
		// 判断用户是否已经收藏过了
		$pub_mod->init('bbs', 'fav', 'fav_id');
		
		$where = array(
				'uid' => $login_user['uid'],
				'target_id' => $ipt_list['target_id'],
				'target_type' => $ipt_list['target_type'],
		);
		
		$fav = $pub_mod->getRowWhere($where);
		if (!$fav) {
			api_result(1, '取消收藏的对象不存在');
		}		
		
		if ($fav['is_del'] == 1) {
			api_result(0, '之前就已经取消啦', array('fav_count'=>($target?intval($target['fav_count']):0)));
		}
		
		$update_data = array(
				'is_del' => 1,
				'ut' => time(),
		);
		
		$ret = $pub_mod->updateRow($fav['fav_id'], $update_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		// 更新fav_count
		$update_data = array(
				'fav_count' => array(-1, 'add'),
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
		
		api_result(0, '取消收藏成功', array('fav_count'=>$target?$target['fav_count']-1:0));
	}
}