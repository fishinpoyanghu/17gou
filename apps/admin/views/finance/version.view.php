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
                                <a href="?c=finance" class="current">版本控制</a>
                            </li>
                           
                        </ul>
                    </div>
                    <div class="article-edit">
                        <form class="form-horizontal" id="submit_form" action="?c=finance&a=savev" method="post">

                            <div class="form-group row">
                                <label class="control-label">版本号：</label>
                                <div class="control-cont">
                                    <div class="dm-input">
                                        <label><input type="text" name="v" class="form-control ipt-width-long" value="<?php echo $data['info']['v']?>"/></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label">下载链接：</label>
                                <div class="control-cont">
                                    <div class="dm-input">
                                        <label><input type="text" name="url" class="form-control ipt-width-long" value="<?php echo $data['info']['url']?>"/></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label">安装包大小（Mb）：</label>
                                <div class="control-cont">
                                    <div class="dm-input">
                                        <label><input type="text" name="size" class="form-control ipt-width-long" value="<?php echo $data['info']['size']?>"/></label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label">简介：</label>
                                <div class="control-cont">
                                    <div class="dm-textarea">
                                        <textarea name="desc" class="form-control" style="width: 360px;height: 100px"><?php echo $data['info']['desc']?></textarea>
                                    </div>
                                </div>
                            </div>

                        <div class="form-operate vercenter-wrap">
                            <div class="vercenter">
                                <button class="btn-orange btn-small btn js-sub" type="submit"><i class="icon-ok"></i> 提交</button>
                            </div>
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
</script>
</body>
</html>
