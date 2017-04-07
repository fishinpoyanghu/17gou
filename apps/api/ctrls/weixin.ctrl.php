<?php
/**
 * 微信api
 */

class WeixinCtrl extends BaseCtrl {
	
	// 定义微信的token
	private $wx_token = 'dayskeifdls';
	
	// 定义微信的appid
	private $wx_appid = 'wx7319e2fdd0cbf364';
	
	// 定义微信的appsecret
	private $wx_appsecret = '686b8ee735da4433f1d326633e798e18';
	
	// 微信接口stat
	private $state = 'weixin';
	
	/*public function loginWx() {
		
		// 标准参数检查
		$base = api_check_base();
		
		$code = pstr('code');
		
		if (empty($code)) {
			api_result(5, 'code参数错误');
		}
		
		$res = $this->do_wx_login($base['appid'], $code);
		
		if ($res['code'] == 0) {
			$data = $res['data'];
			$sessid = $data['sessid'];
			unset($data['sessid']);
			
			api_result(0, $res['msg'], $data, $sessid);
		}
		else {
			api_result(1, $res['msg']);
		}
	}*/

    public function loginWx(){
        // 标准参数检查
        $base = api_check_base();

        $open = pstr('openId');
        $token = pstr('accessToken');
        $invite_code = pstr('invite_code');
        if (empty($open) || empty($token)) {
            api_result(5, '参数错误');
        }

        // 获取用户的头像、昵称等信息
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$open.'&lang=zh_CN';

        $ret = curl_page($url);
        if (empty($ret)) {
            return make_result(1, 'get user info error.');
        }
        
        $res_info = json_decode($ret, true);
        $res_info['nickname']= preg_replace_callback('/./u', function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
         $res_info['nickname']); //过滤表情http://www.phpchina.com/portal.php?mod=view&aid=40005
        // 判断用户是否已经在库里存在
        $pub_mod = Factory::getMod('pub');
        $pub_mod->init('main', 'user', 'uid');

        $where = array(
            'appid' => 10002,
            'unionid' => $res_info['unionid'],
        );

        $user = $pub_mod->getRowWhere($where);
        $uid = 0;
        $wxuser = false;
        if ($user) {
            $uid = $user['uid'];

            $where = array(
                'uid' => $uid,
                'appid' => 10002,
            );
            $pub_mod->init('main', 'wxuser', 'wx_uid');
            $wxuser = $pub_mod->getRowWhere($where);
            $first = 0;
        }
        else {
            $uid = get_auto_id(C('AUTOID_M_USER'));

            // 创建user
            $data = array(
                'uid' => $uid,
                'appid' => 10002,
                'name' => $res_info['openid'],
                'nick' => $res_info['nickname'],
                'icon' => $res_info['headimgurl'],
                'sex' => $res_info['sex'],
                'type' => 1,
                'unionid' => $res_info['unionid'],
                'rt' => time(),
                'ut' => time(),
                'ip' => get_ip(1),
            );
            if($invite_code){
                $a = api_decode_invite_code('10002', $invite_code);
                if($a>0){
                    $data['rebate_uid'] = $a;
                }
            }

            $pub_mod->init('main', 'user', 'uid');
            $pub_mod->createRow($data);
            $user = $pub_mod->getRow($uid);

            /*if($user && $a>0){
                $msg_mod = Factory::getMod('msg');
                $msg_mod->sendNotify(10002, $a, 10002, 4, 0, 6, '您成功邀请'.$res_info['nickname'].'注册。');
                $_nc_list = Factory::getMod('nc_list');
                $_nc_list->setDbConf('shop', 'fenxiao');
                $ret = $_nc_list->getDataOne(array('level'=>0), array(), array(), array(), false);
                if($ret['percent']>0){
                    $insert = array(
                        'uid' => $a,
                        'money' => $ret['percent'],
                        'desc' => '邀请奖励',
                        'ut' => time(),
                        'appid' => '10002',
                    );
                    $_nc_list->setDbConf('shop', 'money');
                    $_nc_list->insertData($insert);
                    $_nc_list->setDbConf('main', 'user');
                    $sql = "update {$_nc_list->dbConf['tbl']} set `money`=`money`+{$ret['percent']} where `uid`={$a}";
                    $_nc_list->executeSql($sql);
                }
            }*/
            $first = 1;
        }
        // 判断wxuser存不存在
        if ($wxuser) {

            // 更新 $wxuser
            $update_wxdata = array(
                'wx_openid' => $res_info['openid'],
                'wx_nickname' => $res_info['nickname'],
                'wx_sex' => $res_info['sex'],
                'wx_province' => $res_info['province'],
                'wx_city' => $res_info['city'],
                'wx_country' => $res_info['country'],
                //'wx_headimgurl' => $res_info['headimgurl'],
                'access_token' => $token,
                'refresh_token' => $token,
                'ut' => time(),
            );

            $wxuser = array_merge($wxuser, $update_wxdata);

            $pub_mod->init('main', 'wxuser', 'wx_uid');
            $pub_mod->updateRow($wxuser['wx_uid'], $update_wxdata);
        }
        else {

            $wxuid = get_auto_id(C('AUTOID_M_WXUSER'));

            // 创建wxuser
            $wxuser = array(
                'wx_uid' => $wxuid,
                'appid' => 10002,
                'uid' => $uid,
                'wx_openid' => $res_info['openid'],
                'wx_nickname' => $res_info['nickname'],
                'wx_sex' => $res_info['sex'],
                'wx_province' => $res_info['province'],
                'wx_city' => $res_info['city'],
                'wx_country' => $res_info['country'],
                'wx_headimgurl' => $res_info['headimgurl'],
                'wx_unionid' => $res_info['unionid'],
                'access_token' => $token,
                'refresh_token' => $token,
                'rt' => time(),
                'ut' => time(),
            );

            $pub_mod->init('main', 'wxuser', 'wx_uid');
            $pub_mod->createRow($wxuser);
        }

        // 如果存在，直接设置登录
        if ($user) {

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

            // 到这里，让用户登录
            $login_mod = Factory::getMod('login');
            $sessid = $login_mod->setLogin($user);

            $data = api_get_output_user_data($user);
            $data['sessid'] = $sessid;
            $data['first'] = $first;
            $sessid = $data['sessid'];
            unset($data['sessid']);
            api_result(0, '登录成功', $data, $sessid);
        }
        api_result(1, '未知错误，登录失败。');

    }
	
