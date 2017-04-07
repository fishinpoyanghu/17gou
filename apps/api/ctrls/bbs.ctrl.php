<?php
/**
 * 微社区api
 * 
 */

class BbsCtrl extends BaseCtrl {
	

	public function sectionList() {
		
		// 调用测试用例
// 		$this->test_section_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 不需要检查登录
		
		$where = array(
				'appid' => $base['appid'],
				'is_del' => 0,
		);
		$orderby = 'ORDER BY `weight` DESC, `class_id` ASC';
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'class', 'class_id');
		
		$section_list = $pub_mod->getRowList($where, 0, 128, $orderby);
		
		$data = array();
		foreach ($section_list as $section) {
			$data[] = array(
					'section_id' => intval($section['class_id']),
					'section_name' => ap_strval($section['class_name']),
			);
		}
		
		api_result(0, 'succ', $data);
	}

	public function postList() {
		
		// 调用测试用例
// 		$this->test_post_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 不需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getPostCond();
		
		// 当是获取某一个用户的帖子的时候，显示只能获取当前登录用户的帖子
// 		if (($plist['uid'] > 0) && ($login_user['uid'] != $plist['uid'])) {
// 			api_result('5', '只能获取自己的帖子列表');
// 		}
		
		$where = array(
				'appid' => $base['appid'],
				'is_del' => 0,
		);
		if (!empty($plist['section_id'])) {
			$where['class_id'] = array($plist['section_id'], 'in');
		}
		if (api_v_notnull($plist['title'])) {
			$where['title'] = array('%'.$plist['title'].'%', 'like');
		}
		
		if ($plist['is_good'] > 0) {
			$where['is_good'] = array($plist['is_good'], '>=');
		}
		
		if ($plist['is_hot'] > 0) {
			$where['is_hot'] = array($plist['is_hot'], '>=');
		}
		
		if ($plist['uid'] > 0) {
			$where['uid'] = $plist['uid'];
		}
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'post', 'post_id');
		
		$post_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $plist['orderby']);
