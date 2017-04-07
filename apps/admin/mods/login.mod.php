<?php
/**
 * Created on 2011-11-4
 *
 * 后台登录类
 */

class LoginMod extends BaseMod {
	
	/**
	 * 得到当前的登录用户
	 * 
	 * @param string sess_id
	 * 
	 * @return array array(
				'uid'        => 0, 
				'name'       => '',
				'company_id' => 0,
				'appid'      => 0,
				'appname'    => '',
				'appid_list' => array(),
				'last_active'=> 0,
		);
	 */
	public function getLogin($sess_id='') {
		
		if (empty($sess_id)) {
			$sess_id = Cookie::get('admin_sid');
		}
		
		// 如果有sess_id，开始判断用户的登录状态
		$sess_info = false;
		if ($sess_id) {
			$sess_info = do_cache('get', 'login', $sess_id);
		}
		// 如果有sess_id，开始判断用户的登录状态
		if ($sess_info === false) {
			
			if ($sess_id) {
				// 既然用户不处于登录状态，设置此sess_id登出啦！
				$this->setLogout($sess_id);
			}
			return false;
		}
		
		// 60分钟不活动，设置成不登录
		if ((time() - $sess_info['last_active']) > 3600) {
			
			$this->setLogout($sess_id);
			return false;
		}
		
		$sess_info['last_active'] = time();
		
		// 更新用户的登录信息
		do_cache('set', 'login', $sess_id, $sess_info);
		
		return $sess_info;
		
	}
	
	/**
	 * 设置用户登录
	 * 
	 * @param array user, 包含完整的管理员用户信息
	 * @param int expire, 过期时间，秒
	 * @param array appid_list, 这个用户包含的appid_list
	 * 
	 * @return boolean
		);
	 */
	public function setLogin($user, $expire='') {
		
		if (empty($ip)) $ip = get_ip();
		
		// 取得sess_id
		$sess_id = sess_id($user['admin_id'], $ip);
		
		// 保存Cookie
		Cookie::set('admin_sid', $sess_id, $expire);
						
		// 保存用户的登录信息
		$login_info = array(
				'uid'        => $user['admin_id'], 
				'name'       => $user['name'],
				'last_active'=> time(),
		);
		
		// 保存Session到缓存
		do_cache('set', 'login', $sess_id, $login_info);
		
		return true;
	}
	
	/**
	 * 设置用户登出
	 * 
	 * @param string sess_id
	 */
	public function setLogout($sess_id='') {
		
		if (empty($sess_id)) {
			$sess_id = Cookie::get('admin_sid');
		}
		
		Cookie::delete('admin_sid');
		
		if ($sess_id)
			do_cache('delete', 'login', $sess_id);
		
		return true;
	}

	/**
	 * 修改登录密码
	 * @param $uid
	 * @param $name
	 * @param $old
	 * @param $new
	 * @param $affirm
	 * @return array
	 */
	public function setPwd($uid,$name,$old,$new,$affirm){
		if(!$uid) return array('state' => 0,'msg' => '修改失败');
		if(empty($old) || empty($new) || empty($affirm)){
			return array('state' => 0,'msg' => '请输入完整信息');
		}
		$admin_data = Factory::getData('admin');
		$admin = $admin_data->getAdmin($uid);
		if(md5(md5($name.LOGIN_KEY.$old)) !== $admin['password']){
			return array('state' => 0,'msg' => '原密码错误，请重新输入');
		}
		if(preg_match('/[a-zA-Z]/',$new)==0){
			return array('state' => false,'msg'=>'密码必须包含字母');
		}
		if(preg_match('/[0-9]/',$new)==0){
			return array('state' => false,'msg'=>'密码必须包含数字');
		}
		if(strlen($new) < 6){
			return array('state' => false,'msg'=>'密码不能低于6位');
		}
		if($new !== $affirm){
			return array('state' => 0,'msg' => '新密码和确认密码不一致');
		}
		$update = array('password' => md5(md5($name.LOGIN_KEY.$new)));

		$admin_data->updateAdmin($uid,$update);
		return array('state' => 1,'msg' => '修改成功');
	}

	/**
	 * 是否是超级管理员
	 * @param $uid
	 * @return bool
	 */
	public function isSuperAdmin($uid){
		$uid = intval($uid);
		if(!$uid) return false;
		$admin_data = Factory::getData('admin');
		$type = $admin_data->isSuperAdmin($uid);
		return $type['type'] ? true : false;
	}
}