	public function goWx() {
		
		// 标准参数检查
		//$base = api_check_base();
     	$base = array('appid'=>10002);
        $code = gstr('invite_code');
		// 得到当前的登录用户，如果已经登录，直接跳转啦
		// @todo
		
		// 判断是否是在微信打开，如果不是，给出提示
		// @todo
				
		// 先尝试到snsapi_userinfo的网页授权
		$callback_url = A_PATH . '/?c=weixin&a=get_openid&appid='.$base['appid'].'&invite_code='.$code;
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->wx_appid.'&redirect_uri='.urlencode($callback_url).'&response_type=code&scope=snsapi_userinfo&state='.$this->state.'#wechat_redirect'; 

		header("Location: ".$url);
        die;
	}
	
	public function getOpenid() {
		
		$appid = gstr('appid');
		
		$code = gstr('code');

        $state = gstr('state');
        $invite_code = gstr('invite_code');

		if ($state != $this->state) {
			dump('非法请求');exit;
		}
		
		if (empty($code)) {
			dump('没有授权');exit;
		}
		
		if (empty($appid)) {
			dump('缺少appid');exit;
		}
		
		// 到这里，表示授权成功了
		$res = $this->do_wx_login($appid, $code,$invite_code);
		if ($res['code'] == 0) {
			header("Location: ".W_PATH."/#/loginTransferPage/".$res['data']['sessid']);
		}
		else {
			dump($res['msg']);
		}
	}
	
	public function check() {
		
		if ($this->check_signature()) {
			echo gstr('echostr');
		}
	}
	