// 		dump($post_list);
		$concat_keys = array(
				'class_name' => 'section_name',
		);
		
		$post_list = $list_mod->concatTbl($base['appid'], $post_list, 'class_id', 'bbs', 'class', 'class_id', $concat_keys);
		
		$concat_keys = array(
				'nick' => 'unick',
				'icon' => 'uicon',
		);
		$post_list = $list_mod->concatTbl($base['appid'], $post_list, 'uid', 'main', 'user', 'uid', $concat_keys);

		$post_list = $list_mod->isFavZan($base['appid'], $login_user?$login_user['uid']:0, $post_list, 'post_id', 1);
		
		$data = array();
		foreach ($post_list as $k=>$post) {
			$data[] = $this->format_post($post);
		}
		api_result(0, 'succ', $data);
	}

	public function post() {
		
		// 调用测试用例
// 		$this->test_post();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 不需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$validate_cfg = array(
				'post_id' => array(
						'api_v_numeric||帖子ID非法',
				),
		);
		
		$plist = api_get_posts($base['appid'], $validate_cfg);
		
		if ($plist['post_id'] < 1) {
			api_result(5, '帖子ID非法');
		}
		
		$where = array(
				'appid' => $base['appid'],
				'post_id' => $plist['post_id'],
				'is_del' => 0,
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'post', 'post_id');
		
		$post = $pub_mod->getRowWhere($where);
		if (!$post) {
			api_result(2, '帖子不存在');
		}
		
		$concat_keys = array(
				'class_name' => 'section_name',
		);
		
		$list_mod = Factory::getMod('list');
		$post_list = $list_mod->concatTbl($base['appid'], array($post), 'class_id', 'bbs', 'class', 'class_id', $concat_keys);
		
		$concat_keys = array(
				'nick' => 'unick',
				'icon' => 'uicon',
		);
		$post_list = $list_mod->concatTbl($base['appid'], array($post), 'uid', 'main', 'user', 'uid', $concat_keys);
		
		$post_list = $list_mod->isFavZan($base['appid'], $login_user?$login_user['uid']:0, $post_list, 'post_id', 1);
		$post_list[0]['pv'] += 1;
		
		$data = $this->format_post($post_list[0]);
		$data['content'] = ap_strval($post_list[0]['content']);
		
		// 增加一次帖子的pv
		api_increase_count('bbs', 'post', 'post_id', $plist['post_id'], 1);
		
		api_result(0, 'succ', $data);
	}

	public function createPost() {
		
		// 调用测试用例
// 		$this->test_create_post();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'section_id' => array(
						'api_v_numeric|1||版块ID不合法',
				),
				'title' => array(
						'api_v_length|0,40||标题长度不能超过40个字',
				),
				'content' => array(
						'api_v_notnull||内容不能为空',
				),
				'pics' => array(						
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$ipt_list['title'] = api_safe_ipt($ipt_list['title']);
// 		$ipt_list['content'] = api_safe_ipt($ipt_list['content']);
		$ipt_list['pics'] = trim(api_safe_ipt($ipt_list['pics']), ',');
				
		// 判断section_id是不是这个app的版块
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'class', 'class_id');
		
		$section = $pub_mod->getRow($ipt_list['section_id']);
		if ((!$section) || ($section['is_del'] == 1) || ($section['appid'] != $base['appid'])) {
			api_result(5, '发表帖子的版块不存在');
		}
		
		// 判断用户是否有权限在这个版块发帖
		// @todo
		
		$data = array(
				'post_id' => get_auto_id(C('AUTOID_POST')),
				'class_id' => $ipt_list['section_id'],
				'uid' => $login_user['uid'],
				'appid' => $base['appid'],
				'title' => $ipt_list['title'],
				'content' => $ipt_list['content'],
				'pics' => $ipt_list['pics'],
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod->init('bbs', 'post', 'post_id');
		$ret = $pub_mod->createRow($data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, '发帖成功', array('post_id'=>intval($data['post_id'])));
	}
	
	public function uploadImgBase64() {
	
		// 调用测试用例
// 		$this->test_upload_img_base64();
	
		// 标准参数检查
		$base = api_check_base();
	
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
	
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$lib_file->setMaxsize(2560);
		$ret = $lib_file->doUploadBase64('file');
		
		if ($ret['code'] != 0) {
			api_result(1, $ret['msg']);
		}
		
		$data = array();
		
		foreach ($ret['data'] as $k=>$v) {
			$data[] = $this->do_cut_img($v['url'], $v['abs_path']);
		}
	
		api_result(0, '图片上传成功', $data);		
	}
	
	public function uploadImg() {
	
		// 调用测试用例
// 		$this->test_upload_img();
	
		// 标准参数检查
		$base = api_check_base();
	
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
	
		$data = $this->do_upload_img('filename');
	
		api_result(0, '图片上传成功', $data);
	}
	
	private function do_upload_img($name) {
		
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$ret = $lib_file->doUploadPic($name);
		
		if ($ret['code'] != 0) {
			api_result(1, $ret['msg']);
		}
		
		// 到这里，表示上传成功啦
		// 开始做图片裁剪
		$data = array();
		
		foreach ($ret['data'] as $k=>$pic) {
				
			$data[] = $this->do_cut_img($pic['url'], $pic['abs_path']);
		}
		
		return $data;
	}
	
	private function do_cut_img($url, $abs_path) {
		
		include_once CORE_ROOT.'/class/util/image/thumb.class.php';
		$thumb = new ThumbHandler();
		
		$iconinfo = api_cut_iconname($url);
		
		$is = getimagesize($abs_path);
		
		$w = $is[0]; // $thumb->getImgWidth($abs_path);
		
		// 裁剪出一个跨度是200的图片
		$thumb->setSrcImg($abs_path);
		$thumb->setImgDisplayQuality(60); // 降低图片质量，从而达到降低图片大小的目的
		$thumb->setDstImg(UPLOAD_PATH.$iconinfo['icon']);
		$thumb->createImg($w>200?floor(20000/$w):100);
		
		$thumb->setSrcImg($abs_path);
		$thumb->setImgDisplayQuality(90);
		$thumb->setDstImg(UPLOAD_PATH.$iconinfo['iconraw']);
		$thumb->createImg($w>640?floor(64000/$w):100);
		
		$data = array(
				'icon' => C('UPLOAD_DOMAIN').$iconinfo['icon'],
				'iconraw' => C('UPLOAD_DOMAIN').$iconinfo['iconraw'],
		);
		
		return $data;
	}
	
	private function format_post($post) {
		
		$icon_info = ap_user_icon_url($post['uicon']);
	
		$ret = array(
				'post_id' => intval($post['post_id']),
				'section_id' => intval($post['class_id']),
				'section_name' => ap_strval($post['section_name']),
				'uid' => intval($post['uid']),
				'unick' => ap_strval($post['unick']),
				'uicon' => ap_strval($icon_info['icon']),
				'title' => ap_strval($post['title']),
				'summary' => api_make_summary($post['content'], 30),
				'pics' => ap_strval($post['pics']),
				'pv' => intval($post['pv']),
				'is_good' => intval($post['is_good']),
				'is_hot' => intval($post['is_hot']),
				'fav_count' => intval($post['fav_count']),
				'zan_count' => intval($post['zan_count']),
				'reply_count' => intval($post['reply_count']),
				'is_fav' => intval($post['is_fav']),
				'is_zan' =>intval($post['is_zan']),
				'rt' => ap_strval(date_friendly($post['rt'])),
		);
	
		return $ret;
	}
}
