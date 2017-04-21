<?php
/**
 * 用户api
 * 
 */

class UserCtrl extends BaseCtrl {
	
	private $rebate_rate=4; // 返利比例，默认是4%


	public function getUser() {
		
		// 调用测试用例
// 		$this->test_get_user();
		
		// 标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
				'uid' => array(
						'api_v_numeric|1||uid不合法',
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$where = array(
				'uid' => $ipt_list['uid'],
				'appid' => $base['appid'],
		);
		
		$user = $pub_mod->getRowWhere($where);
		
		if (!$user) {
			api_result(2, '用户不存在');
		}
		
		if ($user['stat']) {
			// 表示用户已经被删除
			// @todo
		}
		
		// 到这里，可以返回用户信息给客户端了
		$data = api_get_output_simple_user_data($user);
		
		api_result(0, 'succ', $data);
	}
	
	public function getLogin() {
		
		// 调用测试用例
// 		$this->test_get_login();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		if($login_user['type']==1 &&  $login_user['unionid']){
		    $nc_activity = Factory::getMod('nc_activity');
            $backtime=$nc_activity->rebatemoney($login_user['unionid'],$login_user['uid']); //赠送金额
            $login_user['back_time']=$backtime?$backtime:$login_user['back_time'];
            if($login_user['back_time']+0>time()){ //登录送余额 
                $nc_activity->addloginmoney($login_user); 
            }
		}
		  
		// 到这里，表示可以登录啦
		$data = api_get_output_user_data($login_user);
		session_start();
		if(isset($_SESSION['wxregister_first'])){
		 	$data['wxregister_first']=1;
		 }

		api_result(0, 'succ', $data);
	}
	
	public function uploadIcon() {
		
		// 调用测试用例
// 		$this->test_upload_icon();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$ret = $this->do_upload('file');
		
		$data = array(
				'icon' => $ret['icon'],
				'iconraw' => $ret['iconraw'],
		);
		$iconinfo = $ret['iconinfo'];
		
		// 更新用户的头像信息
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$update_data = array(
				'icon' => $iconinfo['icon'],
				'ut' => time(),
		);
		
		$ret = $pub_mod->updateRow($login_user['uid'], $update_data);
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
		
		api_result(0, '头像上传成功', $data);
	}


    public function uploadIcon2(){
        $base = api_check_base();

        // 得到当前登录用户
        $login_user = app_get_login_user($base['sessid'], $base['appid'], true);
        include_once COMMON_PATH.'libs/LibFile.php';
        $lib_file = new LibFile();
        $lib_file->setMaxsize(2560);
        $re = LibFile::upload('filename', UPLOAD_PATH, PIC_UPLOAD_URL, 1024, array('.jpg', '.gif', '.png'));

        switch ($re['msg']) {
            case "EXT_ERR":
                api_result(1, '不支持的图片类型');
                break;
            case "SIZE_OVER":
                api_result(1, '图片大小超出限制');
                break;
            case "UPLOAD_ERROR":
                api_result(1, '图片保存失败');
                break;
            case "UPLOAD_SUCCESS":
                $iconinfo = api_cut_iconname($re['url']);

                include_once CORE_ROOT.'/class/util/image/thumb.class.php';
                $thumb = new ThumbHandler();
                $thumb->setSrcImg($re['file']);
                $thumb->setCutType(1);
                $thumb->setDstImg(UPLOAD_PATH.$iconinfo['icon']);
                $thumb->createImg(120,120);

                $thumb->setSrcImg($re['file']);
                $thumb->setDstImg(UPLOAD_PATH.$iconinfo['iconraw']);
                $thumb->createImg(640,640);

                $ret = array(
                    'icon' => C('UPLOAD_DOMAIN').$iconinfo['icon'],
                    'iconraw' => C('UPLOAD_DOMAIN').$iconinfo['iconraw'],
                    'iconinfo' => $iconinfo,
                );

                $data = array(
                    'icon' => $ret['icon'],
                    'iconraw' => $ret['iconraw'],
                );
                $iconinfo = $ret['iconinfo'];

                // 更新用户的头像信息
                $pub_mod = Factory::getMod('pub');
                $pub_mod->init('main', 'user', 'uid');

                $update_data = array(
                    'icon' => $iconinfo['icon'],
                    'ut' => time(),
                );

                $ret = $pub_mod->updateRow($login_user['uid'], $update_data);
                if (!$ret) {
                    api_result(1, '数据库错误，请重试');
                }

                api_result(0, '头像上传成功', $data);

                break;
        }
    }

