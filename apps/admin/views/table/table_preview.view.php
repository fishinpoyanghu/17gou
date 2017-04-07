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
	<div style="margin:20px 20px;">
		<?php 
		foreach ($row as $k=>$v) {
		?>
		<div style="margin-bottom:10px;">
			<?php echo $v['name'];?>：
			<?php 
			if (strlen_utf8($v['value']) < 100) {
				echo strlen_utf8($v['value']) > 0 ? $v['value'] : '-';
			}
			else {
			?>
			<br />
			&nbsp;&nbsp;<?php echo $v['value'];?>
			<?php 
			}
			?>
		</div>
		<?php 
		}
		?>
	</div>
</body>
</html>
