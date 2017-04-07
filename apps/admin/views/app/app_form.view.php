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
  <div class="container">
    <div class="main">
        <div class="main-inner">
            <div class="main-container">
                <div class="dp-page-head">
                    <div class="left-item">
		                <div class="dp-nav">
		                    <ul class="dp-nav-inner">
		                        <li>
		                            <a<?php if ($type == 'wx') {?> class="current"<?php }?> href="/?c=app&a=pay">微信APP支付</a>
		                        </li>
		                        <li>
		                            <a<?php if ($type == 'wxmp') {?> class="current"<?php }?> href="/?c=app&a=pay&type=wxmp">微信公众号支付</a>
		                        </li>
		                        <!--
		                        <li>
		                            <a href="?ct=info&ac=unionSet">银联支付</a>
		                        </li>
		                        <li>
		                            <a href="?ct=info&ac=alipaySet">支付宝支付</a>
		                        </li>
		                        -->
		                    </ul>
		                </div>
		            </div>
                </div>
                <div class="article-edit">
                	<?php include '../views/form/base.view.php';?>
                </div>
            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
  </div>
  <!-- container end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
seajs.use('table/table_form', function(t) {
	t.init('<?php echo $form_id;?>');
});

seajs.use('table/editor', function(t) {
	t.init();
});
</script>
</body>
</html>
