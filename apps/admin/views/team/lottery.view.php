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
<div class="container">
    <div class="main">
        <div class="main-inner">
            <div class="main-container">
                <div class="dp-page-head">
                    <h2 class="title">抽奖设置</i></h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <a class="btn-orange btn" style="margin-left:5px;" data-toggle="modal" data-target="#add">+添加奖品</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>奖品类型</th>
                            <th>奖品名称</th>
                            <th>获得积分</th>
                            <th>中奖率</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data['info'] as $val){?>
                            <tr>
                                <td><?php echo $val['type']==1?'实物':($val['type']==2?'金钱':'积分')?></td>
                                <td><?php echo $val['name'];?></td>
                                <td><?php echo $val['point']?></td>
                                <td><input class="ipt" type="text" name="percent" data-id="<?php echo $val['id']?>" value="<?php echo $val['percent']?>">%</td>
                                <td>
                                    <a data-id="<?php echo $val['id']?>" class="js-del"><i class="icon-trash gray" title="删除"></i></a>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fl">
                            <div class="btn-group" role="group" aria-label="group">
                                <button class="btn-default btn btn-sm js-sub">提交中奖率</button>
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

<!--添加奖品-->
<div class="dm-modal modal fade" id="add">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                    <li><h4>添加奖品</h4></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="tab-content">
                        <div class="form-group" style="margin: 20px 0 30px;width:230px;">
                            <label class="control-label">奖品类型：</label>
                            <div class="control-cont ">
                                <select name="type" class="dp-select">
                                    <option value="0">积分</option>
                                    <option value="1">实物</option>
                                    <option value="2">金钱</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin: 20px 0 30px;">
                            <label class="control-label js-text">积分</label>
                            <div class="control-cont">
                                <span class="dm-input dib">
                                    <input name="content" type="text" class="form-control ipt-width-mid" />
                                </span>
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


<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('goods/lottery');
</script>
</body>
</html>