	private function do_wx_login($appid, $code,$invite_code) {
		
		// 到这里，表示授权成功了
		// 通过code获取access_token
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';
		$ret=$this->curlmsg($code);
		//$ret = curl_page($url,0,'',20,20);//延长8秒
		if (empty($ret)) {  //&r='.rand(1,9999999);
			return make_result(1, 'get access token error.微信服务器繁忙！请尝试重新登录！');
		}
		
		$res_token = json_decode($ret, true);
		// 获取用户的头像、昵称等信息
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$res_token['access_token'].'&openid='.$res_token['openid'].'&lang=zh_CN';
		
		$ret = curl_page($url);
		if (empty($ret)) {
			return make_result(1, 'get user info error.');
		}
		
		$res_info = json_decode($ret, true);
		// 判断用户是否已经在库里存在
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$where = array(
				'appid' => $appid,
				'unionid' => $res_info['unionid'],
		);
		
		$user = $pub_mod->getRowWhere($where);
		$uid = 0; 
		$wxuser = false;
		if ($user) {
			$uid = $user['uid']; 
			$where = array(
					'uid' => $uid,
					'appid' => $appid,
			);
			$pub_mod->init('main', 'wxuser', 'wx_uid');
			$wxuser = $pub_mod->getRowWhere($where);
            $first = 0;
		}
		else {
				
			$uid = get_auto_id(C('AUTOID_M_USER'));
				
			// 创建user
			$data = array(
					'uid' => $uid,
					'appid' => $appid,
					'name' => $res_info['openid'],
					'nick' => $res_info['nickname'],
					'sex' => $res_info['sex'],
					'type' => 1,
					'unionid' => $res_info['unionid'],
					'rt' => time(),
					'ut' => time(),
                    'icon' => $res_info['headimgurl'],
                'ip' => get_ip(1),
                //'money'=>1,  //注册用户默认赠送1元体验卷改为抢红包才有
			);
		if($invite_code){
            $a = api_decode_invite_code('10002', $invite_code);
            if($a>0){
                $data['rebate_uid'] = $a;
            }
        }

			$pub_mod->init('main', 'user', 'uid');
			$pub_mod->createRow($data);
		
			$user = $pub_mod->getRow($uid);

            if($user && $a>0){
                $msg_mod = Factory::getMod('msg');
                $msg_mod->sendNotify(10002, $a, 10002, 4, 0, 6, '您成功邀请'.$res_info['nickname'].'注册。');
           /*     $_nc_list = Factory::getMod('nc_list');
                $_nc_list->setDbConf('shop', 'fenxiao');
                $ret = $_nc_list->getDataOne(array('level'=>0), array(), array(), array(), false);
                if($ret['percent']>0){
                    $insert = array(
                        'uid' => $a,
                        'money' => $ret['percent'],
                        'desc' => '邀请奖励',
                        'ut' => time(),
                        'appid' => '10002',
                    );
                    $_nc_list->setDbConf('shop', 'money');
                    $_nc_list->insertData($insert);
                    $_nc_list->setDbConf('main', 'user');
                    $sql = "update {$_nc_list->dbConf['tbl']} set `money`=`money`+{$ret['percent']} where `uid`={$a}";
                    $_nc_list->executeSql($sql);
                }*/
            }
            $first = 1;
        }
		// 判断wxuser存不存在
		if ($wxuser) {
				
			// 更新 $wxuser
			$update_wxdata = array(
					'wx_openid' => $res_info['openid'],
					'wx_nickname' => $res_info['nickname'],
					'wx_sex' => $res_info['sex'],
					'wx_province' => $res_info['province'],
					'wx_city' => $res_info['city'],
					'wx_country' => $res_info['country'],
					//'wx_headimgurl' => $res_info['headimgurl'],
					'access_token' => $res_token['access_token'],
					'refresh_token' => $res_token['refresh_token'],
					'ut' => time(),
			);
				
			$wxuser = array_merge($wxuser, $update_wxdata);
				
			$pub_mod->init('main', 'wxuser', 'wx_uid');
			$pub_mod->updateRow($wxuser['wx_uid'], $update_wxdata);
		}
		else {
				
			$wxuid = get_auto_id(C('AUTOID_M_WXUSER'));
				
			// 创建wxuser
			$wxuser = array(
					'wx_uid' => $wxuid,
					'appid' => $appid,
					'uid' => $uid,
					'wx_openid' => $res_info['openid'],
					'wx_nickname' => $res_info['nickname'],
					'wx_sex' => $res_info['sex'],
					'wx_province' => $res_info['province'],
					'wx_city' => $res_info['city'],
					'wx_country' => $res_info['country'],
					'wx_headimgurl' => $res_info['headimgurl'],
					'wx_unionid' => $res_info['unionid'],
					'access_token' => $res_token['access_token'],
					'refresh_token' => $res_token['refresh_token'],
					'rt' => time(),
					'ut' => time(),
			);
		
			$pub_mod->init('main', 'wxuser', 'wx_uid');
			$pub_mod->createRow($wxuser);
            session_start();
            $_SESSION['wxregister_first']=1;
            
		}
		
		// 如果存在，直接设置登录
		if ($user) {
				
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
				
			// 到这里，让用户登录
			$login_mod = Factory::getMod('login');
			$sessid = $login_mod->setLogin($user);
			
			$data = api_get_output_user_data($user);
			$data['sessid'] = $sessid;
            $data['first'] = $first;
			
            /*$nc_activity = Factory::getMod('nc_activity');
            $backtime=$nc_activity->rebatemoney($user['unionid'],$user['uid']); //赠送金额
            $user['back_time']=$backtime?$backtime:$user['back_time'];
            if($user['back_time']+0>time()){ //登录送余额 
                $nc_activity->addloginmoney($user); 
            }*/

			return make_result(0, '登录成功', $data);
		}
		
		return make_result(1, '未知错误，登录失败。');
	}
	
