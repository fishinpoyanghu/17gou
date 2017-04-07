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
                    <h2 class="title">商品分类管理</h2>
                    <div class="right-item vercenter-wrap">
                        <div class="item dib vercenter">
                            <!-- 如果$category_list有内容，显示搜索目录框 -->
                        </div>
                        <span class="dp-search-item item vercenter">
                            <div class="input-group dp-input" style="float:left;">
                                <input class="form-control input-sm" name="keyword" data-type="search" type="text" placeholder="按分类名搜索" value="<?php echo $data['keyword']?>" />
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" id="searchBtn"><i class="icon-search"></i></button>
                                </span>
                            </div>
                            <a href="?c=team&a=addGoodsCfy" class="btn-orange btn" id="table_add_btn" style="margin-left:5px;">+添加分类</a>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" />分类ID</th>
                            <th>排序</th>
                            <th>分类名</th>
                            <th>上级分类id</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <div class="fl">
                                            <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['goods_type_id'];?>" />
                                        </div>
                                        <div class="dp-pic-and-txt fl">
                                            <?php echo $val['goods_type_id'];?>
                                        </div>
                                    </td>
                                    <td><?php echo $val['sort'];?></td>
                                    <td><?php echo $val['name'];?></td>
                                    <td><?php echo $val['father_id'];?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                    <td>
                                        <a href="?c=team&a=addGoodsCfy&id=<?php echo $val['goods_type_id']?>" style="margin-right:3px;"><i class="icon-pencil gray-dark" title="编辑"></i></a>
                                        <a data-id="<?php echo $val['goods_type_id']?>" class="js-del"><i class="icon-trash gray" title="删除"></i></a>
                                        <?php if($val['shou']==0){ ?>  <a data-id="<?php echo $val['goods_type_id']?>" class="tianjia" >上PC首页</a><?php }else{ ?><a class="qudiao" data-id="<?php echo $val['goods_type_id']?>" >下PC首页</a><?php } ?>
                                    </td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fl">
                            <div class="btn-group" role="group" aria-label="group" id="multi_operate" style="display: none">
                                <button class="btn-default btn btn-sm" onclick="location.reload();"><i class="icon-refresh"></i></button>
                                <button class="btn-default btn btn-sm" id="multi_delete"><i class="icon-trash"></i> 批量删除</button>
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
    seajs.use('team/teamclassif');
</script>
</body>
</html>
