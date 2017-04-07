define(function(require, exports, module) {
	
	var get_ipt_vlist = require('form/form_input');
	var Piclib = require('piclib');
	require('artdialog');
	
	exports.init = function($form_id) {
		
		var $top_dialog = top.dialog.get(window);
		
		$okbtn_id = $form_id+"_okbtn";
		$cancelbtn_id = $form_id+"_cancelbtn";
		$action_url = $("#"+$form_id).attr('action');
		
		// 注册图片选择事件
		$.each($('.vercenter[data-target="#modal_piclib"]'), function() {
			$(this).bind('click', function () {
				var $dn = $(this).attr("data-name");
				var piclib = new Piclib({
                    userPic: true,
                    iconLib: '',
                    newPic: true
                }, function (imgUrl) {
                	$('#'+$form_id+' [name="'+$dn+'"').val(imgUrl);
                	$('#'+$form_id+' [name="img_'+$dn+'"').attr('src', imgUrl);
                });
			});
		});
		
		// 添加 取消 按钮的事件处理
		$("#"+$cancelbtn_id).on('click', function() {
			$top_dialog.close().remove();
		});
		
		// 添加 提交 按钮的事件处理
		$("#"+$okbtn_id).on('click', function() {
			
			var $add_v = get_ipt_vlist($form_id);
			
			var $btn=$(this).button("loading");
			$.ajax({
				type: "POST",
				url: $action_url,
				dataType: 'jsonp',
				data: $add_v,
				success: function(r) {
					if (r.code == 0) {
						dialog({
							content: r.msg,
							width: '150px',
							okValue: "确定",
							ok: function() {
								this.close().remove();
								
								$top_dialog.close(r.data);
								$top_dialog.remove();
							}
						}).show();
					}
					else {
						dialog({
							content: r.msg,
							width: '150px',
							okValue: "确定",
							ok: function() {
								this.close().remove();
							}
						}).show();
					}
				}
			});
			
			$btn.button('reset');
			return false;
		});
	}
});
