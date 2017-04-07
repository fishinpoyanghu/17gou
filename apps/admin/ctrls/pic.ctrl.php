<?php
/**
 * 表管理 ctrl
 */

class PicCtrl extends BaseCtrl {
	
	public function tree() {
		$data = array();
		Factory::getView("table/editor", $data);
	}
	
	public function pasteUpload() {
		
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$lib_file->setMaxsize(2048);
		$ret = $lib_file->doUploadBase64('wangEditorPasteFile');
		
		if ($ret['code'] == 0) {
			
			$ret['data'] = $ret['data'][0];
			
			$cut_res = $this->do_cut_img($ret['data']['url'], $ret['data']['abs_path']);
			
			echo C('UPLOAD_DOMAIN').$cut_res['iconraw'];
		}
	}
	
	public function editorAssist() {
		
		Factory::getView("table/editor_assist", array());
	}
	
	public function editorAssistPage() {
		
		header('Content-Type:text/html;charset=utf-8 ');
		
		$src = '/?c=pic&a=editor_assist';
		
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$lib_file->setMaxsize(2048);
		$ret = $lib_file->doUploadPic('wangEditor_uploadImg');
		
		$url = '';
		if ($ret['code'] == 0) {
			
			$url = C('UPLOAD_DOMAIN').$ret['data'][0]['url'];
			
			$src .= '#ok|'.$url;
		}
		else {
			$src .= '#'.$ret['msg'];
		}
		
		echo '<iframe src="'.$src.'"></iframe>';
	}

	public function index() {
		
		$ret = array(
				'a' => array(
						'-1' => '全部'
				)
		);
		
		$ret['a'][2] = '2';
		$ret['a'][3] = '3';
		$ret['a'][4] = '4';
		
		$a = json_encode($ret);
		$b = json_decode($a, true);
 
		
		$page = gint('page');
		$kw = gstr('kw');
		$cids = gstr('cids');
				
		// 传输给view的数据
		$data = array(
				'global_cfg' => array('nav1'=>'app', 'nav2'=>'pic'),
		);
// 		echo strrchr("I love Shanghai!","Sh");exit;
// 		do_cache('set', 'pic', '12345', array('name'=>'days'), 'admin');
		dump(do_cache('get', 'pic', '22YXNT0hlz'));
		exit;
		Factory::getView("table/pic", $data);
	}
	
	public function upload() {
		
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$ret = $lib_file->doUploadPic('upfile');
		dump($ret);
		
		dump($_FILES);
		$file = $_FILES['upfile'];
		$pinfo=pathinfo($file["name"]);
		$ftype=$pinfo['extension'];
		dump($pinfo);
		$image_size = getimagesize($file['tmp_name']);
		dump($image_size);
	}
	
