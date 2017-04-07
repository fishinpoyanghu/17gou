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
                    <h2 class="title">商品列表</h2>
                    <div class="right-item vercenter-wrap">
                        <div class="item dib vercenter">
                            <!-- 如果$category_list有内容，显示搜索目录框 -->
                            <select class="dp-select" name="activity_type"> <!--3是限购-->
                                    <option value="0" >请选择专区</option> 
                                    <option value="1" <?php if($data['activity_type']==1) echo 'selected'?>>一元专区</option> 
                                    <option value="2" <?php if($data['activity_type']==2) echo 'selected'?>>十元专区</option>
                                    <option value="4" <?php if($data['activity_type']==4) echo 'selected'?>>二人购</option>
                                    <option value="6" <?php if($data['activity_type']==6) echo 'selected'?>>幸运购</option>
                                    <option value="7" <?php if($data['activity_type']==7) echo 'selected'?>>返现购</option>
                            </select>

                            <select class="dp-select" name="cate">
                                     <option value='0'>请选择商品分类</option>
                                    <?php foreach($data['type'] as $t){?> 
                                    <option value="<?php echo $t['goods_type_id']?>" <?php if($data['cate']==$t['goods_type_id']) echo 'selected'?>><?php echo $t['name']?></option>
                                    <?php }?>
                             </select>
                        </div>
                        <span class="dp-search-item item vercenter">
                            <div class="input-group dp-input" style="float:left;">
                                <input class="form-control input-sm" name="keyword" data-type="search" type="text" placeholder="按商品名搜索" value="<?php echo $data['keyword']?>" />
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" id="search"><i class="icon-search"></i></button>
                                </span>
                            </div>

                            <a href="?c=goods" class="btn-orange btn" id="table_add_btn" style="margin-left:5px;">+添加商品</a>
                        </span>
                    </div>

                     
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" />商品图</th>
                            <th>商品名称</th>
                            <th>商品分类</th>
                            <th>商品专区</th>
                            <th>自动开始下期</th>
                            <th>创建时间</th>
                            <th>热门度</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <div class="fl">
                                            <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['goods_id'];?>" />
                                        </div>
                                        <div class="dp-pic-and-txt fl">
                                            <img src="<?php echo $val['main_img']?>" width="54" height="54"/>
                                        </div>
                                    </td>
                                    <td><?php echo $val['title']?></td>
                                    <td><?php echo $val['name'];?></td>
                                    <td>
                                    <?php  if($val['activity_type']==1){
                                                $q= '一元专区';
                                        }else if($val['activity_type']==2){
                                                $q='十元专区';
                                        }else if($val['activity_type']==4){
                                                $q='二人云购';
                                        }else if($val['activity_type']==6){
                                                $q='幸运购';
                                        }else if($val['activity_type']==7){
                                                $q='返现购';
                                        } 
                                        echo $q;
                                        ?>
                                      </td>
                                    <td><?php echo $val['is_in_activity']==2?'是':'否';?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                    <td><?php echo $val['weight'];?></td>
                                    <td>
                                        <?php if($val['is_start']){?>
                                            <a data-id="<?php echo $val['goods_id']?>" class="js-goods-start">开始活动</a> |
                                        <?php }else{echo '开始活动 | '.'<a data-id='.$val['goods_id'].' class="js-goods-stop">结束活动 |</a>';}?>
                                        <?php if($val['is_del']){?>
                                            <a data-id="<?php echo $val['goods_id']?>" class="js-goods-del">删除</a> |
                                        <?php }else{echo '删除 | ';}?>
                                        <a href="?c=goods&id=<?php echo $val['goods_id']?>" >编辑</a>
                                     <!--    <a data-id="<?php echo $val['goods_id']?>" class="remen" ><?php echo $val['weight']>0 ? '取消热门' : '加入热门' ?></a> -->

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
    seajs.use('goods/goods');
</script>
</body>
</html>
