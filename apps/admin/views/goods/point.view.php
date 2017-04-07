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
                                <a href="?c=goods&a=pointRule">积分规则</a>
                            </li>
                            <li>
                                <a href="?c=goods&a=pointRuleDetail" class="current">规则设置</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal" id="submit_form">
                        <div class="form-group">
                            <div class="dm-textarea textarea-index" style="height: 652px;">
                                <script type="text/plain" id="detail"></script>
                            </div>
                        </div>
                    </form>
                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
                            <button class="btn-orange btn-small btn js-sub" style="margin-left: 500px"><i class="icon-ok"></i> 提交</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
</div>
<!-- container end -->
<script>
    window.STATIC_LIST = "<?php echo SYS_STATIC_URL?>";
    window.DETAIL_CONTENT = '<?php echo $data['point']?>';
</script>

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('goods/point');
</script>
</body>
</html>
