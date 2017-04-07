define(function(require, exports, module) {
	
	require('artdialog');
	var Piclib = require('piclib');

	//保存
	$('.js-sub').on('click',function(e){
		e.preventDefault();
		//var data = [
		//	{"id":$('input[name=login_id]').val(),"point":$('input[name=login]').val()},
		//	{"id":$('input[name=info_id]').val(),"point":$('input[name=info]').val()},
		//	{"id":$('input[name=lottery_id]').val(),"point":$('input[name=lottery]').val()},
		//	{"id":$('input[name=consume_id]').val(),"point":$('input[name=consume]').val(),"limit":$('input[name=consume_limit]').val()},
		//	{"id":$('input[name=share_id]').val(),"point":$('input[name=share]').val(),"limit":$('input[name=share_limit]').val()}
		//];
		var data = {
			'login':{"point":$('input[name=login]').val()},
			'info':{"point":$('input[name=info]').val()},
			'lottery':{"point":$('input[name=lottery]').val()},
			'share':{"point":$('input[name=share]').val(),"limit":$('input[name=share_limit]').val()},
			'consume':{"point":$('input[name=consume]').val(),"limit":$('input[name=consume_limit]').val()},
            'show':{"point":$('input[name=show]').val()},
            'red':{"point":$('input[name=red]').val()}
		};

		$.post('?c=goods&a=savePointRule',{'data':data},function(re){
			altDialog(re.msg);
		},'json');
	});

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
