<?php
/**
 * 编辑器文件上传类
 */

class EditorUploadCtrl extends BaseCtrl {
	

	
	public function uploadAppIcon() {
		
		// 调用测试用例
// 		$this->test_upload_app_icon();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$data = $this->do_upload($app, $login_user, 'file', 144, 144);
		
		api_result(0, 'succ', $data);
	}
	
	public function uploadLoadingImg() {
		
		// 调用测试用例
// 		$this->test_upload_loading_img();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$data = $this->do_upload($app, $login_user, 'file', 480, 800);
		
		// 保存到 我的图片
		// @todo
		
		api_result(0, 'succ', $data);
	}
	
	private function do_upload($app, $login_user, $key, $w, $h) {
		
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$lib_file->setMaxsize(2560);
		$ret = $lib_file->doUploadPic('file');
		
		if ($ret['code'] != 0) {
			api_result(1, $ret['msg']);
		}
		
		$ret['data'] = $ret['data'][0];
		
		// 到这里，表示上传成功啦
		// 开始做图片裁剪
		$iconinfo = api_cut_iconname($ret['data']['url']);
		
		include_once CORE_ROOT.'/class/util/image/thumb.class.php';
		$thumb = new ThumbHandler();
		$thumb->setSrcImg($ret['data']['abs_path']);
		$thumb->setCutType(1);
		$thumb->setDstImg(UPLOAD_PATH.$iconinfo['icon']);
		$thumb->createImg($w, $h);
		
		$data = array(
				'icon' => C('UPLOAD_DOMAIN').$iconinfo['icon'],
		);
		
		// 保存到 我的图片
		$data_pic_add = array(
				'pic_id' => get_auto_id(C('AUTOID_PIC')),
				'appid' => $app['appid'],
				'uid' => $login_user['uid'],
				'pic_category_id' => 0,
				'name' => $ret['src_name'],
				'url' => $iconinfo['icon'],
				'size' => $ret[w].'x'.$ret['h'],
				'filesize' => $ret['filesize'],
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('admin', 'pic', 'pic_id');
		
		$pub_mod->createRow($data_pic_add);
		
		return $data;
	}
}
