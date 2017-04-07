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
                    <h2 class="title">指定中奖名单</h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <a class="btn-orange btn" style="margin-left:5px;" data-toggle="modal" data-target="#add">+添加指定中奖名单</a>
                            <a href="?c=activity&a=assignList&award=1" class="btn-orange btn" style="margin-left:5px;">只看中奖用户</a>
                            <a href="?c=activity&a=assignList&award=0" class="btn-orange btn" style="margin-left:5px;">查看全部用户</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>用户名</th>
                            <th>账号</th>
                            <th>密码</th>
                            <th>中奖记录</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td><?php echo $val['nick']?></td>
                                    <td><?php echo $val['user']?></td>
                                    <td><?php echo $val['pwd'];?></td>
                                    <td><?php echo $val['lucky_num']? $val['lucky_num']:'-';?></td>
                                    <td>
                                        <a class="js-edit <?php echo $val['stat']?'green':''?>" data-id="<?php echo $val['id']?>"><?php echo $val['stat']?'开启':'关闭';?>指定中奖名单</a>
                                    </td>
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


<!--添加指定名单-->
<div class="dm-modal modal fade" id="add">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                    <li><h4>指定中奖名单</h4></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="tab-content">
                        <div class="form-group row">
                            <label class="control-label">中奖名单：</label>
                            <div class="control-cont">
                                <div class="dm-textarea">
                                    <textarea name="list" class="form-control" placeholder="添加用户名，每行一个" style="width: 235px;height: 100px"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer tac">
                            <button class="btn-orange btn-large btn js-add">确定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('activity/assign');
</script>
</body>
</html>
