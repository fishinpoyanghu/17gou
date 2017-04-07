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
                    <h2 class="title">抽奖记录</h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <div class="input-group dp-input" style="float:left;">
                                <input class="form-control input-sm" name="keyword" data-type="search" type="text" placeholder="按用户名搜索" value="<?php echo $data['keyword']?>" />
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" id="searchBtn"><i class="icon-search"></i></button>
                                </span>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>用户</th>
                            <th>奖品</th>
                            <th>获得积分</th>
                            <th>时间</th>
                            <th>是否发送</th>
                            <th>地址</th>
                            <th>收件人</th>
                            <th>电话</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td><?php echo $val['nick']?></td>
                                    <td><?php echo $val['name']?></td>
                                    <td><?php echo $val['point'];?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['ut']);?></td>
                                    <td <?php if(!$val['send']) echo 'style="color:#03a9f5"'?>><?php echo $val['send']?'已发送':'未发送';?></td>
                                    <td><?php echo empty($val['address'])?'-':$val['address'];?></td>
                                    <td><?php echo empty($val['receive'])?'-':$val['receive'];?></td>
                                    <td><?php echo empty($val['phone'])?'-':$val['phone'];?></td>
                                    <td>
                                        <?php if($val['point'] == 0 && !$val['send']){?>
                                            <a data-id="<?php echo $val['id']?>" class="js-entering" data-toggle="modal" data-target="#express">录入物流信息</a>
                                        <?php }else{echo '-';}?>
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


<!--录入物流信息-->
<div class="dm-modal modal fade" id="express">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                    <li><h4>录入物流信息</h4></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="tab-content">
                        <div class="form-group" style="margin: 20px 0 30px;width:230px;">
                            <label class="control-label">物流公司：</label>
                            <div class="control-cont ">
                                <select name="express" class="dp-select">
                                    <?php foreach($data['express'] as $e){?>
                                        <option value="<?php echo $e['name']?>"><?php echo $e['name']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin: 20px 0 30px;">
                            <label class="control-label js-text">快递单号：</label>
                            <div class="control-cont">
                                <span class="dm-input dib">
                                    <input name="logistics_num" type="text" class="form-control ipt-width-mid" />
                                </span>
                            </div>
                        </div>
                        <div class="form-footer tac">
                            <button class="btn-orange btn-large btn js-express">确定</button>
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
    seajs.use('goods/lottery');
</script>
</body>
</html>
