<?php
/**
 * 短信api
 */

class SmsCtrl extends BaseCtrl {
	

	public function sendRegMcode() {
		
		// 调用测试用例
// 		$this->test_send_reg_mcode();
		
		// 标准参数检查
		$base = api_check_base();
		
		$validate_cfg = array(
				'mobile' => array(
						'api_v_mobile||手机号码不合法',
				),
            'code' => array(
                 'api_v_notnull||验证码不能为空',
            ),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

		 
        session_start();

        $code = do_cache('get','code',session_id().'_reg');
        if(strtolower($ipt_list['code'])!=$code || $code==''){
            api_result(1, '验证码错误');
        }

        do_cache('delete','code',session_id().'_reg');

		// 判断手机号码是否已经被注册
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');

		$where = array(
				'appid' => $base['appid'],
				'name' => $ipt_list['mobile'],
		);
		$user = $pub_mod->getRowWhere($where);
		
		if ($user) {
			api_result(1, '该号码已经被注册，请直接登录');
		}
		
		// 每60s只可以发送一次
		$ret = do_cache('get', 'mcode', $ipt_list['mobile']);
		if ($ret && ((time()-$ret['last']) < 60)) {
			api_result(1, '发送太频繁，请稍后重试');
		}
		
		// 每天最多发送5次
		if ($ret && ($ret['day'] >= 5)) {
			api_result(1, '每天最多发送5次');
		}
		
		// 到这里，表示可以发送了
		$code = $this->chuanglanSms($ipt_list['mobile']);
		
		$cache_ary = array(
				'code' => $code,
				'last' => time(),
				'day'  => $ret['day']+1,
		);
		$ret = do_cache('set', 'mcode', $ipt_list['mobile'], $cache_ary);
		
		api_result(0, '发送成功');
	}
	
	public function sendForgotMcode() {
		
		// 标准参数检查
		$base = api_check_base();
	
		$validate_cfg = array(
				'mobile' => array(
						'api_v_mobile||手机号码不合法',
				),
            'code' => array(
                 'api_v_notnull||验证码不能为空',
            ),
		);
	
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);

        session_start();

        $code = do_cache('get','code',session_id().'_reg');
        if(strtolower($ipt_list['code'])!=$code || $code==''){
            api_result(1, '验证码错误');
        }

        

        do_cache('delete','code',session_id().'_reg'); 
         
		// 判断手机号是否是被注册过的
		$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');
	
		$where = array(
				'appid' => $base['appid'],
				'name' => $ipt_list['mobile'],
		);
		$user = $pub_mod->getRowWhere($where);
	
		if (!$user) {
			api_result(1, '这个号码没有注册过');
		}
	
		// 每60s只可以发送一次
		$ret = do_cache('get', 'forgotcode', $ipt_list['mobile']);
		if ($ret && ((time()-$ret['last']) < 60)) {
			api_result(1, '发送太频繁，请稍后重试');
		}
	
		// 每天最多发送5次
		if ($ret && ($ret['day'] >= 5)) {
			api_result(1, '每天最多发送5次');
		}
	
		// 到这里，表示可以发送了
		$code = $this->chuanglanSms($ipt_list['mobile']);
	
		$cache_ary = array(
				'code' => $code,
				'last' => time(),
				'day'  => $ret['day']+1,
				'uid'  => $user['uid'],
		);
		$ret = do_cache('set', 'forgotcode', $ipt_list['mobile'], $cache_ary);
	
		api_result(0, '发送成功');
	}
	public function sendBindMcode(){
		$base = api_check_base();
		
		$validate_cfg = array(
				'mobile' => array(
						'api_v_mobile||手机号码不合法',
				),
            'code' => array(
                 'api_v_notnull||验证码不能为空',
            ),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg); 
        session_start(); 
        $code = do_cache('get','code',session_id().'_reg');
        if(strtolower($ipt_list['code'])!=$code || $code==''){
            api_result(1, '验证码错误');
        }

        do_cache('delete','code',session_id().'_reg');

		// 判断手机号码是否已经被注册绑定手机暂时未定用户数量
		/*$pub_mod = Factory::getMod('pub');
		$pub_mod->init('main', 'user', 'uid');

		$where = array(
				'appid' => $base['appid'],
				'name' => $ipt_list['mobile'],
		);
		$user = $pub_mod->getRowWhere($where);
		
		if ($user) {
			api_result(1, '该号码已经被注册，请直接登录');
		}
		*/
		// 每60s只可以发送一次
		$ret = do_cache('get', 'mcode', $ipt_list['mobile']);
		if ($ret && ((time()-$ret['last']) < 60)) {
			api_result(1, '发送太频繁，请稍后重试');
		}
		
		// 每天最多发送5次
		if ($ret && ($ret['day'] >= 5)) {
			api_result(1, '每天最多发送5次');
		}
		
		// 到这里，表示可以发送了
		$code = $this->chuanglanSms($ipt_list['mobile']);
		
		$cache_ary = array(
				'code' => $code,
				'last' => time(),
				'day'  => $ret['day']+1,
		);
		$ret = do_cache('set', 'mcode', $ipt_list['mobile'], $cache_ary);
		
		api_result(0, '发送成功');
	}
	
/*	private function _generate_code($phone) {
		
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
        $result = curl_page($url, 1, http_build_query($data), 10, 10);
        $info = json_decode($result,true);
        if($info['state']){
            return $code;
        }else{
            api_result(1, '发送失败'.$info['msg']);
        }
		
	}*/ 
	/**   创蓝短信接口 */
	private function chuanglanSms($phone) {   
		require_once COMMON_PATH.'/libs/ChuanglanSmsHelper/ChuanglanSmsApi.php';
		$clapi  = new ChuanglanSmsApi();   
		//$code='888888';
		//$data ="您好，您的验证码是" . $code ; 
		$code = mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
		//$phone='13431379649';
		$data ="您好，您的验证码是".$code ." ,  欢迎注册亿七购商城，感谢您的支持，如有疑请关注亿七购公众平台（yiqigou668）！如非本人操作，请忽略本短信。"; 
		//'【亿七购】您的验证码为：'.$code
		$result = $clapi->sendSMS($phone, $data,'true');
		$result = $clapi->execResult($result);   
		if(isset($result[1]) && $result[1]==0){
			 return $code;
		}else{
			//echo "发送失败{$result[1]}";
			api_result(1, '发送失败'.$result[1]);
		}  
	}

}
