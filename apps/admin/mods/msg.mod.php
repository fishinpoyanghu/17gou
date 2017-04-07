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
}
