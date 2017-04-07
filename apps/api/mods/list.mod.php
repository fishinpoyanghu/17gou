<?php
/**
 * 对列表的一些解析包装类
 */

class ListMod extends BaseMod {
	
	public function getPageCond() {
		$ret = array(
				'from'  => pint('from'),
				'count' => pint('count'),
		);
		
		if ($ret['from'] < 1) $ret['from'] = 1;
		
		if (empty($ret['count'])) {
			$ret['count'] = 10;
		}
		if ($ret['count'] > 48) $ret['count'] = 48;
		
		return $ret;
	}
	
	public function getFavListCond() {
		
		return $this->getPageCond();
	}
	
	public function getMsgSysCond() {
		
		return $this->getPageCond();
	}
	
	public function getMsgNotifyCond() {
	
		$ret = array(
				'type'  => pint('type'),
				'from'  => pint('from'),
				'count' => pint('count'),
		);
		
		if (!in_array($ret['type'], array(0,1,2,3,4,5))) {
			api_result(5, 'type不合法');
		}
	
		if ($ret['from'] < 1) $ret['from'] = 1;
	
		if (empty($ret['count'])) {
			$ret['count'] = 10;
		}
	
		return $ret;
	}
	
	public function getReplyCond() {
		
		$ret = array(
				'target_id'   => pint('target_id'),
				'target_type' => pint('target_type'),
				'uid'         => pint('uid'),
				'from'        => pint('from'),
				'count'       => pint('count'),
				'orderby'     => $this->getOrderby(array('reply_id', 'rt')),
		);
		
		if (!in_array($ret['target_type'], array(1,2,3))) {
			api_result(5, 'target_type参数不合法');
		}
		
		if ($ret['from'] < 1) $ret['from'] = 1;
		
		if ($ret['orderby'] === false) {
			api_result(5, 'orderby参数不合法');
		}
		
		if (empty($ret['count'])) {
			$ret['count'] = 10;
		}
		
		return $ret;
	}
	
	public function getPostCond() {
		
		$ret = array(
				'section_id'      => pstr('section_id'),
				'uid' => pint('uid'),
				'title'    => pstr('title'),
				'is_good' => pint('is_good'),
				'is_hot' => pint('is_hot'),
				'from'     => pint('from'),
				'count'    => pint('count'),
				'orderby'  => $this->getOrderby(array('post_id', 'is_good', 'is_hot', 'pv', 'fav_count', 'zan_count', 'reply_count', 'rt')),
		);
		
		if (!$this->isIntList($ret['section_id'])) {
			api_result(5, '版块ID参数错误');
		}
		if ($ret['section_id']) $ret['section_id'] = explode(',', $ret['section_id']);
		
		if ($ret['from'] < 1) $ret['from'] = 1;
		
		if ($ret['orderby'] === false) {
			api_result(5, 'orderby参数不合法');
		}
		
		if (empty($ret['count'])) {
			$ret['count'] = 10;
		}
		
		return $ret;
	}
	
	public function getArticleCond() {
		
		$ret = array(
				'cid'      => pstr('cid'),
				'title'    => pstr('title'),
// 				'page'     => pint('page'),
// 				'pagesize' => pint('pagesize'),
				'from'     => pint('from'),
				'count'    => pint('count'),
				'orderby'  => $this->getOrderby(array('article_id', 'pv', 'fav_count', 'zan_count', 'reply_count', 'rt', 'weight')),
		);
		
		if (!$this->isIntList($ret['cid'])) {
			api_result(5, 'cid参数错误');
		}
		if ($ret['cid']) $ret['cid'] = explode(',', $ret['cid']);
		
// 		if ($ret['page'] < 1) $ret['page'] = 1;
		
// 		if ($ret['pagesize'] < 1) $ret['pagesize'] = 1;
// 		if ($ret['pagesize'] > 128) {
// 			api_result(5, 'pagesize最大只能128');
// 		}
		
		if ($ret['from'] < 1) $ret['from'] = 1;
// 		if ($ret['from'] > $ret['pagesize']) {
// 			api_result(5, 'from参数不能比pagesize参数大');
// 		}
		
		if ($ret['orderby'] === false) {
			api_result(5, 'orderby参数不合法');
		}
		
		if (empty($ret['count'])) {
			$ret['count'] = 10;
		}
		
		$ret = api_safe_ipt($ret);
		
		return $ret;
	}
	
	public function getEditorArticleListCond() {
	
		$ret = array(
				'cid'      => pint('cid'),
				'title'    => pstr('title'),
				'page'     => pint('page'),
				'pagesize' => pint('pagesize'),
				'orderby'  => $this->getOrderby(array('article_id', 'pv', 'fav_count', 'zan_count', 'reply_count', 'rt', 'weight')),
		);
			
		if ($ret['page'] < 1) $ret['page'] = 1;

		if ($ret['pagesize'] < 1) $ret['pagesize'] = 10;
		if ($ret['pagesize'] > 64) {
			api_result(5, 'pagesize最大只能64');
		}
			
		if ($ret['orderby'] === false) {
			api_result(5, 'orderby参数不合法');
		}
		if ($ret['orderby'] == ' ') {
			$ret['orderby'] = ' ORDER BY article_id DESC';
		}
		
		$ret = api_safe_ipt($ret);
	
		return $ret;
	}
	
	public function getEditorPageListCond() {
	
		$ret = array(
				'name'     => pstr('name'),
				'page'     => pint('page'),
				'pagesize' => pint('pagesize'),
		);
			
		if ($ret['page'] < 1) $ret['page'] = 1;
	
		if ($ret['pagesize'] < 1) $ret['pagesize'] = 10;
		if ($ret['pagesize'] > 64) {
			api_result(5, 'pagesize最大只能64');
		}
	
		$ret = api_safe_ipt($ret);
	
		return $ret;
	}
	
