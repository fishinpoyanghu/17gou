<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telphone=no, email=no" />
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
                                <a href="?c=finance&a=yijian" class="current">公告管理</a>
                            </li>
                        </ul>
                    </div>
                    <div class="right-item vercenter-wrap">

                    </div>
                </div>

                <div class="article-edit">
                    <form class="form-horizontal" id="submit_form">
                        <input type="hidden" name="id" value="<?php echo $data['id']?>">
                        <div class="form-group row">
                            <label class="control-label">标题：</label>
                            <div class="control-cont">
                                <label><input type="text" name="title" class="form-control ipt-width-long" value="<?php echo $data['info']['title'] ?>"/></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">标签：</label>
                            <div class="control-cont">
                                <label><input type="text" name="name" class="form-control ipt-width-long" value="<?php echo $data['info']['name'] ?>"/></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">内容：</label>
                            <div class="control-cont">
                                <textarea name="content" style="height: 400px;width: 100%;"><?php echo str_replace('<br>',"\n",$data['info']['content']); ?></textarea>
                            </div>
                        </div>

                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
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


<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('finance/notice_edit');
</script>
</body>
</html>