	private function check_signature() {
		
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
	
		$token = $this->wx_token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
	
		if( $tmpStr == $signature ) {
			return true;
		}
		else {
			return false;
		}
	}

    public function wxQrcode(){
        require COMMON_PATH.'libs/wxpay/Wx.Api.php';
        $wxApi = new WxApi();
        $id = get_auto_id(C('AUTOID_POST'));
        $info = array();
        do_cache('set', 'wxjsapi', 'wxlogin'.$id, $info);
        $qrcode = $wxApi->getQrcode($id);
        $qrcode = json_decode($qrcode,true);
        $data = array(
            'id' => $id,
            'sign' => md5($id.LOGIN_KEY),
            'img' => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$qrcode['ticket'],
        );
        api_result(0,'succ',$data);
    }

    public function checkQrcode(){
        $id = gstr('id');
        $sign = gstr('sign2');
        if($sign != md5($id.LOGIN_KEY)){
            api_result(1,'failure');
        }

        $result = do_cache('get', 'wxjsapi', 'wxlogin'.$id);
        if(!$result || empty($result)){
            api_result(1,'fail');
        }else{
            api_result(0,'succ',$result);
        }
    }

    public function pclogin(){

        $code = gstr('code');

        if (empty($code)) {
            dump('没有授权');exit;
        }

        // 到这里，表示授权成功了
        $res = $this->do_pc_login($code);
        if ($res['code'] == 0) {
            header("Location: ".PC_PATH."/#!/loginTransferPage/".$res['data']['sessid']);
        }
        else {
            dump($res['msg']);
        }
        // 到这里，表示授权成功了

    }

