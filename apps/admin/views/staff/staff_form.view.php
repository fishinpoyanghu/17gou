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
  <style type="text/css">
  .article-edit .form-operate {margin-left:0px;text-align:center;}
  </style>
</head>

<body>
  <!-- header start -->
  <!-- header end -->
  <!-- container start -->
  <div class="container">
    <div class="main">
        <div class="main-inner" style="margin-left:0px;">
            <div class="main-container">
                <div class="article-edit">
                	<?php include '../views/form/base.view.php';?>
                </div>
            </div>
        </div>
    </div>
  </div>
  <!-- container end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
seajs.use('form/staff_form', function(t) {
	t.init('<?php echo $form_id;?>');
});

seajs.use('table/editor', function(t) {
	t.init();
});
</script>
</body>
</html>