	/**
	 * Ajax获取图片列表
	 */
	public function getPicList() {
		
		$login_user = app_get_login_user(2, 2);
		
		// 设置默认分页大小
		$pagesize = 24;
		
		$tbl = gstr('tbl');
		
		$t_mod = Factory::getMod('table');
		
		/* data数据接口：
			array(
				name:$name, // mypic/material/app_nav
				active:(
					[style:$style],
					[color:$color],
					classify:$classify, // 分类目录ID，-1表示全部
				),
				page:$page, // 分页的当前页，0表示第一页
			)
		*/
		$data = pstr('data');
		$data = json_decode($data, true);
		
		if (!is_array($data)) {
			echo_result(1, '非法参数'.pstr('data'));
		}
		
		if ($data['page'] < 1) $data['page'] = 1;
		
		// 我的图片
		if ($data['name'] == 'mypic') {
			
			$tbl = 'pic';
			if (!$t_mod->tryLoadTable($tbl)) {
				echo_result(1, '非法的数据库表数据，请联系管理员');
			}
			
			$base_cfg = $t_mod->getBaseCfg();
			
			$where = array(
					'stat' => 0,
			);
			
			if ($data['active']['classify'] != '-1') $where['pic_category_id'] = str_replace('c', '', $data['active']['classify']);
						
			// 如果有 appid_required
			if ($base_cfg['appid_required']) {
				$where['appid'] = $login_user['appid'];
			}
			
			// 取得图片列表
			$pic_list = $t_mod->getTableListSimple($where, ' ORDER BY pic_id DESC', ($data['page']-1)*$pagesize, $pagesize);
			
			// 取得图片总数
			$pic_total = $t_mod->getTableCount($where);
			
			$t_mod->closeTable();
			
			// 取得所有的图片目录列表
			$t_mod->tryLoadTable('pic_category');
			$base_cfg = $t_mod->getBaseCfg();
			
			$where = array(
					'stat' => 0,
			);
			
			// 如果有 appid_required
			if ($base_cfg['appid_required']) {
				$where['appid'] = $login_user['appid'];
			}
			
			$category_list = $t_mod->getTableListSimple($where, ' ORDER BY pic_category_id DESC', 0, 1024);
			
			$data_ret = array(
					'active' => $data['active'],
					'classify' => array(
							'-1' => '全部',
					),
					'pics' => array(),
					'page' => $data['page'],
					'picnum' => $pic_total,
			);
			
			foreach ($category_list as $category) {
				// json_encode在某种情况下会乱序，key加个c仅仅是为了避免乱序
				$data_ret['classify']['c'.$category['pic_category_id']] = $category['name'];
			}
			
			foreach ($pic_list as $pic) {
				$data_ret['pics'][] = array(
						'url' => C('UPLOAD_DOMAIN').$pic['url'],
						'size' => $pic['size'],
						'is_font' => false,
				);
			}
			
			echo_result(0, 'succ', $data_ret);
		}
		
		// 素材库
		if ($data['name'] == 'material') {
			
			$tbl = 'pic_material';
			if (!$t_mod->tryLoadTable($tbl)) {
				echo_result(1, '非法的数据库表数据，请联系管理员');
			}
				
			$where = array(
					'stat' => 0,
			);
				
			if ($data['active']['classify'] != '-1') $where['pic_category_id'] = str_replace('c', '', $data['active']['classify']);
			if ($data['active']['style'] != '-1') $where['style'] = str_replace('c', '', $data['active']['style']);
			if ($data['active']['color'] != '-1') $where['color'] = str_replace('c', '', $data['active']['color']);
				
			// 取得图片列表
			$pic_list = $t_mod->getTableListSimple($where, ' ORDER BY pic_category_id ASC, pic_material_id DESC', ($data['page']-1)*$pagesize, $pagesize);
				
			// 取得图片总数
			$pic_total = $t_mod->getTableCount($where);
				
			// 取得所有的图片目录列表
			$conf = include get_app_root().'/conf/material.conf.php';
			$data_ret = array(
					'active' => $data['active'],
					'style' => $conf['material']['style'],
					'color' => $conf['material']['color'],
					'classify' => $conf['material']['classify'],
					'pics' => array(),
					'page' => $data['page'],
					'picnum' => $pic_total,
			);
			
			foreach ($pic_list as $pic) {
				$data_ret['pics'][] = array(
						'url' => C('UPLOAD_DOMAIN').'material/'.$pic['url'],
						'size' => '', //$pic['size'],
						'is_font' => false,
				);
			}
				
			echo_result(0, 'succ', $data_ret);
		}
		
		// 素材库
		if ($data['name'] == 'app_nav') {
				
			$tbl = 'pic_app_nav';
			if (!$t_mod->tryLoadTable($tbl)) {
				echo_result(1, '非法的数据库表数据，请联系管理员');
			}
		
			$where = array(
					'stat' => 0,
			);
		
			if ($data['active']['classify'] != '-1') $where['pic_category_id'] = str_replace('c', '', $data['active']['classify']);
					
			// 取得图片列表
			$pic_list = $t_mod->getTableListSimple($where, ' ORDER BY pic_category_id ASC, pic_app_nav_id DESC', ($data['page']-1)*$pagesize, $pagesize);
		
			// 取得图片总数
			$pic_total = $t_mod->getTableCount($where);
		
			// 取得所有的图片目录列表
			$conf = include get_app_root().'/conf/material.conf.php';
			$data_ret = array(
					'active' => $data['active'],
					'classify' => $conf['app_nav'],
					'pics' => array(),
					'page' => $data['page'],
					'picnum' => $pic_total,
			);
				
			foreach ($pic_list as $pic) {
				$data_ret['pics'][] = array(
						'url' => C('UPLOAD_DOMAIN').'app_nav/'.$pic['url'],
						'size' => '', //$pic['size'],
						'is_font' => false,
				);
			}
		
			echo_result(0, 'succ', $data_ret);
		}
		
		// 素材库
		if ($data['name'] == 'icon_font') {
		
			$tbl = 'icon.font';
			if (!$t_mod->tryLoadTable($tbl)) {
				echo_result(1, '非法的数据库表数据，请联系管理员');
			}
		
			$where = array(
					'stat' => 0,
			);
		
			if ($data['active']['classify'] != '-1') $where['pic_category_id'] = str_replace('c', '', $data['active']['classify']);
				
			// 取得图片列表
			$pic_list = $t_mod->getTableListSimple($where, ' ORDER BY pic_category_id ASC, icon_font_id DESC', ($data['page']-1)*$pagesize, $pagesize);
		
			// 取得图片总数
			$pic_total = $t_mod->getTableCount($where);
		
			// 取得所有的图片目录列表
			$conf = include get_app_root().'/conf/material.conf.php';
			$data_ret = array(
					'active' => $data['active'],
					'classify' => $conf['icon_font'],
					'pics' => array(),
					'page' => $data['page'],
					'picnum' => $pic_total,
			);
		
			foreach ($pic_list as $pic) {
				$data_ret['pics'][] = array(
						'url' => $pic['class'],
						'size' => '', //$pic['size'],
						'is_font' => true,
				);
			}
		
			echo_result(0, 'succ', $data_ret);
		}
	}
	
