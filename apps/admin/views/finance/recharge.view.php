<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telphone=no, email=no" />
    <link rel="dns-prefetch" href="//bigh5.com">
    <?php include '../views/public/head.view.php';?>
    <?php echo '<link rel="stylesheet" href="'.C('SITE_DOMAIN').'/wangEditor-1.3.12.css?'.C('VERSION_CSS').'">';?>

</head>

<body>
<!-- header start -->
<?php include '../views/public/header.view.php';?>
<!-- header end -->
<!-- container start -->
<!-- container start -->
<div class="container">
    <div class="main">
        <div class="main-inner">
            <div class="main-container">
                <div class="dp-page-head">
                    <div class="dp-nav">
                        <ul class="dp-nav-inner">
                            <li>
                                <a href="?c=finance" >平台流水</a>
                            </li>
                            <li>
                                <a href="?c=finance&a=consume">消费记录</a>
                            </li>
                            <li>
                                <a href="?c=finance&a=recharge" class="current">充值记录</a>
                            </li>
                        </ul>
                    </div>
                    <div class="right-item vercenter-wrap">
                        <span class="item vercenter">
                            <span>时间查询：</span>
                            <span class="dp-input dib">
                                <input name="start" type="text" class="form-control js_picker" value="<?php echo $data['start']?>" readonly="" style="width: 80px">
                            </span>
                            <span>到</span>
                            <span class="dp-input dib">
                                <input name="end" type="text" class="form-control js_picker" value="<?php echo $data['end']?>" readonly="" style="width: 80px">
                            </span>
                            <span class="dp-input dib">
                            <input class="form-control ipt-width-short" type="text" name="keyword" placeholder="请输入用户名" value="<?php echo $data['keyword']?>">
                            </span>
                            <button class="btn-orange btn" id="search">搜索</button>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>充值时间</th>
                            <th>用户</th>
                            <th>充值金额</th>
                            <th>账户余额</th>
                            <th>充值渠道</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data['list'] as $val){?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s',$val['ut'])?></td>
                                <td><?php echo $val['nick']?></td>
                                <td><?php $money = json_decode($val['money_info'],true); echo $money['need_money'];?></td>
                                <td><?php echo $val['money']?></td>
                                <td><?php echo $val['pay_type']==1?'微信':'支付宝';?></td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fr">
                            <nav class="dp-page-right">
                                <?php echo $data['page_content'];?>
                            </nav>
                            <div class="page-cnt">共<?php echo $data['page_total']?>条，每页<?php echo $data['page_num']?>条</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
</div>
<!-- container end -->


<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('finance/recharge');
</script>
</body>
</html>
