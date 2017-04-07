<form id="<?php echo $form_id;?>" class="form-horizontal" action="<?php echo $action_url;?>" refresh-url="<?php echo $refresh_url; ?>" page-type="<?php echo $page_type;?>">
    <?php
    foreach ($ipt_list as $k=>$ipt) {
    	$sm_count = 10;
    	$tp_class = '';
    	if (in_array($ipt['type'], array('text', 'password'))) {
    		if ($ipt['tips'] != '') {
        		$sm_count = 5;
        		$tp_class = '';
    		}
    		else {
    			$tp_class = ' ipt-width-long';
    		}
    	} 
    ?>
    <div class="form-group row">
        <label class="control-label col-sm-2"><?php if (isset($ipt['require']) && $ipt['require']) {echo '<em class="required">*</em>';} echo $ipt['name'];?>：</label>
        <div class="col-sm-<?php echo $sm_count; ?>">
            <div class="input-group dp-input<?php echo $tp_class;?>">
            	<?php if ($ipt['type'] == 'show') {?>
            		<label name="<?php echo $k;?>"><?php echo $ipt['value'];?></label>
            	<?php } elseif ($ipt['type'] == 'text') {?>
                	<input type="text" name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="text" class="form-control input-sm" placeholder="<?php echo $ipt['placeholder'];?>" value="<?php echo $ipt['value'];?>" aria-describedby="basic-addon<?php echo $k;?>" />
                	<?php if ($ipt['tips'] != '') {?><span class="input-group-addon" id="basic-addon<?php echo $k;?>">&nbsp;<?php echo $ipt['tips'];?></span><?php } ?>
                <?php } elseif ($ipt['type'] == 'password') {?>
                	<input type="password" name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="text" class="form-control input-sm" placeholder="<?php echo $ipt['placeholder'];?>" value="<?php echo $ipt['value'];?>" />
                	<?php if ($ipt['tips'] != '') {?><span class="input-group-addon" id="basic-addon<?php echo $k;?>">&nbsp;<?php echo $ipt['tips'];?></span><?php } ?>
                <?php } elseif ($ipt['type'] == 'textarea') {?>
                	<textarea name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="textarea" rows="3" cols="50" placeholder="<?php echo $ipt['placeholder'];?>"><?php echo $ipt['value'];?></textarea><?php if ($ipt['tips']) {?><br /><label class="ipt_tips"><?php echo $ipt['tips'];?></label><?php }?>
                <?php } elseif ($ipt['type'] == 'editor') {?>
                <script>$editor_key = '<?php echo $k;?>';</script>
                	<textarea name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="editor" style="width:100%; height:200px; max-height:400px;"><?php echo $ipt['value'];?></textarea><label class="ipt_tips"><?php echo $ipt['tips'];?></label>
                <?php } elseif ($ipt['type'] == 'pic') {?>
                	<em class="navicon">
                		<label name="<?php echo $k;?>_label"></label>
                		<input type="hidden" name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="pic" data-userpic="<?php echo $ipt['attr']['userPic'];?>" data-iconlib="<?php echo $ipt['attr']['iconLib'];?>" data-fontlib="<?php echo $ipt['attr']['fontLib'];?>" data-tipsnew="<?php echo $ipt['attr']['tipsnew'];?>" value="<?php echo $ipt['value'];?>" />
                	</em>
                	<em class="gap"></em>
                	<span class="dib">
                		<span class="vercenter-wrap">
                			<a class="vercenter js-modify" data-target="#modal_piclib" data-name="<?php echo $k;?>">选择</a>
                			<label class="ipt_tips"><?php echo $ipt['tips'];?></label>
                		</span>                		
                	</span>
                	
                <?php } elseif ($ipt['type'] == 'select') {?>
                	<select name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="select" class="dp-select">
	                    <?php 
            			foreach ($ipt['options'] as $option) {
            			?>
            			<option value="<?php echo $option['value'];?>"<?php if ($option['value'] == $ipt['value']) {echo ' selected';}?>><?php echo $option['text'];?></option>
            			<?php 
            			}
            			?>
                  	</select>
                  	<label class="ipt_tips"><?php echo $ipt['tips'];?></label>
                <?php } elseif ($ipt['type'] == 'radio') {?>
                	<div class="radio" style="padding-top:0;">
                		<?php 
            			foreach ($ipt['options'] as $option) {
            			?>
                		<label>	                			
                			<input type="radio" class="dp-radio" name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="radio" value="<?php echo $option['value'];?>"<?php if ($option['value'] == $ipt['value']) {echo ' checked';}?>> <?php echo $option['text'];?>             			
                		</label>
                		&nbsp;&nbsp;
                		<?php 
            			}
            			?>
            			<label class="ipt_tips"><?php echo $ipt['tips'];?></label>
                	</div>
                <?php } elseif ($ipt['type'] == 'checkbox') {?>
                	<div class="checkbox" style="padding-top:0;">
                		<?php 
                		$ipt['value'] = explode(',', $ipt['value']);
            			foreach ($ipt['options'] as $option) {
            			?>
                		<label>	                			
                			<input type="checkbox" class="dp-checkbox" name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="checkbox" value="<?php echo $option['value'];?>"<?php if (in_array($option['value'], $ipt['value'])) {echo ' checked';}?>> <?php echo $option['text'];?>             			
                		</label>
                		&nbsp;&nbsp;
                		<?php 
            			}
            			?>
            			<label class="ipt_tips"><?php echo $ipt['tips'];?></label>
                	</div>
                <?php } elseif ($ipt['type'] == 'color') {?>
                	<div class="select-bgc" color-name="<?php echo $k;?>">
                		<!-- <span class="bgc-option" style="background:#FFFFFF;" data-bgcolor="#FFFFFF"></span>
	                    <span class="bgc-option" style="background:#F9F8F3;" data-bgcolor="#F9F8F3"></span> -->
	                    <span class="bgc-option" style="background:#4C5A65;" data-bgcolor="#4C5A65"></span>
	                    <span class="bgc-option" style="background:#2BB8AA;" data-bgcolor="#2BB8AA"></span>
	                    <span class="bgc-option" style="background:#4184D3;" data-bgcolor="#4184D3"></span>
	                    <span class="bgc-option" style="background:#F3AE63;" data-bgcolor="#F3AE63"></span>
	                    <span class="bgc-option" style="background:#EF0070;" data-bgcolor="#EF0070"></span>
	                    <span class="bgc-option" style="background:#FF78A6;" data-bgcolor="#FF78A6"></span>
	                    <span class="bgc-option" style="background:#E63B53;" data-bgcolor="#E63B53"></span>
	                    <!-- <span class="bgc-option" style="background:#FAFAFA;" data-bgcolor="#FAFAFA"></span>
	                    <span class="bgc-option" style="background:#DEDEDE;" data-bgcolor="#DEDEDE"></span> -->
	                    <span class="bgc-option" style="background:#333230;" data-bgcolor="#333230"></span>
	                    <span class="bgc-option" style="background:#319962;" data-bgcolor="#319962"></span>
	                    <span class="bgc-option" style="background:#005499;" data-bgcolor="#005499"></span>
	                    <span class="bgc-option" style="background:#FF5000;" data-bgcolor="#FF5000"></span>
	                    <span class="bgc-option" style="background:#ED145B;" data-bgcolor="#ED145B"></span>
	                    <span class="bgc-option" style="background:#F15B5B;" data-bgcolor="#F15B5B"></span>
	                    <span class="bgc-option" style="background:#CC0000;" data-bgcolor="#CC0000"></span>
	                </div>
	                <div class="dp-input dib form-inline">
	                	 其它：<input type="text" name="<?php echo $k;?>_other" class="ipt-width-short form-control" value="<?php echo $ipt['value'];?>">
	                	 <input type="hidden" name="<?php echo $k;?>" data-form="<?php echo $form_id;?>" row-type="color" value="<?php echo $ipt['value'];?>">
	                </div>
                <?php }?>
            </div>
        </div>
    </div>
    <?php 
    }
    ?>
</form>
<div class="form-operate vercenter-wrap">
    <div class="vercenter">
    	<?php if (!(isset($hide_cancel) && $hide_cancel)) {?><button id="<?php echo $form_id;?>_cancelbtn" class="btn-small btn"><i class="icon-reply"></i> 取消</button><?php }?>
    	<button id="<?php echo $form_id;?>_okbtn" class="btn-orange btn-small btn" data-loading-text="提交中..."><i class="icon-ok"></i> <?php echo $form_okbtn['name'];?></button>
    </div>
</div>