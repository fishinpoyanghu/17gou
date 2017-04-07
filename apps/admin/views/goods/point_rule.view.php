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
            width: 100px;
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
                    <div class="dp-nav">
                        <ul class="dp-nav-inner">
                            <li>
                                <a href="?c=goods&a=pointRule" class="current">积分规则</a>
                            </li>
                            <li>
                                <a href="?c=goods&a=pointRuleDetail">规则设置</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal">
                        <input type="hidden" name="id" value="<?php echo $data['info']['id']?>">
                        <div class="form-group row">
                            <label class="control-label">首次登录：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="login" class="form-control ipt-width-short dib" value="<?php echo $data['info']['登录']['point']?>"/> 分</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">完善资料：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="info" class="form-control ipt-width-short dib" value="<?php echo $data['info']['完善资料']['point']?>"/> 分</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">抽奖每次：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="lottery" class="form-control ipt-width-short dib" value="<?php echo $data['info']['抽奖']['point']?>"/> 分</label>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">晒单每次：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="show" class="form-control ipt-width-short dib" value="<?php echo $data['info']['晒单']['point']?>"/> 分</label>

                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">分享每次：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="share" class="form-control ipt-width-short dib" value="<?php echo $data['info']['分享']['point']?>"/> 分，</label>
                                    <label>每日最多 <input type="text" name="share_limit" class="form-control ipt-width-short dib" value="<?php echo $data['info']['分享']['limit']?>"/> 分</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">单笔消费每满：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="consume_limit" class="form-control ipt-width-short dib" value="<?php echo $data['info']['消费']['limit']?>"/> 元，</label>
                                    <label>获得积分 <input type="text" name="consume" class="form-control ipt-width-short dib" value="<?php echo $data['info']['消费']['point']?>"/> 分</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">参与红包扣除：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="red" class="form-control ipt-width-short dib" value="<?php echo $data['info']['红包']['point']?>"/> 积分</label>
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
    seajs.use('goods/point_rule');
</script>
</body>
</html>
