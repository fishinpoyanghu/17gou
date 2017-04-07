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
                    <h2 class="title">系统消息</h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <div class="input-group dp-input" style="float:left;">
                                <input class="form-control input-sm" name="keyword" data-type="search" type="text" placeholder="按标题搜索" value="<?php echo $data['keyword']?>" />
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" id="search"><i class="icon-search"></i></button>
                                </span>
                            </div>
                            <a href="?c=activity&a=editSysMsg" class="btn-orange btn" style="margin-left:5px;">+发送消息</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" />标题</th>
                            <th>内容</th>
                            <th>发布时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <div class="fl">
                                            <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['msg_sys_id'];?>" />
                                        </div>
                                        <div class="dp-pic-and-txt fl">
                                            <?php echo $val['title']?>
                                        </div>
                                    </td>
                                    <td><?php echo $val['content']?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['ut']);?></td>
                                    <td>
                                        <a href="?c=activity&a=editSysMsg&id=<?php echo $val['msg_sys_id']?>" >编辑</a> |
                                        <a data-id="<?php echo $val['msg_sys_id']?>" class="js-del">删除</a>
                                    </td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fl">
                            <div class="btn-group" role="group" aria-label="group" id="multi_operate" style="display: none">
                                <button class="btn-default btn btn-sm" onclick="location.reload();"><i class="icon-refresh"></i></button>
                                <button class="btn-default btn btn-sm js-multi"><i class="icon-trash"></i> 批量删除</button>
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
    seajs.use('activity/sys_msg');
</script>
</body>
</html>
