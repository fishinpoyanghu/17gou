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
    <style>
        .form-horizontal .control-cont,.textarea-index{
            margin-left: 95px;
        }
    </style>
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
                    <h2 class="title"><?php if(empty($data['info'])){echo '添加商品';}else{ echo '编辑商品';}?></i></h2>
                </div>
                <div class="article-edit">
                    <form class="form-horizontal" id="submit_form">
                        <input type="hidden" name="id" value="<?php echo $data['id']?>">
                        <div class="form-group row">
                            <label class="control-label">商品专区：</label>
                            <div class="control-cont">
                                <select class="dp-select" name="activity_type"> <!--3是限购-->
                                    <option value="1" <?php if($data['info']['activity_type']==1) echo 'selected'?>>一元专区</option> 
                                    <option value="2" <?php if($data['info']['activity_type']==2) echo 'selected'?>>十元专区</option>
                                    <option value="4" <?php if($data['info']['activity_type']==4) echo 'selected'?>>二人购</option>
                                    <option value="6" <?php if($data['info']['activity_type']==6) echo 'selected'?>>幸运购</option>
                                    <option value="7" <?php if($data['info']['activity_type']==7) echo 'selected'?>>返现购</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row js-type">
                            <label class="control-label">商品分类：</label>
                            <div class="control-cont">
                                <select class="dp-select" name="type">
                                    <?php foreach($data['type'] as $t){?>
                                    <option value="<?php echo $t['goods_type_id']?>" <?php if($data['info']['goods_type_id']==$t['goods_type_id']) echo 'selected'?>><?php echo $t['name']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">开始下期：</label>
                            <div class="control-cont">
                                <select class="dp-select" name="is_in_activity">
                                    <option value="1" <?php if($data['info']['is_in_activity']==1) echo 'selected'?>>非自动开始</option>
                                    <option value="2" <?php if($data['info']['is_in_activity']==2) echo 'selected'?>>自动开始</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">参与人数：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="need_num" class="form-control ipt-width-long" value="<?php echo $data['info']['need_num']?>"/></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">热门度：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="weight" class="form-control ipt-width-long" value="<?php echo $data['info']['weight']?>"/></label>
                                </div>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="control-label">利润比：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="rate_percent" class="form-control ipt-width-long" value="<?php echo $data['info']['rate_percent']?>"/></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">商品价值：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="value" class="form-control ipt-width-long" value="<?php echo $data['info']['value']?>"/></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">商品标题：</label>
                            <div class="control-cont">
                                <div class="dm-input">
                                    <label><input type="text" name="title" class="form-control ipt-width-long" value="<?php echo $data['info']['title']?>"/></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label">商品子标题：</label>
                            <div class="control-cont">
                                <div class="dm-textarea">
                                    <textarea name="sub_title" class="form-control" style="width: 360px;height: 100px"><?php echo $data['info']['sub_title']?></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label">列表图片：</label>
                            <div class="control-cont">
                                <div class="add-pic dib one-pic">
                                    <?php if(!empty($data['info']['main_img'])){?>
                                    <div class="picbox item">
                                        <div class="dm-thumbpic">
                                            <input type="hidden" name="main_img" value="<?php echo $data['info']['main_img']?>">
                                            <img class="js-img" src="<?php echo $data['info']['main_img']?>" height="60" width="60" alt="">
                                        </div>
                                        <a class="delete one-pic-delete"><i class="g-icon-close g-icon"></i></a>
                                    </div>
                                    <?php }?>
                                    <div class="item add-pic-button"<?php if($data['info']['main_img']) echo 'style="display:none"'?>>
                                    <a class="btn-add js-upload-one icon-plus" data-target="#modal_piclib">
                                        <i class="icon-plus dp-icon" style="margin-top: 16px"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">详情图片：</label>
                            <div class="control-cont">
                                <div class="add-pic dib more-pic">
                                    <?php if($data['info']['title_img']){ foreach(explode(',',$data['info']['title_img']) as $item){?>
                                        <div class="picbox item">
                                            <div class="dm-thumbpic"><img class="title_img" src="<?php echo $item?>" height="60" width="60"></div>
                                            <a class="delete"><i class="g-icon-close g-icon"></i></a>
                                        </div>
                                    <?php }}?>
                                    <div class="item add-more-pic-button">
                                        <a class="btn-add js-upload icon-plus" data-target="#modal_piclib">
                                            <i class="icon-plus dp-icon" style="margin-top: 16px"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="form-group row">
                            <label class="control-label">详情：</label>
                            <div class="dm-textarea textarea-index" style="height: 270px;">
                                <script type="text/plain" id="detail"></script>
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
<script>
    window.STATIC_LIST = "<?php echo SYS_STATIC_URL?>";
</script>

<div style="display:none" id="aaa"><?php echo $data['info']['detail']?></div>

<!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
    seajs.use('goods/index');
</script>
</body>
</html>
