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
                    <div class="dp-nav">
                        <ul class="dp-nav-inner">
                         <!--    <li>
                                <a href="?c=activity" <?php if($data['type'] == 1)echo 'class="current"'?>>一元活动记录</a>
                            </li>
                            <li>
                                <a href="?c=activity&type=2" <?php if($data['type'] == 2)echo 'class="current"'?>>十元活动记录</a>
                            </li> -->
                            
                            <li style="float:right;">
                              <span class="dp-search-item item vercenter">
                              <div class="input-group dp-input" style="float:left;">
                                <input class="form-control input-sm" name="keyword" data-type="search" type="text" placeholder="按商品名搜索" value="<?php echo $data['keyword']?>" />
                                 <input type="hidden" id='type_id' value='<?php  echo $data["type"];?>'>
                                <span class="input-group-btn">
                                    <button class="btn btn-sm btn-default" id="search"><i class="icon-search"></i></button>
                                </span>
                            </div>
                            
                        </span>
                            </li>
                        </ul>

                        
                    </div>
                        
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" />参团团号</th>
                            <th>商品</th>
                            <th>中奖记录</th>
                            <th>活动状态</th>
                            <th>开始时间</th>
                            <th>结束时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <div class="fl">
                                            <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['teamwar_id'];?>" />
                                        </div>
                                        <div class="dp-pic-and-txt fl">
                                            <?php echo $val['teamwar_id']?>
                                        </div>
                                    </td>
                                    <td><?php echo $val['title']?></td>
                                    <td><?php echo $val['lucky_num'],'--',$val['nick'];?></td>
                                    <td><?php  if($val['flag']==1){
                                            echo '默认未成团';
                                        }else if($val['flag']==2){
                                            echo '人数不够自动结束'; 
                                        }else if($val['flag']==3){
                                             echo '后台手动结束下架';    
                                        }else if($val['flag']==7){
                                             echo '成团';          
                                         }else if($val['flag']==8){
                                             echo '已揭晓';          
                                        }    
                                        ?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['ut']);?></td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['et'])?></td>
                                    <td>
                                      
					       <?php if($val['flag'] ==1){?>
                                                <a data-id="<?php echo $val['teamwar_id']?>" class="js-end">结束活动</a> |
                                            <?php }else{echo "结束活动 |";}?>
                                            <a target="_blank" href="?c=team&a=record&id=<?php echo $val['teamwar_id']?>&type=<?php echo $data['type']?>">查看参与记录</a>
                                      
                                            
                                        
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


<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('team/activity');
</script>
</body>
</html>
