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
        label{
            margin-right: 8px;;
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
                    <h2 class="title">限购设置</h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal">
                        <div class="form-group row">
                            <label class="control-label">间隔时间：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="interval_1" class="form-control ipt-width-short dib" value="<?php echo $data['info']['jiange1']?>"/> - </label>
                                    <label><input type="text" name="interval_2" class="form-control ipt-width-short dib" value="<?php echo $data['info']['jiange2']?>"/> 秒（最小5秒）</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label">价钱：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="money1" class="form-control ipt-width-short dib" value="<?php echo $data['info']['money1']?>"/> - </label>
                                    <label><input type="text" name="money2" class="form-control ipt-width-short dib" value="<?php echo $data['info']['money2']?>"/>  （左边小右边大同时填写才生效）</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">限购时间：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <?php for($i=0;$i<=23;$i++){?>
                                    <label><input type="checkbox" name="hour" class="dp-checkbox" value="<?php echo $i;?>" <?php if(in_array($i,$data['info']['hour']))echo "checked";?>/><?php echo $i."时";?> </label>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">商品低于：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="stop" class="form-control ipt-width-short dib" value="<?php echo $data['info']['stop']?>"/> 份时停止限购</label>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">限购开关：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="radio" name="state" class="dp-radio" value="1" <?php if($data['info']['state'] == '' || $data['info']['state']==1)echo "checked"?>/> 开 </label>
                                    <label><input type="radio" name="state" class="dp-radio" value="0" <?php if($data['info']['state']==0)echo "checked"?>/> 关 </label>
                                    <a class="btn-small btn-orange btn js-save" style="margin-left: 220px;margin-bottom: 7px;">保存</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-wrap">
                        <table class="table-condensed dp-table table table-striped">
                            <thead>
                            <tr>
                                <th class="dp-col-align-left"><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox"/>商品名称</th>
                                <th>创建时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($data['list'] as $val){?>
                                <tr>
                                    <td>
                                        <div class="fl">
                                            <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $val['goods_id'];?>" <?php if(in_array($val['goods_id'],$data['info']['goods']))echo "checked";?>/>
                                        </div>
                                        <div class="dp-pic-and-txt fl">
                                            <?php echo $val['title']?>
                                        </div>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i:s',$val['rt']);?></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
</div>
<!-- container end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('custom/index');
</script>
</body>
</html>
