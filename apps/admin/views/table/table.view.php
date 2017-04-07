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
  <div class="container">
    <div class="main">
      <div class="main-inner">
        <div class="main-container">
          <div class="dp-page-head">
          	<?php if ($title_list) {?>
          	<div class="left-item">
                <div class="dp-nav">
                    <ul class="dp-nav-inner">
                    <?php foreach ($title_list as $title) {?>
                        <li>
                            <a<?php if ($tbl == $title['key']) {?> class="current"<?php }?> href="<?php app_echo_url($title['url']);?>"><?php echo $title['text'];?></a>
                        </li>
                    <?php }?>
                    </ul>
                </div>
            </div>
            <?php } else {?>
            <h2 class="title"><?php echo $base_cfg['title'];?></h2>
            <?php }?>
            <div class="right-item vercenter-wrap">
              <div class="item dib vercenter">
              <!-- 如果$category_list有内容，显示搜索目录框 -->
	              <?php 
	              foreach ($category_list as $search_key=>$category) {
	              ?>
	              <select name="<?php echo $search_key;?>" data-type="search" class="dp-select">
	              	<?php 
	              	foreach ($category['option_list'] as $option) {
	              	?>
	              	<option value="<?php echo $option['value'];?>"<?php if ($option['value'] == $search_conds[$search_key]) {echo ' selected';}?>><?php echo $option['text'];?></option>
	              	<?php
	              	}
	              	?>
	              </select>
	              <?php 
	              }
	              ?>
              </div> 
              <span class="dp-search-item item vercenter">
              	<?php
              	if (isset($base_cfg['search']) && (count($base_cfg['search']) >= 4)) {
              	?>
              	<div class="input-group dp-input" style="float:left;">
	                <input class="form-control input-sm" name="kw" data-type="search" type="text" placeholder="<?php echo $base_cfg['search']['palceholder']; ?>" value="<?php echo htmlspecialchars($search_conds['kw'], ENT_QUOTES);?>" />
	                <span class="input-group-btn">
	                	<button class="btn btn-sm btn-default" id="searchBtn"><i class="icon-search"></i></button>
	                </span>
	            </div>
                <?php
              	}
                if ($add_cfg) {
                ?>
                	<a href="<?php app_echo_url(tpf_var($base_cfg['pageAddUrl'], $tbl, '').$search_uri);?>" class="btn-orange btn" id="table_add_btn" style="margin-left:5px;"><?php echo $add_cfg['addBtnName'];?></a>                	
                <?php 
                }
                if ($upload_cfg) {
                ?>
                	<button class="btn-orange btn" id="table_upload_btn" style="margin-left:5px;"><?php echo $upload_cfg['uploadBtnName'];?></button>
                <?php 
                }
                ?>
              </span>
            </div>
          </div>
          <div class="table-wrap">
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
              <?php foreach ($tbl_list as $k=>$row) {?>
              	<tr>
              		
              		<?php
              		$head_index = 0;
              		foreach ($tbl_head as $m=>$head) {
              			$head_index++;
              			if ($head_index == 1) {
              		?>
              		<td>
              			<?php if ($classify_cfg || $del_cfg) { ?>
              			<div class="fl">
	                      <input name="multi_delete" class="dp-checkbox" type="checkbox" value="<?php echo $row[$m];?>" />
	                    </div>
	                    <?php } ?>
	                    <div class="dp-pic-and-txt fl">
	                      <a href="<?php echo tpf_var($base_cfg['previewUrl'], $row[$m]);?>" title="点击预览" target="_blank"><?php echo $row[$m];?></a>
	                    </div>
	                </td>
              		<?php
              			}
              			else {
              		?>
              		<td>
              			<p><?php echo $row[$m];?></p>
              		</td>
              		<?php
              			}
              		}
              		?>              		
              	</tr>
              <?php }?>
              </tbody>
            </table>
            <div class="dp-tfoot clearfix">
              <div class="item fl">
                <div class="btn-group" role="group" aria-label="group" id="multi_operate">
                  <button class="btn-default btn btn-sm" onclick="location.reload();"><i class="icon-refresh"></i></button>
                  <?php
                  if ($classify_cfg) {
                  ?>
                  <button class="btn-default btn btn-sm" id="modify_classify_btn"><?php echo $classify_cfg['multiBtnName'];?></button>
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
              <div class="item fr">
                
                <nav class="dp-page-right">
                	<?php echo $page_content;?>
				</nav>
                <div class="page-cnt">共<?php echo $tbl_count;?>条，每页<?php echo $pagesize;?>条</div>
                
              </div>
            </div>            
          </div>
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
  <input type="hidden" name="modal_classifyFlag" id="modal_classifyFlag" value="<?php echo $classify_cfg ? 1 : 0;?>" />
  <!-- 全局变量 end -->
<?php echo_js('libs/sea.js');?>
<?php echo_js('libs/seajs_preload.js');?>
<?php echo_js('libs/seajs_config.js');?>

<script>
seajs.use('table/table_list', function(t) {
	t.init();
});

seajs.use('table/editor', function(t) {
	t.init();
});
</script>
</body>
</html>
