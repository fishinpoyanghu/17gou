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
                    <div class="dp-nav">
                        <ul class="dp-nav-inner">
                            <li>
                                <a href="?c=activity&a=comment" <?php if($data['state'] == 0)echo 'class="current"'?>>全部</a>
                            </li>
                            <li>
                                <a href="?c=activity&a=comment&state=1" <?php if($data['state'] == 1)echo 'class="current"'?>>未审核</a>
                            </li>
                            <li>
                                <a href="?c=activity&a=comment&state=2" <?php if($data['state'] == 2)echo 'class="current"'?>>不通过</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" />用户</th>
                            <th>评论内容</th>
                            <th>发布时间</th>
                            <th>审核状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data['list'] as $val){?>
                            <tr>
                                <td>
                                    <div class="fl">
                                        <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['id'];?>" />
                                    </div>
                                    <div class="dp-pic-and-txt fl">
                                        <?php echo $val['nick']?>
                                    </div>
                                </td>
                                <td><?php echo $val['text']?></td>
                                <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                <td><?php echo $val['state'];?></td>
                                <td>
                                    <?php if($val['stat'] != 2){?>
                                    <a data-id="<?php echo $val['id']?>" class="js-com-pass">通过</a> |
                                    <?php }?>
                                    <?php if($val['stat'] != 3){?>
                                    <a data-id="<?php echo $val['id']?>" class="js-com-no-pass">不通过</a> |
                                    <?php }?>
                                    <a data-id="<?php echo $val['id']?>" class="js-com-del">删除</a>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fl">
                            <div class="btn-group" role="group" aria-label="group" id="multi_operate" style="display: none">
                                <button class="btn-default btn btn-sm" onclick="location.reload();"><i class="icon-refresh"></i></button>
                                <button class="btn-default btn btn-sm js-com-multi"><i class="icon-trash"></i> 批量删除</button>
                            </div>
                        </div>
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
    seajs.use('activity/show');
</script>
</body>
</html>
