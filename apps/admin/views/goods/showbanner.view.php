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
    <link rel="stylesheet" href="<?php echo SYS_STATIC_URL.'/css/select2.min.css'?>">
    <style>
        .ipt {
            width: 60px;
            text-align: center;
            height: 25px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
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
                    <h2 class="title">晒单banner配置</h2>
                    <div class="right-item vercenter-wrap">
                        <div class="item dib vercenter">
                            <!-- 如果$category_list有内容，显示搜索目录框 -->
                        </div>
                    </div>
                </div>
                <label class="control-label">更换图片：</label>
                <div class="add-pic dib one-pic">
                    <div class="item add-pic-button">
                        <a class="btn-add js-upload-one icon-plus" data-target="#modal_piclib">
                            <i class="icon-plus dp-icon" style="margin-top: 16px"></i>
                        </a>
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
    <?php include '../views/public/menu.view.php';?>
</div>
<!-- container end -->

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('goods/showbanner');
</script>
</body>
</html>
