define(function(require, exports, module) {
	
	require('artdialog');


	$('.js-add').on('click',function(){
		var name = $('input[name=name]').val();
		var pwd = $('input[name=password]').val();
		var type = $('select[name=type]').val();
		$.post('?c=index&a=addAdmin',{"name":name,"pwd":pwd,"type":type},function(re){
			if(re.code > 0){
				location.reload();
			}else{
				$('.js-msg').text(re.msg);
			}
		},'json');
	});

	/**
	 * 设置权限
	 */
	$('.js-admin').on('click',function(){
		var id = $(this).attr('data-id');
		var text = $(this).text();
		modifyAdmin(id,1,text);
	});

	$('.js-stat').on('click',function(){
		var id = $(this).attr('data-id');
		var text = $(this).text();
		modifyAdmin(id,0,text);
	});


	function modifyAdmin(id,type,text){
		dialog({
			content:'确定'+text+'吗？',
			width: '160px',
			okValue: "确定",
			cancelValue: '取消',
			cancel: true,
			quickClose: true,
			ok: function() {
				this.close().remove();
				$.post('?c=index&a=modifyAdmin',{"id":id,"type":type},function(re){
					if(re.code > 0){
						altDialog('操作成功',function(){
							location.reload();
						});
					}else{
						altDialog('操作失败');
					}
				},'json');
			}
		}).show();
	}

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
