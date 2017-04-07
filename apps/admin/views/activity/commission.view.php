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
        .form-horizontal .control-label{
            width: 98px;
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
                    <h2 class="title">师徒关系设置</h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal">
                        <div class="form-group row">
                            <label class="control-label">一级师徒关系：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="hidden" name="one_id" value="1"/></label>
                                    <label>
                                        <input type="text" name="one" class="form-control ipt-width-short dib" value="<?php echo $data['info'][1]['percent']?>"/>
                                        <span>*通过您的分享成功购买商品的伙伴,请输入0~100之间的数，如师徒关系20%，输入20</span>
                                    </label>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">二级伙伴师徒关系：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="hidden" name="two_id" value="2"/></label>
                                    <label>
                                        <input type="text" name="two" class="form-control ipt-width-short dib" value="<?php echo $data['info'][2]['percent']?>"/>
                                        <span>*同上</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">三级师徒关系：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="hidden" name="three_id" value="3"/></label>
                                    <label>
                                        <input type="text" name="three" class="form-control ipt-width-short dib" value="<?php echo $data['info'][3]['percent']?>"/>
                                        <span>*同上</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">注册师徒关系：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="hidden" name="reg_id" value="0"/></label>
                                    <label>
                                        <input type="text" name="reg" class="form-control ipt-width-short dib" value="<?php echo $data['info'][0]['percent']?>"/>
                                        <span>*通过您的分享成功注册可以获得的师徒关系,请输入0~100之间的数，如输入20，则获得20元</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
                            <button class="btn-orange btn-small btn js-sub"><i class="icon-ok"></i> 提交</button>
                        </div>
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
    seajs.use('activity/commission');
</script>
</body>
</html>
