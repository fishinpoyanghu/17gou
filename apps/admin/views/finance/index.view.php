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
                                <a href="?c=finance" class="current">平台流水</a>
                            </li>
                            <li>
                                <a href="?c=finance&a=consume">消费记录</a>
                            </li>
                            <li>
                                <a href="?c=finance&a=recharge">充值记录</a>
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
                            <button class="btn-orange btn js-screen">查询</button>
                        </span>
                    </div>
                </div>
                <div class="article-edit">
                    <div id="main" style="width: 100%;height:600px;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
</div>
<!-- container end -->


<script>
    window._TIME_ = <?php echo $data['time']?>;
    window._DATA_ = <?php echo $data['data']?>;
    window._TOTAL_ = <?php echo $data['total']?>;
</script>

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('finance/index');
</script>
</body>
</html>