    private function do_pc_login($code){
        $appid = 'wxf3905c189d8faa14';
        $token = 'c4eee15709e955a0a9b07a6203123fb2';
        // 通过code获取access_token
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$token.'&code='.$code.'&grant_type=authorization_code';

        $ret = curl_page($url);
        if (empty($ret)) {
            return make_result(1, 'get access token error.');
        }

        $res_token = json_decode($ret, true);
        // 获取用户的头像、昵称等信息
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$res_token['access_token'].'&openid='.$res_token['openid'].'&lang=zh_CN';

        $ret = curl_page($url);
        if (empty($ret)) {
            return make_result(1, 'get user info error.');
        }

        $res_info = json_decode($ret, true);
        // 判断用户是否已经在库里存在
        $pub_mod = Factory::getMod('pub');
        $pub_mod->init('main', 'user', 'uid');

        $where = array(
            'appid' => '10002',
            'unionid' => $res_info['unionid'],
        );

        $user = $pub_mod->getRowWhere($where);
        $uid = 0;
        $wxuser = false;
        if ($user) {
            $uid = $user['uid'];

            $where = array(
                'uid' => $uid,
                'appid' => '10002',
            );
            $pub_mod->init('main', 'wxuser', 'wx_uid');
            $wxuser = $pub_mod->getRowWhere($where);
            $first = 0;
        }
        else {

            $uid = get_auto_id(C('AUTOID_M_USER'));

            // 创建user
            $data = array(
                'uid' => $uid,
                'appid' => '10002',
                'name' => $res_info['openid'],
                'nick' => $res_info['nickname'],
                'sex' => $res_info['sex'],
                'type' => 1,
                'unionid' => $res_info['unionid'],
                'rt' => time(),
                'ut' => time(),
                'icon' => $res_info['headimgurl'],
                'ip' => get_ip(1),
            );


            $pub_mod->init('main', 'user', 'uid');
            $pub_mod->createRow($data);

            $user = $pub_mod->getRow($uid);

            /*if($user && $a>0){
                $msg_mod = Factory::getMod('msg');
                $msg_mod->sendNotify(10002, $a, 10002, 4, 0, 6, '您成功邀请'.$res_info['nickname'].'注册。');
                $_nc_list = Factory::getMod('nc_list');
                $_nc_list->setDbConf('shop', 'fenxiao');
                $ret = $_nc_list->getDataOne(array('level'=>0), array(), array(), array(), false);
                if($ret['percent']>0){
                    $insert = array(
                        'uid' => $a,
                        'money' => $ret['percent'],
                        'desc' => '邀请奖励',
                        'ut' => time(),
                        'appid' => '10002',
                    );
                    $_nc_list->setDbConf('shop', 'money');
                    $_nc_list->insertData($insert);
                    $_nc_list->setDbConf('main', 'user');
                    $sql = "update {$_nc_list->dbConf['tbl']} set `money`=`money`+{$ret['percent']} where `uid`={$a}";
                    $_nc_list->executeSql($sql);
                }
            }*/
            $first = 1;
        }
        // 判断wxuser存不存在
        if ($wxuser) {

            // 更新 $wxuser
            $update_wxdata = array(
                'wx_openid' => $res_info['openid'],
                'wx_nickname' => $res_info['nickname'],
                'wx_sex' => $res_info['sex'],
                'wx_province' => $res_info['province'],
                'wx_city' => $res_info['city'],
                'wx_country' => $res_info['country'],
                //'wx_headimgurl' => $res_info['headimgurl'],
                'access_token' => $res_token['access_token'],
                'refresh_token' => $res_token['refresh_token'],
                'ut' => time(),
            );

            $wxuser = array_merge($wxuser, $update_wxdata);

            $pub_mod->init('main', 'wxuser', 'wx_uid');
            $pub_mod->updateRow($wxuser['wx_uid'], $update_wxdata);
        }
        else {

            $wxuid = get_auto_id(C('AUTOID_M_WXUSER'));

            // 创建wxuser
            $wxuser = array(
                'wx_uid' => $wxuid,
                'appid' => '10002',
                'uid' => $uid,
                'wx_openid' => $res_info['openid'],
                'wx_nickname' => $res_info['nickname'],
                'wx_sex' => $res_info['sex'],
                'wx_province' => $res_info['province'],
                'wx_city' => $res_info['city'],
                'wx_country' => $res_info['country'],
                'wx_headimgurl' => $res_info['headimgurl'],
                'wx_unionid' => $res_info['unionid'],
                'access_token' => $res_token['access_token'],
                'refresh_token' => $res_token['refresh_token'],
                'rt' => time(),
                'ut' => time(),
            );

            $pub_mod->init('main', 'wxuser', 'wx_uid');
            $pub_mod->createRow($wxuser);
        }

        // 如果存在，直接设置登录
        if ($user) {

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

            // 到这里，让用户登录
            $login_mod = Factory::getMod('login');
            $sessid = $login_mod->setLogin($user);



            $data = api_get_output_user_data($user);
            $data['sessid'] = $sessid;
            $data['first'] = $first;

            return make_result(0, '登录成功', $data);
        }

        return make_result(1, '未知错误，登录失败。');
    }
    //这个curl 仅仅用于测试
    private function curlmsg($code){
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wx_appid.'&secret='.$this->wx_appsecret.'&code='.$code.'&grant_type=authorization_code';//&r='.rand(1,9999999); 
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_token_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 20);
        $res = curl_exec($ch);


