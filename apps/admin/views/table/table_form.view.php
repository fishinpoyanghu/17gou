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
                    <h2 class="title"><?php echo $form_title;?></i></h2>
                    <div class="right-item vercenter-wrap" style="top:11px;">
                    	<div class="item dib vercenter">
                    	<?php if (!(isset($hide_cancel) && $hide_cancel)) {?>
                    		<a href="javascript:history.go(-1);" title="返回"><i class="icon-reply gray-dark"></i></a>
                    	<?php }?>
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
