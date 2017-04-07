<?php

class QqCtrl extends BaseCtrl {
	
	private $qq_appid = '1105435002';
	
	public function loginQq() {
		
		// 标准参数检查
		$base = api_check_base();
		
		$code = pstr('code');
		
		if (empty($code)) {
			api_result(5, 'code参数错误');
		}

        $_openInfo = explode('|||',$code);
        $openInfo = array(
            'openId' => $_openInfo[0],
            'accessToken' => $_openInfo[1],
        );
        if(empty($openInfo)){
            api_result(5, 'code参数错误');
        }

        $res = $this->do_qq_login($this->qq_appid,$openInfo);

		if ($res['code'] == 0) {
			$data = $res['data'];
			$sessid = $data['sessid'];
			unset($data['sessid']);
			
			api_result(0, $res['msg'], $data, $sessid);
		}
		else {
			api_result(1, $res['msg']);
		}
	}

	private function do_qq_login($appid, $openInfo) {



		// 判断用户是否已经在库里存在
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
		
		$where = array(
            'appid' => 10002,
            'unionid' => $openInfo['openId'],
		);
		
		$user = $pub_mod->getRowWhere($where);
		$uid = 0;
		$wxuser = false;
		if ($user) {
			$uid = $user['uid'];
				
			$where = array(
					'uid' => $uid,
			);
			$pub_mod->init('main', 'qquser', 'qq_uid');
			$wxuser = $pub_mod->getRowWhere($where);
            $first = 0;
		}
		else {
				
			$uid = get_auto_id(C('AUTOID_M_USER'));
            $url = "https://graph.qq.com/user/get_simple_userinfo?access_token={$openInfo['accessToken']}&oauth_consumer_key={$appid}&openid={$openInfo['openId']}&format=json";
            $ret = curl_page($url);
            if (empty($ret)) {
                return make_result(1, 'get user info error.');
            }
            $res_info = json_decode($ret, true);

            /*return array(
                'nickname' => $res['nickname'],
                'sex' => $res['gender']=='男' ? 1 : 0,
                'user_img' => $upload,
            );*/
			// 创建user
			$data = array(
					'uid' => $uid,
					'appid' => 10002,
					'name' => $res_info['nickname'],
					'nick' => $res_info['nickname'],
					'icon' => $res_info['figureurl'],
					'sex' => $res_info['gender']=='男' ? 1 : 0,
					'type' => 2,
					'unionid' => $openInfo['openId'],
					'rt' => time(),
					'ut' => time(),
                'ip' => ip2long($_SERVER['REMOTE_ADDR']),

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
				
			/*// 更新 $wxuser
			$update_wxdata = array(
					'wx_openid' => $res_token['openid'],
					'wx_nickname' => $res_info['nickname'],
					'wx_sex' => $res_info['sex'],
					'wx_province' => $res_info['province'],
					'wx_city' => $res_info['city'],
					'wx_country' => $res_info['country'],
					'wx_headimgurl' => $res_info['headimgurl'],
					'access_token' => $res_token['access_token'],
					'refresh_token' => $res_token['refresh_token'],
					'ut' => time(),
			);
				
			$wxuser = array_merge($wxuser, $update_wxdata);
				
			$pub_mod->init('main', 'qquser', 'qq_uid');
			$pub_mod->updateRow($wxuser['wx_uid'], $update_wxdata);*/
		}
		else {
				if($res_info){
                    $wxuid = get_auto_id(C('AUTOID_M_QQUSER'));

                    // 创建wxuser
                    $wxuser = array(
                        'qq_uid' => $wxuid,
                        'uid' => $uid,
                        'qq_openid' => $openInfo['openId'],
                        'qq_nickname' => $res_info['nickname'],
                        'qq_gender' => $res_info['gender']=='男' ? 1 : 0,
                        'qq_figureurl' => $res_info['figureurl'],
                        'rt' => time(),
                        'ut' => time(),
                    );

                    $pub_mod->init('main', 'qquser', 'qq_uid');
                    $pub_mod->createRow($wxuser);
                }

		}
		
		// 如果存在，直接设置登录
		if ($user) {
				
			if (empty($user['nick'])) $user['nick'] = $wxuser['wx_nickname'];
			if (empty($user['icon'])) {
				$user['icon'] = $wxuser['qq_figureurl'];
				$user['iconraw'] = $wxuser['qq_figureurl'];
			}
			else {
				$iconinfo = ap_user_icon_url($user['icon']);
				$user['icon'] = $iconinfo['icon'];
				$user['iconraw'] = $iconinfo['iconraw'];
			}
			if (empty($user['sex'])) $user['sex'] = $wxuser['qq_gender'];
				
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

    }

    private function do_pc_login($code){
        $appid = '1105628453';
        $token = 'YLhPpeLcXmhDCbc4';
        // 通过code获取access_token
        $url = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id='.$appid.'&client_secret='.$token.'&code='.$code.'&redirect_uri=http%3a%2f%2fwww.1yyunke.com%2fapps%2fapi%2fwww%2findex.php%3fc%3dqq%26a%3dpclogin';
        $ret = curl_page($url);
        if (empty($ret)) {
            return make_result(1, 'get access token error.');
        }
        parse_str($ret,$res_token);
        $url2 = 'https://graph.qq.com/oauth2.0/me?access_token='.$res_token['access_token'];
        $ret2 = curl_page($url2);
        if (empty($ret2)) {
            return make_result(1, 'get openid error.');
        }
        $openid = json_decode(substr(str_replace('callback(','',$ret2),0,-3), true);

        return $this->do_qq_login($appid,array('accessToken'=>$res_token['access_token'],'openId'=>$openid['openid']));

    }
	

}