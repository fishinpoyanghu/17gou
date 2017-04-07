define(function(require, exports, module) {
	
	require('artdialog');

	$('.js-sub').on('click',function(e){
		e.preventDefault();
		var old = $('input[name=old]').val();
		var new_pwd = $('input[name=new]').val();
		var affirm_pwd = $('input[name=affirm]').val();
		$.post('?c=index&a=setPwd',{'old':old,'new_pwd':new_pwd,'affirm_pwd':affirm_pwd},function(re){
			if(re.code > 0){
				altDialog('修改成功',function(){
					location.href = "?index&a=logout";
				});
			}else{
				altDialog(re.msg);
			}
		},'json');
	});

	//弹出提示
	function altDialog(content,callback){
		dialog({
			content:content,
			width: '180px',
			okValue: "确定",
			ok: function() {
				this.close().remove();
				if(typeof callback === 'function'){
					callback();
				}
			}
		}).show();
	}

});
