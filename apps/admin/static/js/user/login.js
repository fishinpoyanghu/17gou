define(function(require, exports, module) {
	
	require('artdialog');
	var get_ipt_vlist = require('form/form_input');
	
	//var util = require('mods/util');
	//util.login();
//	var $login_url = $dp_global_config.api_domain+'?c=user&a=login&'+$dp_global_config.stand_params;
//	
//	var $data = {name:"13761188500", password:"days123"};
//	
//	$.ajax({
//		type: "POST",
//		url: $login_url,
//		dataType: 'json',
//		xhrFields: {
//            withCredentials: true
//        },
//		data: $data,
//		success: function(r) {
//			if (r.code == 0) {
//				
//			}
//			alert(r.msg);
//		}
//	});
		
	exports.init = function($form_id) {
		
		$okbtn_id = $form_id+"_okbtn";
		$action_url = $("#"+$form_id).attr('action');
		$action_url = $dp_global_config.site_domain+'?a=ajax_login&'+$dp_global_config.stand_params;
		
		// 添加 提交 按钮的事件处理
		$("#"+$okbtn_id).on('click', function() {
			
			var $self = this;
			var $add_v = get_ipt_vlist($form_id);
			
			var $btn = $(this).button('loading');
			$.ajax({
				type: "POST",
				url: $action_url,
				dataType: 'json',
				xhrFields: {
					withCredentials: true
				},
				data: $add_v,
				success: function(r) {
					
					if (r.code == 0) {
						$("#"+$okbtn_id).html('正在跳转...');
						location.href="?c=app";
					}
					else {
						var $d = dialog({
							content: '<font color="red">'+r.msg+'</font>',
							width: '150px',
							quickClose: true,
							follow: $self
						});
                        $('#code').click();
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


    $('#code').click(function(){
        $(this).attr('src',$(this).attr('src')+'&_='+Math.random());
    });
    
    $('#sms').click(function(){
        var code = $('input[name=code]').val();
        var name = $('input[name=name]').val();
        if(code==''){
            alert('请填写验证码');
            return false;
        }
        if(code==''){
            alert('请填写手机号');
            return false;
        }
        $.getJSON('?c=index&a=sms',{"code":code,"name":name},function(re){
            $('#code').click();
            alert(re.msg);
        });
    });
});
