define(function(require, exports, module) {

	require('artdialog');

	//弹出提示
	function altDialog(content,callback){
		dialog({
			content:content,
			width: '150px',
			okValue: "确定",
			ok: function() {
				window.location.href=window.location.href;
				this.close().remove();
				if(typeof callback === 'function'){
					callback();
				}
			}
		}).show();
	}


	$('.js-sub').on('click',function(){
		 
		$.post('?c=sys&a=savesysset',$('.form-horizontal').serializeArray(),function(re){
			altDialog(re.msg);
		},'json');
	});
});
