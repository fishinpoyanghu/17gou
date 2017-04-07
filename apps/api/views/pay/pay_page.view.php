<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>确认支付</title>
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/0.4.2/weui.min.css" />
</head>
<body>
<div style="width:100%;height: 45px;position:absolute;top:0;left:0;text-align: center;color:#fff;background-color:#f44336;line-height:45px;font-size:20px;">确认支付</div>
<div style="text-align:center;margin-top:80px;width:100%;height:100px;">
    <span style="font-size:18px;color:#333;margin-top:15px;display: inline-block;width:200px;">订单金额</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;¥<?php echo $data['money'];?></span>
    <br>
    <span style="font-size:18px;color:#333;margin-top:15px;display: inline-block;width:200px;">选择支付方式</span>
    <select>
        <option value="1">余额支付</option>
    </select>
</div>

<div style="width:100%;height: 45px;position:absolute;bottom:80px;left:0;text-align: center;">
    <a  href="?c=app&a=pay&order_num=<?php echo $data['order'];?>&sign=<?php echo $data['sign'];?>"><button style="width:95%;height: 45px;background-color:#f44336;border:none;color:#fff;font-size:16px;border-radius:8px;">立即支付</button></a>
</div>

</body>
</html>