	public function getEditorPicListCond() {
	
		$ret = array(
				'cid'      => pint('cid'),
				'page'     => pint('page'),
				'pagesize' => pint('pagesize'),
		);
			
		if ($ret['page'] < 1) $ret['page'] = 1;
	
		if ($ret['pagesize'] < 1) $ret['pagesize'] = 10;
		if ($ret['pagesize'] > 64) {
			api_result(5, 'pagesize最大只能64');
		}
	
		return $ret;
	}
	
	public function subArray($ary, $from, $count) {
		
		$ret = array();
		for ($i=$from; $i<=$count; $i++) {
			
			if (isset($ary[$i-1])) {
				$ret[] = $ary[$i-1];
			}
		}
		
		return $ret;
	}
	
	/**
	 * 把列表加上is_fav, is_zan字段
	 * 
	 * @param int appid
	 * @param int login_uid
	 * @param array list
	 * @param string key
	 * @param int target_type
	 * 
	 */
	public function isFavZan($appid, $login_uid, $list, $key, $target_type) {
		
		if (empty($list)) return $list;
		
		$key_ids = array();
		foreach ($list as $k=>$v) {
			$key_ids[] = $v[$key];
			$list[$k]['is_fav'] = 0;
			$list[$k]['is_zan'] = 0;
		}
		
		if ($login_uid < 1) return $list;
		
		$where = array(
				'appid' => $appid,
				'uid' => $login_uid,
				'target_type' => $target_type,
				'target_id' => array($key_ids, 'in'),
				'is_del' => 0,
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('bbs', 'fav', 'fav_id');
		
		$fav_list_tmp = $pub_mod->getRowList($where, 0, count($list));
		$fav_list = array();
		foreach ($fav_list_tmp as $k=>$fav) {
			$fav_list[$fav['target_id']] = 1;
		}
		
		foreach ($list as $k=>$v) {
			if (isset($fav_list[$v[$key]])) {
				$list[$k]['is_fav'] = 1;
			}
		}
		
		$pub_mod->init('bbs', 'zan', 'zan_id');
		
		$zan_list_tmp = $pub_mod->getRowList($where, 0, count($list));
		$zan_list = array();
		foreach ($zan_list_tmp as $k=>$zan) {
			$zan_list[$zan['target_id']] = 1;
		}
		
		foreach ($list as $k=>$v) {
			if (isset($zan_list[$v[$key]])) {
				$list[$k]['is_zan'] = 1;
			}
		}
		
		return $list;
	}
	
	/**
	 * 把$list2按照$list1的顺序排序
	 * 
	 * @param array $list1
	 * @param array $list2
	 * @param string $pkid_name
	 */
	public function reorderList($list1, $list2, $pkid_name) {
		
		$ret = array();
		$list3 = array();
		foreach ($list2 as $k=>$v) {
			$list3[$v[$pkid_name]] = $v;
		}
		
		foreach ($list1 as $k=>$v) {
			$ret[] = $list3[$v[$pkid_name]];
		}
		
		return $ret;
	}
	
	/**
	 * 做表关联
	 * 
	 * @param int $appid
	 * @param array $list
	 * @param string $concat_id，要关联的字段名称
	 * @param string $target_cfg_name
	 * @param string $target_tbl_alias
	 * @param string $target_pkid 目标表的主键ID
	 * @param array $concat_keys array(
	 * 			k1 => renamek1,
	 *          k2 => renamek2,
	 * 		)
	 */
	public function concatTbl($appid, $list, $concat_id, $target_cfg_name, $target_tbl_alias, $target_pkid, $concat_keys) {
		
		if (!is_array($list)) return $list;
		if (count($list) == 0) return $list;
		
		$concat_id_list = array();
		foreach ($list as $k=>$v) {
			$concat_id_list[$v[$concat_id]] = $v[$concat_id];
		}
		$concat_id_list = array_values($concat_id_list);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init($target_cfg_name, $target_tbl_alias, $target_pkid);
		
		$where = array(
				'appid' => $appid,
				$target_pkid => array($concat_id_list, 'in'),
		);
		
		$target_list_tmp = $pub_mod->getRowList($where, 0, count($concat_id_list));
		
		$target_list = array();
		foreach ($target_list_tmp as $k=>$target) {
			$target_list[$target[$target_pkid]] = $target;
		}
		
		foreach ($list as $k=>$v) {
			
			if (isset($target_list[$v[$concat_id]])) {
				
				foreach ($concat_keys as $m=>$n) {
					$list[$k][$n] = (null === $target_list[$v[$concat_id]][$m]) ? '' : $target_list[$v[$concat_id]][$m];
				}
			}
		}
		
		return $list;
	}
	
	public function isIntList($p) {
		
		if (!api_v_notnull($p)) return true;
		
		$ary = explode(',', $p);
		
		foreach ($ary as $k=>$v) {
			if (!is_numeric($v)) return false;
		}
		
		return true;
	}
	
	public function getOrderby($allow_keys) {
		
		$orderby = pstr('orderby');
		
		if (!api_v_notnull($orderby)) return ' ';
		
		$ob_list = explode(',', $orderby);
		
		foreach ($ob_list as $k=>$ob) {
			$ob = explode('|', $ob);
			
			if (!((count($ob) == 2) && in_array($ob[0], $allow_keys) && in_array($ob[1], array('asc', 'desc')))) {
				return false;
			}
			
			$ob_list[$k] = $ob[0].' '.$ob[1];
		}
		
		if (count($ob_list) < 1) return ' ';
		
		return ' ORDER BY '.implode(',', $ob_list);
	}
}