	public function bindInviteCode() {
		
		// 调用测试用例
// 		$this->test_bind_invite_code();
	
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		if ($login_user['rebate_uid'] > 0) {
			api_result(1, '已经绑定过啦，不能重复绑定');
		}
		
		// 如果注册时间离绑定时间超过3天，禁止绑定
		if ($login_user['rt'] < (time()-86400*3)) {
			api_result(1, '已经注册超过3天的用户不能绑定了哦');
		}
		
		// 如果用户不是第三方的，不能绑定
		if ($login_user['type'] == 0) {
			api_result(1, '不允许绑定的用户');
		}
		
		$validate_cfg = array(
				'invite_code' => array(	
				),
		);
	
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

		$rebate_uid = api_decode_invite_code($base['appid'], $ipt_list['invite_code']);
        if($rebate_uid == -1){
            api_result(0, 'succ');
        }

		if ($rebate_uid < 1) {
			api_result(1, '邀请码不正确');
		}
		
		if ($rebate_uid == $login_user['uid']) {
			api_result(1, '不允许绑定自己');
		}

		// 到这里，表示可以绑定了
		$update_data = array(
				'rebate_rate' => $this->rebate_rate,
				'rebate_uid' => $rebate_uid,
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$ret = $pub_mod->updateRow($login_user['uid'], $update_data);
    if($ret){
        //告知他成功邀请了一个用户
        $msg_mod = Factory::getMod('msg');
        $msg_mod->sendNotify(10002, $rebate_uid, 10002, 4, 0, 6, '您成功邀请'.$login_user['nick'].'注册。');
        $_nc_list = Factory::getMod('nc_list');
        $_nc_list->setDbConf('shop', 'fenxiao');
        $ret = $_nc_list->getDataOne(array('level'=>0), array(), array(), array(), false);
        if($ret['percent']>0){
            $insert = array(
                'uid' => $rebate_uid,
                'money' => $ret['percent'],
                'desc' => '佣金:0:'.$login_user['nick'],
                'ut' => time(),
                'appid' => '10002',
            );
            $_nc_list->setDbConf('shop', 'money');
            $_nc_list->insertData($insert);
            $_nc_list->setDbConf('main', 'user');
            $sql = "update {$_nc_list->dbConf['tbl']} set `yongjin`=`yongjin`+{$ret['percent']} where `uid`={$rebate_uid}";
            $_nc_list->executeSql($sql);
        }
    }else{
        api_result(1, '数据库错误，请重试');
    }
		
		api_result(0, 'succ');
	}
	
	public function reg() {
		
		// 调用测试用例
// 		$this->test_reg();
		
		// 标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
				'name' => array(
						'api_v_mobile||用户名必须是手机号码',
						'api_unique|main,user,uid||手机号已存在',
				),
				'password' => array(
					//	'api_v_length|6,15||密码长度必须在6-15个字符之间',
					//	'api_v_password||密码必须包含字母和数字,不能含有其他字符',
				),
				'sex' => array(
						'api_v_inarray|0;;1;;2||必须选择性别'
				),
				'nick' => array(
						/*'api_v_length|1,12||昵称长度必须在1-12个字之间',
						'api_v_notspace||昵称不能包含空格',
						'api_unique|main,user,uid||昵称已存在',*/
				),
				'invite_code' => array(),
		);
		$mregister=''; //表示是否是手机端注册
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		if($ipt_list['password']!='' || $ipt_list['nick']!=''){ //证明是pc端
			$validate_cfg['password']=array(
					 	'api_v_length|6,15||密码长度必须在6-15个字符之间',
					 	'api_v_password||密码必须包含字母和数字,不能含有其他字符',
				);
			$validate_cfg['nick']=array(
					 	'api_v_length|1,12||昵称长度必须在1-12个字之间',
						'api_v_notspace||昵称不能包含空格',
						'api_unique|main,user,uid||昵称已存在',
				);
		   $ipt_list = api_get_posts($base['appid'], $validate_cfg);
		}else{ //手机端自动发送密码及自动生成用户名
			$str='abcdefghijklmnopqrstuvwxyz';
			$sstr=substr(str_shuffle($str),0,2);
			$num=substr(str_shuffle('0123456789'),0,4);
			$ipt_list['password']=$sstr.$num;
			$mregister=1;

		}
		$ipt_list = api_safe_ipt($ipt_list);
		 
		$rebate_uid = api_decode_invite_code($base['appid'], $ipt_list['invite_code']);
		
		// 判断手机验证码
		$mcode = pstr('mcode');

        $cache_ary = do_cache('get', 'mcode', $ipt_list['name']);
        if(time()-$cache_ary['last']>10*60){
           api_result(8, '验证码已失效');
        }


		if ($mcode != $cache_ary['code']) {
			api_result(8, '验证码错误');
		}

		// 删除手机验证码缓存
		do_cache('delete', 'mcode', $ipt_list['name']);
		
		// 判断是否有上传用户头像
		$icon = '';
		if (isset($_POST['icon']) && (!empty($_POST['icon']))) {
			$ret = $this->do_upload('icon');
			$icon = api_get_icon_uri($ret['icon']);
		}
		
		// 到这里，表示可以注册了
		$data = array(
				'uid' => get_auto_id(C('AUTOID_M_USER')),
				'appid' => $base['appid'],
				'name' => $ipt_list['name'],
				'password' => md5($ipt_list['password']),
				'sex' => $ipt_list['sex'],
				'nick' => $ipt_list['nick'],
				'icon' => $icon ? str_replace('uploads/','',$icon) : mt_rand(1,12).'.png',
				'type' => 0,
				'ip' => get_ip(1),
				'rt' => time(),
				'ut' => time(),
				'phone'=> $ipt_list['name'],
			//	'money'=>1 //默认注册赠送1块钱
		);
		$data['nick']=empty($ipt_list['nick'])?'yiqigou_'.$data['uid']:$ipt_list['nick'];

		if ($rebate_uid > 0) {
			$data['rebate_rate'] = $this->rebate_rate;
			$data['rebate_uid'] = $rebate_uid;
		}
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		$ret = $pub_mod->createRow($data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试');
		}
	 	$nc_list = Factory::getMod('nc_list');
		$nc_list->setDbConf('main', 'user_extend'); 
	    $nc_list->insertData(array(
                'uid' => $data['uid']  
        ));
        //注册送积分
        $point_mod = Factory::getMod('nc_point');
        $point_mod->createRegPoint($data);
		
		//注册即有红包
		/*$packet_mod = Factory::getMod('nc_packet');
		$packet_mod->createRegPacket($data);*/
		
		if ($rebate_uid > 0) {
			//告知他成功邀请了一个用户
			$msg_mod = Factory::getMod('msg');
			$msg_mod->sendNotify(10002, $rebate_uid, 10002, 4, 0, 6, '您成功邀请'.$ipt_list['nick'].'注册。');
            $_nc_list = Factory::getMod('nc_list');
            $_nc_list->setDbConf('shop', 'fenxiao');
            $ret = $_nc_list->getDataOne(array('level'=>0), array(), array(), array(), false);
            if($ret['percent']>0){
                $insert = array(
                    'uid' => $rebate_uid,
                    'money' => $ret['percent'],
                    'desc' => '佣金:0:'.$ipt_list['nick'],
                    'ut' => time(),
                    'appid' => '10002',
                );
                $_nc_list->setDbConf('shop', 'money');
                $_nc_list->insertData($insert);
                $_nc_list->setDbConf('main', 'user');
                $sql = "update {$_nc_list->dbConf['tbl']} set `yongjin`=`yongjin`+{$ret['percent']} where `uid`={$rebate_uid}";
                $_nc_list->executeSql($sql);
            }
		}
		
		// 执行登录动作
		$user = $pub_mod->getRow($data['uid']);
		
		// 到这里，表示可以登录啦
		$this->do_login($user,$mregister,$ipt_list);
	}
	
