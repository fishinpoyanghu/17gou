define(function(require, exports, module) {
	
	require('artdialog');

	$('.js-sub').on('click',function(){
		var title = $('input[name=title]').val();
		var sub_title = $('textarea[name=sub_title]').val();
		$.post('?c=activity&a=saveShare',{"title":title,"sub_title":sub_title},function(re){
			altDialog(re.msg);
		},'json');
	});


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


});
