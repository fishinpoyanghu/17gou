<?php
/**
 * 对列表的一些解析包装类
 */

class MsgMod extends BaseMod {
	
	private function isMsgElement($key) {
		
		$allow_keys = array(
				'sys_new',
				'notify_reply_new',
				'notify_zan_new',
				'notify_hongbao_new',
				'notify_invite_new',
				'notify_lucky_new',
				'msg_new',
		);
		$key_arr = explode(',', $key);
		if(empty($key_arr)) return false;
		foreach($key_arr as $val){
			if(!in_array($val, $allow_keys)) return false;
		}
		
		return true;
	}
	
	/**
	 * 给用户的某个消息字段加上一定的数量
	 * 
	 * @param int $uid
	 * @param string $key
	 * @param int $step
	 */
	public function increaseUserMsgCount($uid, $key, $step=1) {
		
		if (!$this->isMsgElement($key)) {
			api_result(5, '不允许操作的数据库字段');
		}		
		
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user');
		$time = time();
		$sql = "update ".DATABASE.".t_user set {$key}={$key}+1,ut={$time} where uid={$uid}";
		
		return $nc_list->executeSql($sql);
	}
	
	/**
	 * 给所有用户的sys_new+1
	 */
	public function increaseSysNew($appid) {
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$update_data = array(
				'sys_new' => array(1, 'add'),
				'ut' => time(),
		);
		$update_where = array(
				'appid' => $appid,
		);
		
		$ret = $pub_mod->updateRowWhere($update_where, $update_data);
		
		return $ret;
	}
	
	/**
	 * 设置用户表的某个消息字段值是$v
	 * 
	 * @param int $uid
	 * @param string $key，如果多个key请用半角逗号分隔
	 * @param int $v, default:0
	 */
	public function setUserMsgCount($uid, $key, $v=0) {
		
		if (!$this->isMsgElement($key)) {
			api_result(5, '不允许操作的数据库字段');
		}
		
		$key_list = explode(',', $key);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$update_data = array(
				'ut' => time(),
		);
		foreach ($key_list as $v) {
			$update_data[$v] = intval($v);
		}
		
		$ret = $pub_mod->updateRow($uid, $update_data);
		
		return $ret;
	}
	
	/**
	 * 发送系统消息
	 * @param int $appid,
	 * @param int $uid, 接收者的uid，如果传0，表示发送给所有人
	 * @param string $title, 系统消息标题
	 * @param string $content, 系统消息内容
	 * @param string $pics, 消息图片地址，多张图片以半角逗号分隔
	 */
	public function sendSys($appid, $uid, $title, $content, $pics) {
		
		$data = array(
				'msg_sys_id' => get_auto_id(C('AUTOID_M_MSG_SYS')),
				'appid' => $appid,
				'uid' => $uid,
				'title' => $title,
				'content' => $content,
				'pics' => $pics,
				'rt' => time(),
				'ut' => time(),
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'msg_sys', 'msg_sys_id');
		
		$ret = $pub_mod->createRow($data);
		
		// 给用户的sys_new+1
		if ($uid > 0) {
			$this->increaseUserMsgCount($uid, 'sys_new', 1);
		}
		else {
			$this->increaseSysNew($appid);
		}
		
		return $ret;
	}
	
	/**
	 * 发送通知消息
	 * 
	 * @param int $appid
	 * @param int $uid，接收者的uid
	 * @param int $from_uid，发送者的用户ID，如果是系统通知，from_uid=10001
	 * @param int $type 1:评论 2:赞 3:获得红包 4:邀请成功 5:夺宝中奖
	 * @param int $target_id 当target_type=5/6/7时，target_id传0
	 * @param int $target_type 1：社区 2：文章 3：商品 4：表单 5:红包 6:邀请 7:中奖通知
	 * @param string $content
	 */
	public function sendNotify($appid, $uid, $from_uid, $type, $target_id, $target_type, $content) {
		
		$data = array(
				'msg_notify_id' => get_auto_id(C('AUTOID_M_MSG_NOTIFY')),
				'appid' => $appid,
				'uid' => $uid,
				'from_uid' => $from_uid,
				'type' => $type,
				'target_id' => $target_id,
				'target_type' => $target_type,
				'content' => $content,
				'rt' => time(),
				'ut' => time(),
		);
		
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'msg_notify');
		