	public function login() {
		// 调用测试用例
// 		$this->test_login();
		// 标准参数检查
		$base = api_check_base();
		$name = pstr('name');
		$password = pstr('password');
		$pcode = pstr('pcode');
		
		// 这里判断是否需要检查pcode
		// @todo

        if (empty($name) || empty($password)) {
			api_result(5, '用户名或者密码错误');
		}

        $where = array(
				'appid' => $base['appid'],
				'name'  => $name,
		);
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');

        $user = $pub_mod->getRowWhere($where);

        if (!$user) {
			api_result(5, '用户名不存在');
		}
		
		if ($user['password'] != md5($password)) {
			api_result(5, '用户名或者密码错误');
		}

		// 到这里，表示可以登录啦
		$this->do_login($user);
	}
	
	public function logout() {
		
		// 标准参数检查
		$base = api_check_base();
		
		$login_mod = Factory::getMod('login');
		$login_mod->setLogout($base['sessid']);
		
		api_result(0, '登出成功');
	}
	
	public function modifyUserInfo() {
		api_result(5, '很抱歉,目前不能修改昵称!');
		// 调用测试用例
// 		$this->test_modify_user_info();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'signature' => array(
						'api_v_length|0,100||签名长度不能超过100个字',
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$pub_mod = Factory::getMod('pub');
		
		$nick = pstr('nick');
		if ($nick && ($nick != $login_user['nick'])) {
			
			if (!api_v_length($nick, 1, 10)) {
				api_result(5, '昵称长度必须在1-10个字之间');
			}
			if (!api_v_notspace($nick)) {
				api_result(5, '昵称不能包含空格');
			}
			
			$where = array(
					'appid' => $base['appid'],
					'nick' => $nick,
			);
			$pub_mod->init('main', 'user', 'uid');
			$_tmp = $pub_mod->getRowWhere($where);
			
			if ($_tmp) {
				api_result(5, '昵称已存在');
			}
			
			$ipt_list['nick'] = $nick;
		}
		
		// 到这里，可以修改用户信息了
		$update_data = array(
				'ut' => time(),
		);
		if (isset($_POST['signature'])) $update_data['signature'] = $ipt_list['signature'];
		if (isset($ipt_list['nick'])) $update_data['nick'] = $ipt_list['nick'];
		
		if (count($update_data) > 1) {
			$pub_mod->init('main', 'user', 'uid');
			
			$ret = $pub_mod->updateRow($login_user['uid'], $update_data);
			
			if (!$ret) {
				api_result(1, '数据库错误，请重试', $data);
			}
		}
		
		api_result(0, '修改个人信息成功');
	}
	
