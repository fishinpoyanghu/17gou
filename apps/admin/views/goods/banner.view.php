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
                    <h2 class="title">banner配置</h2>
                    <div class="right-item vercenter-wrap">
                        <div class="item dib vercenter">
                            <!-- 如果$category_list有内容，显示搜索目录框 -->
                        </div>
                        <span class="dp-search-item item vercenter">
                            <a class="btn-orange btn js-add" style="margin-left:5px;">+添加banner</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>图片</th>
                            <th>排序</th>
                            <th>创建时间</th>
                            <th>创建人</th>
                            <th>状态</th>
                            <th>类型</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $val['img']?>" width="54" height="54"/>
                                    </td>
                                    <td><input class="ipt" type="text" name="sort" data-id="<?php echo $val['id']?>" value="<?php echo $val['sort'];?>"/></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                    <td><?php echo $val['cer'];?></td>
                                    <td><?php echo $val['state']?'已发布':'未发布';?></td>
                                    <td><?php if($val['type']==1){
                                            echo '1元购';
                                             }  else {
                                            echo '拼团';
                                               }
                                       
                                        ?></td>
                                    <td>
                                        <a data-id="<?php echo $val['id']?>" data-goods_id="<?php echo $val['goods_id']?>" class="js-link" data-toggle="modal" data-target="#add_link" data-url="<?php echo $val['url']; ?>"><?php echo $val['goods_id']?'修改':'添加'?>链接</a> |
                                        <a data-id="<?php echo $val['id']?>" class="js-use"><?php echo $val['state']?'关闭':'发布';?></a> |
                                        <a data-id="<?php echo $val['id']?>" class="js-del">删除</a>
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

<!--添加链接-->
<div class="dm-modal modal fade" id="add_link">
    <div class="modal-dialog modal-sm" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul class="nav nav-tabs">
                    <li><h4>添加链接</h4></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="tab-content">
                        <div class="form-group" style="margin: 20px 0 30px;width:230px;">
                            <label class="control-label">商品名称：</label>
                            <div class="control-cont">
                                <select name="goods" class="js-user-select " style="width:400px;">
                                      <option value="">请选择商品！</option>
                                    <?php foreach($data['goods_list'] as $goods){?>
                                        <option value="<?php echo $goods['goods_id']?>"><?php echo $goods['title']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin: 20px 0 30px;width:230px;">
                            <label class="control-label">外部链接：</label>
                            <div class="control-cont">
                                <input type="text" name="url" value="" />
                                如需链接到商品，此项请留空
                            </div>
                        </div>
                         <div class="form-group" style="margin: 20px 0 30px;width:230px;">
                            <label class="control-label"> 类型：</label>
                            <div class="control-cont">
                                <select name="type">
                               <option value="1">1元购</option>
                               <option value="2">拼团</option>
                               </select>
                                 
                            </div>
                        </div>
                        <div class="form-footer tac">
                            <button class="btn-orange btn-large btn js-add-link">确定</button>
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
    seajs.use('goods/banner');
</script>
</body>
</html>