		$ret = $nc_list->insertData($data);
		
		// 给用户的notify_reply_new+1
		if ($type == 1) {
			$this->increaseUserMsgCount($uid, 'notify_reply_new', 1);
		}
		elseif ($type == 2) {
			$this->increaseUserMsgCount($uid, 'notify_zan_new', 1);
		}
		elseif ($type == 3) {
			$this->increaseUserMsgCount($uid, 'notify_hongbao_new', 1);
		}
		elseif ($type == 4) {
			$this->increaseUserMsgCount($uid, 'notify_invite_new', 1);
		}
		elseif ($type == 5) {
			$this->increaseUserMsgCount($uid, 'notify_lucky_new', 1);
		}
		
		return $ret;
	}

	/**
	 * 福袋发送通知消息
	 * 消息推送改为公众号推送
	 *  
	 * @param int $uid，接收者的uid
	 *  
	 * @param int $type  类型 1邀请注册，2邀请消费，3拼团成功，4购买福袋，5打开福袋
	 *  
	 * @param array content   invite_nick,邀请人名字 goods_name 商品名字  count 一元购商品购买次数 give_count 福袋赠送次数
	 * @param string $content
	 */
	public function sendPacketNotify( $uid,$type,$num,$content, $goods_id='', $avtivity_type='',$avtivity_id='',$invite_userid='') { 
		if($type==2){
			return '';
		}
		$data = array(
				'uid' => $uid ,
				'type' => $type,
				'num' => $num,
				'content' => $content,
				'goods_id' => $goods_id,
				'avtivity_type' => $avtivity_type,
				'avtivity_id'=>$avtivity_id,
				'invite_userid' => $invite_userid,  
				'rt' => time(), 
		);
		
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'packet_msg'); 
		$ret = $nc_list->insertData($data);
		//公众号推送消息
		// 给用户的notify_reply_new+1
		/*	if ($type == 1) {
				$this->increaseUserMsgCount($uid, 'notify_reply_new', 1);
	    }*/
		 
		
		return $ret;
	}
	//全局消息推送 不是系统通知。
	//商品详细页  activity type   1 购买了1元购商品  2 准备揭晓 3  已经开奖  4 开团 5参团 6团结束 7 单独购买 8 是开福袋 开出3个  提醒1个福袋送3个 还有提醒即将揭晓商品  谁买了商品是否提示    谁中奖了。提示 谁开团了。谁购买了 参团了这个商品  正在拼团这个商品  1 一元购 2 开团 3 福袋抽奖
	public function sendSystNotify($type,$msg) { 
		$nc_list = Factory::getMod('nc_list');
		if($msg['goods_id']){  
			$where = array(
	            'goods_id' => $msg['goods_id'],  
	        );
	        $column = array(
	            'title' 
	        );
	        if($type==1){
	        	$nc_list->setDbConf('shop', 'goods');
	        }else{
	        	$nc_list->setDbConf('team', 'team_goods');
	        }
	         
	        $goods = $nc_list->getDataOne($where, $column, array(), array(), false); 
	        $msg['goods_name']=$goods['title'];
        }
		if($msg['uid']){  
			$where = array(
	            'uid' => $msg['uid'],  
	        );
	        $column = array(
	            'nick' ,'icon'
	        );
	        $nc_list->setDbConf('main', 'user');
	        $user = $nc_list->getDataOne($where, $column, array(), array(), false); 
	        $msg['nick']=$user['nick'];
	        $msg['icon']=$user['icon'];
        }  

		$data = array( 
				'type' => $type,
			    'msg'=>json_encode($msg),
				'rt' => time(), 
		);
		
		$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'sys_notify'); 
		$ret = $nc_list->insertData($data); 
		 
	}

}
