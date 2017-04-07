<?php
/**
 * 编辑器文章类
 */

class EditorListCtrl extends BaseCtrl {


	
	public function articleClassList() {
		
		// 调用测试用例
// 		$this->test_article_class_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 不需要检查登录
		
		$appid = pint('appid');
		if (!$appid) {
			api_result(0, 'appid错误');
		}
		
		$where = array(
				'appid' => $appid,
				'stat' => 0,
		);
		$orderby = 'ORDER BY article_category_id ASC';
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'article_category', 'article_category_id');
		
		$section_list = $pub_mod->getRowList($where, 0, 128, $orderby);
		
		$data = array();
		foreach ($section_list as $section) {
			$data[] = array(
					'key' => intval($section['article_category_id']),
					'name' => ap_strval($section['name']),
			);
		}
		
		api_result(0, 'succ', $data);
	}
	
	public function articleList() {

		// 调用测试用例
// 		$this->test_article_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getEditorArticleListCond();
		
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		if ((!empty($plist['cid'])) && $plist['cid']) {
			$where['cid'] = $plist['cid'];
		}
		if (api_v_notnull($plist['title'])) {
			$where['title'] = array('%'.$plist['title'].'%', 'like');
		}
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'article', 'article_id');
		
		$total = $pub_mod->getRowTotal($where);
		
		$article_list = $pub_mod->getRowList($where, ($plist['page']-1)*$plist['pagesize'], $plist['pagesize'], $plist['orderby']);
		
		$concat_keys = array(
				'name' => 'cname',
		);
		
		$article_list = $list_mod->concatTbl($app['appid'], $article_list, 'cid', 'main', 'article_category', 'article_category_id', $concat_keys);
		
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		
		$pub_mod->init('main', 'article_category', 'article_category_id');
		$class_list = $pub_mod->getRowList($where, 0, 1024);
		
		$data = array(
				'page' => intval($plist['page']),
				'pagesize' => intval($plist['pagesize']),
				'total' => intval($total),
				'page_total' => intval(ceil($total/$plist['pagesize'])),
				'article_list' => array(),
				'class_list' => array(
						array(
								'key' => 0,
								'name' => '全部',
						),
				),
		);
		foreach ($article_list as $article) {
			$data['article_list'][] = $this->format_article($article);
		}
		
		foreach ($class_list as $class) {
			
			$data['class_list'][] = array(
					'key' => intval($class['article_category_id']),
					'name' => ap_strval($class['name']),
			);
		}
		
		api_result(0, 'succ', $data);
	}
	
	public function pageList() {

		// 调用测试用例
// 		$this->test_page_list();
				
		// 标准参数检查
		$base = api_check_base();
		
		// 需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getEditorPageListCond();
		
		$where = array(
				'appid' => $app['appid'],
				'stat' => 0,
		);
		if (api_v_notnull($plist['name'])) {
			$where['name'] = array('%'.$plist['name'].'%', 'like');
		}
		
		$orderby = 'ORDER BY app_page_id ASC';
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('admin', 'app_page', 'app_page_id');
		
		$total = $pub_mod->getRowTotal($where);
		
		$page_list = $pub_mod->getRowList($where, ($plist['page']-1)*$plist['pagesize'], $plist['pagesize'], $orderby);
		
		$data = array(
				'page' => intval($plist['page']),
				'pagesize' => intval($plist['pagesize']),
				'total' => intval($total),
				'page_total' => intval(ceil($total/$plist['pagesize'])),
				'page_list' => array(),
		);
		
		foreach ($page_list as $page) {
			$data['page_list'][] = array(
					'page_id' => intval($page['app_page_id']),
					'name' => ap_strval($page['name']),
					'rt' => date_friendly($page['rt']),
			);
		}
		
		api_result(0, 'succ', $data);
	}
	
	public function picList() {
		
		// 调用测试用例
// 		$this->test_pic_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$app = api_get_curr_app($login_user['uid']);
		if (!$app) {
			api_result(2, 'appid错误');
		}
		
		$validate_cfg = array(
				'type' => array(
						'api_v_inarray|lib_loading;;lib_icons;;mypic||type不合法',
				),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getEditorPicListCond();
		
		$pub_mod = Factory::getMod('pub');
		
		$data = array(
				'page' => intval($plist['page']),
				'pagesize' => intval($plist['pagesize']),
				'total' => 0, //intval($total),
				'page_total' => 0, // intval(ceil($total/$plist['pagesize'])),
				'pic_list' => array(),
				'class_list' => array(),
		);
		
		// 如果是 启动/引导 图片库
		if ($ipt_list['type'] == 'lib_loading') {
			
			$where = array(
					'type' => 'lib_loading',
					'stat' => 0,
			);
			$orderby = 'ORDER BY piclib_id DESC';
			
			$pub_mod->init('admin', 'piclib', 'piclib_id');
			
			$data['total'] = $pub_mod->getRowTotal($where);
			
			$pic_list = $pub_mod->getRowList($where, ($plist['page']-1)*$plist['pagesize'], $plist['pagesize'], $orderby);
			
			foreach ($pic_list as $pic) {
				$info = api_get_pic_allpath($pic['url']);
				$data['pic_list'][] = array(
						'pic_id' => intval($pic['piclib_id']),
						'url' => $info['url'],
						'urlraw' => $info['urlraw'],
						'size' => ap_strval($pic['size']),
				);
			}
		}
		// 如果是 导航图片库
		elseif ($ipt_list['type'] == 'lib_icons') {
			
			$where = array(
					'stat' => 0,
			);
			if ($plist['cid'] > 0) {
				$where['pic_category_id'] = $plist['cid'];
			}
			
			$orderby = 'ORDER BY pic_category_id ASC, pic_app_nav_id DESC';
			
			$pub_mod->init('admin', 'pic_app_nav', 'pic_app_nav_id');
			
			$data['total'] = $pub_mod->getRowTotal($where);
			
			$pic_list = $pub_mod->getRowList($where, ($plist['page']-1)*$plist['pagesize'], $plist['pagesize'], $orderby);
			
			foreach ($pic_list as $pic) {
				$data['pic_list'][] = array(
						'pic_id' => intval($pic['pic_app_nav_id']),
						'url' => C('UPLOAD_DOMAIN').'app_nav/'.$pic['url'],
						'urlraw' => C('UPLOAD_DOMAIN').'app_nav/'.$pic['url'],
						'size' => ap_strval($pic['size']),
				);
			}
			
			// 取得所有的图片目录列表
			$conf = include get_app_root().'/conf/material.conf.php';
			
			foreach ($conf['lib_icons'] as $key=>$val) {
				$data['class_list'][] = array(
						'key' => intval($key),
						'name' => ap_strval($val),
				);
			}
		}
		// 如果是 我的图片 
		elseif ($ipt_list['type'] == 'mypic') {
			
			$where = array(
					'appid' => $app['appid'],
					'uid' => $login_user['uid'],
					'stat' => 0,
			);
			if ($plist['cid'] > 0) {
				$where['pic_category_id'] = $plist['cid'];
			}
			
			$orderby = 'ORDER BY pic_id DESC';
			
			$pub_mod->init('admin', 'pic', 'pic_id');
			
			$data['total'] = $pub_mod->getRowTotal($where);
				
			$pic_list = $pub_mod->getRowList($where, ($plist['page']-1)*$plist['pagesize'], $plist['pagesize'], $orderby);
			
			foreach ($pic_list as $pic) {
				$info = api_get_pic_allpath($pic['url']);
				$data['pic_list'][] = array(
						'pic_id' => intval($pic['pic_id']),
						'url' => $info['url'],
						'urlraw' => $info['urlraw'],
						'size' => ap_strval($pic['size']),
				);
			}
			
			// 取出目录列表
			$where = array(
					'appid' => $app['appid'],
					'uid' => $login_user['uid'],
					'stat' => 0,
			);
			$orderby = 'ORDER BY pic_category_id ASC';
			
			$pub_mod->init('admin', 'pic_category', 'pic_category_id');
			$class_list = $pub_mod->getRowList($where, 0, 128);
			
			$data['class_list'][] = array(
					'key' => 0,
					'name' => '全部',
			);
			foreach ($class_list as $class) {
				$data['class_list'][] = array(
						'key' => intval($class['pic_category_id']),
						'name' => ap_strval($class['name']),
				);
			}
		}
		
		$data['page_total'] = intval(ceil($data['total']/$data['pagesize']));
		
		api_result(0, 'succ', $data);
	}
	
	private function format_article($article) {
		
		$ret = array(
				'article_id' => intval($article['article_id']),
				'cid' => intval($article['cid']),
				'cname' => ap_strval($article['cname']),
				'title' => ap_strval($article['title']),
				'summary' => api_make_summary($article['summary']?$article['summary']:$article['content'], 30),
				'cover' => ap_strval($article['cover']),
				'pv' => intval($article['pv']),
				'fav_count' => intval($article['fav_count']),
				'zan_count' => intval($article['zan_count']),
				'reply_count' => intval($article['reply_count']),
				'rt' => ap_strval(date_friendly($article['rt'])),
		);
		
		return $ret;
	}
}
