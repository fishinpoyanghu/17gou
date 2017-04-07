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
    <link rel="stylesheet" href="<?php echo SYS_STATIC_URL.'/css/select2.min.css'?>">
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
                    <h2 class="title"><?php echo $data['info']['msg_id']? '编辑消息':'添加消息'?></h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal" id="submit_form">
                        <input type="hidden" name="id" value="<?php echo $data['info']['msg_notify_id']?>">
                        <div class="form-group row">
                            <label class="control-label">收件人：</label>
                            <div class="control-cont">
                                <select name="to_uid" class="js-user-select " style="width:200px;">
                                    <?php foreach($data['user'] as $val){?>
                                    <option value="<?php echo $val['uid']?>" <?php if($data['info']['uid']==$val['uid']) echo 'selected="selected"'?>><?php echo $val['nick']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">内容：</label>
                            <div class="control-cont">
                                <div class="dm-textarea">
                                    <textarea name="content" class="form-control" style="width: 360px;height: 100px"><?php echo $data['info']['content']?></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
                            <a href="javascript:history.go(-1)" class="btn-small btn"><i class="icon-reply"></i> 取消</a>
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

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('activity/msg');
</script>
</body>
</html>
