<?php
/**
 * 微信支付成功后的通知接口
 */
file_put_contents('/tmp/yydb.log',$GLOBALS['HTTP_RAW_POST_DATA'],FILE_APPEND);

define('PROJECT_ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
define('APP_ROOT', dirname(dirname(__FILE__)));

// 定义app的名字
$app_name  = 'api';
// 定义app的根目录
$app_root  = PROJECT_ROOT.'/apps/api';

// 初始化应用
$GLOBALS['core_app_cfg'] = array(
	'app_name'  => $app_name,
	'app_root'  => $app_root
);
// 设置时区
date_default_timezone_set('Asia/Shanghai');

require_once PROJECT_ROOT.'/common/libs/wxpay/WxPay.Api.php';
require_once PROJECT_ROOT.'/core/index.php';
require_once APP_ROOT.'/common/public.inc.php';

//获取参数
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
$result = WxPayResults::Init($xml);

if($result['return_code'] == 'SUCCESS'){//支付成功
	$mod = Factory::getMod('nc_pay');
	$mod->paySuccess($result['out_trade_no'], 1, $result['transaction_id']);
	$data = array(
		'return_code' => 'SUCCESS',
		'return_msg' => 'OK'
	);
}else{
	$data = array(
		'return_code' => 'FAIL',
		'return_msg' => 'OK'
	);
}
echo toXml($data);
file_put_contents('/tmp/yydb.log',$xml."\r\n".var_export($result,true)."\r\n".var_export($data,true),FILE_APPEND);
function toXml($data){
	if(!empty($data)){
		$xml = "<xml>";
		foreach ($data as $key=>$val){
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}
}