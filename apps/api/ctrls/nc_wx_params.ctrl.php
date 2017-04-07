<?php
/**
 * @since 2016-01-26
 */
class NcWxParamsCtrl extends BaseCtrl{
	
	/**
	 * 获取wx.config时的参数
	 */
	public function wxConfig(){
		require_once COMMON_PATH.'libs/wxpay/Wx.Api.php';
		//测试使用
		//api_testcase_base(10003,'DDV02-N710UJ-2MR2G-K2DXK-9103C');
		
		//标准参数检查
		$base = api_check_base();
		//判断是否是用户自己提交订单
		//$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$validate_cfg = array(
			'url' => array(),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		
		if(empty($ipt_list['url'])){
			api_result(5, '参数错误');
		}
		
		$wxApi = new WxApi();
		$signature = $wxApi->getSign($ipt_list['url']);
		$data = array(
			'appId' => WxPayConfig::APPID,
			'timestamp' => $wxApi->getTimestamp(),
			'nonceStr' => $wxApi->getNoncestr(),
			'signature' => $signature
		);
		api_result(0, '获取成功', $data);
	}
	
	/**
	 * 微信支付参数
	 */
	public function wxPay(){
		require_once COMMON_PATH.'libs/wxpay/WxPay.Api.php';
		require_once COMMON_PATH.'libs/wxpay/WxPay.JsApiPay.php';
		
		//标准参数检查
		$base = api_check_base();
		$validate_cfg = array(
			'order_num' => array(),
		);
		$ipt_list = api_get_posts($base['appid'], $validate_cfg);
		$login_user = app_get_login_user($base['sessid'], $base['appid'], true);
		
		$nc_list_mod = Factory::getMod('nc_list');
		$nc_list_mod->setDbConf('shop', 'order');
		$where = array(
			'order_num' => $ipt_list['order_num'],
			'flag' => 0,
			'uid' => $login_user['uid']
		);
		$column = array(
			'money_info'
		);
		$orderInfo = $nc_list_mod->getDataOne($where);
		//是否已经支付
		if(empty($orderInfo)){
			echo "已经支付";
			exit;
		}
		$moneyInfo = json_decode($orderInfo['money_info'], true);
		$money = $moneyInfo['need_money'];
		if($money == 0){
			echo "无需支付";
			exit;
		}
		//$money *= 100;
		$money = 1;
		$ipt_list['order_num'] = '2016012617295410000000063';
		
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody("购买商品");
		$input->SetOut_trade_no($ipt_list['order_num']);
		$input->SetTotal_fee($money);
		$input->SetNotify_url("http://api.dooplus.cn/wx_notify.php");
		$input->SetTrade_type("APP");
		
		$order = WxPayApi::unifiedOrder($input);
		printPre($order);exit;
		$tools = new JsApiPay();
		$jsApiParameters = $tools->GetJsApiParameters($order);
		
	}
}