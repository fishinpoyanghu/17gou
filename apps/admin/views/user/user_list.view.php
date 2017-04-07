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
                            
                        分润用户：  <select class="dp-select"  >  
                                    <?php foreach ($commission_user as $key => $value){ ?>
                                      <option value=" <?php echo $value['uid'];?>" >  <?php echo $value['nick'];?></option>  
                                    <?php }?>  
                            </select>  
 
                        </div>
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
                            <th>用户UID</th>
                            <th>头像</th>
                            <th>帐号</th>
                            <th>昵称</th> 
                           
                            <th>注册时间</th>
                            <th>金额</th>
                            <th>佣金</th>
                            <th>微信openid</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                   <td><?php echo $val['uid']?></td>
                                <td>
                                <p><?php echo $val['icon'];?> </p>
                                 </td> 
                                  <!--   <td>
                                            <?php  if($val['type']==0){
                                                        $q= '手机注册';
                                                }else if($val['type']==1){
                                                        $q='微信';
                                                
                                                } 
                                                echo $q;
                                                ?>
                                              </td> -->
                                    <td><?php echo $val['name'];?></td>
                                    <td><?php echo $val['nick'];?></td>
                                  
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                    <td><?php echo $val['money'];?></td>
                                    <td><?php echo $val['yongjin'];?></td>
                                    <td><?php echo $val['wx_openid'];?></td>
                                    <td>
                                        <?php if($data['login_user']['is_super']){?>
                                             <a data-id="<?php echo $val['uid']?>" class="js-addmoney">添加金额</a> |
                                             <a data-id="<?php echo $val['uid']?>" class="js-reset">重置密码</a>   |
                                              <?php  if(!in_array($val['uid'],$data['commission_uid'])){   
                                                      echo  "<a data-id='".$val['uid']."' data-c='1' class='js-commission'>分佣</a>";
                                                }else {
                                                      echo  "<a data-id='".$val['uid']."' data-c='2' class='js-commission'>取消分佣</a>"; 
                                                } ?>

                                        <?php } ?>
                                       
                               

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
    seajs.use('user/user_list');
</script>
</body>
</html>
