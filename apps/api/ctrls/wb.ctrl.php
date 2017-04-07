<?php

class WbCtrl extends BaseCtrl {
	
	private $wb_appid = '989077055';
	
	public function loginWb() {
		
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

        $res = $this->do_wb_login($this->wb_appid,$openInfo);

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

	private function do_wb_login($appid, $openInfo) {

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
			$pub_mod->init('main', 'wbuser', 'wb_uid');
			$wxuser = $pub_mod->getRowWhere($where);
            $first = 0;
		}
		else {
				
			$uid = get_auto_id(C('AUTOID_M_USER'));
            $url = 'https://api.weibo.com/2/users/show.json?uid='.$openInfo['openId'].'&access_token='.$openInfo['accessToken'];

            /*$upload = LibFile::getRemoteImage($res['profile_image_url'],PIC_UPLOAD_DIR, PIC_UPLOAD_URL,1000);
            return array(
                'nickname' => $res['name'],
                'sex' => $res['gender']=='m' ? 1 : ($res['gender'] == 'f' ? 0 : 2),
                'user_img' => $upload,
            );*/
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
					'name' => $res_info['name'],
					'nick' => $res_info['name'],
					'icon' => $res_info['profile_image_url'],
					'sex' => $res_info['gender']=='m' ? 1 : ($res_info['gender'] == 'f' ? 0 : 2),
					'type' => 3,
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
                    $wxuid = get_auto_id(C('AUTOID_M_WBUSER'));

                    // 创建wxuser
                    $wxuser = array(
                        'wb_uid' => $wxuid,
                        'uid' => $uid,
                        'wb_openid' => $openInfo['openid'],
                        'wb_nickname' => $res_info['name'],
                        'wb_gender' => $res_info['gender']=='m' ? 1 : ($res_info['gender'] == 'f' ? 0 : 2),
                        'wb_figureurl' => $res_info['profile_image_url'],
                        'rt' => time(),
                        'ut' => time(),
                    );

                    $pub_mod->init('main', 'wbuser', 'wb_uid');
                    $pub_mod->createRow($wxuser);
                }

		}
		
		// 如果存在，直接设置登录
		if ($user) {
				
			if (empty($user['nick'])) $user['nick'] = $wxuser['wx_nickname'];
			if (empty($user['icon'])) {
				$user['icon'] = $wxuser['wb_figureurl'];
				$user['iconraw'] = $wxuser['wb_figureurl'];
			}
			else {
				$iconinfo = ap_user_icon_url($user['icon']);
				$user['icon'] = $iconinfo['icon'];
				$user['iconraw'] = $iconinfo['iconraw'];
			}
			if (empty($user['sex'])) $user['sex'] = $wxuser['wb_sex'];
				
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
	

}