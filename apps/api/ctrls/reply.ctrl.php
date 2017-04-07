<?php
/**
 * 评论api
 */

class ReplyCtrl extends BaseCtrl {
	

	public function replyList() {
		
		// 调用测试用例
// 		$this->test_reply_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 不需要检查登录
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getReplyCond();
		
		$where = array(
				'appid' => $base['appid'],
				'is_del' => 0,
		);
		if ($plist['target_type'] > 0) {
			$where['target_type'] = $plist['target_type'];
		}
		if ($plist['target_id'] > 0) {
			$where['target_id'] = $plist['target_id'];
		}
		if ($plist['uid'] > 0) {
			$where['uid'] = $plist['uid'];
		}
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'reply', 'reply_id');
		
		$reply_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $plist['orderby']);
		
		$concat_keys = array(
				'nick' => 'unick',
				'icon' => 'uicon',
		);		
		$reply_list = $list_mod->concatTbl($base['appid'], $reply_list, 'uid', 'main', 'user', 'uid', $concat_keys);
		
		$reply_list2 = array(
				'1' => array(),
				'2' => array(),
				'3' => array(),
				'4' => array(),
		);
		foreach ($reply_list as $reply) {
			$reply_list2[$reply['target_type']][] = $reply;
		}
		
		// 关联社区
		$concat_keys = array(
				'title' => 'target_title',
				'pics' => 'target_cover',
				'is_del'  => 'target_stat',
		);
		$reply_list2["1"] = $list_mod->concatTbl($base['appid'], $reply_list2["1"], 'target_id', 'bbs', 'post', 'post_id', $concat_keys);
		
		// 关联文章
		$concat_keys = array(
				'title' => 'target_title',
				'cover' => 'target_cover',
				'stat'  => 'target_stat',
		);
		$reply_list2["2"] = $list_mod->concatTbl($base['appid'], $reply_list2["2"], 'target_id', 'main', 'article', 'article_id', $concat_keys);
		
		// 关联商品
		// @todo
		
		// 关联表单
		$concat_keys = array(
				'title' => 'target_title',
				'cover' => 'target_cover',
				'stat'  => 'target_stat',
		);
		$reply_list2["4"] = $list_mod->concatTbl($base['appid'], $reply_list2["4"], 'target_id', 'main', 'form', 'form_id', $concat_keys);
		
		$reply_list2 = array_merge($reply_list2["1"], $reply_list2["2"], $reply_list2["3"], $reply_list2["4"]);
		
		// 重新排序$reply_list2
		$reply_list = $list_mod->reorderList($reply_list, $reply_list2, 'reply_id');
		
		// 如果target_cover有多张图片，只去第一张，并格式化输出
		$data = array();
		foreach ($reply_list as $reply) {
			
			if ($reply['title_cover']) {
				$ary = explode(',', $reply['title_cover']);
				$reply['title_cover'] = $ary[0];
			}
			
			$data[] = $this->format_reply($reply);
		}
		
		api_result(0, 'succ', $data);
	}
	
	public function addReply() {
		
		// 调用测试用例
// 		$this->test_add_reply();
		
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
						'api_v_inarray|1;;2;;3;;4||target_type参数不合法'
				),
				'content' => array(
						'api_v_notnull||回复内容不能为空'
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list['content'] = api_safe_ipt($ipt_list['content']);
		
		// 防止评论重复提交
		$cache_info = do_cache('get', 'reply-repeat', $login_user['uid'].'-'.$ipt_list['target_id']);
		if ($cache_info && ($cache_info == md5($ipt_list['content']))) {
			api_result(1, '不能重复提交同样内容的评论');
		}		
		
		$pub_mod = Factory::getMod('pub');
		
		/* 取得target */
		$target = false;
		$del_key = 'stat';
		$where = array(
				'appid' => $base['appid'],
		);
		
		// 社区
		if ($ipt_list['target_type'] == 1) {
			$pub_mod->init('bbs', 'post', 'post_id');
			$where['post_id'] = $ipt_list['target_id'];
				
			$target = $pub_mod->getRowWhere($where);
			$del_key = 'is_del';
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
		// 表单
		elseif ($ipt_list['target_type'] == 4) {
			$pub_mod->init('main', 'form', 'form_id');
			$where['form_id'] = $ipt_list['target_id'];
				
			$target = $pub_mod->getRowWhere($where);
		}
		
		if (!$target) {
			api_result(1, '评论的对象不存在');
		}
		if ($target[$del_key] == 1) {
			api_result(1, '评论的对象已经被删除');
		}
		
		// 判断p_uid是否是评论过target_id的用户，如果不是，说明是非法操作
		// @todo
		
		// 判断当前用户有没有评论这篇帖子的权限
		// @todo
		
		// 到这里，表示可以发表评论了
		$data = array(
				'reply_id' => get_auto_id(C('AUTOID_REPLY')),
				'uid' => $login_user['uid'],
				'p_uid' => $ipt_list['p_uid'],
				'appid' => $base['appid'],
				'target_id' => $ipt_list['target_id'],
				'target_type' => $ipt_list['target_type'],
				'content' => $ipt_list['content'],
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod->init('bbs', 'reply', 'reply_id');
		$ret = $pub_mod->createRow($data);
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		// 添加缓存
		do_cache('set', 'reply-repeat', $login_user['uid'].'-'.$ipt_list['target_id'], md5($ipt_list['content']));
		
		// 更新target_id的reply_count
		$update_data = array(
				'reply_count' => array(1, 'add'),
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
		// 表单
		elseif ($ipt_list['target_type'] == 4) {
			$pub_mod->init('main', 'form', 'form_id');
		}
		
		$ret = $pub_mod->updateRow($ipt_list['target_id'], $update_data);
		
		$msg_mod = Factory::getMod('msg');
		
		// 如果是社区，通知作者，被评论了
		if (($ipt_list['target_type'] == 1) && ($target['uid'] != $login_user['uid'])) {			
			$msg_mod->sendNotify($base['appid'], $target['uid'], $login_user['uid'], 1, $ipt_list['target_id'], $ipt_list['target_type'], $ipt_list['content']);
		}
		// 如果是评论的评论，通知被评论者
		if ($ipt_list['p_uid'] > 0) {
			
			if (($ipt_list['target_type'] == 1) && ($target['uid'] != $login_user['uid']) && ($login_user['uid'] == $ipt_list['p_uid'])) {
				// 社区，且作者不是当前登录用户，且帖子的作者和被回复的uid不是同一个人
				// 这种情况不通知
			}
			else {
				$msg_mod->sendNotify($base['appid'], $ipt_list['p_uid'], $login_user['uid'], 1, $ipt_list['target_id'], $ipt_list['target_type'], $ipt_list['content']);
			}
		}
		
		api_result(0, '评论成功', array('reply_id'=>$data['reply_id'], 'reply_count'=>$target['reply_count']+1));
	}
	
	private function format_reply($reply) {
		
		$icon_info = ap_user_icon_url($reply['uicon']);
		
		$ret = array(
				'reply_id' => intval($reply['reply_id']),
				'target_type' => intval($reply['target_type']),
				'target_id' => intval($reply['target_id']),
				'target_title' => ap_strval($reply['target_title']),
				'target_cover' => ap_strval($reply['target_cover']),
				'target_stat' => intval($reply['target_stat']),
				'content' => ap_strval($reply['content']),
				'uid' => intval($reply['uid']),
				'unick' => ap_strval($reply['unick']),
				'uicon' => ap_strval($icon_info['icon']),
				'rt' => ap_strval(date_friendly($reply['rt'])),
		);
		
		return $ret;
	}
}