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
  	
  	.list-group-item {padding:10px 0px 10px 7px;}
  	.node-selected {color:#FFFFFF;background-color:#428bca;}
  	#deptree .node-icon{color:#6389C1}
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
                    <h2 class="title">员工管理</h2>
                    <div class="right-item vercenter-wrap">
             			<span class="dp-search-item item vercenter">
             				<input type="hidden" name="dp_company_id" id="dp_company_id" value="<?php echo $company['department_id'];?>" />
			              	<?php
			              	if (isset($base_cfg['search']) && (count($base_cfg['search']) >= 5)) {
			              	?>
			              	<div class="input-group dp-input" style="float:left;">
				                <input class="form-control input-sm" name="kw" id="kw" type="text" placeholder="<?php echo $base_cfg['search']['palceholder']; ?>" value="<?php echo htmlspecialchars($kw, ENT_QUOTES);?>" />
				                <!--  <span class="dp-input">
				                  </span>-->
				                <span class="input-group-btn">
				                	<button class="btn btn-sm btn-default" id="searchBtn"><i class="icon-search"></i></button>
				                </span>
				            </div>
			                <?php
			              	}
			                if ($add_cfg) {
			                ?>
			                	<button class="btn-orange btn" id="add_tableRow" style="margin-left:5px;"><?php echo $add_cfg['addBtnName'];?></button>
			                <?php 
			                }
			                ?>
			            </span>
			        </div>
                </div>
                <section class="table-wrap fl" style="width:100%;display:table;">
                	
                	<aside id="deptree" class="treeview" style="width:250px;display:table-cell;vertical-align:top;height:100%;padding:0;float:none;">
                		<ul class="list-group">
                		<?php foreach ($node_list as $node) { ?>
                			<li class="list-group-item" node-id="<?php echo $node['department_id'];?>" node-pid="<?php echo $node['pid'];?>" node-level="<?php echo $node['level'];?>" expand="<?php echo $node['expand'];?>" style="<?php if ($node['level'] > 2) {echo 'display:none';}?>"><?php for ($i=1; $i < $node['level']; $i++) {?><span class="indent"></span><?php }?><span class="icon expand-icon <?php echo $node['expand'] ? 'icon-caret-down' : 'icon-caret-right';?>"></span><span class="icon node-icon icon-folder-close"></span><span class="node-text"><?php echo $node['name'].'</span><span class="node-staffcount">('.$node['count'].')'; ?></span><span class="badge" style="display: none;"><i class="icon-plus" title="创建子部门" node-ops="dep_add"></i><i class="icon-pencil" title="编辑" node-ops="dep_edit"></i><i class="icon-trash" title="删除" node-ops="dep_delete"></i></span></li>
                		<?php } ?>
                	</aside>
                	<aside class="table-wrap" style="display:table-cell;vertical-align:top;height:100%;padding:0 0 0 11px;float:none;" id="dp-staff-tbl">
                		<table class="table-condensed dp-table table table-striped" id="dp-list-tbl">
			              <thead>
			                <tr>
			                <?php 
			                $head_index = 0;
			                foreach($tbl_head as $k=>$head) {
			                	$head_index++;
			                	if ($head_index == 1) {
			                ?>
			                	<th class="dp-col-align-left"><?php if ($classify_cfg || $del_cfg) {?><input class="dp-checkbox" id="checkAll" style="margin:0 15px 0 5px;" type="checkbox" /><?php }?><?php echo $head['name'];?></th>
			                	<?php
			                	}
			                	else {?>
			                    <th<?php if (isset($head['_w']) && $head['_w']) { echo ' style="width:'.$head['_w'].'"'; } ?>><?php echo $head['name'];?></th>
			                <?php }} ?>
			                </tr>
			              </thead>
			              <tbody>
			              </tbody>
			            </table>
			            <div class="dp-tfoot clearfix">
			              <div class="item fl">
			                <div class="btn-group" role="group" aria-label="group" id="multi_operate">
			                  <button class="btn-default btn btn-sm" btn-type="refresh_btn"><i class="icon-refresh"></i></button>
			                  <?php
			                  if ($classify_cfg) {
			                  ?>
			                  <button class="btn-default btn btn-sm" id="modify_classify" data-toggle="modal" data-target="#modal_modifyClassify"><?php echo $classify_cfg['multiBtnName'];?></button>
			                   <?php 
			                  }
			                  ?>
			                  <?php 
			                  if ($del_cfg && $del_cfg['multiBtnName']) {
			                  ?>
			                  <button class="btn-default btn btn-sm" id="multi_delete" msg-confirm="<?php echo $del_cfg['confirmMsgMulti'];?>"><?php echo $del_cfg['multiBtnName'];?></button>
			                  <?php 
			                  }
			                  ?>
			                </div>
			              </div>
			              <div class="item fr" id="dp-tbl-page">
			                
			                <nav class="dp-page-right">
			                	<?php echo $page_content;?>
							</nav>
			                <div class="page-cnt">共<?php echo $tbl_count;?>条，每页<?php echo $pagesize;?>条</div>
			                
			              </div>
			            </div>
                	</aside>
                </section>
            </div>
        </div>
    </div>
    <?php include '../views/public/menu.view.php';?>
  </div>
  <!-- container end -->
  <!-- 设置一些隐藏全局变量 -->
  <input type="hidden" name="tbl" id="tbl" value="<?php echo $tbl;?>" />
  <input type="hidden" name="modal_uploadFlag" id="modal_uploadFlag" value="<?php echo $upload_cfg ? 1 : 0;?>" />
  <input type="hidden" name="modal_addFlag" id="modal_addFlag" value="<?php echo ($add_cfg && ($add_cfg['open'] == 'modal')) ? 1 : 0;?>" />
  <input type="hidden" name="modal_editFlag" id="modal_editFlag" value="<?php echo ($edit_cfg && ($edit_cfg['open'] == 'modal')) ? 1 : 0;?>" />
  <input type="hidden" name="modal_resetFlag" id="modal_resetFlag" value="<?php echo $reset_cfg ? 1 : 0;?>" />
  <input type="hidden" name="modal_delFag" id="modal_delFlag" value="<?php echo $del_cfg ? 1 : 0;?>" />
  <!-- 全局变量 end -->
  
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
seajs.use('staff/department', function(t) {
	t.init();
});

seajs.use('table/editor', function(t) {
	t.init();
});
// $(function(){
//     $('#textarea1').wangEditor();
// });
</script>
</body>
</html>
