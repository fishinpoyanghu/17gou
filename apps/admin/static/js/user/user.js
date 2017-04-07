define(function(require, exports, module) {
	
	require('artdialog');
	var get_ipt_vlist = require('form/form_input');
		
	exports.init = function($form_id) {
		
		$okbtn_id = $form_id+"_okbtn";
		$action_url = $("#"+$form_id).attr('action');
		
		// 添加 提交 按钮的事件处理
		$("#"+$okbtn_id).on('click', function() {
			
			var $self = this;
			var $add_v = get_ipt_vlist($form_id);
			
			var $btn = $(this).button('loading');
			$.ajax({
				type: "POST",
				url: $action_url,
				dataType: 'jsonp',
				data: $add_v,
				success: function(r) {
					
					if (r.code == 0) {
						$("#"+$okbtn_id).html('正在跳转...');
						location.href="/?c=app";
					}
					else {
						var $d = dialog({
							content: '<font color="red">'+r.msg+'</font>',
							width: '150px',
							quickClose: true,
							follow: $self
						});
						$d.show();
						setTimeout(function() {
							$d.close().remove();
						}, 2000);
					}
					$btn.button('reset');
				}
			});
			
			
			return false;
		});
	}
});
