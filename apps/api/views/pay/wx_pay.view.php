<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>确认支付</title>
    <link rel="stylesheet" href="http://res.wx.qq.com/open/libs/weui/0.4.2/weui.min.css" />
</head>
<body>
    <style>
		*{
			font-family: "Microsoft YaHei";
		}
		#btn{
			width:96%;
			height:50px;
			background-color: #07BF05;
			cursor: pointer;
			color: white;
			font-size: 20px;
			border-radius: 4px;
			border-color: #07BF05;
		}
		#title{
			font-size: 16px;
		    line-height: 30px;
		    font-weight: 600;
		}
		#money{
		    font-size: 35px;
		    line-height: 30px;
			font-family: Helvetica;
		}
		#shop-info{
			background-color: #fff;
		    width: 100%;
		    height: 40px;
		    margin: 20px 0px;
		    border-top: 2px solid #ECECEC;
		    border-bottom: 2px solid #ECECEC;
			line-height: 40px;
			font-size: 14px;
		}
		body{
			background-color: whitesmoke;
		}
	</style>
    <div align="center" style="margin-top:10px;">
    	<span id="title">购买商品</span>
    </div>
    <div align="center">
    	<span id="money">￥<?php echo $money;?></span>
    </div>
    <div align="center" id="shop-info">
    	<span style="color:#ABABAB;margin-right:20px;">收款方 亿七购</span>
    	<span></span>
    </div>
	<div align="center">
		<a href="javascript:;" id="btn" class="weui_btn_primary weui_btn" onclick="callpay()" >立即支付</a>
	</div>

	<div class="weui_dialog_alert" id="dialog2" style="display: none;">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_bd"></div>
            <div class="weui_dialog_ft">
                <a href="javascript:;" class="weui_btn_dialog primary" onclick="closeAlert()">确定</a>
            </div>
        </div>
    </div>

	<script src="//cdn.bootcss.com/zepto/1.1.6/zepto.min.js"></script>
	<script type="text/javascript">
		var order_num = '<?php echo $order_num;?>',
			key = '<?php echo $key;?>',
			msg,code;
		//执行支付
		function jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApi; ?>,
				function(res){
					if(res.err_msg.indexOf('ok') > 0){
						msg = '支付成功';
						code = 1;
					}else if(res.err_msg.indexOf('cancel') > 0){
						msg = '取消支付';
						code = 2;
					}else{
						msg = '支付失败';
						code = 3;
					}
					$('.weui_dialog_bd').text(msg);
					$('#dialog2').show();
				}
			);
		}

		//调起支付
		function callpay(){
			if (typeof WeixinJSBridge == "undefined"){
				if( document.addEventListener ){
					document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
				}else if (document.attachEvent){
					document.attachEvent('WeixinJSBridgeReady', jsApiCall);
					document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
				}
			}else{
				jsApiCall();
			}
		}

		//关闭弹窗
		function closeAlert(){
			$.post('?c=nc_pay&a=operate_record',{order_num:order_num,key:key,code:code});
			$('#dialog2').hide();
			if(code == 1){
				history.go(-1);
			}
		}
	</script>
</body>
</html>