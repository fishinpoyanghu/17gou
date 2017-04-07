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
                        
                    </div>
                    <div class="vercenter-wrap">
                        <span class="item vercenter">
                            <span>时间查询：</span>
                            <span class="dp-input dib">
                                <input name="start" type="text" class="form-control js_picker" value="<?php echo $data['start']?>" readonly="" style="width: 80px">
                            </span>
                            <span>到</span>
                            <span class="dp-input dib">
                                <input name="end" type="text" class="form-control js_picker" value="<?php echo $data['end']?>" readonly="" style="width: 80px">
                            </span>
                            
                            <select id='people' style="height: 32px;width:100px;">
                                    <option value='1476'>老表</option>
                                    <option value='46018'>老周</option>
                                    <option value='34938'>啊粤</option>
                                    <option value='5902'>啊旭</option>  
                                    <option value='29180'>家瑜</option>  
                                   <option value='29178'>嘉嘉</option>  
                                     
                            </select>
                             
                            
                            <button class="btn-orange btn" id="query">查询</button>
                        </span>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
                        <thead>
                        <tr>
                            <th>总用户量（层级1级）</th>
                            <th>消费金额</th>
                            <th></th>
                            
                        </tr>
                        </thead>
                        <tbody id="allmoney_info"><tr>
                         <td> 
                         </td>
                         <td> </td></tr>
                        </tbody>
                    </table>
                    <div class="dp-tfoot clearfix">
                        <div class="item fr">
                            
                             
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
    seajs.use('finance/achievement');
</script>
</body>
</html>