	public function modifyPassword() {
		
		// 调用测试用例
// 		$this->test_modify_password();
		
		// 标准参数检查
		$base = api_check_base();
		
		// 得到当前登录用户
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
				'password' => array(
						'api_v_length|6,15||密码长度必须在6-15个字符之间',
						'api_v_password||密码必须由字母、数字或下划线组成',
				),
				/*'password_old' => array(
						'api_v_notnull||旧密码不能为空',
				),*/
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		/*if (md5($ipt_list['password_old']) !== $login_user['password']) {
			api_result(5, '旧密码不正确');
		}*/
		
		// 到这里，可以修改密码了
		$update_data = array(
				'password' => md5($ipt_list['password']),
				'ut' => time(),
		);
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$ret = $pub_mod->updateRow($login_user['uid'], $update_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试', $data);
		}
		
		api_result(0, '修改密码成功');
	}
	
	public function checkForgotMcode() {
		
		// 调用测试用例
// 		$this->test_check_forgot_mcode();
		
		// 标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
				'mobile' => array(
						'api_v_mobile||请输入正确的手机号码',
				),
				'mcode' => array(),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$ret = do_cache('get', 'forgotcode', $ipt_list['mobile']);
		
		if (!$ret) {
			api_result(8, '验证码不正确');
		}
		if ($ret['code'] != $ipt_list['mcode']) {
			api_result(8, '验证码不正确');
		}
		
		// 到这里，验证通过了，删除mcode的key，并保存另外个savekey
		$savekey = md5(microtime().mt_rand(0,10000).'fdkkekd');
		
		do_cache('set', 'forgotsavekey', $savekey, $ret['uid']);
		
		api_result(0, '验证码正确', array('savekey'=>$savekey));
	}
	
