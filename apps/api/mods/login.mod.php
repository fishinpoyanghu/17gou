<?php
/**
 * Created on 2011-11-4
 *
 * 登录类
 */

class LoginMod extends BaseMod {
	
	/**
	 * 得到当前的登录用户
	 * 
	 * 注意：如果修改这个函数，需要同时修改 apps/admin/mods/login.mod.php getLogin函数
	 * 
	 * @param string sessid
	 * 
	 * @return array array(
	 *			'uid'        => 0, 
	 *			'name'       => '',
	 *			'company_id' => 0,
	 *			'appid'      => 0,
	 *			'appname'    => '',
	 *			'appid_list' => array(),
	 *			'last_active'=> 0,
	 *	);
	 */
	public function getLogin($sessid='') {
		
		static $login_cache_list = array();
		
		if (empty($sessid)) {
			$sessid = Cookie::get('sid');
		}
		if (!$sessid) return false;
		
		if (isset($login_cache_list[$sessid])) {
			return $login_cache_list[$sessid];
		}

        // 如果有sessid，开始判断用户的登录状态
		$sess_info = false;

        if ($sessid) {
			$sess_info = do_cache('get', 'login', $sessid);
		}


        // 如果有sessid，开始判断用户的登录状态
		if ($sess_info === false) {
			
			if ($sessid) {
				// 既然用户不处于登录状态，设置此sessid登出啦！
				$this->setLogout($sessid);
			}
			return false;
		}
		
		$sess_info['last_active'] = time();

        // 更新用户的登录信息
		do_cache('set', 'login', $sessid, $sess_info);

        $pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$user = $pub_mod->getRow($sess_info['uid']);
		
		if ($user) {
			$user = array_merge($sess_info, $user);
			
			// 如果是微信用户
			if ($user['type'] == 1) {
				$pub_mod->init('main', 'wxuser', 'wx_uid');
				$where = array(
						'appid' => $user['appid'],
						'uid' => $user['uid'],
				);
				$wxuser = $pub_mod->getRowWhere($where);
				
				if ($wxuser) {
					if (empty($user['nick'])) $user['nick'] = $wxuser['wx_nickname'];
					if (empty($user['icon'])) {
						$user['icon'] = $wxuser['wx_headimgurl'];
						$user['iconraw'] = $wxuser['wx_headimgurl'];
					}
					else {
						$iconinfo = ap_user_icon_url($user['icon']);
						$user['icon'] = $iconinfo['icon'];
						$user['iconraw'] = $iconinfo['iconraw'];
					}
					if (empty($user['sex'])) $user['sex'] = $wxuser['wx_sex'];
				}
			}
			
			$login_cache_list[$sessid] = $user;
			
			return $user;
		}
		
		return false;
		
	}
	
	/**
	 * 设置用户登录
	 * 
	 * @param array user, 包含完整的用户信息
	 * @param int expire, 过期时间，秒
	 * 
	 * @return boolean
		);
	 */
	public function setLogin($user, $expire='') {
		
		if (empty($ip)) $ip = get_ip();
		
		// 取得sessid
		$sessid = sess_id($user['uid'], $ip);
		
		// 保存Cookie
		Cookie::set('sid', $sessid, $expire);
		// 保存用户的登录信息
		$cache = array(
				'uid' => $user['uid'],
				'last_active' => time(),
		);
		
		// 保存Session到缓存
		do_cache('set', 'login', $sessid, $cache);
		
		return $sessid;
	}
	
	/**
	 * 设置用户登出
	 * 
	 * @param string sessid
	 */
	public function setLogout($sessid='') {
		
		if (empty($sessid)) {
			$sessid = Cookie::get('sid');
		}
		
		Cookie::delete('sid');
		
		if ($sessid)
			do_cache('delete', 'login', $sessid);
		
		return true;
	}
}
