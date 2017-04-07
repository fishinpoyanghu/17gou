<?php
/**
 * 后台管理首页 ctrl
 */

class IndexCtrl extends BaseCtrl {

	public function index() {
				
		$tbl = 'admin';
		$t_mod = Factory::getMod('table');
		
		if (!$t_mod->tryLoadTable($tbl)) {
			dump('not exists!');
		}
				
		$tbl_head = $t_mod->getListCfg();		
		$base_cfg = $t_mod->getBaseCfg();
		
		// 传输给view的数据
		$data = array(
				'form_id' => 'loginform',
				'action_url' => '?a=ajax_login',
				'login_user' => false,
				'base_cfg' => $base_cfg,
				'tbl_head' => $tbl_head,
		);
		
		Factory::getView("user/login", $data);
	}
	
	public function ajaxLogin() {
		
		// 标准参数检查
		$base = api_check_base();
				
		$name = pstr('name');
        $password = pstr('password');
        $sms = pstr('sms');

		if (empty($name) || empty($password) || empty($sms)) {
			echo_result(1, '用户名或者密码不正确');
		}

        /*$code2 = do_cache('get','code',$name);
        if(strtolower($sms)!=$code2 || $code2==''){
            echo_result(1, '短信验证码错误');
        }

        do_cache('delete','code',$name);*/
		
		// 验证用户是否可以登录
		$admin_mod = Factory::getMod('admin');
		$where = array(
				'name' => $name,
		);
		
		$admin_list = $admin_mod->getAdminList($where, 0, 1);
		if (!$admin_list) {
			echo_result(1, '用户名或者密码不正确');
		}
		
		$admin = $admin_list[0];
		
		if ($admin['password'] != md5(md5($name.LOGIN_KEY.$password))) {
			echo_result(1, '用户名或者密码不正确');
		}
		
		if ($admin['stat'] > 0) {
			echo_result(1, '帐号已经被禁止登录');
		}


		
		// 到这里，表示帐号可以登录
		$login_mod = Factory::getMod('login');
		
		$login_mod->setLogin($admin);
		
		echo_result(0, 'succ');
	}

	/**
	 * 退出
	 */
	public function logout(){
		$login_mod = Factory::getMod('login');
		$login_mod->setLogout();
		header('Location: ?c=index');
		exit;
	}

	/**
	 * 修改密码页面
	 */
	public function password(){
		$login_user = app_get_login_user(1, 1);
		$data = array(
			'login_user' => $login_user,
		);
		Factory::getView("user/password", $data);
	}

	/**
	 * 修改密码
	 */
	public function setPwd(){
		$login_user = app_get_login_user(1, 1);
		$old = pstr('old');
		$new = pstr('new_pwd');
		$affirm = pstr('affirm_pwd');
		$login_mod = Factory::getMod('login');
		$res = $login_mod->setPwd($login_user['uid'],$login_user['name'],$old,$new,$affirm);
		echo_result($res['state'],$res['msg']);
	}

	/**
	 * 管理员列表
	 */
	public function adminList(){
		$login_user = app_get_login_user(1, 1);
		if(!$login_user['is_super']) exit;
		$page = gint('page');
		$page = $page < 1 ? 1 : $page;
		$num = 15;
		$mod = Factory::getMod('admin');
		$info = $mod->adminList($page,$num);
		$page_content = page(ceil($info['total']/$num), $page, "?c=index&a=adminList&page");
		$data = array(
			'login_user' => $login_user,
			'list' => $info['list'],
			'page_content' => $page_content,
			'page_total' => $info['total'],
			'page_num' => $num,
			'menu' => '',
		);

		Factory::getView("user/admin_list", $data);
	}

	/**
	 * 添加管理员
	 */
	public function addAdmin(){
		$login_user = app_get_login_user(1, 1);
		if(!$login_user['is_super']) exit;
		$data = array(
			'name' => pstr('name'),
			'password' => pstr('pwd'),
			'type' => pint('type'),
		);
		$mod = Factory::getMod('admin');
		$res = $mod->addAdmin($data);
		echo_result($res['state'],$res['msg']);
	}

	/**
	 * 修改管理员账户信息
	 */
	public function modifyAdmin(){
		$login_user = app_get_login_user(1, 1);
		if(!$login_user['is_super']) exit;
		$data = array(
			'id' => pint('id'),
			'type' => pint('type'),
		);
		$mod = Factory::getMod('admin');
		$res = $mod->modifyAdmin($data);
		echo_result($res);
	}

    public function code(){
        $code = Factory::getMod('code');
        session_start();
        $code->doimg();
        do_cache('set','code',session_id().'_login',$code->getCode());
    }

    public function sms(){
        $code = gstr('code');
        $name = gstr('name');
        session_start();

        $code2 = do_cache('get','code',session_id().'_login');
        if(strtolower($code)!=$code2 || $code2==''){
            echo_result(1, '验证码错误');
        }
        if($name==''){
            echo_result(1, '请填写手机号');
        }

        do_cache('delete','code',session_id().'_login');

        $pub_mod = Factory::getMod('pub');
        $pub_mod->init('admin', 'admin', 'uid');

        $where = array(
            'name' => $name,
        );
        $user = $pub_mod->getRowWhere($where);

        if (!$user) {
            echo_result(0,'该号码未注册');
        }

        $sms = $this->_generate_code($name);

        do_cache('set', 'code', $name, $sms);

        echo_result(0, '发送成功');

    }

    private function _generate_code($phone) {

        $code = mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
        $url = 'https://msg.damaiapp.com/sms/';
        $data = array(
            'appId' => '17gou',
            'phone' => $phone,
            'content' => '【亿七购】您的验证码为：'.$code,
            'time' => time(),
            'key' => 'HUIV_FBII_THIOO_GBOP_BEIHN',
        );
        ksort($data);
        $sign = md5(http_build_query($data));
        $data['sign'] = $sign;
        unset($data['key']);
        $result = curl_page($url, 1, http_build_query($data), 3, 3);
        $info = json_decode($result,true);
        if($info['state']){
            return $code;
        }else{
            echo_result(1, '发送失败'.$info['msg']);
        }

    }
}
