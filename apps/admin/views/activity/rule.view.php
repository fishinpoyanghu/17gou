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
                    <h2 class="title">规则设置</h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal" id="submit_form">
                        <div class="form-group">
                            <label class="control-label">亿七购：</label>
                            <div class="dm-textarea textarea-index" style="height: 240px;margin-left: 90px;">
                                <script type="text/plain" id="detail"></script>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">抢红包：</label>
                            <div class="dm-textarea textarea-index" style="height: 240px;margin-left: 90px;">
                                <script type="text/plain" id="red"></script>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">抽奖规则：</label>
                            <div class="dm-textarea textarea-index" style="height: 230px;margin-left: 90px;">
                                <script type="text/plain" id="lottery"></script>
                            </div>
                        </div>
                    </form>
                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
                            <button class="btn-orange btn-small btn js-sub" style="margin-left: 370px"><i class="icon-ok"></i> 提交</button>
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
    window.ONE_CONTENT = '<?php echo $data['one']?>';
    window.RED_CONTENT = '<?php echo $data['red']?>';
    window.LOTTERY_CONTENT = '<?php echo $data['lottery']?>';
</script>

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('activity/rule');
</script>
</body>
</html>
