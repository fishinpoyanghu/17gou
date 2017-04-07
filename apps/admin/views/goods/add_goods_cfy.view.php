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
    <div class="main">
        <div class="main-inner">
            <div class="main-container">
                <div class="dp-page-head">
                    <h2 class="title">编辑分类</i></h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal">
                        <input type="hidden" name="id" value="<?php echo $data['info']['id']?>">
                        <input type="hidden" name="pid" value="<?php echo $data['pid']?>">
                        <div class="form-group row">
                            <label class="control-label col-sm-2">分类名称：</label>
                            <div class="col-sm-10">
                                <div class="input-group dp-input ipt-width-long">
                                    <input type="text" name="name" class="form-control input-sm" value="<?php echo $data['info']['name']?>"/>
                                </div>
                            </div>
                        </div>
                       
                    </form>
                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
                            <a href="javascript:history.go(-1)" class="btn-small btn"><i class="icon-reply"></i> 取消</a>
                            <button class="btn-orange btn-small btn js-sub"><i class="icon-ok"></i> 提交</button>
                        </div>
                    </div>
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
    seajs.use('goods/goods');
</script>
</body>
</html>
