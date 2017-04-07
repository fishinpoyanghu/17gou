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
                    <h2 class="title">管理员列表</h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <a class="btn-orange btn" style="margin-left:5px;" data-toggle="modal" data-target="#add">+添加管理员</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>用户名</th>
                            <th>创建时间</th>
                            <th>权限</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td><?php echo $val['name']?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                    <td><?php echo $val['type']? '超级管理员':'普通管理员';?></td>
                                    <td>
                                        <a class="js-admin <?php if(!$val['type'])echo 'red'?>" data-id="<?php echo $val['admin_id']?>">设为<?php echo $val['type']?'普通':'超级';?>管理员</a> |
                                        <a class="js-stat" data-id="<?php echo $val['admin_id']?>"><?php echo $val['stat']?'解封':'封禁';?></a>
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

<!--添加管理员-->
<div class="dm-modal modal fade" id="add">
    <div class="modal-dialog modal-sm" style="width: 535px">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                    <li><h4>添加管理员</h4></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="tab-content">
                        <div class="form-group">
                            <label class="control-label js-text">账户：</label>
                            <div class="control-cont">
                                <span class="dm-input dib">
                                    <input name="name" type="text" class="form-control ipt-width-long" placeholder="账号只能是手机号码"/>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label js-text">密码：</label>
                            <div class="control-cont">
                                <span class="dm-input dib">
                                    <input name="password" type="password" class="form-control ipt-width-long" placeholder="密码必须同时包含数字和字母，不低于6位"/>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">权限：</label>
                            <div class="control-cont ">
                                <select name="type" class="dp-select">
                                    <option value="0">普通管理员</option>
                                    <option value="1">超级管理员</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-cont js-msg"></div>

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
    seajs.use('user/admin');
</script>
</body>
</html>
