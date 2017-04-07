<?php
/**
 * 文章api
 * 
 */

class ArticleCtrl extends BaseCtrl {
	
	public function articleList() {
		
		// 调用测试用例
// 		$this->test_article_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 不需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getArticleCond();
		
		$where = array(
				'appid' => $base['appid'],
				'stat' => 0,
		);
		if (!empty($plist['cid'])) {
			$where['cid'] = array($plist['cid'], 'in');
		}
		if (api_v_notnull($plist['title'])) {
			$where['title'] = array('%'.$plist['title'].'%', 'like');
		}
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'article', 'article_id');
		
// 		$total = $pub_mod->getRowTotal($where);
				
		$article_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $plist['orderby']);
// 		dump($article_list);
		
// 		$article_list = $list_mod->subArray($article_list, $plist['from'], $plist['count']);
// 		dump($article_list);
		
		$concat_keys = array(
				'name' => 'cname',
		);
		
		$article_list = $list_mod->concatTbl($base['appid'], $article_list, 'cid', 'main', 'article_category', 'article_category_id', $concat_keys);

		$article_list = $list_mod->isFavZan($base['appid'], $login_user?$login_user['uid']:0, $article_list, 'article_id', 2);
		
		$data = array();
		foreach ($article_list as $k=>$article) {
			$data[] = $this->format_article($article);
		}
		
		api_result(0, 'succ', $data);
	}
	
	public function article() {
	
		// 调用测试用例
// 		$this->test_article();
	
		// 标准参数检查
		$base = api_check_base();
	
		// 不需要检查登录
		$login_user = app_get_login_user($base['sessid'], $base['appid'], false);
		
		$validate_cfg = array(
				'article_id' => array(
						'api_v_numeric||文章ID非法',
				),
		);
		
		$plist = api_get_posts($base['appid'], $validate_cfg);
		
		if ($plist['article_id'] < 1) {
			api_result(5, '文章ID非法');
		}
	
		$where = array(
				'appid' => $base['appid'],
				'article_id' => $plist['article_id'],
				'stat' => 0,
		);
	
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'article', 'article_id');
	
		$article = $pub_mod->getRowWhere($where);
		if (!$article) {
			api_result(2, '文章不存在');
		}
	
		$concat_keys = array(
				'name' => 'cname',
		);
		
		$list_mod = Factory::getMod('list');
		$article_list = $list_mod->concatTbl($base['appid'], array($article), 'cid', 'main', 'article_category', 'article_category_id', $concat_keys);
		
		$article_list = $list_mod->isFavZan($base['appid'], $login_user?$login_user['uid']:0, $article_list, 'article_id', 2);
		$article_list[0]['pv'] += 1;
		
		$data = $this->format_article($article_list[0]);
		$data['content'] = ap_strval($article_list[0]['content']);
		
		// 增加一次文章的pv
		api_increase_count('main', 'article', 'article_id', $plist['article_id'], 1);
	
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
				'is_fav' => intval($article['is_fav']),
				'is_zan' => intval($article['is_zan']),
				'rt' => ap_strval(date_friendly($article['rt'])),
		);
		
		return $ret;
	}
}
