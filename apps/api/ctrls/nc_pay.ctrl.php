<?php
/**
 * @since 2016-01-13
 * note 付费相关
 */
class NcPayCtrl extends BaseCtrl{

    private $alipay_partnerid = '2088421757537010';
    private $partner = 'tuxuansheng@163.com';
    private $alipay_privatepath = 'libs/alipay/rsa_private_key.pem';
    private $alipay_publicpath = 'libs/alipay/alipay_public_key.pem';


    private $wx_mchid = '1372637502';
    private $wxappid = 'wx13e34e2d2afead85';

	
	/**
	 * 微信支付页面
	 */
	public function wxPay(){
		require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';
		require_once COMMON_PATH.'libs/wxpay/WxPay.JsApiPay.php';
		
		//获取openid
		$tools = new JsApiPay();
		$openId = $tools->GetOpenid();  
		if(!$openId){
            unset($_GET['code']);
            $openId = $tools->GetOpenid();
        }
		//获取订单号
		$order_num = gstr('order_num');
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'order');
		$where = array(
			'order_num' => $order_num,
			'flag' => 0
		);
		$column = array(
			'money_info','flag'
		);
		$orderInfo = $nc_list_mod->getDataList($where);
		//是否已经支付
		if(empty($orderInfo)){
			echo "已经支付";
			exit;
		}
		$moneyInfo = json_decode($orderInfo[0]['money_info'], true);
		$money = $moneyInfo['need_money'];
		if($money == 0){
			echo "无需支付";
			exit;
		}
		
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody("购买商品");
		$input->SetOut_trade_no($order_num);
		$input->SetTotal_fee($money*100);
		$input->SetNotify_url(A_PATH."/wx_notify.php");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		
		$order = WxPayApi::unifiedOrder($input);
		$jsApiParameters = $tools->GetJsApiParameters($order);
		$data = array(
			'jsApi' => $jsApiParameters,
			'money' =>  sprintf("%.2f",$money),
			'order_num' => $order_num,
			'key' => md5($order_num.'key')
		);
		Factory::getView("pay/wx_pay", $data);
	}
	
	/**
	 * 第三方支付页面操作记录
	 */
	public function operateRecord(){
		$order_num = pstr('order_num');
		$key = pstr('key');
		$code = pint('code');
		$md5 = md5($order_num.'key');
		if($md5 != $key) exit;
		
		$where = array(
			'order_num' => $order_num,
		);
		$data = array(
			'transaction_flag' => $code
		);
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'order');
		$nc_list_mod->updateData($where, $data);
	}
	
	/**
	 * 无需额外支付时使用的接口
	 */
	public function noPay(){
		
		//测试使用
		//$this->test_post_data(array('order_num' => '2016012220044810000000060'));
		/*$a[]='2016122716524610000073086';
        
        $mod = Factory::getMod('nc_pay');
        foreach($a as $v){
            $mod->paySuccess($v);
        }*/
		//标准参数检查
		$base = api_check_base();
		$validate_cfg = array(
			'order_num' => array(),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'order');
		$where = array(
			'order_num' => $ipt_list['order_num'],
			'flag' => 0
		);
		$column = array(
			'money_info','flag','uid'
		);
		
		$orderInfo = $nc_list_mod->getDataList($where);
		if(empty($orderInfo)){
			api_result(1, '无该订单，或者订单已经支付');
		}
		$moneyInfo = json_decode($orderInfo[0]['money_info'], true);
		//判断订单是否真的无需额外支付
		if(!empty($moneyInfo['need_money'])){
			api_result(1, '该订单需额外支付');
		}
		//判断是否是用户自己提交订单
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		if($login_user['uid'] != $orderInfo[0]['uid']){
			api_result(9, '没有权限');
		}

        if($moneyInfo['remain_use']>bcadd($login_user['money'],$login_user['yongjin'],2)){
            api_result(9, '当前余额不足以支付订单!');
        }
		$mod = Factory::getMod('nc_pay');
		$re = $mod->paySuccess($ipt_list['order_num']);
		if($re){
			api_result(0, '支付成功');
		}else{
			api_result(1, '支付失败');
		}
	}
	
	/**
	 * 充值接口
	 */
	public function recharge(){
		//标准参数检查
		$base = api_check_base();
		$validate_cfg = array(
			'pay_money' => array(
				'api_v_numeric|1||pay_money不合法',
			),
			'pay_type' => array(
				'api_v_numeric|1||pay_type不合法',
			),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		//判断金额
		if($ipt_list['pay_money'] <= 0){
			api_result(1, "金额不正确");
		}
		
		$nc_pay = Factory::getMod('nc_pay');
		
		$data = $nc_pay->doRecharge($base, $ipt_list);
		if(empty($data)){
			api_result(1, '下单失败');
		}else{
			api_result(0, '下单成功', $data);
		}
	}

       public function alPay(){
        $order_num = gstr('order_num');
        $src = gstr('src');
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'order');
        $where = array(
            'order_num' => $order_num,
            'flag' => 0
        );

        $orderInfo = $nc_list_mod->getDataList($where);
        //是否已经支付
        if(empty($orderInfo)){
            echo "已经支付";
            exit;
        }
        $moneyInfo = json_decode($orderInfo[0]['money_info'], true);
        $money = $moneyInfo['need_money'];
        if($money == 0){
            echo "无需支付";
            exit;
        }
        require_once COMMON_PATH.'libs/alipay/alipay_submit.class.php';

        if($src == 'pc'){
            $return_url = PC_PATH.'/#!/balance_detail';
        }elseif($src == 'pc2'){
            $return_url = PC_PATH.'/#!/payResult/payOrder_'.$order_num;
        }else{
            $return_url = W_PATH."/#/payResult/".$order_num;
        }
        $notify_url = A_PATH.'/ali_notify.php';

        $alipay_config['partner'] = $this->alipay_partnerid;
        $alipay_config['seller_id'] =$this->alipay_partnerid;
        $alipay_config['private_key_path'] =COMMON_PATH.$this->alipay_privatepath;
        $alipay_config['ali_public_key_path']= COMMON_PATH.$this->alipay_publicpath;
        $alipay_config['sign_type'] = strtoupper('RSA');
        $alipay_config['input_charset']= strtolower('utf-8');
        $alipay_config['transport']    = 'http';
        //当签约账号就是收款账号时，请务必使用参数seller_id，即seller_id的值与partner的值相同。 这里需要注意
        //service改成手机支付只需service 改成 alipay.wap.create.direct.pay.by.user
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_id" => trim($alipay_config['partner']),
            "payment_type"  => "1",
            "notify_url"    => $notify_url,
            "return_url"    => $return_url,
            "out_trade_no"  => $order_num,
            "subject"   => '购买商品',
            "total_fee" => $money,
            "body"  => '购买商品',
            "_input_charset" => trim(strtolower('utf-8')),
        );

        $alipaySubmit = new AlipaySubmit($alipay_config);
        $form_text = $alipaySubmit->buildRequestForm($parameter,"get", "");
        Factory::getView("pay/alipay", array('form_text'=>$form_text));

    }

    public function alPay2(){
        $order_num = gstr('order_num');
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'order');
        $where = array(
            'order_num' => $order_num,
            'flag' => 0
        );

        $orderInfo = $nc_list_mod->getDataList($where);
        //是否已经支付
        if(empty($orderInfo)){
            echo "已经支付";
            exit;
        }
        $moneyInfo = json_decode($orderInfo[0]['money_info'], true);
        $money = $moneyInfo['need_money'];
        if($money == 0){
            echo "无需支付";
            exit;
        }
        require_once COMMON_PATH.'libs/alipay/alipay_submit.class.php';

        $return_url = W_PATH."/#/payResult/".$order_num;
        $notify_url = A_PATH.'/ali_notify.php';
        //建立请求
        $alipay_config = array(
            'sign_type' => 'RSA',
            'private_key_path' => COMMON_PATH.$this->alipay_privatepath,
        );
        $alipay_config['partner'] = $this->alipay_partnerid;
        $alipay_config['seller_id']	=$this->partner;
        $parameter = array(
            "service" => "mobile.securitypay.pay",
            "partner" => trim($alipay_config['partner']),
            "_input_charset"	=> 'utf-8',
            'sign_type' => 'RSA',
            'notify_url' => $notify_url,
            'out_trade_no' => $order_num,
            'subject' => '购买商品',
            'payment_type' => '1',
            'seller_id' => trim($alipay_config['seller_id']),
            'total_fee' => round($money,2),
            'body' => '购买商品',
        );
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestParaToString($parameter);
        api_result(0,'succ',array('param'=>$html_text));
    }


    public function wxPay2(){
        require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';

        //获取订单号
        $order_num = gstr('order_num');
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'order');
        $where = array(
            'order_num' => $order_num,
            'flag' => 0
        );
        $column = array(
            'money_info','flag'
        );
        $orderInfo = $nc_list_mod->getDataList($where);
        //是否已经支付
        if(empty($orderInfo)){
            echo "已经支付";
            exit;
        }
        $moneyInfo = json_decode($orderInfo[0]['money_info'], true);
        $money = $moneyInfo['need_money'];
        if($money == 0){
            echo "无需支付";
            exit;
        }

        $input = new WxPayUnifiedOrder();
        $input->SetAppid($this->wxappid);
        $input->SetMch_id($this->wx_mchid);
        $input->SetBody('购买商品');
        $input->SetOut_trade_no($order_num);
        $input->SetTotal_fee($money*100);
        $input->SetAttach('app');  // wtf fix
        $input->SetNotify_url(A_PATH."/wx_notify.php");
        $input->SetTrade_type('APP');
        $order = WxPayApi::unifiedOrder($input,6,WxPayConfig::APPKEY);

        if($order['return_code']!='SUCCESS' || $order['result_code']!='SUCCESS'){
            api_result(1,$order['return_msg']);
        }
        //生成客户端所需信息
        $output = new WxPayAppUnifiedOrder();
        $output->SetAppid($this->wxappid);
        $output->SetPartnerid($this->wx_mchid);
        $output->SetPrepayid($order['prepay_id']);
        $output->SetPackage('Sign=WXPay');
        $output->SetNoncestr(md5(mt_rand()));
        $output->SetTimestamp(time());
        $output->SetSign(WxPayConfig::APPKEY);
        $data = $output->GetValues();
        api_result(0,'succ',array('param'=>$data));

    }

    public function wxPay3(){
        require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';

        //获取订单号
        $order_num = gstr('order_num');
        $nc_list_mod = Factory::getMod('nc_list');
        $nc_list_mod->setDbConf('shop', 'order');
        $where = array(
            'order_num' => $order_num,
            'flag' => 0
        );
        $column = array(
            'money_info','flag'
        );
        $orderInfo = $nc_list_mod->getDataList($where);
        //是否已经支付
        if(empty($orderInfo)){
            echo "已经支付";
            exit;
        }
        $moneyInfo = json_decode($orderInfo[0]['money_info'], true);
        $money = $moneyInfo['need_money'];
        if($money == 0){
            echo "无需支付";
            exit;
        }

        $input = new WxPayUnifiedOrder();
        $input->SetBody('购买商品');
        $input->SetAttach('h5');
        $input->SetOut_trade_no($order_num);
        $input->SetTotal_fee($money*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 1200));
        $input->SetNotify_url(A_PATH."/wx_notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($order_num);

        $input->SetSpbill_create_ip(get_ip());
        $input->SetNonce_str(md5(mt_rand()));//随机字符串
        $order = WxPayApi::unifiedOrder($input);
        if($order['return_code']!='SUCCESS' || $order['result_code']!='SUCCESS'){
            echo $order['return_msg'];
            exit;
        }
        $url2 = $order["code_url"];
        //api_result(0,'succ',array('data'=>$url2));
        require COMMON_PATH.'/libs/phpqrcode/phpqrcode.php';
        QRcode::png($url2,false,QR_ECLEVEL_L,5);
        exit;
    }



}