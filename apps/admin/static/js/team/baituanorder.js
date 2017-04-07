define(function(require, exports, module) {
	
	require('artdialog');


	//错误提示
	function altDialog(content,callback){
		dialog({
			content:content,
			width: '150px',
			okValue: "确定",
			ok: function() {
				this.close().remove();
				if(typeof callback === 'function'){
					callback();
				}
			}
		}).show();
	}


	$('.js-entering').on('click',function(){
		 window.id = $(this).attr('data-id');
	});

	$('.js-sub').on('click',function(){
		$('#express').modal('hide');
		var code = $('select[name=express]').val();
		var logistics_num = $('input[name=logistics_num]').val();
		$.getJSON('?c=activity&a=addExpress',{"id":window.id,"code":code,"num":logistics_num},function(re){
			if(re.code > 0){
				altDialog('录入成功',function(){
					location.reload();
				});
			}else{
				altDialog('录入失败！');
			}
		});
	});

});