	public function saveForgotPassword() {
		
		// 标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
				'savekey' => array(
						'api_v_notnull||缺少savekey',
				),
				'password' => array(
						'api_v_length|6,15||密码长度必须在6-15个字符之间',
						'api_v_password||密码必须由字母、数字或下划线组成',
				),
		);
		
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		$uid = do_cache('get', 'forgotsavekey', $ipt_list['savekey']);
		
		if (!$uid) {
			api_result(1, '可能是间隔时间过长，请重新发起找回密码');
		}
		
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$update_data = array(
				'password' => md5($ipt_list['password']),
				'ut' => time(),
		);
		
		$ret = $pub_mod->updateRow($uid, $update_data);
		
		if (!$ret) {
			api_result(1, '数据库错误，请重试', $data);
		}
		
		api_result(0, '重置密码成功');
	}

	private function do_login($user,$mregister='',$ipt_list='') {
		$login_mod = Factory::getMod('login');
		$sessid = $login_mod->setLogin($user);
	   if($mregister && $ipt_list){ //表示手机端注册
			require_once COMMON_PATH.'/libs/ChuanglanSmsHelper/ChuanglanSmsApi.php';
			$clapi  = new ChuanglanSmsApi();   
			$password =  $ipt_list['password']; 
			$data ="您好，您的密码是".$password ." ,  欢迎注册亿七购商城，感谢您的支持，如有疑请关注亿七购公众平台（yiqigou668）！如非本人操作，请忽略本短信。"; 			 
			$result = $clapi->sendSMS($ipt_list['name'], $data,'true');
			$result = $clapi->execResult($result);   
			if(isset($result[1]) && $result[1]==0){
			 	api_result(0, '登录成功,密码已发送到手机', api_get_output_user_data($user), $sessid);
			}else{			 
			    api_result(2, '登录成功,密码发送到手机失败！请记住密码：'.$password, api_get_output_user_data($user), $sessid);
			}  
		}


		api_result(0, '登录成功', api_get_output_user_data($user), $sessid);
	}
	
	private function do_upload($key) {
		
		include_once COMMON_PATH.'libs/LibFile.php';
		
		$lib_file = new LibFile();
		$lib_file->setMaxsize(2560);
		$ret = $lib_file->doUploadBase64($key);
		
		if ($ret['code'] != 0) {
			api_result(1, $ret['msg']);
		}
		
		$ret['data'] = $ret['data'][0];
		
		// 到这里，表示上传成功啦
		// 开始做图片裁剪
		$iconinfo = api_cut_iconname($ret['data']['url']);
		
		include_once CORE_ROOT.'/class/util/image/thumb.class.php';
		$thumb = new ThumbHandler();
		$thumb->setSrcImg($ret['data']['abs_path']);
		$thumb->setCutType(1);
		$thumb->setDstImg(UPLOAD_PATH.$iconinfo['icon']);
		$thumb->createImg(120,120);
		
		$thumb->setSrcImg($ret['data']['abs_path']);
		$thumb->setDstImg(UPLOAD_PATH.$iconinfo['iconraw']);
		$thumb->createImg(640,640);
		
		$ret = array(
				'icon' => C('UPLOAD_DOMAIN').$iconinfo['icon'],
				'iconraw' => C('UPLOAD_DOMAIN').$iconinfo['iconraw'],
				'iconinfo' => $iconinfo,
		);
		
		return $ret;
	}

    public function code(){
        $code = Factory::getMod('code');
        session_start();
        $code->doimg();
        do_cache('set','code',session_id().'_reg',$code->getCode());
    }
}
