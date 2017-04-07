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
                    <h2 class="title">红包列表</h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <a href="?c=activity&a=editRed" class="btn-orange btn" style="margin-left:5px;">+添加红包</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" />红包价格</th>
                            <th>参与人数</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <div class="fl">
                                            <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['red_id'];?>" />
                                        </div>
                                        <div class="dp-pic-and-txt fl">
                                            <?php echo $val['price']?>
                                        </div>
                                    </td>
                                    <td><?php echo $val['need_num'];?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                    <td>
                                        <?php if(!$val['is_start']){?>
                                        <a data-id="<?php echo $val['red_id'];?>" class="js-start">开始第一期</a> |
                                        <a data-id="<?php echo $val['red_id'];?>" class="js-del">删除</a> |
                                            <a href="?c=activity&a=editRed&id=<?php echo $val['red_id']?>">编辑</a>
                                        <?php }?>
                                    </td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fl">
                            <div class="btn-group" role="group" aria-label="group" id="multi_operate" style="display: none">
                                <button class="btn-default btn btn-sm" onclick="location.reload();"><i class="icon-refresh"></i></button>
                                <button class="btn-default btn btn-sm" data-toggle="modal" data-target="#m_modify"><i class="icon-trash"></i> 批量修改</button>
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

<div class="dm-modal modal fade" id="m_modify">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                    <li><h4>批量修改</h4></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="tab-content">
                        <div class="form-group" style="margin: 20px 42px 30px;width:230px;">
                            <label class="control-label">批量修改：</label>
                            <div class="control-cont ">
                                <select name="type" class="dm-select ">
                                    <option value="2">自动开始下一期</option>
                                    <option value="1">非自动开始下一期</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-footer tac">
                            <button class="btn-orange btn-large btn js-modify">确定</button>
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
    seajs.use('activity/red');
</script>
</body>
</html>
