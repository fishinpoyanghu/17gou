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
  <?php echo_css('bootstrap-treeview.min.css');?>
  <?php echo '<link rel="stylesheet" href="'.C('SITE_DOMAIN').'/wangEditor-1.3.12.css?'.C('VERSION_CSS').'">';?>
  <script type="text/javascript">var $editor_key='textarea1';</script>
  <style type="text/css">
  	.badge{display:inline-block;min-width:10px;padding:3px 7px;font-size:12px;line-height:1;text-align:center;white-space:nowrap;vertical-align:baseline;}.badge:empty{display:none}
  	
  	.node-selected {color:#FFFFFF;background-color:#428bca;}
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
                    <h2 class="title">编辑器测试</h2>
                </div>
                <div class="article-edit">
                	
                	<div id="deptree" style="width:300px;" class="treeview">
                		<ul class="list-group">
                			<li class="list-group-item" node-id="1" node-pid="0" node-level="1" expland="1"><span class="icon expand-icon icon-caret-down"></span><span class="icon node-icon icon-folder-close"></span>广州四三九九信息科技有限公司<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
                			<li class="list-group-item" node-id="2" node-pid="1" node-level="2" expland="1"><span class="indent"></span><span class="icon expand-icon icon-caret-down"></span><span class="icon node-icon icon-folder-close"></span>平台技术中心<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
                			<li class="list-group-item" node-id="3" node-pid="2" node-level="3" expland="0"><span class="indent"></span><span class="indent"></span><span class="icon expand-icon icon-caret-right"></span><span class="icon node-icon icon-folder-close"></span>页游平台<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
                			<li class="list-group-item node-selected" node-id="4" node-pid="2" node-level="3" expland="0"><span class="indent"></span><span class="indent"></span><span class="icon expand-icon icon-caret-down"></span><span class="icon node-icon icon-folder-open"></span>手游平台<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
                			<li class="list-group-item" node-id="5" node-pid="1" node-level="2" expland="0"><span class="indent"></span><span class="icon expand-icon icon-caret-right"></span><span class="icon node-icon icon-folder-close"></span>研发二部<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
                			<li class="list-group-item" node-id="6" node-pid="0" node-level="1" expland="0"><span class="icon expand-icon icon-caret-right"></span><span class="icon node-icon icon-folder-close"></span>广州爱游信息科技有限公司<span class="badge" style="display: none;"><i class="icon-plus-sign" title="创建子部门" node-ops="dep_add"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
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
seajs.use('table/editor', function(t) {
	t.init();
});
// $(function(){
//     $('#textarea1').wangEditor();
// });
</script>
</body>
</html>
