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
                                <a href="?c=finance&a=yijian" class="current">意见反馈</a>
                            </li>
                        </ul>
                    </div>
                    <div class="right-item vercenter-wrap">

                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>时间</th>
                            <th>用户昵称</th>
                            <th>内容</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data['list'] as $val){?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s',$val['ut'])?></td>
                                <td><?php echo $val['nick']?></td>
                                <td><?php echo $val['yijian']?></td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fr">
                            <nav class="dp-page-right">
                                <?php echo $data['page_content'];?>
                            </nav>
                            <div class="page-cnt">共<?php echo $data['page_total']?>条，每页<?php echo $data['page_num']?>条</div>
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
