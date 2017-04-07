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
</head>

<body>
<!--
<form class="form-horizontal">
	<div class="form-group dp-input">
		<label for="dep_name">部门名称：</label>
		<input type="text" name="dep_name" class="form-control input-sm" style="width:200px;" placeholder="部门名称" />
	</div>
</form>
-->
<form class="form-horizontal">
	<div class="form-group dp-input">
		<label for="dep_name" class="control-label fl">部门名称：</label>
		<div class="fl">
			<input type="text" name="dep_name" id="dep_name" value="<?php echo $dep['name'];?>" class="form-control input-sm" placeholder="部门名称" autofocus />
		</div>
	</div>
</form>
<div class="form-operate vercenter-wrap" style="margin-top:20px;">
	<div class="vercenter">
		<button id="dep_cancel_btn" class="btn-small btn"><i class="icon-reply"></i> 取消</a>
		<button id="dep_ok_btn" class="btn-orange btn-small btn" data-loading-text="提交中..."><i class="icon-ok"></i> 保存</button>
	</div>
</div>
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
seajs.use([], function(t) {
	var dialog = top.dialog.get(window);

	$('#dep_ok_btn').on('click', function() {

		var $dep_name = $("#dep_name").val().trim();
		
		if (($dep_name.length < 1) || ($dep_name.length > 10)) {
			alert("部门名字长度范围只允许1-10个字");
			return false;
		}

		$.post('?c=department&a=ajax_edit_save&dep_id=<?php echo $dep_id;?>&dep_name='+encodeURIComponent($dep_name), function(res) {
			res = JSON.parse(res);

			if (res.code == 0) {
				dialog.close(res.data);
				dialog.remove();
			}
			else {
				alert(res.msg);
			}
		});
		
	});

	$('#dep_cancel_btn').on('click', function() {
		dialog.close();
		dialog.remove();
	});
});
</script>
</body>
</html>