        if (curl_errno ( $ch )) {
           //  echo 'Errno:' ;var_dump(curl_error ( $ch ));  
             file_put_contents('/tmp/wxlogin.log','UnifiedOrderResult:'.var_export(curl_error ( $ch ),true).date('Y-m-d H:i:s'),FILE_APPEND);
             $this->goWx();
            make_result(1, 'get access token error.微信服务器繁忙！请尝试重新登录！');
        }
        return $res;
    }

    public function binduser(){ 
        if(!$_POST['openid'] && !$_POST['pid']){
             echo json_encode(array('error_code'=>1));exit;
        }
        $pid=$_POST['pid']+0;
        $appid=10002;
        $time=time();
        require_once COMMON_PATH.'libs/wxpay/Wx.Api.php';    
        $wxApi = new WxApi();   
        
        $res_info=$wxApi->getsubscribe($_POST['openid']);  
        if(!$res_info['unionid']){
            if($res_info['errcode']=='40001'){ 
                $res_info=$wxApi->getsubscribe($_POST['openid'],1);  //强制刷新重新获取
                if(!$res_info['unionid']){
                    echo json_encode(array('error_code'=>2,'msg'=>$res_info));exit;
                }
            }
        }
    
        $nc_list=Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'user');
        $where = array( 
            'unionid' => $res_info['unionid']    
        );
        $ret = $nc_list->getDataOne($where, array('uid'), array(), array(), false);
       
        if($ret){
            echo json_encode(array('error_code'=>3));exit;
        }
        
        $res_info['nickname']= preg_replace_callback('/./u', function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $res_info['nickname']); //过滤表情http://www.phpchina.com/portal.php?mod=view&aid=40005
        $uid = get_auto_id(C('AUTOID_M_USER')); 
        // 创建user
        $data = array(
                'uid' => $uid,
                'appid' => $appid,
                'name' => $res_info['openid'],
                'nick' => $res_info['nickname'],
                'sex' => $res_info['sex'],
                'type' => 1,
                'unionid' => $res_info['unionid'],
                'rt' => $time,
                'ut' => $time,
                'icon' => $res_info['headimgurl'],
                'ip' => get_ip(1),
                'rebate_uid'=>$pid 
        );
         $nc_list->insertData($data); 
         $wxuid = get_auto_id(C('AUTOID_M_WXUSER'));
            
        // 创建wxuser
        $wxuser = array(
                'wx_uid' => $wxuid,
                'appid' => $appid,
                'uid' => $uid,
                'wx_openid' => $res_info['openid'],
                'wx_nickname' => $res_info['nickname'],
                'wx_sex' => $res_info['sex'],
                'wx_province' => $res_info['province'],
                'wx_city' => $res_info['city'],
                'wx_country' => $res_info['country'],
                'wx_headimgurl' => $res_info['headimgurl'],
                'wx_unionid' => $res_info['unionid'],
                'access_token' => '',
                'refresh_token' => '',
                'rt' => $time,
                'ut' => $time,
        );
        $nc_list->setDbConf('main', 'wxuser');
        $nc_list->insertData($wxuser); 
        $msg_mod = Factory::getMod('msg');
        $msg_mod->sendNotify(10002, $pid, 10002, 4, 0, 6, '您成功邀请'.$res_info['nickname'].'注册。');
        echo json_encode(array('error_code'=>0));exit;

    }

    public function getqrcode(){  //$uid=102958    

        $nc_wx=Factory::getMod('nc_wx'); 
        $nc_wx->dealwxqrcode();  
         
    }
    

}