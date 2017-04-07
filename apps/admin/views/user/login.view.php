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
  <style type="text/css">
  .article-edit .form-operate {margin-left:0px;text-align:center;}
  .error{padding:8px;padding-left:25px;background-position:5px center;background-repeat:no-repeat}
  .error a{text-decoration:underline}
  .error{background-color:#FBE3E4;border:2px solid #FBC2C4;color:#8A1F11;background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAw1BMVEX////////uVVXuVFTkSEjVb1HsUlLnS0vvW1vmS0vuWVnvXV3dPj7rUFDkyLz2o6P1mZndQD3rUVHVbU/dPz3kR0fUclLdQTzbUDvaWj/69fP60dHeQjvTg2btVFTcTzvmSkrdTTr//f3lzL/UgWTvYWH++fniwbLvX1/zhYXVbEzygYH0k5P709P95ub+8fHwZ2fjxbj1nZ3839/6z8/xcXHdSzrdTDreQzv83d32n5/eQjz3q6v+9fXwa2v96+v84eGH3JX9AAAAAXRSTlMAQObYZgAAALlJREFUeF49jlWSxDAMBSXZDjLDMDPPMt7/VCsnU9t/3VV+FmhmRRXHVTGDB4P+2BfCH/cHrTZ1nlBLktcNh1S6nxtWJ9yTVACrTISIEVl3xO9hdoHUpKcl4jv7zSFbwQsP7LiwL3hmArEgos0U8Yedhq9tsHrIXHX4gGeDrAPiccmlfbI1KUJ8W/PO9Exmyt/S7qu35uXTryuyFcBW0j8y7U43OjX06Zp5aSe8Z5dzeBCokeeNVKDlD2nuD2i4ItR8AAAAAElFTkSuQmCC)}.error a{color:#8A1F11}
  .reglogin-body{padding-top:100px}  
  .reglogin-body .reg-form,.reglogin-body .login-form{margin:auto;width:360px}
  </style>
</head>

<body>
  <!-- header start -->
  <?php include '../views/public/header.view.php';?>
  <!-- header end -->
  <!-- container start -->
  <div class="container">
    <div class="main">
	    <div class="main-body reglogin-body">
	      <div class="login-form">	        
	        	<div class="panel panel-default">
				  <div class="panel-heading h5 text-center">用户登录</div>
				  <div class="panel-body">
				  	<form id="<?php echo $form_id;?>" class="form-horizontal" action="<?php echo $action_url;?>" method="post">
				  	<!--
				  	<div class="form-group">
		            	<label class="control-label">主体：</label>
		            	<div class="control-cont">
		              		<div class="dp-input dib">
		              			<input type="text" name="company_name" class="form-control" maxlength="11" value="<?php echo $company['name'];?>" disabled="true" style="width:180px;" />
		              			<input type="hidden" name="company_id" data-form="<?php echo $company['department_id'];?>" value="" />
		              		</div>
		           		</div>
		          	</div>
		          	-->
		          	<div class="form-group">
		            	<label class="control-label">手机号：</label>
		            	<div class="control-cont">
		              		<div class="dp-input dib">
		              			<input type="text" name="name" data-form="<?php echo $form_id;?>" class="form-control" style="width:180px;" />
		              		</div>
		              	</div>
		            </div>
                        <div class="form-group">
                            <label class="control-label">验证码：</label>
                            <div class="control-cont">
                                <div class="dp-input dib">
                                    <input type="text" name="code" data-form="<?php echo $form_id;?>" class="form-control" style="width:180px;" /><img id="code" alt="图形验证码" src="?c=index&a=code">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">手机验证码：</label>
                            <div class="control-cont">
                                <div class="dp-input dib">
                                    <input type="text" name="sms" data-form="<?php echo $form_id;?>" class="form-control" style="width:180px;" /><a class="btn-orange btn-sm btn" id="sms" style="width:60px;"> 获取 </a>
                                </div>
                            </div>
                        </div>
		          	<div class="form-group">
		            	<label class="control-label">密码：</label>
		            	<div class="control-cont">
		              		<div class="dp-input dib">
		              			<input type="password" name="password" data-form="<?php echo $form_id;?>" class="form-control" style="width:180px;" />
		              		</div>
		              	</div>
		            </div>
		            </form>
				  </div>
				  <div class="panel-footer text-center">
				  	<button id="<?php echo $form_id;?>_okbtn" class="btn-orange btn-sm btn" data-loading-text="验证中..." type="submit" style="width:60px;"> 登 录 </button>
				  </div>
				</div>
	      </div>
	    </div>
    </div>
  </div>
  <!-- container end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
seajs.use('user/login', function(t) {
	t.init('<?php echo $form_id;?>');
});
</script>
</body>
</html>
