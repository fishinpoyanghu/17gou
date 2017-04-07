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
                    <style>
                        .dp-page-head h2.title{border:none;}
                    </style>
                    <h2 class="title" <?php if($data['d']=='question'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question">常见问题</a></h2>
                    <h2 class="title" <?php if($data['d']=='ruleIntroduction'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=ruleIntroduction">规则介绍</a></h2>
                    <h2 class="title" <?php if($data['d']=='know'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=know">了解1元云客</a></h2>
                    <h2 class="title" <?php if($data['d']=='fortune'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=fortune">会员福分经验</a></h2>
                    <h2 class="title" <?php if($data['d']=='systemEnsure'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=systemEnsure">1元云客保障体系</a></h2>
                    <h2 class="title" <?php if($data['d']=='safetyPayment'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=safetyPayment">安全支付</a></h2>
                    <h2 class="title" <?php if($data['d']=='complaint'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=complaint">投诉建议</a></h2>
                    <h2 class="title" <?php if($data['d']=='deliveryMoney'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=deliveryMoney">配送费用</a></h2>
                    <h2 class="title" <?php if($data['d']=='sign'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=sign">商品验货与签收</a></h2>
                    <h2 class="title" <?php if($data['d']=='noReceive'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=noReceive">长时间未收到商品问题</a></h2>
                    <h2 class="title" <?php if($data['d']=='introduce'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=introduce">亿七购介绍</a></h2>
                    <h2 class="title" <?php if($data['d']=='serviceAgreement'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=serviceAgreement">服务协议</a></h2>
                    <h2 class="title" <?php if($data['d']=='contact'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=contact">联系我们</a></h2>
                    <h2 class="title" <?php if($data['d']=='cooperation'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=cooperation">商务合作</a></h2>
                    <h2 class="title" <?php if($data['d']=='invite'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=invite">邀请</a></h2>
                    <h2 class="title" <?php if($data['d']=='qq'){echo 'style="border-bottom:2px solid #03a9f5;"'; } ?>><a href="?c=activity&a=question2&d=qq">官方QQ交流群</a></h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal" id="submit_form">
                        <div class="form-group">
                            <div class="dm-textarea textarea-index" style="height: 652px;">
                                <script type="text/plain" id="detail"></script>
                            </div>
                        </div>
                    </form>
                    <div class="form-operate vercenter-wrap">
                        <div class="vercenter">
                            <button class="btn-orange btn-small btn js-sub" style="margin-left: 500px"><i class="icon-ok"></i> 提交</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
</div>
<!-- container end -->
<script>
    window.STATIC_LIST = "<?php echo SYS_STATIC_URL?>";
</script>
<div style="display: none;" id="aaa"><?php echo $data['content'] ?></div>

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('activity/question');
</script>
</body>
</html>
