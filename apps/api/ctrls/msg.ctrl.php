<?php
/**
 * 消息api
 */

class MsgCtrl extends BaseCtrl {


	
	public function sysList() {
		
		// 调用测试用例
// 		$this->test_sys_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		 
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getMsgSysCond();
		
		$where = array(
				'appid' => $base['appid'],
				'uid'   => array(array($login_user['uid'], 0), 'in'),
				'stat'  => 0,
		);
		$orderby = 'ORDER BY msg_sys_id DESC';
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'msg_sys', 'msg_sys_id');
		
		$msg_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $orderby);
				
		$data = array();
		foreach ($msg_list as $k=>$msg) {
			$data[] = array(
					'msg_sys_id' => intval($msg['msg_sys_id']),
					'title' => ap_strval($msg['title']),
					'content' => ap_strval($msg['content']),
					'pics' => ap_strval($msg['pics']),
					'rt' => date_friendly($msg['ut']),
			);
		}
		
		// 清空新系统消息数
		$msg_mod = Factory::getMod('msg');
		$msg_mod->setUserMsgCount($login_user['uid'], 'sys_new');
		
		api_result(0, '获取系统消息成功', $data);
	}
	
	public function notifyList() {
		
		// 调用测试用例
// 		$this->test_notify_list();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$list_mod = Factory::getMod('list');
		
		$list_mod = Factory::getMod('list');
		$plist = $list_mod->getMsgNotifyCond();
		
		$where = array(
				'appid' => $base['appid'],
				'uid'   => $login_user['uid'],
				'stat'  => 0,
		);
		if ($plist['type'] > 0) {
			$where['type'] = $plist['type'];
		}
		$orderby = 'ORDER BY msg_notify_id DESC';
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'msg_notify', 'msg_notify_id');
		
		$msg_list = $pub_mod->getRowList($where, $plist['from']-1, $plist['count'], $orderby);
		
		$concat_keys = array(
				'nick' => 'from_unick',
				'icon' => 'from_uicon',
		);
		$msg_list = $list_mod->concatTbl($base['appid'], $msg_list, 'from_uid', 'main', 'user', 'uid', $concat_keys);
		
		$msg_list2 = array(
				'1' => array(),
				'2' => array(),
				'3' => array(),
				'4' => array(),
				'5' => array(),
				'6' => array(),
				'7' => array(),
				'8' => array(),
		);
		foreach ($msg_list as $msg) {
			$msg['target_title'] = '';
			$msg['target_content'] = '';
			$msg['target_cover'] = '';
			$msg['target_stat'] = 0;
			$msg_list2[$msg['target_type']][] = $msg;
		}
		
		// 关联社区
		$concat_keys = array(
				'title' => 'target_title',
				'content' => 'target_content',
				'pics' => 'target_cover',
				'is_del'  => 'target_stat',
		);
		$msg_list2["1"] = $list_mod->concatTbl($base['appid'], $msg_list2["1"], 'target_id', 'bbs', 'post', 'post_id', $concat_keys);
		
		// 关联文章
		$concat_keys = array(
				'title' => 'target_title',
				'content' => 'target_content',
				'cover' => 'target_cover',
				'stat'  => 'target_stat',
		);
		$msg_list2["2"] = $list_mod->concatTbl($base['appid'], $msg_list2["2"], 'target_id', 'main', 'article', 'article_id', $concat_keys);
		
		// 关联商品
		// @todo
		
		// 关联表单
		$concat_keys = array(
				'title' => 'target_title',
				'description' => 'target_content',
				'cover' => 'target_cover',
				'stat'  => 'target_stat',
		);
		$msg_list2['4'] = $list_mod->concatTbl($base['appid'], $msg_list2["4"], 'target_id', 'main', 'form', 'form_id', $concat_keys);
		
		$msg_list2 = array_merge($msg_list2["1"], $msg_list2["2"], $msg_list2["3"], $msg_list2["4"], $msg_list2["5"], $msg_list2["6"], $msg_list2["7"], $msg_list2["8"]);
		
		// 重新排序$msg_list2
		$msg_list = $list_mod->reorderList($msg_list, $msg_list2, 'msg_notify_id');
		
		// 如果target_cover有多张图片，只去第一张，并格式化输出
		$data = array();
		foreach ($msg_list as $msg) {
				
			if ($msg['target_cover']) {
				$ary = explode(',', $msg['target_cover']);
				$msg['target_cover'] = $ary[0];
			}
			
			if (empty($msg['target_title'])) {
				$msg['target_title'] = api_make_summary($msg['target_content'], 30);
			}
			
			$icon_info = ap_user_icon_url($msg['from_uicon']);
				
			$data[] = array(
					'msg_notify_id' => intval($msg['msg_notify_id']),
					'target_type' => intval($msg['target_type']),
					'target_id' => intval($msg['target_id']),
					'target_title' => ap_strval($msg['target_title']),
					'target_cover' => ap_strval($msg['target_cover']),
					'target_stat' => intval($fav['target_stat']),
					'content' => ap_strval($msg['content']),
					'from_uid' => intval($msg['from_uid']),
					'from_unick' => ap_strval($msg['from_unick']),
					'from_uicon' => ap_strval($icon_info['icon']),
					'rt' => date_friendly($msg['rt']),
			);
		}
		
		// 根据type清空用户的notify_reply_new或者notify_zan_new字段
		$msg_mod = Factory::getMod('msg');
		// 清空新评论通知数
		if ($plist['type'] == 1) {
			$msg_mod->setUserMsgCount($login_user['uid'], 'notify_reply_new');
		}
		// 清空新赞通知数
		elseif ($plist['type'] == 2) {
			$msg_mod->setUserMsgCount($login_user['uid'], 'notify_zan_new');
		}
		elseif ($plist['type'] == 3) {
			$msg_mod->setUserMsgCount($login_user['uid'], 'notify_hongbao_new');
		}
		elseif ($plist['type'] == 4) {
			$msg_mod->setUserMsgCount($login_user['uid'], 'notify_invite_new');
		}
		elseif ($plist['type'] == 5) {
			$msg_mod->setUserMsgCount($login_user['uid'], 'notify_lucky_new');
		}
		elseif ($plist['type'] == 0) {
			$msg_mod->setUserMsgCount($login_user['uid'], 'notify_reply_new,notify_zan_new,notify_hongbao_new,notify_lucky_new,notify_invite_new');
		}
		
		api_result(0, '获取通知消息成功', $data);
	}
	/**
	*全局系统消息推送
	*
	*/ 
    public function getsysnotify(){
    	$base = api_check_base(); 
    	$validate_cfg = array(
			'type' => array(			 
			)		 
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		if($ipt_list['type']){
			$where['type']=$ipt_list['type'];
		} 
        $order = array(
            'id' => 'desc',
        );
        $limit = array(
			'begin' => 0,
			'length' => 10
		);
        $nc_list = Factory::getMod('nc_list'); 
        $nc_list->setDbConf('main', 'sys_notify');
        $ret3 = $nc_list->getDataList($where,array(),$order,$limit); 
        $time=time();
        foreach($ret3 as $k=>$v){
        	$ret3[$k]['now']=$time;
        	$ret3[$k]['msg']=json_decode($v['msg'],true);
        	if($ret3[$k]['msg']['icon']){
        		$ret3[$k]['msg']['icon']=ap_user_icon_url($ret3[$k]['msg']['icon'])['icon'];
        	}
        	 
        }
        sort($ret3);
		api_result(0, 'succ',$ret3);

    }
	 
}
