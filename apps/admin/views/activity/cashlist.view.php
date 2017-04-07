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
                    <h2 class="title">提现列表</h2>
                    <div class="right-item vercenter-wrap">
                        <span class="dp-search-item item vercenter">
                            <div class="input-group dp-input" style="float:left;">
                                <input class="form-control input-sm" name="keyword" data-type="search" type="text" placeholder="按用户名搜索" value="<?php echo $data['keyword']?>" />
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" id="search"><i class="icon-search"></i></button>
                                </span>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>用户名</th>
                            <th>用户现有佣金</th>
                            <th>提现金额(提现一次1元手续费)</th>
                            <th>订单号</th>
                            <th>申请时间</th>
                            <th>审批信息</th> 
                            <th>付款类型</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td><?php echo $val['nick']?></td>
                                     <td><?php echo $val['yongjin']?></td>
                                    <td><?php echo $val['money']?></td>
                                    <td><?php echo $val['order_num']?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td> 
                                    <td><?php echo  $val['msg']?></td> 
                                    <td>   
                                       <?php if($val['type']==1){
                                             echo ' 微信付款';
                                        }else{
                                             echo '银行卡付款';
                                        }
                                        ?> 
                                      </td> 
                                    <td> 
                                    <?php if($val['status']==1){
                                            echo ' 等待审核';
                                        }else if($val['status']==2){
                                            echo '已批准，等待打款';
                                        }else if($val['status']==3){
                                            echo '不批准';
                                        }else if($val['status']==4){
                                            echo '已打款';
                                        }else if($val['status']==5){
                                            echo '系统不通过';
                                        }
                                        ?> 
                                    </td>

                                    <td><?php if($val['status']==1){?>
                                        <a data-id="<?php echo $val['id']?>" class="js-pass">批准</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a data-id="<?php echo $val['id']?>" class="js-nopass">不批准</a> 
                                       
                                        <?php }else{ }?></td>
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
    seajs.use('activity/cashlist');
</script>
</body>
</html>
