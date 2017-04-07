define(function(require, exports, module) {
	
	var get_ipt_vlist = require('form/form_input');
	var Piclib = require('piclib');
	require('artdialog');
	
	exports.init = function($form_id) {
		
		$okbtn_id = $form_id+"_okbtn";
		$cancelbtn_id = $form_id+"_cancelbtn";
		$action_url = $("#"+$form_id).attr('action');
		
		// 初始化颜色选择
		if ($('.select-bgc .bgc-option').length>0) {
			var $colorname = $('.select-bgc .bgc-option').parent().attr('color-name');
			var $colorvalue = $("#"+$form_id+' [name="'+$colorname+'"]').val();
			$('.select-bgc .bgc-option[data-bgcolor="'+$colorvalue+'"]').addClass("checked");
		}
		// 注册颜色选择事件
		$('.select-bgc .bgc-option').on('click', function() {
			$.each($('.select-bgc .bgc-option'), function() {
				$(this).removeClass('checked');
			});
			
			$(this).addClass('checked');
			var $colorname = $(this).parent().attr('color-name')
			$("#"+$form_id+' [name="'+$colorname+'_other"]').val($(this).attr("data-bgcolor"));
			$("#"+$form_id+' [name="'+$colorname+'"]').val($(this).attr("data-bgcolor"));
		})
		
		// 注册图片选择事件
		$.each($('.vercenter[data-target="#modal_piclib"]'), function() {
			
			var $dn = $(this).attr("data-name");
			
			var $iconv = $('#'+$form_id+' [name="'+$dn+'"').val();
			if ($iconv.length>0) {
				if ($iconv.indexOf('icon-') == 0) {
            		$('#'+$form_id+' label[name="'+$dn+'_label"').html('<i class="'+$iconv+'" style="font-size:48px"></i>');
            	}
            	else {
            		$('#'+$form_id+' label[name="'+$dn+'_label"').html('<a href="'+$iconv+'" target="_blank"><img src="'+$iconv+'" width="60" /></a>');
            	}
			}
			
			$(this).bind('click', function () {
				
				var $userpic = parseInt($('#'+$form_id+' [name="'+$dn+'"').attr('data-userpic'));
				var $iconlib = $('#'+$form_id+' [name="'+$dn+'"').attr('data-iconlib');
				var $fontlib = $('#'+$form_id+' [name="'+$dn+'"').attr('data-fontlib');
				
				var piclib = new Piclib({
                    userPic: $userpic>0?true:false,
                    iconLib: $iconlib,
                    fontLib: $fontlib,
                    tipsnew: $('#'+$form_id+' [name="'+$dn+'"').attr('data-tipsnew'),
                    newPic: true
                }, function (imgUrl) {
                	$('#'+$form_id+' [name="'+$dn+'"').val(imgUrl);
                	
                	if (imgUrl.indexOf('icon-') == 0) {
                		$('#'+$form_id+' label[name="'+$dn+'_label"').html('<i class="'+imgUrl+'" style="font-size:48px"></i>');
                	}
                	else {
                		$('#'+$form_id+' label[name="'+$dn+'_label"').html('<a href="'+imgUrl+'" target="_blank"><img src="'+imgUrl+'" width="60" /></a>');
                	}
                });
			});
		});
		
		// 添加 取消 按钮的事件处理
		$("#"+$cancelbtn_id).on('click', function() {
			if ($('#'+$form_id).attr('page-type') == 'dialog') {
				var $top_dialog = top.dialog.get(window);
				$top_dialog.close().remove();
			}
			else {
				history.go(-1);
			}
		});
		
		// 添加 提交 按钮的事件处理
		$("#"+$okbtn_id).on('click', function() {
			
			var $self = this;
			var $ipt_v = get_ipt_vlist($form_id);
			
			var $btn=$(this).button("loading");
			$.ajax({
				type: "POST",
				url: $action_url,
				dataType: 'jsonp',
				data: $ipt_v,
				success: function(r) {
					if (r.code == 0) {
						dialog({
							content: r.msg,
							width: '150px',
							okValue: "确定",
							ok: function() {
								if ($('#'+$form_id).attr('page-type') == 'dialog') {
									top.location.reload();
								}
								else {
									location.href = $('#'+$form_id).attr('refresh-url');
								}
							},
							follow: $self
						}).show();
					}
					else {
						dialog({
							content: r.msg,
							width: '150px',
							okValue: "确定",
							ok: function() {
								this.close().remove();
							},
							follow: $self
						}).show();
					}
				}
			});
			
			$btn.button('reset');
			return false;
		});
	}
});
