<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>支付结果</title>
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/0.4.2/weui.min.css" />
</head>
<body>
<div style="width:100%;height: 45px;position:absolute;top:0;left:0;text-align: center;color:#fff;background-color:#f44336;line-height:45px;font-size:20px;">支付结果</div>
<?php if($data['flag']==1){ ?>
<div style="text-align:center;margin-top:80px;width:100%;height:100px;">
    <img src="<?php echo W_PATH; ?>/img/pay_succ.jpg" alt="" style="width:100px;height:100px;">
</div>
    <p style="font-size:18px;text-align:center;color:#333;margin-top:15px;">支付成功</p>
<?php }else{ ?>
<div style="text-align:center;margin-top:80px;width:100%;height:100px;">
    <img src="<?php echo W_PATH; ?>/img/pay_fail.jpg" alt="" style="width:100px;height:100px;">
</div>
    <p style="font-size:18px;text-align:center;color:#333;margin-top:15px;">支付失败</p>
<?php } ?>

<div style="width:100%;height: 45px;position:absolute;bottom:80px;left:0;text-align: center;">
  <button style="width:95%;height: 45px;background-color:#f44336;border:none;color:#fff;font-size:16px;border-radius:8px;" onclick="clickUrl()">返回应用</button>
</div>

<script>
  clickUrl = function(){
    window.location.replace(
      'dmsafariYunke:@'+"params"
    );
  }
</script>
</body>
</html>