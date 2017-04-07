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
</head>

<body>
  <!-- header start -->
  <?php include '../views/public/header.view.php';?>
  <!-- header end -->
  <!-- container start -->
  <div class="container">
  	<div class="main" >
  		<div class="main-header">
  			<h3 class="title">我的APP</h3>
        </div>
        <div class="main-inner" style="margin-left: 40px;margin-right:40px;">
            <div class="app-container main-container">
            	<!--
            	<div class="dp-page-head">
                    <div class="left-item vercenter-wrap">
                        <div class="vercenter">
                            <button class="btn-large btn-green btn" data-toggle="modal" data-target="#modal_addApp">+ 新建App</button>
                        </div>
                    </div>
                </div>
                <div class="dp-page-head">
                    <h3 class="title">广州days哈哈哈公司</h3>
                </div>
                -->
                <div class="app-list">
                    <ul class="list clearfix">
                    <?php 
                    foreach ($app_list as $app) {
                    ?>
                    	<li><a class="btn-large btn" href="<?php app_echo_url('?c=goods&a=goodsList&'.get_appid_uri($app['appid']));?>" title="创建：admin&#10;最后修改：admin"><?php echo $app['name'];?></a></li>
                    <?php 
                    }
                    ?>
                    </ul>
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
// seajs.use('table/table_list', function(t) {
// 	t.init();
// });
</script>
</body>
</html>