	/**
	 * 上传图片，并返回图片的url等信息
	 */
	public function getUrl() {
		
		include_once COMMON_PATH.'libs/LibFile.php';
		$size = gstr('size');
		$lib_file = new LibFile();
		$lib_file->setMaxsize(2048);
		$uploadpath = gstr('uploadpath'); 
		$path='';
		if($uploadpath=='team'){
			$path='team/';
		}
		 
		$ret = $lib_file->doUploadPic('filename',$size,$path);
	 
		if ($ret['code'] == 0) {
			$ret['data'] = $ret['data'][0];
			
			$cut_res = $this->do_cut_img($ret['data']['url'], $ret['data']['abs_path']);
//			$ret['data']['url'] = $cut_res['icon'];
			
			$file = file_get_contents($cut_res['abs_icon']);
			$base64 = 'data:'.$ret['data']['type']. ';base64,'.base64_encode($file);
			
			$cache_id = $ret['data']['dest_name'];
			
			$data = array(
					'id' => $cache_id,
					'base64' => $base64,
			);
			
			// 把成功上传的图片的信息暂时存在缓存里，用户如果点击“确定上传”, 可以从里面读取
			do_cache('set', 'pic', $cache_id, $ret['data']);
			
			echo_result(0, 'succ', $data);
		}
		else {
			echo_result($ret['code'], $ret['msg']);
		}
	}
	
	/**
	 * 保存图片信息
	 */
	public function savePic() {
		
		$login_user = app_get_login_user(2, 2);
		
		$ids = gstr('ids');
		$cids = gstr('cids');
		$tbl = gstr('tbl');
		
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			echo_result(1, '非法的数据库表数据，请联系管理员');
		}
		
		$base_cfg = $t_mod->getBaseCfg();
		
		// 取最后一个cid
		if ($cids) {
			$cids = explode(',', $cids);
			$cids = intval($cids[count($cids)-1]);
		}
		else {
			$cids = 0;
		}
		
		// 这里，要判断cid的合法性，例如是否存在，是否是当前用户自己归属的cid
		// @todo
		
		if (!$ids) {
			echo_result(1, '请先上传');
		}
		$ids = explode(',', $ids);
		
		$ret = array();
		
		foreach ($ids as $id) {
			
			if (!$id) continue;
			
			$info = do_cache('get', 'pic', $id);
						
			if (empty($info)) continue;
			
			$data = array(
					'pic_id' => get_auto_id(C('AUTOID_PIC')),
					'pic_category_id' => $cids,
					'name' => $info['src_name'],
					'url' => $info['url'],
					'size' => $info['w'].'x'.$info['h'],
					'filesize' => $info['filesize'],
					'rt' => time(),
					'ut' => time(),
			);

			
			// 如果appid_required是true
			if ($base_cfg['appid_required']) {
				$data['appid'] = $login_user['appid'];
			}
			
			$res = $t_mod->createTableRow($data);
			if ($res) {
				do_cache('delete', 'pic', $id);
				$ret[] = C('UPLOAD_DOMAIN').$info['url'];
			}
		}
		
		if (count($ret) == 0) {
			echo_result(1, '数据库错误，图片保存失败');
		}
		
		echo_result(0, 'succ', $ret);
	}
	
	private function do_cut_img($url, $abs_path) {
	
		include_once CORE_ROOT.'/class/util/image/thumb.class.php';
		$thumb = new ThumbHandler();
	
		$iconinfo = $this->get_cut_iconname($url);
	
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
				'icon' => $iconinfo['icon'],
				'iconraw' => $iconinfo['iconraw'],
				'abs_icon' => UPLOAD_PATH.$iconinfo['icon'],
				'abs_iconraw' => UPLOAD_PATH.$iconinfo['iconraw'],
		);
	
		return $data;
	}
	
	/**
	 * 通过$url得到图片要被裁剪的2个大小的路径
	 * 
	 * @param string $url
	 * @return array
	 */
	private function get_cut_iconname($url) {
		
		$pinfo = pathinfo($url);
		
		return array(
				'icon' => $pinfo['dirname'].'/'.$pinfo['filename'].'_n'.'.'.$pinfo['extension'],
				'iconraw' => $pinfo['dirname'].'/'.$pinfo['filename'].'_n_big'.'.'.$pinfo['extension'],
		);
	}
}
