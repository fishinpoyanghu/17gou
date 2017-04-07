define(function(require, exports, module) {

	require('artdialog');

	//弹出提示
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


	$('.js-sub').on('click',function(){
		var data = [
			{"level":$('input[name=one_id]').val(),"percent":$('input[name=one]').val()},
			{"level":$('input[name=two_id]').val(),"percent":$('input[name=two]').val()},
			{"level":$('input[name=three_id]').val(),"percent":$('input[name=three]').val()},
			{"level":$('input[name=reg_id]').val(),"percent":$('input[name=reg]').val()}
		];
		$.post('?c=activity&a=saveCom',{"data":data},function(re){
			altDialog(re.msg);
		},'json');
	});
});
